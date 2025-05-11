<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warehouse;
use App\Supplier;
use App\Product;
use App\Unit;
use App\Tax;
use App\Account; 
use App\Purchase;
use App\ProductPurchase;
use App\Product_Warehouse;
use App\Payment;
use App\PaymentWithCheque;
use App\PaymentWithCreditCard;
use App\PosSetting;
use DB;
use App\GeneralSetting;
use Stripe\Stripe;
use Auth;
use App\User;
use App\ProductVariant;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Customer;
use App\Types_document;

use App\Returns;
use App\ReturnPurchase;
use App\PurchaseProductReturn;


use DateTime;
class PurchaseController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('purchases-index')){            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_purchase_list = Purchase::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_purchase_list = Purchase::orderBy('id', 'desc')->get();
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            $lims_pos_setting_data = PosSetting::latest()->first();
            $lims_account_list = Account::where('is_active', true)->get();

            //dd($lims_purchase_list);
            return view('purchase.index', compact('lims_purchase_list', 'lims_account_list', 'all_permission', 'lims_pos_setting_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function purchaseData(Request $request)
    {
        $columns = array( 
            1 => 'created_at', 
            2 => 'invoice',
            5 => 'grand_total',
            6 => 'paid_amount',
        );
        
        if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            $totalData = Purchase::where('user_id', Auth::id())->count();
        else
            $totalData = Purchase::count();

        $totalFiltered = $totalData;

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value'))){
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $purchases = Purchase::with('supplier', 'warehouse')->offset($start)
                            ->where('user_id', Auth::id())
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            else
                $purchases = Purchase::with('supplier', 'warehouse')->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();

        }
        else
        {
            $search = $request->input('search.value');
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own') {
                $purchases =  Purchase::select('purchases.*')
                            ->with('supplier', 'warehouse')
                            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                            ->whereDate('purchases.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                            ->where('purchases.user_id', Auth::id())
                            ->orwhere([
                                ['purchases.invoice', 'LIKE', "%{$search}%"],
                                ['purchases.user_id', Auth::id()]
                            ])
                            ->orwhere([
                                ['suppliers.name', 'LIKE', "%{$search}%"],
                                ['purchases.user_id', Auth::id()]
                            ])
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)->get();

                $totalFiltered = Purchase::
                            leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                            ->whereDate('purchases.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                            ->where('purchases.user_id', Auth::id())
                            ->orwhere([
                                ['purchases.invoice', 'LIKE', "%{$search}%"],
                                ['purchases.user_id', Auth::id()]
                            ])
                            ->orwhere([
                                ['suppliers.name', 'LIKE', "%{$search}%"],
                                ['purchases.user_id', Auth::id()]
                            ])
                            ->count();
            }
            else {
                $purchases =  Purchase::select('purchases.*')
                            ->with('supplier', 'warehouse')
                            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                            ->whereDate('purchases.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                            ->orwhere('purchases.invoice', 'LIKE', "%{$search}%")
                            ->orwhere('suppliers.name', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

                $totalFiltered = Purchase::
                                leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                                ->whereDate('purchases.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                                ->orwhere('purchases.invoice', 'LIKE', "%{$search}%")
                                ->orwhere('suppliers.name', 'LIKE', "%{$search}%")
                                ->count();
            }
        }
        $data = array();
        if(!empty($purchases))
        {
            foreach ($purchases as $key=>$purchase)
            {
                $nestedData['id'] = $purchase->id;
                $nestedData['key'] = $key;
                $product_image = explode(",", $purchase->document);
                $product_image = htmlspecialchars($product_image[0]);
                
                $product_images = explode(",", $purchase->document);

                // Extrae el primer archivo y elimina cualquier carácter especial
                $firstFileName = htmlspecialchars(trim($product_images[0]));

                // Obtener la extensión del primer archivo
                $extension = pathinfo($firstFileName, PATHINFO_EXTENSION);

                // Nombre del archivo, por ejemplo desde la base de datos
                $fileName = $product_image;

                // Obtener la extensión del archivo
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                
                if($product_image) {
                    if($extension === "pdf") {
                        $nestedData['document'] = '<img src="'.url('public/documents/purchase/pdf.png').'" height="80" width="80">';
                    }
                    else {
                        $nestedData['document'] = '<img src="'.url('public/documents/purchase', $product_image).'" height="80" width="80">';    
                    }                    
                }
                else {
                    $nestedData['document'] = '<img src="'.url('public/documents/purchase/ina.png').'" height="80" width="80">';
                }
                
                $nestedData['date'] = date(config('date_format'), strtotime($purchase->created_at->toDateString()));
                $nestedData['reference_no'] = $purchase->reference_no;
                $nestedData['invoice'] = $purchase->invoice;
                
                if($purchase->supplier_id) {
                    $supplier = $purchase->supplier;
                }
                else {
                    $supplier = new Supplier();
                }
                $nestedData['supplier'] = $supplier->name;
                if($purchase->status == 1){
                    $nestedData['purchase_status'] = '<div class="badge badge-success">'.trans('file.Recieved').'</div>';
                    $purchase_status = trans('file.Recieved');
                }
                elseif($purchase->status == 2){
                    $nestedData['purchase_status'] = '<div class="badge badge-success">'.trans('file.Partial').'</div>';
                    $purchase_status = trans('file.Partial');
                }
                elseif($purchase->status == 3){
                    $nestedData['purchase_status'] = '<div class="badge badge-danger">'.trans('file.Pending').'</div>';
                    $purchase_status = trans('file.Pending');
                }
                else{
                    $nestedData['purchase_status'] = '<div class="badge badge-danger">'.trans('file.Ordered').'</div>';
                    $purchase_status = trans('file.Ordered');
                }

                if($purchase->payment_status == 1){
                    $nestedData['payment_status'] = '<div class="badge badge-danger">'.trans('file.Due').'</div>';
                }
                else{
                    $nestedData['payment_status'] = '<div class="badge badge-success">'.trans('file.Paid').'</div>';
                }

              $fecha1 = date("Y-m-d");
              $fecha2 = $purchase->estimated_delivery_date;
     
              $fecha1= new DateTime($fecha1);
               $fecha2= new DateTime($fecha2);
               $diff = $fecha1->diff($fecha2);

                if($fecha1 <= $fecha2){
                $interval = $diff->days;
           
            }else{
                $interval = $diff->days*-1;
       
            }
                
                   
                 if($interval >=15){
                    $nestedData['delivery_status'] = '<div class="badge badge-success">En tiempo</div>';
            }
                
                elseif($interval <=15 && $interval >=0){
                    $nestedData['delivery_status'] = '<div class="badge badge-warning">Menos de 15 dias</div>';
                 }
                 elseif($interval <0){
                    $nestedData['delivery_status'] = '<div class="badge badge-danger">Vencida</div>';
                  }
                  else{
                    $nestedData['delivery_status'] = '<div class="badge badge-danger">Vencida</div>';
                   }

                $nestedData['grand_total'] = number_format($purchase->grand_total, 2);
                $nestedData['paid_amount'] = number_format($purchase->paid_amount, 2);
                $nestedData['due'] = number_format($purchase->grand_total - $purchase->paid_amount, 2);
                
                $nestedData['confirmation_date'] = $purchase->confirmation_date;
                $nestedData['dispatch_date'] = $purchase->dispatch_date;
                $nestedData['estimated_delivery_date'] = $purchase->estimated_delivery_date;

                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> '.trans('file.View').'</button>
                                </li>';
                                
                $nestedData['options'] .= '<li>
                    <a href="'.route('purchases.download', $purchase->id).'" class="btn btn-link"><i class="dripicons-download"></i>'.trans('file.Download').'</a>
                </li>';  

                $nestedData['options'] .= '<li>
                    <a href="' . route('purchases.document', $purchase->id) . '" class="btn btn-link" target="_blank" rel="noopener noreferrer">
                        <i class="dripicons-zoom-out"></i>' . trans('file.Attach') . '
                    </a>
                </li>';         
                                
                // if(in_array("purchases-edit", $request['all_permission']))
                //     $nestedData['options'] .= '<li>
                //         <a href="'.route('purchases.edit', $purchase->id).'" class="btn btn-link"><i class="dripicons-document-edit"></i> '.trans('file.edit').'</a>
                //         </li>';
                $nestedData['options'] .= 
                    '<li>
                        <button type="button" class="add-payment btn btn-link" data-id = "'.$purchase->id.'" data-toggle="modal" data-target="#add-payment"><i class="fa fa-plus"></i> '.trans('file.Add Payment').'</button>
                    </li>
                    <li>
                        <button type="button" class="get-payment btn btn-link" data-id = "'.$purchase->id.'"><i class="fa fa-money"></i> '.trans('file.View Payment').'</button>
                    </li>';
                if(in_array("purchases-delete", $request['all_permission']))
                    $nestedData['options'] .= \Form::open(["route" => ["purchases.destroy", $purchase->id], "method" => "DELETE"] ).'
                            <li>
                              <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> '.trans("file.delete").'</button> 
                            </li>'.\Form::close().'
                            <li><a href="#" class="edit-product btn btn-link edit_dates" data-toggle="modal" data-target="#editModal" data-purchase-id = "'.$purchase->id.'" >Gestion fechas</a></li>
                        </ul>
                    </div>';

                // data for purchase details by one click
                $user = User::find($purchase->user_id);
                $customer = Customer::find($purchase->customer_id);

                $nestedData['purchase'] = array( '[ "'.date(config('date_format'), strtotime($purchase->created_at->toDateString())).'"', ' "'.$purchase->reference_no.'"', ' "'.$purchase_status.'"',  ' "'.$purchase->id.'"', ' "'.$purchase->warehouse->name.'"', ' "'.$purchase->warehouse->phone.'"', ' "'.$purchase->warehouse->address.'"', ' "'.$supplier->name.'"', ' "'.$supplier->company_name.'"', ' "'.$supplier->email.'"', ' "'.$supplier->phone_number.'"', ' "'.$supplier->address.'"', ' "'.$supplier->city.'"', ' "'.$purchase->total_tax.'"', ' "'.$purchase->total_discount.'"', ' "'.$purchase->total_cost.'"', ' "'.$purchase->order_tax.'"', ' "'.$purchase->order_tax_rate.'"', ' "'.$purchase->order_discount.'"', ' "'.$purchase->shipping_cost.'"', ' "'.$purchase->grand_total.'"', ' "'.$purchase->paid_amount.'"', ' "'.preg_replace('/\s+/S', " ", $purchase->note).'"', ' "'.$user->name.'"', ' "'.$user->email.'"',/*typepurchase es 25*/ '"'.$purchase->type_purchase.'"', '"'.$customer->name.'"', '"'.$customer->address.'"', '"'.$purchase->serial.'"', '"'.$purchase->chemistry_application.'", "'.$purchase->email_suppliers.'", "'.$purchase->terms.'", "'.$purchase->name_purchase.'", "'.$purchase->email_purchase.'", "'.$purchase->document.'", "'.$purchase->po_number.'", "'.$purchase->suppliers_name.'", "'.$purchase->invoice.'"]'
                );
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );
            
        echo json_encode($json_data);
    }



    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('purchases-add')){
            $lims_supplier_list = Supplier::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_product_list_without_variant = $this->productWithoutVariant();
            $lims_product_list_with_variant = $this->productWithVariant();
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_documents_list = Types_document::where('modulo', 'POC')->get();

            return view('purchase.create', compact('lims_supplier_list', 'lims_warehouse_list', 'lims_tax_list', 'lims_product_list_without_variant', 'lims_product_list_with_variant','lims_customer_list','lims_documents_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function productWithoutVariant()
    {
        return Product::ActiveStandard()->select('id', 'name', 'code')
                ->whereNull('is_variant')->get();
    }

    public function productWithVariant()
    {
        return Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->ActiveStandard()
                ->whereNotNull('is_variant')
                ->select('products.id', 'products.name', 'product_variants.item_code')
                ->orderBy('position')->get();
    }

    public function limsProductSearch(Request $request)
    {
        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");
        $lims_product_data = Product::where('code', $product_code[0])->first();
        if(!$lims_product_data) {
            $lims_product_data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.*', 'product_variants.item_code')
                ->where('product_variants.item_code', $product_code[0])
                ->first();
        }

        $product[] = $lims_product_data->name;
        if($lims_product_data->is_variant)
            $product[] = $lims_product_data->item_code;
        else
            $product[] = $lims_product_data->code;
        $product[] = $lims_product_data->cost;
        
        if ($lims_product_data->tax_id) {
            $lims_tax_data = Tax::find($lims_product_data->tax_id);
            $product[] = $lims_tax_data->rate;
            $product[] = $lims_tax_data->name;
        } else {
            $product[] = 0;
            $product[] = 'No Tax';
        }
        $product[] = $lims_product_data->tax_method;

        $units = Unit::where("base_unit", $lims_product_data->unit_id)
                    ->orWhere('id', $lims_product_data->unit_id)
                    ->get();
        $unit_name = array();
        $unit_operator = array();
        $unit_operation_value = array();
        foreach ($units as $unit) {
            if ($lims_product_data->purchase_unit_id == $unit->id) {
                array_unshift($unit_name, $unit->unit_name);
                array_unshift($unit_operator, $unit->operator);
                array_unshift($unit_operation_value, $unit->operation_value);
            } else {
                $unit_name[]  = $unit->unit_name;
                $unit_operator[] = $unit->operator;
                $unit_operation_value[] = $unit->operation_value;
            }
        }
        
        $product[] = implode(",", $unit_name) . ',';
        $product[] = implode(",", $unit_operator) . ',';
        $product[] = implode(",", $unit_operation_value) . ',';
        $product[] = $lims_product_data->id;
        return $product;
    }

    public function store(Request $request)
    {
        $data = $request->except('document');
        //dd($data);
        $data['user_id'] = Auth::id();
        $data['name_purchase'] = auth()->user()->name_purchase;
        $data['email_purchase'] = auth()->user()->email_purchase;
        $data['reference_no'] = 'pr-' . date("Ymd") . '-'. date("his");
        //$data['po_number'] = Types_document::find("6");
        
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
            $document->move('public/documents/purchase', $documentName);
            $data['document'] = $documentName;
        }

        if($data["status"]==1){
        $data["date_received"] =date("Y-m-d");
            
        }
        //return dd($data);
        Purchase::create($data);

        $lims_purchase_data = Purchase::latest()->first();

        $status = $lims_purchase_data->status; 

        $lims_data_purchase = Types_document::find($data["document_id"]);

        $nuevocorrelativo =$data['po_number']+1;

        $data_actualiza["correlativo"] =$nuevocorrelativo; 

        Types_document::where([
            ['id', $data["document_id"]]
        ])->update($data_actualiza);


        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $qty = $data['qty'];
        $recieved = $data['recieved'];
        $purchase_unit = $data['purchase_unit'];
        $net_unit_cost = $data['net_unit_cost'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];
        $product_purchase = [];

        foreach ($product_id as $i => $id) {
            $lims_purchase_unit_data  = Unit::where('unit_name', $purchase_unit[$i])->first();

            if ($lims_purchase_unit_data->operator == '*') {
                $quantity = $recieved[$i] * $lims_purchase_unit_data->operation_value;
            } else {
                $quantity = $recieved[$i] / $lims_purchase_unit_data->operation_value;
            }
            $lims_product_data = Product::find($id);
            if($lims_product_data->is_variant) {
                $lims_product_variant_data = ProductVariant::select('id', 'variant_id', 'qty')->FindExactProductWithCode($lims_product_data->id, $product_code[$i])->first();
                $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $id],
                    ['variant_id', $lims_product_variant_data->variant_id],
                    ['warehouse_id', $data['warehouse_id']]
                ])->first();
                $product_purchase['variant_id'] = $lims_product_variant_data->variant_id;
                //add quantity to product variant table
                 if($status==1){ 

             

                $lims_product_variant_data->qty += $quantity;

                $lims_product_variant_data->save();
                }
            }
            else {
                $product_purchase['variant_id'] = null;
                $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $id],
                    ['warehouse_id', $data['warehouse_id'] ],
                ])->first();
            }
            //add quantity to product table
            /*se agrega solo si el estado de la compra es 1 osea recibido promedio */
            
            if($status==1){

            if($lims_purchase_data->type_purchase==1){
            $lims_product_data->qty = $this->cantidad_actual($id)+$qty[$i]; 
            $costo_promedio= $this->costo_promedio($id, $qty[$i], $net_unit_cost[$i]); 
            $lims_product_data->cost = $costo_promedio;

            $lims_product_data->save();
           

            }

            

 
           

           /*Valor 1 sera recalculado a aprtie de la sumatoria de la cantidad que se vendio - la existencia en negativo */
           /*Aqui ejecutaremos el recalculo de costo promedio*/
          /*
             $datos = DB::select('
                SELECT 
                CASE
                        WHEN (sum(qty) - sum( existence)) = 0 THEN 1
                        WHEN (sum(qty) - sum( existence)) < 0 THEN 1
                        ELSE (sum(qty) - sum( existence))
                    END

                 as valor1
                from product_sales
                WHERE 
                product_id='.$id."
                and existence<0
                and unit_cost=0 
                ");



                $valor1= ( $datos[0]->valor1*$lims_product_data->cost);
                $valor2= $quantity*$net_unit_cost[$i];
 
             $cantidad_actual = $lims_product_data->qty; 
             if($cantidad_actual<0){
                $cantidad_actual =0; 
             }

             $valor3 =$datos[0]->valor1+$quantity;

            $lims_product_data->qty = $valor3;
            $costo_promedio= (($valor1+$valor2)/$valor3); 
         
            $lims_product_data->cost =$costo_promedio; 
             
            //((($lims_product_data->qty*$lims_product_data->cost)+($quantity*$net_unit_cost ))/($lims_product_data->qty+$quantity));
            $lims_product_data->save();
            /*Logica para guardar la fecha de recibo*/


                
            }
            //add quantity to warehouse

             if($status==1){


            if ($lims_product_warehouse_data) {

                $lims_product_warehouse_data->qty = $lims_product_warehouse_data->qty + $quantity;
            } 
            else {
                $lims_product_warehouse_data = new Product_Warehouse();
                $lims_product_warehouse_data->product_id = $id;
                $lims_product_warehouse_data->warehouse_id = $data['warehouse_id'];
                $lims_product_warehouse_data->qty = $quantity;
                if($lims_product_data->is_variant)
                    $lims_product_warehouse_data->variant_id = $lims_product_variant_data->variant_id;
            }

            $lims_product_warehouse_data->save();
           } 
            $product_purchase['purchase_id'] = $lims_purchase_data->id ;
            $product_purchase['product_id'] = $id;
            $product_purchase['qty'] = $qty[$i];
            $product_purchase['recieved'] = $recieved[$i];
            $product_purchase['purchase_unit_id'] = $lims_purchase_unit_data->id;
            $product_purchase['net_unit_cost'] = $net_unit_cost[$i];
            $product_purchase['net_unit_cost_original'] = $net_unit_cost[$i];
            $product_purchase['discount'] = $discount[$i];
            $product_purchase['tax_rate'] = $tax_rate[$i];
            $product_purchase['tax'] = $tax[$i];
            $product_purchase['total'] = $total[$i];
            ProductPurchase::create($product_purchase);
        }

        $this->recordKardex($lims_purchase_data->id, 2,1);


        return redirect('purchases')->with('message', 'Purchase created successfully');
    }




