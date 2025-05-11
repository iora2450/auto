<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\CustomerGroup;
use App\Warehouse;
use App\Biller;
use App\Product;
use App\Unit;
use App\Tax;
use App\Product_Warehouse;
use App\Types_document;
use App\Sale;
use DB;
use App\Returns;
use App\Account;
use App\ProductReturn;
use App\ProductVariant;
use App\Variant;
use App\CashRegister;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use NumberToWords\NumberToWords;

use App\ReturnPurchase;
use App\PurchaseProductReturn;



class ReturnController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('returns-index')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_return_all = Returns::with('biller', 'customer', 'warehouse', 'user')->orderBy('id', 'desc')->orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_return_all = Returns::with('biller', 'customer', 'warehouse', 'user')->orderBy('id', 'desc')->get();
            return view('return.index', compact('lims_return_all', 'all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('returns-add')){
            $lims_customer_list = Customer::where('is_active',true)->get();
            if(Auth::user()->role_id <= 2) {
                $lims_warehouse_list = Warehouse::where('is_active',true)->get();
                $lims_biller_list = Biller::where('is_active',true)->get();
            }
            else {
                $lims_warehouse_list = Warehouse::where('id',Auth::user()->warehouse_id)->get();
                $lims_biller_list = Biller::where('id', Auth::user()->biller_id)->get();
            }
            $lims_tax_list = Tax::where('is_active',true)->get();
            $lims_documents_list = Types_document::where('modulo', 'POD')->get();

            return view('return.create', compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list', 'lims_documents_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function getCustomerGroup($id)
    {
         $lims_customer_data = Customer::find($id);
         $lims_customer_group_data = CustomerGroup::find($lims_customer_data->customer_group_id);
         return $lims_customer_group_data->percentage;
    }

    public function getProduct($id)
    {
        //retrieve data of product without variant
        $lims_product_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
        ])->whereNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();
        //retrieve data of product with variant
        $lims_product_with_variant_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
        ])->whereNotNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();

        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_price = [];
        $product_data = [];
        foreach ($lims_product_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $product_price[] = $product_warehouse->price;
            $lims_product_data = Product::select('code', 'name', 'type')->find($product_warehouse->product_id);
            $product_code[] =  $lims_product_data->code;
            $product_name[] = htmlspecialchars($lims_product_data->name);
            $product_type[] = $lims_product_data->type;
        }
        foreach ($lims_product_with_variant_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $lims_product_data = Product::select('name', 'type')->find($product_warehouse->product_id);
            $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_warehouse->product_id, $product_warehouse->variant_id)->first();
            $product_code[] =  $lims_product_variant_data->item_code;
            $product_name[] = htmlspecialchars($lims_product_data->name);
            $product_type[] = $lims_product_data->type;
        }
        $lims_product_data = Product::select('code', 'name', 'type')->where('is_active', true)->whereNotIn('type', ['standard'])->get();
        foreach ($lims_product_data as $product) 
        {
            $product_qty[] = $product->qty;
            $product_code[] =  $product->code;
            $product_name[] = htmlspecialchars($product->name);
            $product_type[] = $product->type;
        }
        $product_data[] = $product_code;
        $product_data[] = $product_name;
        $product_data[] = $product_qty;
        $product_data[] = $product_type;
        $product_data[] = $product_price;
        return $product_data;
    }

    public function limsProductSearch(Request $request)
    {
        $todayDate = date('Y-m-d');
        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");
        $lims_product_data = Product::where('code', $product_code[0])->first();
        $product_variant_id = null;
        if(!$lims_product_data) {
            $lims_product_data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.*', 'product_variants.id as product_variant_id', 'product_variants.item_code', 'product_variants.additional_price')
                ->where('product_variants.item_code', $product_code[0])
                ->first();
            $lims_product_data->code = $lims_product_data->item_code;
            $lims_product_data->price += $lims_product_data->additional_price;
            $product_variant_id = $lims_product_data->product_variant_id;
        }
        $product[] = $lims_product_data->name;
        $product[] = $lims_product_data->code;
        if($lims_product_data->promotion && $todayDate <= $lims_product_data->last_date){
            $product[] = $lims_product_data->promotion_price;
        }
        else
            $product[] = $lims_product_data->price;
        
        if($lims_product_data->tax_id) {
            $lims_tax_data = Tax::find($lims_product_data->tax_id);
            $product[] = $lims_tax_data->rate;
            $product[] = $lims_tax_data->name;
        }
        else{
            $product[] = 0;
            $product[] = 'No Tax';
        }
        $product[] = $lims_product_data->tax_method;
        if($lims_product_data->type == 'standard'){
            $units = Unit::where("base_unit", $lims_product_data->unit_id)
                    ->orWhere('id', $lims_product_data->unit_id)
                    ->get();
            $unit_name = array();
            $unit_operator = array();
            $unit_operation_value = array();
            foreach ($units as $unit) {
                if($lims_product_data->sale_unit_id == $unit->id) {
                    array_unshift($unit_name, $unit->unit_name);
                    array_unshift($unit_operator, $unit->operator);
                    array_unshift($unit_operation_value, $unit->operation_value);
                }
                else {
                    $unit_name[]  = $unit->unit_name;
                    $unit_operator[] = $unit->operator;
                    $unit_operation_value[] = $unit->operation_value;
                }
            }
            $product[] = implode(",",$unit_name) . ',';
            $product[] = implode(",",$unit_operator) . ',';
            $product[] = implode(",",$unit_operation_value) . ',';     
        }
        
        else{
            $product[] = 'n/a'. ',';
            $product[] = 'n/a'. ',';
            $product[] = 'n/a'. ',';
        }
        $product[] = $lims_product_data->id;
        $product[] = $product_variant_id;
        $product[] = $lims_product_data->promotion;
        return $product;
    }

    public function store(Request $request)
    {
        $data = $request->except('document');
        //return dd($data);
        //$data['reference_no'] = 'rr-' . date("Ymd") . '-'. date("his");
       /* $data['reference_no'] = $data['reference_no'];
        $data['document'] = $data['factura'];
        $data['user_id'] = Auth::id();
        $cash_register_data = CashRegister::where([
            ['user_id', $data['user_id']],
            ['warehouse_id', $data['warehouse_id']],
            ['status', true]
        ])->first();
        if($cash_register_data)
            $data['cash_register_id'] = $cash_register_data->id;
        $lims_account_data = Account::where('is_default', true)->first();
        $data['account_id'] = $lims_account_data->id;*/


        //$document = $request->document;
        // if ($document) {
        //     $v = Validator::make(
        //         [
        //             'extension' => strtolower($request->document->getClientOriginalExtension()),
        //         ],
        //         [
        //             'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
        //         ]
        //     );
        //     if ($v->fails())
        //         return redirect()->back()->withErrors($v->errors());
            
        //     $documentName = $document->getClientOriginalName();
        //     $document->move('public/return/documents', $documentName);
        //     $data['document'] = $documentName;
        // }


        $data = $request->except('document');
        //return dd($data);
        
        $datoFactura = DB::select("SELECT IFNULL(MAX(correlativo),0) as correlativo FROM types_documents WHERE id=".$data['document_id']); 
        $correlativoNew = $datoFactura[0]->correlativo; 
        
        if(!isset($data['reference_no']))
            $data['reference_no'] = $correlativoNew;
            
        //$data['reference_no'] = 'rr-' . date("Ymd") . '-'. date("his");
        $data['user_id'] = Auth::id();
        $cash_register_data = CashRegister::where([
            ['user_id', $data['user_id']],
            ['warehouse_id', $data['warehouse_id']],
            ['status', true]
        ])->first();
        if($cash_register_data)
            $data['cash_register_id'] = $cash_register_data->id;
        $lims_account_data = Account::where('is_default', true)->first();
        $data['account_id'] = $lims_account_data->id;
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());
            
            $documentName = $document->getClientOriginalName();
            $document->move('public/return/documents', $documentName);
            $data['document'] = $documentName;
        }




        $lims_return_data = Returns::create($data);
        $lims_customer_data = Customer::find($data['customer_id']);
        //collecting male data
        $mail_data['email'] = $lims_customer_data->email;
        $mail_data['reference_no'] = $lims_return_data->reference_no;
        $mail_data['total_qty'] = $lims_return_data->total_qty;
        $mail_data['total_price'] = $lims_return_data->total_price;
        $mail_data['order_tax'] = $lims_return_data->order_tax;
        $mail_data['order_tax_rate'] = $lims_return_data->order_tax_rate;
        $mail_data['grand_total'] = $lims_return_data->grand_total;

        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $qty = $data['qty'];
        $sale_unit = $data['sale_unit'];
        $net_unit_price = $data['net_unit_price'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];

        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::find($pro_id);
            $variant_id = null;
            if($sale_unit[$key] != 'n/a'){
                $lims_sale_unit_data  = Unit::where('unit_name', $sale_unit[$key])->first();
                $sale_unit_id = $lims_sale_unit_data->id;
                if($lims_sale_unit_data->operator == '*')
                    $quantity = $qty[$key] * $lims_sale_unit_data->operation_value;
                elseif($lims_sale_unit_data->operator == '/')
                    $quantity = $qty[$key] / $lims_sale_unit_data->operation_value;

                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::
                        select('id', 'variant_id', 'qty')
                        ->FindExactProductWithCode($pro_id, $product_code[$key])
                        ->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['warehouse_id'])->first();
                    $lims_product_variant_data->qty += $quantity;
                    $lims_product_variant_data->save();
                    $variant_data = Variant::find($lims_product_variant_data->variant_id);
                    $variant_id = $variant_data->id;
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['warehouse_id'])->first();

                $lims_product_data->qty +=  $quantity;
                $lims_product_warehouse_data->qty += $quantity;

                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            else {
                if($lims_product_data->type == 'combo'){
                    $product_list = explode(",", $lims_product_data->product_list);
                    $qty_list = explode(",", $lims_product_data->qty_list);
                    $price_list = explode(",", $lims_product_data->price_list);

                    foreach ($product_list as $index=>$child_id) {
                        $child_data = Product::find($child_id);
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $data['warehouse_id'] ],
                            ])->first();

                        $child_data->qty += $qty[$key] * $qty_list[$index];
                        $child_warehouse_data->qty += $qty[$key] * $qty_list[$index];

                        $child_data->save();
                        $child_warehouse_data->save();
                    }
                }
                $sale_unit_id = 0;
            }
            if($lims_product_data->is_variant)
                $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name . ']';
            else
                $mail_data['products'][$key] = $lims_product_data->name;
            
            if($sale_unit_id)
                $mail_data['unit'][$key] = $lims_sale_unit_data->unit_code;
            else
                $mail_data['unit'][$key] = '';

            $mail_data['qty'][$key] = $qty[$key];
            $mail_data['total'][$key] = $total[$key];
            
            $nuevocorrelativo = $data['reference_no']+1;
        
            $data_actualiza["correlativo"] = $nuevocorrelativo; 

            Types_document::where([
                ['id', $data["document_id"]]
            ])->update($data_actualiza);
            
            ProductReturn::insert(
                ['return_id' => $lims_return_data->id, 'product_id' => $pro_id, 'variant_id' => $variant_id, 'qty' => $qty[$key], 'sale_unit_id' => $sale_unit_id, 'net_unit_price' => $net_unit_price[$key], 'discount' => $discount[$key], 'tax_rate' => $tax_rate[$key], 'tax' => $tax[$key], 'total' => $total[$key], 'created_at' => \Carbon\Carbon::now(),  'updated_at' => \Carbon\Carbon::now()]
            );
        }
        $message = 'Return created successfully';
        if($mail_data['email']){
            try{
                Mail::send( 'mail.return_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Return Details' );
                });
            }
            catch(\Exception $e){
                $message = 'Return created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
          $this->recordKardex($lims_return_data->id, 1,1);
          $this->genInvoice($lims_return_data->id, "");
        return redirect('return-sale')->with('message', $message);
    }

   



    public function genInvoice($id, $numerocontrolanexo)
    {
    
    
     
            $numeroControlAnexo =  $numerocontrolanexo;
            $lims_sale_data = Returns::find($id);
    
    
    
            $numeroinput =  $lims_sale_data->numeroControlInput;
            $datesale = $lims_sale_data->dateSale;
            $datesale =  substr($datesale, 0, 10);
           
            $lim_sale_data_anexo = Sale::where('numerocontrol', $numeroinput)->first();
  
            $codigogeneracionanexo = $lim_sale_data_anexo->codgeneracion;
    
            /// case que me traiga el numero de documento 
    
            $documentoid1 = $lim_sale_data_anexo->document_id;
            $docu_1;
    
            switch ($documentoid1) {
                case 1:
                    $docu_1 = "03";
                    break;
                case 2:
                    $docu_1 = "01";
                    break;
                case 5:
                    $docu_1= "11";
                    break;
    
                default:
                    break;
                }
    
    
    
            ///la licitacion para ver la venta
            $licitacion = $lim_sale_data_anexo->licitacion;
            $terceros ="";
            if($licitacion=="on")
            {
            $terceros = "{
                   \"nit\":\"06143009941026\",
                   \"nombre\":\"Bolsa de Productos de el salvador, s.a. de c.c.\"
               }";
               
            }
            else
            {
                $terceros = "null";
            }
            
    
    
    
    
    
            $lims_product_sale_data = ProductReturn::where('return_id', $id)->get();
     
            $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
            $lims_customer_data = Customer::find($lims_sale_data->customer_id);
            $nrc = $lims_customer_data->tax_no ;
            $nit = $lims_customer_data->nit ;
            $nombre = $lims_customer_data->name ;
            $direccion = $lims_customer_data->address ;
            $dui = $lims_customer_data->dui ;
            $sCompany = $lims_customer_data->company_name ;
       
            $telefono = $lims_customer_data->phone_number ;
    
            $email = $lims_customer_data->email ;
            $estado1 = $lims_customer_data->estado ;
            $municipio = $lims_customer_data->municipio ;
    
            $address = $lims_customer_data->address;
            $name_giro = $lims_customer_data->gire->name;
            $code_giro = $lims_customer_data->gire->code;
    
    
    
    
         
    
          //  $lims_payment_data = Payment::where('sale_id', $id)->get();
            $bytes = random_bytes(16);
            $documentId = $lims_sale_data->document_id;
    
    ///sello de recibido 
            $sResult = "";
    
    
        // Establecer los bits de la versión y de la variante
        $bytes[6] = chr(ord($bytes[6]) & 0x0F | 0x40); // Versión 4 (0100)
        $bytes[8] = chr(ord($bytes[8]) & 0x3F | 0x80); // Variante RFC 4122 (1000)
    
        // Convertir los bytes a una cadena UUID
        $uuid = vsprintf('%08s-%04s-%04s-%04s-%012s', [
            bin2hex(substr($bytes, 0, 4)),
            bin2hex(substr($bytes, 4, 2)),
            bin2hex(substr($bytes, 6, 2)),
            bin2hex(substr($bytes, 8, 2)),
            bin2hex(substr($bytes, 10, 6))
        ]);
    
                            //03 es credito fiscal 
                            //"numeroControl": "DTE-03-00000000-000000000000501",
    
                        //codigoGeneracion
                        /*$codigoGeneracion =  $this->genId('0123456789ABCDEF', 32);
                        $cadenaFormato="";
                        for ($i=0; $i <strlen($codigoGeneracion)  ; $i++) { 
                        if($i==8 || $i==12 || $i==16 || $i==20 ){
                                $cadenaFormato.='-';
                                $cadenaFormato .=$codigoGeneracion[$i];
                        }else{
                            $cadenaFormato .=$codigoGeneracion[$i];
                        }
                        }*/
                        //Numero de control 
                        $referen_corre = $lims_sale_data->reference_no;
                        $numeroControl= '000000000000000'.$referen_corre; 
                        $numeroControlFormateado= substr($numeroControl, -15);
                        $subtype = "";
                        if (empty($numeroControlAnexo)) {
                            $numeroControlAnexo = "NA";
                        } else {
                            $lims_sale_data_anexo = Sale::where('numerocontrol', $numeroControlAnexo)->get();
                            
                            if ($lims_sale_data_anexo->isEmpty()) {
                                $numeroControlAnexo = "NA";
                            } else {
                                $codgeneracion_anexo = $lims_sale_data_anexo->pluck('codgeneracion')->first();
                                $fecha_anexo = $lims_sale_data_anexo->pluck('created_at')->first();
                                $fecEmiAnex = substr($fecha_anexo, 0, 10);
                                $horEmiAnex = substr($fecha_anexo, -8);
                                $subtype = substr($numeroControlAnexo, 4, 2);
                            }
                        }
                   
                        
                        
                        
                      
    
                        $fecha = $lims_sale_data->created_at;
                        $fecEmi = substr($fecha, 0, 10);
                        $horEmi = substr($fecha, -8);
    
    
                        // Leer el archivo JSON
                $jsonString = file_get_contents('app/Http/Controllers/company.json');
    
    
                        // Analizar el contenido del archivo en un objeto PHP
                        $data = json_decode($jsonString);
                        
                        // Acceder a los valores de los campos
                        $emisorNit = $data->emisor->nit;
                        $emisorNrc = $data->emisor->nrc;
                        $emisorNombre = $data->emisor->nombre;
                        $emisorCodActividad = $data->emisor->codActividad;
                        $emisorDescActividad = $data->emisor->descActividad;
                        $emisorNombreComercial = $data->emisor->nombreComercial;
                        $emisorTipoEstablecimiento = $data->emisor->tipoEstablecimiento;
                        $emisorDireccionDepartamento = $data->emisor->direccion->departamento;
                        $emisorDireccionMunicipio = $data->emisor->direccion->municipio;
                        $emisorDireccionComplemento = $data->emisor->direccion->complemento;
                        $emisorTelefono = $data->emisor->telefono;
                        $emisorCorreo = $data->emisor->correo;
                        $emailauto = $data->emisor->emailauto;
                        $emisorkey = $data->emisor->keypublica;
                        $emisorprivatekey = $data->emisor->keyprivada;
                        $emisorapi = $data->emisor->api;
                        $emisorambiente = $data->emisor->ambiente;
    
    
                        
                        
    
    //documento de credi fiscal
    
    
            /*AQUI CONSTRUIREMOS JSON PARA ENVIO DE FACTURACION ELECTRONICA*/
    
            $detalleProductos = [];
            $dIva = 0.00;
            $dSUmNeto = 0.00;
            
                // Obtener los productos relacionados con la venta
                foreach ($lims_product_sale_data as $index => $product_sale) {
    
    
                    $product = product::find($product_sale->product_id);
    
                    $productre = ProductReturn::where('return_id', $id)
                    ->where('product_id', $product_sale->product_id)
                    ->first();
                    
                    // Verificar si el producto existe
                    if ($product) {
                        $cost = (double) $productre->net_unit_price;
    
                        $detalleProducto = [
                            "numItem" => $index + 1, // Número secuencial del item
                            "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                            "numeroDocumento" => $codigogeneracionanexo, // Número de documento de referencia"                        
                            "codigo" => null, // Código del producto obtenido de la clase Product
                            "codTributo" => null,
                            "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                            "cantidad" => $productre->qty, // Cantidad del producto
                            "uniMedida" => 59, // Unidad de medida del producto
                            "precioUni" =>  $cost, // Precio unitario del producto
                            "montoDescu" => 0,
                            "ventaNoSuj" => 0,
                            "ventaExenta" => 0,
                            "ventaGravada" => ($productre->qty * $cost) , // Precio total del producto
                            "tributos" => [
                                "20" // Código del impuesto (en este caso, 13% de IVA)
                            ]
    
                        
                        ];
                //$dIva = $dIva + (($productre->qty * $cost)*0.13);
                $dIva += $product_sale->tax;
                $dSUmNeto += $product_sale->total - $product_sale->tax;
                $detalleProductos[] = $detalleProducto;
                    }
                }
    
                //$dIva = number_format($dIva, 2 );
                //$dSUmNeto = number_format($dSUmNeto,2);
                $dTotal = $dIva + $dSUmNeto;

                $sLetras = $this->numeroALetras($dTotal);
   

      
                $code_muni = $lims_customer_data->municipio->code;
                $code_estado = $lims_customer_data->estado->code;
    
           $json_variable='{
            "nit": "'.$emisorNit.'",
            "activo": "true",
            "passwordPri": "'.$emisorprivatekey.'",
            "dteJson": {
        
            "identificacion": {
                "version": 3,
                "ambiente": "01",
                "tipoDte": "05",
                "numeroControl": "DTE-05-00000000-'.$numeroControlFormateado.'",
                "codigoGeneracion": "'.strtoupper($uuid).'",
                "tipoModelo": 1,
                "tipoOperacion": 1,
                "tipoContingencia": null,
                "motivoContin": null,
                "fecEmi": "'.$fecEmi.'",
                "horEmi": "'.$horEmi.'",
                "tipoMoneda": "USD"
        
            },
            "documentoRelacionado": [{
            "tipoDocumento": "'.$docu_1.'",
            "tipoGeneracion" :2, 
            "numeroDocumento" : "'.$codigogeneracionanexo.'",
            "fechaEmision":"'.$datesale.'"
            
            }],
            "emisor": {
                "nit": "'.$emisorNit.'",
                "nrc": "'.$emisorNrc.'",
                "nombre": "'.$emisorNombre.'",
                "codActividad": "'.$emisorCodActividad.'",
                "descActividad": "'.$emisorDescActividad.'",
                "nombreComercial": "'.$emisorNombreComercial.'",
                "tipoEstablecimiento": "02",
                "direccion": {
                    "departamento": "'.$emisorDireccionDepartamento.'",
                    "municipio": "'.$emisorDireccionMunicipio.'",
                    "complemento": "'.$emisorDireccionComplemento.'"
                },
                "telefono": "'.$emisorTelefono.'",
                "correo": "'.$emisorCorreo.'"
            },
            "receptor": {
                "nit": "'.$nit.'",
                "nrc": "'.$nrc.'",
                "nombre": "'.$nombre.'",
                "codActividad": "'.$code_giro.'",
                "descActividad": "'.$name_giro.'",
                "nombreComercial": "'.$sCompany.'",
                "direccion": {
                    "departamento": "'.$code_estado.'",
                    "municipio": "'.$code_muni.'",
                    "complemento": "'.$address.'"
                },
                "telefono": "'.$telefono.'",
                            "correo": "'.$email.'"
                },
            "ventaTercero": '.$terceros.',
            "cuerpoDocumento": [],
            "resumen": {
                "totalNoSuj": 0,
                "totalExenta": 0,
                "totalGravada": '.$dSUmNeto.',
                "subTotalVentas": '.$dSUmNeto.',
                "descuNoSuj": 0,
                "descuExenta": 0,
                "descuGravada": 0,
                "totalDescu": 0,
                "tributos": [{
                    "codigo": "20",
                    "descripcion": "Impuesto al Valor Agregado 13%",
                    "valor": '. round($dIva, 2).'
                }],
                "subTotal": '.$dSUmNeto.',
                "ivaPerci1": 0,
                "ivaRete1": 0,
                "reteRenta": 0,
                "montoTotalOperacion": '.$dTotal.',
                "totalLetras": "'.$sLetras.'",
                      "condicionOperacion": 2
            },
        
                "extension":null,
                "apendice": null
        
            }
            
        }'; 
    
                
                      // fin
                    // Agregar el detalle de productos al JSON existente
                    $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                    
    
         
                 $sResult =    $this->processRequest($json_variable);
    
    //sujeto excluido
    
    
    $dataresult  = json_decode($sResult, true);
    $data = json_decode($json_variable, true);
    //$tipoDte = $data['dteJson']['identificacion']['tipoDte'];
    $numeroControl1 = $data['dteJson']['identificacion']['numeroControl'];
    $fechae =  $data['dteJson']['identificacion']['fecEmi'];
    
    $sello = $dataresult['selloRecibido'];
    $estado = $dataresult['estado'];
                 
    $lims_sale_data->numerocontrol = $numeroControl1;
    $lims_sale_data->codgeneracion = strtoupper($uuid);
    
    // $lims_sale_data->codgeneracionAnexo = $numeroControlAnexo;
    $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".strtoupper($uuid)."&fechaEmi=".$fechae;
    $aqrl = $sqr;
    
    if ($estado =="PROCESADO")
    {
        $lims_sale_data->sello = $sello;
    $lims_sale_data->estadodte = "done";
    $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>"; 
    //echo $imgh;
    $jsonString =  $json_variable;
    
    
    $jsonData = json_decode($jsonString, true);
    //usar la funcion geninvoice_pdf para generar el pdf
    //coloca una variable donde este el correo electronico dle cliente
    
     //$pdf1 = $this->genInvoice_pdf($id);
    
    // Codifica nuevamente el JSON con la opción JSON_PRETTY_PRINT
    $prettyJson = json_encode($jsonData, JSON_PRETTY_PRINT);
    
    
      //$this->enviarCorreoConImagenYTexto($email, "factura electronica", "Gracias por su preferencia a continuacion su qr" , $json_variable, $aqrl, $pdf1);
    $lims_sale_data->update();
    } 
    else {
        $lims_sale_data->sello = "NA";
        $lims_sale_data->estadodte = json_encode($dataresult);
        
        $lims_sale_data->update();
    } 
    
    if ($id>0)
    {
       // echo "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>";
    }
    else{
    
    }
    
    
    
            $numberToWords = new NumberToWords();
            if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
                $numberTransformer = $numberToWords->getNumberTransformer('en');
            else
                $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
            $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);
            if ($estado =="PROCESADO")
            {
                return "done";
            }
            else
            {
                return "error";
            }
            //return view('sale.invoice', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
        }
    
    
    
        function processRequest($json_variable) {
            // Parámetros para la solicitud de autenticación
        
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://142.44.196.208/wscarlos/cs.php',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => http_build_query(array('json_variable' => $json_variable)), 
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            return $response;
        }
        




        public function numeroALetras($numero)
        {
            $decimales = floor(($numero - floor($numero)) * 100); // Obtener los decimales
            $numero = floor($numero); // Obtener la parte entera del número
        
            $unidades = array(
                0 => 'CERO',
                1 => 'UN',
                2 => 'DOS',
                3 => 'TRES',
                4 => 'CUATRO',
                5 => 'CINCO',
                6 => 'SEIS',
                7 => 'SIETE',
                8 => 'OCHO',
                9 => 'NUEVE',
                10 => 'DIEZ',
                11 => 'ONCE',
                12 => 'DOCE',
                13 => 'TRECE',
                14 => 'CATORCE',
                15 => 'QUINCE',
                16 => 'DIECISÉIS',
                17 => 'DIECISIETE',
                18 => 'DIECIOCHO',
                19 => 'DIECINUEVE',
                20 => 'VEINTE',
                30 => 'TREINTA',
                40 => 'CUARENTA',
                50 => 'CINCUENTA',
                60 => 'SESENTA',
                70 => 'SETENTA',
                80 => 'OCHENTA',
                90 => 'NOVENTA'
            );
        
            $centenas = array(
                100 => 'CIEN',
                200 => 'DOSCIENTOS',
                300 => 'TRESCIENTOS',
                400 => 'CUATROCIENTOS',
                500 => 'QUINIENTOS',
                600 => 'SEISCIENTOS',
                700 => 'SETECIENTOS',
                800 => 'OCHOCIENTOS',
                900 => 'NOVECIENTOS'
            );
        
            $millones = array(
                'SINGULAR' => 'MILLÓN',
                'PLURAL' => 'MILLONES'
            );
        
            $mil = 'MIL';
        
            // Verificar si el número está dentro del rango permitido
            if ($numero < 0 || $numero >= 1000000000) {
                return 'Número fuera de rango';
            }
        
            // Obtener la parte entera en letras
            $parteEntera = ($numero > 0) ? $this->convertirNumeroALetras($numero) : 'CERO';
        
            // Obtener los decimales en letras
            $parteDecimal = ($decimales > 0) ? $this->convertirNumeroALetras($decimales) : '';
        
            // Combinar la parte entera y los decimales en una cadena
            $resultado = ($parteEntera != '') ? $parteEntera . ' DÓLARES' : '';
            $resultado .= ($parteDecimal != '') ? ' CON ' . $parteDecimal . ' CENTAVOS' : '';
        
            return $resultado;
        }
    

        public function convertirNumeroALetras($numero)
        {
            $unidades = array(
                0 => 'CERO',
                1 => 'UN',
                2 => 'DOS',
                3 => 'TRES',
                4 => 'CUATRO',
                5 => 'CINCO',
                6 => 'SEIS',
                7 => 'SIETE',
                8 => 'OCHO',
                9 => 'NUEVE',
                10 => 'DIEZ',
                11 => 'ONCE',
                12 => 'DOCE',
                13 => 'TRECE',
                14 => 'CATORCE',
                15 => 'QUINCE',
                16 => 'DIECISÉIS',
                17 => 'DIECISIETE',
                18 => 'DIECIOCHO',
                19 => 'DIECINUEVE',
                20 => 'VEINTE',
                30 => 'TREINTA',
                40 => 'CUARENTA',
                50 => 'CINCUENTA',
                60 => 'SESENTA',
                70 => 'SETENTA',
                80 => 'OCHENTA',
                90 => 'NOVENTA'
            );
        
            $centenas = array(
                100 => 'CIEN',
                200 => 'DOSCIENTOS',
                300 => 'TRESCIENTOS',
                400 => 'CUATROCIENTOS',
                500 => 'QUINIENTOS',
                600 => 'SEISCIENTOS',
                700 => 'SETECIENTOS',
                800 => 'OCHOCIENTOS',
                900 => 'NOVECIENTOS'
            );
        
            $millones = array(
                'SINGULAR' => 'MILLÓN',
                'PLURAL' => 'MILLONES'
            );
        
            $mil = 'MIL';
        
            $resultado = '';
        
            if ($numero >= 1000000) {
                $millon = floor($numero / 1000000);
                $resultado .= ($millon > 1) ? $this->convertirNumeroMenorMil($millon) . ' ' . $millones['PLURAL'] : $millones['SINGULAR'];
                $numero %= 1000000;
            }
        
            if ($numero >= 1000) {
                $mil = floor($numero / 1000);
                $resultado .= ($resultado != '') ? ' ' . $this->convertirNumeroMenorMil($mil) . ' ' . $mil : $this->convertirNumeroMenorMil($mil) . ' ' . $mil;
                $numero %= 1000;
            }
        
            if ($numero > 0) {
                $resultado .= ($resultado != '') ? ' ' . $this->convertirNumeroMenorMil($numero) : $this->convertirNumeroMenorMil($numero);
            }
        
            return $resultado;
        }
        
        function enviarCorreoConImagenYTexto($destinatario, $asunto, $mensajeTexto, $json, $aqrl,  $pdf) {
            // URL de la imagen del QR
            $imagenQR = "https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl);
            $pdfData = $pdf->output();
            // Decodificar el JSON
            $objetoJson = json_decode($json, true);
        
            // Agrega el JSON dentro de la etiqueta <pre> para preservar el formato
            $jsonFormatted = json_encode($objetoJson['dteJson'], JSON_PRETTY_PRINT);
        
            // Guarda el JSON en un archivo de texto
            $archivo = 'archivo.json';
            file_put_contents($archivo, $jsonFormatted);
        
            // Configura las cabeceras
            $cabeceras = "From: MR JB <mr.jbinversionessadecv@gmail.com>\r\n";
            $cabeceras .= "Reply-To: mr.jbinversionessadecv@gmail.com\r\n";
            $cabeceras .= "Content-type: multipart/mixed; boundary=\"mixedboundary\"\r\n";
        
            // Crear el mensaje multipart
            $mensaje = "--mixedboundary\r\n" .
                       "Content-Type: multipart/related; boundary=\"relboundary\"\r\n\r\n" .
                       "--relboundary\r\n" .
                       "Content-Type: text/html; charset=\"UTF-8\"\r\n" .
                       "Content-Transfer-Encoding: 7bit\r\n\r\n" .
                       "<html><body>" . $mensajeTexto . "<br><img src=\"" . $imagenQR . "\" alt=\"QR Code\" style=\"width: 200px; height: 200px;\"></body></html>\r\n\r\n" .
                       "--relboundary--\r\n" .
                       "--mixedboundary\r\n" .
                       "Content-Type: application/json; name=\"" . $archivo . "\"\r\n" .
                       "Content-Transfer-Encoding: base64\r\n" .
                       "Content-Disposition: attachment; filename=\"" . $archivo . "\"\r\n\r\n" .
                       chunk_split(base64_encode(file_get_contents($archivo))) . "\r\n\r\n" .
                       "--mixedboundary\r\n" .
                       "Content-Type: application/pdf; name=\"consumidor.pdf\"\r\n" .
                       "Content-Transfer-Encoding: base64\r\n" .
                       "Content-Disposition: attachment; filename=\"consumidor.pdf\"\r\n\r\n" .
                       chunk_split(base64_encode($pdfData)) . "\r\n\r\n" .
                       "--mixedboundary--";
        
            // Envía el correo utilizando la función mail()
            $enviado = mail($destinatario, $asunto, $mensaje, $cabeceras);
        
            // Verifica si el correo fue enviado con éxito
            if ($enviado) {
            //    echo "El correo ha sido enviado correctamente.";
            } else {
                echo "Error al enviar el correo.";
            }
        
            // Elimina el archivo después de adjuntarlo
            unlink($archivo);
        }
        
        
        public function convertirNumeroMenorMil($numero)
        {
            $unidades = array(
                0 => '',
                1 => 'UN',
                2 => 'DOS',
                3 => 'TRES',
                4 => 'CUATRO',
                5 => 'CINCO',
                6 => 'SEIS',
                7 => 'SIETE',
                8 => 'OCHO',
                9 => 'NUEVE',
                10 => 'DIEZ',
                11 => 'ONCE',
                12 => 'DOCE',
                13 => 'TRECE',
                14 => 'CATORCE',
                15 => 'QUINCE',
                16 => 'DIECISÉIS',
                17 => 'DIECISIETE',
                18 => 'DIECIOCHO',
                19 => 'DIECINUEVE',
                20 => 'VEINTE',
                30 => 'TREINTA',
                40 => 'CUARENTA',
                50 => 'CINCUENTA',
                60 => 'SESENTA',
                70 => 'SETENTA',
                80 => 'OCHENTA',
                90 => 'NOVENTA'
            );
        
            $centenas = array(
                100 => 'CIEN',
                200 => 'DOSCIENTOS',
                300 => 'TRESCIENTOS',
                400 => 'CUATROCIENTOS',
                500 => 'QUINIENTOS',
                600 => 'SEISCIENTOS',
                700 => 'SETECIENTOS',
                800 => 'OCHOCIENTOS',
                900 => 'NOVECIENTOS'
            );
        
            $resultado = '';
        
            if ($numero >= 100) {
                $centena = floor($numero / 100) * 100;
                $resultado .= $centenas[$centena];
                $numero %= 100;
            }
        
            if ($numero >= 20) {
                $decena = floor($numero / 10) * 10;
                $resultado .= ($resultado != '') ? ' Y ' . $unidades[$decena] : $unidades[$decena];
                $numero %= 10;
            }
        
            if ($numero > 0) {
                $resultado .= ($resultado != '') ? ' Y ' . $unidades[$numero] : $unidades[$numero];
            }
        
            return $resultado;
        }
            


    //Type = 1 Sale
    //Type=2 Purchase
    //Type = 1 Sale
    //Type=2 Purchase
    public function recordKardex($idtran, $typetransaction, $signo)
    {
        if($typetransaction == 1){
            //Datos de la venta disponibles. 
            if($signo ==-1){
                $lims_purchase_data = Sale::find($idtran);

                $lims_product_purchase_data = Product_Sale::where('sale_id', $idtran)->get();
                $concepto = 'Venta';
                $sale_id = $idtran;

            }else{
                $lims_purchase_data = Returns::find($idtran);

                $lims_product_purchase_data = ProductReturn::where('return_id', $idtran)->get();

                $concepto = 'Retorno Venta';
                $sale_id = $idtran;
            }
       
            $referencia = $lims_purchase_data->reference_no; 

            //dd($referencia);
            $lims_supplier_data = Customer::find($lims_purchase_data->customer_id);
            
            if($lims_supplier_data->count() >0){
                $name_supplier = $lims_supplier_data->name; 
    
            }else{
                $name_supplier =''; 
            }      


 
            foreach ($lims_product_purchase_data as $key => $product_purchase_data) {
                $product = Product::find($product_purchase_data->product_id);
                $unit = Unit::find($product_purchase_data->sale_unit_id);
   

         $data = DB::select("SELECT IFNULL(MAX(correlativo),0)+1 as correlativo FROM kardex WHERE product_id=".$product_purchase_data->product_id); 


         $correlativo=  $data[0]->correlativo; 
         /*Esta operacion se hace porque a estas alturas el campo QTY ya fue afectado */
         if($signo>0){
                 $stock      =  $product->qty-$product_purchase_data->qty; 
            }else{

                 $stock      =  $product->qty+$product_purchase_data->qty; 
            }
         
         $saldo      =  $stock+($product_purchase_data->qty*$signo);
           if($signo ==-1){
            $costo      =  $product_purchase_data->cost;  
           }else{

              $costo      =  $product->cost;  
           }
         $costo_unitario_promedio      =  $product->cost;  

         DB::statement("
        

                     INSERT INTO kardex (

                      product_id,
                      name_product,
                      created_at,
                      cost,
                      stock,
                      qty,
                      correlativo,
                      nombreProveedor,
                      concepto,
                      signo,
                      saldo, 
                      costo_unitario_promedio,                      
                      documento
                    ) 
                    VALUES
                      (

                        ".$product_purchase_data->product_id.",
                        '".$product->name."',
                        '".date("Y-m-d H:i:s")."',
                        ".$costo.",
                         ".$stock.",
                         ".$product_purchase_data->qty.",
                          ".$correlativo.",
                        '".$name_supplier."',
                        '".$concepto."',
                        ".$signo.",
                        ".$saldo.",
                        ".$costo_unitario_promedio.",
                        ".$referencia."

                      ) ;
                        ");

        }
         
      } 

      if($typetransaction ==2){
        //Datos de la venta disponibles. 
        if($signo == 1){
         $lims_purchase_data = Purchase::find($idtran);
          $lims_product_purchase_data = ProductPurchase::where('purchase_id', $idtran)->get();
          $concepto='compra'; 
            
     }else{
        $lims_purchase_data = ReturnPurchase::find($idtran);
         $lims_product_purchase_data = PurchaseProductReturn::where('return_id', $idtran)->get();
         $concepto='retorno-compra'; 
         
     }
         $referencia = $lims_purchase_data->reference_no; 

         

          $lims_supplier_data = Supplier::find($lims_purchase_data->supplier_id);
          if($lims_supplier_data->count() >0){
          $name_supplier = $lims_supplier_data->name; 
    
      }else{

        $name_supplier =''; 
      }      


      
 
        foreach ($lims_product_purchase_data as $key => $product_purchase_data) {
            $product = Product::find($product_purchase_data->product_id);
            $unit = Unit::find($product_purchase_data->purchase_unit_id);
   

         $data = DB::select("SELECT IFNULL(MAX(correlativo),0)+1 as correlativo FROM kardex WHERE product_id=".$product_purchase_data->product_id); 


         $correlativo=  $data[0]->correlativo; 
         /*Esta operacion se hace porque a estas alturas el campo QTY ya fue afectado */
         if($signo>0){
                 $stock      =  $product->qty-$product_purchase_data->qty; 
            }else{

                 $stock      =  $product->qty+$product_purchase_data->qty; 
            }
         
         $saldo      =  $stock+($product_purchase_data->qty*$signo);
         $costo      =  $product_purchase_data->net_unit_cost;  
          $costo_unitario_promedio      =  $product->cost;  


         DB::statement("
        

                     INSERT INTO kardex (

                      product_id,
                      name_product,
                      created_at,
                      cost,
                      stock,
                      qty,
                      correlativo,
                      nombreProveedor,
                      lote,
                      concepto,
                      signo,
                      saldo,
                       costo_unitario_promedio

                    ) 
                    VALUES
                      (

                        ".$product_purchase_data->product_id.",
                        '".$product->name."',
                        '".date("Y-m-d H:i:s")."',
                        ".$costo.",
                         ".$stock.",
                         ".$product_purchase_data->qty.",
                          ".$correlativo.",
                        '".$name_supplier."',
                        '',
                        '".$concepto."',
                        ".$signo.",
                        ".$saldo.",
                         ".$costo_unitario_promedio."

                      ) ;
                        ");

        }
         
      }

  
      
    }



    

    public function sendMail(Request $request)
    {
        $data = $request->all();
        $lims_return_data = Returns::find($data['return_id']);
        $lims_product_return_data = ProductReturn::where('return_id', $data['return_id'])->get();
        $lims_customer_data = Customer::find($lims_return_data->customer_id);
        if($lims_customer_data->email) {
            //collecting male data
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['reference_no'] = $lims_return_data->reference_no;
            $mail_data['total_qty'] = $lims_return_data->total_qty;
            $mail_data['total_price'] = $lims_return_data->total_price;
            $mail_data['order_tax'] = $lims_return_data->order_tax;
            $mail_data['order_tax_rate'] = $lims_return_data->order_tax_rate;
            $mail_data['grand_total'] = $lims_return_data->grand_total;

            foreach ($lims_product_return_data as $key => $product_return_data) {
                $lims_product_data = Product::find($product_return_data->product_id);
                if($product_return_data->variant_id){
                    $variant_data = Variant::find($product_return_data->variant_id);
                    $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name .']';
                }
                else
                    $mail_data['products'][$key] = $lims_product_data->name;

                if($product_return_data->sale_unit_id){
                    $lims_unit_data = Unit::find($product_return_data->sale_unit_id);
                    $mail_data['unit'][$key] = $lims_unit_data->unit_code;
                }
                else
                    $mail_data['unit'][$key] = '';

                $mail_data['qty'][$key] = $product_return_data->qty;
                $mail_data['total'][$key] = $product_return_data->qty;
            }

            try{
                Mail::send( 'mail.return_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Return Details' );
                });
                $message = 'Mail sent successfully';
            }
            catch(\Exception $e){
                $message = 'Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        else
            $message = 'Customer doesnt have email!';
        
        return redirect()->back()->with('message', $message);
    }

    public function productReturnData($id)
    {
        $lims_product_return_data = ProductReturn::where('return_id', $id)->get();
        foreach ($lims_product_return_data as $key => $product_return_data) {
            $product = Product::find($product_return_data->product_id);
            if($product_return_data->sale_unit_id != 0){
                $unit_data = Unit::find($product_return_data->sale_unit_id);
                $unit = $unit_data->unit_code;
            }
            else
                $unit = '';
            if($product_return_data->variant_id) {
                $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_return_data->product_id, $product_return_data->variant_id)->first();
                $product->code = $lims_product_variant_data->item_code;
            }

            $product_return[0][$key] = $product->name . ' [' . $product->code . ']';
            $product_return[1][$key] = $product_return_data->qty;
            $product_return[2][$key] = $unit;
            $product_return[3][$key] = $product_return_data->tax;
            $product_return[4][$key] = $product_return_data->tax_rate;
            $product_return[5][$key] = $product_return_data->discount;
            $product_return[6][$key] = $product_return_data->total;
        }
        return $product_return;
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('returns-edit')){
            $lims_customer_list = Customer::where('is_active',true)->get();
            $lims_warehouse_list = Warehouse::where('is_active',true)->get();
            $lims_biller_list = Biller::where('is_active',true)->get();
            $lims_tax_list = Tax::where('is_active',true)->get();
            $lims_return_data = Returns::find($id);
            $lims_product_return_data = ProductReturn::where('return_id', $id)->get();
            return view('return.edit',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list', 'lims_return_data','lims_product_return_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('document');
        //return dd($data);
        $document = $request->document;
        if ($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $documentName = $document->getClientOriginalName();
            $document->move('public/return/documents', $documentName);
            $data['document'] = $documentName;
        }

        $lims_return_data = Returns::find($id);
        $lims_product_return_data = ProductReturn::where('return_id', $id)->get();

        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $product_variant_id = $data['product_variant_id'];
        $qty = $data['qty'];
        $sale_unit = $data['sale_unit'];
        $net_unit_price = $data['net_unit_price'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];

        foreach ($lims_product_return_data as $key => $product_return_data) {
            $old_product_id[] = $product_return_data->product_id;
            $old_product_variant_id[] = null;
            $lims_product_data = Product::find($product_return_data->product_id);
            if($lims_product_data->type == 'combo'){
                $product_list = explode(",", $lims_product_data->product_list);
                $qty_list = explode(",", $lims_product_data->qty_list);

                foreach ($product_list as $index=>$child_id) {
                    $child_data = Product::find($child_id);
                    $child_warehouse_data = Product_Warehouse::where([
                        ['product_id', $child_id],
                        ['warehouse_id', $lims_return_data->warehouse_id ],
                        ])->first();

                    $child_data->qty -= $product_return_data->qty * $qty_list[$index];
                    $child_warehouse_data->qty -= $product_return_data->qty * $qty_list[$index];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }
            elseif($product_return_data->sale_unit_id != 0){
                $lims_sale_unit_data = Unit::find($product_return_data->sale_unit_id);
                if ($lims_sale_unit_data->operator == '*')
                    $quantity = $product_return_data->qty * $lims_sale_unit_data->operation_value;
                elseif($lims_sale_unit_data->operator == '/')
                    $quantity = $product_return_data->qty / $lims_sale_unit_data->operation_value;

                if($product_return_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($product_return_data->product_id, $product_return_data->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_return_data->product_id, $product_return_data->variant_id, $lims_return_data->warehouse_id)
                    ->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                    $lims_product_variant_data->qty -= $quantity;
                    $lims_product_variant_data->save();
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_return_data->product_id, $lims_return_data->warehouse_id)
                    ->first();

                $lims_product_data->qty -= $quantity;
                $lims_product_warehouse_data->qty -= $quantity;
                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            if($product_return_data->variant_id && !(in_array($old_product_variant_id[$key], $product_variant_id)) ){
                $product_return_data->delete();
            }
            elseif( !(in_array($old_product_id[$key], $product_id)) )
                $product_return_data->delete();
        }
        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::find($pro_id);
            $product_return['variant_id'] = null;
            if($sale_unit[$key] != 'n/a'){
                $lims_sale_unit_data = Unit::where('unit_name', $sale_unit[$key])->first();
                $sale_unit_id = $lims_sale_unit_data->id;
                if ($lims_sale_unit_data->operator == '*')
                    $quantity = $qty[$key] * $lims_sale_unit_data->operation_value;
                elseif($lims_sale_unit_data->operator == '/')
                    $quantity = $qty[$key] / $lims_sale_unit_data->operation_value;

                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('id', 'variant_id', 'qty')->FindExactProductWithCode($pro_id, $product_code[$key])->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['warehouse_id'])
                    ->first();
                    $variant_data = Variant::find($lims_product_variant_data->variant_id);

                    $product_return['variant_id'] = $lims_product_variant_data->variant_id;
                    $lims_product_variant_data->qty += $quantity;
                    $lims_product_variant_data->save();
                }
                else {
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['warehouse_id'])
                    ->first();
                }

                $lims_product_data->qty +=  $quantity;
                $lims_product_warehouse_data->qty += $quantity;

                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            else {
                if($lims_product_data->type == 'combo'){
                    $product_list = explode(",", $lims_product_data->product_list);
                    $qty_list = explode(",", $lims_product_data->qty_list);

                    foreach ($product_list as $index=>$child_id) {
                        $child_data = Product::find($child_id);
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $data['warehouse_id'] ],
                            ])->first();

                        $child_data->qty += $qty[$key] * $qty_list[$index];
                        $child_warehouse_data->qty += $qty[$key] * $qty_list[$index];

                        $child_data->save();
                        $child_warehouse_data->save();
                    }
                }
                $sale_unit_id = 0;
            }

            if($lims_product_data->is_variant)
                $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name .']';
            else
                $mail_data['products'][$key] = $lims_product_data->name;

            if($sale_unit_id)
                $mail_data['unit'][$key] = $lims_sale_unit_data->unit_code;
            else
                $mail_data['unit'][$key] = '';

            $mail_data['qty'][$key] = $qty[$key];
            $mail_data['total'][$key] = $total[$key];

            $product_return['return_id'] = $id ;
            $product_return['product_id'] = $pro_id;
            $product_return['qty'] = $qty[$key];
            $product_return['sale_unit_id'] = $sale_unit_id;
            $product_return['net_unit_price'] = $net_unit_price[$key];
            $product_return['discount'] = $discount[$key];
            $product_return['tax_rate'] = $tax_rate[$key];
            $product_return['tax'] = $tax[$key];
            $product_return['total'] = $total[$key];

            if($product_return['variant_id'] && in_array($product_variant_id[$key], $old_product_variant_id)) {
                ProductReturn::where([
                    ['product_id', $pro_id],
                    ['variant_id', $product_return['variant_id']],
                    ['return_id', $id]
                ])->update($product_return);
            }
            elseif( $product_return['variant_id'] === null && (in_array($pro_id, $old_product_id)) ) {
                ProductReturn::where([
                    ['return_id', $id],
                    ['product_id', $pro_id]
                    ])->update($product_return);
            }
            else
                ProductReturn::create($product_return);
        }
        $lims_return_data->update($data);
        $lims_customer_data = Customer::find($data['customer_id']);
        //collecting male data
        $mail_data['email'] = $lims_customer_data->email;
        $mail_data['reference_no'] = $lims_return_data->reference_no;
        $mail_data['total_qty'] = $lims_return_data->total_qty;
        $mail_data['total_price'] = $lims_return_data->total_price;
        $mail_data['order_tax'] = $lims_return_data->order_tax;
        $mail_data['order_tax_rate'] = $lims_return_data->order_tax_rate;
        $mail_data['grand_total'] = $lims_return_data->grand_total;
        $message = 'Return updated successfully';
        if($mail_data['email']){
            try{
                Mail::send( 'mail.return_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Return Details' );
                });
            }
            catch(\Exception $e){
                $message = 'Return updated successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        return redirect('return-sale')->with('message', $message);
    }

    public function deleteBySelection(Request $request)
    {
        $return_id = $request['returnIdArray'];
        foreach ($return_id as $id) {
            $lims_return_data = Returns::find($id);
            $lims_product_return_data = ProductReturn::where('return_id', $id)->get();

            foreach ($lims_product_return_data as $key => $product_return_data) {
                $lims_product_data = Product::find($product_return_data->product_id);
                if( $lims_product_data->type == 'combo' ){
                    $product_list = explode(",", $lims_product_data->product_list);
                    $qty_list = explode(",", $lims_product_data->qty_list);

                    foreach ($product_list as $index=>$child_id) {
                        $child_data = Product::find($child_id);
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $lims_return_data->warehouse_id ],
                            ])->first();

                        $child_data->qty -= $product_return_data->qty * $qty_list[$index];
                        $child_warehouse_data->qty -= $product_return_data->qty * $qty_list[$index];

                        $child_data->save();
                        $child_warehouse_data->save();
                    }
                }
                elseif($product_return_data->sale_unit_id != 0){
                    $lims_sale_unit_data = Unit::find($product_return_data->sale_unit_id);

                    if ($lims_sale_unit_data->operator == '*')
                        $quantity = $product_return_data->qty * $lims_sale_unit_data->operation_value;
                    elseif($lims_sale_unit_data->operator == '/')
                        $quantity = $product_return_data->qty / $lims_sale_unit_data->operation_value;
                    if($product_return_data->variant_id) {
                        $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($product_return_data->product_id, $product_return_data->variant_id)->first();
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_return_data->product_id, $product_return_data->variant_id, $lims_return_data->warehouse_id)->first();
                        $lims_product_variant_data->qty -= $quantity;
                        $lims_product_variant_data->save();
                    }
                    else
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_return_data->product_id, $lims_return_data->warehouse_id)->first();

                    $lims_product_data->qty -= $quantity;
                    $lims_product_warehouse_data->qty -= $quantity;
                    $lims_product_data->save();
                    $lims_product_warehouse_data->save();
                    $product_return_data->delete();
                }
            }
            $lims_return_data->delete();
            }
        return 'Return deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_return_data = Returns::find($id);
        $lims_product_return_data = ProductReturn::where('return_id', $id)->get();

        foreach ($lims_product_return_data as $key => $product_return_data) {
            $lims_product_data = Product::find($product_return_data->product_id);
            if( $lims_product_data->type == 'combo' ){
                $product_list = explode(",", $lims_product_data->product_list);
                $qty_list = explode(",", $lims_product_data->qty_list);

                foreach ($product_list as $index=>$child_id) {
                    $child_data = Product::find($child_id);
                    $child_warehouse_data = Product_Warehouse::where([
                        ['product_id', $child_id],
                        ['warehouse_id', $lims_return_data->warehouse_id ],
                        ])->first();

                    $child_data->qty -= $product_return_data->qty * $qty_list[$index];
                    $child_warehouse_data->qty -= $product_return_data->qty * $qty_list[$index];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }
            elseif($product_return_data->sale_unit_id != 0){
                $lims_sale_unit_data = Unit::find($product_return_data->sale_unit_id);

                if ($lims_sale_unit_data->operator == '*')
                    $quantity = $product_return_data->qty * $lims_sale_unit_data->operation_value;
                elseif($lims_sale_unit_data->operator == '/')
                    $quantity = $product_return_data->qty / $lims_sale_unit_data->operation_value;
                
                if($product_return_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($product_return_data->product_id, $product_return_data->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_return_data->product_id, $product_return_data->variant_id, $lims_return_data->warehouse_id)->first();
                    $lims_product_variant_data->qty -= $quantity;
                    $lims_product_variant_data->save();
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_return_data->product_id, $lims_return_data->warehouse_id)->first();

                $lims_product_data->qty -= $quantity;
                $lims_product_warehouse_data->qty -= $quantity;
                $lims_product_data->save();
                $lims_product_warehouse_data->save();
                $product_return_data->delete();
            }
        }
        $lims_return_data->delete();
        return redirect('return-sale')->with('not_permitted', 'Data deleted successfully');;
    }
    
    public function genReturn_nc($id)
    {
        $userName = auth()->user()->name_purchase;
        $userNit = auth()->user()->email_purchase;
        $lims_return_data = Returns::find($id);
        $lims_product_return_data = ProductReturn::where('return_id', $id)->get();
        $lims_biller_data = Biller::find($lims_return_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_return_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_return_data->customer_id);

        //dd($lims_product_return_data);
        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('es');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        $numberInWords = $numberTransformer->toWords($lims_return_data->grand_total);

        return view('return.invoice_nc', compact('lims_return_data', 'lims_product_return_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'numberInWords'));
    }


    public function getDteJson(Request $request) {
        // Obtén el ID enviado por la solicitud AJAX
        $id = $request->input('id');
        $json_variable="";
        $lims_return_data = Returns::find($id);
        // ontener estadodte de $lim_return_data
        $estado = $lims_return_data->estadodte;
        //if es null crear un json que diga ok
        if ($estado == null)
        {
            $json_variable="";



         }
         else
            {
                $json_variable = $lims_return_data->estadodte;
            }

    
        // Devuelve el JSON como respuesta
        return response()->json($json_variable);
    }
    

    public function envioJson(Request $request) {
    try{
     
    
        $id = $request->input('id');
       
 
        $numeroControlAnexo =  "";
        $lims_sale_data = Returns::find($id);



        $numeroinput =  $lims_sale_data->numeroControlInput;
      
        $datesale = $lims_sale_data->dateSale;
        $datesale =  substr($datesale, 0, 10);
        $lim_sale_data_anexo = Sale::where('numerocontrol', $numeroinput)->first();

        /// case que me traiga el numero de documento 

        $documentoid1 = $lim_sale_data_anexo->document_id;
        $docu_1;
    
        switch ($documentoid1) {
            case 1:
                $docu_1 = "03";
                break;
            case 2:
                $docu_1 = "01";
                break;
            case 5:
                $docu_1= "11";
                break;

            default:
                break;
            }



        ///la licitacion para ver la venta
        $licitacion = $lim_sale_data_anexo->licitacion;
     //  console.log($licitacion);
        $terceros ="";
        if($licitacion=="on")
        {
           $terceros = "{
               \"nit\":\"06143009941026\",
             \"nombre\":\"Bolsa de Productos de el salvador, s.a. de c.c.\"
           }";
       // $terceros = "null";
        }
        else
        {
            $terceros = "null";
        }
        
 

        $codigogeneracionanexo = $lim_sale_data_anexo->codgeneracion;



        $lims_product_sale_data = ProductReturn::where('return_id', $id)->get();
        $codigoGeneracion = $lims_sale_data->codgeneracion;
        $numerocontroldte = $lims_sale_data->numerocontrol;



 
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $nrc = $lims_customer_data->tax_no ;
        $nit = $lims_customer_data->nit ;
        $nombre = $lims_customer_data->name ;
        $direccion = $lims_customer_data->address ;
        $dui = $lims_customer_data->dui ;
        $sCompany = $lims_customer_data->company_name ;
   
        $telefono = $lims_customer_data->phone_number ;

        $email = $lims_customer_data->email ;
        $estado1 = $lims_customer_data->estado ;
        $municipio = $lims_customer_data->municipio ;

        $address = $lims_customer_data->address;
        $name_giro = $lims_customer_data->gire->name;
        $code_giro = $lims_customer_data->gire->code;



                    $fecha = $lims_sale_data->created_at;
                    $fecEmi = substr($fecha, 0, 10);
                    $horEmi = substr($fecha, -8);


                    // Leer el archivo JSON
            $jsonString = file_get_contents('app/Http/Controllers/company.json');


                    // Analizar el contenido del archivo en un objeto PHP
                    $data = json_decode($jsonString);
                    
                    // Acceder a los valores de los campos
                    $emisorNit = $data->emisor->nit;
                    $emisorNrc = $data->emisor->nrc;
                    $emisorNombre = $data->emisor->nombre;
                    $emisorCodActividad = $data->emisor->codActividad;
                    $emisorDescActividad = $data->emisor->descActividad;
                    $emisorNombreComercial = $data->emisor->nombreComercial;
                    $emisorTipoEstablecimiento = $data->emisor->tipoEstablecimiento;
                    $emisorDireccionDepartamento = $data->emisor->direccion->departamento;
                    $emisorDireccionMunicipio = $data->emisor->direccion->municipio;
                    $emisorDireccionComplemento = $data->emisor->direccion->complemento;
                    $emisorTelefono = $data->emisor->telefono;
                    $emisorCorreo = $data->emisor->correo;
                    $emailauto = $data->emisor->emailauto;
                    $emisorkey = $data->emisor->keypublica;
                    $emisorprivatekey = $data->emisor->keyprivada;
                    $emisorapi = $data->emisor->api;
                    $emisorambiente = $data->emisor->ambiente;


                    
             

                                //documento de credi fiscal


                                     /*AQUI CONSTRUIREMOS JSON PARA ENVIO DE FACTURACION ELECTRONICA*/

        $detalleProductos = [];
        $dIva = 0.00;
        $dSUmNeto = 0.00;
        
  
            // Obtener los productos relacionados con la venta
            foreach ($lims_product_sale_data as $index => $product_sale) {

                $product = product::find($product_sale->product_id);

                $productre = ProductReturn::where('return_id', $id)
                ->where('product_id', $product_sale->product_id)
                ->first();
              
                // Verificar si el producto existe
                if ($product) {
                    $cost = (double) $productre->net_unit_price;
                    $detalleProducto = [
                        "numItem" => $index + 1, // Número secuencial del item
                        "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                        "numeroDocumento" => $codigogeneracionanexo, // Número de documento de referencia"                        
                        "codigo" => null, // Código del producto obtenido de la clase Product
                        "codTributo" => null,
                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                        "cantidad" => $product_sale->qty, // Cantidad del producto
                        "uniMedida" => 59, // Unidad de medida del producto
                        "precioUni" =>  $cost, // Precio unitario del producto
                        "montoDescu" => 0,
                        "ventaNoSuj" => 0,
                        "ventaExenta" => 0,
                        "ventaGravada" => $product_sale->qty * $cost , // Precio total del producto
                        "tributos" => [
                            "20" // Código del impuesto (en este caso, 13% de IVA)
                        ]

                    
                    ];
                    $dIva = $dIva + round((($product_sale->qty * $cost)*0.13),2);
                    $dSUmNeto =   ($product_sale->qty * $cost) + $dSUmNeto  ;
                    $detalleProductos[] = $detalleProducto;
                  
                }
            }

            $dTotal = $dIva + $dSUmNeto;

            $sLetras = $this->numeroALetras($dTotal);
            
            $code_muni = $lims_customer_data->municipio->code;
            $code_estado = $lims_customer_data->estado->code;
     
       $json_variable='{
        "nit": "'.$emisorNit.'",
        "activo": "true",
        "passwordPri": "'.$emisorprivatekey.'",
        "dteJson": {
    
        "identificacion": {
            "version": 3,
            "ambiente": "01",
            "tipoDte": "05",
            "numeroControl": "'.$numerocontroldte.'",
            "codigoGeneracion": "'.$codigoGeneracion.'",
            "tipoModelo": 1,
            "tipoOperacion": 1,
            "tipoContingencia": null,
            "motivoContin": null,
            "fecEmi": "'.$fecEmi.'",
            "horEmi": "'.$horEmi.'",
            "tipoMoneda": "USD"
    
        },
        "documentoRelacionado": [{
        "tipoDocumento": "'.$docu_1.'",
        "tipoGeneracion" :2, 
        "numeroDocumento" : "'.$codigogeneracionanexo.'",
        "fechaEmision":"'.$datesale.'"
        
        }],
        "emisor": {
            "nit": "'.$emisorNit.'",
            "nrc": "'.$emisorNrc.'",
            "nombre": "'.$emisorNombre.'",
            "codActividad": "'.$emisorCodActividad.'",
            "descActividad": "'.$emisorDescActividad.'",
            "nombreComercial": "'.$emisorNombreComercial.'",
            "tipoEstablecimiento": "02",
            "direccion": {
                "departamento": "'.$emisorDireccionDepartamento.'",
                "municipio": "'.$emisorDireccionMunicipio.'",
                "complemento": "'.$emisorDireccionComplemento.'"
            },
            "telefono": "'.$emisorTelefono.'",
            "correo": "'.$emisorCorreo.'"
        },
        "receptor": {
            "nit": "'.$nit.'",
            "nrc": "'.$nrc.'",
            "nombre": "'.$nombre.'",
            "codActividad": "'.$code_giro.'",
            "descActividad": "'.$name_giro.'",
            "nombreComercial": "'.$sCompany.'",
            "direccion": {
                "departamento": "'.$code_estado.'",
                "municipio": "'.$code_muni.'",
                "complemento": "'.$address.'"
            },
            "telefono": "'.$telefono.'",
                        "correo": "'.$email.'"
            },
        "ventaTercero": '.$terceros.',
        "cuerpoDocumento": [],
        "resumen": {
            "totalNoSuj": 0,
            "totalExenta": 0,
                 "totalGravada": '.strval($dSUmNeto).',
                        "subTotalVentas": '.strval($dSUmNeto).',
            "descuNoSuj": 0,
            "descuExenta": 0,
            "descuGravada": 0,
            "totalDescu": 0,
            "tributos": [{
                "codigo": "20",
                "descripcion": "Impuesto al Valor Agregado 13%",
                "valor": '.$dIva.'
            }],
                "subTotal": '.strval($dSUmNeto).',
            "ivaPerci1": 0,
            "ivaRete1": 0,
            "reteRenta": 0,
            "montoTotalOperacion": '.$dTotal.',
            "totalLetras": "'.$sLetras.'",
                  "condicionOperacion": 2
        },
    
            "extension":null,
            "apendice": null
    
        }
        
              }'; 

            
                  // fin
                // Agregar el detalle de productos al JSON existente
                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
           
           //  echo $json_variable;
           // exit();
        
             $sResult = $this->processRequest($json_variable);

         
                $dataresult  = json_decode($sResult, true);
                $data = json_decode($json_variable, true);
                //$tipoDte = $data['dteJson']['identificacion']['tipoDte'];
              
                $numeroControl1 = $data['dteJson']['identificacion']['numeroControl'];
                $fechae =  $data['dteJson']['identificacion']['fecEmi'];

                $sello = $dataresult['selloRecibido'];
                $estado = $dataresult['estado'];
               
                $lims_sale_data->numerocontrol = $numerocontroldte;
                $lims_sale_data->codgeneracion = $codigoGeneracion;
           
                // $lims_sale_data->codgeneracionAnexo = $numeroControlAnexo;
                $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".$codigoGeneracion."&fechaEmi=".$fechae;
                $aqrl = $sqr;

                if ($estado =="PROCESADO")
                {
                    $lims_sale_data->sello = $sello;
                $lims_sale_data->estadodte = "done";
                $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>"; 
                //echo $imgh;
                $jsonString =  $json_variable;


                $jsonData = json_decode($jsonString, true);
                //usar la funcion geninvoice_pdf para generar el pdf
                //coloca una variable donde este el correo electronico dle cliente

            //    $pdf1 = $this->genInvoice_pdf($id);

                // Codifica nuevamente el JSON con la opción JSON_PRETTY_PRINT
                $prettyJson = json_encode($jsonData, JSON_PRETTY_PRINT);

          

              //  $this->enviarCorreoConImagenYTexto($email, "factura electronica", "Gracias por su preferencia a continuacion su qr" , $json_variable, $aqrl, $pdf1);
                $lims_sale_data->update();
                } 
                else {
                    $lims_sale_data->sello = "NA";
                    $lims_sale_data->estadodte = json_encode($dataresult);
                    
                    $lims_sale_data->update();
                } 

                if ($id>0)
                {
                // echo "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>";
                }
                else{

                }


        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);
        if ($estado =="PROCESADO")
        {
            return "done";
        }
        else
        {
            return $estado;
        }
    }
    catch (Exception $e) {
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        exit();
        return $e->getMessage();
    }
    
              
}



