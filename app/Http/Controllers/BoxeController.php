<?php

namespace App\Http\Controllers;

use App\Boxe;
use App\BoxeDetail;
use App\Warehouse;
use App\Biller;
use App\Ticket;
use App\CashRegister;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use DB;


class BoxeController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        
        if($role->hasPermissionTo('boxcuts-index')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
                        
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_box_cut_all = Boxe::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_box_cut_all = Boxe::orderBy('id', 'desc')->get();
            return view('boxcut.index', compact('lims_box_cut_all', 'all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        
        if($role->hasPermissionTo('boxcuts-add')){
            
            if(Auth::user()->role_id <= 2) {
                $lims_warehouse_list = Warehouse::where('is_active',true)->get();
                $lims_biller_list = Biller::where('is_active',true)->get();
            }
            else {
                $lims_warehouse_list = Warehouse::where('id',Auth::user()->warehouse_id)->get();
                $lims_biller_list = Biller::where('id', Auth::user()->biller_id)->get();
            }
            $lims_ticket_list=DB::table('tickets as prod')
                ->select(DB::raw('CONCAT(prod.name) AS producto'),'prod.id','prod.base_unit')
                ->groupBy('producto','prod.id','base_unit')                
                ->orderBy('id', 'desc')
                ->get();
            
            return view('boxcut.create', compact('lims_warehouse_list', 'lims_biller_list', 'lims_ticket_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $mytime= Carbon::now('America/Costa_Rica');

            $data = $request->all();

            $data['reference_no'] = 'bo-' . date("Ymd") . '-'. date("his");
            $data['user_id'] = Auth::id();
            $cash_register_data = CashRegister::where([
                ['user_id', $data['user_id']],
                ['warehouse_id', $data['warehouse_id']],
                ['status', true]
            ])->first();

            if($cash_register_data)
                $data['cash_register_id'] = $cash_register_data->id;

            $lims_sale_data = Boxe::create($data);

            $id_producto=$request->id_producto;
            $cantidad=$request->qty_producto;
            $descuento=$request->total_dinero;

            //Recorro todos los elementos
            $cont=0;

            while($cont < count($id_producto)){

                /*enviamos valores a las propiedades del objeto detalle*/
                /*al idcompra del objeto detalle le envio el id del objeto venta, que es el objeto que se ingresó en la tabla ventas de la bd*/
                /*el id es del registro de la venta*/
                $detalle = new BoxeDetail();
                $detalle->boxe_id = $lims_sale_data->id;
                $detalle->ticket_id = $id_producto[$cont];
                $detalle->qty_producto = $cantidad[$cont];
                $detalle->total_dinero = $descuento[$cont];
                $detalle->save();
                $cont=$cont+1;
            }
            DB::commit();

        } catch(Exception $e){

            DB::rollBack();
        }

        return Redirect::to('boxcuts');
    }   

    
}
