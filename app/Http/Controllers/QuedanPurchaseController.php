<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use DateTime;
use App\Biller;
use App\Purchase;
use App\Supplier;
use App\Warehouse;
use App\CashRegister;
use App\GeneralSetting;
use App\QuedanPurchase;
use App\Quedanxpurchase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QuedanPurchaseController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        //dd($role);
        if($role->hasPermissionTo('quedanpurchase-index')){           
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_quedan_list = QuedanPurchase::orderBy('id', 'desc')->get();
            else
                $lims_quedan_list = DB::select('  SELECT id,date_quedan,supplier_id,STATUS,total,due_date,created_at, updated_at 
                     , 
                     ifnull((SELECT GROUP_CONCAT(s.invoice) FROM `quedanxpurchases` qs 
                        INNER JOIN purchases s ON s.id=  qs.purchase_id 
                        WHERE quedan_id = q.id),"") AS number_invoice
                     FROM quedan_purchases q
                        order by date_quedan desc
                      ');   

            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
                if(empty($all_permission))
                    $all_permission[] = 'dummy text';
            
                    $lims_client_list = Supplier::where('is_active', true)->get(); 

                    $datos_facturas = DB::select('  SELECT a.id, invoice,grand_total, b.name
                        FROM purchases  a
                        inner join suppliers b where a.supplier_id=b.id
                            and a.id not in(
                            select purchase_id from quedanxpurchases
                        ) 
                    ');

            return view('quedan_purchase.index', compact('lims_quedan_list', 'all_permission','lims_client_list','datos_facturas'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($supplier_id=0)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('quedanpurchase-add')){         
            $lims_client_list = Supplier::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            
            $datos_nc = DB::select('  SELECT a.id, invoice,grand_total, b.name
                                FROM return_purchases  a
                                inner join suppliers b where a.supplier_id=b.id
                                and a.id not in(
                                    select return_id from quedanxpurchases
                                ) 
                                and a.supplier_id='.$supplier_id);


            $datos_facturas = DB::select('  SELECT a.id, invoice,grand_total, b.name
                                FROM purchases  a
                                inner join suppliers b where a.supplier_id=b.id
                                and a.id not in(
                                    select purchase_id from quedanxpurchases
                                ) 
                                and a.supplier_id='.$supplier_id);     

            return view('quedan_purchase.create', compact('lims_client_list','datos_facturas', 'supplier_id', 'lims_warehouse_list', 'datos_nc'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
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
        $invoices = $data["number_invoice"];
        if( isset($data["number_nc"]) ) {
            $notasnc = $data["number_nc"];    
        }else {
            $notasnc = "";
        }
        
        $data['reference_no'] = 'qd-' . date("Ymd") . '-'. date("his");
        //dd($notasnc);
        $data["status"] =1; 
        $data["number_invoice"] ="";        
        $data["number_nc"] ="";        
        $data["warehouse_id"] = $request->warehouse_id;

        $id =  QuedanPurchase::create($data)->id;
  
        foreach ($invoices  as $key ) {
            $data["quedan_id"]= $id;
            $data["purchase_id"] =$key;
            $data["return_id"] = 0; 

            quedanxpurchase::create($data);
        }

        //$id =  QuedanPurchase::create($data)->id;

        if (! empty($notasnc)) {
            foreach ($notasnc  as $key ) {
                $data["purchase_id"] = 0;
                $data["quedan_id"]= $id;
                $data["return_id"] =$key; 

                quedanxpurchase::create($data);
            }                 
        }

        return redirect('quedan_purchase')->with('message', "Quedan por Compra registrado Exitosamente");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\QuedanPurchase  $quedanPurchase
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\QuedanPurchase  $quedanPurchase
     * @return \Illuminate\Http\Response
     */
    public function edit(QuedanPurchase $quedanPurchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\QuedanPurchase  $quedanPurchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuedanPurchase $quedanPurchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QuedanPurchase  $quedanPurchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuedanPurchase $quedanPurchase)
    {
        //
    }

    public function update_quedan_purchase()
    {
        $id= $_POST["quedan_id"];
        $lims_quedan_data = QuedanPurchase::find($id);
        
        $data["id"] = $id;
        $data["date_quedan"] = $_POST['date_quedan'];
        $invoices = $_POST['number_invoice_2'];
        $data["supplier_id"] = $_POST['supplier_id'];
 
        DB::select('  delete from quedanxpurchases where id_quedan = '.$id);

        foreach ($invoices  as $key ) {
            $data2["quedan_id"]= $id;
            $data2["purchase_id"] =$key; 

            Quedanxpurchase::create($data2);
        }

        $data["total"] = $_POST['total'];
        $data["due_date"] = $_POST['due_date'];

        /*Este update estaba al final pero lo necesito antes para que la variable de estado se llene con el valor del combo.*/
        $lims_quedan_data->update($data);
    }

    public function buscar_facturas_purchase()
    {
        $id= $_POST["customer_id"];
        $lims_quedan_data = Customer::find($id);

        $data["id"] = $id;
        $data["date_quedan"] = $_POST['date_quedan'];
        $data["number_invoice"] = $_POST['number_invoice'];
        $data["supplier_id"] = $_POST['supplier_id'];
        $data["total"] = $_POST['total'];
        $data["due_date"] = $_POST['due_date'];

        /*Este update estaba al final pero lo necesito antes para que la variable de estado se llene con el valor del combo.*/
        $lims_quedan_data->update($data);
    }
    
    public function obtener_facturas_purchase()
    {
        $supplier_id= $_POST["supplier_id"];
    
        $datos_facturas = DB::select('SELECT a.id, reference_no, grand_total, b.name
                            FROM purchases  a
                            inner join suppliers b where a.supplier_id=b.id
                            and a.id not in(
                             select purchase_id from quedanxpurchases
                            ) 
                            and a.supplier_id ='.$supplier_id);

        $html =""; 
        foreach ($datos_facturas as $key ) {
            $html .="<option data-monto=".$key->grand_total." value=".$key->id.">".$key->reference_no.'-'.$key->name.'-'.$key->grand_total."</opton>";
        }
  
        echo $html; 
    }
    
    public function productReturnData($id)
    {
        $lims_product_return_data = QuedanPurchase::where('quedan_id', $id)->get();

        dd($lims_product_return_data);

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

    protected $namespace = 'App\Http\Controllers';

    public function genQuedan($id)
    {
        
        $lims_sale_data = QuedanPurchase::find($id);
        $lims_biller_data = Biller::find($lims_sale_data->biller_id);
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);
        
        $codigoqr = $lims_sale_data->reference_no;
        //$codigoqr = QrCode::size(100)->generate($aqrl);
       
        $lims_quedan_data = QuedanPurchase::join('suppliers','quedan_purchases.supplier_id','=','suppliers.id')
            ->join('quedanxpurchases','quedan_purchases.id','quedanxpurchases.quedan_id')
            ->select('quedan_purchases.id','quedan_purchases.date_quedan', 'quedan_purchases.due_date', 'quedan_purchases.total', 'suppliers.name', 'quedan_purchases.reference_no')
            ->where('quedan_purchases.id','=',$id)
            ->groupBy('quedan_purchases.id','quedan_purchases.date_quedan', 'quedan_purchases.due_date', 'quedan_purchases.total', 'suppliers.name', 'quedan_purchases.reference_no')
            ->get();

        $lims_detalle_quedan_data = QuedanPurchase::join('suppliers','quedan_purchases.supplier_id','=','suppliers.id')
            ->join('quedanxpurchases','quedan_purchases.id','quedanxpurchases.quedan_id') 
            ->join('purchases','quedanxpurchases.purchase_id','purchases.id') 
            ->select('quedan_purchases.id','quedan_purchases.date_quedan', 'suppliers.name',
             'quedanxpurchases.purchase_id', 'purchases.created_at', 'purchases.invoice', 'purchases.grand_total')
            ->where('quedan_purchases.id','=',$id)
            ->groupBy('quedan_purchases.id','quedan_purchases.date_quedan', 'suppliers.name', 
             'quedanxpurchases.purchase_id', 'purchases.created_at', 'purchases.invoice', 'purchases.grand_total')
            ->get();

        $lims_detalle_quedan_nc_data = QuedanPurchase::join('suppliers','quedan_purchases.supplier_id','=','suppliers.id')
            ->join('quedanxpurchases','quedan_purchases.id','quedanxpurchases.quedan_id') 
            ->join('return_purchases','quedanxpurchases.return_id','return_purchases.id') 
            ->select('quedan_purchases.id','quedan_purchases.date_quedan', 'suppliers.name',
             'quedanxpurchases.return_id', 'return_purchases.created_at', 'return_purchases.invoice', 'return_purchases.grand_total')
            ->where('quedan_purchases.id','=',$id)
            ->groupBy('quedan_purchases.id','quedan_purchases.date_quedan', 'suppliers.name', 
             'quedanxpurchases.return_id', 'return_purchases.created_at', 'return_purchases.invoice', 'return_purchases.grand_total')
            ->get();

        return view('quedan_purchase.pdf', compact('lims_quedan_data', 'lims_detalle_quedan_data','lims_sale_data','lims_biller_data','lims_warehouse_data', 'lims_detalle_quedan_nc_data', 'codigoqr'));
    }
}
