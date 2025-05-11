<?php

namespace App\Http\Controllers;

use App\Sexcluded; 
use App\Excluded;
use App\Warehouse;
use App\Product;
use App\Unit;
use App\Payment;
use NumberToWords\NumberToWords;
use Auth;
use App\ProductSexcluded;
use App\Product_Warehouse;
use App\Tax;
use App\PosSetting;
use App\Types_document;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Luecano\NumeroALetras\NumeroALetras;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Dompdf\Dompdf;
use Carbon\Carbon;
use DB;
use App\Exports\ExcludesExport;

class SexcludedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('exclude-index')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_excluded_all = Sexcluded::with('excluded', 'warehouse', 'user')->orderBy('id', 'desc')->orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_excluded_all = Sexcluded::with('excluded', 'warehouse', 'user')->orderBy('id', 'desc')->get();
                $lims_pos_setting_data = PosSetting::latest()->first();
            return view('sexcluded.index', compact('lims_excluded_all', 'all_permission', 'lims_pos_setting_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function saleData(Request $request)
    {
     
        $controlnumeroanexo= "NA";
        $columns = array( 
            1 => 'created_at', 
            2 => 'reference_no',
            3 => 'estadodte',
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
        dd($order);
        if(empty($request->input('search.value'))){
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $sales = Sexcluded::with('excluded', 'warehouse', 'user')->offset($start)
                    ->where('user_id', Auth::id())
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            else
                $sales = Sexcluded::with('excluded', 'warehouse', 'user')->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        }
        else
        {
            $search = $request->input('search.value');
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own') {
                $sales =  Sexcluded::select('excludes.*')
                            ->with('excluded', 'warehouse', 'user')
                            ->join('excludes', 'excludes.customer_id', '=', 'excluded.id')
                            ->whereDate('excludes.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                            ->where('excludes.user_id', Auth::id())
                            ->orwhere([
                                ['excludes.reference_no', 'LIKE', "%{$search}%"],
                                ['excludes.user_id', Auth::id()]
                            ])
                            ->orwhere([
                                ['excludes.name', 'LIKE', "%{$search}%"],
                                ['excludes.user_id', Auth::id()]
                            ])
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)->get();

                $totalFiltered = Sexcluded::
                            join('excluded', 'excludes.customer_id', '=', 'excluded.id')
                            ->whereDate('excludes.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                            ->where('excludes.user_id', Auth::id())
                            ->orwhere([
                                ['excludes.reference_no', 'LIKE', "%{$search}%"],
                                ['excludes.user_id', Auth::id()]
                            ])
                            ->orwhere([
                                ['excludes.name', 'LIKE', "%{$search}%"],
                                ['excludes.user_id', Auth::id()]
                            ])
                            ->count();
            }
            else {
                $sales =  Sexcluded::select('excludes.*')
                            ->with('excluded', 'warehouse', 'user')
                            ->join('excludes', 'excludes.customer_id', '=', 'excluded.id')
                            ->whereDate('excludes.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                            ->where('excludes.user_id', Auth::id())
                            ->orwhere('excludes.reference_no', 'LIKE', "%{$search}%")
                            ->orwhere('excludes.name', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)->get();

                $totalFiltered = Sale::
                            join('excludes', 'excludes.customer_id', '=', 'excluded.id')
                            ->whereDate('excludes.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
                            ->where('excludes.user_id', Auth::id())
                            ->orwhere('excludes.reference_no', 'LIKE', "%{$search}%")
                            ->orwhere('excludes.name', 'LIKE', "%{$search}%")
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
                
                if ($sale->estadodte == "done")
                {                    
                    $nestedData['estadodte'] = "Procesado";
                }
                else
                {
                    $nestedData['estadodte'] = "Rechazado";
                }

                $nestedData['excluded'] = $sale->excluded->name;
                $conteo1 = DB::select('  SELECT count(*) as total
                    FROM product_sexcludes where sexcluded_id='.$sale->id." and existence<0 and unit_cost=0");      
                                   

                $conteo = $conteo1[0]->total; 

                $nestedData['grand_total'] = number_format($sale->grand_total, 2);
                
                $nestedData['options'] = '<div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                        <li><a href="'.route('sale.invoiceprint', ['id' => $sale->id]).'" class="btn btn-link"><i class="fa fa-copy"></i> '.trans('file.Generate Invoice').'</a></li>

                                <li>
                                    <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> '.trans('file.View').'</button>
                                </li>
                                
                                
                                <li>
                                <button type="button" class="btn btn-link viewdte"><i class="fa fa-eye"></i> Estado de DTE</button>
                            </li>'
                                
                                ;
                                


                         $nestedData['options'] .= '<li><a href="'.route('sale.invoice_ccf', $sale->id).'" class="btn btn-link"><i class="fa fa-copy"></i> Generate invoice ccf</a></li>
                                <li>';    

                        $nestedData['options'] .= '<li><a href="'.route('sale.invoice_export', $sale->id).'" class="btn btn-link"><i class="fa fa-copy"></i> Generate invoice Exp</a></li>
                                <li>';


                if(in_array("sales-edit", $request['all_permission'])){
                    if($sale->sale_status != 3)
                        $nestedData['options'] .= '<li>
                            <a href="'.route('sales.edit', $sale->id).'" class="btn btn-link"><i class="dripicons-document-edit"></i> '.trans('file.edit').'</a>
                            </li>';
                    else
                        $nestedData['options'] .= '<li>
                            <a href="'.url('sales/'.$sale->id.'/create').'" class="btn btn-link"><i class="dripicons-document-edit"></i> '.trans('file.edit').'</a>
                        </li>';
                }
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



                     <li><a href="'.route('sale.anular', $sale->id).'" class="btn btn-link"><i class="fa fa-copy"></i> Anular</a></li>

                      <li><a href="'.route('sale.aplicar_costos', $sale->id).'" class="btn btn-danger"><i class="fa fa-copy"></i> Applicar costos</a></li>



                    ';
                if(in_array("sales-delete", $request['all_permission']))
                    $nestedData['options'] .= \Form::open(["route" => ["sales.destroy", $sale->id], "method" => "DELETE"] ).'
                            <li>
                              <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> '.trans("file.delete").'</button> 
                            </li>'.\Form::close().'
                        </ul>
                    </div>';
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
            
        echo json_encode($json_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$role = Role::find(Auth::user()->role_id);
        //if($role->hasPermissionTo('exclude-add')){
        //    $lims_excluded_list = Excluded::where('is_active',true)->get();
        //    if(Auth::user()->role_id <= 2) {
        //        $lims_warehouse_list = Warehouse::where('is_active',true)->get();
        //    }
        //    else {
        //        $lims_warehouse_list = Warehouse::where('id',Auth::user()->warehouse_id)->get();
        //    }
        //    $lims_documents_list = Types_document::where('modulo', 'POS')->get();
        //    $lims_tax_list = Tax::where('is_active',true)->get();
        //    $lims_pos_setting_data = PosSetting::latest()->first();            
        //    $lims_product_list_without_variant = $this->productWithoutVariant();
        //    $lims_product_list_with_variant = $this->productWithVariant();
        //    return view('sexcluded.create', compact('lims_excluded_list', 'lims_warehouse_list', 'lims_tax_list', 'lims_pos_setting_data', 'lims_product_list_without_variant', 'lims_product_list_with_variant','lims_documents_list'));
        //}
        //else
        //    return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
            
            
       $lims_excluded_list = Excluded::where('is_active',true)->get();
       $lims_warehouse_list = Warehouse::where('is_active',true)->get();
       
       
       $lims_documents_list = Types_document::where('modulo', 'POS')->get();
            $lims_tax_list = Tax::where('is_active',true)->get();
            $lims_pos_setting_data = PosSetting::latest()->first();            
            $lims_product_list_without_variant = $this->productWithoutVariant();
            $lims_product_list_with_variant = $this->productWithVariant();
            return view('sexcluded.create', compact('lims_excluded_list', 'lims_warehouse_list', 'lims_tax_list', 'lims_pos_setting_data', 'lims_product_list_without_variant', 'lims_product_list_with_variant','lims_documents_list'));
       
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('document');
        //dd($data);
        $datoFactura = DB::select("SELECT IFNULL(MAX(correlativo),0) as correlativo FROM types_documents WHERE id=".$data['document_id']); 
        $correlativoNew = $datoFactura[0]->correlativo; 
        
        if(!isset($data['reference_no']))
            $data['reference_no'] = $correlativoNew;
            
        $data['user_id'] = Auth::id();
        //$data['reference_no'] = 'se-' . date("Ymd") . '-'. date("his");
        
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

        Sexcluded::create($data);
         $lims_data_sale = Types_document::find($data["document_id"]);
        /*Decidir si sera un incremento normal de correlativo o el usuario coloco un correlativo nuevo*/

        $nuevocorrelativo =$data['reference_no']+1;

        $data_actualiza["correlativo"] =$nuevocorrelativo; 

        Types_document::where([
            ['id', $data["document_id"]]
        ])->update($data_actualiza);
        

        $lims_purchase_data = Sexcluded::latest()->first();
        //dd($lims_purchase_data);
        $status = $lims_purchase_data->status;

        $product_id = $data['product_id'];
        $product_code = $data['product_code'];
        $qty = $data['qty'];
        $description = $data['description'];
        $purchase_unit = $data['purchase_unit'];
        $net_unit_cost = $data['net_unit_cost'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];
        $product_purchase = [];

        foreach ($product_id as $i => $id) {
            $lims_purchase_unit_data  = Unit::where('unit_name', $purchase_unit[$i])->first();

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
             
            $product_purchase['sexcluded_id'] = $lims_purchase_data->id ;
            $product_purchase['product_id'] = $id;
            $product_purchase['qty'] = $qty[$i];
            $product_purchase['description'] = $description[$i];
            $product_purchase['purchase_unit_id'] = $lims_purchase_unit_data->id;
            $product_purchase['net_unit_cost'] = $net_unit_cost[$i];
            $product_purchase['net_unit_cost_original'] = $net_unit_cost[$i];
            $product_purchase['discount'] = $discount[$i];
            $product_purchase['tax_rate'] = $tax_rate[$i];
            $product_purchase['tax'] = $tax[$i];
            $product_purchase['total'] = $total[$i];
            
            ProductSexcluded::create($product_purchase);
        }
        $this->genInvoice($lims_purchase_data->id, "");
        return redirect('sexcluded')->with('message', 'Excluded created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sexcluded  $sexcluded
     * @return \Illuminate\Http\Response
     */
    public function show(Sexcluded $sexcluded)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sexcluded  $sexcluded
     * @return \Illuminate\Http\Response
     */
    public function edit(Sexcluded $sexcluded)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sexcluded  $sexcluded
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sexcluded $sexcluded)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sexcluded  $sexcluded
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sexcluded $sexcluded)
    {
        //
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

    public function productPurchaseData($id)
    {
        $lims_product_purchase_data = ProductSexcluded::where('sexcluded_id', $id)->get();
        foreach ($lims_product_purchase_data as $key => $product_purchase_data) {
            $product = Product::find($product_purchase_data->product_id);
            $unit = Unit::find($product_purchase_data->purchase_unit_id);
           
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

    public function genInvoice_ccf($id)
    {
        $userName = auth()->user()->name_purchase;
        $userNit = auth()->user()->email_purchase;
        $lims_sale_data = Sexcluded::find($id);
        $lims_product_sale_data = ProductSexcluded::where('sexcluded_id', $id)->get();
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Excluded::find($lims_sale_data->excluded_id);
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

        $pdf= \PDF::loadView('sexcluded.efact',[
            "lims_sale_data"=>$lims_sale_data,
            "valor"=> $sofia,
            "lims_product_sale_data"=>$lims_product_sale_data,
            "lims_warehouse_data"=>$lims_warehouse_data,
            "lims_customer_data"=>$lims_customer_data,
            "lims_payment_data"=>$lims_payment_data,
            "numberToWords"=>$numberInWords,
            "todo"=>$todo,
            "userName"=>$userName,
            "userNit"=>$userNit,
            "aqrl"=>$aqrl ]);
        //dd($pdf);

        $namearchivo = $lims_sale_data->codgeneracion . ".pdf";
        //dd($namearchivo);
        return $pdf->setPaper('letter', 'portrait')->download($namearchivo);

        //return view('sale.invoice_ccf', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
    }



    public function genInvoice($id, $numerocontrolanexo)
    {     
        try{
        $numeroControlAnexo =  $numerocontrolanexo;
        
        $lims_sale_data = Sexcluded::find($id);
        $lims_product_sale_data = ProductSexcluded::where('sexcluded_id', $id)->get();
     
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        $lims_customer_data = Excluded::find($lims_sale_data->excluded_id);
    
        $nit = $lims_customer_data->nit ;
        $nombre = $lims_customer_data->name ;
        $direccion = $lims_customer_data->address ;
        $dui = $lims_customer_data->dui ;
    
        $telefono = $lims_customer_data->phone ;
        $email = $lims_customer_data->email ;
        $estado1 = $lims_customer_data->estado ;
        $municipio = $lims_customer_data->municipio ;
         
    
        //$lims_payment_data = Payment::where('sale_id', $id)->get();
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
        $numeroControl= '000000000000000'.$lims_sale_data->reference_no; 
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
        $dIva = 0.00000000000000000000000;
        $dSUmNeto = 0.000000000000000000000;
        $customerName = '';
        
        // Obtener los productos relacionados con la venta
        foreach ($lims_product_sale_data as $index => $product_sale) {
            $product = product::find($product_sale->product_id); // Obtener el producto correspondiente
            
            // Verificar si el producto existe
            if ($product) {
                $sale = Sexcluded::find($product_sale->sexcluded_id); // Obtener la venta correspondiente
          
                
                $preciouni_r =  $product_sale->net_unit_cost;
                //convertir $preciouni_r a double 
                $preciouni_r = doubleval($preciouni_r);

                $compra_r =($product_sale->qty *  $product_sale->net_unit_cost);      
                
                //convertir $compra_r a double pero de dos decimales 

                $compra_r = doubleval($compra_r);

                $detalleProducto = [
                    "numItem" => $index + 1, // Número secuencial del item
                    "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)                        
                    "codigo" => $product->code, // Código del producto obtenido de la clase Product
                    "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                    "cantidad" => $product_sale->qty, // Cantidad del producto
                    "uniMedida" => 59, // Unidad de medida del producto
                    "precioUni" => $preciouni_r, // Precio unitario del producto
                    "montoDescu" => 0,
                    "compra" => $compra_r // Precio total del producto
                
                ];
                $dIva = $dIva + $product_sale->tax;
                $dSUmNeto = $dSUmNeto +round(($product_sale->qty * round( $product_sale->net_unit_cost,2)),2);
                $detalleProductos[] = $detalleProducto;
            }
        }
    
   
        $dTotal = $dIva + $dSUmNeto;
        $sLetras = $this->numeroALetras($dTotal);
        $reterenta = $dSUmNeto*.1;
        $valorsujetot = $dSUmNeto - ($reterenta + $dIva);

        $code_muni = $lims_customer_data->municipio->code;
        $code_estado = $lims_customer_data->estado->code;
        $json_variable='
    
  
        {
            "nit": "'.$emisorNit.'",
            "activo": "true",
            "passwordPri": "'.$emisorprivatekey.'",
            "dteJson": {

            	"identificacion": {
            		"version": 1,
            		"ambiente": "01",
            		"tipoDte": "14",
            		"numeroControl": "DTE-14-00000000-'.$numeroControlFormateado.'",
                                    "codigoGeneracion": "'.strtoupper($uuid).'",
            		"tipoModelo": 1,
            		"tipoOperacion": 1,
            		"tipoContingencia": null,
            		"motivoContin": null,
                    "fecEmi": "'.$fecEmi.'",
                    "horEmi": "'.$horEmi.'",
            		"tipoMoneda": "USD"

            	},
            	"emisor": {
                "nit": "'.$emisorNit.'",
                "nrc": "'.$emisorNrc.'",
                "nombre": "'.$emisorNombre.'",
                "codActividad": "'.$emisorCodActividad.'",
                "descActividad": "'.$emisorDescActividad.'",
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
        	"sujetoExcluido": {
                "tipoDocumento":"13",
                "numDocumento": "'.$dui.'",
          
                "nombre": "'.$nombre.'" ,
                "codActividad": null,
                "descActividad": null,
            
                "direccion": {
                    "departamento": "'.$code_estado.'",
                    "municipio": "'.$code_muni.'",
                    "complemento": "'.$direccion.'"
                },
                "telefono": "'.$telefono.'",
                "correo": "'.$email.'"
            },
	       "cuerpoDocumento": [],
	       "resumen": {

        		"totalDescu": 0,
        		"subTotal": '.$dSUmNeto.',
        	
        		"ivaRete1": '.$dIva.',
        		"reteRenta": '.$reterenta.',
                "descu": 0,
                "totalCompra": '.$dSUmNeto.',
        		"totalPagar": '.$valorsujetot.',
        		"totalLetras": "'.$sLetras.'",
        	
        		"condicionOperacion": 1,
        		"pagos": [{
        			"codigo": "01",
        			"montoPago": '.$dSUmNeto.',
        			"plazo": null,
        			"referencia": null,
        			"periodo": null
        	}],
        	"observaciones": ""
        },

		
		"apendice": null

	   }
	
    }'; 
    
                
                      // fin
                    // Agregar el detalle de productos al JSON existente
                    $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                    
    
                 //echo($json_variable);
               //exit;
                 $sResult =    $this->processRequest($json_variable);

    //sujeto excluido

    
    $dataresult  = json_decode($sResult, true);
    // contatener Json_variable y result 
  
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
  //  $pdf1 = $this->genInvoice_pdf($id);
    //coloca una variable donde este el correo electronico dle cliente
    
    // Codifica nuevamente el JSON con la opción JSON_PRETTY_PRINT
    $prettyJson = json_encode($jsonData, JSON_PRETTY_PRINT);
    
    
    //  $this->enviarCorreoConImagenYTexto($email, "factura electronica", "Gracias por su compra a continuacion su qr" , $json_variable, $aqrl, $pdf1);
    //echo  $aqrl;
    } 
    else {
        $lims_sale_data->sello = "NA";
        $lims_sale_data->estadodte = json_encode($dataresult);
        
    
    } 

    
              $lims_sale_data->update();
    
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
            catch (Exception $e) {
                $lims_sale_data->sello = "NA";
                $lims_sale_data->estadodte = json_encode($dataresult);
                
                return $e->getMessage();
            }
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
        

        
        public function getDteJson(Request $request) {

            $id = $request->input('id');
            $json_variable="";
            $lims_return_data = Sexcluded::find($id);
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
        $id = $request->input('id');
                    /*   
                        $json_variable="";
                        $lims_return_data = Retention::find($id);
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
                        return response()->json($json_variable);*/
    
     
                    $numeroControlAnexo =  "";
                    $lims_sale_data = Sexcluded::find($id);
    
              
                    $codigoGeneracion = $lims_sale_data->codgeneracion;
                    $numerocontroldte = $lims_sale_data->numerocontrol;
         
    
                    $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
                    $lims_customer_data = Supplier::find($lims_sale_data->supplier_id);
    
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
    
    
                    
                    
    
                    //documento de credi fiscal
    
           
        
                /*AQUI CONSTRUIREMOS JSON PARA ENVIO DE FACTURACION ELECTRONICA*/
     
                $detalleProductos = [];
                $dIva = 0.00000000000000000000000;
                $dSUmNeto = 0.000000000000000000000;
                $customerName = '';
                    // Obtener los productos relacionados con la venta
                    foreach ($lims_product_sale_data as $index => $product_sale) {
                        $product = product::find($product_sale->product_id); // Obtener el producto correspondiente
                        
                        // Verificar si el producto existe
                        if ($product) {
        
                            $sale = Sexcluded::find($product_sale->sexcluded_id); // Obtener la venta correspondiente
                      
                            
                       $preciouni_r = number_format( $product_sale->net_unit_cost,2);
                       //convertir $preciouni_r a double 
                         $preciouni_r = doubleval($preciouni_r);
    
                       $compra_r =number_format(($product_sale->qty * number_format( $product_sale->net_unit_cost,2)),2);      
                       //convertir $compra_r a double pero de dos decimales 
    
                         $compra_r = doubleval($compra_r);
    
                            $detalleProducto = [
                                "numItem" => $index + 1, // Número secuencial del item
                                "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)                        
                                "codigo" => $product->code, // Código del producto obtenido de la clase Product
                                "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                                "cantidad" => $product_sale->qty, // Cantidad del producto
                                "uniMedida" => 59, // Unidad de medida del producto
                                "precioUni" => $preciouni_r, // Precio unitario del producto
                                "montoDescu" => 0,
                                "compra" => $compra_r // Precio total del producto
                            
                            ];
                    $dIva = $dIva + $product_sale->tax;
                    $dSUmNeto = $dSUmNeto +number_format(($product_sale->qty * number_format( $product_sale->net_unit_cost,2)),2);
                            $detalleProductos[] = $detalleProducto;
                        }
                    }
        
                    $dIva = number_format($dIva, 2 );
                    $dSUmNeto = number_format($dSUmNeto);
                    $dTotal = $dIva + $dSUmNeto;
                    $sLetras = $this->numeroALetras($dTotal);
                    $reterenta = $dSUmNeto*.1;
                    $valorsujetot = $dSUmNeto - ($reterenta + $dIva);
        
                    $code_muni = $lims_customer_data->municipio->code;
                    $code_estado = $lims_customer_data->estado->code;
               $json_variable='
                            {
                                "nit": "'.$emisorNit.'",
                                "activo": "true",
                                "passwordPri": "'.$emisorprivatekey.'",
                                "dteJson": {
    
                                "identificacion": {
                                    "version": 1,
                                    "ambiente": "01",
                                    "tipoDte": "14",
                                    "numeroControl": "DTE-14-00000000-'.$numeroControlFormateado.'",
                                                    "codigoGeneracion": "'.strtoupper($uuid).'",
                                    "tipoModelo": 1,
                                    "tipoOperacion": 1,
                                    "tipoContingencia": null,
                                    "motivoContin": null,
                                    "fecEmi": "'.$fecEmi.'",
                                    "horEmi": "'.$horEmi.'",
                                    "tipoMoneda": "USD"
    
                                },
                                "emisor": {
                                    "nit": "'.$emisorNit.'",
                                    "nrc": "'.$emisorNrc.'",
                                    "nombre": "'.$emisorNombre.'",
                                    "codActividad": "'.$emisorCodActividad.'",
                                    "descActividad": "'.$emisorDescActividad.'",
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
                                "sujetoExcluido": {
                                "tipoDocumento":"13",
                                "numDocumento": "'.$dui.'",
                            
                                "nombre": "'.$nombre.'" ,
                                "codActividad": null,
                                "descActividad": null,
                                
                                "direccion": {
                                    "departamento": "'.$code_estado.'",
                                    "municipio": "'.$code_muni.'",
                                    "complemento": "'.$direccion.'"
                                },
                                "telefono": "'.$telefono.'",
                                "correo": "'.$email.'"
                                },
                                "cuerpoDocumento": [],
                                "resumen": {
    
                                    "totalDescu": 0,
                                    "subTotal": '.$dSUmNeto.',
                                
                                    "ivaRete1": '.$dIva.',
                                    "reteRenta": '.$reterenta.',
                                    "descu": 0,
                                    "totalCompra": '.$dSUmNeto.',
                                    "totalPagar": '.$valorsujetot.',
                                    "totalLetras": "'.$sLetras.'",
                                
                                    "condicionOperacion": 1,
                                    "pagos": [{
                                        "codigo": "01",
                                        "montoPago": '.$dSUmNeto.',
                                        "plazo": null,
                                        "referencia": null,
                                        "periodo": null
                                    }],
                                    "observaciones": ""
    
                                },
    
                                    
                                    "apendice": null
    
                                }
                                
                            }'; 
                                
                    
                          // fin
                        // Agregar el detalle de productos al JSON existente
                        $json_variable = str_replace('"cuerpoDocumento": []', '"cuerpoDocumento": ' . json_encode($detalleProductos), $json_variable);
                        
        
                     //echo($json_variable);
                   //exit;
             $sResult =    $this->processRequest($json_variable);
             
             $responseArray = json_decode($sResult, true);
      
             if (strpos(json_encode($responseArray), 'Bad Request') !== false) {
                echo "error al trasmitir";
                exit();
            } else {
             
       
                $dataresult  = json_decode($sResult, true);
                $data = json_decode($json_variable, true);
                //$tipoDte = $data['dteJson']['identificacion']['tipoDte'];
                $numeroControl1 = $data['dteJson']['identificacion']['numeroControl'];
                $fechae =  $data['dteJson']['identificacion']['fecEmi'];
          
                $sello = $dataresult['selloRecibido'];
                $estado = $dataresult['estado'];
      
                // $lims_sale_data->codgeneracionAnexo = $numeroControlAnexo;
                $sqr = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".$lims_sale_data->codgeneracion."&fechaEmi=".$fechae;
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
                $pdf1 = $this->genInvoice_pdf($id);
                //coloca una variable donde este el correo electronico dle cliente
    
                // Codifica nuevamente el JSON con la opción JSON_PRETTY_PRINT
                $prettyJson = json_encode($jsonData, JSON_PRETTY_PRINT);
    
                $this->enviarCorreoConImagenYTexto($email, "factura electronica", "Gracias por su compra a continuacion su qr" , $json_variable, $aqrl, $pdf1);
                $lims_sale_data->update();
    
                //echo  $aqrl;
                } 
                else {
                $lims_sale_data->sello = "NA";
                $lims_sale_data->estadodte = json_encode($dataresult);
                $lims_sale_data->update();  
    
                } 
    
                    $numberToWords = new NumberToWords();
                    if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
                        $numberTransformer = $numberToWords->getNumberTransformer('en');
                    else
                        $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
                    $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);
                    if ($estado =="PROCESADO")
                    {
    
                    }
                    else
                    {
                        echo json_encode($dataresult);
                        exit;
                    }
                }
           }

           public function dowjson(Request $request) {
            $id = $request->input('id');
      
            $numeroControlAnexo =  "";
            $lims_sale_data = Sexcluded::find($id);

      
            $codigoGeneracion = $lims_sale_data->codgeneracion;
            $numerocontroldte = $lims_sale_data->numerocontrol;
 

            $lims_product_sale_data = ProductSexcluded::where('sexcluded_id', $id)->get();
     
            $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
            $lims_customer_data = Excluded::find($lims_sale_data->excluded_id);
    
            $nit = $lims_customer_data->nit ;
            $nombre = $lims_customer_data->name ;
            $direccion = $lims_customer_data->address ;
            $dui = $lims_customer_data->dui ;
    
            $telefono = $lims_customer_data->phone ;
            $email = $lims_customer_data->email ;
            $estado1 = $lims_customer_data->estado ;
            $municipio = $lims_customer_data->municipio ;




           

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


        
            

            //documento de credi fiscal

   

        /*AQUI CONSTRUIREMOS JSON PARA ENVIO DE FACTURACION ELECTRONICA*/

        $detalleProductos = [];
        $dIva = 0.00;
        $dSUmNeto = 0.00;
        $customerName = '';
            // Obtener los productos relacionados con la venta
            foreach ($lims_product_sale_data as $index => $product_sale) {
                $product = product::find($product_sale->product_id); // Obtener el producto correspondiente
                
                // Verificar si el producto existe
                if ($product) {

                    $sale = Sexcluded::find($product_sale->sexcluded_id); // Obtener la venta correspondiente
              
                    
               $preciouni_r = number_format( $product_sale->net_unit_cost,2);
               //convertir $preciouni_r a double 
                 $preciouni_r = doubleval($preciouni_r);

               $compra_r =number_format(($product_sale->qty * number_format( $product_sale->net_unit_cost,2)),2);      
               //convertir $compra_r a double pero de dos decimales 

                 $compra_r = doubleval($compra_r);

                    $detalleProducto = [
                        "numItem" => $index + 1, // Número secuencial del item
                        "tipoItem" => 1, // Tipo de item (en este caso, 1 para productos)                        
                        "codigo" => $product->code, // Código del producto obtenido de la clase Product
                        "descripcion" => $product->name, // Nombre del producto obtenido de la clase Product
                        "cantidad" => $product_sale->qty, // Cantidad del producto
                        "uniMedida" => 59, // Unidad de medida del producto
                        "precioUni" => $preciouni_r, // Precio unitario del producto
                        "montoDescu" => 0,
                        "compra" => $compra_r // Precio total del producto
                    
                    ];
            $dIva = $dIva + $product_sale->tax;
            $dSUmNeto = $dSUmNeto +number_format(($product_sale->qty * number_format( $product_sale->net_unit_cost,2)),2);
                    $detalleProductos[] = $detalleProducto;
                }
            }

            $dIva = number_format($dIva, 2 );
            $dSUmNeto = number_format($dSUmNeto);
            $dTotal = $dIva + $dSUmNeto;
            $sLetras = $this->numeroALetras($dTotal);
            $reterenta = $dSUmNeto*.1;
            $valorsujetot = $dSUmNeto - ($reterenta + $dIva);

            $code_muni = $lims_customer_data->municipio->code;
            $code_estado = $lims_customer_data->estado->code;
       $json_variable='
               {

                        "identificacion": {
                            "version": 1,
                            "ambiente": "01",
                            "tipoDte": "14",
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
                        "emisor": {
                            "nit": "'.$emisorNit.'",
                            "nrc": "'.$emisorNrc.'",
                            "nombre": "'.$emisorNombre.'",
                            "codActividad": "'.$emisorCodActividad.'",
                            "descActividad": "'.$emisorDescActividad.'",
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
                        "sujetoExcluido": {
                        "tipoDocumento":"13",
                        "numDocumento": "'.$dui.'",
                    
                        "nombre": "'.$nombre.'" ,
                        "codActividad": null,
                        "descActividad": null,
                        
                        "direccion": {
                            "departamento": "'.$code_estado.'",
                            "municipio": "'.$code_muni.'",
                            "complemento": "'.$direccion.'"
                        },
                        "telefono": "'.$telefono.'",
                        "correo": "'.$email.'"
                        },
                        "cuerpoDocumento": [],
                        "resumen": {

                            "totalDescu": 0,
                            "subTotal": '.$dSUmNeto.',
                        
                            "ivaRete1": '.$dIva.',
                            "reteRenta": '.$reterenta.',
                            "descu": 0,
                            "totalCompra": '.$dSUmNeto.',
                            "totalPagar": '.$valorsujetot.',
                            "totalLetras": "'.$sLetras.'",
                        
                            "condicionOperacion": 1,
                            "pagos": [{
                                "codigo": "01",
                                "montoPago": '.$dSUmNeto.',
                                "plazo": null,
                                "referencia": null,
                                "periodo": null
                            }],
                            "observaciones": ""

                        },

                            
                            "apendice": null

                        
                        
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
            
    public function report(Request $request)
    {        
        $fecha_ini = $request->get('fecha_ini');
        $fecha_fin = $request->get('fecha_fin');

        $fi = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
        $ff = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:59';

        if($request->fecha_ini !=='')
        {
            $fi = Carbon::parse($fecha_ini)->format('Y-m-d'). ' 00:00:00';
            $ff = Carbon::parse($fecha_fin)->format('Y-m-d'). ' 23:59:59';
        }

        $sales = Sexcluded::join('excludeds','sexcludeds.excluded_id','=','excludeds.id')
                ->select(DB::raw('DATE_FORMAT(sexcludeds.created_at, "%d/%m/%Y") as formatted_dob'),'sexcludeds.reference_no',
                    DB::raw('@rownum := @rownum + 1 as rownum'),
                    'sexcludeds.created_at', 
                    'excludeds.name as cliente','sexcludeds.total_discount',DB::raw('round(sum((sexcludeds.grand_total-sexcludeds.total_tax)),2) as subtotal'),
                    DB::raw('round(sum((sexcludeds.total_tax)),2) as impuesto'),
                    DB::raw('round(sum((sexcludeds.grand_total/1.13)*sexcludeds.total_tax),2) as ivatercero'),
                    DB::raw('round(sexcludeds.grand_total,2) as total'),DB::raw('1 as tipo'))
                ->whereBetween('sexcludeds.created_at', [$fi, $ff])
                ->groupBy('sexcludeds.reference_no',
                    'sexcludeds.created_at','sexcludeds.total_tax',
                    'sexcludeds.grand_total','excludeds.name','sexcludeds.total_discount')
                ->orderBy('sexcludeds.id', 'asc')->get();

        //dd($sales);

       // $sales = Sale::join('customers','sales.customer_id','=','customers.id')
       //          ->select(DB::raw('DATE_FORMAT(sales.created_at, "%d/%m/%Y") as formatted_dob'),'sales.reference_no','sales.document_id',
       //              'sales.resolucion','sales.serie',DB::raw('@rownum := @rownum + 1 as rownum'),
       //              DB::raw('REPLACE(customers.tax_no, "-", "")'),
       //              'customers.name as cliente','sales.total_price',DB::raw('round(sum((sales.grand_total-sales.total_tax)/1.13),2) as subtotal'),
       //              DB::raw('round(sum(((sales.grand_total-sales.total_tax)/1.13)*sales.total_tax),2) as impuesto'),
       //              DB::raw('round(sum((sales.grand_total/1.13)*sales.total_tax),2) as ivatercero'),
       //              DB::raw('round(sales.grand_total-sales.total_tax,2) as total'),DB::raw('1 as tipo'))
       //          ->whereBetween('sales.created_at', [$fi, $ff])
       //          ->groupBy('sales.reference_no','document_id','sales.resolucion','sales.serie',
       //              'sales.created_at','sales.sale_status','sales.total_tax','customers.tax_no',
       //              'sales.grand_total','customers.name','sales.total_price')
       //          ->orderBy('sales.id', 'asc')->get();
            //dd($sales);

            $stotal = Sexcluded::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13;
            $gtotal = Sexcluded::whereBetween('created_at', [$fi, $ff])->sum('grand_total');
            $siva = Sexcluded::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13*0.13;
            $spercepcion = Sexcluded::whereBetween('created_at', [$fi, $ff])->sum('total_discount');
            $sexento = Sexcluded::whereBetween('created_at', [$fi, $ff])->sum('total_discount');
            $stotal_tax = Sexcluded::whereBetween('created_at', [$fi, $ff])->sum('total_tax');
            $total = Sexcluded::whereBetween('created_at', [$fi, $ff])->sum('grand_total');
            $timporta = Sexcluded::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13;

            return view('sexcluded.report',[
                "sales"=>$sales,
                "previo1"=>round($stotal,2),
                "previo"=>($gtotal-$sexento-$spercepcion)/1.13,
                "siva"=>(($total-$spercepcion)/1.13)*0.13,
                "sexento"=>$sexento,
                "spercepcion"=>$spercepcion,
                "simporta"=>$timporta,
                "sumaTotal"=>($total),]);
    }

    public function excel(Request $request)
    {
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

        $sexcludeds = Sexcluded::join('excludeds','sexcludeds.excluded_id','=','excludeds.id')
            ->join('product_sexcludeds','sexcludeds.id','=','product_sexcludeds.sexcluded_id')
            ->select('sexcludeds.id', DB::raw('DATE_FORMAT(sexcludeds.created_at, "%d/%m/%Y") as formatted_dob'),
                'sexcludeds.excluded_id', 'excludeds.name as cliente', 'excludeds.address', 'excludeds.phone', 'excludeds.dui', 'sexcludeds.reference_no',
                'product_sexcludeds.net_unit_cost as total', 'sexcludeds.total_discount as renta', 'sexcludeds.grand_total as gtotal',
                'sexcludeds.numeroControl', 'sexcludeds.codgeneracion', 'sexcludeds.sello')
            ->whereBetween('sexcludeds.created_at', [$fi, $ff])
            ->groupBy('sexcludeds.id', 'sexcludeds.reference_no', 'sexcludeds.created_at', 'sexcludeds.excluded_id',
                'excludeds.name', 'excludeds.address', 'excludeds.phone', 'excludeds.dui', 'product_sexcludeds.net_unit_cost',
                'sexcludeds.total_discount', 'sexcludeds.grand_total','sexcludeds.numeroControl', 'sexcludeds.codgeneracion', 'sexcludeds.sello')
            ->orderBy('sexcludeds.id', 'asc')
            ->get();
        //dd($sexcludeds);

        // $sales = Sale::join('customers','sales.customer_id','=','customers.id')
        //         ->join('product_sales','sales.id','=','product_sales.id')
        //         ->select('sales.id', DB::raw('DATE_FORMAT(sales.created_at, "%d/%m/%Y") as formatted_dob'), DB::raw('1 as tipo'),
        //             'sales.reference_no', 'customers.name as cliente', 'product_sales.description',
        //             DB::raw('round(sum((sales.grand_total)/1.13),2) as subtotal'),
        //             DB::raw('round(sum(sales.total_tax),2) as impuesto'),
        //             DB::raw('round(sales.grand_total,2) as total'), 'customers.code as code', 'sales.tercero as tipoe',
        //             'sales.numeroControl', 'sales.codgeneracion', 'sales.sello')
        //         ->whereBetween('sales.created_at', [$fi, $ff])
        //         ->groupBy('sales.id','sales.reference_no','document_id','sales.resolucion','sales.serie','product_sales.description',
        //             'sales.created_at','sales.sale_status','sales.total_tax','customers.tax_no', 'customers.code', 'sales.tercero',
        //             'sales.grand_total','customers.name','sales.total_price','sales.codgeneracion', 'sales.sello','sales.numeroControl')
        //         ->orderBy('sales.id', 'asc')->get();
                //dd($sales);

            //$stotal = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13;
            //$gtotal = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 0)->sum('grand_total');
            //$siva = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total')/1.13*0.13;
            //$sexento = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_price');
            //$stotal_tax = Sale::whereBetween('created_at', [$fi, $ff])->sum('total_tax');
            //$total = Sale::whereBetween('created_at', [$fi, $ff])->sum('grand_total');
            //$timporta = Sale::whereBetween('created_at', [$fi, $ff])->where('document_id', 1)->sum('grand_total')/1.13;
            //dd($sales);

        return (new ExcludesExport($sexcludeds))->download('exluidostr.xlsx');
    }

}
    