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
use App\Quedan;
use App\quedanxsale;
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
use DateTime;
use App\Customer;

class QuedanController extends Controller
{
   

 public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('quedan-index')){            
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_quedan_list = Quedan::orderBy('id', 'desc')->get();
            else
               
                // $lims_quedan_list = Quedan::orderBy('id', 'desc')->get();
                  $lims_quedan_list = DB::select('  SELECT id,date_quedan,customer_id,STATUS,total,due_date,created_at, updated_at 
                     , 
                     ifnull((SELECT GROUP_CONCAT(s.reference_no) FROM `quedanxsales` qs 
                        INNER JOIN sales s ON s.id=  qs.id_sale 
                        WHERE id_quedan = q.id),"") AS number_invoice
                     FROM quedans q
                        order by date_quedan desc
                      ');   


                 $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            //$lims_pos_setting_data = PosSetting::latest()->first();
           // $lims_account_list = Account::where('is_active', true)->get();
             $lims_client_list = Customer::where('is_active', true)->get();

             $datos_facturas = DB::select('  SELECT a.id, reference_no,grand_total, b.name
                            FROM sales  a
                            inner join customers b where a.customer_id=b.id
                            and a.id not in(
                             select id_sale from quedanxsales
                            ) 
                           ');

            return view('quedan.index', compact('lims_quedan_list', 'all_permission','lims_client_list','datos_facturas'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }



    public function create($customer_id=0)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('quedan-add')){
         
            $lims_client_list = Customer::where('is_active', true)->get();
            
            $datos_facturas = DB::select('  SELECT a.id, reference_no,grand_total, b.name
                            FROM sales  a
                            inner join customers b where a.customer_id=b.id
                            and a.id not in(
                             select id_sale from quedanxsales
                            ) 
                            and a.customer_id='.$customer_id);     


            return view('quedan.create', compact('lims_client_list','datos_facturas', 'customer_id'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }


  public function store(Request $request)
    {
         $data = $request->except('document');
         $invoices = $data["number_invoice"];
         
         $data["status"] =1; 
         $data["number_invoice"] =""; 

          $id =  Quedan::create($data)->id;
  

        foreach ($invoices  as $key ) {
                   $data["id_quedan"]= $id;
                   $data["id_sale"] =$key; 

             quedanxsale::create($data);
        }

           

        return redirect('quedan')->with('message', "Quedan registrado");
    }
    


    public function destroy($id)
    {
        $lims_quedan_data = Quedan::find($id);
        $lims_quedan_data->delete();
        return redirect('quedan')->with('not_permitted', 'Quedan eliminado');
    }



  public function update_quedan(){
   
    $id= $_POST["quedan_id"];
    $lims_quedan_data = Quedan::find($id);
        

        $data["id"] = $id;
        $data["date_quedan"] = $_POST['date_quedan'];
        $invoices = $_POST['number_invoice_2'];
        $data["customer_id"] = $_POST['customer_id'];
 

        DB::select('  delete from quedanxsales where id_quedan= '.$id);

        foreach ($invoices  as $key ) {
                   $data2["id_quedan"]= $id;
                   $data2["id_sale"] =$key; 

             quedanxsale::create($data2);
        }


       // $data["status"] = $_POST['status'];
        $data["total"] = $_POST['total'];
        $data["due_date"] = $_POST['due_date'];

        /*Este update estaba al final pero lo necesito antes para que la variable de estado se llene con el 
        valor del combo.
        */
        $lims_quedan_data->update($data);

  }


  public function buscar_facturas(){
   
    $id= $_POST["customer_id"];
    $lims_quedan_data = Customer::find($id);
      /*  
  $lims_quedan_data = DB::select('
                SELECT a.created_at, 
                s.`reference_no`,
                a.`qty`,
                b.`name`,
                "" AS lote,
                b.`name`,
                a.`unit_cost`,
                a.unit_cost*a.qty AS costo_total,
                a.`net_unit_price`,
                a.`net_unit_price`*a.`qty` AS precio_total,
                a.gain,
                c.name
                 FROM `product_sales` a
                 INNER JOIN sales s ON s.id= a.sale_id
                INNER JOIN products b ON a.product_id = b.id
                left join customers c on c.id= s.customer_id
                WHERE CONVERT(a.created_at,DATE) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
               '.$where);
  */

        $data["id"] = $id;
        $data["date_quedan"] = $_POST['date_quedan'];
        $data["number_invoice"] = $_POST['number_invoice'];
        $data["customer_id"] = $_POST['customer_id'];
       // $data["status"] = $_POST['status'];
        $data["total"] = $_POST['total'];
        $data["due_date"] = $_POST['due_date'];

        /*Este update estaba al final pero lo necesito antes para que la variable de estado se llene con el 
        valor del combo.
        */
        $lims_quedan_data->update($data);

  }



  public function obtener_facturas(){
   
    $customer_id= $_POST["customer_id"];


    
    $datos_facturas = DB::select('  SELECT a.id, reference_no,grand_total, b.name
                            FROM sales  a
                            inner join customers b where a.customer_id=b.id
                            and a.id not in(
                             select id_sale from quedanxsales
                            ) 
                            and a.customer_id ='.$customer_id);


  $html =""; 
  foreach ($datos_facturas as $key ) {
      $html .="<option data-monto=".$key->grand_total." value=".$key->id.">".$key->reference_no.'-'.$key->name.'-'.$key->grand_total."</opton>";
    }
  
  echo $html; 

  }




}
