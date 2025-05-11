<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Customer;
use App\CustomerGroup;
use App\Warehouse;
use App\Biller;
use App\Brand;
use App\Category;
use App\Product;
use App\Unit;
use App\Tax;
use App\Sale;
use App\Delivery;
use App\PosSetting;
use App\Product_Sale;
use App\Product_Warehouse;
use App\Payment;
use App\Account;
use App\Coupon;
use App\GiftCard;
use App\PaymentWithCheque;
use App\PaymentWithGiftCard;
use App\PaymentWithCreditCard;
use App\PaymentWithPaypal;
use App\User;
use App\Variant;
use App\ProductVariant;
use App\CashRegister;
use App\Returns;
use App\Expense;
use App\ProductPurchase;
use App\Purchase;
use App\Types_document;
use DB;
use App\GeneralSetting;
use Stripe\Stripe;
use NumberToWords\NumberToWords;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\AdaptivePayments;
use GeniusTS\HijriDate\Date;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use Dompdf\Dompdf;
use Luecano\NumeroALetras\NumeroALetras;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use App\Exports\SalesExport;

use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;
class SaleController extends Controller
{
    public function index()
    {      
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-index')) {
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_sale_all = Sale::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_sale_all = Sale::orderBy('id', 'desc')->get();

            $lims_gift_card_list = GiftCard::where("is_active", true)->get();
            $lims_pos_setting_data = PosSetting::latest()->first();
            $lims_account_list = Account::where('is_active', true)->get();

            return view('sale.index',compact('lims_sale_all', 'lims_gift_card_list', 'lims_pos_setting_data', 'lims_account_list', 'all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function saleData(Request $request)
    {

        $columns = array( 
            1 => 'created_at', 
            2 => 'reference_no',
            3 => 'duca',
            7 => 'grand_total',
            8 => 'paid_amount',
        );
        
        
        if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            $totalData = Sale::where('user_id', Auth::id())->count();
        else
            $totalData = Sale::count();

        $totalFiltered = $totalData;
        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'sales.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value'))){
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $sales = Sale::with('biller', 'customer', 'warehouse', 'user')->offset($start)
                            ->where('user_id', Auth::id())
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            else
                $sales = Sale::with('biller', 'customer', 'warehouse', 'user')->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
        }
        else
        {
            $search = $request->input('search.value');
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own') {
                $sales =  Sale::select('sales.*')
                ->with('biller', 'customer', 'warehouse', 'user')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where(function($query) use ($search) {
                    $query->whereDate('sales.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                          ->orWhere('sales.reference_no', 'LIKE', "%{$search}%")
                          ->orWhere('sales.duca', 'LIKE', "%{$search}%")
                          ->orWhere('sales.serie', 'LIKE', "%{$search}%")
                          ->orWhere('customers.name', 'LIKE', "%{$search}%");
                })
                ->where('sales.user_id', Auth::id())
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();


                $totalFiltered = Sale::
                            join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->whereDate('sales.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                            ->where('sales.user_id', Auth::id())
                            ->orwhere([
                                ['sales.reference_no', 'LIKE', "%{$search}%"],
                                ['sales.user_id', Auth::id()]
                            ])
                            ->orwhere([
                                ['customers.name', 'LIKE', "%{$search}%"],
                                ['sales.user_id', Auth::id()]
                            ])
                            ->count();
            }
            else {
                $sales =  Sale::select('sales.*')
                ->with('biller', 'customer', 'warehouse', 'user')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where(function($query) use ($search) {
                    $query->whereDate('sales.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                          ->orWhere('sales.reference_no', 'LIKE', "%{$search}%")
                          ->orWhere('sales.duca', 'LIKE', "%{$search}%")
                          ->orWhere('sales.serie', 'LIKE', "%{$search}%")
                          ->orWhere('customers.name', 'LIKE', "%{$search}%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

    $totalFiltered = Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where(function($query) use ($search) {
                    $query->whereDate('sales.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                          ->orWhere('sales.reference_no', 'LIKE', "%{$search}%")
                          ->orWhere('sales.duca', 'LIKE', "%{$search}%")
                          ->orWhere('sales.serie', 'LIKE', "%{$search}%")
                          ->orWhere('customers.name', 'LIKE', "%{$search}%");
                })
                ->count();

            }
        }
        $data = array();
        if(!empty($sales))
        {
            foreach ($sales as $key=>$sale)
            {
                $nestedData['id'] = $sale->id;
                $nestedData['key'] = $key;
                $nestedData['date'] = date(config('date_format'), strtotime($sale->created_at->toDateString()));
                $nestedData['reference_no'] = $sale->reference_no;
                $nestedData['duca'] = $sale->duca;
                if ($sale->estadodte == "done")
                {
                    
                $nestedData['estadodte'] = "Procesado";
                }
                else if ($sale->estadodte == "inv")
                {
                    $nestedData['estadodte'] = "Invalidado";
                }
                else
                {

                    $nestedData['estadodte'] = "Rechazado";
                }  

                $nestedData['biller'] = $sale->biller->name;
                $nestedData['customer'] = $sale->customer->name;
                   $conteo1 = DB::select('  SELECT count(*) as total
                            FROM product_sales where sale_id='.$sale->id." and existence<0 and unit_cost=0");      
                                   

                 $conteo = $conteo1[0]->total; 

                 //$problema_costo = "<div class='badge badge-success'>0</div>"; 
                 //if($conteo >0){
                 //  $problema_costo= "<div class='badge badge-danger'>".$conteo."</div>";  
                 //}
                 
                if($sale->document_id == 1) {
                    $problema_costo = "<div class='badge badge-light'>".$sale->serie."</div>";  
                }
                else{
                    $problema_costo = "<div class='badge badge-light'>".$sale->serie."</div>";  
                }


                if($sale->sale_status == 1){
                    $nestedData['sale_status'] = '<div class="badge badge-success">'.trans('file.Completed').'</div>';
                    $sale_status = trans('file.Completed');
                }
                elseif($sale->sale_status == 2){
                    $nestedData['sale_status'] = '<div class="badge badge-danger">'.trans('file.Pending').'</div>';
                    $sale_status = trans('file.Pending');
                }
                else{
                    $nestedData['sale_status'] = '<div class="badge badge-warning">'.trans('file.Draft').'</div>';
                    $sale_status = trans('file.Draft');
                }

                if($sale->payment_status == 1)
                    $nestedData['payment_status'] = '<div class="badge badge-danger">'.trans('file.Pending').'</div>';
                elseif($sale->payment_status == 2)
                    $nestedData['payment_status'] = '<div class="badge badge-danger">'.trans('file.Due').'</div>';
                elseif($sale->payment_status == 3)
                    $nestedData['payment_status'] = '<div class="badge badge-warning">'.trans('file.Partial').'</div>';
                else
                    $nestedData['payment_status'] = '<div class="badge badge-success">'.trans('file.Paid').'</div>';

                $nestedData['grand_total'] = number_format($sale->grand_total, 2);
                $nestedData['paid_amount'] = number_format($sale->paid_amount, 2);
                $nestedData['due'] = number_format($sale->grand_total - $sale->paid_amount, 2);
                      if($sale->canceled==1){
                        $nestedData['canceled']="Anulada";
                    }else{
                        $nestedData['canceled']="";
                    }
                $nestedData['problema_costo'] = $problema_costo;

                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li><a href="'.route('sale.invoice', $sale->id).'" class="btn btn-link"><i class="fa fa-copy"></i> '.trans('file.Generate Invoice').'</a></li>
                           '; 

                        $nestedData['options'] .= '<li><a href="'.route('sale.invoice_ccf', $sale->id).'" class="btn btn-link"><i class="fa fa-copy"></i> Generate invoice ccf</a></li>
                                <li>';    

                        $nestedData['options'] .= '<li><a href="'.route('sale.invoice_export', $sale->id).'" class="btn btn-link"><i class="fa fa-copy"></i> Generate invoice Exp</a></li>
                                <li>';

                                $nestedData['options'] .= '<li><a href="'.route('sale.anulardte', $sale->id).'" class="btn btn-link"><i class="fa fa-copy"></i> Anular DTE</a></li>
                                <li>';

                                


                 // if(in_array("sales-edit", $request['all_permission'])){
                //     if($sale->sale_status != 3)
                //         $nestedData['options'] .= '<li>
                //             <a href="'.route('sales.edit', $sale->id).'" class="btn btn-link"><i class="dripicons-document-edit"></i> '.trans('file.edit').'</a>
                //             </li>';
                //     else
                //         $nestedData['options'] .= '<li>
                //             <a href="'.url('sales/'.$sale->id.'/create').'" class="btn btn-link"><i class="dripicons-document-edit"></i> '.trans('file.edit').'</a>
                //         </li>';
                // }
                $nestedData['options'] .= 
                    '<li>
                        <button type="button" class="add-payment btn btn-link" data-id = "'.$sale->id.'" data-toggle="modal" data-target="#add-payment"><i class="fa fa-plus"></i> '.trans('file.Add Payment').'</button>
                    </li>
                    <li>
                        <button type="button" class="get-payment btn btn-link" data-id = "'.$sale->id.'"><i class="fa fa-money"></i> '.trans('file.View Payment').'</button>
                    </li>
                    <li>
                        <button type="button" class="add-delivery btn btn-link" data-id = "'.$sale->id.'"><i class="fa fa-truck"></i> '.trans('file.Add Delivery').'</button>
                    </li>
                 <li>
                                <button type="button" class="btn btn-link" id="examinarDTEButton" onclick="verdte('.$sale->id.')"><i class="fa fa-eye"></i> DTE</button>

                            </li>
                            <li>
                            <button type="button" class="btn btn-link" id="examinarDTEButtonjson" onclick="dwdte('.$sale->id.')"><i class="fa fa-eye"></i> JSON</button>
                        </li>

                           
                    ';
                
                if(Auth::user()->role_id <= 1) {
                    $nestedData['options'] .= 
                        '<li>
                            <a href="'.route('sale.anular', $sale->id).'" class="btn btn-link">
                                <i class="fa fa-copy"></i> 
                                Anular
                            </a>
                        </li>';
                
                    //// aqui tendria que ir el codigo para la invalidación
                }
                
                // data for sale details by one click
                $coupon = Coupon::find($sale->coupon_id);
                if($coupon)
                    $coupon_code = $coupon->code;
                else
                    $coupon_code = null;

                $nestedData['sale'] = array( '[ "'.date(config('date_format'), strtotime($sale->created_at->toDateString())).'"', ' "'.$sale->reference_no.'"', ' "'.$sale_status.'"', ' "'.$sale->biller->name.'"', ' "'.$sale->biller->company_name.'"', ' "'.$sale->biller->email.'"', ' "'.$sale->biller->phone_number.'"', ' "'.$sale->biller->address.'"', ' "'.$sale->biller->city.'"', ' "'.$sale->customer->name.'"', ' "'.$sale->customer->phone_number.'"', ' "'.$sale->customer->address.'"', ' "'.$sale->customer->city.'"', ' "'.$sale->id.'"', ' "'.$sale->total_tax.'"', ' "'.$sale->total_discount.'"', ' "'.$sale->total_price.'"', ' "'.$sale->order_tax.'"', ' "'.$sale->order_tax_rate.'"', ' "'.$sale->order_discount.'"', ' "'.$sale->shipping_cost.'"', ' "'.$sale->grand_total.'"', ' "'.$sale->paid_amount.'"', ' "'.preg_replace('/\s+/S', " ", $sale->sale_note).'"', ' "'.preg_replace('/\s+/S', " ", $sale->staff_note).'"', ' "'.$sale->user->name.'"', ' "'.$sale->user->email.'"', ' "'.$sale->warehouse->name.'"', ' "'.$coupon_code.'"', ' "'.$sale->coupon_discount.'" , "'.$sale->canceled.'", "'.$problema_costo.'" ]'
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
            
        return response()->json($json_data);

    }
    public function salesByDate(Request $request)
    {
        // Obtener los parámetros de fecha (formato: YYYY-MM-DD)
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
    
        // Inicializar la variable de ventas (por defecto, colección vacía)
        $sales = collect();
        $totalTax = 0;
        $totalSubTotal = 0;
        $totalGrandTotal = 0;
    
        // Si se han enviado ambos valores, o al menos uno, filtra la consulta
        if ($startDate || $endDate) {
            // Construir la consulta base con las relaciones necesarias
            if (Auth::user()->role_id > 2 && config('staff_access') == 'own') {
                $query = Sale::with('biller', 'customer')->where('user_id', Auth::id());
            } else {
                $query = Sale::with('biller', 'customer');
            }
    
            // Aplicar el filtro según los valores ingresados
            if ($startDate && $endDate) {
                $query->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereRaw('DATE(created_at) >= ?', [$startDate]);
            } elseif ($endDate) {
                $query->whereRaw('DATE(created_at) <= ?', [$endDate]);
            }
    
            // Obtener las ventas ordenadas
            $sales = $query->orderBy('created_at', 'desc')->get();
    
            // Calcular los totales solo si hay ventas
            if ($sales->count() > 0) {
                $totalTax = $sales->sum('total_tax');
                $totalSubTotal = $sales->sum('total_price');
                $totalGrandTotal = $sales->sum('grand_total');
            }
        }
    
        // Retornar la vista con los datos filtrados y los totales
        return view('sale.date_range', compact('sales', 'startDate', 'endDate', 'totalTax', 'totalSubTotal', 'totalGrandTotal'));
    }
    
    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-add')){
            $lims_customer_list = Customer::where('is_active', true)->get();
            if(Auth::user()->role_id > 2) {
                $lims_warehouse_list = Warehouse::where([
                    ['is_active', true],
                    ['id', Auth::user()->warehouse_id]
                ])->get();
                $lims_biller_list = Biller::where([
                    ['is_active', true],
                    ['id', Auth::user()->biller_id]
                ])->get();
            }
            else {
                $lims_warehouse_list = Warehouse::where('is_active', true)->get();
                $lims_biller_list = Biller::where('is_active', true)->get();
            }
             $lims_documents_list = Types_document::where('modulo', 'POS')->get();

            


            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_pos_setting_data = PosSetting::latest()->first();

            return view('sale.create',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_pos_setting_data', 'lims_tax_list','lims_documents_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function get_info_pos($document_id){
     
     $lims_documents_list = Types_document::where('id', $document_id)->get();
     
    // echo json_encode($lims_documents_list);
     return json_encode($lims_documents_list);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $numerocontrolanexo = isset($data['numeroControlInput']) ? $data['numeroControlInput'] : 'na';

        $datoFactura = DB::select("SELECT IFNULL(MAX(correlativo),0) as correlativo FROM types_documents WHERE id=".$data['document_id']); 
        $correlativoNew = $datoFactura[0]->correlativo; 

        if(!isset($data['reference_no']))
            $data['reference_no'] = $correlativoNew;

        if(isset($request->reference_no)) {
            $this->validate($request, [
                'reference_no' => [
                    'max:191', 'required'/* tenia esta restriccion, se quito por que colocamos correlativos por tipo de documentos y en determinados escenarios puede llegar a suceder, 'unique:sales'*/
                ],
            ]);
        }
        //return dd($data);
        $data['user_id'] = Auth::id();
        $cash_register_data = CashRegister::where([
            ['user_id', $data['user_id']],
            ['warehouse_id', $data['warehouse_id']],
            ['status', true]
        ])->first();

        if($cash_register_data)
            $data['cash_register_id'] = $cash_register_data->id;

        if($data['pos']) {
            if(!isset($data['reference_no']))
                $data['reference_no'] = 'posr-' . date("Ymd") . '-'. date("his");

            $balance = $data['grand_total'] - $data['paid_amount'];
            if($balance > 0 || $balance < 0)
                $data['payment_status'] = 2;
            else
                $data['payment_status'] = 4;

            if($data['draft']) {
                $lims_sale_data = Sale::find($data['sale_id']);
                $lims_product_sale_data = Product_Sale::where('sale_id', $data['sale_id'])->get();
                foreach ($lims_product_sale_data as $product_sale_data) {
                    $product_sale_data->delete();
                }
                $lims_sale_data->delete();
            }
        }
        else {
            if(!isset($data['reference_no']))
                $data['reference_no'] = 'sr-' . date("Ymd") . '-'. date("his");
        }

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
            $document->move('public/sale/documents', $documentName);
            $data['document'] = $documentName;
        }
        if($data['coupon_active']) {
            $lims_coupon_data = Coupon::find($data['coupon_id']);
            $lims_coupon_data->used += 1;
            $lims_coupon_data->save();
        }
         
        

         if($data["licitacion"] =="on"){
              $data["licitacion"]="on";
         }else{
             $data["licitacion"]="off";
         }

        
         $lims_sale_data = Sale::create($data);
         $lims_data_sale = Types_document::find($data["document_id"]);
         /*Decidir si sera un incremento normal de correlativo o el usuario coloco un correlativo nuevo*/

        $nuevocorrelativo =$data['reference_no']+1;
        

         
         $data_actualiza["correlativo"] =$nuevocorrelativo; 

         Types_document::where([
                    ['id', $data["document_id"]]
                ])->update($data_actualiza);

        
        $lims_customer_data = Customer::find($data['customer_id']);
        //collecting male data
        $mail_data['email'] = $lims_customer_data->email;
        $mail_data['reference_no'] = $lims_sale_data->reference_no;
        $mail_data['sale_status'] = $lims_sale_data->sale_status;
        $mail_data['payment_status'] = $lims_sale_data->payment_status;
        $mail_data['total_qty'] = $lims_sale_data->total_qty;
        $mail_data['total_price'] = $lims_sale_data->total_price;
        $mail_data['order_tax'] = $lims_sale_data->order_tax;
        $mail_data['order_tax_rate'] = $lims_sale_data->order_tax_rate;
        $mail_data['order_discount'] = $lims_sale_data->order_discount;
        $mail_data['shipping_cost'] = $lims_sale_data->shipping_cost;
        $mail_data['grand_total'] = $lims_sale_data->grand_total;
        $mail_data['paid_amount'] = $lims_sale_data->paid_amount;

        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $qty = $data['qty'];
        $sale_unit = $data['sale_unit'];
        $net_unit_price = $data['net_unit_price'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $description = $data['description'];
        $tax = $data['tax'];
        $total = $data['subtotal'];
        $product_sale = [];

        $total_qty = 0;
        $total_price = 0;
        $total_tax = 0;
        $total_discount = 0;
        foreach ($product_id as $i => $id) {
            $lims_product_data = Product::where('id', $id)->first();
            $product_sale['variant_id'] = null;
            if($lims_product_data->type == 'combo' && $data['sale_status'] == 1){
                $product_list = explode(",", $lims_product_data->product_list);
                $qty_list = explode(",", $lims_product_data->qty_list);
                $price_list = explode(",", $lims_product_data->price_list);

                foreach ($product_list as $key=>$child_id) {
                    $child_data = Product::find($child_id);
                    $child_warehouse_data = Product_Warehouse::where([
                        ['product_id', $child_id],
                        ['warehouse_id', $data['warehouse_id'] ],
                        ])->first();

                    $child_data->qty -= $qty[$i] * $qty_list[$key];
                    $child_warehouse_data->qty -= $qty[$i] * $qty_list[$key];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }

            if($sale_unit[$i] != 'n/a') {
                $lims_sale_unit_data  = Unit::where('unit_name', $sale_unit[$i])->first();
                $sale_unit_id = $lims_sale_unit_data->id;
                if($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('id', 'variant_id', 'qty')->FindExactProductWithCode($id, $product_code[$i])->first();
                    $product_sale['variant_id'] = $lims_product_variant_data->variant_id;
                }  

                if($data['sale_status'] == 1){
                    if($lims_sale_unit_data->operator == '*')
                        $quantity = $qty[$i] * $lims_sale_unit_data->operation_value;
                    elseif($lims_sale_unit_data->operator == '/')
                        $quantity = $qty[$i] / $lims_sale_unit_data->operation_value;
                    //deduct quantity
                    $lims_product_data->qty = $lims_product_data->qty - $quantity;

                    $lims_product_data->save();
                    //deduct product variant quantity if exist
                    if($lims_product_data->is_variant) {
                        $lims_product_variant_data->qty -= $quantity;
                        $lims_product_variant_data->save();
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($id, $lims_product_variant_data->variant_id, $data['warehouse_id'])->first();
                    }
                    else {
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($id, $data['warehouse_id'])->first();
                    }
                    //deduct quantity from warehouse
                    $lims_product_warehouse_data->qty -= $quantity;
                    $lims_product_warehouse_data->save();
                }
            }
            else
                $sale_unit_id = 0;
            if($product_sale['variant_id']){
                $variant_data = Variant::select('name')->find($product_sale['variant_id']);
                $mail_data['products'][$i] = $lims_product_data->name . ' ['. $variant_data->name .']';
            }
            else
                $mail_data['products'][$i] = $lims_product_data->name;
            if($lims_product_data->type == 'digital')
                $mail_data['file'][$i] = url('/public/product/files').'/'.$lims_product_data->file;
            else
                $mail_data['file'][$i] = '';
            if($sale_unit_id)
                $mail_data['unit'][$i] = $lims_sale_unit_data->unit_code;
            else
                $mail_data['unit'][$i] = '';

            $product_sale['sale_id'] = $lims_sale_data->id ;
            $product_sale['product_id'] = $id;
            $product_sale['qty'] = $mail_data['qty'][$i] = $qty[$i];
            $product_sale['sale_unit_id'] = $sale_unit_id;
            $product_sale['net_unit_price'] = $net_unit_price[$i];
            $product_sale['discount'] = $discount[$i];
            $product_sale['tax_rate'] = $tax_rate[$i];
            $product_sale['description'] = isset($description[$i]) ? nl2br($description[$i]) : '';
            $product_sale['tax'] = $tax[$i];
            $product_sale['total'] = $mail_data['total'][$i] = $total[$i];
             $lims_product_data2 = Product::where('id', $id)->first();
            if($lims_product_data2->qty <=0){
             //$product_sale['unit_cost'] = 0;
                $product_sale['unit_cost'] = $lims_product_data->cost;
            }else{
                $product_sale['unit_cost'] = $lims_product_data->cost;

            }
            
            $product_sale['gain']      = ($net_unit_price[$i]-$lims_product_data->cost)*$qty[$i];
        /*    if($net_unit_price[$i]>0){
            $product_sale['gain_porc'] = number_format((($net_unit_price[$i]-$lims_product_data->cost)/$net_unit_price[$i])*100,2);    
            }else{
             $product_sale['gain_porc']=0;

            }*/

            if (isset($net_unit_price[$i]) && $net_unit_price[$i] > 0) {
                $product_sale['gain_porc'] = round((($net_unit_price[$i] - $lims_product_data->cost) / $net_unit_price[$i]) * 100, 2);
            } else {
                $product_sale['gain_porc'] = 0.00;  // Valor predeterminado si el precio unitario neto es cero o negativo
            }
            
           
            $product_sale['existence'] = $lims_product_data2->qty;  //Existencia 
           


            Product_Sale::create($product_sale);

                    // Acumular totales para solucionar el bug
        $total_qty += $qty[$i];
        $total_price += $net_unit_price[$i] * $qty[$i];
        $total_tax += $tax[$i];
        $total_discount += $discount[$i];
        }

        /*Invocacion al registro de kardex*/
        $data['total_qty'] = $total_qty;
        $data['total_price'] = $total_price;
        $data['order_tax'] = $total_tax;
        $data['order_discount'] = $total_discount;
        $data['grand_total'] = $total_price + $total_tax - $total_discount + $data['shipping_cost'];
    
        // Actualizar la venta con los totales recalculados
        $lims_sale_data->update($data);
    
        $this->recordKardex($lims_sale_data->id, 1,-1);



        if($data['sale_status'] == 3)
            $message = 'Sale successfully added to draft';
        else
            $message = ' Sale created successfully';
        if($mail_data['email'] && $data['sale_status'] == 1) {
            try {
                Mail::send( 'mail.sale_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Sale Details' );
                });
            }
            catch(\Exception $e){
                $message = ' Sale created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }

        if($data['payment_status'] == 3 || $data['payment_status'] == 4 || ($data['payment_status'] == 2 && $data['pos'] && $data['paid_amount'] > 0)) {

            if($data['paid_by_id'] == 1)
                $paying_method = 'Cash';
            elseif ($data['paid_by_id'] == 2){
                $paying_method = 'Gift Card';
            }
            elseif ($data['paid_by_id'] == 3)
                $paying_method = 'Credit Card';
            elseif ($data['paid_by_id'] == 4)
                $paying_method = 'Cheque';
            elseif ($data['paid_by_id'] == 5)
                $paying_method = 'Paypal';
            else
                $paying_method = 'Deposit';

            $lims_payment_data = new Payment();
            $lims_payment_data->user_id = Auth::id();
            if($cash_register_data)
                $lims_payment_data->cash_register_id = $cash_register_data->id;
            $lims_account_data = Account::where('is_default', true)->first();
            $lims_payment_data->account_id = $lims_account_data->id;
            $lims_payment_data->sale_id = $lims_sale_data->id;
            $data['payment_reference'] = 'spr-'.date("Ymd").'-'.date("his");
            $lims_payment_data->payment_reference = $data['payment_reference'];
            $lims_payment_data->amount = $data['paid_amount'];
            $lims_payment_data->change = $data['paying_amount'] - $data['paid_amount'];
            $lims_payment_data->paying_method = $paying_method;
            $lims_payment_data->payment_note = $data['payment_note'];
            $lims_payment_data->save();

            $lims_payment_data = Payment::latest()->first();
            $data['payment_id'] = $lims_payment_data->id;
            if($paying_method == 'Credit Card'){
                $lims_pos_setting_data = PosSetting::latest()->first();
                Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
                $token = $data['stripeToken'];
                $grand_total = $data['grand_total'];

                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('customer_id', $data['customer_id'])->first();

                if(!$lims_payment_with_credit_card_data) {
                    // Create a Customer:
                    $customer = \Stripe\Customer::create([
                        'source' => $token
                    ]);
                    
                    // Charge the Customer instead of the card:
                    $charge = \Stripe\Charge::create([
                        'amount' => $grand_total * 100,
                        'currency' => 'usd',
                        'customer' => $customer->id
                    ]);
                    $data['customer_stripe_id'] = $customer->id;
                }
                else {
                    $customer_id = 
                    $lims_payment_with_credit_card_data->customer_stripe_id;

                    $charge = \Stripe\Charge::create([
                        'amount' => $grand_total * 100,
                        'currency' => 'usd',
                        'customer' => $customer_id, // Previously stored, then retrieved
                    ]);
                    $data['customer_stripe_id'] = $customer_id;
                }
                $data['charge_id'] = $charge->id;
                PaymentWithCreditCard::create($data);
            }
            elseif ($paying_method == 'Gift Card') {
                $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
                $lims_gift_card_data->expense += $data['paid_amount'];
                $lims_gift_card_data->save();
                PaymentWithGiftCard::create($data);
            }
            elseif ($paying_method == 'Cheque') {
                PaymentWithCheque::create($data);
            }
            elseif ($paying_method == 'Paypal') {
                $provider = new ExpressCheckout;
                $paypal_data = [];
                $paypal_data['items'] = [];
                foreach ($data['product_id'] as $key => $product_id) {
                    $lims_product_data = Product::find($product_id);
                    $paypal_data['items'][] = [
                        'name' => $lims_product_data->name,
                        'price' => ($data['subtotal'][$key]/$data['qty'][$key]),
                        'qty' => $data['qty'][$key]
                    ];
                }
                $paypal_data['items'][] = [
                    'name' => 'Order Tax',
                    'price' => $data['order_tax'],
                    'qty' => 1
                ];
                $paypal_data['items'][] = [
                    'name' => 'Order Discount',
                    'price' => $data['order_discount'] * (-1),
                    'qty' => 1
                ];
                $paypal_data['items'][] = [
                    'name' => 'Shipping Cost',
                    'price' => $data['shipping_cost'],
                    'qty' => 1
                ];
                if($data['grand_total'] != $data['paid_amount']){
                    $paypal_data['items'][] = [
                        'name' => 'Due',
                        'price' => ($data['grand_total'] - $data['paid_amount']) * (-1),
                        'qty' => 1
                    ];
                }
                //return $paypal_data;
                $paypal_data['invoice_id'] = $lims_sale_data->reference_no;
                $paypal_data['invoice_description'] = "Reference # {$paypal_data['invoice_id']} Invoice";
                $paypal_data['return_url'] = url('/sale/paypalSuccess');
                $paypal_data['cancel_url'] = url('/sale/create');

                $total = 0;
                foreach($paypal_data['items'] as $item) {
                    $total += $item['price']*$item['qty'];
                }

                $paypal_data['total'] = $total;
                $response = $provider->setExpressCheckout($paypal_data);
                 // This will redirect user to PayPal
                return redirect($response['paypal_link']);
            }
            elseif($paying_method == 'Deposit'){
                $lims_customer_data = Customer::find($data['customer_id']);
                $lims_customer_data->expense += $data['paid_amount'];
                $lims_customer_data->save();
            }
        }
        //// tengo duda como se maneja el if 
       /* $result = $this->genInvoiceDTE($lims_sale_data->id, $numerocontrolanexo);
    
        if ($result == "done") {
            if ($lims_sale_data->sale_status == '1') {
                return redirect('sales/gen_invoice/' . $lims_sale_data->id)->with('message', $message);
            } elseif ($data['pos']) {
                return redirect('pos')->with('message', $message);
            } else {
                return redirect('sales')->with('message', $message);
            }
        } else {
            $errorMessage = $result;
            if ($lims_sale_data->sale_status == '1') {
                return redirect('sales/gen_invoice/' . $lims_sale_data->id)->with('error', $errorMessage);
            } elseif ($data['pos']) {
                return redirect('pos')->with('error', $errorMessage);
            } else {
                return redirect('sales')->with('error', $errorMessage);
            }
        }
  */
  if($lims_sale_data->sale_status == '1')



  if($this->genInvoiceDTE($lims_sale_data->id, $numerocontrolanexo)=="done")
  {
     $message = 'DTE generado correctamente';
     if($lims_sale_data->document_id == 1)
     {
         return redirect()->route('sale.invoice_ccf', ['id' => $lims_sale_data->id, 'controlnumeroanexo' => $numerocontrolanexo])->with('message', $message);
     }
     else
     {
         return redirect()->route('sale.invoice', ['id' => $lims_sale_data->id, 'controlnumeroanexo' => $numerocontrolanexo])->with('message', $message);
     }
  }
  else
  {
     $message = 'DTE NO generado correctamente';
     if($lims_sale_data->document_id == 1)
     {
         return redirect()->route('sale.invoice_ccf', ['id' => $lims_sale_data->id, 'controlnumeroanexo' => $numerocontrolanexo])->with('message', $message);
     }
     else
     {
         return redirect()->route('sale.invoice', ['id' => $lims_sale_data->id, 'controlnumeroanexo' => $numerocontrolanexo])->with('message', $message);
     }
  }
  elseif($data['pos'])
  {
     return redirect('pos')->with('message', $message);
  }
         else
             return redirect('sales')->with('message', $message);
     
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
             $sale_id = $idtran;

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

        $lims_warehouse_data = Warehouse::find($lims_purchase_data->warehouse_id);
        //dd($lims_supplier_data);
        if($lims_warehouse_data->count() >0){
            $name_traslado = $lims_warehouse_data->id;  
            $signo1 = $signo;
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
            $costo      =  $product_purchase_data->unit_cost;  
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
                      costo_unitario_promedio,                      
                      documento,
                      sale_id,
                      warehouse_id
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
                        ".$referencia.",
                        ".$sale_id.",
                        '".$name_traslado."'
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
        $lims_sale_data = Sale::find($data['sale_id']);
        $lims_product_sale_data = Product_Sale::where('sale_id', $data['sale_id'])->get();
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        if($lims_customer_data->email) {
            //collecting male data
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['reference_no'] = $lims_sale_data->reference_no;
            $mail_data['sale_status'] = $lims_sale_data->sale_status;
            $mail_data['payment_status'] = $lims_sale_data->payment_status;
            $mail_data['total_qty'] = $lims_sale_data->total_qty;
            $mail_data['total_price'] = $lims_sale_data->total_price;
            $mail_data['order_tax'] = $lims_sale_data->order_tax;
            $mail_data['order_tax_rate'] = $lims_sale_data->order_tax_rate;
            $mail_data['order_discount'] = $lims_sale_data->order_discount;
            $mail_data['shipping_cost'] = $lims_sale_data->shipping_cost;
            $mail_data['grand_total'] = $lims_sale_data->grand_total;
            $mail_data['paid_amount'] = $lims_sale_data->paid_amount;

            foreach ($lims_product_sale_data as $key => $product_sale_data) {
                $lims_product_data = Product::find($product_sale_data->product_id);
                if($product_sale_data->variant_id) {
                    $variant_data = Variant::select('name')->find($product_sale_data->variant_id);
                    $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name . ']';
                }
                else
                    $mail_data['products'][$key] = $lims_product_data->name;
                if($lims_product_data->type == 'digital')
                    $mail_data['file'][$key] = url('/public/product/files').'/'.$lims_product_data->file;
                else
                    $mail_data['file'][$key] = '';
                if($product_sale_data->sale_unit_id){
                    $lims_unit_data = Unit::find($product_sale_data->sale_unit_id);
                    $mail_data['unit'][$key] = $lims_unit_data->unit_code;
                }
                else
                    $mail_data['unit'][$key] = '';

                $mail_data['qty'][$key] = $product_sale_data->qty;
                $mail_data['total'][$key] = $product_sale_data->qty;
            }

            try{
                Mail::send( 'mail.sale_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Sale Details' );
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

    public function paypalSuccess(Request $request)
    {
        $lims_sale_data = Sale::latest()->first();
        $lims_payment_data = Payment::latest()->first();
        $lims_product_sale_data = Product_Sale::where('sale_id', $lims_sale_data->id)->get();
        $provider = new ExpressCheckout;
        $token = $request->token;
        $payerID = $request->PayerID;
        $paypal_data['items'] = [];
        foreach ($lims_product_sale_data as $key => $product_sale_data) {
            $lims_product_data = Product::find($product_sale_data->product_id);
            $paypal_data['items'][] = [
                'name' => $lims_product_data->name,
                'price' => ($product_sale_data->total/$product_sale_data->qty),
                'qty' => $product_sale_data->qty
            ];
        }
        $paypal_data['items'][] = [
            'name' => 'order tax',
            'price' => $lims_sale_data->order_tax,
            'qty' => 1
        ];
        $paypal_data['items'][] = [
            'name' => 'order discount',
            'price' => $lims_sale_data->order_discount * (-1),
            'qty' => 1
        ];
        $paypal_data['items'][] = [
            'name' => 'shipping cost',
            'price' => $lims_sale_data->shipping_cost,
            'qty' => 1
        ];
        if($lims_sale_data->grand_total != $lims_sale_data->paid_amount){
            $paypal_data['items'][] = [
                'name' => 'Due',
                'price' => ($lims_sale_data->grand_total - $lims_sale_data->paid_amount) * (-1),
                'qty' => 1
            ];
        }

        $paypal_data['invoice_id'] = $lims_payment_data->payment_reference;
        $paypal_data['invoice_description'] = "Reference: {$paypal_data['invoice_id']}";
        $paypal_data['return_url'] = url('/sale/paypalSuccess');
        $paypal_data['cancel_url'] = url('/sale/create');

        $total = 0;
        foreach($paypal_data['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }

        $paypal_data['total'] = $lims_sale_data->paid_amount;
        $response = $provider->getExpressCheckoutDetails($token);
        $response = $provider->doExpressCheckoutPayment($paypal_data, $token, $payerID);
        $data['payment_id'] = $lims_payment_data->id;
        $data['transaction_id'] = $response['PAYMENTINFO_0_TRANSACTIONID'];
        PaymentWithPaypal::create($data);
        return redirect('sales')->with('message', 'Sales created successfully');
    }

    public function paypalPaymentSuccess(Request $request, $id)
    {
        $lims_payment_data = Payment::find($id);
        $provider = new ExpressCheckout;
        $token = $request->token;
        $payerID = $request->PayerID;
        $paypal_data['items'] = [];
        $paypal_data['items'][] = [
            'name' => 'Paid Amount',
            'price' => $lims_payment_data->amount,
            'qty' => 1
        ];
        $paypal_data['invoice_id'] = $lims_payment_data->payment_reference;
        $paypal_data['invoice_description'] = "Reference: {$paypal_data['invoice_id']}";
        $paypal_data['return_url'] = url('/sale/paypalPaymentSuccess');
        $paypal_data['cancel_url'] = url('/sale');

        $total = 0;
        foreach($paypal_data['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }

        $paypal_data['total'] = $total;
        $response = $provider->getExpressCheckoutDetails($token);
        $response = $provider->doExpressCheckoutPayment($paypal_data, $token, $payerID);
        $data['payment_id'] = $lims_payment_data->id;
        $data['transaction_id'] = $response['PAYMENTINFO_0_TRANSACTIONID'];
        PaymentWithPaypal::create($data);
        return redirect('sales')->with('message', 'Payment created successfully');
    }

    public function getProduct($id)
    {

        /*estejoin requiere que los items tenga n un registro en product-warehouse, 
         hare que si no lo encuentra ingrese un registro con cero. 

        */
         

        $datos = DB::select('select * from products where id not in(select product_id from product_warehouse where warehouse_id='.$id.') ');
     

        foreach ($datos as $key ) {
                $product_warehouse= new Product_Warehouse;
             $product_warehouse->product_id= $key->id;
             $product_warehouse->variant_id= null;
             $product_warehouse->warehouse_id= $id;
             $product_warehouse->qty=0;
             $product_warehouse->price= $key->price;
              $product_warehouse->save();
            
        }


   
      
        $lims_product_warehouse_data = Product::leftJoin('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true]
     ,
            ['product_warehouse.warehouse_id', $id]
            ,
            ['product_warehouse.qty', '>', 0]
            
        ])->whereNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();

        $lims_product_with_variant_warehouse_data = Product::leftJoin('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id]
            /*,
            ['product_warehouse.qty', '>', 0]*/
        ])->whereNotNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();

       
        //product without variant
        foreach ($lims_product_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $product_price[] = $product_warehouse->price;
            $lims_product_data = Product::find($product_warehouse->product_id);
            $product_code[] =  $lims_product_data->code;
            $product_name[] = htmlspecialchars($lims_product_data->name);
            $product_type[] = $lims_product_data->type;
            $product_id[] = $lims_product_data->id;
            $product_list[] = $lims_product_data->product_list;
            $qty_list[] = $lims_product_data->qty_list;
        }
        //product with variant
       /*
        foreach ($lims_product_with_variant_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $lims_product_data = Product::find($product_warehouse->product_id);
            $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_warehouse->product_id, $product_warehouse->variant_id)->first();
            $product_code[] =  $lims_product_variant_data->item_code;
            $product_name[] = htmlspecialchars($lims_product_data->name);
            $product_type[] = $lims_product_data->type;
            $product_id[] = $lims_product_data->id;
            $product_list[] = $lims_product_data->product_list;
            $qty_list[] = $lims_product_data->qty_list;
        }
        */
        //retrieve product with type of digital and combo
        $lims_product_data = Product::whereNotIn('type', ['standard'])->where('is_active', true)->get();
        foreach ($lims_product_data as $product) 
        {
            $product_qty[] = $product->qty;
            $product_code[] =  $product->code;
            $product_name[] = $product->name;
            $product_type[] = $product->type;
            $product_id[] = $product->id;
            $product_list[] = $product->product_list;
            $qty_list[] = $product->qty_list;
        }
        $product_data = [$product_code, $product_name, $product_qty, $product_type, $product_id, $product_list, $qty_list, $product_price];
        return $product_data;
    }

    public function posSale()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-add')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';

            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_customer_group_all = CustomerGroup::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_product_list = Product::select('id', 'name', 'code', 'image')->ActiveFeatured()->whereNull('is_variant')->get();
            foreach ($lims_product_list as $key => $product) {
                $images = explode(",", $product->image);
                $product->base_image = $images[0];
            }
            $lims_product_list_with_variant = Product::select('id', 'name', 'code', 'image')->ActiveFeatured()->whereNotNull('is_variant')->get();

            foreach ($lims_product_list_with_variant as $product) {
                $images = explode(",", $product->image);
                $product->base_image = $images[0];
                $lims_product_variant_data = $product->variant()->orderBy('position')->get();
                $main_name = $product->name;
                $temp_arr = [];
                foreach ($lims_product_variant_data as $key => $variant) {
                    $product->name = $main_name.' ['.$variant->name.']';
                    $product->code = $variant->pivot['item_code'];
                    $lims_product_list[] = clone($product);
                }
            }
            
            $product_number = count($lims_product_list);
            $lims_pos_setting_data = PosSetting::latest()->first();
            $lims_brand_list = Brand::where('is_active',true)->get();
            $lims_category_list = Category::where('is_active',true)->get();
            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own') {
                $recent_sale = Sale::where([
                    ['sale_status', 1],
                    ['user_id', Auth::id()]
                ])->orderBy('id', 'desc')->take(10)->get();
                $recent_draft = Sale::where([
                    ['sale_status', 3],
                    ['user_id', Auth::id()]
                ])->orderBy('id', 'desc')->take(10)->get();
            }
            else {
                $recent_sale = Sale::where('sale_status', 1)->orderBy('id', 'desc')->take(10)->get();
                $recent_draft = Sale::where('sale_status', 3)->orderBy('id', 'desc')->take(10)->get();
            }
            $lims_coupon_list = Coupon::where('is_active',true)->get();
            $flag = 0;

            return view('sale.pos', compact('all_permission', 'lims_customer_list', 'lims_customer_group_all', 'lims_warehouse_list', 'lims_product_list', 'product_number', 'lims_tax_list', 'lims_biller_list', 'lims_pos_setting_data', 'lims_brand_list', 'lims_category_list', 'recent_sale', 'recent_draft', 'lims_coupon_list', 'flag'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function getProductByFilter($category_id, $brand_id)
    {
        $data = [];
        if(($category_id != 0) && ($brand_id != 0)){
            $lims_product_list = DB::table('products')
                                ->join('categories', 'products.category_id', '=', 'categories.id')
                                ->where([
                                    ['products.is_active', true],
                                    ['products.category_id', $category_id],
                                    ['brand_id', $brand_id]
                                ])->orWhere([
                                    ['categories.parent_id', $category_id],
                                    ['products.is_active', true],
                                    ['brand_id', $brand_id]
                                ])->select('products.name', 'products.code', 'products.image')->get();
        }
        elseif(($category_id != 0) && ($brand_id == 0)){
            $lims_product_list = DB::table('products')
                                ->join('categories', 'products.category_id', '=', 'categories.id')
                                ->where([
                                    ['products.is_active', true],
                                    ['products.category_id', $category_id],
                                ])->orWhere([
                                    ['categories.parent_id', $category_id],
                                    ['products.is_active', true]
                                ])->select('products.id', 'products.name', 'products.code', 'products.image', 'products.is_variant')->get();
        }
        elseif(($category_id == 0) && ($brand_id != 0)){
            $lims_product_list = Product::where([
                                ['brand_id', $brand_id],
                                ['is_active', true]
                            ])
                            ->select('products.id', 'products.name', 'products.code', 'products.image', 'products.is_variant')
                            ->get();
        }
        else
            $lims_product_list = Product::where('is_active', true)->get();

        $index = 0;
        foreach ($lims_product_list as $product) {
            if($product->is_variant) {
                $lims_product_data = Product::select('id')->find($product->id);
                $lims_product_variant_data = $lims_product_data->variant()->orderBy('position')->get();
                foreach ($lims_product_variant_data as $key => $variant) {
                    $data['name'][$index] = $product->name.' ['.$variant->name.']';
                    $data['code'][$index] = $variant->pivot['item_code'];
                    $images = explode(",", $product->image);
                    $data['image'][$index] = $images[0];
                    $index++;
                }
            }
            else {
                $data['name'][$index] = $product->name;
                $data['code'][$index] = $product->code;
                $images = explode(",", $product->image);
                $data['image'][$index] = $images[0];
                $index++;
            }
        }
        return $data;
    }

    public function getFeatured()
    {
        $data = [];
        $lims_product_list = Product::where([
            ['is_active', true],
            ['featured', true]
        ])->select('products.id', 'products.name', 'products.code', 'products.image', 'products.is_variant')->get();

        $index = 0;
        foreach ($lims_product_list as $product) {
            if($product->is_variant) {
                $lims_product_data = Product::select('id')->find($product->id);
                $lims_product_variant_data = $lims_product_data->variant()->orderBy('position')->get();
                foreach ($lims_product_variant_data as $key => $variant) {
                    $data['name'][$index] = $product->name.' ['.$variant->name.']';
                    $data['code'][$index] = $variant->pivot['item_code'];
                    $images = explode(",", $product->image);
                    $data['image'][$index] = $images[0];
                    $index++;
                }
            }
            else {
                $data['name'][$index] = $product->name;
                $data['code'][$index] = $product->code;
                $images = explode(",", $product->image);
                $data['image'][$index] = $images[0];
                $index++;
            }
        }
        return $data;
    }

    public function getCustomerGroup($id)
    {
         $lims_customer_data = Customer::find($id);
         $lims_customer_group_data = CustomerGroup::find($lims_customer_data->customer_group_id);
         return $lims_customer_group_data->percentage;
    }

    public function limsProductSearch(Request $request)
    {


        $todayDate = date('Y-m-d');
        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");
        $product_variant_id = null;
        $lims_product_data = Product::where('code', $product_code[0])->first();
 
        if(!$lims_product_data) {
            $lims_product_data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.*', 'product_variants.id as product_variant_id', 'product_variants.item_code', 'product_variants.additional_price')
                ->where('product_variants.item_code', $product_code[0])
                ->first();
            $product_variant_id = $lims_product_data->product_variant_id;
        }

        $product[] = $lims_product_data->name;
        if($lims_product_data->is_variant){
            $product[] = $lims_product_data->item_code;
            $lims_product_data->price += $lims_product_data->additional_price;
        }
        else
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

    public function getGiftCard()
    {
        $gift_card = GiftCard::where("is_active", true)->whereDate('expired_date', '>=', date("Y-m-d"))->get(['id', 'card_no', 'amount', 'expense']);
        return json_encode($gift_card);
    }

    public function productSaleData($id)
    {
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        foreach ($lims_product_sale_data as $key => $product_sale_data) {
            $product = Product::find($product_sale_data->product_id);
            if($product_sale_data->variant_id) {
                $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_sale_data->product_id, $product_sale_data->variant_id)->first();
                $product->code = $lims_product_variant_data->item_code;
            }
            $unit_data = Unit::find($product_sale_data->sale_unit_id);
            if($unit_data){
                $unit = $unit_data->unit_code;
            }
            else
                $unit = '';
            $product_sale[0][$key] = $product->name . ' [' . $product->code . ']';
            $product_sale[1][$key] = $product_sale_data->qty;
            $product_sale[2][$key] = $unit;
            $product_sale[3][$key] = $product_sale_data->tax;
            $product_sale[4][$key] = $product_sale_data->tax_rate;
            $product_sale[5][$key] = $product_sale_data->discount;
            $product_sale[6][$key] = $product_sale_data->total;
            $product_sale[7][$key] = $product_sale_data->existence;
        }
        return $product_sale;
    }

    public function saleByCsv()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-add')){
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();

            return view('sale.import',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function importSale(Request $request)
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
                    return redirect()->back()->with('message', 'Product does not exist!');
                $unit[] = Unit::where('unit_code', $current_line[2])->first();
                if(!$unit[$i-1] && $current_line[2] == 'n/a')
                    $unit[$i-1] = 'n/a';
                elseif(!$unit[$i-1]){
                    return redirect()->back()->with('message', 'Sale unit does not exist!');
                }
                if(strtolower($current_line[5]) != "no tax"){
                    $tax[] = Tax::where('name', $current_line[5])->first();
                    if(!$tax[$i-1])
                        return redirect()->back()->with('message', 'Tax name does not exist!');
                }
                else
                    $tax[$i-1]['rate'] = 0;

                $qty[] = $current_line[1];
                $price[] = $current_line[3];
                $discount[] = $current_line[4];
            }
            $i++;
        }
        //return $unit;
        $data = $request->except('document');
        $data['reference_no'] = 'sr-' . date("Ymd") . '-'. date("his");
        $data['user_id'] = Auth::user()->id;
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
            $document->move('public/documents/sale', $documentName);
            $data['document'] = $documentName;
        }
        $item = 0;
        $grand_total = $data['shipping_cost'];
        Sale::create($data);
        $lims_sale_data = Sale::latest()->first();
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        
        foreach ($product_data as $key => $product) {
            if($product['tax_method'] == 1){
                $net_unit_price = $price[$key] - $discount[$key];
                $product_tax = $net_unit_price * ($tax[$key]['rate'] / 100) * $qty[$key];
                $total = ($net_unit_price * $qty[$key]) + $product_tax;
            }
            elseif($product['tax_method'] == 2){
                $net_unit_price = (100 / (100 + $tax[$key]['rate'])) * ($price[$key] - $discount[$key]);
                $product_tax = ($price[$key] - $discount[$key] - $net_unit_price) * $qty[$key];
                $total = ($price[$key] - $discount[$key]) * $qty[$key];
            }
            if($data['sale_status'] == 1 && $unit[$key]!='n/a'){
                $sale_unit_id = $unit[$key]['id'];
                if($unit[$key]['operator'] == '*')
                    $quantity = $qty[$key] * $unit[$key]['operation_value'];
                elseif($unit[$key]['operator'] == '/')
                    $quantity = $qty[$key] / $unit[$key]['operation_value'];
                $product['qty'] -= $quantity;
                $product_warehouse = Product_Warehouse::where([
                    ['product_id', $product['id']],
                    ['warehouse_id', $data['warehouse_id']]
                ])->first();
                $product_warehouse->qty -= $quantity;
                $product->save();
                $product_warehouse->save();
            }
            else
                $sale_unit_id = 0;
            //collecting mail data
            $mail_data['products'][$key] = $product['name'];
            if($product['type'] == 'digital')
                $mail_data['file'][$key] = url('/public/product/files').'/'.$product['file'];
            else
                $mail_data['file'][$key] = '';
            if($sale_unit_id)
                $mail_data['unit'][$key] = $unit[$key]['unit_code'];
            else
                $mail_data['unit'][$key] = '';

            $product_sale = new Product_Sale();
            $product_sale->sale_id = $lims_sale_data->id;
            $product_sale->product_id = $product['id'];
            $product_sale->qty = $mail_data['qty'][$key] = $qty[$key];
            $product_sale->sale_unit_id = $sale_unit_id;
            $product_sale->net_unit_price = number_format((float)$net_unit_price, 2, '.', '');
            $product_sale->discount = $discount[$key] * $qty[$key];
            $product_sale->tax_rate = $tax[$key]['rate'];
            $product_sale->tax = number_format((float)$product_tax, 2, '.', '');
            $product_sale->total = $mail_data['total'][$key] = number_format((float)$total, 2, '.', '');
            $product_sale->save();
            $lims_sale_data->total_qty += $qty[$key];
            $lims_sale_data->total_discount += $discount[$key] * $qty[$key];
            $lims_sale_data->total_tax += number_format((float)$product_tax, 2, '.', '');
            $lims_sale_data->total_price += number_format((float)$total, 2, '.', '');
        }
        $lims_sale_data->item = $key + 1;
        $lims_sale_data->order_tax = ($lims_sale_data->total_price - $lims_sale_data->order_discount) * ($data['order_tax_rate'] / 100);
        $lims_sale_data->grand_total = ($lims_sale_data->total_price + $lims_sale_data->order_tax + $lims_sale_data->shipping_cost) - $lims_sale_data->order_discount;
        $lims_sale_data->save();
        $message = 'Sale imported successfully';
        if($lims_customer_data->email){
            //collecting male data
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['reference_no'] = $lims_sale_data->reference_no;
            $mail_data['sale_status'] = $lims_sale_data->sale_status;
            $mail_data['payment_status'] = $lims_sale_data->payment_status;
            $mail_data['total_qty'] = $lims_sale_data->total_qty;
            $mail_data['total_price'] = $lims_sale_data->total_price;
            $mail_data['order_tax'] = $lims_sale_data->order_tax;
            $mail_data['order_tax_rate'] = $lims_sale_data->order_tax_rate;
            $mail_data['order_discount'] = $lims_sale_data->order_discount;
            $mail_data['shipping_cost'] = $lims_sale_data->shipping_cost;
            $mail_data['grand_total'] = $lims_sale_data->grand_total;
            $mail_data['paid_amount'] = $lims_sale_data->paid_amount;
            if($mail_data['email']){
                try{
                    Mail::send( 'mail.sale_details', $mail_data, function( $message ) use ($mail_data)
                    {
                        $message->to( $mail_data['email'] )->subject( 'Sale Details' );
                    });
                }
                
                catch(\Exception $e){
                    $message = 'Sale imported successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
                }
            }
        }
        return redirect('sales')->with('message', $message);
    }

    public function createSale($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-edit')){
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_customer_group_all = CustomerGroup::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_sale_data = Sale::find($id);
            $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
            $lims_product_list = Product::where([
                                    ['featured', 1],
                                    ['is_active', true]
                                ])->get();
            foreach ($lims_product_list as $key => $product) {
                $images = explode(",", $product->image);
                $product->base_image = $images[0];
            }
            $product_number = count($lims_product_list);
            $lims_pos_setting_data = PosSetting::latest()->first();
            $lims_brand_list = Brand::where('is_active',true)->get();
            $lims_category_list = Category::where('is_active',true)->get();
            $lims_coupon_list = Coupon::where('is_active',true)->get();

            return view('sale.create_sale',compact('lims_biller_list', 'lims_customer_list', 'lims_warehouse_list', 'lims_tax_list', 'lims_sale_data','lims_product_sale_data', 'lims_pos_setting_data', 'lims_brand_list', 'lims_category_list', 'lims_coupon_list', 'lims_product_list', 'product_number', 'lims_customer_group_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-edit')){
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_sale_data = Sale::find($id);
            $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
            $lims_documents_list = Types_document::where('modulo', 'POS')->get();


            return view('sale.edit',compact('lims_documents_list','lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list', 'lims_sale_data','lims_product_sale_data'));
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
            $document->move('public/sale/documents', $documentName);
            $data['document'] = $documentName;
        }
        $balance = $data['grand_total'] - $data['paid_amount'];
        if($balance < 0 || $balance > 0)
            $data['payment_status'] = 2;
        else
            $data['payment_status'] = 4;
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $product_variant_id = $data['product_variant_id'];
        $qty = $data['qty'];
        $sale_unit = $data['sale_unit'];
        $net_unit_price = $data['net_unit_price'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $description = $data['description'];

        $tax = $data['tax'];
        $total = $data['subtotal'];
        $old_product_id = [];
        $product_sale = [];
        foreach ($lims_product_sale_data as  $key => $product_sale_data) {
            $old_product_id[] = $product_sale_data->product_id;
            $old_product_variant_id[] = null;
            $lims_product_data = Product::find($product_sale_data->product_id);

            if( ($lims_sale_data->sale_status == 1) && ($lims_product_data->type == 'combo') ) {
                $product_list = explode(",", $lims_product_data->product_list);
                $qty_list = explode(",", $lims_product_data->qty_list);

                foreach ($product_list as $index=>$child_id) {
                    $child_data = Product::find($child_id);
                    $child_warehouse_data = Product_Warehouse::where([
                        ['product_id', $child_id],
                        ['warehouse_id', $lims_sale_data->warehouse_id ],
                        ])->first();

                    $child_data->qty += $product_sale_data->qty * $qty_list[$index];
                    $child_warehouse_data->qty += $product_sale_data->qty * $qty_list[$index];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }
            elseif( ($lims_sale_data->sale_status == 1) && ($product_sale_data->sale_unit_id != 0)) {
                $old_product_qty = $product_sale_data->qty;
                $lims_sale_unit_data = Unit::find($product_sale_data->sale_unit_id);
                if ($lims_sale_unit_data->operator == '*')
                    $old_product_qty = $old_product_qty * $lims_sale_unit_data->operation_value;
                else
                    $old_product_qty = $old_product_qty / $lims_sale_unit_data->operation_value;
                if($product_sale_data->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($product_sale_data->product_id, $product_sale_data->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($product_sale_data->product_id, $product_sale_data->variant_id, $lims_sale_data->warehouse_id)
                    ->first();
                    $old_product_variant_id[$key] = $lims_product_variant_data->id;
                    $lims_product_variant_data->qty += $old_product_qty;
                    $lims_product_variant_data->save();
                }
                else
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($product_sale_data->product_id, $lims_sale_data->warehouse_id)
                    ->first();
                $lims_product_data->qty += $old_product_qty;
                $lims_product_warehouse_data->qty += $old_product_qty;
                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            if($product_sale_data->variant_id && !(in_array($old_product_variant_id[$key], $product_variant_id)) ){
                $product_sale_data->delete();
            }
            elseif( !(in_array($old_product_id[$key], $product_id)) )
                $product_sale_data->delete();
        }
        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::find($pro_id);
            $product_sale['variant_id'] = null;
            if($lims_product_data->type == 'combo' && $data['sale_status'] == 1){
                $product_list = explode(",", $lims_product_data->product_list);
                $qty_list = explode(",", $lims_product_data->qty_list);

                foreach ($product_list as $index=>$child_id) {
                    $child_data = Product::find($child_id);
                    $child_warehouse_data = Product_Warehouse::where([
                        ['product_id', $child_id],
                        ['warehouse_id', $data['warehouse_id'] ],
                        ])->first();

                    $child_data->qty -= $qty[$key] * $qty_list[$index];
                    $child_warehouse_data->qty -= $qty[$key] * $qty_list[$index];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }
            if($sale_unit[$key] != 'n/a') {
                $lims_sale_unit_data = Unit::where('unit_name', $sale_unit[$key])->first();
                $sale_unit_id = $lims_sale_unit_data->id;
                if($data['sale_status'] == 1) {
                    $new_product_qty = $qty[$key];
                    if ($lims_sale_unit_data->operator == '*') {
                        $new_product_qty = $new_product_qty * $lims_sale_unit_data->operation_value;
                    } else {
                        $new_product_qty = $new_product_qty / $lims_sale_unit_data->operation_value;
                    }
                    if($lims_product_data->is_variant) {
                        $lims_product_variant_data = ProductVariant::select('id', 'variant_id', 'qty')->FindExactProductWithCode($pro_id, $product_code[$key])->first();
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($pro_id, $lims_product_variant_data->variant_id, $data['warehouse_id'])
                        ->first();
                        
                        $product_sale['variant_id'] = $lims_product_variant_data->variant_id;
                        $lims_product_variant_data->qty -= $new_product_qty;
                        $lims_product_variant_data->save();
                    }
                    else {
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($pro_id, $data['warehouse_id'])
                        ->first();
                    }
                    $lims_product_data->qty -= $new_product_qty;
                    $lims_product_warehouse_data->qty -= $new_product_qty;
                    $lims_product_data->save();
                    $lims_product_warehouse_data->save();
                }
            }
            else
                $sale_unit_id = 0;
            
            //collecting mail data
            if($product_sale['variant_id']) {
                $variant_data = Variant::select('name')->find($product_sale['variant_id']);
                $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name . ']';
            }
            else
                $mail_data['products'][$key] = $lims_product_data->name;

            if($lims_product_data->type == 'digital')
                $mail_data['file'][$key] = url('/public/product/files').'/'.$lims_product_data->file;
            else
                $mail_data['file'][$key] = '';
            if($sale_unit_id)
                $mail_data['unit'][$key] = $lims_sale_unit_data->unit_code;
            else
                $mail_data['unit'][$key] = '';

            $product_sale['sale_id'] = $id ;
            $product_sale['product_id'] = $pro_id;
            $product_sale['qty'] = $mail_data['qty'][$key] = $qty[$key];
            $product_sale['sale_unit_id'] = $sale_unit_id;
            $product_sale['net_unit_price'] = $net_unit_price[$key];
            $product_sale['discount'] = $discount[$key];
            $product_sale['tax_rate'] = $tax_rate[$key];
             $product_sale['description'] = nl2br($description[$key]);
            $product_sale['tax'] = $tax[$key];
            $product_sale['total'] = $mail_data['total'][$key] = $total[$key];
            
            /*Colocar el costo del item nuevo. */
            $lims_product_data = Product::find($pro_id);
             $product_sale['unit_cost'] = $lims_product_data->cost;
             $product_sale['gain']      = ($net_unit_price[$key]-$lims_product_data->cost)*$qty[$key];
             $product_sale['gain_porc'] = number_format((($net_unit_price[$key]-$lims_product_data->cost)/$net_unit_price[$key])*100,2);


            
            if($product_sale['variant_id'] && in_array($product_variant_id[$key], $old_product_variant_id)) {
                Product_Sale::where([
                    ['product_id', $pro_id],
                    ['variant_id', $product_sale['variant_id']],
                    ['sale_id', $id]
                ])->update($product_sale);
            }
            elseif( $product_sale['variant_id'] === null && (in_array($pro_id, $old_product_id)) ) {
                Product_Sale::where([
                    ['sale_id', $id],
                    ['product_id', $pro_id]
                    ])->update($product_sale);
            }
            else
                Product_Sale::create($product_sale);
        }
        $lims_sale_data->update($data);
        $lims_customer_data = Customer::find($data['customer_id']);
        $message = 'Sale updated successfully';
        //collecting mail data
        if($lims_customer_data->email){
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['reference_no'] = $lims_sale_data->reference_no;
            $mail_data['sale_status'] = $lims_sale_data->sale_status;
            $mail_data['payment_status'] = $lims_sale_data->payment_status;
            $mail_data['total_qty'] = $lims_sale_data->total_qty;
            $mail_data['total_price'] = $lims_sale_data->total_price;
            $mail_data['order_tax'] = $lims_sale_data->order_tax;
            $mail_data['order_tax_rate'] = $lims_sale_data->order_tax_rate;
            $mail_data['order_discount'] = $lims_sale_data->order_discount;
            $mail_data['shipping_cost'] = $lims_sale_data->shipping_cost;
            $mail_data['grand_total'] = $lims_sale_data->grand_total;
            $mail_data['paid_amount'] = $lims_sale_data->paid_amount;
            if($mail_data['email']){
                try{
                    Mail::send( 'mail.sale_details', $mail_data, function( $message ) use ($mail_data)
                    {
                        $message->to( $mail_data['email'] )->subject( 'Sale Details' );
                    });
                }
                catch(\Exception $e){
                    $message = 'Sale updated successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
                }
            }
        }

        return redirect('sales')->with('message', $message);
    }

    public function printLastReciept()
    {
        $sale = Sale::where('sale_status', 1)->latest()->first();
        return redirect()->route('sale.invoice', $sale->id);
    }

    public function genInvoice($id)
    {
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $lims_payment_data = Payment::where('sale_id', $id)->get();

        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);

        return view('sale.invoice', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
    }

    public function genInvoice_pdf($id)
    {
        $userName = auth()->user()->name_purchase;
        $userNit = auth()->user()->email_purchase;
        $lims_sale_data = Sale::find($id);
            
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $lims_payment_data = Payment::where('sale_id', $id)->get();
            
        $uuid =  $lims_sale_data->codgeneracion;
        $fecha = $lims_sale_data->created_at;
        $fecEmi = substr($fecha, 0, 10);
        $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".strtoupper($uuid)."&fechaEmi=".$fecEmi;
        $aqrl = $sqr;
        $loco = "https://rcsinversiones.com/demo/generate_qr.php?texto=";
                
        $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>"; 
    
        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);
    
        $formatter = new NumeroALetras();
        $todo = $formatter->toInvoice($lims_sale_data->grand_total);
    
        //dd($todo);
        foreach($lims_product_sale_data as $qr){
            $dte = $qr->sale_id;
        }
    
        //dd($dte);
        //$codigoQr = QrCode::size(60)->generate($dte);
        $codigoQr=1;
        $valor = 1;
            
        // return QrCode::generate($aqrl);
        $sofia = QrCode::size(100)->generate($aqrl);
    
        // dd($todo);
        //$qrcodeL = base64_encode(QrCode::format('svg')->size(60)->errorCorrection('H')->generate('string'));
    
        $pdf= \PDF::loadView('sale.factdte',[
            "lims_sale_data"=>$lims_sale_data,
            "valor"=> $sofia,
            "lims_product_sale_data"=>$lims_product_sale_data,
            "lims_biller_data"=>$lims_biller_data,
            "lims_warehouse_data"=>$lims_warehouse_data,
            "lims_customer_data"=>$lims_customer_data,
            "lims_payment_data"=>$lims_payment_data,
            "numberToWords"=>$numberInWords,
            "todo"=>$todo,
            "userName"=>$userName,
            "userNit"=>$userNit,
            "aqrl"=>$aqrl ]);            
             
        $namearchivo = $lims_sale_data->codgeneracion . ".pdf";
        
        //return $pdf->setPaper('a4', 'portrait')->download('consumidor.pdf');
        return $pdf->setPaper('letter', 'portrait')->download($namearchivo);
     
        //return $pdf->setPaper('a4', 'portrait')->download('factura.pdf');
        //return $pdf;
    
        //return view('sale.invoice_ccf', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
    }


    public function genInvoice_pdfEmail($id)
    {
        $userName = auth()->user()->name_purchase;
        $userNit = auth()->user()->email_purchase;
        $lims_sale_data = Sale::find($id);
            
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $lims_payment_data = Payment::where('sale_id', $id)->get();
            
        $uuid =  $lims_sale_data->codgeneracion;
        $fecha = $lims_sale_data->created_at;
        $fecEmi = substr($fecha, 0, 10);
        $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".strtoupper($uuid)."&fechaEmi=".$fecEmi;
        $aqrl = $sqr;
        $loco = "https://rcsinversiones.com/demo/generate_qr.php?texto=";
                
        $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>"; 
    
        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);
    
        $formatter = new NumeroALetras();
        $todo = $formatter->toInvoice($lims_sale_data->grand_total);
    
        //dd($todo);
        foreach($lims_product_sale_data as $qr){
            $dte = $qr->sale_id;
        }
    
        //dd($dte);
        //$codigoQr = QrCode::size(60)->generate($dte);
        $codigoQr=1;
        $valor = 1;
            
        // return QrCode::generate($aqrl);
        $sofia = QrCode::size(100)->generate($aqrl);
    
        // dd($todo);
        //$qrcodeL = base64_encode(QrCode::format('svg')->size(60)->errorCorrection('H')->generate('string'));
    
        $documentId = $lims_sale_data->document_id;

        if ($documentId == 1) {
            $pdf = \PDF::loadView('sale.efact', [
                "lims_sale_data" => $lims_sale_data,
                "valor" => $sofia,
                "lims_product_sale_data" => $lims_product_sale_data,
                "lims_biller_data" => $lims_biller_data,
                "lims_warehouse_data" => $lims_warehouse_data,
                "lims_customer_data" => $lims_customer_data,
                "lims_payment_data" => $lims_payment_data,
                "numberToWords" => $numberInWords,
                "todo" => $todo,
                "userName" => $userName,
                "userNit" => $userNit,
                "aqrl" => $aqrl
            ]);
        } elseif ($documentId == 2) {
            $pdf = \PDF::loadView('sale.factdte', [
                "lims_sale_data" => $lims_sale_data,
                "valor" => $sofia,
                "lims_product_sale_data" => $lims_product_sale_data,
                "lims_biller_data" => $lims_biller_data,
                "lims_warehouse_data" => $lims_warehouse_data,
                "lims_customer_data" => $lims_customer_data,
                "lims_payment_data" => $lims_payment_data,
                "numberToWords" => $numberInWords,
                "todo" => $todo,
                "userName" => $userName,
                "userNit" => $userNit,
                "aqrl" => $aqrl
            ]);
        } else {
            // Maneja otros casos si es necesario
        }
         // Convertir el PDF a base64
 
         $pdfContent = $pdf->output();  // Genera el contenido binario del PDF
         $pdfBase64 = base64_encode($pdfContent);  // Codifica en Base64  // Genera el contenido binario del PDF
   
   return $pdfBase64;
    }

    public function genInvoice_ccf($id)
    {
        $userName = auth()->user()->name_purchase;
        $userNit = auth()->user()->nit;
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $lims_payment_data = Payment::where('sale_id', $id)->get();
        
        $uuid =  $lims_sale_data->codgeneracion;
        $fecha = $lims_sale_data->created_at;
        $fecEmi = substr($fecha, 0, 10);
        $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".strtoupper($uuid)."&fechaEmi=".$fecEmi;
        $aqrl = $sqr;
        $loco = "https://rcsinversiones.com/demo/generate_qr.php?texto=";
            
        $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>"; 

        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);

        $formatter = new NumeroALetras();
        $todo = $formatter->toInvoice($lims_sale_data->grand_total);

        foreach($lims_product_sale_data as $qr){
            $dte = $qr->sale_id;
        }

        $codigoQr=1;
        $valor = 1;
        
        $sofia = QrCode::size(100)->generate($aqrl);

        $pdf= \PDF::loadView('sale.efact',[
            "lims_sale_data"=>$lims_sale_data,
            "valor"=> $sofia,
            "lims_product_sale_data"=>$lims_product_sale_data,
            "lims_biller_data"=>$lims_biller_data,
            "lims_warehouse_data"=>$lims_warehouse_data,
            "lims_customer_data"=>$lims_customer_data,
            "lims_payment_data"=>$lims_payment_data,
            "numberToWords"=>$numberInWords,
            "todo"=>$todo,
            "userName"=>$userName,
            "userNit"=>$userNit,
            "aqrl"=>$aqrl ]);
   
        $namearchivo = $lims_sale_data->codgeneracion . ".pdf";
        
        //return $pdf->setPaper('a4', 'portrait')->download('consumidor.pdf');
        return $pdf->setPaper('letter', 'portrait')->download($namearchivo);

        //return view('sale.invoice_ccf', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
    }



    public function old_genInvoice_ccf($id)
    {
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $lims_payment_data = Payment::where('sale_id', $id)->get();

        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);

        return view('sale.invoice_ccf', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
    }

    public function genInvoice_export($id)
    {
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $lims_payment_data = Payment::where('sale_id', $id)->get();

        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);

        if($lims_customer_data->type_taxpayer_id == 3)
            $percepcion = number_format($lims_sale_data->grand_total*0.01,2);
        else
            $percepcion = 0;

        return view('sale.invoice_export', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords', 'percepcion'));
    }

    public function aplicar_costos($id)
    {
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where([
                                                      ['sale_id',$id],
                                                      ['existence','<',0]
                                                       ])->get();

        //print_r($lims_product_sale_data);

        /*Aqui tengo los items, debo ir a actualizar el maestro de productos*/
       foreach($lims_product_sale_data as $item) {
        //a cada fila iremos a aplicarle el costo actual si y solo si ya esta inflado el inventario 
         $lims_product_data = Product::where('id', $item["product_id"])->get();

         if($lims_product_data[0]["qty"] >0 && $lims_product_data[0]["qty"]>= $item->existence){
           //Significa que ya entro suficiente cantidad como para corregir costos


             $product_sale2["unit_cost"]=$lims_product_data[0]["cost"];

                     
               Product_Sale::where([
                    ['product_id', $item["product_id"] ],
                     ['sale_id', $id],
                     ['existence','<',0]
                      
                     
                    ])->update($product_sale2);

         }
       }
 return redirect('sales')->with('message', "Se aplicaron costos, si la factura sigue en rojo favor verificar que tenga suficiente existencia.");
           
    }


    public function anular($id)
    {
        $nrd = DB::delete('delete from kardex where sale_id='.$id);
        
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        
        /*Aqui tengo los items, debo ir a actualizar el maestro de productos*/
        if($lims_sale_data["canceled"] ==0){
            foreach($lims_product_sale_data as $item) {
                $lims_product_data = Product::where('id', $item["product_id"])->get();

                $product_sale["canceled_date"]=$lims_sale_data["created_at"];
                $product_sale["qty"] =$lims_product_data[0]["qty"]+$item["qty"];
                /*ubicaremos el item y actualizaremos la cantidad y la fecha, la formula seria la cantidad*/
               
                Product::where([
                    ['id', $item["product_id"]
                      
                    ]
                ])->update($product_sale);


               $product_sale2["canceled_date"]='1900-01-01 00:00:00';
                         
                Product::where([
                    ['id', $item["product_id"]
                      
                    ]
                ])->update($product_sale2);
                
                $child_warehouse_data = Product_Warehouse::where([
                    ['product_id', $item["product_id"]],
                    ['warehouse_id', $lims_sale_data->warehouse_id],
                ])->get();

                //dd($child_warehouse_data);
                $product_warehouse_sale["qty"] =$child_warehouse_data[0]["qty"]+$item["qty"];

                //dd($product_warehouse_sale);
                Product_Warehouse::where([
                    ['product_id', $item["product_id"]],
                    ['warehouse_id', $lims_sale_data->warehouse_id],                     
                ])->update($product_warehouse_sale);
                
                $this->recalculoKardex($item["product_id"]); 
            }

            /*Actualizar el estado de la factura, canceled =1*/
            /*para eviitar que facturas anuladas esten haciendo ruido en reportes de venta
            la solucion mas optima sera actualizar los campos de valores a cero. 
            */
            $sale_update["canceled"]=1;
            $sale_update["total_qty"]=0;
            $sale_update["total_discount"]=0;
            $sale_update["total_tax"]=0;
            $sale_update["total_price"]=0;
            $sale_update["grand_total"]=0;
            $sale_update["paid_amount"]=0;
        
            Sale::where([['id', $id]
                ])->update($sale_update);

            /*Tambien debemos setear a cero valores del detalle*/

            $sale_update_detail["qty"]=0;
            $sale_update_detail["tax"]=0;
            $sale_update_detail["total"]=0;
            $sale_update_detail["discount"]=0;
            $sale_update_detail["net_unit_price"]=0;
            $sale_update_detail["unit_cost"]=0;
            $sale_update_detail["gain"]=0;
            $sale_update_detail["gain_porc"]=0;
        
            Product_Sale::where([['sale_id', $id]
                ])->update($sale_update_detail);

            $payment_update["amount"]=0;

            Payment::where([['sale_id', $id]
                ])->update($payment_update);

            /*Redirect a ventas*/
   
            return redirect('sales')->with('message', "Factura anulada exitosamente.");

        }else{

            echo "La factura ya fue anulada";
        }
    }

public function recalculoKardex($idproduct){
   

  // echo date("Y-m-d h:i:s");

    $datos = DB::select('
       select * from kardex
       where product_id='.$idproduct."
       order by created_at asc
       ");
     $saldo=0;
     $correlativo=1; 
    foreach ($datos as $key ) {
     $correlativo_actual = $key->correlativo; 
     $fecha_movimiento = $key->created_at; 
     $qty         = $key->qty;
     if($correlativo == 1){
      $saldo = $key->stock; 
     }else{
      $saldo+=$qty; 
     }
     

     if($correlativo_actual > 1){
     /*actualizar la columna stock*/
     DB::update('update kardex set correlativo= ? , stock = ? where product_id = ? and correlativo=? and created_at=?', [$correlativo , $saldo , $idproduct, $correlativo_actual, $fecha_movimiento]);
    }
    $correlativo=$correlativo+1; 
    }
/*
echo "<pre>";
    print_r($datos);

echo "</pre>";
*/

  }


   


    public function addPayment(Request $request)
    {
        $data = $request->all();
        if(!$data['amount'])
            $data['amount'] = 0.00;
        
        $lims_sale_data = Sale::find($data['sale_id']);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        $lims_sale_data->paid_amount += $data['amount'];
        $balance = $lims_sale_data->grand_total - $lims_sale_data->paid_amount;
        if($balance > 0 || $balance < 0)
            $lims_sale_data->payment_status = 2;
        elseif ($balance == 0)
            $lims_sale_data->payment_status = 4;
        
        if($data['paid_by_id'] == 1)
            $paying_method = 'Cash';
        elseif ($data['paid_by_id'] == 2)
            $paying_method = 'Gift Card';
        elseif ($data['paid_by_id'] == 3)
            $paying_method = 'Credit Card';
        elseif($data['paid_by_id'] == 4)
            $paying_method = 'Cheque';
        elseif($data['paid_by_id'] == 5)
            $paying_method = 'Paypal';
        elseif($data['paid_by_id'] == 6)
            $paying_method = 'Deposit';
        else
            $paying_method = 'Cobro';


        $cash_register_data = CashRegister::where([
            ['user_id', Auth::id()],
            ['warehouse_id', $lims_sale_data->warehouse_id],
            ['status', true]
        ])->first();

        $lims_payment_data = new Payment();
        $lims_payment_data->user_id = Auth::id();
        $lims_payment_data->sale_id = $lims_sale_data->id;
        if($cash_register_data)
            $lims_payment_data->cash_register_id = $cash_register_data->id;
        $lims_payment_data->account_id = $data['account_id'];
        $data['payment_reference'] = 'spr-' . date("Ymd") . '-'. date("his");
        $lims_payment_data->payment_reference = $data['payment_reference'];
        $lims_payment_data->amount = $data['amount'];
        $lims_payment_data->change = $data['paying_amount'] - $data['amount'];
        $lims_payment_data->paying_method = $paying_method;
        $lims_payment_data->payment_note = $data['payment_note'];
        $lims_payment_data->save();
        $lims_sale_data->save();

        $lims_payment_data = Payment::latest()->first();
        $data['payment_id'] = $lims_payment_data->id;

        if($paying_method == 'Gift Card'){
            $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
            $lims_gift_card_data->expense += $data['amount'];
            $lims_gift_card_data->save();
            PaymentWithGiftCard::create($data);
        }
        elseif($paying_method == 'Credit Card'){
            $lims_pos_setting_data = PosSetting::latest()->first();
            Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
            $token = $data['stripeToken'];
            $amount = $data['amount'];

            $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('customer_id', $lims_sale_data->customer_id)->first();

            if(!$lims_payment_with_credit_card_data) {
                // Create a Customer:
                $customer = \Stripe\Customer::create([
                    'source' => $token
                ]);
                
                // Charge the Customer instead of the card:
                $charge = \Stripe\Charge::create([
                    'amount' => $amount * 100,
                    'currency' => 'usd',
                    'customer' => $customer->id,
                ]);
                $data['customer_stripe_id'] = $customer->id;
            }
            else {
                $customer_id = 
                $lims_payment_with_credit_card_data->customer_stripe_id;

                $charge = \Stripe\Charge::create([
                    'amount' => $amount * 100,
                    'currency' => 'usd',
                    'customer' => $customer_id, // Previously stored, then retrieved
                ]);
                $data['customer_stripe_id'] = $customer_id;
            }
            $data['customer_id'] = $lims_sale_data->customer_id;
            $data['charge_id'] = $charge->id;
            PaymentWithCreditCard::create($data);
        }
        elseif ($paying_method == 'Cheque') {
            PaymentWithCheque::create($data);
        }
        elseif ($paying_method == 'Paypal') {
            $provider = new ExpressCheckout;
            $paypal_data['items'] = [];
            $paypal_data['items'][] = [
                'name' => 'Paid Amount',
                'price' => $data['amount'],
                'qty' => 1
            ];
            $paypal_data['invoice_id'] = $lims_payment_data->payment_reference;
            $paypal_data['invoice_description'] = "Reference: {$paypal_data['invoice_id']}";
            $paypal_data['return_url'] = url('/sale/paypalPaymentSuccess/'.$lims_payment_data->id);
            $paypal_data['cancel_url'] = url('/sale');

            $total = 0;
            foreach($paypal_data['items'] as $item) {
                $total += $item['price']*$item['qty'];
            }

            $paypal_data['total'] = $total;
            $response = $provider->setExpressCheckout($paypal_data);
            return redirect($response['paypal_link']);
        }
       elseif ($paying_method == 'Deposit') {
            $lims_customer_data->expense += $data['amount'];
        }
        elseif ($paying_method == 'Cobro') {
            $lims_customer_data->expense += $data['amount'];
            $lims_customer_data->save();
        }
        $message = 'Payment created successfully';
        if($lims_customer_data->email){
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['sale_reference'] = $lims_sale_data->reference_no;
            $mail_data['payment_reference'] = $lims_payment_data->payment_reference;
            $mail_data['payment_method'] = $lims_payment_data->paying_method;
            $mail_data['grand_total'] = $lims_sale_data->grand_total;
            $mail_data['paid_amount'] = $lims_payment_data->amount;
            try{
                Mail::send( 'mail.payment_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Payment Details' );
                });
            }
            catch(\Exception $e){
                $message = 'Payment created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
            
        }
        return redirect('sales')->with('message', $message);
    }

    public function getPayment($id)
    {
        $lims_payment_list = Payment::where('sale_id', $id)->get();
        $date = [];
        $payment_reference = [];
        $paid_amount = [];
        $paying_method = [];
        $payment_id = [];
        $payment_note = [];
        $gift_card_id = [];
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
            if($payment->paying_method == 'Gift Card'){
                $lims_payment_gift_card_data = PaymentWithGiftCard::where('payment_id',$payment->id)->first();
                $gift_card_id[] = $lims_payment_gift_card_data->gift_card_id;
            }
            elseif($payment->paying_method == 'Cheque'){
                $lims_payment_cheque_data = PaymentWithCheque::where('payment_id',$payment->id)->first();
                $cheque_no[] = $lims_payment_cheque_data->cheque_no;
            }
            else{
                $cheque_no[] = $gift_card_id[] = null;
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
        $payments[] = $gift_card_id;
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
        $lims_sale_data = Sale::find($lims_payment_data->sale_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);
        //updating sale table
        $amount_dif = $lims_payment_data->amount - $data['edit_amount'];
        $lims_sale_data->paid_amount = $lims_sale_data->paid_amount - $amount_dif;
        $balance = $lims_sale_data->grand_total - $lims_sale_data->paid_amount;
        if($balance > 0 || $balance < 0)
            $lims_sale_data->payment_status = 2;
        elseif ($balance == 0)
            $lims_sale_data->payment_status = 4;
        $lims_sale_data->save();

        if($lims_payment_data->paying_method == 'Deposit'){
            $lims_customer_data->expense -= $lims_payment_data->amount;
            $lims_customer_data->save();
        }
        if($data['edit_paid_by_id'] == 1)
            $lims_payment_data->paying_method = 'Cash';
        elseif ($data['edit_paid_by_id'] == 2){
            if($lims_payment_data->paying_method == 'Gift Card'){
                $lims_payment_gift_card_data = PaymentWithGiftCard::where('payment_id', $data['payment_id'])->first();

                $lims_gift_card_data = GiftCard::find($lims_payment_gift_card_data->gift_card_id);
                $lims_gift_card_data->expense -= $lims_payment_data->amount;
                $lims_gift_card_data->save();

                $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
                $lims_gift_card_data->expense += $data['edit_amount'];
                $lims_gift_card_data->save();

                $lims_payment_gift_card_data->gift_card_id = $data['gift_card_id'];
                $lims_payment_gift_card_data->save(); 
            }
            else{
                $lims_payment_data->paying_method = 'Gift Card';
                $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
                $lims_gift_card_data->expense += $data['edit_amount'];
                $lims_gift_card_data->save();
                PaymentWithGiftCard::create($data);
            }
        }
        elseif ($data['edit_paid_by_id'] == 3){
            $lims_pos_setting_data = PosSetting::latest()->first();
            Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
            if($lims_payment_data->paying_method == 'Credit Card'){
                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $lims_payment_data->id)->first();

                \Stripe\Refund::create(array(
                  "charge" => $lims_payment_with_credit_card_data->charge_id,
                ));

                $customer_id = 
                $lims_payment_with_credit_card_data->customer_stripe_id;

                $charge = \Stripe\Charge::create([
                    'amount' => $data['edit_amount'] * 100,
                    'currency' => 'usd',
                    'customer' => $customer_id
                ]);
                $lims_payment_with_credit_card_data->charge_id = $charge->id;
                $lims_payment_with_credit_card_data->save();
            }
            else{
                $token = $data['stripeToken'];
                $amount = $data['edit_amount'];
                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('customer_id', $lims_sale_data->customer_id)->first();

                if(!$lims_payment_with_credit_card_data) {
                    $customer = \Stripe\Customer::create([
                        'source' => $token
                    ]);

                    $charge = \Stripe\Charge::create([
                        'amount' => $amount * 100,
                        'currency' => 'usd',
                        'customer' => $customer->id,
                    ]);
                    $data['customer_stripe_id'] = $customer->id;
                }
                else {
                    $customer_id = 
                    $lims_payment_with_credit_card_data->customer_stripe_id;

                    $charge = \Stripe\Charge::create([
                        'amount' => $amount * 100,
                        'currency' => 'usd',
                        'customer' => $customer_id
                    ]);
                    $data['customer_stripe_id'] = $customer_id;
                }
                $data['customer_id'] = $lims_sale_data->customer_id;
                $data['charge_id'] = $charge->id;
                PaymentWithCreditCard::create($data);
            }
            $lims_payment_data->paying_method = 'Credit Card';
        }
        elseif($data['edit_paid_by_id'] == 4){
            if($lims_payment_data->paying_method == 'Cheque'){
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
        elseif($data['edit_paid_by_id'] == 5){
            //updating payment data
            $lims_payment_data->amount = $data['edit_amount'];
            $lims_payment_data->paying_method = 'Paypal';
            $lims_payment_data->payment_note = $data['edit_payment_note'];
            $lims_payment_data->save();

            $provider = new ExpressCheckout;
            $paypal_data['items'] = [];
            $paypal_data['items'][] = [
                'name' => 'Paid Amount',
                'price' => $data['edit_amount'],
                'qty' => 1
            ];
            $paypal_data['invoice_id'] = $lims_payment_data->payment_reference;
            $paypal_data['invoice_description'] = "Reference: {$paypal_data['invoice_id']}";
            $paypal_data['return_url'] = url('/sale/paypalPaymentSuccess/'.$lims_payment_data->id);
            $paypal_data['cancel_url'] = url('/sale');

            $total = 0;
            foreach($paypal_data['items'] as $item) {
                $total += $item['price']*$item['qty'];
            }

            $paypal_data['total'] = $total;
            $response = $provider->setExpressCheckout($paypal_data);
            return redirect($response['paypal_link']);
        }   
        else{
            $lims_payment_data->paying_method = 'Deposit';
            $lims_customer_data->expense += $data['edit_amount'];
            $lims_customer_data->save();
        }
        //updating payment data
        $lims_payment_data->account_id = $data['account_id'];
        $lims_payment_data->amount = $data['edit_amount'];
        $lims_payment_data->change = $data['edit_paying_amount'] - $data['edit_amount'];
        $lims_payment_data->payment_note = $data['edit_payment_note'];
        $lims_payment_data->save();
        $message = 'Payment updated successfully';
        //collecting male data
        if($lims_customer_data->email){
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['sale_reference'] = $lims_sale_data->reference_no;
            $mail_data['payment_reference'] = $lims_payment_data->payment_reference;
            $mail_data['payment_method'] = $lims_payment_data->paying_method;
            $mail_data['grand_total'] = $lims_sale_data->grand_total;
            $mail_data['paid_amount'] = $lims_payment_data->amount;
            try{
                Mail::send( 'mail.payment_details', $mail_data, function( $message ) use ($mail_data)
                {
                    $message->to( $mail_data['email'] )->subject( 'Payment Details' );
                });
            }
            catch(\Exception $e){
                $message = 'Payment updated successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        return redirect('sales')->with('message', $message);
    }

    public function deletePayment(Request $request)
    {
        $lims_payment_data = Payment::find($request['id']);
        $lims_sale_data = Sale::where('id', $lims_payment_data->sale_id)->first();
        $lims_sale_data->paid_amount -= $lims_payment_data->amount;
        $balance = $lims_sale_data->grand_total - $lims_sale_data->paid_amount;
        if($balance > 0 || $balance < 0)
            $lims_sale_data->payment_status = 2;
        elseif ($balance == 0)
            $lims_sale_data->payment_status = 4;
        $lims_sale_data->save();

        if ($lims_payment_data->paying_method == 'Gift Card') {
            $lims_payment_gift_card_data = PaymentWithGiftCard::where('payment_id', $request['id'])->first();
            $lims_gift_card_data = GiftCard::find($lims_payment_gift_card_data->gift_card_id);
            $lims_gift_card_data->expense -= $lims_payment_data->amount;
            $lims_gift_card_data->save();
            $lims_payment_gift_card_data->delete();
        }
        elseif($lims_payment_data->paying_method == 'Credit Card'){
            $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $request['id'])->first();
            $lims_pos_setting_data = PosSetting::latest()->first();
            Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
            \Stripe\Refund::create(array(
              "charge" => $lims_payment_with_credit_card_data->charge_id,
            ));

            $lims_payment_with_credit_card_data->delete();
        }
        elseif ($lims_payment_data->paying_method == 'Cheque') {
            $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $request['id'])->first();
            $lims_payment_cheque_data->delete();
        }
        elseif ($lims_payment_data->paying_method == 'Paypal') {
            $lims_payment_paypal_data = PaymentWithPaypal::where('payment_id', $request['id'])->first();
            if($lims_payment_paypal_data){
                $provider = new ExpressCheckout;
                $response = $provider->refundTransaction($lims_payment_paypal_data->transaction_id);
                $lims_payment_paypal_data->delete();
            }
        }
        elseif ($lims_payment_data->paying_method == 'Deposit'){
            $lims_customer_data = Customer::find($lims_sale_data->customer_id);
            $lims_customer_data->expense -= $lims_payment_data->amount;
            $lims_customer_data->save();
        }
        $lims_payment_data->delete();
        return redirect('sales')->with('not_permitted', 'Payment deleted successfully');
    }

    public function todaySale()
    {
        $data['total_sale_amount'] = Sale::whereDate('created_at', date("Y-m-d"))->sum('grand_total');
        $data['total_payment'] = Payment::whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['cash_payment'] = Payment::where([
                                    ['paying_method', 'Cash']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['credit_card_payment'] = Payment::where([
                                    ['paying_method', 'Credit Card']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['gift_card_payment'] = Payment::where([
                                    ['paying_method', 'Gift Card']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['cheque_payment'] = Payment::where([
                                    ['paying_method', 'Cheque']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['paypal_payment'] = Payment::where([
                                    ['paying_method', 'Paypal']
                                ])->whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['total_sale_return'] = Returns::whereDate('created_at', date("Y-m-d"))->sum('grand_total');
        $data['total_expense'] = Expense::whereDate('created_at', date("Y-m-d"))->sum('amount');
        $data['total_cash'] = $data['total_payment'] - ($data['total_sale_return'] + $data['total_expense']);
        return $data;
    }

    public function todayProfit($warehouse_id)
    {
        if($warehouse_id == 0)
            $product_sale_data = Product_Sale::select(DB::raw('product_id, sum(qty) as sold_qty, sum(total) as sold_amount'))->whereDate('created_at', date("Y-m-d"))->groupBy('product_id')->get();
        else
            $product_sale_data = Sale::join('product_sales', 'sales.id', '=', 'product_sales.sale_id')
            ->select(DB::raw('product_sales.product_id, sum(product_sales.qty) as sold_qty, sum(product_sales.total) as sold_amount'))
            ->where('sales.warehouse_id', $warehouse_id)->whereDate('sales.created_at', date("Y-m-d"))
            ->groupBy('product_sales.product_id')->get();
        
        $product_revenue = 0;
        $product_cost = 0;
        $profit = 0;
        foreach ($product_sale_data as $key => $product_sale) {
            if($warehouse_id == 0)
                $product_purchase_data = ProductPurchase::where('product_id', $product_sale->product_id)->get();
            else
                $product_purchase_data = Purchase::join('product_purchases', 'purchases.id', '=', 'product_purchases.purchase_id')
                ->where([
                    ['product_purchases.product_id', $product_sale->product_id],
                    ['purchases.warehouse_id', $warehouse_id]
                ])->select('product_purchases.*')->get();

            $purchased_qty = 0;
            $purchased_amount = 0;
            $sold_qty = $product_sale->sold_qty;
            $product_revenue += $product_sale->sold_amount;
            foreach ($product_purchase_data as $key => $product_purchase) {
                $purchased_qty += $product_purchase->qty;
                $purchased_amount += $product_purchase->total;
                if($purchased_qty >= $sold_qty) {
                    $qty_diff = $purchased_qty - $sold_qty;
                    $unit_cost = $product_purchase->total / $product_purchase->qty;
                    $purchased_amount -= ($qty_diff * $unit_cost);
                    break;
                }
            }

            $product_cost += $purchased_amount;
            $profit += $product_sale->sold_amount - $purchased_amount;
        }
        
        $data['product_revenue'] = $product_revenue;
        $data['product_cost'] = $product_cost;
        if($warehouse_id == 0)
            $data['expense_amount'] = Expense::whereDate('created_at', date("Y-m-d"))->sum('amount');
        else
            $data['expense_amount'] = Expense::where('warehouse_id', $warehouse_id)->whereDate('created_at', date("Y-m-d"))->sum('amount');

        $data['profit'] = $profit - $data['expense_amount'];
        return $data;
    }

    public function deleteBySelection(Request $request)
    {
        $sale_id = $request['saleIdArray'];
        foreach ($sale_id as $id) {
            $lims_sale_data = Sale::find($id);
            $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
            $lims_delivery_data = Delivery::where('sale_id',$id)->first();
            if($lims_sale_data->sale_status == 3)
                $message = 'Draft deleted successfully';
            else
                $message = 'Sale deleted successfully';
            foreach ($lims_product_sale_data as $product_sale) {
                $lims_product_data = Product::find($product_sale->product_id);
                //adjust product quantity
                if( ($lims_sale_data->sale_status == 1) && ($lims_product_data->type == 'combo') ){
                    $product_list = explode(",", $lims_product_data->product_list);
                    $qty_list = explode(",", $lims_product_data->qty_list);

                    foreach ($product_list as $index=>$child_id) {
                        $child_data = Product::find($child_id);
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $lims_sale_data->warehouse_id ],
                            ])->first();

                        $child_data->qty += $product_sale->qty * $qty_list[$index];
                        $child_warehouse_data->qty += $product_sale->qty * $qty_list[$index];

                        $child_data->save();
                        $child_warehouse_data->save();
                    }
                }
                elseif(($lims_sale_data->sale_status == 1) && ($product_sale->sale_unit_id != 0)){
                    $lims_sale_unit_data = Unit::find($product_sale->sale_unit_id);
                    if ($lims_sale_unit_data->operator == '*')
                        $product_sale->qty = $product_sale->qty * $lims_sale_unit_data->operation_value;
                    else
                        $product_sale->qty = $product_sale->qty / $lims_sale_unit_data->operation_value;
                    if($product_sale->variant_id) {
                        $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($lims_product_data->id, $product_sale->variant_id)->first();
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($lims_product_data->id, $product_sale->variant_id, $lims_sale_data->warehouse_id)->first();
                        $lims_product_variant_data->qty += $product_sale->qty;
                        $lims_product_variant_data->save();
                    }
                    else {
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($lims_product_data->id, $lims_sale_data->warehouse_id)->first();
                    }

                    $lims_product_data->qty += $product_sale->qty;
                    $lims_product_warehouse_data->qty += $product_sale->qty;
                    $lims_product_data->save();
                    $lims_product_warehouse_data->save();
                }
                $product_sale->delete();
            }
            $lims_payment_data = Payment::where('sale_id', $id)->get();
            foreach ($lims_payment_data as $payment) {
                if($payment->paying_method == 'Gift Card'){
                    $lims_payment_with_gift_card_data = PaymentWithGiftCard::where('payment_id', $payment->id)->first();
                    $lims_gift_card_data = GiftCard::find($lims_payment_with_gift_card_data->gift_card_id);
                    $lims_gift_card_data->expense -= $payment->amount;
                    $lims_gift_card_data->save();
                    $lims_payment_with_gift_card_data->delete();
                }
                elseif($payment->paying_method == 'Cheque'){
                    $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $payment->id)->first();
                    $lims_payment_cheque_data->delete();
                }
                elseif($payment->paying_method == 'Credit Card'){
                    $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $payment->id)->first();
                    $lims_payment_with_credit_card_data->delete();
                }
                elseif($payment->paying_method == 'Paypal'){
                    $lims_payment_paypal_data = PaymentWithPaypal::where('payment_id', $payment->id)->first();
                    if($lims_payment_paypal_data)
                        $lims_payment_paypal_data->delete();
                }
                elseif($payment->paying_method == 'Deposit'){
                    $lims_customer_data = Customer::find($lims_sale_data->customer_id);
                    $lims_customer_data->expense -= $payment->amount;
                    $lims_customer_data->save();
                }
                $payment->delete();
            }
            if($lims_delivery_data)
                $lims_delivery_data->delete();
            if($lims_sale_data->coupon_id) {
                $lims_coupon_data = Coupon::find($lims_sale_data->coupon_id);
                $lims_coupon_data->used -= 1;
                $lims_coupon_data->save();
            }
            $lims_sale_data->delete();
        }
        return 'Sale deleted successfully!';
    }
    
    public function destroy($id)
    {
        $url = url()->previous();
        $lims_sale_data = Sale::find($id);
        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_delivery_data = Delivery::where('sale_id',$id)->first();
        if($lims_sale_data->sale_status == 3)
            $message = 'Draft deleted successfully';
        else
            $message = 'Sale deleted successfully';
        foreach ($lims_product_sale_data as $product_sale) {
            $lims_product_data = Product::find($product_sale->product_id);
            //adjust product quantity
            if( ($lims_sale_data->sale_status == 1) && ($lims_product_data->type == 'combo') ){
                $product_list = explode(",", $lims_product_data->product_list);
                $qty_list = explode(",", $lims_product_data->qty_list);

                foreach ($product_list as $index=>$child_id) {
                    $child_data = Product::find($child_id);
                    $child_warehouse_data = Product_Warehouse::where([
                        ['product_id', $child_id],
                        ['warehouse_id', $lims_sale_data->warehouse_id ],
                        ])->first();

                    $child_data->qty += $product_sale->qty * $qty_list[$index];
                    $child_warehouse_data->qty += $product_sale->qty * $qty_list[$index];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }
            elseif(($lims_sale_data->sale_status == 1) && ($product_sale->sale_unit_id != 0)){
                $lims_sale_unit_data = Unit::find($product_sale->sale_unit_id);
                if ($lims_sale_unit_data->operator == '*')
                    $product_sale->qty = $product_sale->qty * $lims_sale_unit_data->operation_value;
                else
                    $product_sale->qty = $product_sale->qty / $lims_sale_unit_data->operation_value;
                if($product_sale->variant_id) {
                    $lims_product_variant_data = ProductVariant::select('id', 'qty')->FindExactProduct($lims_product_data->id, $product_sale->variant_id)->first();
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($lims_product_data->id, $product_sale->variant_id, $lims_sale_data->warehouse_id)->first();
                    $lims_product_variant_data->qty += $product_sale->qty;
                    $lims_product_variant_data->save();
                }
                else {
                    $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($lims_product_data->id, $lims_sale_data->warehouse_id)->first();
                }
                    
                $lims_product_data->qty += $product_sale->qty;
                $lims_product_warehouse_data->qty += $product_sale->qty;
                $lims_product_data->save();
                $lims_product_warehouse_data->save();
            }
            $product_sale->delete();
        }
        $lims_payment_data = Payment::where('sale_id', $id)->get();
        foreach ($lims_payment_data as $payment) {
            if($payment->paying_method == 'Gift Card'){
                $lims_payment_with_gift_card_data = PaymentWithGiftCard::where('payment_id', $payment->id)->first();
                $lims_gift_card_data = GiftCard::find($lims_payment_with_gift_card_data->gift_card_id);
                $lims_gift_card_data->expense -= $payment->amount;
                $lims_gift_card_data->save();
                $lims_payment_with_gift_card_data->delete();
            }
            elseif($payment->paying_method == 'Cheque'){
                $lims_payment_cheque_data = PaymentWithCheque::where('payment_id', $payment->id)->first();
                $lims_payment_cheque_data->delete();
            }
            elseif($payment->paying_method == 'Credit Card'){
                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('payment_id', $payment->id)->first();
                $lims_payment_with_credit_card_data->delete();
            }
            elseif($payment->paying_method == 'Paypal'){
                $lims_payment_paypal_data = PaymentWithPaypal::where('payment_id', $payment->id)->first();
                if($lims_payment_paypal_data)
                    $lims_payment_paypal_data->delete();
            }
            elseif($payment->paying_method == 'Deposit'){
                $lims_customer_data = Customer::find($lims_sale_data->customer_id);
                $lims_customer_data->expense -= $payment->amount;
                $lims_customer_data->save();
            }
            $payment->delete();
        }
        if($lims_delivery_data)
            $lims_delivery_data->delete();
        if($lims_sale_data->coupon_id) {
            $lims_coupon_data = Coupon::find($lims_sale_data->coupon_id);
            $lims_coupon_data->used -= 1;
            $lims_coupon_data->save();
        }
        $lims_sale_data->delete();
        return Redirect::to($url)->with('not_permitted', $message);
    }

    public function searchInvoices(Request $request)
    {
        $referenceNo = $request->input('reference_no');
        $duca = $request->input('duca');
        $query = Sale::query();
    
        if ($referenceNo) {
            $query->where('reference_no', 'LIKE', "%$referenceNo%");
        }
    
        if ($duca) {
            $query->orWhere('duca', 'LIKE', "%$duca%");
        }
    
        $invoices = $query->get();
    
        return view('sale.search_invoices', compact('invoices'));
    }

    public function envioJson(Request $request) {
        try {
            $id = $request->input('id');
   
            $numeroControlAnexo =  "";
            $lims_sale_data = Sale::find($id);
            $lims_customer_data = Customer::find($lims_sale_data->customer_id);
            $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
            $codigoGeneracion = $lims_sale_data->codgeneracion;
            $numerocontroldte = $lims_sale_data->numerocontrol;
    
            // Verifica que los datos esenciales existen
            if (!$lims_sale_data || !$lims_customer_data || !$lims_product_sale_data) {
                throw new Exception('Datos de venta, cliente o productos no encontrados.');
            }
    
            $nit = $lims_customer_data->nit;
            $code_country = $lims_customer_data->countries1->code;
            $name_country = $lims_customer_data->countries1->name;
            $code_muni = $lims_customer_data->municipio->code;
            $code_estado = $lims_customer_data->estado->code;
            $nrc = $lims_customer_data->tax_no;
            $nombre = $lims_customer_data->name;
            $direccion = $lims_customer_data->address;
            $dui = $lims_customer_data->dui;
            $nrcspu = $lims_customer_data->vat_number;
            $telefono = $lims_customer_data->phone;
            $email = $lims_customer_data->email;
            $estado1 = $lims_customer_data->estado;
            $municipio = $lims_customer_data->municipio;
            $name_giro = $lims_customer_data->gire->name;
            $code_giro = $lims_customer_data->gire->code;
            $sCompany = $lims_customer_data->company_name;
            $sPhoneNUmber = $lims_customer_data->phone_number;
            $address = $lims_customer_data->address;
            $documentId = $lims_sale_data->document_id;
            $fecha = $lims_sale_data->created_at;
            $fecEmi = substr($fecha, 0, 10);
            $horEmi = substr($fecha, -8);
          
            // Leer el archivo JSON
            $jsonString = file_get_contents('app/Http/Controllers/company.json');
            $data = json_decode($jsonString);
    
            // Verifica que los datos del JSON existen
            if (!$data) {
                throw new Exception('Error al leer el archivo JSON.');
            }
    
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
            $licitacion = $lims_sale_data->licitacion;
            $terceros = "";
    
            if ($licitacion == "on") {
                $terceros = "null";
            } else {
                $terceros = "null";
            }
    
            // Construcción del JSON para el envío de facturación electrónica
            // Dependiendo del valor de $documentId se generará el JSON específico
            
            $dTotal = 0.00;
                     switch ($documentId) {
                        case 1:
                        $detalleProductos = [];
                        $dIva = 0.00;
                        $dSUmNeto = 0.00;
                        $dSUmNetoExentas = 0.00;

                        $dExentas = 0.00;
                        $dgravadas = 0.00;
                        $customerName = '';
                     
    
                            // Obtener los productos relacionados con la venta
                            foreach ($lims_product_sale_data as $index => $product_sale) {
                                $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                  
                                // Verificar si el producto existe
                                if ($product) {
                //si tax es cero es exento 
                $cantidad = $product_sale->qty;
                if ($product_sale->tax == 0)    
                {
                    
                    $dExentas =  $product_sale->total;
                    $dgravadas = 0.00;
                    $cantidad = 0 ;
                }
                else{
                $dgravadas = round( $product_sale->total -$product_sale->tax, 2);
                    $dExentas = 0.00;
                }

                                    $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                              
               $tributos = [20];
               if ($product_sale->tax == 0) {
                $tributos = null;
                }
                else{
                    $tributos = ["20"];
                }
                                    $detalleProducto = [
                                        "numItem" => $index + 1, // Número secuencial del item
                                        "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                        "numeroDocumento" => null,
                                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                        "codTributo" => null,
                                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                        "cantidad" => $cantidad, // Cantidad del producto
                                        "uniMedida" => 59, // Unidad de medida del producto
                                        "precioUni" => round($product_sale->net_unit_price,2), // Precio unitario del producto
                                        "montoDescu" => 0,
                                        "ventaNoSuj" => 0,
                                        "ventaExenta" =>  0,
                                        "ventaGravada" => $dgravadas, // Monto gravado del producto
                                        "tributos" => $tributos, // Código de tributos aplicados al producto
                                        "psv" => 0,
                                        "noGravado" => $dExentas
                                    ];
                                    $dSUmNetoExentas += $dExentas;
                                    $dIva += $product_sale->tax;
                                    $dSUmNeto += $dgravadas;
                                    $detalleProductos[] = $detalleProducto;
                                }




                        
                   
                            }
                
                      // Calcular el total
                      $dTotal = $dIva + $dSUmNeto+ $dSUmNetoExentas;

                      $sLetras = $this->numeroALetras($dTotal);
                       
                             
                    
                
                       $json_variable='
                
                        {
                                    "nit": "'.$emisorNit.'",
                                    "activo": "true",
                                    "passwordPri": "'.$emisorprivatekey.'",
                                    "dteJson": {
                
                                    "identificacion": {
                                        "version": 3,
                                        "ambiente": "'.$emisorambiente.'",
                                        "tipoDte": "03",
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
                                    "documentoRelacionado": null,
                                    "emisor": {
                                        "nit": "'.$emisorNit.'",
                                        "nrc": "'.$emisorNrc.'",
                                        "nombre": "'.$emisorNombre.'",
                                        "codActividad": "'.$emisorCodActividad.'",
                                        "descActividad": "'.$emisorDescActividad.'",
                                        "nombreComercial": "'.$emisorNombreComercial.'",
                                        "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                        "direccion": {
                                          "departamento": "'.$emisorDireccionDepartamento.'",
                                          "municipio": "'.$emisorDireccionMunicipio.'",
                                          "complemento": "'.$emisorDireccionComplemento.'"
                                        },
                                        "telefono": "'.$emisorTelefono.'",
                                        "correo": "'.$emisorCorreo.'",
                                        "codEstableMH": null,
                                        "codEstable": null,
                                        "codPuntoVentaMH": null,
                                        "codPuntoVenta": null
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
                                        "telefono": "'.$sPhoneNUmber.'",
                                        "correo": "'.$email.'"
                                        },
                                    "otrosDocumentos": null,
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
                        "porcentajeDescuento": 0,
                        "totalDescu": 0,
                        "tributos": [{
                            "codigo": "20",
                            "descripcion": "Impuesto al Valor Agregado 13%",
                            "valor": '.strval($dIva).'
                        }],
                        "subTotal": '.strval($dSUmNeto).',
                        "ivaPerci1": 0,
                        "ivaRete1": 0,
                        "reteRenta": 0,
                                        "montoTotalOperacion": '.strval($dTotal-$dSUmNetoExentas).',
                                        "totalNoGravado":'.strval($dSUmNetoExentas).',
                                        "totalPagar": '.strval($dTotal).',
                                        "totalLetras": "'.$sLetras.' ",
                                        "saldoFavor": 0,
                                        "condicionOperacion": 1,
                                        "pagos": [{
                                            "codigo": "01",
                                            "montoPago": '.strval($dTotal).',
                                            "plazo": null,
                                            "referencia": null,
                                            "periodo": null
                                        }],
                                          "numPagoElectronico": null
                                    },
                
                                        "extension": {
                                            "nombEntrega": null,
                                            "docuEntrega": null,
                                            "nombRecibe": null,
                                            "docuRecibe": null,
                                            "observaciones": null,
                                            "placaVehiculo": null
                                        },
                                        "apendice": null
                
                                    }
                                   
                                }'; 
  
                                  // fin
                                // Agregar el detalle de productos al JSON existente
                                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                   
                            
                             $sResult =    $this->processRequest($json_variable);
                          
                           
                            break;
                
                            ///consmidor
                            case 2:
                                $detalleProductos = [];
                                $dIva = 0.00;
                                $dSUmNeto = 0.00;
                                $dSUmNetoExentas = 0.00;

                                $dExentas = 0.00;
                                $dgravadas = 0.00;
                                $customerName = '';
                               
                                
                                    // Obtener los productos relacionados con la venta
                                    foreach ($lims_product_sale_data as $index => $product_sale) {
                                        $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                 
                                        // Verificar si el producto existe
                                        if ($product) {
                                          //si tax es cero es exento 
                    $porcetajeimpuesto = 0.13;
                    $cantidad = $product_sale->qty;
                    if ($product_sale->tax == 0)    
                    {
                        $dExentas =  $product_sale->total-$product_sale->tax;
                        $dgravadas = 0.00;
                        $porcetajeimpuesto = 0.00;
                        $cantidad = 0 ;
                    }
                    else{
                     $dgravadas = round( $product_sale->total, 2);
                        $dExentas = 0.00;
                        $porcetajeimpuesto = 0.13;
                    }
                                    
                                            $detalleProducto = [
                                                "numItem" => $index + 1, // Número secuencial del item
                                                "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                "numeroDocumento" => null,
                                                "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                "codTributo" => null,
                                                "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                "cantidad" =>round($cantidad, 8), // Cantidad del producto
                                                "uniMedida" => 59, // Unidad de medida del producto
                                                "precioUni" => round($product_sale->net_unit_price + ($product_sale->net_unit_price *$porcetajeimpuesto),2), // Precio unitario del producto
                                                "montoDescu" => 0,
                                                "ventaNoSuj" => 0,
                                                "ventaExenta" =>0,
                                                "ventaGravada" => $dgravadas , // Monto gravado del producto
                                                "tributos" => null,
                                                "ivaItem" => $product_sale->tax, // Código de tributos aplicados al producto
                                                "psv" => 0,
                                                "noGravado" => $dExentas
                                            ];
                                            $dSUmNetoExentas += $dExentas;              
                                            $dIva += $product_sale->tax;
                                            $dSUmNeto += $dgravadas - $product_sale->tax;
                                            $detalleProductos[] = $detalleProducto;
                                        }

 
                                    }
                        
                             
                                    $dTotal = $dIva + $dSUmNeto;

                                    $sLetras = $this->numeroALetras($dTotal+$dSUmNetoExentas);
                       
                  
           
           
           
                       
                        
                               $json_variable='
                        
                                {
                                 
                                    "nit": "'.$emisorNit.'",
                                    "activo": "true",
                                    "passwordPri": "'.$emisorprivatekey.'",
                                            "dteJson": {
                        
                                            "identificacion": {
                                                "version": 1,
                                                "ambiente": "'.$emisorambiente.'",
                                                "tipoDte": "01",
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
                                            "documentoRelacionado": null,
                                            "emisor": {
                                                "nit": "'.$emisorNit.'",
                                                "nrc": "'.$emisorNrc.'",
                                                "nombre": "'.$emisorNombre.'",
                                                "codActividad": "'.$emisorCodActividad.'",
                                                "descActividad": "'.$emisorDescActividad.'",
                                                "nombreComercial": "'.$emisorNombreComercial.'",
                                                "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                "direccion": {
                                                  "departamento": "'.$emisorDireccionDepartamento.'",
                                                  "municipio": "'.$emisorDireccionMunicipio.'",
                                                  "complemento": "'.$emisorDireccionComplemento.'"
                                                },
                                                "telefono": "'.$emisorTelefono.'",
                                                "correo": "'.$emisorCorreo.'",
                                                "codEstableMH": null,
                                                "codEstable": null,
                                                "codPuntoVentaMH": null,
                                                "codPuntoVenta": null
                                            },
                                            "receptor": {
                                              
                                                "nrc": null,
                                                "nombre": "'.$nombre.'",
                                                "codActividad": "'.$code_giro.'",
                                                "descActividad": "'.$name_giro.'",
                                          "tipoDocumento": '.(empty($nit) ? 'null' : ($code_estado == "00" ? '"37"' : '"36"')).',
                                           "numDocumento": '.(empty($nit) ? 'null' : '"'.$nit.'"').',
                                                "direccion": {
                                                    "departamento": "'.$code_estado.'",
                                                    "municipio": "'.$code_muni.'",
                                                    "complemento": "'.$address.'"
                                                },
                                                "telefono": "'.$sPhoneNUmber.'",
                                                "correo": "'.$email.'"
                                                },
                                            "otrosDocumentos": null,
                                            "ventaTercero": '.$terceros.',
                                            "cuerpoDocumento": [],
                                            "resumen": {
                                       "totalNoSuj":0,
                                "totalExenta": 0,
                                "totalGravada": '.strval($dTotal).',
                                "subTotalVentas": '.strval($dTotal).',
                                "descuNoSuj": 0,
                                "descuExenta": 0,
                                "descuGravada": 0,
                                "porcentajeDescuento": 0,
                                "totalDescu": 0,
                                "tributos": null,
                                "subTotal": '.strval($dTotal).',
                          
                                "ivaRete1": 0,
                                "reteRenta": 0,
                                                "montoTotalOperacion": '.strval($dTotal).',
                                                "totalNoGravado": '.strval($dSUmNetoExentas).',
                                                "totalPagar": '.strval($dTotal+$dSUmNetoExentas).',
                                                "totalLetras": "'.$sLetras.' ",
                                                "saldoFavor": 0,
                                                "condicionOperacion": 1,
                                                "totalIva": '.strval(round($dIva, 2)).',
                                                "pagos": [{
                                                    "codigo": "01",
                                                    "montoPago": '.strval($dTotal+$dSUmNetoExentas).',
                                                    "plazo": null,
                                                    "referencia": null,
                                                    "periodo": null
                                                }],
                                                  "numPagoElectronico": null
                                            },
                        
                                                "extension": {
                                                    "nombEntrega": null,
                                                    "docuEntrega": null,
                                                    "nombRecibe": null,
                                                    "docuRecibe": null,
                                                    "observaciones": null,
                                                    "placaVehiculo": null
                                                },
                                                "apendice": null
                        
                                            }
                                           
                                        }'; 
                        
                                       
                                                    
                                          // fin
                                        // Agregar el detalle de productos al JSON existente
                                        $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                     
                                  
                                     
                                    $sResult =   $this->processRequest($json_variable);
                         
                                    break;
                                            ///factura exportacion
                                    case 5:
                                        $detalleProductos = [];
                                        $dIva = 0.00;
                                        $dSUmNeto = 0.00;
                                        $customerName = '';
                                            // Obtener los productos relacionados con la venta
                                            foreach ($lims_product_sale_data as $index => $product_sale) {
                                                $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                                
                                                // Verificar si el producto existe
                                                if ($product) {
                                
                                                    $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                                              
                                                    
                                          
                                                        $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                               
                                                    $detalleProducto = [
                                                        "numItem" => $index + 1, // Número secuencial del item
                                                     
                                                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                     
                                                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                        "cantidad" => $product_sale->qty, // Cantidad del producto
                                                        "uniMedida" => 59, // Unidad de medida del producto
                                                        "precioUni" => round( $product_sale->net_unit_price + ($product_sale->net_unit_price *0.13)), // Precio unitario del producto
                                                        "montoDescu" => 0,
                                                     
                                                        "ventaGravada" => $product_sale->total , // Monto gravado del producto
                                                        "tributos" => null,
                                                        "noGravado" => 0
                                                    ];
                                                    $dIva = $dIva + $product_sale->tax;
                            $dSUmNeto =  $product_sale->total -$product_sale->tax;
                        
                                                    $detalleProductos[] = $detalleProducto;
                                                }



                                            $dIva = number_format($dIva, 2 );
                                            $dSUmNeto = number_format($dSUmNeto);
                                            $dTotal += $dIva + $dSUmNeto;

                                            }
                                
                                            $sLetras = $this->numeroALetras($dTotal);
                                
                                
                                
                                       $json_variable='
                                
                                        {
                                                    "nit": "06140203741144",
                                                    "activo": "true",
                                                    "passwordPri": "'.$emisorprivatekey.'",
                                                    "dteJson": {
                                
                                                    "identificacion": {
                                                        "version": 1,
                                                        "ambiente": "'.$emisorambiente.'",
                                                        "tipoDte": "11",
                                                        "numeroControl": "'.$numerocontroldte.'",
                                
                                                        "codigoGeneracion": "'.$codigoGeneracion.'",
                                                        "tipoModelo": 1,
                                                        "tipoOperacion": 1,
                                                        "tipoContingencia": null,
                                                        "motivoContigencia": null,
                                                        "fecEmi": "'.$fecEmi.'",
                                                        "horEmi": "'.$horEmi.'",
                                                        "tipoMoneda": "USD"
                                
                                                    },
                                                    "otrosDocumentos":null,
                                                    "ventaTercero": null,
                                                    "emisor": {
                                                        "nit": "'.$emisorNit.'",
                                                        "nrc": "'.$emisorNrc.'",
                                                        "nombre": "'.$emisorNombre.'",
                                                        "codActividad": "'.$emisorCodActividad.'",
                                                        "descActividad": "'.$emisorDescActividad.'",
                                                        "nombreComercial": "'.$emisorNombreComercial.'",
                                                        "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                        "direccion": {
                                                          "departamento": "'.$emisorDireccionDepartamento.'",
                                                          "municipio": "'.$emisorDireccionMunicipio.'",
                                                          "complemento": "'.$emisorDireccionComplemento.'"
                                                        },
                                                        "telefono": "'.$emisorTelefono.'",
                                                        "correo": "'.$emisorCorreo.'",
                                                        "codEstableMH": null,
                                                        "codEstable": null,
                                                        "codPuntoVentaMH": null,
                                                        "codPuntoVenta": null,
                                                        "regimen":null,
                                                        "recintoFiscal": "01",
                                                        "tipoItemExpor": 1
                                                    },
                                                    "receptor": {
                                                        "nombre": "'.$customerName.'",
                                                        "descActividad": "'.$name_giro.'",
                                                        "nombreComercial": "'.$sCompany.'",
                                                        "codPais": "'.$code_country.'",
                                                        "nombrePais":"'.$name_country.'",
                                                        "complemento":"'.$address.'",
                                                        "tipoDocumento": "36",
                                                        "numDocumento":"'.$nit.'",
                                                        "tipoPersona":1,
                                                        "telefono": "'.$sPhoneNUmber.'",
                                                        "correo": "'.$email.'"
                                                    },
                                                    "otrosDocumentos": null,
                                                    "ventaTercero": null,
                                                    "cuerpoDocumento": [],
                                                    "resumen": {
                                                        "totalGravada": '.$dTotal.',
                                                        "porcentajeDescuento": 0,
                                                        "totalDescu": 0,
                                                        "descuento":0,
                                                        "codIncoterms": null,
                                                        "descIncoterms": null,
                                                        "flete":0,
                                                        "seguro":0, 
                                                        "montoTotalOperacion": '.$dTotal.',
                                                        "totalNoGravado": 0,
                                                        "totalPagar": '.$dTotal.',
                                                        "totalLetras": "'.$sLetras.'",
                                                      
                                                        "condicionOperacion": 1,
                                                        "pagos": [
                                                            {
                                                                "codigo": "01",
                                                                "montoPago": '.$dTotal.',
                                                                "plazo": "01",
                                                                "referencia": null,
                                                                "periodo": 0
                                                            }
                                                        ],
                                                        "observaciones": "observaciones",
                                                        "numPagoElectronico": null
                                                    },
                                                    "apendice": null
                                
                                                    }
                                                   
                                                }'; 
                                
                                            
                                                  // fin
                                                // Agregar el detalle de productos al JSON existente
                                                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                                
                                
                                             // echo($json_variable);
                                            //   exit;
                                            $sResult =     $this->processRequest($json_variable);
                                            break;
                                                    /// nota de credito
                                            case 3:
                                                $detalleProductos = [];
                                                $dIva = 0.00;
                                                $dSUmNeto = 0.00;
                                                $customerName = '';
                                                    // Obtener los productos relacionados con la venta
                                                    foreach ($lims_product_sale_data as $index => $product_sale) {
                                                        $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                                        
                                                        // Verificar si el producto existe
                                                        if ($product) {
                                        
                                                            $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                                                      
                                                            
                                               
                                                                $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                                        
                                                    
                                                            $detalleProducto = [
                                                                "numItem" => $index + 1, // Número secuencial del item
                                                                "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                                "numeroDocumento" => $codgeneracion_anexo,
                                                               
                                                                "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                                "codTributo" => null,
                                                                "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                                "cantidad" => $product_sale->qty, // Cantidad del producto
                                                                "uniMedida" => 59, // Unidad de medida del producto
                                                                "precioUni" => $product_sale->net_unit_price , // Precio unitario del producto
                                                                "montoDescu" => 0,
                                                                "ventaNoSuj" => 0,
                                                                "ventaExenta" => 0,
                                                                "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                                "tributos" => [
                                                                    "20"
                                                                  ]
                                                            ];
                                                            $dIva = $dIva + $product_sale->tax;
                                                            $dSUmNeto =  $product_sale->total -$product_sale->tax;
                                                        
                                                    $detalleProductos[] = $detalleProducto;
                                                        }

     $dIva = number_format($dIva, 2 );
                                                    $dSUmNeto = number_format($dSUmNeto);
                                                    $dTotal += $dIva + $dSUmNeto;

                                                    }
                                        
                                               
                                                    $sLetras = $this->numeroALetras($dTotal);
                                        
                                        
                                        
                                               $json_variable='
                                        
                                                {
                                                            "nit": "06140203741144",
                                                            "activo": "true",
                                                            "passwordPri": "'.$emisorprivatekey.'",
                                                            "dteJson": {
                                        
                                                            "identificacion": {
                                                                "version": 3,
                                                                "ambiente": "'.$emisorambiente.'",
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
                                                                "tipoDocumento": "'.$subtype.'",
                                                                "tipoGeneracion" :2, 
                                                                "numeroDocumento" : "'.$codgeneracion_anexo.'",
                                                                "fechaEmision":"'.$fecEmiAnex.'"
                                                                
                                                                }],
                                                            "emisor": {
                                                                "nit": "'.$emisorNit.'",
                                                                "nrc": "'.$emisorNrc.'",
                                                                "nombre": "'.$emisorNombre.'",
                                                                "codActividad": "'.$emisorCodActividad.'",
                                                                "descActividad": "'.$emisorDescActividad.'",
                                                                "nombreComercial": "'.$emisorNombreComercial.'",
                                                                "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
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
                                                                "nombre": "'.$customerName.'",
                                                                "codActividad": "'.$code_giro.'",
                                                                "descActividad": "'.$name_giro.'",
                                                                "nombreComercial": null,
                                                                "direccion": {
                                                                    "departamento": "'.$code_estado.'",
                                                                    "municipio": "'.$code_muni.'",
                                                                    "complemento": "'.$address.'"
                                                                },
                                                                "telefono": "'.$email.'",
                                                                "correo": "'.$email.'"
                                                                },
                                               
                                                            "ventaTercero": null,
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
                                                                "totalLetras": "'.$sLetras.'",
                                                                      "condicionOperacion": 2
                                                            },
                                                            "extension": {
                                                                "nombEntrega": "Francisco Orellana",
                                                                "docuEntrega": "08130203001010",
                                                                "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                                "docuRecibe": "06140203741144",
                                                                "observaciones": null
                                                            },
                                                            "apendice": null
                                        
                                                            }
                                                           
                                                        }'; 
                                        
                                                    
                                                          // fin
                                                        // Agregar el detalle de productos al JSON existente
                                                        $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                                        
                                        
                                               //     echo($json_variable);
                                                 //      exit;
                                                 $sResult =     $this->processRequest($json_variable);
                                                    break;
                
                                                    ///remision 
                                                    case 4:
                                                        $detalleProductos = [];
                                                        $dIva = 0.00;
                                                        $dSUmNeto = 0.00;
                                                        $customerName = '';
                                                            // Obtener los productos relacionados con la venta
                                                            foreach ($lims_product_sale_data as $index => $product_sale) {
                                                                $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                                                
                                                                // Verificar si el producto existe
                                                                if ($product) {
                                                
                                                                    $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                                                              
                                                                    
                                                                    // Verificar si la venta y el cliente existen
                                                             
                                                                        $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                                              
                
                                                            if ($numeroControlAnexo == "NA")
                                                            {
                                                                        $detalleProducto = [
                                                                        "numItem" => $index + 1, // Número secuencial del item
                                                                        "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                                        "numeroDocumento" => null,
                                                                       
                                                                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                                        "codTributo" => null,
                                                                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                                        "cantidad" => $product_sale->qty, // Cantidad del producto
                                                                        "uniMedida" => 59, // Unidad de medida del producto
                                                                        "precioUni" => $product_sale->net_unit_price , // Precio unitario del producto
                                                                        "montoDescu" => 0,
                                                                        "ventaNoSuj" => 0,
                                                                        "ventaExenta" => 0,
                                                                        "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                                        "tributos" => [
                                                                            "20"
                                                                          ]
                                                                    ]; 
                                                            }
                                                            else {
                                                                # code...
                                                                $detalleProducto = [
                                                                    "numItem" => $index + 1, // Número secuencial del item
                                                                    "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                                    "numeroDocumento" => $codgeneracion_anexo,
                                                                   
                                                                    "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                                    "codTributo" => null,
                                                                    "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                                    "cantidad" => $product_sale->qty, // Cantidad del producto
                                                                    "uniMedida" => 59, // Unidad de medida del producto
                                                                    "precioUni" => round($product_sale->net_unit_price, 2) , // Precio unitario del producto
                                                                    "montoDescu" => 0,
                                                                    "ventaNoSuj" => 0,
                                                                    "ventaExenta" => 0,
                                                                    "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                                    "tributos" => [
                                                                        "20"
                                                                      ]
                                                                ]; 
                                                            }
                                                           
                                                            $dIva = $dIva + $product_sale->tax;
                                                            $dSUmNeto =  $product_sale->total -$product_sale->tax;
                                                        
                                                            $detalleProductos[] = $detalleProducto;
                                                                }

     $dIva = number_format($dIva, 2 );
                                                            $dSUmNeto = number_format($dSUmNeto);
                                                            $dTotal += $dIva + $dSUmNeto;


                                                            }
                                                
                                                       
                                                            $sLetras = $this->numeroALetras($dTotal);
                                                
                                                if($numeroControlAnexo == "NA")
                                                {
                                                    $json_variable='
                                                
                                                    {
                                                                "nit": "06140203741144",
                                                                "activo": "true",
                                                                "passwordPri":"'.$emisorprivatekey.'",
                                                                "dteJson": {
                                            
                                                                "identificacion": {
                                                                    "version": 3,
                                                                    "ambiente": "'.$emisorambiente.'",
                                                                    "tipoDte": "04",
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
                                                                "documentoRelacionado": null,
                                                                "emisor": {
                                                                    "nit": "'.$emisorNit.'",
                                                                    "nrc": "'.$emisorNrc.'",
                                                                    "nombre": "'.$emisorNombre.'",
                                                                    "codActividad": "'.$emisorCodActividad.'",
                                                                    "descActividad": "'.$emisorDescActividad.'",
                                                                    "nombreComercial": "'.$emisorNombreComercial.'",
                                                                    "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                                    "direccion": {
                                                                    "departamento": "'.$emisorDireccionDepartamento.'",
                                                                    "municipio": "'.$emisorDireccionMunicipio.'",
                                                                    "complemento": "'.$emisorDireccionComplemento.'"
                                                                    },
                                                                    "telefono": "'.$emisorTelefono.'",
                                                                    "correo": "'.$emisorCorreo.'",
                                                                "codEstableMH": null,
                                                        "codEstable": null,
                                                        "codPuntoVentaMH": null,
                                                        "codPuntoVenta": null
                                                                   
                                                                },
                                                                "receptor": {
                                                                    
                                                                    "nrc": "'.$nrc.'",
                                                                    "tipoDocumento":"36",
                                                                    "numDocumento":"'.$nit.'",
                                                                    "nombre": "'.$customerName.'",
                                                                    "codActividad": "'.$code_giro.'",
                                                                    "descActividad": "'.$name_giro.'",
                                                                    "nombreComercial": null,
                                                                    "direccion": {
                                                                        "departamento": "'.$code_estado.'",
                                                                        "municipio": "'.$code_muni.'",
                                                                        "complemento": "'.$address.'"
                                                                    },
                                                                    "telefono": "'.$sPhoneNUmber.'",
                                                                    "correo": "'.$email.'",
                                                                    
                                                                    "bienTitulo": "04"
                                                                    },
                                                   
                                                                "ventaTercero": null,
                                                                "cuerpoDocumento": [],
                                                                "resumen": {
                                                                    "totalNoSuj": 0,
                                                                    "totalExenta": 0,
                                                                    "totalGravada": '.$dSUmNeto.',
                                                                    "subTotalVentas": '.$dSUmNeto.',
                                                                    "descuNoSuj": 0,
                                                                    "porcentajeDescuento": 0,
                                                                    "descuExenta": 0,
                                                                    "descuGravada": 0,
                                                                    "totalDescu": 0,
                                                                    "tributos": [{
                                                                        "codigo": "20",
                                                                        "descripcion": "Impuesto al Valor Agregado 13%",
                                                                        "valor": '.$dIva.'
                                                                    }],
                                                                    "subTotal": '.$dSUmNeto.',
                                                                    "montoTotalOperacion": '.$dTotal.',
                                                                    "totalLetras": "DOS MIL DOSCIENTOS SESENTA DÓLARES "
                                                                },
                                                                "extension": {
                                                                    "nombEntrega": "Francisco Orellana",
                                                                    "docuEntrega": "08130203001010",
                                                                    "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                                    "docuRecibe": "06140203741144",
                                                                    "observaciones": null
                                                                },
                                                                "apendice": null
                                            
                                                                }
                                                               
                                                            }'; 
                                            
                
                                                }
                                                else {
                                                    $json_variable='
                                                
                                                    {
                                                                "nit": "06140203741144",
                                                                "activo": "true",
                                                                "passwordPri": "'.$emisorprivatekey.'",
                                                                "dteJson": {
                                            
                                                                "identificacion": {
                                                                    "version": 3,
                                                                    "ambiente": "'.$emisorambiente.'",
                                                                    "tipoDte": "04",
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
                                                                    "tipoDocumento": "'.$subtype.'",
                                                                    "tipoGeneracion" :2, 
                                                                    "numeroDocumento" : "'.$codgeneracion_anexo.'",
                                                                    "fechaEmision":"'.$fecEmiAnex.'"
                                                                    
                                                                    }],
                                                                "emisor": {
                                                                    "nit": "'.$emisorNit.'",
                                                                    "nrc": "'.$emisorNrc.'",
                                                                    "nombre": "'.$emisorNombre.'",
                                                                    "codActividad": "'.$emisorCodActividad.'",
                                                                    "descActividad": "'.$emisorDescActividad.'",
                                                                    "nombreComercial": "'.$emisorNombreComercial.'",
                                                                    "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                                    "direccion": {
                                                                    "departamento": "'.$emisorDireccionDepartamento.'",
                                                                    "municipio": "'.$emisorDireccionMunicipio.'",
                                                                    "complemento": "'.$emisorDireccionComplemento.'"
                                                                    },
                                                                    "telefono": "'.$emisorTelefono.'",
                                                                    "correo": "'.$emisorCorreo.'",
                                                                "codEstableMH": null,
                                                        "codEstable": null,
                                                        "codPuntoVentaMH": null,
                                                        "codPuntoVenta": null
                                                                   
                                                                },
                                                                "receptor": {
                                                                    
                                                                    "nrc": "'.$nrc.'",
                                                                    "tipoDocumento":"36",
                                                                    "numDocumento":"'.$nit.'",
                                                                    "nombre": "'.$customerName.'",
                                                                    "codActividad": "'.$code_giro.'",
                                                                    "descActividad": "'.$name_giro.'",
                                                                    "nombreComercial": null,
                                                                    "direccion": {
                                                                        "departamento": "'.$code_estado.'",
                                                                        "municipio": "'.$code_muni.'",
                                                                        "complemento": "'.$address.'"
                                                                    },
                                                                    "telefono": "'.$sPhoneNUmber.'",
                                                                    "correo": "'.$email.'",
                                                                    
                                                                    "bienTitulo": "04"
                                                                    },
                                                   
                                                                "ventaTercero": null,
                                                                "cuerpoDocumento": [],
                                                                "resumen": {
                                                                    "totalNoSuj": 0,
                                                                    "totalExenta": 0,
                                                                    "totalGravada": '.$dSUmNeto.',
                                                                    "subTotalVentas": '.$dSUmNeto.',
                                                                    "descuNoSuj": 0,
                                                                    "porcentajeDescuento": 0,
                                                                    "descuExenta": 0,
                                                                    "descuGravada": 0,
                                                                    "totalDescu": 0,
                                                                    "tributos": [{
                                                                        "codigo": "20",
                                                                        "descripcion": "Impuesto al Valor Agregado 13%",
                                                                        "valor": '.$dIva.'
                                                                    }],
                                                                    "subTotal": '.$dSUmNeto.',
                                                                    "montoTotalOperacion": '.$dTotal.',
                                                                    "totalLetras": "DOS MIL DOSCIENTOS SESENTA DÓLARES "
                                                                },
                                                                "extension": {
                                                                    "nombEntrega": "Francisco Orellana",
                                                                    "docuEntrega": "08130203001010",
                                                                    "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                                    "docuRecibe": "06140203741144",
                                                                    "observaciones": null
                                                                },
                                                                "apendice": null
                                            
                                                                }
                                                               
                                                            }'; 
                                            
                                                }
                                                
                                                    
                                                            
                                                                  // fin
                                                                // Agregar el detalle de productos al JSON existente
                                                                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                                                
                                                
                                                         //   echo($json_variable);
                                                           //    exit;
                                                           $sResult =          $this->processRequest($json_variable);
                                                            break;
                
                            default:
                                // Código para casos que no coinciden con el valor especificado
                                // Aquí puedes colocar el código para manejar los casos no especificados
                                // ...
                                break;
                            }           
            
           //  $sResult =    $this->processRequest($json_variable);

           $responseArray = json_decode($sResult, true);
    
           if (strpos(json_encode($responseArray), 'Bad Request') !== false) {
               throw new Exception('Error al transmitir: Bad Request');
           }
    
           $dataresult = json_decode($sResult, true);
           $data = json_decode($json_variable, true);
           $numeroControl1 = $data['dteJson']['identificacion']['numeroControl'];
           $fechae = $data['dteJson']['identificacion']['fecEmi'];
           $sello = $dataresult['selloRecibido'];
           $estado = $dataresult['estado'];
          
           $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=" . $lims_sale_data->codgeneracion . "&fechaEmi=" . $fechae;
           $aqrl = $sqr;

           if ($estado == "PROCESADO") {
               $lims_sale_data->sello = $sello;
               
               $lims_sale_data->estadodte = "done";
                  $lims_sale_data->update();
                  $pdf1 = $this->genInvoice_pdfEmail($id);
               $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>";
               $jsonString = $json_variable;
            
               $jsonData = json_decode($jsonString, true);
               $prettyJson = json_encode($jsonData, JSON_PRETTY_PRINT);
              // $email = "iora2451@gmail.com";
               // con un echo comprobar los parametros del metodo enviarcorreoconimagenYtexto 
               try{
                $this->enviarCorreoConImagenYTexto($email, "factura electronica", "Gracias por su compra a continuacion su qr", $json_variable, $aqrl, $lims_sale_data->codgeneracion, $pdf1);
            }   catch (Exception $e) {
                       echo "Error al enviar el correo: " . $e->getMessage();
                          exit();
            }
             
           } else {
       
            $lims_sale_data->sello = "NA";
         
            $lims_sale_data->estadodte =$sResult;
      
            $lims_sale_data->update(); 
          
            return $sResult;
           }
         
           $numberToWords = new NumberToWords();
           if (\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb') {
               $numberTransformer = $numberToWords->getNumberTransformer('en');
           } else {
               $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
           }
           $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);

           if ($estado != "PROCESADO") {
               echo $sResult;
               
               exit;
           }
       } catch (Exception $e) {
          $lims_sale_data = Sale::find($id);
      
        $lims_sale_data->estadodte = json_encode([
            'numeroControl' => $numerocontroldtte ?? 'N/A',
            'codgeneracion' => strtoupper($codigoGeneracion ?? 'N/A'), //uddd
            'error' => $e->getMessage()
        ]);
        $lims_sale_data->update();
        $this->logError("Exception in genInvoiceDTE", [
            'error' => $e->getMessage()
        ]);
        return $sResult;
       }
   }
        
        
           public function dowjson(Request $request) {
            try {
            $id = $request->input('id');
    
    
    
            $numeroControlAnexo =  "";
    
            $lims_sale_data = Sale::find($id);
            $lims_customer_data = Customer::find($lims_sale_data->customer_id);
            $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
            $codigoGeneracion = $lims_sale_data->codgeneracion;
            $numerocontroldte = $lims_sale_data->numerocontrol;
    
            $resultDTE = $lims_sale_data->estadodte;
            // fi result is done then return json success
           
    
            $nit = $lims_customer_data->nit ;
    
            $code_country = $lims_customer_data->countries1->code;
            $name_country = $lims_customer_data->countries1->name;
     
           
            $code_muni = $lims_customer_data->municipio->code;
            $code_estado = $lims_customer_data->estado->code;
            $nrc = $lims_customer_data->tax_no;
        
       
    
    
            $nit = $lims_customer_data->nit ;
            $nombre = $lims_customer_data->name ;
            $direccion = $lims_customer_data->address ;
            $dui = $lims_customer_data->dui ;
            $nrcspu = $lims_customer_data->vat_number;
            $telefono = $lims_customer_data->phone ;
            $email = $lims_customer_data->email ;
            $estado1 = $lims_customer_data->estado ;
            $municipio = $lims_customer_data->municipio ;
            $name_giro = $lims_customer_data->gire->name;
            $code_giro = $lims_customer_data->gire->code;
            $sCompany = $lims_customer_data->company_name ;
            $sPhoneNUmber = $lims_customer_data->phone_number  ;
            $email = $lims_customer_data->email  ;
            $address = $lims_customer_data->address;
    
            $documentId = $lims_sale_data->document_id;
    
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
            $json_variable="";

    
            //documento de credi fiscal
      
             /*AQUI CONSTRUIREMOS JSON PARA ENVIO DE FACTURACION ELECTRONICA*/
             $dTotal = 0.00;
             switch ($documentId) {
                case 1:
                $detalleProductos = [];
                $dIva = 0.00;
                $dSUmNeto = 0.00;
                $dSUmNetoExentas = 0.00;

                $dExentas = 0.00;
                $dgravadas = 0.00;
                $customerName = '';

    
                    // Obtener los productos relacionados con la venta
                    foreach ($lims_product_sale_data as $index => $product_sale) {
                        $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                          
                        // Verificar si el producto existe
                        if ($product) {
        
                            $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                      
       //si tax es cero es exento 
                            $cantidad = $product_sale->qty;
                            if ($product_sale->tax == 0)    
                            {
                                $dExentas =  $product_sale->total;
                                $dgravadas = 0.00;
                                $cantidad = 0;
                            }
                            else{
                            $dgravadas = round( $product_sale->total -$product_sale->tax, 2);
                                $dExentas = 0.00;
                            }
                            $tributos = [20];
                            if ($product_sale->tax == 0) {
                            $tributos = null;
                            }
                            else{
                                $tributos = ["20"];
                            }
                            $detalleProducto = [
                                "numItem" => $index + 1, // Número secuencial del item
                                "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                "numeroDocumento" => null,
                                "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                "codTributo" => null,
                                "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                "cantidad" => $cantidad, // Cantidad del producto
                                "uniMedida" => 59, // Unidad de medida del producto
                                "precioUni" => round($product_sale->net_unit_price,2), // Precio unitario del producto
                                "montoDescu" => 0,
                                "ventaNoSuj" => 0,
                                "ventaExenta" =>  0,
                                "ventaGravada" => $dgravadas, // Monto gravado del producto
                                "tributos" => $tributos, // Código de tributos aplicados al producto
                                "psv" => 0,
                                "noGravado" => $dExentas
                            ];
                                     // Acumular los valores
                $dSUmNetoExentas += $dExentas;
                $dIva += $product_sale->tax;
                $dSUmNeto += $dgravadas;
                $detalleProductos[] = $detalleProducto;
                        }



                    }
        
            
                    $dTotal = $dIva + $dSUmNeto+ $dSUmNetoExentas;

                    $sLetras = $this->numeroALetras($dTotal);
                    
               
        
        
               $json_variable='
        
                {
                            "nit": "'.$emisorNit.'",
                            "activo": "true",
                            "passwordPri": "'.$emisorprivatekey.'",
                            "dteJson": {
        
                            "identificacion": {
                                "version": 3,
                                "ambiente": "'.$emisorambiente.'",
                                "tipoDte": "03",
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
                            "documentoRelacionado": null,
                            "emisor": {
                                "nit": "'.$emisorNit.'",
                                "nrc": "'.$emisorNrc.'",
                                "nombre": "'.$emisorNombre.'",
                                "codActividad": "'.$emisorCodActividad.'",
                                "descActividad": "'.$emisorDescActividad.'",
                                "nombreComercial": "'.$emisorNombreComercial.'",
                                "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                "direccion": {
                                  "departamento": "'.$emisorDireccionDepartamento.'",
                                  "municipio": "'.$emisorDireccionMunicipio.'",
                                  "complemento": "'.$emisorDireccionComplemento.'"
                                },
                                "telefono": "'.$emisorTelefono.'",
                                "correo": "'.$emisorCorreo.'",
                                "codEstableMH": null,
                                "codEstable": null,
                                "codPuntoVentaMH": null,
                                "codPuntoVenta": null
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
                                "telefono": "'.$sPhoneNUmber.'",
                                "correo": "'.$email.'"
                                },
                            "otrosDocumentos": null,
                            "ventaTercero": null,
                            "cuerpoDocumento": [],
                            "resumen": {
                                                   "totalNoSuj": 0, 
                        "totalExenta": 0,
                        "totalGravada": '.strval($dSUmNeto).',
                        "subTotalVentas": '.strval($dSUmNeto).',
                        "descuNoSuj": 0,
                        "descuExenta": 0,
                        "descuGravada": 0,
                        "porcentajeDescuento": 0,
                        "totalDescu": 0,
                        "tributos": [{
                            "codigo": "20",
                            "descripcion": "Impuesto al Valor Agregado 13%",
                            "valor": '.strval($dIva).'
                        }],
                        "subTotal": '.strval($dSUmNeto).',
                        "ivaPerci1": 0,
                        "ivaRete1": 0,
                        "reteRenta": 0,
                                "montoTotalOperacion": '.strval($dTotal-$dSUmNetoExentas).',
                                "totalNoGravado": '.strval($dSUmNetoExentas).',
                                "totalPagar": '.strval($dTotal).',
                                "totalLetras": "'.$sLetras.' ",
                                "saldoFavor": 0,
                                "condicionOperacion": 1,
                                "pagos": [{
                                    "codigo": "01",
                                    "montoPago": '.strval($dTotal).',
                                    "plazo": null,
                                    "referencia": null,
                                    "periodo": null
                                }],
                                  "numPagoElectronico": null
                            },
        
                                "extension": {
                                    "nombEntrega": null,
                                    "docuEntrega": null,
                                    "nombRecibe": null,
                                    "docuRecibe": null,
                                    "observaciones": null,
                                    "placaVehiculo": null
                                },
                                "apendice": null
        
                            }
                           
                        }'; 
        
                    
                          // fin
                        // Agregar el detalle de productos al JSON existente
                        $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                        
    
                     //$sResult =    $this->processRequest($json_variable);
                    break;
        
                    ///consmidor
                    case 2:
                        $detalleProductos = [];
                        $dIva = 0.00;
                        $dSUmNeto = 0.00;
                        $dSUmNetoExentas = 0.00;

                        $dExentas = 0.00;
                        $dgravadas = 0.00;
                        $customerName = '';
                       
                        
                            // Obtener los productos relacionados con la venta
                            foreach ($lims_product_sale_data as $index => $product_sale) {
                                $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                         
                                // Verificar si el producto existe
                                if ($product) {
                                    $porcetajeimpuesto = 0.13;
                                    if ($product_sale->tax == 0)    
                                    {
                                        $dExentas =  $product_sale->total-$product_sale->tax;
                                        $dgravadas = 0.00;
                                        $porcetajeimpuesto = 0.00;
                                    }
                                    else{
                                     $dgravadas = round( $product_sale->total, 2);
                                        $dExentas = 0.00;
                                        $porcetajeimpuesto = 0.13;
                                    }
                            
                                    $detalleProducto = [
                                        "numItem" => $index + 1, // Número secuencial del item
                                        "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                        "numeroDocumento" => null,
                                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                        "codTributo" => null,
                                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                        "cantidad" => $cantidad, // Cantidad del producto
                                        "uniMedida" => 59, // Unidad de medida del producto
                                        "precioUni" => round($product_sale->net_unit_price + ($product_sale->net_unit_price *$porcetajeimpuesto),2), // Precio unitario del producto
                                        "montoDescu" => 0,
                                        "ventaNoSuj" => $dExentas,
                                        "ventaExenta" =>0,
                                        "ventaGravada" => $dgravadas , // Monto gravado del producto
                                        "tributos" => null,
                                        "ivaItem" => $product_sale->tax, // Código de tributos aplicados al producto
                                        "psv" => 0,
                                        "noGravado" => 0
                                    ];
                                    $dSUmNetoExentas += $dExentas;              
                                    $dIva += $product_sale->tax;
                                    $dSUmNeto += $dgravadas - $product_sale->tax;
                                    $detalleProductos[] = $detalleProducto;
                                }



                            }
                
                            $dTotal = $dIva + $dSUmNeto;

                            $sLetras = $this->numeroALetras($dTotal+$dSUmNetoExentas);
               
          
               
                
                       $json_variable='
                
                        {
                         
                            "nit": "'.$emisorNit.'",
                            "activo": "true",
                            "passwordPri": "'.$emisorprivatekey.'",
                                    "dteJson": {
                
                                    "identificacion": {
                                        "version": 1,
                                        "ambiente": "'.$emisorambiente.'",
                                        "tipoDte": "01",
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
                                    "documentoRelacionado": null,
                                    "emisor": {
                                        "nit": "'.$emisorNit.'",
                                        "nrc": "'.$emisorNrc.'",
                                        "nombre": "'.$emisorNombre.'",
                                        "codActividad": "'.$emisorCodActividad.'",
                                        "descActividad": "'.$emisorDescActividad.'",
                                        "nombreComercial": "'.$emisorNombreComercial.'",
                                        "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                        "direccion": {
                                          "departamento": "'.$emisorDireccionDepartamento.'",
                                          "municipio": "'.$emisorDireccionMunicipio.'",
                                          "complemento": "'.$emisorDireccionComplemento.'"
                                        },
                                        "telefono": "'.$emisorTelefono.'",
                                        "correo": "'.$emisorCorreo.'",
                                        "codEstableMH": null,
                                        "codEstable": null,
                                        "codPuntoVentaMH": null,
                                        "codPuntoVenta": null
                                    },
                                    "receptor": {
                                      
                                        "nrc": null,
                                        "nombre": "'.$nombre.'",
                                        "codActividad": "'.$code_giro.'",
                                        "descActividad": "'.$name_giro.'",
                                    "tipoDocumento": '.(empty($nit) ? 'null' : ($code_estado == "00" ? '"37"' : '"36"')).',
                                           "numDocumento": '.(empty($nit) ? 'null' : '"'.$nit.'"').',
                                        "direccion": {
                                            "departamento": "'.$code_estado.'",
                                            "municipio": "'.$code_muni.'",
                                            "complemento": "'.$address.'"
                                        },
                                        "telefono": "'.$sPhoneNUmber.'",
                                        "correo": "'.$email.'"
                                        },
                                    "otrosDocumentos": null,
                                    "ventaTercero": null,
                                    "cuerpoDocumento": [],
                                    "resumen": {
                                      "totalNoSuj": '.strval($dSUmNetoExentas).',
                                "totalExenta": 0,
                                "totalGravada": '.strval($dTotal).',
                                "subTotalVentas": '.strval($dTotal+$dSUmNetoExentas).',
                                "descuNoSuj": 0,
                                "descuExenta": 0,
                                "descuGravada": 0,
                                "porcentajeDescuento": 0,
                                "totalDescu": 0,
                                "tributos": null,
                                "subTotal": '.strval($dTotal+$dSUmNetoExentas).',
                          
                                "ivaRete1": 0,
                                "reteRenta": 0,
                                        "montoTotalOperacion": '.strval($dTotal-$dSUmNetoExentas).',
                                        "totalNoGravado": '.strval($dSUmNetoExentas).',
                                        "totalPagar": '.strval($dTotal).',
                                        "totalLetras": "'.$sLetras.' ",
                                        "saldoFavor": 0,
                                        "condicionOperacion": 1,
                                        "totalIva": '.strval($dIva).',
                                        "pagos": [{
                                            "codigo": "01",
                                            "montoPago": '.strval($dTotal).',
                                            "plazo": null,
                                            "referencia": null,
                                            "periodo": null
                                        }],
                                          "numPagoElectronico": null
                                    },
                
                                        "extension": {
                                            "nombEntrega": null,
                                            "docuEntrega": null,
                                            "nombRecibe": null,
                                            "docuRecibe": null,
                                            "observaciones": null,
                                            "placaVehiculo": null
                                        },
                                        "apendice": null
                
                                    }
                                   
                                }'; 
                
                            
                                  // fin
                                // Agregar el detalle de productos al JSON existente
                                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                             
                          
                             // echo($json_variable);
                            //   exit;
               
                    
                          //  $sResult =   $this->processRequest($json_variable);
                            break;
                                    ///factura exportacion
                            case 5:
                                $detalleProductos = [];
                                $dIva = 0.00;
                                $dSUmNeto = 0.00;
                                $customerName = '';
                                    // Obtener los productos relacionados con la venta
                                    foreach ($lims_product_sale_data as $index => $product_sale) {
                                        $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                        
                                        // Verificar si el producto existe
                                        if ($product) {
                        
                                            $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                                      
                                            
                                  
                                                $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                       
                                            $detalleProducto = [
                                                "numItem" => $index + 1, // Número secuencial del item
                                             
                                                "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                             
                                                "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                "cantidad" => $product_sale->qty, // Cantidad del producto
                                                "uniMedida" => 59, // Unidad de medida del producto
                                                "precioUni" => round( $product_sale->net_unit_price + ($product_sale->net_unit_price *0.13),2), // Precio unitario del producto
                                                "montoDescu" => 0,
                                             
                                                "ventaGravada" => $product_sale->total , // Monto gravado del producto
                                                "tributos" => null,
                                                "noGravado" => 0
                                            ];
                                            $dIva = $dIva + $product_sale->tax;
                    $dSUmNeto =  $product_sale->total -$product_sale->tax;
                
                                            $detalleProductos[] = $detalleProducto;
                                        }


                                    $dIva = number_format($dIva, 2 );
                                    $dSUmNeto = number_format($dSUmNeto);
                                    $dTotal += $dIva + $dSUmNeto;

                                    }
                        
                                    $sLetras = $this->numeroALetras($dTotal);
                        
                        
                        
                               $json_variable='
                        
                                {
                                            "nit": "06140203741144",
                                            "activo": "true",
                                            "passwordPri": "'.$emisorprivatekey.'",
                                            "dteJson": {
                        
                                            "identificacion": {
                                                "version": 1,
                                                "ambiente": "'.$emisorambiente.'",
                                                "tipoDte": "11",
                                                "numeroControl": "'.$numerocontroldte.'",
                        
                                                "codigoGeneracion": "'.$codigoGeneracion.'",
                                                "tipoModelo": 1,
                                                "tipoOperacion": 1,
                                                "tipoContingencia": null,
                                                "motivoContigencia": null,
                                                "fecEmi": "'.$fecEmi.'",
                                                "horEmi": "'.$horEmi.'",
                                                "tipoMoneda": "USD"
                        
                                            },
                                            "otrosDocumentos":null,
                                            "ventaTercero": null,
                                            "emisor": {
                                                "nit": "'.$emisorNit.'",
                                                "nrc": "'.$emisorNrc.'",
                                                "nombre": "'.$emisorNombre.'",
                                                "codActividad": "'.$emisorCodActividad.'",
                                                "descActividad": "'.$emisorDescActividad.'",
                                                "nombreComercial": "'.$emisorNombreComercial.'",
                                                "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                "direccion": {
                                                  "departamento": "'.$emisorDireccionDepartamento.'",
                                                  "municipio": "'.$emisorDireccionMunicipio.'",
                                                  "complemento": "'.$emisorDireccionComplemento.'"
                                                },
                                                "telefono": "'.$emisorTelefono.'",
                                                "correo": "'.$emisorCorreo.'",
                                                "codEstableMH": null,
                                                "codEstable": null,
                                                "codPuntoVentaMH": null,
                                                "codPuntoVenta": null,
                                                "regimen":null,
                                                "recintoFiscal": "01",
                                                "tipoItemExpor": 1
                                            },
                                            "receptor": {
                                                "nombre": "'.$customerName.'",
                                                "descActividad": "'.$name_giro.'",
                                                "nombreComercial": "'.$sCompany.'",
                                                "codPais": "'.$code_country.'",
                                                "nombrePais":"'.$name_country.'",
                                                "complemento":"'.$address.'",
                                                "tipoDocumento": "36",
                                                "numDocumento":"'.$nit.'",
                                                "tipoPersona":1,
                                                "telefono": "'.$sPhoneNUmber.'",
                                                "correo": "'.$email.'"
                                            },
                                            "otrosDocumentos": null,
                                            "ventaTercero": null,
                                            "cuerpoDocumento": [],
                                            "resumen": {
                                                "totalGravada": '.$dTotal.',
                                                "porcentajeDescuento": 0,
                                                "totalDescu": 0,
                                                "descuento":0,
                                                "codIncoterms": null,
                                                "descIncoterms": null,
                                                "flete":0,
                                                "seguro":0, 
                                                "montoTotalOperacion": '.$dTotal.',
                                                "totalNoGravado": 0,
                                                "totalPagar": '.$dTotal.',
                                                "totalLetras": "'.$sLetras.'",
                                              
                                                "condicionOperacion": 1,
                                                "pagos": [
                                                    {
                                                        "codigo": "01",
                                                        "montoPago": '.$dTotal.',
                                                        "plazo": "01",
                                                        "referencia": null,
                                                        "periodo": 0
                                                    }
                                                ],
                                                "observaciones": "observaciones",
                                                "numPagoElectronico": null
                                            },
                                            "apendice": null
                        
                                            }
                                           
                                        }'; 
                        
                                    
                                          // fin
                                        // Agregar el detalle de productos al JSON existente
                                        $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                        
                        
                                     // echo($json_variable);
                                    //   exit;
                     
                            
                                   // $sResult =     $this->processRequest($json_variable);
                                    break;
                                            /// nota de credito
                                    case 3:
                                        $detalleProductos = [];
                                        $dIva = 0.00;
                                        $dSUmNeto = 0.00;
                                        $customerName = '';
                                            // Obtener los productos relacionados con la venta
                                            foreach ($lims_product_sale_data as $index => $product_sale) {
                                                $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                                
                                                // Verificar si el producto existe
                                                if ($product) {
                                
                                                    $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                                              
                                                    
                                       
                                                        $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                                
                                            
                                                    $detalleProducto = [
                                                        "numItem" => $index + 1, // Número secuencial del item
                                                        "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                        "numeroDocumento" => $codgeneracion_anexo,
                                                       
                                                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                        "codTributo" => null,
                                                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                        "cantidad" => $product_sale->qty, // Cantidad del producto
                                                        "uniMedida" => 59, // Unidad de medida del producto
                                                        "precioUni" => $product_sale->net_unit_price , // Precio unitario del producto
                                                        "montoDescu" => 0,
                                                        "ventaNoSuj" => 0,
                                                        "ventaExenta" => 0,
                                                        "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                        "tributos" => [
                                                            "20"
                                                          ]
                                                    ];
                                                    $dIva = $dIva + $product_sale->tax;
                                                    $dSUmNeto =  $product_sale->total -$product_sale->tax;
                                                
                                            $detalleProductos[] = $detalleProducto;
                                                }

  $dIva = number_format($dIva, 2 );
                                            $dSUmNeto = number_format($dSUmNeto);
                                            $dTotal += $dIva + $dSUmNeto;

                                            }
                                
                                          
                                            $sLetras = $this->numeroALetras($dTotal);
                                
                                
                                
                                       $json_variable='
                                
                                        {
                                                    "nit": "06140203741144",
                                                    "activo": "true",
                                                    "passwordPri": "'.$emisorprivatekey.'",
                                                    "dteJson": {
                                
                                                    "identificacion": {
                                                        "version": 3,
                                                        "ambiente": "'.$emisorambiente.'",
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
                                                        "tipoDocumento": "'.$subtype.'",
                                                        "tipoGeneracion" :2, 
                                                        "numeroDocumento" : "'.$codgeneracion_anexo.'",
                                                        "fechaEmision":"'.$fecEmiAnex.'"
                                                        
                                                        }],
                                                    "emisor": {
                                                        "nit": "'.$emisorNit.'",
                                                        "nrc": "'.$emisorNrc.'",
                                                        "nombre": "'.$emisorNombre.'",
                                                        "codActividad": "'.$emisorCodActividad.'",
                                                        "descActividad": "'.$emisorDescActividad.'",
                                                        "nombreComercial": "'.$emisorNombreComercial.'",
                                                        "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
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
                                                        "nombre": "'.$customerName.'",
                                                        "codActividad": "'.$code_giro.'",
                                                        "descActividad": "'.$name_giro.'",
                                                        "nombreComercial": null,
                                                        "direccion": {
                                                            "departamento": "'.$code_estado.'",
                                                            "municipio": "'.$code_muni.'",
                                                            "complemento": "'.$address.'"
                                                        },
                                                        "telefono": "'.$email.'",
                                                        "correo": "'.$email.'"
                                                        },
                                       
                                                    "ventaTercero": null,
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
                                                        "totalLetras": "'.$sLetras.'",
                                                              "condicionOperacion": 2
                                                    },
                                                    "extension": {
                                                        "nombEntrega": "Francisco Orellana",
                                                        "docuEntrega": "08130203001010",
                                                        "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                        "docuRecibe": "06140203741144",
                                                        "observaciones": null
                                                    },
                                                    "apendice": null
                                
                                                    }
                                                   
                                                }'; 
                                
                                            
                                                  // fin
                                                // Agregar el detalle de productos al JSON existente
                                                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                                
                                
                                       //     echo($json_variable);
                                         //      exit;
                                 
                                 
                                        // $sResult =     $this->processRequest($json_variable);
                                            break;
        
                                            ///remision 
                                            case 4:
                                                $detalleProductos = [];
                                                $dIva = 0.00;
                                                $dSUmNeto = 0.00;
                                                $customerName = '';
                                                    // Obtener los productos relacionados con la venta
                                                    foreach ($lims_product_sale_data as $index => $product_sale) {
                                                        $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                                        
                                                        // Verificar si el producto existe
                                                        if ($product) {
                                        
                                                            $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                                                      
                                                            
                                                            // Verificar si la venta y el cliente existen
                                                     
                                                                $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                                      
        
                                                    if ($numeroControlAnexo == "NA")
                                                    {
                                                                $detalleProducto = [
                                                                "numItem" => $index + 1, // Número secuencial del item
                                                                "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                                "numeroDocumento" => null,
                                                               
                                                                "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                                "codTributo" => null,
                                                                "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                                "cantidad" => $product_sale->qty, // Cantidad del producto
                                                                "uniMedida" => 59, // Unidad de medida del producto
                                                                "precioUni" => $product_sale->net_unit_price , // Precio unitario del producto
                                                                "montoDescu" => 0,
                                                                "ventaNoSuj" => 0,
                                                                "ventaExenta" => 0,
                                                                "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                                "tributos" => [
                                                                    "20"
                                                                  ]
                                                            ]; 
                                                    }
                                                    else {
                                                        # code...
                                                        $detalleProducto = [
                                                            "numItem" => $index + 1, // Número secuencial del item
                                                            "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                            "numeroDocumento" => $codgeneracion_anexo,
                                                           
                                                            "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                            "codTributo" => null,
                                                            "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                            "cantidad" => $product_sale->qty, // Cantidad del producto
                                                            "uniMedida" => 59, // Unidad de medida del producto
                                                            "precioUni" => $product_sale->net_unit_price , // Precio unitario del producto
                                                            "montoDescu" => 0,
                                                            "ventaNoSuj" => 0,
                                                            "ventaExenta" => 0,
                                                            "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                            "tributos" => [
                                                                "20"
                                                              ]
                                                        ]; 
                                                    }
                                                   
                                                    $dIva = $dIva + $product_sale->tax;
                                                    $dSUmNeto =  $product_sale->total -$product_sale->tax;
                                                
                                                    $detalleProductos[] = $detalleProducto;
                                                        }

         $dIva = number_format($dIva, 2 );
                                                    $dSUmNeto = number_format($dSUmNeto);
                                                    $dTotal += $dIva + $dSUmNeto;

                                                    }
                                        
                                           
                                                    $sLetras = $this->numeroALetras($dTotal);
                                        
                                        if($numeroControlAnexo == "NA")
                                        {
                                            $json_variable='
                                        
                                            {
                                                        "nit": "06140203741144",
                                                        "activo": "true",
                                                        "passwordPri":"'.$emisorprivatekey.'",
                                                        "dteJson": {
                                    
                                                        "identificacion": {
                                                            "version": 3,
                                                            "ambiente": "'.$emisorambiente.'",
                                                            "tipoDte": "04",
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
                                                        "documentoRelacionado": null,
                                                        "emisor": {
                                                            "nit": "'.$emisorNit.'",
                                                            "nrc": "'.$emisorNrc.'",
                                                            "nombre": "'.$emisorNombre.'",
                                                            "codActividad": "'.$emisorCodActividad.'",
                                                            "descActividad": "'.$emisorDescActividad.'",
                                                            "nombreComercial": "'.$emisorNombreComercial.'",
                                                            "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                            "direccion": {
                                                            "departamento": "'.$emisorDireccionDepartamento.'",
                                                            "municipio": "'.$emisorDireccionMunicipio.'",
                                                            "complemento": "'.$emisorDireccionComplemento.'"
                                                            },
                                                            "telefono": "'.$emisorTelefono.'",
                                                            "correo": "'.$emisorCorreo.'",
                                                        "codEstableMH": null,
                                                "codEstable": null,
                                                "codPuntoVentaMH": null,
                                                "codPuntoVenta": null
                                                           
                                                        },
                                                        "receptor": {
                                                            
                                                            "nrc": "'.$nrc.'",
                                                            "tipoDocumento":"36",
                                                            "numDocumento":"'.$nit.'",
                                                            "nombre": "'.$customerName.'",
                                                            "codActividad": "'.$code_giro.'",
                                                            "descActividad": "'.$name_giro.'",
                                                            "nombreComercial": null,
                                                            "direccion": {
                                                                "departamento": "'.$code_estado.'",
                                                                "municipio": "'.$code_muni.'",
                                                                "complemento": "'.$address.'"
                                                            },
                                                            "telefono": "'.$sPhoneNUmber.'",
                                                            "correo": "'.$email.'",
                                                            
                                                            "bienTitulo": "04"
                                                            },
                                           
                                                        "ventaTercero": null,
                                                        "cuerpoDocumento": [],
                                                        "resumen": {
                                                            "totalNoSuj": 0,
                                                            "totalExenta": 0,
                                                            "totalGravada": '.$dSUmNeto.',
                                                            "subTotalVentas": '.$dSUmNeto.',
                                                            "descuNoSuj": 0,
                                                            "porcentajeDescuento": 0,
                                                            "descuExenta": 0,
                                                            "descuGravada": 0,
                                                            "totalDescu": 0,
                                                            "tributos": [{
                                                                "codigo": "20",
                                                                "descripcion": "Impuesto al Valor Agregado 13%",
                                                                "valor": '.$dIva.'
                                                            }],
                                                            "subTotal": '.$dSUmNeto.',
                                                            "montoTotalOperacion": '.$dTotal.',
                                                            "totalLetras": "DOS MIL DOSCIENTOS SESENTA DÓLARES "
                                                        },
                                                        "extension": {
                                                            "nombEntrega": "Francisco Orellana",
                                                            "docuEntrega": "08130203001010",
                                                            "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                            "docuRecibe": "06140203741144",
                                                            "observaciones": null
                                                        },
                                                        "apendice": null
                                    
                                                        }
                                                       
                                                    }'; 
                                    
        
                                        }
                                        else {
                                            $json_variable='
                                        
                                            {
                                                        "nit": "06140203741144",
                                                        "activo": "true",
                                                        "passwordPri": "'.$emisorprivatekey.'",
                                                        "dteJson": {
                                    
                                                        "identificacion": {
                                                            "version": 3,
                                                            "ambiente": "01",
                                                            "tipoDte": "04",
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
                                                            "tipoDocumento": "'.$subtype.'",
                                                            "tipoGeneracion" :2, 
                                                            "numeroDocumento" : "'.$codgeneracion_anexo.'",
                                                            "fechaEmision":"'.$fecEmiAnex.'"
                                                            
                                                            }],
                                                        "emisor": {
                                                            "nit": "'.$emisorNit.'",
                                                            "nrc": "'.$emisorNrc.'",
                                                            "nombre": "'.$emisorNombre.'",
                                                            "codActividad": "'.$emisorCodActividad.'",
                                                            "descActividad": "'.$emisorDescActividad.'",
                                                            "nombreComercial": "'.$emisorNombreComercial.'",
                                                            "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                            "direccion": {
                                                            "departamento": "'.$emisorDireccionDepartamento.'",
                                                            "municipio": "'.$emisorDireccionMunicipio.'",
                                                            "complemento": "'.$emisorDireccionComplemento.'"
                                                            },
                                                            "telefono": "'.$emisorTelefono.'",
                                                            "correo": "'.$emisorCorreo.'",
                                                        "codEstableMH": null,
                                                "codEstable": null,
                                                "codPuntoVentaMH": null,
                                                "codPuntoVenta": null
                                                           
                                                        },
                                                        "receptor": {
                                                            
                                                            "nrc": "'.$nrc.'",
                                                            "tipoDocumento":"36",
                                                            "numDocumento":"'.$nit.'",
                                                            "nombre": "'.$customerName.'",
                                                            "codActividad": "'.$code_giro.'",
                                                            "descActividad": "'.$name_giro.'",
                                                            "nombreComercial": null,
                                                            "direccion": {
                                                                "departamento": "'.$code_estado.'",
                                                                "municipio": "'.$code_muni.'",
                                                                "complemento": "'.$address.'"
                                                            },
                                                            "telefono": "'.$sPhoneNUmber.'",
                                                            "correo": "'.$email.'",
                                                            
                                                            "bienTitulo": "04"
                                                            },
                                           
                                                        "ventaTercero": null,
                                                        "cuerpoDocumento": [],
                                                        "resumen": {
                                                            "totalNoSuj": 0,
                                                            "totalExenta": 0,
                                                            "totalGravada": '.$dSUmNeto.',
                                                            "subTotalVentas": '.$dSUmNeto.',
                                                            "descuNoSuj": 0,
                                                            "porcentajeDescuento": 0,
                                                            "descuExenta": 0,
                                                            "descuGravada": 0,
                                                            "totalDescu": 0,
                                                            "tributos": [{
                                                                "codigo": "20",
                                                                "descripcion": "Impuesto al Valor Agregado 13%",
                                                                "valor": '.$dIva.'
                                                            }],
                                                            "subTotal": '.$dSUmNeto.',
                                                            "montoTotalOperacion": '.$dTotal.',
                                                            "totalLetras": "DOS MIL DOSCIENTOS SESENTA DÓLARES "
                                                        },
                                                        "extension": {
                                                            "nombEntrega": "Francisco Orellana",
                                                            "docuEntrega": "08130203001010",
                                                            "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                            "docuRecibe": "06140203741144",
                                                            "observaciones": null
                                                        },
                                                        "apendice": null
                                    
                                                        }
                                                       
                                                    }'; 
                                    
                                        }
                                        
                                            
                                                    
                                                          // fin
                                                        // Agregar el detalle de productos al JSON existente
                                                        $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                                        
                                        
                                                 //   echo($json_variable);
                                                   //    exit;
                                          
                                           
                                               $sResult =          $this->processRequest($json_variable);
                                                    break;
        
                    default:
                        // Código para casos que no coinciden con el valor especificado
                        // Aquí puedes colocar el código para manejar los casos no especificados
                        // ...
                        break;
                    }           
    
        $sello1 = $lims_sale_data->sello;
      //          $data = json_decode($json_variable);

                    // Acceder solo al dteJson
        //        $dteJson = $data->dteJson;
                    
                    // Volver a codificar con formato legible
          //      $pretty_json = json_encode($dteJson, JSON_PRETTY_PRINT);
            $pretty_json = $this->agregarSelloRecepcion($json_variable, $sello1);

                    echo $pretty_json;
                }
                catch (\Exception $e) {
                    // Manejo de la excepción
                    // Puedes registrar el error o devolver una respuesta con un mensaje de error
                    return response()->json(['error' => $e->getMessage()], 500);
                }
                 
           }
           function agregarSelloRecepcion($json_variable, $sello) {
            // Decodificar el JSON en un array asociativo
            $data = json_decode($json_variable, true); // true para array asociativo
        
            // Verificar si el JSON fue decodificado correctamente
            if (json_last_error() === JSON_ERROR_NONE) {
                // Asegurarse de que 'dteJson' exista
                if (isset($data['dteJson'])) {
                    // Añadir el campo "sello_recepcion" dentro de 'dteJson'
                    $data['dteJson']['sello_recepcion'] = $sello;
                    
                    // Codificar solo 'dteJson' en formato legible
                    return json_encode($data['dteJson'], JSON_PRETTY_PRINT);
                } else {
                    return "No se encontró 'dteJson' en la estructura JSON.";
                }
            } else {
                return "Error en la decodificación del JSON: " . json_last_error_msg();
            }
        }
        function agregarSelloRecepcionDATA($json_variable, $sello) {
            // Decodificar el JSON en un array asociativo
            $data = json_decode($json_variable, true); // true para array asociativo
        
            // Verificar si el JSON fue decodificado correctamente
            if (json_last_error() === JSON_ERROR_NONE) {
                // Asegurarse de que 'dteJson' exista
                if (isset($data['dteJson'])) {
                    // Añadir el campo "sello_recepcion" dentro de 'dteJson'
                    $data['dteJson']['sello_recepcion'] = $sello;
                    
                    // Codificar solo 'dteJson' en formato legible
                    return json_encode($data, JSON_PRETTY_PRINT);
                } else {
                    return "No se encontró 'dteJson' en la estructura JSON.";
                }
            } else {
                return "Error en la decodificación del JSON: " . json_last_error_msg();
            }
        }
  /*         public function enviarCorreoConImagenYTexto($destinatario, $asunto, $mensajeTexto, $json, $aqrl, $codgeneracion, $pdfBase64) {
            try {
                // Código de validación existente...
        
                $curl = curl_init();
        $destinatario = "iora2451@gmail.com";
                $data = [
                    'destinatario' => $destinatario,
                    'asunto' => $asunto,
                    'mensajeTexto' => $mensajeTexto,
                    'json' => $json,
                    'aqrl' => $aqrl,
                    'codgeneracion' => $codgeneracion,
                    'pdf' => $pdfBase64,  // Pasar el PDF en base64
                    'remitente' => 'frankdev@frankdeve.com' // Remitente
                ];

                curl_setopt_array($curl, [
                    CURLOPT_URL => 'http://frankdeve.com/correo/sending.php',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $data,
                ]);
        
                $response = curl_exec($curl);
        
                curl_close($curl);

        
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        }*/
        
// Claves de la API de Mailjet


// Método para enviar correo con imagen QR y PDF adjunto
function enviarCorreoConImagenYTexto($destinatario, $asunto, $mensajeTexto, $json, $aqrl, $codgeneracion, $pdfBase64) {
    try {
        
        $apiKey = 'a5056937712ce725980d3835ce2e3b15';
$apiSecret = '641be8e57cb0a32a2ba36865f90e2b9d';
        // Construcción del mensaje en HTML con QR
        $htmlContent = "
            <h3>Factura Electrónica</h3>
            <p>{$mensajeTexto}</p>
            <p>Código de generación: <strong>{$codgeneracion}</strong></p>
            <p>Adjunto encontrará la factura en formato PDF y json.</p>
      
        ";
        $jsonEncoded = base64_encode(json_encode(json_decode($json, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Estructura de datos del correo para Mailjet
        $data = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => 'facturacion@tramitesynegocios.com',
                        'Name' => 'Facturación'
                    ],
                    'To' => [
                        [
                            'Email' => $destinatario,
                            'Name' => 'Cliente'
                        ]
                    ],
                    'Subject' => $asunto,
                    'TextPart' => $mensajeTexto,
                    'HTMLPart' => $htmlContent,
                    'Attachments' => [
                        [
                            'ContentType' => 'application/pdf',
                            'Filename' => 'factura.pdf',
                            'Base64Content' => $pdfBase64
                        ],
                        [
                            'ContentType' => 'application/json',
                            'Filename' => 'factura.json',
                            'Base64Content' => $jsonEncoded
                        ]
                    ]
                ]
            ]
        ];

        // Inicializar cURL para enviar la solicitud a Mailjet
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.mailjet.com/v3.1/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");

        // Ejecutar la solicitud
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Cerrar cURL
        curl_close($ch);

        // Verificar la respuesta de Mailjet
      /*  if ($httpCode == 200) {
            echo "Correo enviado con éxito!";
        } else {
            echo "Error al enviar el correo. Código HTTP: $httpCode. Respuesta: $response";
        }*/

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
        
    
        public function getDteJson(Request $request) {
    
            $id = $request->input('id');
            $json_variable="";
            $lims_return_data = Sale::find($id);
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
        public function logError($message, $context = [])
        {
            Log::error($message, $context);
        }
public function genInvoiceDTE($id, $numerocontrolanexo)
{
    try {
        $numeroControlAnexo = $numerocontrolanexo;
        $lims_sale_data = Sale::find($id);
        if (!$lims_sale_data) {
            throw new \Exception("Sale not found");
        }

        $lims_product_sale_data = Product_Sale::where('sale_id', $id)->get();
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Customer::find($lims_sale_data->customer_id);

        /// codigo de generacion y numero de control 

        $bytes = random_bytes(16);
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

        $documentId = $lims_sale_data->document_id ?? 0;
        $numerocontroldtte= "";
                // Numero de control 
                $numeroControl = '000000000000000' . $lims_sale_data->reference_no; 
                $numeroControlFormateado = substr($numeroControl, -15);
                $subtype = "";
        switch ($documentId) {
            case 1:
            $numerocontroldtte= "DTE-03-00000000-".$numeroControlFormateado."";
            break;     
            case 2:
            $numerocontroldtte= "DTE-01-00000000-".$numeroControlFormateado."";
            break;
            case 4:
            $numerocontroldtte= "DTE-04-00000000-".$numeroControlFormateado."";
            break;  
            case 3:
            $numerocontroldtte= "DTE-05-00000000-".$numeroControlFormateado."";
            break;
           case 5:
            $numerocontroldtte= "DTE-11-00000000-".$numeroControlFormateado."";
            break;
            default:
            $numerocontroldtte= "DTE-00-00000000-".$numeroControlFormateado."";
           }
            




        if (!$lims_customer_data) {
            throw new \Exception("Customer not found");
        }

        $nit = $lims_customer_data->nit ?? 'N/A';
        $licitacion = $lims_sale_data->licitacion ?? 'N/A';
        $code_country = $lims_customer_data->countries1->code ?? 'N/A';
        $name_country = $lims_customer_data->countries1->name ?? 'N/A';
        $sCompany = $lims_customer_data->company_name ?? 'N/A';
        $sPhoneNUmber = $lims_customer_data->phone_number ?? 'N/A';
        $email = $lims_customer_data->email ?? 'N/A';
        $code_muni = $lims_customer_data->municipio->code ?? 'N/A';
        $code_estado = $lims_customer_data->estado->code ?? 'N/A';
        $nrc = $lims_customer_data->tax_no ?? 'N/A';
        $address = $lims_customer_data->address ?? 'N/A';
        $name_giro = $lims_customer_data->gire->name ?? 'N/A';
        $code_giro = $lims_customer_data->gire->code ?? 'N/A';

        $lims_payment_data = Payment::where('sale_id', $id)->get();
     




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

        $fecha = $lims_sale_data->created_at ?? now();
        $fecEmi = substr($fecha, 0, 10);
        $horEmi = substr($fecha, -8);

        // Leer el archivo JSON
        $jsonString = file_get_contents('app/Http/Controllers/company.json');
        $data = json_decode($jsonString);

        if (!$data || !isset($data->emisor)) {
            throw new \Exception("Invalid JSON structure in company.json");
        }

        // Acceder a los valores de los campos
        $emisorNit = $data->emisor->nit ?? 'N/A';
        $emisorNrc = $data->emisor->nrc ?? 'N/A';
        $emisorNombre = $data->emisor->nombre ?? 'N/A';
        $emisorCodActividad = $data->emisor->codActividad ?? 'N/A';
        $emisorDescActividad = $data->emisor->descActividad ?? 'N/A';
        $emisorNombreComercial = $data->emisor->nombreComercial ?? 'N/A';
        $emisorTipoEstablecimiento = $data->emisor->tipoEstablecimiento ?? 'N/A';
        $emisorDireccionDepartamento = $data->emisor->direccion->departamento ?? 'N/A';
        $emisorDireccionMunicipio = $data->emisor->direccion->municipio ?? 'N/A';
        $emisorDireccionComplemento = $data->emisor->direccion->complemento ?? 'N/A';
        $emisorTelefono = $data->emisor->telefono ?? 'N/A';
        $emisorCorreo = $data->emisor->correo ?? 'N/A';
        $emailauto = $data->emisor->emailauto ?? 'N/A';
        $emisorkey = $data->emisor->keypublica ?? 'N/A';
        $emisorprivatekey = $data->emisor->keyprivada ?? 'N/A';
        $emisorapi = $data->emisor->api ?? 'N/A';
        $emisorambiente = $data->emisor->ambiente ?? 'N/A';

        $terceros = $licitacion == "on" ? "{\"nit\":\"0614300994102\",\"nombre\":\"Bolsa de Productos de el salvador, s.a. de c.c.\"}" : "null";
        $dTotal = 0.00;
    switch ($documentId) {
        case 1:
        $detalleProductos = [];
        $dIva = 0.00;
        $dSUmNeto = 0.00;
        $dSUmNetoExentas = 0.00;

        $dExentas = 0.00;
        $dgravadas = 0.00;
        $customerName = '';
       
            // Obtener los productos relacionados con la venta
            foreach ($lims_product_sale_data as $index => $product_sale) {
                $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                
                // Verificar si el producto existe
                if ($product) {

                    $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
              
                    
                    // Verificar si la venta y el cliente existen
                    if ($sale && $sale->customer) {
                        $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                    }

//si tax es cero es exento 
$cantidad = $product_sale->qty;
if ($product_sale->tax == 0)    
{
    $dExentas =  $product_sale->total;
    $dgravadas = 0.00;
    $cantidad = 0 ;
}
else{
 $dgravadas = round( $product_sale->total -$product_sale->tax, 2);
    $dExentas = 0.00;
}

$tributos = [20];
if ($product_sale->tax == 0) {
 $tributos = null;
 }
 else{
     $tributos = ["20"];
 }
                    $detalleProducto = [
                        "numItem" => $index + 1, // Número secuencial del item
                        "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                        "numeroDocumento" => null,
                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                        "codTributo" => null,
                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                        "cantidad" => $cantidad, // Cantidad del producto
                        "uniMedida" => 59, // Unidad de medida del producto
                        "precioUni" => round($product_sale->net_unit_price,2), // Precio unitario del producto
                        "montoDescu" => 0,
                        "ventaNoSuj" => 0,
                        "ventaExenta" =>  0,
                        "ventaGravada" => $dgravadas, // Monto gravado del producto
                        "tributos" => $tributos, // Código de tributos aplicados al producto
                        "psv" => 0,
                        "noGravado" => $dExentas
                    ];
                // Acumular los valores
                $dSUmNetoExentas += $dExentas;
                $dIva += $product_sale->tax;
                $dSUmNeto += $dgravadas;
                $detalleProductos[] = $detalleProducto;
                }

          
            }
  // Calcular el total
                        $dTotal = $dIva + $dSUmNeto+ $dSUmNetoExentas;

                         $sLetras = $this->numeroALetras($dTotal);
            
       


       $json_variable='

        {
                    "nit": "'.$emisorNit.'",
                    "activo": "true",
                    "passwordPri": "'.$emisorprivatekey.'",
                    "dteJson": {

                    "identificacion": {
                        "version": 3,
                        "ambiente": "'.$emisorambiente.'",
                        "tipoDte": "03",
                        "numeroControl": "DTE-03-00000000-'.$numeroControlFormateado.'",

                        "codigoGeneracion": "'.strtoupper($uuid).'",
                        "tipoModelo": 1,
                        "tipoOperacion": 1,
                        "tipoContingencia": null,
                        "motivoContin": null,
                        "fecEmi": "'.$fecEmi.'",
                        "horEmi": "'.$horEmi.'",
                        "tipoMoneda": "USD"

                    },
                    "documentoRelacionado": null,
                    "emisor": {
                        "nit": "'.$emisorNit.'",
                        "nrc": "'.$emisorNrc.'",
                        "nombre": "'.$emisorNombre.'",
                        "codActividad": "'.$emisorCodActividad.'",
                        "descActividad": "'.$emisorDescActividad.'",
                        "nombreComercial": "'.$emisorNombreComercial.'",
                        "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                        "direccion": {
                          "departamento": "'.$emisorDireccionDepartamento.'",
                          "municipio": "'.$emisorDireccionMunicipio.'",
                          "complemento": "'.$emisorDireccionComplemento.'"
                        },
                        "telefono": "'.$emisorTelefono.'",
                        "correo": "'.$emisorCorreo.'",
                        "codEstableMH": null,
                        "codEstable": null,
                        "codPuntoVentaMH": null,
                        "codPuntoVenta": null
                    },
                    "receptor": {
                        "nit": "'.$nit.'",
                        "nrc": "'.$nrc.'",
                        "nombre": "'.$customerName.'",
                        "codActividad": "'.$code_giro.'",
                        "descActividad": "'.$name_giro.'",
                        "nombreComercial": "'.$sCompany.'",
                        "direccion": {
                            "departamento": "'.$code_estado.'",
                            "municipio": "'.$code_muni.'",
                            "complemento": "'.$address.'"
                        },
                        "telefono": "'.$sPhoneNUmber.'",
                        "correo": "'.$email.'"
                        },
                    "otrosDocumentos": null,
                    "ventaTercero": '.$terceros.',
                    "cuerpoDocumento": [],
                    "resumen": {
                        "totalNoSuj":0,
                        "totalExenta": 0,
                        "totalGravada": '.strval($dSUmNeto).',
                        "subTotalVentas": '.strval($dSUmNeto).',
                        "descuNoSuj": 0,
                        "descuExenta": 0,
                        "descuGravada": 0,
                        "porcentajeDescuento": 0,
                        "totalDescu": 0,
                        "tributos": [{
                            "codigo": "20",
                            "descripcion": "Impuesto al Valor Agregado 13%",
                            "valor": '.strval($dIva).'
                        }],
                        "subTotal": '.strval($dSUmNeto).',
                        "ivaPerci1": 0,
                        "ivaRete1": 0,
                        "reteRenta": 0,
                        "montoTotalOperacion": '.strval($dTotal-$dSUmNetoExentas).',
                        "totalNoGravado":  '.strval($dSUmNetoExentas).',
                        "totalPagar": '.strval($dTotal).',
                        "totalLetras": "'.$sLetras.' ",
                        "saldoFavor": 0,
                        "condicionOperacion": 1,
                        "pagos": [{
                            "codigo": "01",
                            "montoPago": '.strval($dTotal).',
                            "plazo": null,
                            "referencia": null,
                            "periodo": null
                        }],
                          "numPagoElectronico": null
                    },

                        "extension": {
                            "nombEntrega": null,
                            "docuEntrega": null,
                            "nombRecibe": null,
                            "docuRecibe": null,
                            "observaciones": null,
                            "placaVehiculo": null
                        },
                        "apendice": null

                    }
                   
                }'; 

            
                  // fin
                // Agregar el detalle de productos al JSON existente
                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                

          
             $sResult =    $this->processRequest($json_variable);
            break;

            ///consmidor
            case 2:
                $detalleProductos = [];
                $dIva = 0.00;
                $dSUmNeto = 0.00;
                $dSUmNetoExentas = 0.00;

                $dExentas = 0.00;
                $dgravadas = 0.00;
                $customerName = '';
                    // Obtener los productos relacionados con la venta
                    foreach ($lims_product_sale_data as $index => $product_sale) {
                        $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                        
                        // Verificar si el producto existe
                        if ($product) {
        
                            $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                      
                            
                            // Verificar si la venta y el cliente existen
                            if ($sale && $sale->customer) {
                                $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                            }
                    //si tax es cero es exento 
                    $porcetajeimpuesto = 0.13;
                    $cantidad = $product_sale->qty;
                        if ($product_sale->tax == 0)    
                        {
                            $dExentas =  $product_sale->total-$product_sale->tax;
                            $dgravadas = 0.00;
                            $porcetajeimpuesto = 0.00;
                            $cantidad = 0 ;
                        }
                        else{
                        $dgravadas = round( $product_sale->total, 2);
                            $dExentas = 0.00;
                            $porcetajeimpuesto = 0.13;
                        }

                            $detalleProducto = [
                                "numItem" => $index + 1, // Número secuencial del item
                                "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                "numeroDocumento" => null,
                                "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                "codTributo" => null,
                                "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                "cantidad" => $cantidad, // Cantidad del producto
                                "uniMedida" => 59, // Unidad de medida del producto
                                "precioUni" => round($product_sale->net_unit_price + ($product_sale->net_unit_price *$porcetajeimpuesto),2), // Precio unitario del producto
                                "montoDescu" => 0,
                                "ventaNoSuj" => 0,
                                "ventaExenta" =>0,
                                "ventaGravada" => $dgravadas , // Monto gravado del producto
                                "tributos" => null,
                                "ivaItem" => $product_sale->tax, // Código de tributos aplicados al producto
                                "psv" => 0,
                                "noGravado" => $dExentas
                            ];
                                // Acumular los valores

                                $dSUmNetoExentas += $dExentas;              
                $dIva += $product_sale->tax;
                $dSUmNeto += $dgravadas - $product_sale->tax;
                $detalleProductos[] = $detalleProducto;
                }

          
            }
  // Calcular el total
                        $dTotal = $dIva + $dSUmNeto;

                         $sLetras = $this->numeroALetras($dTotal+$dSUmNetoExentas);
            
       



        
               $json_variable='
        
                {
                 
                    "nit": "'.$emisorNit.'",
                    "activo": "true",
                    "passwordPri": "'.$emisorprivatekey.'",
                            "dteJson": {
        
                            "identificacion": {
                                "version": 1,
                                "ambiente": "'.$emisorambiente.'",
                                "tipoDte": "01",
                                "numeroControl": "DTE-01-00000000-'.$numeroControlFormateado.'",
        
                                "codigoGeneracion": "'.strtoupper($uuid).'",
                                "tipoModelo": 1,
                                "tipoOperacion": 1,
                                "tipoContingencia": null,
                                "motivoContin": null,
                                "fecEmi": "'.$fecEmi.'",
                                "horEmi": "'.$horEmi.'",
                                "tipoMoneda": "USD"
        
                            },
                            "documentoRelacionado": null,
                            "emisor": {
                                "nit": "'.$emisorNit.'",
                                "nrc": "'.$emisorNrc.'",
                                "nombre": "'.$emisorNombre.'",
                                "codActividad": "'.$emisorCodActividad.'",
                                "descActividad": "'.$emisorDescActividad.'",
                                "nombreComercial": "'.$emisorNombreComercial.'",
                                "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                "direccion": {
                                  "departamento": "'.$emisorDireccionDepartamento.'",
                                  "municipio": "'.$emisorDireccionMunicipio.'",
                                  "complemento": "'.$emisorDireccionComplemento.'"
                                },
                                "telefono": "'.$emisorTelefono.'",
                                "correo": "'.$emisorCorreo.'",
                                "codEstableMH": null,
                                "codEstable": null,
                                "codPuntoVentaMH": null,
                                "codPuntoVenta": null
                            },
                            "receptor": {
                              
                                "nrc": null,
                                "nombre": "'.$customerName.'",
                                "codActividad": "'.$code_giro.'",
                                "descActividad": "'.$name_giro.'",
                               "tipoDocumento": '.(empty($nit) ? 'null' : ($code_estado == "00" ? '"37"' : '"36"')).',
                                           "numDocumento": '.(empty($nit) ? 'null' : '"'.$nit.'"').',
                                "direccion": {
                                    "departamento": "'.$code_estado.'",
                                    "municipio": "'.$code_muni.'",
                                    "complemento": "'.$address.'"
                                },
                                "telefono": "'.$sPhoneNUmber.'",
                                "correo": "'.$email.'"
                                },
                            "otrosDocumentos": null,
                            "ventaTercero": '.$terceros.',
                            "cuerpoDocumento": [],
                            "resumen": {
                                "totalNoSuj": 0,
                                "totalExenta": 0,
                                "totalGravada": '.strval($dTotal).',
                                "subTotalVentas": '.strval($dTotal).',
                                "descuNoSuj": 0,
                                "descuExenta": 0,
                                "descuGravada": 0,
                                "porcentajeDescuento": 0,
                                "totalDescu": 0,
                                "tributos": null,
                                "subTotal": '.strval($dTotal).',
                          
                                "ivaRete1": 0,
                                "reteRenta": 0,
                                "montoTotalOperacion": '.strval($dTotal).',
                                "totalNoGravado": '.strval($dSUmNetoExentas).',
                                "totalPagar": '.strval($dTotal+$dSUmNetoExentas).',
                                "totalLetras": "'.$sLetras.' ",
                                "saldoFavor": 0,
                                "condicionOperacion": 1,
                                "totalIva": '.strval($dIva).',
                                "pagos": [{
                                    "codigo": "01",
                                    "montoPago": '.strval($dTotal+$dSUmNetoExentas).',
                                    "plazo": null,
                                    "referencia": null,
                                    "periodo": null
                                }],
                                  "numPagoElectronico": null
                            },
        
                                "extension": {
                                    "nombEntrega": null,
                                    "docuEntrega": null,
                                    "nombRecibe": null,
                                    "docuRecibe": null,
                                    "observaciones": null,
                                    "placaVehiculo": null
                                },
                                "apendice": null
        
                            }
                           
                        }'; 
        
                    
                          // fin
                        // Agregar el detalle de productos al JSON existente
                        $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                        
        
                     // echo($json_variable);
                    //   exit;
                    $sResult =   $this->processRequest($json_variable);
                    break;
                            ///factura exportacion
                    case 5:
                        $detalleProductos = [];
                        $dIva = 0.00;
                        $dSUmNeto = 0.00;
                        $customerName = '';
                            // Obtener los productos relacionados con la venta
                            foreach ($lims_product_sale_data as $index => $product_sale) {
                                $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                
                                // Verificar si el producto existe
                                if ($product) {
                
                                    $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                              
                                    
                                    // Verificar si la venta y el cliente existen
                                    if ($sale && $sale->customer) {
                                        $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                    }
                            
                                    $detalleProducto = [
                                        "numItem" => $index + 1, // Número secuencial del item
                                     
                                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                     
                                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                        "cantidad" => $product_sale->qty, // Cantidad del producto
                                        "uniMedida" => 59, // Unidad de medida del producto
                                        "precioUni" => round($product_sale->net_unit_price + ($product_sale->net_unit_price *0.13),2), // Precio unitario del producto
                                        "montoDescu" => 0,
                                     
                                        "ventaGravada" => $product_sale->total , // Monto gravado del producto
                                        "tributos" => null,
                                        "noGravado" => 0
                                    ];
                                      // Acumular los valores
                $dIva += $product_sale->tax;
                $dSUmNeto += $product_sale->total - $product_sale->tax;
                $detalleProductos[] = $detalleProducto;
                }

          
            }
                             // Calcular el total
                        $dTotal = $dIva + $dSUmNeto;

                         $sLetras = $this->numeroALetras($dTotal);
            
       


                
                
                
                       $json_variable='
                
                        {
                                    "nit":  "'.$emisorNit.'",
                                    "activo": "true",
                                    "passwordPri": "'.$emisorprivatekey.'",
                                    "dteJson": {
                
                                    "identificacion": {
                                        "version": 1,
                                        "ambiente": "'.$emisorambiente.'",
                                        "tipoDte": "11",
                                        "numeroControl": "DTE-11-00000000-'.$numeroControlFormateado.'",
                
                                        "codigoGeneracion": "'.strtoupper($uuid).'",
                                        "tipoModelo": 1,
                                        "tipoOperacion": 1,
                                        "tipoContingencia": null,
                                        "motivoContigencia": null,
                                        "fecEmi": "'.$fecEmi.'",
                                        "horEmi": "'.$horEmi.'",
                                        "tipoMoneda": "USD"
                
                                    },
                                    "otrosDocumentos":null,
                                    "ventaTercero": null,
                                    "emisor": {
                                        "nit": "'.$emisorNit.'",
                                        "nrc": "'.$emisorNrc.'",
                                        "nombre": "'.$emisorNombre.'",
                                        "codActividad": "'.$emisorCodActividad.'",
                                        "descActividad": "'.$emisorDescActividad.'",
                                        "nombreComercial": "'.$emisorNombreComercial.'",
                                        "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                        "direccion": {
                                          "departamento": "'.$emisorDireccionDepartamento.'",
                                          "municipio": "'.$emisorDireccionMunicipio.'",
                                          "complemento": "'.$emisorDireccionComplemento.'"
                                        },
                                        "telefono": "'.$emisorTelefono.'",
                                        "correo": "'.$emisorCorreo.'",
                                        "codEstableMH": null,
                                        "codEstable": null,
                                        "codPuntoVentaMH": null,
                                        "codPuntoVenta": null,
                                        "regimen":null,
                                        "recintoFiscal": "01",
                                        "tipoItemExpor": 1
                                    },
                                    "receptor": {
                                        "nombre": "'.$customerName.'",
                                        "descActividad": "'.$name_giro.'",
                                        "nombreComercial": "'.$sCompany.'",
                                        "codPais": "'.$code_country.'",
                                        "nombrePais":"'.$name_country.'",
                                        "complemento":"'.$address.'",
                                        "tipoDocumento": "36",
                                        "numDocumento":"'.$nit.'",
                                        "tipoPersona":1,
                                        "telefono": "'.$sPhoneNUmber.'",
                                        "correo": "'.$email.'"
                                    },
                                    "otrosDocumentos": null,
                                    "ventaTercero": null,
                                    "cuerpoDocumento": [],
                                    "resumen": {
                                        "totalGravada": '.$dTotal.',
                                        "porcentajeDescuento": 0,
                                        "totalDescu": 0,
                                        "descuento":0,
                                        "codIncoterms": null,
                                        "descIncoterms": null,
                                        "flete":0,
                                        "seguro":0, 
                                        "montoTotalOperacion": '.$dTotal.',
                                        "totalNoGravado": 0,
                                        "totalPagar": '.$dTotal.',
                                        "totalLetras": "'.$sLetras.'",
                                      
                                        "condicionOperacion": 1,
                                        "pagos": [
                                            {
                                                "codigo": "01",
                                                "montoPago": '.$dTotal.',
                                                "plazo": "01",
                                                "referencia": null,
                                                "periodo": 0
                                            }
                                        ],
                                        "observaciones": "observaciones",
                                        "numPagoElectronico": null
                                    },
                                    "apendice": null
                
                                    }
                                   
                                }'; 
                
                            
                                  // fin
                                // Agregar el detalle de productos al JSON existente
                                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                
                
                             // echo($json_variable);
                            //   exit;
                            $sResult =     $this->processRequest($json_variable);
                            break;
                                    /// nota de credito
                            case 3:
                                $detalleProductos = [];
                                $dIva = 0.00;
                                $dSUmNeto = 0.00;
                                $customerName = '';
                                    // Obtener los productos relacionados con la venta
                                    foreach ($lims_product_sale_data as $index => $product_sale) {
                                        $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                        
                                        // Verificar si el producto existe
                                        if ($product) {
                        
                                            $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                                      
                                            
                                            // Verificar si la venta y el cliente existen
                                            if ($sale && $sale->customer) {
                                                $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                            }
                                    
                                            $detalleProducto = [
                                                "numItem" => $index + 1, // Número secuencial del item
                                                "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                "numeroDocumento" => $codgeneracion_anexo,
                                               
                                                "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                "codTributo" => null,
                                                "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                "cantidad" => $product_sale->qty, // Cantidad del producto
                                                "uniMedida" => 59, // Unidad de medida del producto
                                                "precioUni" => $product_sale->net_unit_price , // Precio unitario del producto
                                                "montoDescu" => 0,
                                                "ventaNoSuj" => 0,
                                                "ventaExenta" => 0,
                                                "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                "tributos" => [
                                                    "20"
                                                  ]
                                            ];
                                            $dIva += $product_sale->tax;
                                            $dSUmNeto += $product_sale->total - $product_sale->tax;
                                        
                                    $detalleProductos[] = $detalleProducto;
                                        }


                                        $dTotal = $dIva + $dSUmNeto;
                                    $dTotal += $dIva + $dSUmNeto;

                                    }
                        
                                   
                                    $sLetras = $this->numeroALetras($dTotal);
                        
                        
                        
                               $json_variable='
                        
                                {
                                            "nit":  "'.$emisorNit.'",
                                            "activo": "true",
                                            "passwordPri": "'.$emisorprivatekey.'",
                                            "dteJson": {
                        
                                            "identificacion": {
                                                "version": 3,
                                                "ambiente": "'.$emisorambiente.'",
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
                                                "tipoDocumento": "'.$subtype.'",
                                                "tipoGeneracion" :2, 
                                                "numeroDocumento" : "'.$codgeneracion_anexo.'",
                                                "fechaEmision":"'.$fecEmiAnex.'"
                                                
                                                }],
                                            "emisor": {
                                                "nit": "'.$emisorNit.'",
                                                "nrc": "'.$emisorNrc.'",
                                                "nombre": "'.$emisorNombre.'",
                                                "codActividad": "'.$emisorCodActividad.'",
                                                "descActividad": "'.$emisorDescActividad.'",
                                                "nombreComercial": "'.$emisorNombreComercial.'",
                                                "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
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
                                                "nombre": "'.$customerName.'",
                                                "codActividad": "'.$code_giro.'",
                                                "descActividad": "'.$name_giro.'",
                                                "nombreComercial": null,
                                                "direccion": {
                                                    "departamento": "'.$code_estado.'",
                                                    "municipio": "'.$code_muni.'",
                                                    "complemento": "'.$address.'"
                                                },
                                                "telefono": "'.$email.'",
                                                "correo": "'.$email.'"
                                                },
                               
                                            "ventaTercero": null,
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
                                                "totalLetras": "'.$sLetras.'",
                                                      "condicionOperacion": 2
                                            },
                                            "extension": {
                                                "nombEntrega": "Francisco Orellana",
                                                "docuEntrega": "08130203001010",
                                                "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                "docuRecibe": "06140203741144",
                                                "observaciones": null
                                            },
                                            "apendice": null
                        
                                            }
                                           
                                        }'; 
                        
                                    
                                          // fin
                                        // Agregar el detalle de productos al JSON existente
                                        $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                        
                        
                               //     echo($json_variable);
                                 //      exit;
                                 $sResult =     $this->processRequest($json_variable);
                                    break;

                                    ///remision 
                                    case 4:
                                        $detalleProductos = [];
                                        $dIva = 0.00;
                                        $dSUmNeto = 0.00;
                                        $customerName = '';
                                            // Obtener los productos relacionados con la venta
                                            foreach ($lims_product_sale_data as $index => $product_sale) {
                                                $product = Product::find($product_sale->product_id); // Obtener el producto correspondiente
                                                
                                                // Verificar si el producto existe
                                                if ($product) {
                                
                                                    $sale = Sale::find($product_sale->sale_id); // Obtener la venta correspondiente
                                              
                                                    
                                                    // Verificar si la venta y el cliente existen
                                                    if ($sale && $sale->customer) {
                                                        $customerName = $sale->customer->name; // Obtener el nombre del cliente de la clase Customer
                                                    }

                                            if ($numeroControlAnexo == "NA")
                                            {
                                                        $detalleProducto = [
                                                        "numItem" => $index + 1, // Número secuencial del item
                                                        "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                        "numeroDocumento" => null,
                                                       
                                                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                        "codTributo" => null,
                                                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                        "cantidad" => $product_sale->qty, // Cantidad del producto
                                                        "uniMedida" => 59, // Unidad de medida del producto
                                                        "precioUni" => $product_sale->net_unit_price , // Precio unitario del producto
                                                        "montoDescu" => 0,
                                                        "ventaNoSuj" => 0,
                                                        "ventaExenta" => 0,
                                                        "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                        "tributos" => [
                                                            "20"
                                                          ]
                                                    ]; 
                                            }
                                            else {
                                                # code...
                                                $detalleProducto = [
                                                    "numItem" => $index + 1, // Número secuencial del item
                                                    "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)
                                                    "numeroDocumento" => $codgeneracion_anexo,
                                                   
                                                    "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                                    "codTributo" => null,
                                                    "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                                    "cantidad" => $product_sale->qty, // Cantidad del producto
                                                    "uniMedida" => 59, // Unidad de medida del producto
                                                    "precioUni" => $product_sale->net_unit_price , // Precio unitario del producto
                                                    "montoDescu" => 0,
                                                    "ventaNoSuj" => 0,
                                                    "ventaExenta" => 0,
                                                    "ventaGravada" => $product_sale->net_unit_price * $product_sale->qty, // Monto gravado del producto
                                                    "tributos" => [
                                                        "20"
                                                      ]
                                                ]; 
                                            }
                                           
                                            $dIva += $product_sale->tax;
                                            $dSUmNeto += $product_sale->total - $product_sale->tax;
                                        
                                            $detalleProductos[] = $detalleProducto;
                                                }

                                  

                                            }
                                
                                            $dTotal = $dIva + $dSUmNeto;

                                            $sLetras = $this->numeroALetras($dTotal);
                               
                                
                                if($numeroControlAnexo == "NA")
                                {
                                    $json_variable='
                                
                                    {
                                                "nit":  "'.$emisorNit.'",
                                                "activo": "true",
                                                "passwordPri": "'.$emisorprivatekey.'",
                                                "dteJson": {
                            
                                                "identificacion": {
                                                    "version": 3,
                                                    "ambiente": "'.$emisorambiente.'",
                                                    "tipoDte": "04",
                                                    "numeroControl": "DTE-04-00000000-'.$numeroControlFormateado.'",
                            
                                                    "codigoGeneracion": "'.strtoupper($uuid).'",
                                                    "tipoModelo": 1,
                                                    "tipoOperacion": 1,
                                                    "tipoContingencia": null,
                                                    "motivoContin": null,
                                                    "fecEmi": "'.$fecEmi.'",
                                                    "horEmi": "'.$horEmi.'",
                                                    "tipoMoneda": "USD"
                            
                                                },
                                                "documentoRelacionado": null,
                                                "emisor": {
                                                    "nit": "'.$emisorNit.'",
                                                    "nrc": "'.$emisorNrc.'",
                                                    "nombre": "'.$emisorNombre.'",
                                                    "codActividad": "'.$emisorCodActividad.'",
                                                    "descActividad": "'.$emisorDescActividad.'",
                                                    "nombreComercial": "'.$emisorNombreComercial.'",
                                                    "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                    "direccion": {
                                                    "departamento": "'.$emisorDireccionDepartamento.'",
                                                    "municipio": "'.$emisorDireccionMunicipio.'",
                                                    "complemento": "'.$emisorDireccionComplemento.'"
                                                    },
                                                    "telefono": "'.$emisorTelefono.'",
                                                    "correo": "'.$emisorCorreo.'",
                                                "codEstableMH": null,
                                        "codEstable": null,
                                        "codPuntoVentaMH": null,
                                        "codPuntoVenta": null
                                                   
                                                },
                                                "receptor": {
                                                    
                                                    "nrc": "'.$nrc.'",
                                                    "tipoDocumento":"36",
                                                    "numDocumento":"'.$nit.'",
                                                    "nombre": "'.$customerName.'",
                                                    "codActividad": "'.$code_giro.'",
                                                    "descActividad": "'.$name_giro.'",
                                                    "nombreComercial": null,
                                                    "direccion": {
                                                        "departamento": "'.$code_estado.'",
                                                        "municipio": "'.$code_muni.'",
                                                        "complemento": "'.$address.'"
                                                    },
                                                    "telefono": "'.$sPhoneNUmber.'",
                                                    "correo": "'.$email.'",
                                                    
                                                    "bienTitulo": "04"
                                                    },
                                   
                                                "ventaTercero": null,
                                                "cuerpoDocumento": [],
                                                "resumen": {
                                                    "totalNoSuj": 0,
                                                    "totalExenta": 0,
                                                    "totalGravada": '.$dSUmNeto.',
                                                    "subTotalVentas": '.$dSUmNeto.',
                                                    "descuNoSuj": 0,
                                                    "porcentajeDescuento": 0,
                                                    "descuExenta": 0,
                                                    "descuGravada": 0,
                                                    "totalDescu": 0,
                                                    "tributos": [{
                                                        "codigo": "20",
                                                        "descripcion": "Impuesto al Valor Agregado 13%",
                                                        "valor": '.$dIva.'
                                                    }],
                                                    "subTotal": '.$dSUmNeto.',
                                                    "montoTotalOperacion": '.$dTotal.',
                                                    "totalLetras": "DOS MIL DOSCIENTOS SESENTA DÓLARES "
                                                },
                                                "extension": {
                                                    "nombEntrega": "Francisco Orellana",
                                                    "docuEntrega": "08130203001010",
                                                    "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                    "docuRecibe": "06140203741144",
                                                    "observaciones": null
                                                },
                                                "apendice": null
                            
                                                }
                                               
                                            }'; 
                            

                                }
                                else {
                                    $json_variable='
                                
                                    {
                                                "nit":  "'.$emisorNit.'",
                                                "activo": "true",
                                                "passwordPri": "'.$emisorprivatekey.'",
                                                "dteJson": {
                            
                                                "identificacion": {
                                                    "version": 3,
                                                    "ambiente": "'.$emisorambiente.'",
                                                    "tipoDte": "04",
                                                    "numeroControl": "DTE-04-00000000-'.$numeroControlFormateado.'",
                            
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
                                                    "tipoDocumento": "'.$subtype.'",
                                                    "tipoGeneracion" :2, 
                                                    "numeroDocumento" : "'.$codgeneracion_anexo.'",
                                                    "fechaEmision":"'.$fecEmiAnex.'"
                                                    
                                                    }],
                                                "emisor": {
                                                    "nit": "'.$emisorNit.'",
                                                    "nrc": "'.$emisorNrc.'",
                                                    "nombre": "'.$emisorNombre.'",
                                                    "codActividad": "'.$emisorCodActividad.'",
                                                    "descActividad": "'.$emisorDescActividad.'",
                                                    "nombreComercial": "'.$emisorNombreComercial.'",
                                                    "tipoEstablecimiento": "'.$emisorTipoEstablecimiento.'",
                                                    "direccion": {
                                                    "departamento": "'.$emisorDireccionDepartamento.'",
                                                    "municipio": "'.$emisorDireccionMunicipio.'",
                                                    "complemento": "'.$emisorDireccionComplemento.'"
                                                    },
                                                    "telefono": "'.$emisorTelefono.'",
                                                    "correo": "'.$emisorCorreo.'",
                                                "codEstableMH": null,
                                        "codEstable": null,
                                        "codPuntoVentaMH": null,
                                        "codPuntoVenta": null
                                                   
                                                },
                                                "receptor": {
                                                    
                                                    "nrc": "'.$nrc.'",
                                                    "tipoDocumento":"36",
                                                    "numDocumento":"'.$nit.'",
                                                    "nombre": "'.$customerName.'",
                                                    "codActividad": "'.$code_giro.'",
                                                    "descActividad": "'.$name_giro.'",
                                                    "nombreComercial": null,
                                                    "direccion": {
                                                        "departamento": "'.$code_estado.'",
                                                        "municipio": "'.$code_muni.'",
                                                        "complemento": "'.$address.'"
                                                    },
                                                    "telefono": "'.$sPhoneNUmber.'",
                                                    "correo": "'.$email.'",
                                                    
                                                    "bienTitulo": "04"
                                                    },
                                   
                                                "ventaTercero": null,
                                                "cuerpoDocumento": [],
                                                "resumen": {
                                                    "totalNoSuj": 0,
                                                    "totalExenta": 0,
                                                    "totalGravada": '.$dSUmNeto.',
                                                    "subTotalVentas": '.$dSUmNeto.',
                                                    "descuNoSuj": 0,
                                                    "porcentajeDescuento": 0,
                                                    "descuExenta": 0,
                                                    "descuGravada": 0,
                                                    "totalDescu": 0,
                                                    "tributos": [{
                                                        "codigo": "20",
                                                        "descripcion": "Impuesto al Valor Agregado 13%",
                                                        "valor": '.$dIva.'
                                                    }],
                                                    "subTotal": '.$dSUmNeto.',
                                                    "montoTotalOperacion": '.$dTotal.',
                                                    "totalLetras": "DOS MIL DOSCIENTOS SESENTA DÓLARES "
                                                },
                                                "extension": {
                                                    "nombEntrega": "Francisco Orellana",
                                                    "docuEntrega": "08130203001010",
                                                    "nombRecibe": "LANDAVERDE SANCHEZ, SONIA ELIZABETH",
                                                    "docuRecibe": "06140203741144",
                                                    "observaciones": null
                                                },
                                                "apendice": null
                            
                                                }
                                               
                                            }'; 
                            
                                }
                                
                                    
                                            
                                                  // fin
                                                // Agregar el detalle de productos al JSON existente
                                                $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                                                
                                
                                         //   echo($json_variable);
                                           //    exit;
                                           $sResult =          $this->processRequest($json_variable);
                                            break;

            default:
                // Código para casos que no coinciden con el valor especificado
                // Aquí puedes colocar el código para manejar los casos no especificados
                // ...
                break;
            }  // Procesar la solicitud
          //  $sResult = $this->processRequest($json_variable);
            $dataresult = json_decode($sResult, true);
            $data = json_decode($json_variable, true);
    