function dowjson(Request $request) {
    $id = $request->input('id');


 
    $numeroControlAnexo =  "";
    $lims_sale_data = Returns::find($id);



    $numeroinput =  $lims_sale_data->numeroControlInput;
    $datesale = $lims_sale_data->dateSale;
    $datesale =  substr($datesale, 0, 10);
    $lim_sale_data_anexo = Sale::where('numerocontrol', $numeroinput)->first();

    $codigogeneracionanexo = $lim_sale_data_anexo->codgeneracion;

    $codigoGeneracion = $lims_sale_data->codgeneracion;
    $numerocontroldte = $lims_sale_data->numerocontrol;


    $lims_product_sale_data = ProductReturn::where('return_id', $id)->get();

    $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
    $lims_customer_data = Customer::find($lims_sale_data->customer_id);
    $nrc = $lims_customer_data->tax_no ;
    $nit = $lims_customer_data->nit ;
    $nombre = $lims_customer_data->name ;
    $direccion = $lims_customer_data->address ;
    $dui = $lims_customer_data->dui ;
    $sCompany = $lims_customer_data->company_name ;

    $telefono = $lims_customer_data->phone_number ;

    $email = $lims_customer_data->email ;
    $estado1 = $lims_customer_data->estado ;
    $municipio = $lims_customer_data->municipio ;

    $address = $lims_customer_data->address;
    $name_giro = $lims_customer_data->gire->name;
    $code_giro = $lims_customer_data->gire->code;

          
                
                
              

                $fecha = $lims_sale_data->created_at;
                $fecEmi = substr($fecha, 0, 10);
                $horEmi = substr($fecha, -8);


                // Leer el archivo JSON
        $jsonString = file_get_contents('app/Http/Controllers/company.json');


                // Analizar el contenido del archivo en un objeto PHP
                $data = json_decode($jsonString);
                
                // Acceder a los valores de los campos
                $emisorNit = $data->emisor->nit;
                $emisorNrc = $data->emisor->nrc;
                $emisorNombre = $data->emisor->nombre;
                $emisorCodActividad = $data->emisor->codActividad;
                $emisorDescActividad = $data->emisor->descActividad;
                $emisorNombreComercial = $data->emisor->nombreComercial;
                $emisorTipoEstablecimiento = $data->emisor->tipoEstablecimiento;
                $emisorDireccionDepartamento = $data->emisor->direccion->departamento;
                $emisorDireccionMunicipio = $data->emisor->direccion->municipio;
                $emisorDireccionComplemento = $data->emisor->direccion->complemento;
                $emisorTelefono = $data->emisor->telefono;
                $emisorCorreo = $data->emisor->correo;
                $emailauto = $data->emisor->emailauto;
                $emisorkey = $data->emisor->keypublica;
                $emisorprivatekey = $data->emisor->keyprivada;
                $emisorapi = $data->emisor->api;
                $emisorambiente = $data->emisor->ambiente;


                
                

//documento de credi fiscal


    /*AQUI CONSTRUIREMOS JSON PARA ENVIO DE FACTURACION ELECTRONICA*/

    $detalleProductos = [];
    $dIva = 0.00;
    $dSUmNeto = 0.00;
    
        // Obtener los productos relacionados con la venta
        foreach ($lims_product_sale_data as $index => $product_sale) {
            $product = product::find($product_sale->product_id); // Obtener el producto correspondiente
            
            // Verificar si el producto existe
            if ($product) {
                $cost = (double) $product->cost;

                $detalleProducto = [
                    "numItem" => $index + 1, // Número secuencial del item
                    "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                    "numeroDocumento" => $codigogeneracionanexo, // Número de documento de referencia"                        
                    "codigo" => $product->code, // Código del producto obtenido de la clase Product
                    "codTributo" => null,
                    "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                    "cantidad" => $product_sale->qty, // Cantidad del producto
                    "uniMedida" => 59, // Unidad de medida del producto
                    "precioUni" =>  $cost, // Precio unitario del producto
                    "montoDescu" => 0,
                    "ventaNoSuj" => 0,
                    "ventaExenta" => 0,
                    "ventaGravada" => $product_sale->qty * $cost , // Precio total del producto
                    "tributos" => [
                        "20" // Código del impuesto (en este caso, 13% de IVA)
                    ]

                
                ];
                $dIva = $dIva + (($product_sale->qty * $cost)*0.13);
        $dSUmNeto =   ($product_sale->qty * $cost) + $dSUmNeto  ;
                $detalleProductos[] = $detalleProducto;
            }
        }

        $dIva = number_format($dIva, 2 );
        $dSUmNeto = number_format($dSUmNeto, 2);
        $dTotal = $dIva + $dSUmNeto;
        $sLetras = $this->numeroALetras($dTotal);

        $code_muni = $lims_customer_data->municipio->code;
        $code_estado = $lims_customer_data->estado->code;

   $json_variable='{
    "nit": "'.$emisorNit.'",
    "activo": "true",
    "passwordPri": "'.$emisorprivatekey.'",
    "dteJson": {

    "identificacion": {
        "version": 3,
        "ambiente": "01",
        "tipoDte": "05",
        "numeroControl": "'.$numerocontroldte.'",
        "codigoGeneracion": "'.$codigoGeneracion.'",
        "tipoModelo": 1,
        "tipoOperacion": 1,
        "tipoContingencia": null,
        "motivoContin": null,
        "fecEmi": "'.$fecEmi.'",
        "horEmi": "'.$horEmi.'",
        "tipoMoneda": "USD"

    },
    "documentoRelacionado": [{
    "tipoDocumento": "07",
    "tipoGeneracion" :2, 
    "numeroDocumento" : "'.$codigogeneracionanexo.'",
    "fechaEmision":"'.$datesale.'"
    
    }],
    "emisor": {
        "nit": "'.$emisorNit.'",
        "nrc": "'.$emisorNrc.'",
        "nombre": "'.$emisorNombre.'",
        "codActividad": "'.$emisorCodActividad.'",
        "descActividad": "'.$emisorDescActividad.'",
        "nombreComercial": "'.$emisorNombreComercial.'",
        "tipoEstablecimiento": "02",
        "direccion": {
            "departamento": "'.$emisorDireccionDepartamento.'",
            "municipio": "'.$emisorDireccionMunicipio.'",
            "complemento": "'.$emisorDireccionComplemento.'"
        },
        "telefono": "'.$emisorTelefono.'",
        "correo": "'.$emisorCorreo.'"
    },
    "receptor": {
        "nit": "'.$nit.'",
        "nrc": "'.$nrc.'",
        "nombre": "'.$nombre.'",
        "codActividad": "'.$code_giro.'",
        "descActividad": "'.$name_giro.'",
        "nombreComercial": "'.$sCompany.'",
        "direccion": {
            "departamento": "'.$code_estado.'",
            "municipio": "'.$code_muni.'",
            "complemento": "'.$address.'"
        },
        "telefono": "'.$telefono.'",
                    "correo": "'.$email.'"
        },
    "ventaTercero": '.$terceros.',
    "cuerpoDocumento": [],
    "resumen": {
        "totalNoSuj": 0,
        "totalExenta": 0,
        "totalGravada": '.$dSUmNeto.',
        "subTotalVentas": '.$dSUmNeto.',
        "descuNoSuj": 0,
        "descuExenta": 0,
        "descuGravada": 0,
        "totalDescu": 0,
        "tributos": [{
            "codigo": "20",
            "descripcion": "Impuesto al Valor Agregado 13%",
            "valor": '.$dIva.'
        }],
        "subTotal": '.$dSUmNeto.',
        "ivaPerci1": 0,
        "ivaRete1": 0,
        "reteRenta": 0,
        "montoTotalOperacion": '.$dTotal.',
        "totalLetras": "'. $sLetras.' ",
              "condicionOperacion": 2
    },

        "extension":null,
        "apendice": null

    }
    
}'; 

        
              // fin
            // Agregar el detalle de productos al JSON existente
            $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
            

            $data = json_decode($json_variable);

            // Volver a codificar con formato legible
            $pretty_json = json_encode($data, JSON_PRETTY_PRINT);
            
            // Imprimir el JSON formateado
            echo $pretty_json;
         
   }


}