    //Type = 1 Sale
   //Type=2 Purchase
    //Type = 1 Sale
   //Type=2 Purchase
     public function recordKardex($idtran, $typetransaction, $signo)
    {


      if($typetransaction ==1){
        //Datos de la venta disponibles. 
        if($signo ==-1){
             $lims_purchase_data = Sale::find($idtran);

             $lims_product_purchase_data = Product_Sale::where('sale_id', $idtran)->get();
             $concepto = 'Venta';

        }else{
            $lims_purchase_data = Returns::find($idtran);

             $lims_product_purchase_data = ProductReturn::where('return_id', $idtran)->get();

              $concepto = 'Retorno Venta';



        }
       
         $referencia = $lims_purchase_data->reference_no; 

        

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
         $invoice = $lims_purchase_data->invoice; 
         

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
                        '',
                        '".$concepto."',
                        ".$signo.",
                        ".$saldo.",
                         ".$costo_unitario_promedio.",
                         ".$invoice."
                      ) ;
                        ");

        }
         
      }

  
      
    }
 


    public function productPurchaseData($id)
    {
        $lims_product_purchase_data = ProductPurchase::where('purchase_id', $id)->get();
        foreach ($lims_product_purchase_data as $key => $product_purchase_data) {
            $product = Product::find($product_purchase_data->product_id);
            $unit = Unit::find($product_purchase_data->purchase_unit_id);
            if($product_purchase_data->variant_id) {
                $lims_product_variant_data = ProductVariant::FindExactProduct($product->id, $product_purchase_data->variant_id)->select('item_code')->first();
                $product->code = $lims_product_variant_data->item_code;
            }
            $product_purchase[0][$key] = $product->name;
            $product_purchase[1][$key] = $product_purchase_data->qty;
            $product_purchase[2][$key] = $unit->unit_code;
            $product_purchase[3][$key] = $product_purchase_data->tax;
            $product_purchase[4][$key] = $product_purchase_data->tax_rate;
            $product_purchase[5][$key] = $product_purchase_data->discount;
            $product_purchase[6][$key] = $product_purchase_data->total;
            $product_purchase[7][$key] = $product->code;
        }
        return $product_purchase;
    }

    public function purchaseByCsv()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('purchases-add')){
            $lims_supplier_list = Supplier::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();

            return view('purchase.import', compact('lims_supplier_list', 'lims_warehouse_list', 'lims_tax_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function importPurchase(Request $request)
    {
        //get the file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        //checking if this is a CSV file
        if($ext != 'csv')
            return redirect()->back()->with('message', 'Please upload a CSV file');

        $filePath=$upload->getRealPath();
        $file_handle = fopen($filePath, 'r');
        $i = 0;
        //validate the file
        while (!feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if($current_line && $i > 0){
                $product_data[] = Product::where('code', $current_line[0])->first();
                if(!$product_data[$i-1])
                    return redirect()->back()->with('message', 'Product with this code '.$current_line[0].' does not exist!');
                $unit[] = Unit::where('unit_code', $current_line[2])->first();
                if(!$unit[$i-1])
                    return redirect()->back()->with('message', 'Purchase unit does not exist!');
                if(strtolower($current_line[5]) != "no tax"){
                    $tax[] = Tax::where('name', $current_line[5])->first();
                    if(!$tax[$i-1])
                        return redirect()->back()->with('message', 'Tax name does not exist!');
                }
                else
                    $tax[$i-1]['rate'] = 0;

                $qty[] = $current_line[1];
                $cost[] = $current_line[3];
                $discount[] = $current_line[4];
            }
            $i++;
        }

        $data = $request->except('file');
        $data['reference_no'] = 'pr-' . date("Ymd") . '-'. date("his");
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

            $ext = pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION);
            $documentName = $data['reference_no'] . '.' . $ext;
            $document->move('public/documents/purchase', $documentName);
            $data['document'] = $documentName;
        }
        $item = 0;
        $grand_total = $data['shipping_cost'];
        $data['user_id'] = Auth::id();
        Purchase::create($data);
        $lims_purchase_data = Purchase::latest()->first();
        
        foreach ($product_data as $key => $product) {
            if($product['tax_method'] == 1){
                $net_unit_cost = $cost[$key] - $discount[$key];
                $product_tax = $net_unit_cost * ($tax[$key]['rate'] / 100) * $qty[$key];
                $total = ($net_unit_cost * $qty[$key]) + $product_tax;
            }
            elseif($product['tax_method'] == 2){
                $net_unit_cost = (100 / (100 + $tax[$key]['rate'])) * ($cost[$key] - $discount[$key]);
                $product_tax = ($cost[$key] - $discount[$key] - $net_unit_cost) * $qty[$key];
                $total = ($cost[$key] - $discount[$key]) * $qty[$key];
            }
            if($data['status'] == 1){
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product['qty'] += $quantity;
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['warehouse_id']]
                ])->first();
                if($product_warehouse) {
                    $product_warehouse->qty += $quantity;
                    $product_warehouse->save();
                }
                else {
                    $lims_product_warehouse_data = new Product_Warehouse();
                    $lims_product_warehouse_data->product_id = $product['id'];
                    $lims_product_warehouse_data->warehouse_id = $data['warehouse_id'];
                    $lims_product_warehouse_data->qty = $quantity;
                    $lims_product_warehouse_data->save();
                }
                $product->save();
            }
            
            $product_purchase = new ProductPurchase();
            $product_purchase->purchase_id = $lims_purchase_data->id;
            $product_purchase->product_id = $product['id'];
            $product_purchase->qty = $qty[$key];
            if($data['status'] == 1)
                $product_purchase->recieved = $qty[$key];
            else
                $product_purchase->recieved = 0;
            $product_purchase->purchase_unit_id = $unit[$key]['id'];
            $product_purchase->net_unit_cost = number_format((float)$net_unit_cost, 2, '.', '');
            $product_purchase->discount = $discount[$key] * $qty[$key];
            $product_purchase->tax_rate = $tax[$key]['rate'];
            $product_purchase->tax = number_format((float)$product_tax, 2, '.', '');
            $product_purchase->total = number_format((float)$total, 2, '.', '');
            $product_purchase->save();
            $lims_purchase_data->total_qty += $qty[$key];
            $lims_purchase_data->total_discount += $discount[$key] * $qty[$key];
            $lims_purchase_data->total_tax += number_format((float)$product_tax, 2, '.', '');
            $lims_purchase_data->total_cost += number_format((float)$total, 2, '.', '');
        }
        $lims_purchase_data->item = $key + 1;
        $lims_purchase_data->order_tax = ($lims_purchase_data->total_cost - $lims_purchase_data->order_discount) * ($data['order_tax_rate'] / 100);
        $lims_purchase_data->grand_total = ($lims_purchase_data->total_cost + $lims_purchase_data->order_tax + $lims_purchase_data->shipping_cost) - $lims_purchase_data->order_discount;
        $lims_purchase_data->save();
        return redirect('purchases');
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('purchases-edit')){
            $lims_supplier_list = Supplier::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_product_list_without_variant = $this->productWithoutVariant();
            $lims_product_list_with_variant = $this->productWithVariant();
            $lims_purchase_data = Purchase::find($id);
            $lims_product_purchase_data = ProductPurchase::where('purchase_id', $id)->get();
             $lims_customer_list = Customer::where('is_active', true)->get();


            return view('purchase.edit', compact('lims_warehouse_list', 'lims_supplier_list', 'lims_product_list_without_variant', 'lims_product_list_with_variant', 'lims_tax_list', 'lims_purchase_data', 'lims_product_purchase_data','lims_customer_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
        
    }

    public function update(Request $request, $id)


    {


        $data = $request->except('document');
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
            $document->move('public/purchase/documents', $documentName);
            $data['document'] = $documentName;
        }
        //return dd($data);
        $balance = $data['grand_total'] - $data['paid_amount'];
        if ($balance < 0 || $balance > 0) {
            $data['payment_status'] = 1;
        } else {
            $data['payment_status'] = 2;
        }
        $lims_purchase_data = Purchase::find($id);
        
        $lims_product_purchase_data = ProductPurchase::where('purchase_id', $id)->get();

         $status = $lims_purchase_data->status; 


        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $qty = $data['qty'];
        $recieved = $data['recieved'];
        $purchase_unit = $data['purchase_unit'];
        $net_unit_cost = $data['net_unit_cost'];
        $net_unit_cost_original = $data['net_unit_cost_original'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];
        $product_purchase = [];
        /*Este update estaba al final pero lo necesito antes para que la variable de estado se llene con el 
        valor del combo.
        */
        $lims_purchase_data->update($data);

        $lims_purchase_data = Purchase::find($id);
        
        $lims_product_purchase_data = ProductPurchase::where('purchase_id', $id)->get();

        $status = $lims_purchase_data->status; 


        foreach ($lims_product_purchase_data as $product_purchase_data) {

            $old_recieved_value = $product_purchase_data->recieved;
            $lims_purchase_unit_data = Unit::find($product_purchase_data->purchase_unit_id);
            
            
            if ($lims_purchase_unit_data->operator == '*') {
                $old_recieved_value = $old_recieved_value * $lims_purchase_unit_data->operation_value;
            } else {
                $old_recieved_value = $old_recieved_value / $lims_purchase_unit_data->operation_value;
            }
            $lims_product_data = Product::find($product_purchase_data->product_id);
            if($lims_product_data->is_variant) {
                $lims_product_variant_data = ProductVariant::select('id', 'variant_id', 'qty')->FindExactProduct($lims_product_data->id, $product_purchase_data->variant_id)->first();
                $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $lims_product_data->id],
                    ['variant_id', $product_purchase_data->variant_id],
                    ['warehouse_id', $lims_purchase_data->warehouse_id]
                ])->first();
                $lims_product_variant_data->qty -= $old_recieved_value;
                $lims_product_variant_data->save();
            }
            else {
                $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $product_purchase_data->product_id],
                    ['warehouse_id', $lims_purchase_data->warehouse_id],
                    ])->first();
            }

            $lims_product_data->qty -= $old_recieved_value;

            $lims_product_warehouse_data->qty -= $old_recieved_value;
            $lims_product_warehouse_data->save();
            $lims_product_data->save();
            $product_purchase_data->delete();
          
        }

        foreach ($product_id as $key => $pro_id) {

            $lims_purchase_unit_data = Unit::where('unit_name', $purchase_unit[$key])->first();
            if ($lims_purchase_unit_data->operator == '*') {
                $new_recieved_value = $recieved[$key] * $lims_purchase_unit_data->operation_value;
            } else {
                $new_recieved_value = $recieved[$key] / $lims_purchase_unit_data->operation_value;
            }

            $lims_product_data = Product::find($pro_id);
            if($lims_product_data->is_variant) {
                $lims_product_variant_data = ProductVariant::select('id', 'variant_id', 'qty')->FindExactProductWithCode($pro_id, $product_code[$key])->first();
                $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $pro_id],
                    ['variant_id', $lims_product_variant_data->variant_id],
                    ['warehouse_id', $data['warehouse_id']]
                ])->first();
                $product_purchase['variant_id'] = $lims_product_variant_data->variant_id;
                //add quantity to product variant table
                $lims_product_variant_data->qty += $new_recieved_value;
                $lims_product_variant_data->save();
            }
            else {
                $product_purchase['variant_id'] = null;
                $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $pro_id],
                    ['warehouse_id', $data['warehouse_id'] ],
                ])->first();
            }

           // $lims_product_data->qty += $new_recieved_value;

            if($lims_product_warehouse_data){
                $lims_product_warehouse_data->qty += $new_recieved_value;
                $lims_product_warehouse_data->save();
            }
            else {
                $lims_product_warehouse_data = new Product_Warehouse();
                $lims_product_warehouse_data->product_id = $pro_id;
                if($lims_product_data->is_variant)
                    $lims_product_warehouse_data->variant_id = $lims_product_variant_data->variant_id;
                $lims_product_warehouse_data->warehouse_id = $data['warehouse_id'];
                $lims_product_warehouse_data->qty = $new_recieved_value;
                $lims_product_warehouse_data->save();
            }
               
          
            if($status==1){
             if($lims_purchase_data->type_purchase==1){
            $lims_product_data->qty = $this->cantidad_actual($pro_id)+$qty[$key]; 
            $costo_promedio= $this->costo_promedio($pro_id, $qty[$key], $net_unit_cost[$key]); 
            $lims_product_data->cost = $costo_promedio;
            }

                /*
             $quantity =  $new_recieved_value; 

            
       
              $datos = DB::select('
                SELECT 
                CASE
                        WHEN (sum(qty) - sum( existence)) = 0 THEN 1
                        WHEN (sum(qty) - sum( existence)) < 0 THEN 1
                        ELSE (sum(qty) - sum( existence))
                    END

                 as valor1
                from product_sales
                WHERE 
                product_id='.$id."
                and existence<0
                and unit_cost=0 
                ");


             $valor1= number_format(( $datos[0]->valor1*$lims_product_data->cost),2);
             $valor2= $quantity*$net_unit_cost[$key];
             $cantidad_actual = $lims_product_data->qty; 
             if($cantidad_actual<0){
                $cantidad_actual =0; 
             }
             $valor3 =$datos[0]->valor1+$quantity;

            $lims_product_data->qty = $valor3;
             $costo_promedio= (($valor1+$valor2)/$valor3); 
         
            $lims_product_data->cost =$costo_promedio; 
            */


            $lims_product_data->save();
            /*Vamos a actualizar la fecha de recibido*/
    
        $lims_purchase_data = Purchase::find($id);
        

        $data["id"] = $id;
        $data["date_received"] = date("Y-m-d");
       
        /*Este update estaba al final pero lo necesito antes para que la variable de estado se llene con el 
        valor del combo.
        */
        $lims_purchase_data->update($data);

        }

        if(isset($net_unit_cost_original[$key])){
           $costo_original = $net_unit_cost_original[$key];
        }else{
          $costo_original = $net_unit_cost[$key]; 

        }

            $product_purchase['purchase_id'] = $id ;
            $product_purchase['product_id'] = $pro_id;
            $product_purchase['qty'] = $qty[$key];
            $product_purchase['recieved'] = $recieved[$key];
            $product_purchase['purchase_unit_id'] = $lims_purchase_unit_data->id;
            $product_purchase['net_unit_cost'] = $net_unit_cost[$key];
            $product_purchase['net_unit_cost_original'] =$costo_original; 
            
            $product_purchase['discount'] = $discount[$key];
            $product_purchase['tax_rate'] = $tax_rate[$key];
            $product_purchase['tax'] = $tax[$key];
            $product_purchase['total'] = $total[$key];
            ProductPurchase::create($product_purchase);
           


        }

       

        

        return redirect('purchases')->with('message', 'Purchase updated successfully');
    }

    public function addPayment(Request $request)
    {
        $data = $request->all();
        $lims_purchase_data = Purchase::find($data['purchase_id']);
        $lims_purchase_data->paid_amount += $data['amount'];
        $balance = $lims_purchase_data->grand_total - $lims_purchase_data->paid_amount;
        if($balance > 0 || $balance < 0)
            $lims_purchase_data->payment_status = 1;
        elseif ($balance == 0)
            $lims_purchase_data->payment_status = 2;
        $lims_purchase_data->save();

        if($data['paid_by_id'] == 1)
            $paying_method = 'Cash';
        elseif ($data['paid_by_id'] == 2)
            $paying_method = 'Gift Card';
        elseif ($data['paid_by_id'] == 3)
            $paying_method = 'Credit Card';
        elseif ($data['paid_by_id'] == 5)
            $paying_method = 'Transferencia';
        elseif ($data['paid_by_id'] == 6)
            $paying_method = 'Descuento';
        else
            $paying_method = 'Cheque';

        $lims_payment_data = new Payment();
        $lims_payment_data->user_id = Auth::id();
        $lims_payment_data->purchase_id = $lims_purchase_data->id;
        $lims_payment_data->account_id = $data['account_id'];
        $lims_payment_data->payment_reference = 'ppr-' . date("Ymd") . '-'. date("his");
        $lims_payment_data->amount = $data['amount'];
        $lims_payment_data->change = $data['paying_amount'] - $data['amount'];
        $lims_payment_data->paying_method = $paying_method;
        $lims_payment_data->payment_note = $data['payment_note'];
        $lims_payment_data->save();

        $lims_payment_data = Payment::latest()->first();
        $data['payment_id'] = $lims_payment_data->id;

        if($paying_method == 'Credit Card'){
            $lims_pos_setting_data = PosSetting::latest()->first();
            Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
            $token = $data['stripeToken'];
            $amount = $data['amount'];

            // Charge the Customer
            $charge = \Stripe\Charge::create([
                'amount' => $amount * 100,
                'currency' => 'usd',
                'source' => $token,
            ]);

            $data['charge_id'] = $charge->id;
            PaymentWithCreditCard::create($data);
        }
        elseif ($paying_method == 'Cheque') {
            PaymentWithCheque::create($data);
        }
        return redirect('purchases')->with('message', 'Payment created successfully');
    }

    public function getPayment($id)
    {
        $lims_payment_list = Payment::where('purchase_id', $id)->get();
        $date = [];
        $payment_reference = [];
        $paid_amount = [];
        $paying_method = [];
        $payment_id = [];
        $payment_note = [];
        $cheque_no = [];
        $change = [];
        $paying_amount = [];
        $account_name = [];
        $account_id = [];
        foreach ($lims_payment_list as $payment) {
            $date[] = date(config('date_format'), strtotime($payment->created_at->toDateString())) . ' '. $payment->created_at->toTimeString();
            $payment_reference[] = $payment->payment_reference;
            $paid_amount[] = $payment->amount;
            $change[] = $payment->change;
            $paying_method[] = $payment->paying_method;
            $paying_amount[] = $payment->amount + $payment->change;
            if($payment->paying_method == 'Cheque'){
                $lims_payment_cheque_data = PaymentWithCheque::where('payment_id',$payment->id)->first();
                $cheque_no[] = $lims_payment_cheque_data->cheque_no;
            }
            else{
                $cheque_no[] = null;
            }
            $payment_id[] = $payment->id;
            $payment_note[] = $payment->payment_note;
            $lims_account_data = Account::find($payment->account_id);
            $account_name[] = $lims_account_data->name;
            $account_id[] = $lims_account_data->id;
        }
        $payments[] = $date;
        $payments[] = $payment_reference;
        $payments[] = $paid_amount;
        $payments[] = $paying_method;
        $payments[] = $payment_id;
        $payments[] = $payment_note;
        $payments[] = $cheque_no;
        $payments[] = $change;
        $payments[] = $paying_amount;
        $payments[] = $account_name;
        $payments[] = $account_id;

        return $payments;
    }

    public function updatePayment(Request $request)
    {
        $data = $request->all();
        $lims_payment_data = Payment::find($data['payment_id']);
        $lims_purchase_data = Purchase::find($lims_payment_data->purchase_id);
        //updating purchase table
        $amount_dif = $lims_payment_data->amount - $data['edit_amount'];
        $lims_purchase_data->paid_amount = $lims_purchase_data->paid_amount - $amount_dif;
        $balance = $lims_purchase_data->grand_total - $lims_purchase_data->paid_amount;
        if($balance > 0 || $balance < 0)
            $lims_purchase_data->payment_status = 1;
        elseif ($balance == 0)
            $lims_purchase_data->payment_status = 2;
        $lims_purchase_data->save();

        //updating payment data
        $lims_payment_data->account_id = $data['account_id'];
        $lims_payment_data->amount = $data['edit_amount'];
        $lims_payment_data->change = $data['edit_paying_amount'] - $data['edit_amount'];
        $lims_payment_data->payment_note = $data['edit_payment_note'];
        if($data['edit_paid_by_id'] == 1)
            $lims_payment_data->paying_method = 'Cash';
        elseif ($data['edit_paid_by_id'] == 2)
            $lims_payment_data->paying_method = 'Gift Card';
        elseif ($data['edit_paid_by_id'] == 3){
            $lims_pos_setting_data = PosSetting::latest()->first();
            \Stripe\Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
            $token = $data['stripeToken'];
            $amount = $data['edit_amount'];
            if($lims_payment_data->paying_method == 'Credit Card'){
                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $lims_payment_data->id)->first();

                \Stripe\Refund::create(array(
                  "charge" => $lims_payment_with_credit_card_data->charge_id,
                ));

                $charge = \Stripe\Charge::create([
                    'amount' => $amount * 100,
                    'currency' => 'usd',
                    'source' => $token,
                ]);

                $lims_payment_with_credit_card_data->charge_id = $charge->id;
                $lims_payment_with_credit_card_data->save();
            }
            else{
                // Charge the Customer
                $charge = \Stripe\Charge::create([
                    'amount' => $amount * 100,
                    'currency' => 'usd',
                    'source' => $token,
                ]);

                $data['charge_id'] = $charge->id;
                PaymentWithCreditCard::create($data);
            }
            $lims_payment_data->paying_method = 'Credit Card';
        } 
        elseif ($data['edit_paid_by_id'] == 5)
            $lims_payment_data->paying_method = 'Transferencia';        
        else{
            if($lims_payment_data->paying_method == 'Cheque'){
                $lims_payment_data->paying_method = 'Cheque';
                $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $data['payment_id'])->first();
                $lims_payment_cheque_data->cheque_no = $data['edit_cheque_no'];
                $lims_payment_cheque_data->save(); 
            }
            else{
                $lims_payment_data->paying_method = 'Cheque';
                $data['cheque_no'] = $data['edit_cheque_no'];
                PaymentWithCheque::create($data);
            }
        }
        $lims_payment_data->save();
        return redirect('purchases')->with('message', 'Payment updated successfully');
    }

    public function deletePayment(Request $request)
    {
        $lims_payment_data = Payment::find($request['id']);
        $lims_purchase_data = Purchase::where('id', $lims_payment_data->purchase_id)->first();
        $lims_purchase_data->paid_amount -= $lims_payment_data->amount;
        $balance = $lims_purchase_data->grand_total - $lims_purchase_data->paid_amount;
        if($balance > 0 || $balance < 0)
            $lims_purchase_data->payment_status = 1;
        elseif ($balance == 0)
            $lims_purchase_data->payment_status = 2;
        $lims_purchase_data->save();

        if($lims_payment_data->paying_method == 'Credit Card'){
            $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $request['id'])->first();
            $lims_pos_setting_data = PosSetting::latest()->first();
            \Stripe\Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
            \Stripe\Refund::create(array(
              "charge" => $lims_payment_with_credit_card_data->charge_id,
            ));

            $lims_payment_with_credit_card_data->delete();
        }
        elseif ($lims_payment_data->paying_method == 'Cheque') {
            $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $request['id'])->first();
            $lims_payment_cheque_data->delete();
        }
        $lims_payment_data->delete();
        return redirect('purchases')->with('not_permitted', 'Payment deleted successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $purchase_id = $request['purchaseIdArray'];
        foreach ($purchase_id as $id) {
            $lims_purchase_data = Purchase::find($id);
            $lims_product_purchase_data = ProductPurchase::where('purchase_id', $id)->get();
            $lims_payment_data = Payment::where('purchase_id', $id)->get();
            foreach ($lims_product_purchase_data as $product_purchase_data) {
                $lims_purchase_unit_data = Unit::find($product_purchase_data->purchase_unit_id);
                if ($lims_purchase_unit_data->operator == '*')
                    $recieved_qty = $product_purchase_data->recieved * $lims_purchase_unit_data->operation_value;
                else
                    $recieved_qty = $product_purchase_data->recieved / $lims_purchase_unit_data->operation_value;

                $lims_product_data = Product::find($product_purchase_data->product_id);
                if($product_purchase_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($lims_product_data->id, $product_purchase_data->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_purchase_data->product_id, $product_purchase_data->variant_id, $lims_purchase_data->warehouse_id)
                        ->first();
                    $lims_product_variant_data->qty -= $recieved_qty;
                    $lims_product_variant_data->save();
                }
                else {
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_purchase_data->product_id, $lims_purchase_data->warehouse_id)
                        ->first();
                }

                $lims_product_data->qty -= $recieved_qty;
                $lims_product_warehouse_data->qty -= $recieved_qty;
                
                $lims_product_warehouse_data->save();
                $lims_product_data->save();
                $product_purchase_data->delete();
            }
            foreach ($lims_payment_data as $payment_data) {
                if($payment_data->paying_method == "Cheque"){
                    $payment_with_cheque_data = PaymentWithCheque::where('payment_id', $payment_data->id)->first();
                    $payment_with_cheque_data->delete();
                }
                elseif($payment_data->paying_method == "Credit Card"){
                    $payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $payment_data->id)->first();
                    $lims_pos_setting_data = PosSetting::latest()->first();
                    \Stripe\Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
                    \Stripe\Refund::create(array(
                      "charge" => $payment_with_credit_card_data->charge_id,
                    ));

                    $payment_with_credit_card_data->delete();
                }
                $payment_data->delete();
            }

            $lims_purchase_data->delete();
        }
        return 'Purchase deleted successfully!';
    }

    public function destroy($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('purchases-delete')){
            $lims_purchase_data = Purchase::find($id);
            $lims_product_purchase_data = ProductPurchase::where('purchase_id', $id)->get();
            $lims_payment_data = Payment::where('purchase_id', $id)->get();
            foreach ($lims_product_purchase_data as $product_purchase_data) {
                $lims_purchase_unit_data = Unit::find($product_purchase_data->purchase_unit_id);
                if ($lims_purchase_unit_data->operator == '*')
                    $recieved_qty = $product_purchase_data->recieved * $lims_purchase_unit_data->operation_value;
                else
                    $recieved_qty = $product_purchase_data->recieved / $lims_purchase_unit_data->operation_value;

                $lims_product_data = Product::find($product_purchase_data->product_id);
                if($product_purchase_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($lims_product_data->id, $product_purchase_data->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_purchase_data->product_id, $product_purchase_data->variant_id, $lims_purchase_data->warehouse_id)
                        ->first();
                    $lims_product_variant_data->qty -= $recieved_qty;
                    $lims_product_variant_data->save();
                }
                else {
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_purchase_data->product_id, $lims_purchase_data->warehouse_id)
                        ->first();
                }
                
                $lims_product_data->qty -= $recieved_qty;
                $lims_product_warehouse_data->qty -= $recieved_qty;

                $lims_product_warehouse_data->save();
                $lims_product_data->save();
                $product_purchase_data->delete();
            }
            foreach ($lims_payment_data as $payment_data) {
                if($payment_data->paying_method == "Cheque"){
                    $payment_with_cheque_data = PaymentWithCheque::where('payment_id', $payment_data->id)->first();
                    $payment_with_cheque_data->delete();
                }
                elseif($payment_data->paying_method == "Credit Card"){
                    $payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $payment_data->id)->first();
                    $lims_pos_setting_data = PosSetting::latest()->first();
                    \Stripe\Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
                    \Stripe\Refund::create(array(
                      "charge" => $payment_with_credit_card_data->charge_id,
                    ));

                    $payment_with_credit_card_data->delete();
                }
                $payment_data->delete();
            }

            $lims_purchase_data->delete();
            return redirect('purchases')->with('not_permitted', 'Purchase deleted successfully');;
        }
        
    }

    public function info_purchase($id_purchase){

    $datos_compra  = DB::select('SELECT id, confirmation_date,dispatch_date ,estimated_delivery_date, deadline_delivery_date FROM purchases where id='.$id_purchase);

    return json_encode($datos_compra);
   }


  public function save_purchase_dates(){
   
   $id= $_POST["id_orden"];
    $lims_purchase_data = Purchase::find($id);
        

        $data["id"] = $id;
        $data["invoice"] = $_POST['invoice'];
        $data["confirmation_date"] = $_POST['fecha_confirmacion'];
        $data["dispatch_date"] = $_POST['fecha_despacho'];
        $data["estimated_delivery_date"] = $_POST['fecha_entrega'];
         $data["deadline_delivery_date"] = $_POST['deadline_delivery_date'];
        /*Este update estaba al final pero lo necesito antes para que la variable de estado se llene con el 
        valor del combo.
        */
        $lims_purchase_data->update($data);

  }

        public function costo_promedio($id,  $cantidad_entrante, $costo_entrante){
          
  
          /*cantidad actual y costo actual se va a calcular antes de assentar la modificacion en tabla products*/

             $datos = DB::select('
                SELECT 
                  qty, cost
                from products
                WHERE 
                id='.$id."
                
                ");

             
               $cantidad_actual = $datos[0]->qty;
               $costo_actual = $datos[0]->cost;
         
               if($cantidad_actual < 0){
             $kardex = DB::select('
                         SELECT stock FROM kardex WHERE stock >=0 
                            AND product_id='.$id.'
                            ORDER BY created_at DESC
                            LIMIT 1
                            
                            ');



                 $cantidad_actual = $kardex[0]->stock;

               }
               

$costo_promedio = (($cantidad_actual*$costo_actual)+($cantidad_entrante*$costo_entrante))/($cantidad_actual+$cantidad_entrante);

return  $costo_promedio; 

        

        }


 public function cantidad_actual($id){
          
  
          /*cantidad actual y costo actual se va a calcular antes de assentar la modificacion en tabla products*/

             $datos = DB::select('
                SELECT 
                  qty, cost
                from products
                WHERE 
                id='.$id."
                
                ");

        
               $cantidad_actual = $datos[0]->qty;
               $costo_actual = $datos[0]->cost;


         return  $cantidad_actual; 

        

        }

    public function download($id)
    {
       // Encuentra el registro en la tabla purchase
        $purchase = Purchase::findOrFail($id);

        // Obtiene el nombre del archivo desde el campo 'document'
        $fileName = $purchase->document;

        // Construye la ruta completa del archivo
        $filePath = public_path("documents/purchase/" . $fileName);
    
        if (empty($fileName)) {
        // Manejo de error
           echo "Error: La ruta del documento está vacía.";
        } else {
            return response()->download($filePath);
        }
        
        // Verifica si el archivo existe
        //if (!Purchase::exists($filePath)) {
        //    abort(404, 'Archivo no encontrado.');
        //}

        // Devuelve el archivo como respuesta para descargar
        //return response()->download($filePath);
       
    }

    public function showDocument($id)
    {
        // Encuentra la compra por ID
        $purchase = Purchase::findOrFail($id);

        // Obtén la ruta del archivo desde el campo 'document'
        $documentPath = $purchase->document;
        
        if (empty($documentPath)) {
        // Manejo de error
           echo "Error: La ruta del documento está vacía.";
        } else {
            return response()->file(public_path('documents/purchase/' . $documentPath));
        }
        //dd($documentPath);
        // Verifica si el archivo existe
        //if (!file_exists(public_path('documents/purchase/' . $documentPath))) {
        //    abort(404, 'Documento no encontrado');
        //}
        
        //return response()->file(public_path('documents/purchase/' . $documentPath));
        // Devuelve el archivo con encabezado para que se abra en una nueva pestaña
        //return response()->file($fullPath, [
        //    'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
        //]);
    }

}