            $numeroControl1 = $data['dteJson']['identificacion']['numeroControl'];
            $fechae = $data['dteJson']['identificacion']['fecEmi'];
    
            $sello = $dataresult['selloRecibido'] ?? 'N/A';
            $estado = $dataresult['estado'] ?? 'N/A';
    
            $lims_sale_data->numerocontrol = $numeroControl1;
            $lims_sale_data->codgeneracion = strtoupper($uuid);
            $lims_sale_data->codgeneracionAnexo = $numeroControlAnexo;
            $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=" . strtoupper($uuid) . "&fechaEmi=" . $fechae;
            $aqrl = $sqr;


            if ($estado == "PROCESADO") {
                $lims_sale_data->sello = $sello;
                $lims_sale_data->estadodte = "done";
                $lims_sale_data->update(); 
                $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>"; 
                $jsonString = $json_variable;
                $jsonData = json_decode($jsonString, true);
                $pdf1 = $this->genInvoice_pdfEmail($id);
                $prettyJson = json_encode($jsonData, JSON_PRETTY_PRINT);
    ///try
   
    $json_variable = $this->agregarSelloRecepcionDATA($json_variable, $sello);

   
                    try{
                        $this->enviarCorreoConImagenYTexto($email, "factura electronica", "Gracias por su compra a continuacion su qr", $json_variable, $aqrl, $lims_sale_data->codgeneracion, $pdf1);
                    }   catch (Exception $e) {
                          echo "Error al enviar el correo: " . $e->getMessage();
                          exit();
                    }

             //   return "done";
            } else {
                
                $lims_sale_data->sello = "NA";
                $lims_sale_data->estadodte = $dataresult ;
                $lims_sale_data->update(); 
                $this->logError("Error al enviar al ministerio de hacienda", [
                    'request' => $json_variable,
                    'response' => $sResult,
                    'data' => $data
                ]);
                return "error";
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
    } catch (\Exception $e) {
       // $json_variable = $this->agregarSelloRecepcionDATA($json_variable, $sello);
        $lims_sale_data = Sale::find($id);
        $lims_sale_data->numerocontrol = $numerocontroldtte ?? 'N/A';
        $lims_sale_data->codgeneracion = strtoupper($uuid ?? 'N/A');
        $lims_sale_data->estadodte = $dataresult;
        $lims_sale_data->update();
        $this->logError("Exception in genInvoiceDTE", [
            'error' => $e->getMessage()
        ]);
        return $e->getMessage();
    }
        //return view('sale.invoice', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
    }


    function processRequest($json_variable) {
        try {
            // Parámetros para la solicitud de autenticación
        
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://142.44.196.208/wscarlos1/cs.php',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 1000,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => http_build_query(array('json_variable' => $json_variable)), 
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
              ),
            ));
           
            $response = curl_exec($curl);
          
            if (curl_errno($curl)) {
               echo 'Error en la solicitud CURL: ' . curl_error($curl);
            
            }
            
            curl_close($curl);

          
            return $response;
            
        } catch (Exception $e) {
            echo 'Excepción capturada: ' . $e->getMessage();
           
            return false; // O cualquier valor que desees retornar en caso de error
        }
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
    
 // En SaleController
public function showAnulardte($id)
{
    $sale = Sale::findOrFail($id); // Asume que quieres cargar una venta específica
    return view('sale.anulardte', compact('sale')); // Retorna la vista con la venta específica
}


public function anularDTEMH(Request $request, $id)
{
try{
    $sale = Sale::find($id);; // Asume que quieres cargar una venta específica
//dd($sale);
    $fecha = $sale->created_at;
    $fecEmi = substr($fecha, 0, 10);
    $horEmi = substr($fecha, -8);
    // Aquí procesarías los datos enviados desde el formulario
    $codigoGeneracion = $sale->codgeneracion;
    $sello = $sale->sello;
    
    $numeroControl = $sale->numerocontrol;
    //fechadoc que solo me saque la fecha sin hora
    
$fechadoc = $sale->created_at;

    //numero de control tomar el primer boque DTE-03-00000000-000000000000001 osea donde esta le 03

    $numeroControltipo = substr($numeroControl, 4, 2);
    $duiSolicitante = $request->duiSolicitante;
    $nombreSolicitante = $request->nombreSolicitante;
    $duiEjecutor = $request->duiEjecutor;
    $nombreEjecutor = $request->nombreEjecutor;
    $motivo = $request->motivo;
    $bytes = random_bytes(16);
    // Establecer los bits de la versión y de la variante
    $bytes[6] = chr(ord($bytes[6]) & 0x0F | 0x40); // Versión 4 (0100)
    $bytes[8] = chr(ord($bytes[8]) & 0x3F | 0x80); // Variante RFC 4122 (1000)
    $uuid = vsprintf('%08s-%04s-%04s-%04s-%012s', [
        bin2hex(substr($bytes, 0, 4)),
        bin2hex(substr($bytes, 4, 2)),
        bin2hex(substr($bytes, 6, 2)),
        bin2hex(substr($bytes, 8, 2)),
        bin2hex(substr($bytes, 10, 6))
    ]);
//vaiables que muestren la fecha actual y otra la hora actual  "fecAnula": "2024-04-02","horAnula": "22:06:44" 
$fecha = date('Y-m-d');
$hora = date('H:i:s');

$lims_customer_data = Customer::find($sale->customer_id);

$nit = $lims_customer_data->nit ;
$sCompany = $sale->company_name ;
$sPhoneNUmber = $sale->phone_number  ;
$email = $lims_customer_data->email  ;
//if email e svacio entonces se le asigna el email de la empresa
if ($email == "")
{
    //$email = "iora2451@gmail.com";
}
if ($sPhoneNUmber == "")
{
    $sPhoneNUmber = "78381988";
}
if ($sCompany == "")
{
    $sCompany = "RC Sistemas";
}
if ($nit == "")
{
    $nit = "06140203741144";
}




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
$tax = $sale->total_tax ;


$numeroControl1= '000000000000000'.$sale->reference_no ; 
$numeroControlFormateado1= substr($numeroControl1, -15);


    $json_variable='
{
   "nit": "'.$emisorNit.'",
                    "activo": "true",
                    "passwordPri": "'.$emisorprivatekey.'",
  "dteJson": {
    "identificacion": {
      "version": 2,
      "ambiente": "01",
      "codigoGeneracion": "'.strtoupper($uuid).'",
      "fecAnula": "'.$fecha.'",
      "horAnula": "'.$hora.'"
    },
    "emisor": {
         "nit": "'.$emisorNit.'",
        "nombre": "'.$emisorNombre.'",
      "tipoEstablecimiento": "02",
      "nomEstablecimiento": "'.$emisorNombreComercial.'",
      "telefono": "'.$emisorTelefono.'",
      "correo": "'.$emisorCorreo.'",
      "codEstableMH": null,
      "codEstable": null,
      "codPuntoVentaMH": null,
      "codPuntoVenta": null
    },
    "documento": {
      "tipoDte": "'.$numeroControltipo.'",
      "codigoGeneracion": "'.$codigoGeneracion.'",
      "selloRecibido": "'.$sello.'",
      "numeroControl": "'.$numeroControl.'",
      "fecEmi": "'.$fecEmi.'",
      "montoIva": '.$tax.',
      "codigoGeneracionR": null,
      "tipoDocumento": "36",
      "numDocumento": "'.$nit.'",
      "nombre": "'.$sCompany.'",
      "telefono": "'.$sPhoneNUmber.'",
      "correo": "'.$email.'"
    },
    "motivo": {
      "tipoAnulacion": 2,
      "motivoAnulacion": "'.$motivo.'",
      "nombreResponsable": "'.$nombreEjecutor.'",
      "tipDocResponsable": "13",
      "numDocResponsable": "'.$duiEjecutor.'",
      "nombreSolicita": "'.$nombreSolicitante.'",
      "tipDocSolicita": "13",
      "numDocSolicita": "'.$duiSolicitante.'"
    }
  }
    }

'; 

        
              // fin

         $sResult =    $this->processRequestAnular($json_variable);
     //echo $json_variable;
     //exit;
    // Lógica para manejar la anulación

    $dataresult  = json_decode($sResult, true);
    $data = json_decode($json_variable, true);

    $sello = $dataresult['selloRecibido'];
    $estado = $dataresult['estado'];

    
    if ($estado =="PROCESADO")
    {
    $sale->codgeneracionanular =strtoupper($uuid);
    $sale->selloanular = $sello;
    $sale->estadodteanular = "done";
 $sale->update();
 //   echo($sResult);
 //   exit();
   
    }
    else {
        $sale->selloanular  = "NA";
        $sale->estadodteanular = json_encode($dataresult);
        $sale->update();
        echo($sResult);
        exit();
    }
} catch (\Exception $e) {
    $lims_sale_data = Sale::find($id);
   
    $lims_sale_data->codgeneracionanular = strtoupper($uuid ?? 'N/A');
    $lims_sale_data->estadodteanular = json_encode([
       
        'codgeneracionanular' => strtoupper($uuid ?? 'N/A'),
        'error' => $e->getMessage()
    ]);
    $lims_sale_data->update();
    $this->logError("Exception in genInvoiceDTE", [
        'error' => $e->getMessage()
    ]);
    return $e->getMessage();
}
    return back()->with('status',  $sResult);
}

function processRequestAnular($json_variable) {
    // Parámetros para la solicitud de autenticación

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://142.44.196.208/wscarlos1/inv.php',
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
    
public function report(Request $request)
{        
    $fecha_ini = $request->get('fecha_ini');
    $fecha_fin = $request->get('fecha_fin');

    $fi = Carbon::now()->format('Y-m-d') . ' 00:00:00';
    $ff = Carbon::now()->format('Y-m-d') . ' 23:59:59';

    if($request->fecha_ini !== '')
    {
        $fi = Carbon::parse($fecha_ini)->format('Y-m-d') . ' 00:00:00';
        $ff = Carbon::parse($fecha_fin)->format('Y-m-d') . ' 23:59:59';
    }

    // Inicializamos la variable rownum
    DB::statement(DB::raw('SET @rownum := 0'));
// Subquery para obtener el monto del servicio de transporte (producto 714)
// Subquery para agrupar la información de product_sales por venta
// Subquery para agrupar la información de product_sales por venta
$psSub = DB::table('product_sales')
    ->select(
        'sale_id',
        DB::raw('SUM(CASE WHEN tax_rate = 0 THEN total ELSE 0 END) as total_exenta'),
        DB::raw('SUM(CASE WHEN tax_rate = 13 THEN total ELSE 0 END) as total_gravada')
    )
    ->groupBy('sale_id');

// Subquery para obtener el monto del servicio de transporte (producto 714)
$transportSub = DB::table('product_sales')
    ->select(
         'sale_id',
         DB::raw('SUM(total) as servicio_trasporte')
    )
    ->where('product_id', 714)
    ->groupBy('sale_id');

// Subquery para obtener el total de servicios de aduana (producto 705)
$aduanaSub = DB::table('product_sales')
    ->select(
         'sale_id',
         DB::raw('SUM(total) as servicio_aduana')
    )
    ->where('product_id', 705)
    ->groupBy('sale_id');

// Subquery para obtener los gastos por cuenta del cliente (producto 705)
// Se sumará el net_unit_price únicamente si tax_rate es 0.
$customSub = DB::table('product_sales')
    ->select(
         'sale_id',
         DB::raw('SUM(CASE WHEN tax = 0 THEN total ELSE 0 END) as gastos_por_cuenta_cliente')
    )
    ->groupBy('sale_id');


// Consulta principal con los joins necesarios
$sales = Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
    ->leftJoinSub($psSub, 'ps', function($join) {
        $join->on('sales.id', '=', 'ps.sale_id');
    })
    ->leftJoinSub($transportSub, 'ts', function($join) {
        $join->on('sales.id', '=', 'ts.sale_id');
    })
    ->leftJoinSub($aduanaSub, 'ads', function($join) {
        $join->on('sales.id', '=', 'ads.sale_id');
    })
    ->leftJoinSub($customSub, 'cs', function($join) {
        $join->on('sales.id', '=', 'cs.sale_id');
    })
    ->select(
        'sales.id',
        DB::raw('sales.created_at'),
        'sales.numerocontrol',
        'sales.codgeneracion',
        'customers.nit',
        'customers.tax_no',
        'sales.sello',
        'sales.reference_no',
        'sales.document_id',
        'sales.resolucion',
        'sales.serie',
        DB::raw('@rownum := @rownum + 1 as rownum'),
        DB::raw('REPLACE(customers.tax_no, "-", "") as tax_no'),
        'customers.name as cliente',
        'sales.total_price',
        DB::raw('ROUND(sales.total_price, 2) as subtotal'),
        DB::raw('ROUND(sales.total_tax, 2) as impuesto'),
        DB::raw('ROUND((sales.grand_total / 1.13) * sales.total_tax, 2) as ivatercero'),
        DB::raw('ROUND(sales.grand_total, 2) as total'),
        'ps.total_exenta',
        'ps.total_gravada',
        DB::raw('1 as tipo'),
        DB::raw('IFNULL(ts.servicio_trasporte, 0) as servicio_trasporte'),
        // Columna para el total de servicios de aduana
        DB::raw('IFNULL(ads.servicio_aduana, 0) as servicio_aduana'),
        // Columna para gastos por cuenta del cliente (net_unit_price cuando tax_rate = 0)
        DB::raw('IFNULL(cs.gastos_por_cuenta_cliente, 0) as gastos_por_cuenta_cliente')
    )
    ->whereBetween('sales.created_at', [$fi, $ff])
    ->orderBy('sales.id', 'asc')
    ->get();




    // Sumatorias generales según el rango de fechas
    $stotal = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total');
    $gtotal = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 0)->sum('grand_total');
    $siva = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total');
    $spercepcion = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_price');
    $sexento = $sales->sum('total_exenta'); // Sumamos el alias ya obtenido en la consulta
    $stotal_tax = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_tax');
    $total = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total');
    $timporta = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 1)->sum('grand_total');

    return view('sale.report', [
        "sales"       => $sales,
        "previo1"     => round($stotal, 2),
        "previo"      => ($gtotal - $sexento - $spercepcion),
        "siva"        => $total,
        "sexento"     => $sexento,
        "spercepcion" => $spercepcion,
        "simporta"    => $timporta,
        "sumaTotal"   => $total,
    ]);
}

    
    public function excel(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
       // if($role->hasPermissionTo('download-excel')){
            if(true){
            $fecha_ini = $request->get('fecha_ini');
            $fecha_fin = $request->get('fecha_fin');

            $fi = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
            $ff = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:59';

            if($request->fecha_ini !=='')
            {
                $fi = Carbon::parse($fecha_ini)->format('Y-m-d'). ' 00:00:00';
                $ff = Carbon::parse($fecha_fin)->format('Y-m-d'). ' 23:59:59';
            }
            
            $sql = $request->get('type_libro');
            DB::statement(DB::raw('set @rownum=0'));

            $sales = Sale::join('customers','sales.customer_id','=','customers.id')
                    ->join('users','sales.user_id','=','users.id')
                    ->select(DB::raw('DATE_FORMAT(sales.created_at, "%d/%m/%Y") as formatted_dob'),'sales.reference_no','sales.document_id',
                        'sales.resolucion','sales.serie',DB::raw('@rownum := @rownum + 1 as rownum'),
                        DB::raw('REPLACE(customers.tax_no, "-", "")'),
                        'customers.name as cliente','sales.total_price',DB::raw('round(sum((sales.grand_total-sales.total_tax)/1.13),2) as subtotal'),
                        DB::raw('round(sum(((sales.grand_total-sales.total_tax)/1.13)*sales.total_tax),2) as impuesto'),
                        DB::raw('round(sum((sales.grand_total/1.13)*sales.total_tax),2) as ivatercero'),
                        DB::raw('round(sales.grand_total-sales.total_tax,2) as total'),DB::raw('1 as tipo'))
                    ->whereBetween('sales.created_at', [$fi, $ff])
                    ->groupBy('sales.reference_no','document_id','sales.resolucion','sales.serie',
                        'sales.created_at','sales.sale_status','sales.total_tax','customers.tax_no',
                        'sales.grand_total','customers.name','sales.total_price')
                    ->orderBy('sales.id', 'asc')->get();
                    //dd($sales);

                $stotal = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13;
                $gtotal = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 0)->sum('grand_total');
                $siva = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13*0.13;
                $sexento = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_price');
                $stotal_tax = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_tax');
                $total = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total');
                $timporta = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 1)->sum('grand_total')/1.13;
                //dd($sales);

            return (new SalesExport($sales))->download('sale-libronew.xlsx');
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }


    public function libro(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
      //  if($role->hasPermissionTo('download-excel')){
            if(true){
            $fecha_ini = $request->get('fecha_ini');
            $fecha_fin = $request->get('fecha_fin');

            $fi = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
            $ff = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:59';
            $tp1 = '03'; 
            $tp2 = '05';

            if($request->fecha_ini !=='')
            {
                $fi = Carbon::parse($fecha_ini)->format('Y-m-d'). ' 00:00:00';
                $ff = Carbon::parse($fecha_fin)->format('Y-m-d'). ' 23:59:59';
            }

            $sales = Sale::join('customers','sales.customer_id','=','customers.id')
                ->select(DB::raw('DATE_FORMAT(sales.created_at, "%d/%m/%Y") as formatted_dob'),'sales.reference_no','sales.document_id',
                    'sales.resolucion','sales.serie',DB::raw('@rownum := @rownum + 1 as rownum'),
                    'customers.tax_no', 'sales.created_at', 'sales.document_id', 'sales.tercero', 
                    'customers.name as cliente','sales.total_price',DB::raw('round(sum((sales.grand_total-sales.total_tax)),2) as subtotal'),
                    DB::raw('round(sum((sales.total_tax)),2) as impuesto'), 'sales.codgeneracion',
                    DB::raw('round(sum((sales.total_tax)),2) as ivatercero'),
                    DB::raw('round(sales.grand_total,2) as total'),DB::raw('1 as tipo'))
                ->whereBetween('sales.created_at', [$fi, $ff])
                ->groupBy('sales.reference_no','document_id','sales.resolucion','sales.serie',
                    'sales.created_at','sales.sale_status','sales.total_tax','customers.tax_no', 'sales.codgeneracion',
                    'sales.grand_total','customers.name','sales.total_price','sales.document_id', 'sales.tercero')
                ->orderBy('sales.id', 'asc')->get();

            //dd($sales);   

            // $sales = Sale::join('customers','sales.customer_id','=','customers.id')
            //     ->join('users','sales.user_id','=','users.id')
            //     ->select(DB::raw('DATE_FORMAT(sales.date_sale, "%d/%m/%Y") as formatted_dob'),'sales.document_class','sales.document_type',
            //         'sales.resolution','sales.serie','sales.num_sale','customers.num_document',
            //         'customers.name as cliente','sales.exento','sales.percepcion',DB::raw('round(sum((sales.total-sales.percepcion)/1.13),2) as subtotal'),
            //         DB::raw('round(sum(((sales.total-sales.percepcion)/1.13)*sales.tax),2) as impuesto'),
            //         'sales.tercero', DB::raw('round(sum((sales.tercero/1.13)*sales.tax),2) as ivatercero'),
            //         DB::raw('round(sales.total+sales.exento,2) as total'),DB::raw('1 as tipo'))
            //     ->whereBetween('sales.date_sale', [$fi, $ff])
            //     ->whereBetween('sales.document_type', [$tp1, $tp2])
            //     ->groupBy('sales.identification_type','sales.document_class','document_type','sales.resolution','sales.serie',
            //         'sales.num_sale','sales.date_sale','sales.status','customers.num_document',
            //         'sales.total','customers.name','sales.exento','sales.percepcion','sales.tercero')
            //     ->orderBy('sales.id', 'asc')->get();

            // $stotal = Sale::whereBetween('date_sale', [$fi, $ff])->where('tipo', 0)->sum('total')/1.13;
            // $gtotal = Sale::whereBetween('date_sale', [$fi, $ff])->where('tipo', 0)->sum('total');
            // $siva = Sale::whereBetween('date_sale', [$fi, $ff])->sum('total')/1.13*0.13;
            // $sexento = Sale::whereBetween('date_sale', [$fi, $ff])->sum('exento');
            // $spercepcion = Sale::whereBetween('date_sale', [$fi, $ff])->sum('percepcion');
            // $total = Sale::whereBetween('date_sale', [$fi, $ff])->sum('total');
            // $timporta = Sale::whereBetween('date_sale', [$fi, $ff])->where('tipo', 1)->sum('total')/1.13;
            // $sconsutotal = Sale::whereBetween('date_sale', [$fi, $ff])->where('document_type', '01')->sum('total')/1.13;
            // $sconsuiva = Sale::whereBetween('date_sale', [$fi, $ff])->where('document_type', '01')->sum('total')/1.13*0.13;
            // $stexporta = Sale::whereBetween('date_sale', [$fi, $ff])->where('document_type', '11')->sum('total')/1.13;

            $stotal = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13;
            $gtotal = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 0)->sum('grand_total');
            $siva = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13*0.13;
            $sexento = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_price');
            $spercepcion = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_price');
            $total = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total');
            $timporta = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 1)->sum('grand_total')/1.13;
            $sconsutotal = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 1)->sum('grand_total')/1.13;
            $sconsuiva = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 1)->sum('grand_total')/1.13*0.13;
            $stexporta = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 1)->sum('grand_total')/1.13;            
            $stotal_tax = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_tax');
            
                //dd($sales);

            $pdf= \PDF::loadView('sale.libro',[
                "sales"=>$sales,
                "previo1"=>round($stotal,2),
                "previo"=>($gtotal-$sexento-$spercepcion)/1.13,
                "siva"=>(($total-$spercepcion)/1.13)*0.13,
                "sexento"=>$sexento,
                "spercepcion"=>$spercepcion,
                "simporta"=>$timporta,
                "sconsutotal"=>$sconsutotal,
                "sconsuiva"=>$sconsuiva,
                "stexporta"=>$stexporta,
                "sumaTotal"=>($total+$sexento)]);

            return $pdf->setPaper('a4', 'landscape')->download('sales.pdf');
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    
   public function ExportacionPdf(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('download-ccf')){
            $fecha_ini = $request->get('fecha_ini');
            $fecha_fin = $request->get('fecha_fin');

            $startDate = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
            $endDate = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:59';
            $documentId = 1;

            if($request->fecha_ini !=='')
            {
                $startDate = Carbon::parse($fecha_ini)->format('Y-m-d'). ' 00:00:00';
                $endDate = Carbon::parse($fecha_fin)->format('Y-m-d'). ' 23:59:59';
            }

            // Filtrar ventas en el rango de fechas
            $lims_sale_data = Sale::with('warehouse')
                ->whereDate('created_at', '>=' , $startDate)
                ->whereDate('created_at', '<=' , $endDate)
                ->where('document_id', $documentId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Crear un array para almacenar los datos de PDF
            $pdfData = [];

            foreach ($lims_sale_data as $sale) {
                $userName = auth()->user()->name_purchase;
                $userNit = auth()->user()->nit;
                $lims_product_sale_data = Product_Sale::where('sale_id', $sale->id)->get();
                $lims_biller_data = Biller::find($sale->biller_id);
                $lims_warehouse_data = Warehouse::find($sale->warehouse_id);
                $lims_customer_data = Customer::find($sale->customer_id);
                $lims_payment_data = Payment::where('sale_id', $sale->id)->get();
                
                $uuid =  $sale->codgeneracion;
                $fecha = $sale->created_at;
                $fecEmi = substr($fecha, 0, 10);
                $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".strtoupper($uuid)."&fechaEmi=".$fecEmi;
                $aqrl = $sqr;
                $loco = "https://rcsinversiones.com/demo/generate_qr.php?texto=";
                
                $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>"; 

                $numberToWords = new NumberToWords();
                if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
                    $numberTransformer = $numberToWords->getNumberTransformer('en');
                else
                    $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
                $numberInWords = $numberTransformer->toWords($sale->grand_total);

                $codgeneracion = $sale->codgeneracion ?? 'No disponible';
                $numerocontrol = $sale->numerocontrol ?? 'No disponible';
                $sello = $sale->sello ?? 'No disponible';
                $created_at = $sale->created_at ?? 'No disponible';

                $formatter = new NumeroALetras();
                $todo = $formatter->toInvoice($sale->grand_total);

                // Generar QR code
                $sofia = QrCode::size(100)->generate($aqrl);

                // Añadir datos al array de datos para el PDF
                $pdfData[] = [
                    "lims_sale_data" => $sale,
                    "codgeneracion" => $codgeneracion,
                    "numerocontrol" => $numerocontrol,
                    "sello" => $sello,
                    "created_at" => $created_at,
                    "valor" => $sofia,
                    "lims_product_sale_data" => $lims_product_sale_data,
                    "lims_biller_data" => $lims_biller_data,
                    "lims_warehouse_data" => $lims_warehouse_data,
                    "lims_customer_data" => $lims_customer_data,
                    "lims_payment_data" => $lims_payment_data,
                    "numberToWords" => $numberInWords,
                    "todo" => $todo,
                    "userName" => $userName,
                    "userNit" => $userNit,
                    "aqrl" => $aqrl
                ];
            }
            
            $pdf = \PDF::loadView('sale.ccf', ['pdfData' => $pdfData]);

            return $pdf->setPaper('a4', 'portrait')->download('consumidor.pdf');
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function ExportacionPdfFac(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('download-fac')){
            $fecha_ini = $request->get('fecha_ini');
            $fecha_fin = $request->get('fecha_fin');

            $startDate = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
            $endDate = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:59';
            $documentId = 2;

            if($request->fecha_ini !=='')
            {
                $startDate = Carbon::parse($fecha_ini)->format('Y-m-d'). ' 00:00:00';
                $endDate = Carbon::parse($fecha_fin)->format('Y-m-d'). ' 23:59:59';
            }

            // Filtrar ventas en el rango de fechas
            $lims_sale_data = Sale::with('warehouse')
                ->whereDate('created_at', '>=' , $startDate)
                ->whereDate('created_at', '<=' , $endDate)
                ->where('document_id', $documentId)
                ->where('canceled', '===' , '0')
                ->orderBy('created_at', 'desc')
                ->get();

            // Crear un array para almacenar los datos de PDF
            $pdfData = [];

            foreach ($lims_sale_data as $sale) {
                $userName = auth()->user()->name_purchase;
                $userNit = auth()->user()->nit;
                $lims_product_sale_data = Product_Sale::where('sale_id', $sale->id)->get();
                $lims_biller_data = Biller::find($sale->biller_id);
                $lims_warehouse_data = Warehouse::find($sale->warehouse_id);
                $lims_customer_data = Customer::find($sale->customer_id);
                $lims_payment_data = Payment::where('sale_id', $sale->id)->get();
                
                $uuid =  $sale->codgeneracion;
                $fecha = $sale->created_at;
                $fecEmi = substr($fecha, 0, 10);
                $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".strtoupper($uuid)."&fechaEmi=".$fecEmi;
                $aqrl = $sqr;
                $loco = "https://rcsinversiones.com/demo/generate_qr.php?texto=";
                
                $imgh = "<img src='https://rcsinversiones.com/demo/generate_qr.php?texto=" . urlencode($aqrl) . "' alt='QR Code' style='width: 70px; height: 70px; display: block; margin: 0 auto;'>"; 

                $numberToWords = new NumberToWords();
                if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
                    $numberTransformer = $numberToWords->getNumberTransformer('en');
                else
                    $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
                $numberInWords = $numberTransformer->toWords($sale->grand_total);

                $codgeneracion = $sale->codgeneracion ?? 'No disponible';
                $numerocontrol = $sale->numerocontrol ?? 'No disponible';
                $sello = $sale->sello ?? 'No disponible';
                $created_at = $sale->created_at ?? 'No disponible';

                $formatter = new NumeroALetras();
                $todo = $formatter->toInvoice($sale->grand_total);

                // Generar QR code
                $sofia = QrCode::size(100)->generate($aqrl);

                // Añadir datos al array de datos para el PDF
                $pdfData[] = [
                    "lims_sale_data" => $sale,
                    "codgeneracion" => $codgeneracion,
                    "numerocontrol" => $numerocontrol,
                    "sello" => $sello,
                    "created_at" => $created_at,
                    "valor" => $sofia,
                    "lims_product_sale_data" => $lims_product_sale_data,
                    "lims_biller_data" => $lims_biller_data,
                    "lims_warehouse_data" => $lims_warehouse_data,
                    "lims_customer_data" => $lims_customer_data,
                    "lims_payment_data" => $lims_payment_data,
                    "numberToWords" => $numberInWords,
                    "todo" => $todo,
                    "userName" => $userName,
                    "userNit" => $userNit,
                    "aqrl" => $aqrl
                ];
            }
            
            $pdf = \PDF::loadView('sale.factura', ['pdfData' => $pdfData]);

            return $pdf->setPaper('a4', 'portrait')->download('consumidor.pdf');
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function downloadJsonccfByDateRange(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('download-jsonfac')){
            $fecha_ini = $request->get('fecha_ini');
            $fecha_fin = $request->get('fecha_fin');

            $startDate = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
            $endDate = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:59';
            $documentId = 1;

            if($request->fecha_ini !=='')
            {
                $startDate = Carbon::parse($fecha_ini)->format('Y-m-d'). ' 00:00:00';
                $endDate = Carbon::parse($fecha_fin)->format('Y-m-d'). ' 23:59:59';
            }
       
            $sales = Sale::with('warehouse')
                ->whereDate('created_at', '>=' , $startDate)
                ->whereDate('created_at', '<=' , $endDate)
                ->where('document_id', $documentId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Crear un array para almacenar los archivos JSON
            $files = [];

            foreach ($sales as $sale) {
                $filename = 'sale_' . $sale->id . '.json';
                $jsonData = json_encode($sale);

                // Crear un archivo JSON en memoria
                $files[$filename] = $jsonData;
            }

            // Preparar la respuesta zip
            return $this->downloadZip($files);
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');    
    }

    public function downloadJsonfacByDateRange(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('download-jsonfac')){
            $fecha_ini = $request->get('fecha_ini');
            $fecha_fin = $request->get('fecha_fin');

            $startDate = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
            $endDate = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:59';
            $documentId = 2;

            if($request->fecha_ini !=='')
            {
                $startDate = Carbon::parse($fecha_ini)->format('Y-m-d'). ' 00:00:00';
                $endDate = Carbon::parse($fecha_fin)->format('Y-m-d'). ' 23:59:59';
            }
       
            $sales = Sale::with('warehouse')
                ->whereDate('created_at', '>=' , $startDate)
                ->whereDate('created_at', '<=' , $endDate)
                ->where('document_id', $documentId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Crear un array para almacenar los archivos JSON
            $files = [];

            foreach ($sales as $sale) {
                $filename = 'sale_' . $sale->id . '.json';
                $jsonData = json_encode($sale);

                // Crear un archivo JSON en memoria
                $files[$filename] = $jsonData;
            }

            // Preparar la respuesta zip
            return $this->downloadZip($files);
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');    
    }

    protected function downloadZip($files)
    {
        $zip = new \ZipArchive();
        $zipFilename = 'sales.zip';

        if ($zip->open(public_path($zipFilename), \ZipArchive::CREATE) === TRUE) {
            foreach ($files as $filename => $content) {
                $zip->addFromString($filename, $content);
            }
            $zip->close();
        }

        return response()->download(public_path($zipFilename))->deleteFileAfterSend(true);
    }
    
    
    
}
