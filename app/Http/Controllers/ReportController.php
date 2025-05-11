<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductPurchase;
use App\Product_Sale;
use App\ProductQuotation;
use App\Sale;
use App\Purchase;
use App\Quotation;
use App\Transfer;
use App\Returns;
use App\ProductReturn;
use App\ReturnPurchase;
use App\ProductTransfer;
use App\PurchaseProductReturn;
use App\Payment;
use App\Warehouse;
use App\Product_Warehouse;
use App\Expense;
use App\Payroll;
use App\User;
use App\Customer;
use App\Supplier;
use App\Variant;
use App\ProductVariant;
use App\Boxe;
use DB;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ReportController extends Controller
{
    public function productQuantityAlert()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('product-qty-alert')){
            $lims_product_data = Product::select('name','code', 'image', 'qty', 'alert_quantity')->where('is_active', true)->whereColumn('alert_quantity', '>', 'qty')->get();
            return view('report.qty_alert_report', compact('lims_product_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function warehouseStock()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('warehouse-stock-report')){
            $total_item = DB::table('product_warehouse')
                        ->join('products', 'product_warehouse.product_id', '=', 'products.id')
                        ->where([
                            ['products.is_active', true],
                            ['product_warehouse.qty', '>' , 0]
                        ])->count();

            $total_qty = Product::where('is_active', true)->sum('qty');
            $total_price = DB::table('products')->where('is_active', true)->sum(DB::raw('price * qty'));
            $total_cost = DB::table('products')->where('is_active', true)->sum(DB::raw('cost * qty'));
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $warehouse_id = 0;
            return view('report.warehouse_stock', compact('total_item', 'total_qty', 'total_price', 'total_cost', 'lims_warehouse_list', 'warehouse_id'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function warehouseStockById(Request $request)
    {
        $data = $request->all();
        if($data['warehouse_id'] == 0)
            return redirect()->back();

        $total_item = DB::table('product_warehouse')
                        ->join('products', 'product_warehouse.product_id', '=', 'products.id')
                        ->where([
                            ['products.is_active', true],
                            ['product_warehouse.qty', '>' , 0],
                            ['product_warehouse.warehouse_id', $data['warehouse_id']]
                        ])->count();
        $total_qty = DB::table('product_warehouse')
                        ->join('products', 'product_warehouse.product_id', '=', 'products.id')
                        ->where([
                            ['products.is_active', true],
                            ['product_warehouse.warehouse_id', $data['warehouse_id']]
                        ])->sum('product_warehouse.qty');
        $total_price = DB::table('product_warehouse')
                        ->join('products', 'product_warehouse.product_id', '=', 'products.id')
                        ->where([
                            ['products.is_active', true],
                            ['product_warehouse.warehouse_id', $data['warehouse_id']]
                        ])->sum(DB::raw('products.price * product_warehouse.qty'));
        $total_cost = DB::table('product_warehouse')
                        ->join('products', 'product_warehouse.product_id', '=', 'products.id')
                        ->where([
                            ['products.is_active', true],
                            ['product_warehouse.warehouse_id', $data['warehouse_id']]
                        ])->sum(DB::raw('products.cost * product_warehouse.qty'));
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $warehouse_id = $data['warehouse_id'];
        return view('report.warehouse_stock', compact('total_item', 'total_qty', 'total_price', 'total_cost', 'lims_warehouse_list', 'warehouse_id'));
    }

    public function dailySale($year, $month)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('daily-sale')){
            $start = 1;
            $number_of_day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
            while($start <= $number_of_day)
            {
                if($start < 10)
                    $date = $year.'-'.$month.'-0'.$start;
                else
                    $date = $year.'-'.$month.'-'.$start;
                $query1 = array(
                    'SUM(total_discount) AS total_discount',
                    'SUM(order_discount) AS order_discount',
                    'SUM(total_tax) AS total_tax',
                    'SUM(order_tax) AS order_tax',
                    'SUM(shipping_cost) AS shipping_cost',
                    'SUM(grand_total) AS grand_total'
                );
                $sale_data = Sale::whereDate('created_at', $date)->selectRaw(implode(',', $query1))->get();
                $total_discount[$start] = $sale_data[0]->total_discount;
                $order_discount[$start] = $sale_data[0]->order_discount;
                $total_tax[$start] = $sale_data[0]->total_tax;
                $order_tax[$start] = $sale_data[0]->order_tax;
                $shipping_cost[$start] = $sale_data[0]->shipping_cost;
                $grand_total[$start] = $sale_data[0]->grand_total;
                $start++;
            }
            $start_day = date('w', strtotime($year.'-'.$month.'-01')) + 1;
            $prev_year = date('Y', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
            $prev_month = date('m', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
            $next_year = date('Y', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
            $next_month = date('m', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $warehouse_id = 0;
            return view('report.daily_sale', compact('total_discount','order_discount', 'total_tax', 'order_tax', 'shipping_cost', 'grand_total', 'start_day', 'year', 'month', 'number_of_day', 'prev_year', 'prev_month', 'next_year', 'next_month', 'lims_warehouse_list', 'warehouse_id'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function dailySaleByWarehouse(Request $request,$year,$month)
    {
        $data = $request->all();
        if($data['warehouse_id'] == 0)
            return redirect()->back();
        $start = 1;
        $number_of_day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        while($start <= $number_of_day)
        {
            if($start < 10)
                $date = $year.'-'.$month.'-0'.$start;
            else
                $date = $year.'-'.$month.'-'.$start;
            $query1 = array(
                'SUM(total_discount) AS total_discount',
                'SUM(order_discount) AS order_discount',
                'SUM(total_tax) AS total_tax',
                'SUM(order_tax) AS order_tax',
                'SUM(shipping_cost) AS shipping_cost',
                'SUM(grand_total) AS grand_total'
            );
            $sale_data = Sale::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', $date)->selectRaw(implode(',', $query1))->get();
            $total_discount[$start] = $sale_data[0]->total_discount;
            $order_discount[$start] = $sale_data[0]->order_discount;
            $total_tax[$start] = $sale_data[0]->total_tax;
            $order_tax[$start] = $sale_data[0]->order_tax;
            $shipping_cost[$start] = $sale_data[0]->shipping_cost;
            $grand_total[$start] = $sale_data[0]->grand_total;
            $start++;
        }
        $start_day = date('w', strtotime($year.'-'.$month.'-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $warehouse_id = $data['warehouse_id'];
        return view('report.daily_sale', compact('total_discount','order_discount', 'total_tax', 'order_tax', 'shipping_cost', 'grand_total', 'start_day', 'year', 'month', 'number_of_day', 'prev_year', 'prev_month', 'next_year', 'next_month', 'lims_warehouse_list', 'warehouse_id'));

    }

    public function dailyPurchase($year, $month)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('daily-purchase')){
            $start = 1;
            $number_of_day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
            while($start <= $number_of_day)
            {
                if($start < 10)
                    $date = $year.'-'.$month.'-0'.$start;
                else
                    $date = $year.'-'.$month.'-'.$start;
                $query1 = array(
                    'SUM(total_discount) AS total_discount',
                    'SUM(order_discount) AS order_discount',
                    'SUM(total_tax) AS total_tax',
                    'SUM(order_tax) AS order_tax',
                    'SUM(shipping_cost) AS shipping_cost',
                    'SUM(grand_total) AS grand_total'
                );
                $purchase_data = Purchase::whereDate('created_at', $date)->selectRaw(implode(',', $query1))->get();
                $total_discount[$start] = $purchase_data[0]->total_discount;
                $order_discount[$start] = $purchase_data[0]->order_discount;
                $total_tax[$start] = $purchase_data[0]->total_tax;
                $order_tax[$start] = $purchase_data[0]->order_tax;
                $shipping_cost[$start] = $purchase_data[0]->shipping_cost;
                $grand_total[$start] = $purchase_data[0]->grand_total;
                $start++;
            }
            $start_day = date('w', strtotime($year.'-'.$month.'-01')) + 1;
            $prev_year = date('Y', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
            $prev_month = date('m', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
            $next_year = date('Y', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
            $next_month = date('m', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $warehouse_id = 0;
            return view('report.daily_purchase', compact('total_discount','order_discount', 'total_tax', 'order_tax', 'shipping_cost', 'grand_total', 'start_day', 'year', 'month', 'number_of_day', 'prev_year', 'prev_month', 'next_year', 'next_month', 'lims_warehouse_list', 'warehouse_id'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function dailyPurchaseByWarehouse(Request $request, $year, $month)
    {        
        $data = $request->all();
        if($data['warehouse_id'] == 0)
            return redirect()->back();
        $start = 1;
        $number_of_day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        while($start <= $number_of_day)
        {
            if($start < 10)
                $date = $year.'-'.$month.'-0'.$start;
            else
                $date = $year.'-'.$month.'-'.$start;
            $query1 = array(
                'SUM(total_discount) AS total_discount',
                'SUM(order_discount) AS order_discount',
                'SUM(total_tax) AS total_tax',
                'SUM(order_tax) AS order_tax',
                'SUM(shipping_cost) AS shipping_cost',
                'SUM(grand_total) AS grand_total'
            );
            $purchase_data = Purchase::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', $date)->selectRaw(implode(',', $query1))->get();
            $total_discount[$start] = $purchase_data[0]->total_discount;
            $order_discount[$start] = $purchase_data[0]->order_discount;
            $total_tax[$start] = $purchase_data[0]->total_tax;
            $order_tax[$start] = $purchase_data[0]->order_tax;
            $shipping_cost[$start] = $purchase_data[0]->shipping_cost;
            $grand_total[$start] = $purchase_data[0]->grand_total;
            $start++;
        }
        $start_day = date('w', strtotime($year.'-'.$month.'-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $warehouse_id = $data['warehouse_id'];

        return view('report.daily_purchase', compact('total_discount','order_discount', 'total_tax', 'order_tax', 'shipping_cost', 'grand_total', 'start_day', 'year', 'month', 'number_of_day', 'prev_year', 'prev_month', 'next_year', 'next_month', 'lims_warehouse_list', 'warehouse_id'));
    }

    public function monthlySale($year)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('monthly-sale')){
            $start = strtotime($year .'-01-01');
            $end = strtotime($year .'-12-31');
            while($start <= $end)
            {
                $start_date = $year . '-'. date('m', $start).'-'.'01';
                $end_date = $year . '-'. date('m', $start).'-'.'31';

                $temp_total_discount = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('total_discount');
                $total_discount[] = number_format((float)$temp_total_discount, 2, '.', '');

                $temp_order_discount = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('order_discount');
                $order_discount[] = number_format((float)$temp_order_discount, 2, '.', '');

                $temp_total_tax = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('total_tax');
                $total_tax[] = number_format((float)$temp_total_tax, 2, '.', '');

                $temp_order_tax = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('order_tax');
                $order_tax[] = number_format((float)$temp_order_tax, 2, '.', '');

                $temp_shipping_cost = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('shipping_cost');
                $shipping_cost[] = number_format((float)$temp_shipping_cost, 2, '.', '');

                $temp_total = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('grand_total');
                $total[] = number_format((float)$temp_total, 2, '.', '');
                $start = strtotime("+1 month", $start);
            }
            $lims_warehouse_list = Warehouse::where('is_active',true)->get();
            $warehouse_id = 0;
            return view('report.monthly_sale', compact('year', 'total_discount', 'order_discount', 'total_tax', 'order_tax', 'shipping_cost', 'total', 'lims_warehouse_list', 'warehouse_id'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function monthlySaleByWarehouse(Request $request, $year)
    {
        $data = $request->all();
        if($data['warehouse_id'] == 0)
            return redirect()->back();

        $start = strtotime($year .'-01-01');
        $end = strtotime($year .'-12-31');
        while($start <= $end)
        {
            $start_date = $year . '-'. date('m', $start).'-'.'01';
            $end_date = $year . '-'. date('m', $start).'-'.'31';

            $temp_total_discount = Sale::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('total_discount');
            $total_discount[] = number_format((float)$temp_total_discount, 2, '.', '');

            $temp_order_discount = Sale::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('order_discount');
            $order_discount[] = number_format((float)$temp_order_discount, 2, '.', '');

            $temp_total_tax = Sale::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('total_tax');
            $total_tax[] = number_format((float)$temp_total_tax, 2, '.', '');

            $temp_order_tax = Sale::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('order_tax');
            $order_tax[] = number_format((float)$temp_order_tax, 2, '.', '');

            $temp_shipping_cost = Sale::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('shipping_cost');
            $shipping_cost[] = number_format((float)$temp_shipping_cost, 2, '.', '');

            $temp_total = Sale::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('grand_total');
            $total[] = number_format((float)$temp_total, 2, '.', '');
            $start = strtotime("+1 month", $start);
        }
        $lims_warehouse_list = Warehouse::where('is_active',true)->get();
        $warehouse_id = $data['warehouse_id'];
        return view('report.monthly_sale', compact('year', 'total_discount', 'order_discount', 'total_tax', 'order_tax', 'shipping_cost', 'total', 'lims_warehouse_list', 'warehouse_id'));
    }

    public function monthlyPurchase($year)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('monthly-purchase')){
            $start = strtotime($year .'-01-01');
            $end = strtotime($year .'-12-31');
            while($start <= $end)
            {
                $start_date = $year . '-'. date('m', $start).'-'.'01';
                $end_date = $year . '-'. date('m', $start).'-'.'31';

                $query1 = array(
                    'SUM(total_discount) AS total_discount',
                    'SUM(order_discount) AS order_discount',
                    'SUM(total_tax) AS total_tax',
                    'SUM(order_tax) AS order_tax',
                    'SUM(shipping_cost) AS shipping_cost',
                    'SUM(grand_total) AS grand_total'
                );
                $purchase_data = Purchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query1))->get();
                
                $total_discount[] = number_format((float)$purchase_data[0]->total_discount, 2, '.', '');
                $order_discount[] = number_format((float)$purchase_data[0]->order_discount, 2, '.', '');
                $total_tax[] = number_format((float)$purchase_data[0]->total_tax, 2, '.', '');
                $order_tax[] = number_format((float)$purchase_data[0]->order_tax, 2, '.', '');
                $shipping_cost[] = number_format((float)$purchase_data[0]->shipping_cost, 2, '.', '');
                $grand_total[] = number_format((float)$purchase_data[0]->grand_total, 2, '.', '');
                $start = strtotime("+1 month", $start);
            }
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $warehouse_id = 0;
            return view('report.monthly_purchase', compact('year', 'total_discount', 'order_discount', 'total_tax', 'order_tax', 'shipping_cost', 'grand_total', 'lims_warehouse_list', 'warehouse_id'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function monthlyPurchaseByWarehouse(Request $request, $year)
    {
        $data = $request->all();
        if($data['warehouse_id'] == 0)
            return redirect()->back();

        $start = strtotime($year .'-01-01');
        $end = strtotime($year .'-12-31');
        while($start <= $end)
        {
            $start_date = $year . '-'. date('m', $start).'-'.'01';
            $end_date = $year . '-'. date('m', $start).'-'.'31';

            $query1 = array(
                'SUM(total_discount) AS total_discount',
                'SUM(order_discount) AS order_discount',
                'SUM(total_tax) AS total_tax',
                'SUM(order_tax) AS order_tax',
                'SUM(shipping_cost) AS shipping_cost',
                'SUM(grand_total) AS grand_total'
            );
            $purchase_data = Purchase::where('warehouse_id', $data['warehouse_id'])->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query1))->get();
            
            $total_discount[] = number_format((float)$purchase_data[0]->total_discount, 2, '.', '');
            $order_discount[] = number_format((float)$purchase_data[0]->order_discount, 2, '.', '');
            $total_tax[] = number_format((float)$purchase_data[0]->total_tax, 2, '.', '');
            $order_tax[] = number_format((float)$purchase_data[0]->order_tax, 2, '.', '');
            $shipping_cost[] = number_format((float)$purchase_data[0]->shipping_cost, 2, '.', '');
            $grand_total[] = number_format((float)$purchase_data[0]->grand_total, 2, '.', '');
            $start = strtotime("+1 month", $start);
        }
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $warehouse_id = $data['warehouse_id'];
        return view('report.monthly_purchase', compact('year', 'total_discount', 'order_discount', 'total_tax', 'order_tax', 'shipping_cost', 'grand_total', 'lims_warehouse_list', 'warehouse_id'));
    }

    public function bestSeller()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('best-seller')){
            $start = strtotime(date("Y-m", strtotime("-2 months")).'-01');
            $end = strtotime(date("Y").'-'.date("m").'-31');
            
            while($start <= $end)
            {
                $start_date = date("Y-m", $start).'-'.'01';
                $end_date = date("Y-m", $start).'-'.'31';

                $best_selling_qty = Product_Sale::select(DB::raw('product_id, sum(qty) as sold_qty'))->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->groupBy('product_id')->orderBy('sold_qty', 'desc')->take(1)->get();
                if(!count($best_selling_qty)){
                    $product[] = '';
                    $sold_qty[] = 0;
                }
                foreach ($best_selling_qty as $best_seller) {
                    $product_data = Product::find($best_seller->product_id);
                    $product[] = $product_data->name.': '.$product_data->code;
                    $sold_qty[] = $best_seller->sold_qty;
                }
                $start = strtotime("+1 month", $start);
            }
            $start_month = date("F Y", strtotime('-2 month'));
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $warehouse_id = 0;
            return view('report.best_seller', compact('product', 'sold_qty', 'start_month', 'lims_warehouse_list', 'warehouse_id'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function bestSellerByWarehouse(Request $request)
    {
        $data = $request->all();
        if($data['warehouse_id'] == 0)
            return redirect()->back();

        $start = strtotime(date("Y-m", strtotime("-2 months")).'-01');
        $end = strtotime(date("Y").'-'.date("m").'-31');

        while($start <= $end)
        {
            $start_date = date("Y-m", $start).'-'.'01';
            $end_date = date("Y-m", $start).'-'.'31';

            $best_selling_qty = DB::table('sales')
                                ->join('product_sales', 'sales.id', '=', 'product_sales.sale_id')->select(DB::raw('product_sales.product_id, sum(product_sales.qty) as sold_qty'))->where('sales.warehouse_id', $data['warehouse_id'])->whereDate('sales.created_at', '>=' , $start_date)->whereDate('sales.created_at', '<=' , $end_date)->groupBy('product_id')->orderBy('sold_qty', 'desc')->take(1)->get();
                                
            if(!count($best_selling_qty)) {
                $product[] = '';
                $sold_qty[] = 0;
            }
            foreach ($best_selling_qty as $best_seller) {
                $product_data = Product::find($best_seller->product_id);
                $product[] = $product_data->name.': '.$product_data->code;
                $sold_qty[] = $best_seller->sold_qty;
            }
            $start = strtotime("+1 month", $start);
        }
        $start_month = date("F Y", strtotime('-2 month'));
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $warehouse_id = $data['warehouse_id'];
        return view('report.best_seller', compact('product', 'sold_qty', 'start_month', 'lims_warehouse_list', 'warehouse_id'));
    }

    public function profitLoss(Request $request)
    {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $query1 = array(
            'SUM(grand_total) AS grand_total',
            'SUM(paid_amount) AS paid_amount',
            'SUM(total_tax + order_tax) AS tax',
            'SUM(total_discount + order_discount) AS discount'
        );
        $query2 = array(
            'SUM(grand_total) AS grand_total',
            'SUM(total_tax + order_tax) AS tax'
        );
        $purchase = Purchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query1))->get();
        $total_purchase = Purchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->count();
        $sale = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query1))->get();
        $total_sale = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->count();
        $return = Returns::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query2))->get();
        $total_return = Returns::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->count();
        $purchase_return = ReturnPurchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query2))->get();
        $total_purchase_return = ReturnPurchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->count();
        $expense = Expense::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('amount');
        $total_expense = Expense::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->count();
        $payroll = Payroll::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('amount');
        $total_payroll = Payroll::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->count();
        $total_item = DB::table('product_warehouse')
                    ->join('products', 'product_warehouse.product_id', '=', 'products.id')
                    ->where([
                        ['products.is_active', true],
                        ['product_warehouse.qty', '>' , 0]
                    ])->count();
        $payment_recieved_number = DB::table('payments')->whereNotNull('sale_id')->whereDate('created_at', '>=' , $start_date)
            ->whereDate('created_at', '<=' , $end_date)->count();
        $payment_recieved = DB::table('payments')->whereNotNull('sale_id')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('payments.amount');
        $credit_card_payment_sale = DB::table('payments')
                            ->where('paying_method', 'Credit Card')
                            ->whereNotNull('payments.sale_id')
                            ->whereDate('payments.created_at', '>=' , $start_date)
                            ->whereDate('payments.created_at', '<=' , $end_date)->sum('payments.amount');
        $cheque_payment_sale = DB::table('payments')
                            ->where('paying_method', 'Cheque')
                            ->whereNotNull('payments.sale_id')
                            ->whereDate('payments.created_at', '>=' , $start_date)
                            ->whereDate('payments.created_at', '<=' , $end_date)->sum('payments.amount');
        $gift_card_payment_sale = DB::table('payments')
                            ->where('paying_method', 'Gift Card')
                            ->whereNotNull('sale_id')
                            ->whereDate('created_at', '>=' , $start_date)
                            ->whereDate('created_at', '<=' , $end_date)
                            ->sum('amount');
        $paypal_payment_sale = DB::table('payments')
                            ->where('paying_method', 'Paypal')
                            ->whereNotNull('sale_id')
                            ->whereDate('created_at', '>=' , $start_date)
                            ->whereDate('created_at', '<=' , $end_date)
                            ->sum('amount');
        $deposit_payment_sale = DB::table('payments')
                            ->where('paying_method', 'Deposit')
                            ->whereNotNull('sale_id')
                            ->whereDate('created_at', '>=' , $start_date)
                            ->whereDate('created_at', '<=' , $end_date)
                            ->sum('amount');
        $cash_payment_sale =  $payment_recieved - $credit_card_payment_sale - $cheque_payment_sale - $gift_card_payment_sale - $paypal_payment_sale - $deposit_payment_sale;
        $payment_sent_number = DB::table('payments')->whereNotNull('purchase_id')->whereDate('created_at', '>=' , $start_date)
            ->whereDate('created_at', '<=' , $end_date)->count();
        $payment_sent = DB::table('payments')->whereNotNull('purchase_id')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('payments.amount');
        $payment_sent_cash = DB::table('payments')->whereNotNull('purchase_id')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('paying_method', 'Cash')->sum('payments.amount');
        $credit_card_payment_purchase = DB::table('payments')
                            ->where('paying_method', 'Gift Card')
                            ->whereNotNull('payments.purchase_id')
                            ->whereDate('payments.created_at', '>=' , $start_date)
                            ->whereDate('payments.created_at', '<=' , $end_date)->sum('payments.amount');
        $cheque_payment_purchase = DB::table('payments')
                            ->where('paying_method', 'Cheque')
                            ->whereNotNull('payments.purchase_id')
                            ->whereDate('payments.created_at', '>=' , $start_date)
                            ->whereDate('payments.created_at', '<=' , $end_date)->sum('payments.amount');
        $cash_payment_purchase =  $payment_sent - $credit_card_payment_purchase - $cheque_payment_purchase;
        $lims_warehouse_all = Warehouse::where('is_active',true)->get();
        $warehouse_name = [];
        foreach ($lims_warehouse_all as $warehouse) {
            $warehouse_name[] = $warehouse->name;
            $warehouse_sale[] = Sale::where('warehouse_id', $warehouse->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query2))->get();
            $warehouse_purchase[] = Purchase::where('warehouse_id', $warehouse->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query2))->get();
            $warehouse_return[] = Returns::where('warehouse_id', $warehouse->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query2))->get();
            $warehouse_purchase_return[] = ReturnPurchase::where('warehouse_id', $warehouse->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->selectRaw(implode(',', $query2))->get();
            $warehouse_expense[] = Expense::where('warehouse_id', $warehouse->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('amount');
        }

        return view('report.profit_loss', compact('purchase', 'total_purchase', 'sale', 'total_sale', 'return', 'purchase_return', 'total_return', 'total_purchase_return', 'expense', 'payroll', 'total_expense', 'total_payroll', 'payment_recieved', 'payment_recieved_number', 'cash_payment_sale', 'cheque_payment_sale', 'credit_card_payment_sale', 'gift_card_payment_sale', 'paypal_payment_sale', 'deposit_payment_sale', 'payment_sent', 'payment_sent_number', 'cash_payment_purchase', 'cheque_payment_purchase', 'credit_card_payment_purchase', 'warehouse_name', 'warehouse_sale', 'warehouse_purchase', 'warehouse_return', 'warehouse_purchase_return', 'warehouse_expense', 'start_date', 'end_date', 'payment_sent_cash'));
    }

    public function productReport(Request $request)
    {
        $data = $request->all();
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $warehouse_id = $data['warehouse_id'];
        $product_id = [];
        $variant_id = [];
        $product_name = [];
        $product_qty = [];
        $lims_product_all = Product::select('id', 'name', 'qty', 'is_variant')->where('is_active', true)->get();
        foreach ($lims_product_all as $product) {
            $lims_product_purchase_data = null;
            $variant_id_all = [];
            if($warehouse_id == 0) {
                if($product->is_variant)
                    $variant_id_all = ProductPurchase::distinct('variant_id')->where('product_id', $product->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->pluck('variant_id');
                else
                    $lims_product_purchase_data = ProductPurchase::where('product_id', $product->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->first();
            }
            else {
                if($product->is_variant)
                    $variant_id_all = DB::table('purchases')
                        ->join('product_purchases', 'purchases.id', '=', 'product_purchases.purchase_id')
                        ->distinct('variant_id')
                        ->where([
                            ['product_purchases.product_id', $product->id],
                            ['purchases.warehouse_id', $warehouse_id]
                        ])->whereDate('purchases.created_at','>=', $start_date)
                          ->whereDate('purchases.created_at','<=', $end_date)
                          ->pluck('variant_id');
                else
                    $lims_product_purchase_data = DB::table('purchases')
                        ->join('product_purchases', 'purchases.id', '=', 'product_purchases.purchase_id')->where([
                                ['product_purchases.product_id', $product->id],
                                ['purchases.warehouse_id', $warehouse_id]
                        ])->whereDate('purchases.created_at','>=', $start_date)
                          ->whereDate('purchases.created_at','<=', $end_date)
                          ->first();
            }
            
            if($lims_product_purchase_data) {
                $product_name[] = $product->name;
                $product_id[] = $product->id;
                $variant_id[] = null;
                if($warehouse_id == 0)
                    $product_qty[] = $product->qty;
                else
                    $product_qty[] = Product_Warehouse::where([
                                    ['product_id', $product->id],
                                    ['warehouse_id', $warehouse_id]
                                ])->sum('qty');
            }
            elseif(count($variant_id_all)) {
                foreach ($variant_id_all as $key => $variantId) {
                    $variant_data = Variant::find($variantId);
                    $product_name[] = $product->name.' ['.$variant_data->name.']';
                    $product_id[] = $product->id;
                    $variant_id[] = $variant_data->id;
                    if($warehouse_id == 0)
                        $product_qty[] = ProductVariant::FindExactProduct($product->id, $variant_data->id)->first()->qty;
                    else
                        $product_qty[] = Product_Warehouse::where([
                                        ['product_id', $product->id],
                                        ['variant_id', $variant_data->id],
                                        ['warehouse_id', $warehouse_id]
                                    ])->first()->qty;
                    
                }
            }

            else{

                if($warehouse_id == 0) {
                    if($product->is_variant){
                        $variant_id_all = Product_Sale::distinct('variant_id')->where('product_id', $product->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->pluck('variant_id');
                        //return $lims_product_sale_data;
                    }
                    else
                        $lims_product_sale_data = Product_Sale::where('product_id', $product->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->first();
                }
                else {
                    if($product->is_variant)
                        $variant_id_all = DB::table('sales')
                            ->join('product_sales', 'sales.id', '=', 'product_sales.sale_id')
                            ->distinct('variant_id')
                            ->where([
                                ['product_sales.product_id', $product->id],
                                ['sales.warehouse_id', $warehouse_id]
                            ])->whereDate('sales.created_at','>=', $start_date)
                              ->whereDate('sales.created_at','<=', $end_date)
                              ->pluck('variant_id');
                    else
                        $lims_product_sale_data = DB::table('sales')
                                ->join('product_sales', 'sales.id', '=', 'product_sales.sale_id')->where([
                                        ['product_sales.product_id', $product->id],
                                        ['sales.warehouse_id', $warehouse_id]
                                ])->whereDate('sales.created_at','>=', $start_date)
                                  ->whereDate('sales.created_at','<=', $end_date)
                                  ->first();
                }
                if($lims_product_sale_data) {
                    $product_name[] = $product->name;
                    $product_id[] = $product->id;
                    $variant_id[] = null;
                    if($warehouse_id == 0)
                        $product_qty[] = $product->qty;
                    else {
                        $product_qty[] = Product_Warehouse::where([
                                        ['product_id', $product->id],
                                        ['warehouse_id', $warehouse_id]
                                    ])->sum('qty');
                    }
                }
                elseif(count($variant_id_all)) {
                    foreach ($variant_id_all as $key => $variantId) {
                        $variant_data = Variant::find($variantId);
                        $product_name[] = $product->name.' ['.$variant_data->name.']';
                        $product_id[] = $product->id;
                        $variant_id[] = $variant_data->id;
                        if($warehouse_id == 0)
                            $product_qty[] = ProductVariant::FindExactProduct($product->id, $variant_data->id)->first()->qty;
                        else
                            $product_qty[] = Product_Warehouse::where([
                                            ['product_id', $product->id],
                                            ['variant_id', $variant_data->id],
                                            ['warehouse_id', $warehouse_id]
                                        ])->first()->qty;
                        
                    }
                }
            }
        }
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('report.product_report',compact('product_id', 'variant_id', 'product_name', 'product_qty', 'start_date', 'end_date', 'lims_warehouse_list', 'warehouse_id'));
    }


public function existenceReport(Request $request)
    {

        $data = $request->all();


        // print_r($data);

        // exit();
        if(isset($data["fecha_inicio"])==""){

            $start_date= date("Y-m-d");
            $end_date  =$start_date;
        }else{

            $start_date=$data["fecha_inicio"];
          
        }


        $where ="";
        if(isset($data["categoria"])!=""){
            $where = " and  a.category_id = ".$data["categoria"];
        }

        $datos = DB::select('
            SELECT a.`name`
                ,
                a.name AS descripcion
                ,
                u.`unit_name`
                ,
                IFNULL((
                    SELECT k.saldo FROM kardex k
                    WHERE k.product_id = a.`id`
                    and CONVERT(k.created_at,DATE) <="'.$start_date.'"
                    ORDER BY created_at DESC
                    LIMIT 1
                    ),0.00) AS stock
                ,
                IFNULL((
                    SELECT k.cost FROM kardex k
                    WHERE k.product_id = a.`id`
                    and CONVERT(k.created_at,DATE) <="'.$start_date.'"
                    and k.saldo>0  
                    ORDER BY created_at DESC
                    LIMIT 1
                    ),0.00) AS costo
                ,
                IFNULL((
                    SELECT k.cost FROM kardex k
                    WHERE k.product_id = a.`id`
                    and CONVERT(k.created_at,DATE) <="'.$start_date.'" 
                    and k.saldo>0 
                    ORDER BY created_at DESC
                    LIMIT 1
                    ),0.00) 
                    *
                    IFNULL((
                        SELECT k.saldo FROM kardex k
                        WHERE k.product_id = a.`id`
                        and CONVERT(k.created_at,DATE) <="'.$start_date.'" 
                        ORDER BY created_at DESC
                        LIMIT 1
                        ),0.00) 

             AS costo_total,
             a.type AS tipo_producto,
             a.code AS codigo
             ,
             cat.name AS categoria
             ,
             mar.title AS marca

 FROM products a
 LEFT JOIN units u ON u.id= a.`purchase_unit_id` 

  LEFT JOIN categories  cat ON cat.id= a.category_id
 LEFT JOIN brands mar ON mar.id= a.brand_id
 where  a.id in(
            SELECT product_id FROM kardex kk
            WHERE CONVERT(kk.created_at,DATE) <= "'.$start_date.'"
            AND kk.saldo>0
 )
  '.$where);

    //dd($datos);
     $datos_categoria = DB::select('  SELECT id, NAME
                            FROM categories  ');   
    
    return view('report.existence_report',compact('start_date','datos','datos_categoria'));


    }


public function costperdayReport(Request $request)
    {


       $data = $request->all();


      // print_r($data);

      // exit();
       if(isset($data["fecha_inicio"])==""){

           $start_date= date("Y-m-d");
           $end_date  =$start_date;
       }else{

           $start_date=$data["fecha_inicio"];
           $end_date  =$data["fecha_final"];
       }


     $where ="";
     if(isset($data["categoria"])!=""){
       $where = " AND  b.category_id = ".$data["categoria"];
     }

    if(isset($data["vendedor"])!=""){
       $where .= " AND  s.biller_id = ".$data["vendedor"];
     }





        $datos = DB::select('
                SELECT a.created_at, 
                s.`reference_no`,
                a.`qty`,
                b.`name` AS name_prod,
                "" AS lote,
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

        $datos_categoria = DB::select('SELECT id, NAME FROM categories  ');     
        $datos_vendedor  = DB::select('SELECT id, NAME FROM billers');     

                                   

       

        return view('report.cost_per_day_report',compact('start_date','end_date','datos','datos_categoria', 'datos_vendedor'));
    }


    public function kardexReport(Request $request)
    {


       $data = $request->all();


      // print_r($data);

      // exit();
       if(isset($data["fecha_inicio"])==""){

           $start_date= date("Y-m-d");
           $end_date  =$start_date;
       }else{

           $start_date=$data["fecha_inicio"];
           $end_date  =$data["fecha_final"];
       }


     $where ="";
     if(isset($data["articulo"])!=""){
       $where = " AND  product_id = ".$data["articulo"];
     }else{
        $where = " AND  product_id =0";

     }

  





        $datos = DB::select("
           
SELECT @i := @i + 1 AS posicion,
                            k.created_at AS fecha,
                            k.documento AS numero_documento,
                            nombreProveedor AS nombreproveedor, 
                            '' AS nacionalidadproveedor,
                            '' AS referencia,
                            '' AS lote,
                            concepto,
                            k.cost,
                            IF(signo>0, k.qty, 0) AS entrada,
                            IF(signo<0, k.qty, 0) AS salida,
                            cast(saldo as decimal(19,2)) as saldo,
                            cast(IF(signo>0, k.qty, 0)*k.cost as decimal(19,2)) AS entradac,
                            cast(IF(signo<0, k.qty, 0) *k.cost as decimal(19,2)) AS salidac,
                            cast(saldo*k.costo_unitario_promedio as decimal(19,2)) AS saldoc,
                            cast(k.costo_unitario_promedio as decimal(19,2)) AS costounitariopromedio,
                            p.name,
                           u.unit_name,
                           code

                            FROM kardex k
                            inner join products p on p.id= k.product_id
                            LEFT JOIN units u ON u.id= p.unit_id
                            WHERE 
                               CONVERT(k.created_at,DATE) BETWEEN '".$start_date."' AND '".$end_date."' 
                             ".$where);


        $datos_iniciales = DB::select("
           
                            SELECT   saldo, saldo*k.cost as saldoc , costo_unitario_promedio

                            FROM kardex k
                           
                            WHERE 
                               CONVERT(k.created_at,DATE) < '".$start_date."'
                             ".$where." order by k.created_at desc limit 1");


     if(count($datos_iniciales)==0){

 $datos_iniciales = DB::select("
           
                            SELECT   0 saldo, 0  as saldoc , 0 costo_unitario_promedio

                           ");
     }


        $datos_articulos = DB::select('SELECT id, name FROM products  ');     
  

                                   

       

        return view('report.kardexReport',compact('start_date','end_date','datos','datos_articulos', 'datos_iniciales'));
    }




public function costpersellerReport(Request $request)
    {


       $data = $request->all();


      // print_r($data);

      // exit();
       if(isset($data["fecha_inicio"])==""){

           $start_date= date("Y-m-d");
           $end_date  =$start_date;
       }else{

           $start_date=$data["fecha_inicio"];
           $end_date  =$data["fecha_final"];
       }
     
    $where=""; 
    if(isset($data["vendedor"])!=""){
       $where .= " AND  a.biller_id = ".$data["vendedor"];
     }


        $datos = DB::select('
              SELECT a.`reference_no`, a.`created_at`, b.`name`, 0 AS exentas, a.`grand_total`, 
                    (
                    SELECT SUM(gain) FROM product_sales ss
                    WHERE ss.sale_id= a.id
                    ) gain 
                    , bil.`name` as vendedor
                     FROM sales a
                    INNER JOIN customers b ON a.`customer_id` = b.`id`
                    INNER JOIN billers bil ON bil.id=  a.`biller_id`

                WHERE CONVERT(a.created_at,DATE) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
'.$where);
       $datos_vendedor  = DB::select('SELECT id, NAME FROM billers');     

        return view('report.cost_per_seller_report',compact('start_date','end_date','datos','datos_vendedor'));
    }


    public function purchaseReport(Request $request)
    {
        $data = $request->all();
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $warehouse_id = $data['warehouse_id'];
        $product_id = [];
        $variant_id = [];
        $product_name = [];
        $product_qty = [];
        $lims_product_all = Product::select('id', 'name', 'qty', 'is_variant')->where('is_active', true)->get();
        foreach ($lims_product_all as $product) {
            $lims_product_purchase_data = null;
            $variant_id_all = [];
            if($warehouse_id == 0) {
                if($product->is_variant)
                    $variant_id_all = ProductPurchase::distinct('variant_id')->where('product_id', $product->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->pluck('variant_id');
                else
                    $lims_product_purchase_data = ProductPurchase::where('product_id', $product->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->first();
            }
            else {
                if($product->is_variant)
                    $variant_id_all = DB::table('purchases')
                        ->join('product_purchases', 'purchases.id', '=', 'product_purchases.purchase_id')
                        ->distinct('variant_id')
                        ->where([
                            ['product_purchases.product_id', $product->id],
                            ['purchases.warehouse_id', $warehouse_id]
                        ])->whereDate('purchases.created_at','>=', $start_date)
                          ->whereDate('purchases.created_at','<=', $end_date)
                          ->pluck('variant_id');
                else
                    $lims_product_purchase_data = DB::table('purchases')
                        ->join('product_purchases', 'purchases.id', '=', 'product_purchases.purchase_id')->where([
                                ['product_purchases.product_id', $product->id],
                                ['purchases.warehouse_id', $warehouse_id]
                        ])->whereDate('purchases.created_at','>=', $start_date)
                          ->whereDate('purchases.created_at','<=', $end_date)
                          ->first();
            }

            if($lims_product_purchase_data) {
                $product_name[] = $product->name;
                $product_id[] = $product->id;
                $variant_id[] = null;
                if($warehouse_id == 0)
                    $product_qty[] = $product->qty;
                else
                    $product_qty[] = Product_Warehouse::where([
                                    ['product_id', $product->id],
                                    ['warehouse_id', $warehouse_id]
                                ])->sum('qty');
            }
            elseif(count($variant_id_all)) {
                foreach ($variant_id_all as $key => $variantId) {
                    $variant_data = Variant::find($variantId);
                    $product_name[] = $product->name.' ['.$variant_data->name.']';
                    $product_id[] = $product->id;
                    $variant_id[] = $variant_data->id;
                    if($warehouse_id == 0)
                        $product_qty[] = ProductVariant::FindExactProduct($product->id, $variant_data->id)->first()->qty;
                    else
                        $product_qty[] = Product_Warehouse::where([
                                        ['product_id', $product->id],
                                        ['variant_id', $variant_data->id],
                                        ['warehouse_id', $warehouse_id]
                                    ])->first()->qty;
                    
                }
            }
        }
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('report.purchase_report',compact('product_id', 'variant_id', 'product_name', 'product_qty', 'start_date', 'end_date', 'lims_warehouse_list', 'warehouse_id'));
    }

    public function saleReport(Request $request)
    {
        $data = $request->all();
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $warehouse_id = $data['warehouse_id'];
        $product_id = [];
        $variant_id = [];
        $product_name = [];
        $product_qty = [];
        $lims_product_all = Product::select('id', 'name', 'qty', 'is_variant')->where('is_active', true)->get();
        
        foreach ($lims_product_all as $product) {
            $lims_product_sale_data = null;
            $variant_id_all = [];
            if($warehouse_id == 0){
                if($product->is_variant)
                    $variant_id_all = Product_Sale::distinct('variant_id')->where('product_id', $product->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->pluck('variant_id');
                else
                    $lims_product_sale_data = Product_Sale::where('product_id', $product->id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->first();
            }
            else {
                if($product->is_variant)
                    $variant_id_all = DB::table('sales')
                        ->join('product_sales', 'sales.id', '=', 'product_sales.sale_id')
                        ->distinct('variant_id')
                        ->where([
                            ['product_sales.product_id', $product->id],
                            ['sales.warehouse_id', $warehouse_id]
                        ])->whereDate('sales.created_at','>=', $start_date)
                          ->whereDate('sales.created_at','<=', $end_date)
                          ->pluck('variant_id');
                else
                    $lims_product_sale_data = DB::table('sales')
                            ->join('product_sales', 'sales.id', '=', 'product_sales.sale_id')->where([
                                    ['product_sales.product_id', $product->id],
                                    ['sales.warehouse_id', $warehouse_id]
                            ])->whereDate('sales.created_at','>=', $start_date)
                              ->whereDate('sales.created_at','<=', $end_date)
                              ->first();
            }
            if($lims_product_sale_data) {
                $product_name[] = $product->name;
                $product_id[] = $product->id;
                $variant_id[] = null;
                if($warehouse_id == 0)
                    $product_qty[] = $product->qty;
                else {
                    $product_qty[] = Product_Warehouse::where([
                                    ['product_id', $product->id],
                                    ['warehouse_id', $warehouse_id]
                                ])->sum('qty');
                }
            }
            elseif(count($variant_id_all)) {
                foreach ($variant_id_all as $key => $variantId) {
                    $variant_data = Variant::find($variantId);
                    $product_name[] = $product->name.' ['.$variant_data->name.']';
                    $product_id[] = $product->id;
                    $variant_id[] = $variant_data->id;
                    if($warehouse_id == 0)
                        $product_qty[] = ProductVariant::FindExactProduct($product->id, $variant_data->id)->first()->qty;
                    else
                        $product_qty[] = Product_Warehouse::where([
                                        ['product_id', $product->id],
                                        ['variant_id', $variant_data->id],
                                        ['warehouse_id', $warehouse_id]
                                    ])->first()->qty;
                    
                }
            }
        }
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('report.sale_report',compact('product_id', 'variant_id', 'product_name', 'product_qty', 'start_date', 'end_date', 'lims_warehouse_list','warehouse_id'));
    }

    public function paymentReportByDate(Request $request)
    {
        $data = $request->all();
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        
        $lims_payment_data = Payment::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->get();
        return view('report.payment_report',compact('lims_payment_data', 'start_date', 'end_date'));
    }

    public function warehouseReport(Request $request)
    {
        $data = $request->all();
        $warehouse_id = $data['warehouse_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];

        $lims_purchase_data = Purchase::where('warehouse_id', $warehouse_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_sale_data = Sale::with('customer')->where('warehouse_id', $warehouse_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_quotation_data = Quotation::with('customer')->where('warehouse_id', $warehouse_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_return_data = Returns::with('customer', 'biller')->where('warehouse_id', $warehouse_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_expense_data = Expense::with('expenseCategory')->where('warehouse_id', $warehouse_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();

        $lims_product_purchase_data = [];
        $lims_product_sale_data = [];
        $lims_product_quotation_data = [];
        $lims_product_return_data = [];

        foreach ($lims_purchase_data as $key => $purchase) {
            $lims_product_purchase_data[$key] = ProductPurchase::where('purchase_id', $purchase->id)->get();
        }
        foreach ($lims_sale_data as $key => $sale) {
            $lims_product_sale_data[$key] = Product_Sale::where('sale_id', $sale->id)->get();
        }
        foreach ($lims_quotation_data as $key => $quotation) {
            $lims_product_quotation_data[$key] = ProductQuotation::where('quotation_id', $quotation->id)->get();
        }
        foreach ($lims_return_data as $key => $return) {
            $lims_product_return_data[$key] = ProductReturn::where('return_id', $return->id)->get();
        }
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('report.warehouse_report', compact('warehouse_id', 'start_date', 'end_date', 'lims_purchase_data', 'lims_product_purchase_data', 'lims_sale_data', 'lims_product_sale_data', 'lims_warehouse_list', 'lims_quotation_data', 'lims_product_quotation_data', 'lims_return_data', 'lims_product_return_data', 'lims_expense_data'));
    }

    public function userReport(Request $request)
    {
        $data = $request->all();
        $user_id = $data['user_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $lims_product_sale_data = [];
        $lims_product_purchase_data = [];
        $lims_product_quotation_data = [];
        $lims_product_transfer_data = [];

        $lims_sale_data = Sale::with('customer', 'warehouse')->where('user_id', $user_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_purchase_data = Purchase::with('warehouse')->where('user_id', $user_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();        
        $lims_quotation_data = Quotation::with('customer', 'warehouse')->where('user_id', $user_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_transfer_data = Transfer::with('fromWarehouse', 'toWarehouse')->where('user_id', $user_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();        
        $lims_payment_data = DB::table('payments')
                           ->where('user_id', $user_id)
                           ->whereDate('payments.created_at', '>=' , $start_date)
                           ->whereDate('payments.created_at', '<=' , $end_date)
                           ->orderBy('created_at', 'desc')
                           ->get();
        $lims_expense_data = Expense::with('warehouse', 'expenseCategory')->where('user_id', $user_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_payroll_data = Payroll::with('employee')->where('user_id', $user_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();

        foreach ($lims_sale_data as $key => $sale) {
            $lims_product_sale_data[$key] = Product_Sale::where('sale_id', $sale->id)->get();
        }
        foreach ($lims_purchase_data as $key => $purchase) {
            $lims_product_purchase_data[$key] = ProductPurchase::where('purchase_id', $purchase->id)->get();
        }
        foreach ($lims_quotation_data as $key => $quotation) {
            $lims_product_quotation_data[$key] = ProductQuotation::where('quotation_id', $quotation->id)->get();
        }
        foreach ($lims_transfer_data as $key => $transfer) {
            $lims_product_transfer_data[$key] = ProductTransfer::where('transfer_id', $transfer->id)->get();
        }

        $lims_user_list = User::where('is_active', true)->get();
        return view('report.user_report', compact('lims_sale_data','user_id', 'start_date', 'end_date', 'lims_product_sale_data', 'lims_payment_data', 'lims_user_list', 'lims_purchase_data', 'lims_product_purchase_data', 'lims_quotation_data', 'lims_product_quotation_data', 'lims_transfer_data', 'lims_product_transfer_data', 'lims_expense_data', 'lims_payroll_data') );
    }

    public function customerReport(Request $request)
    {
        $data = $request->all();
        $customer_id = $data['customer_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $lims_sale_data = Sale::with('warehouse')->where('customer_id', $customer_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_quotation_data = Quotation::with('warehouse')->where('customer_id', $customer_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_return_data = Returns::with('warehouse', 'biller')->where('customer_id', $customer_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_payment_data = DB::table('payments')
                           ->join('sales', 'payments.sale_id', '=', 'sales.id')
                           ->where('customer_id', $customer_id)
                           ->whereDate('payments.created_at', '>=' , $start_date)
                           ->whereDate('payments.created_at', '<=' , $end_date)
                           ->select('payments.*', 'sales.reference_no as sale_reference')
                           ->orderBy('payments.created_at', 'desc')
                           ->get();

        $lims_product_sale_data = [];
        $lims_product_quotation_data = [];
        $lims_product_return_data = [];

        foreach ($lims_sale_data as $key => $sale) {
            $lims_product_sale_data[$key] = Product_Sale::where('sale_id', $sale->id)->get();
        }
        foreach ($lims_quotation_data as $key => $quotation) {
            $lims_product_quotation_data[$key] = ProductQuotation::where('quotation_id', $quotation->id)->get();
        }
        foreach ($lims_return_data as $key => $return) {
            $lims_product_return_data[$key] = ProductReturn::where('return_id', $return->id)->get();
        }
        $lims_customer_list = Customer::where('is_active', true)->get();
        return view('report.customer_report', compact('lims_sale_data','customer_id', 'start_date', 'end_date', 'lims_product_sale_data', 'lims_payment_data', 'lims_customer_list', 'lims_quotation_data', 'lims_product_quotation_data', 'lims_return_data', 'lims_product_return_data'));
    }

    public function supplierReport(Request $request)
    {
        $data = $request->all();
        $supplier_id = $data['supplier_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $lims_purchase_data = Purchase::with('warehouse')->where('supplier_id', $supplier_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_quotation_data = Quotation::with('warehouse', 'customer')->where('supplier_id', $supplier_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_return_data = ReturnPurchase::with('warehouse')->where('supplier_id', $supplier_id)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->orderBy('created_at', 'desc')->get();
        $lims_payment_data = DB::table('payments')
                           ->join('purchases', 'payments.purchase_id', '=', 'purchases.id')
                           ->where('supplier_id', $supplier_id)
                           ->whereDate('payments.created_at', '>=' , $start_date)
                           ->whereDate('payments.created_at', '<=' , $end_date)
                           ->select('payments.*', 'purchases.reference_no as purchase_reference')
                           ->orderBy('payments.created_at', 'desc')
                           ->get();

        $lims_product_purchase_data = [];
        $lims_product_quotation_data = [];
        $lims_product_return_data = [];

        foreach ($lims_purchase_data as $key => $purchase) {
            $lims_product_purchase_data[$key] = ProductPurchase::where('purchase_id', $purchase->id)->get();
        }
        foreach ($lims_return_data as $key => $return) {
            $lims_product_return_data[$key] = PurchaseProductReturn::where('return_id', $return->id)->get();
        }
        foreach ($lims_quotation_data as $key => $quotation) {
            $lims_product_quotation_data[$key] = ProductQuotation::where('quotation_id', $quotation->id)->get();
        }
        $lims_supplier_list = Supplier::where('is_active', true)->get();
        return view('report.supplier_report', compact('lims_purchase_data', 'lims_product_purchase_data', 'lims_payment_data', 'supplier_id', 'start_date', 'end_date', 'lims_supplier_list', 'lims_quotation_data', 'lims_product_quotation_data', 'lims_return_data', 'lims_product_return_data'));
    }

    public function dueReportByDate(Request $request)
    {
        $data = $request->all();
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $lims_sale_data = Sale::where('payment_status', '!=', 4)->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->get();

        return view('report.due_report', compact('lims_sale_data', 'start_date', 'end_date'));
    }


    public function SalesGraph(Request $request)
   {

       $data = $request->all();
       
      
       $where =" ";

       $billers=""; 
       $brands=""; 

       if(count($data)>0){

       if(isset($data["billers"])!=""){
         $where.= " AND biller_id =".$data["billers"];
         $billers= $data["billers"];
       }

      if(isset($data["brands"])!=""){
        $where.= "  AND p.brand_id =".$data["brands"];
        $brands= $data["brands"];
       }

             if(isset($data["clientes"])!=""){
        $where.= "  AND h.customer_id =".$data["clientes"];
        $brands= $data["clientes"];
       }


       
       }





        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('sales-graph')){
        
            
            

  $datos_actual = DB::select("
    SELECT x.nombreMes, IFNULL((SELECT SUM(d.total) FROM sales  h
                                          INNER JOIN product_sales d ON h.id= d.sale_id
                                          LEFT JOIN products p ON p.id= d.product_id
                      WHERE MONTH(h.created_at)  = x.mes 
                      AND YEAR(h.created_at)= YEAR(NOW())    
                      ".$where."    
                                  ),0) AS Venta
            FROM 
            (
            SELECT 1 mes, 'Enero' AS nombreMes
            UNION
            SELECT 2,'Febrero'
            UNION
            SELECT 3, 'Marzo'
            UNION
            SELECT 4,'Abril'
            UNION
            SELECT 5,'Mayo'
            UNION
            SELECT 6, 'Junio'
            UNION
            SELECT 7, 'Julio'
            UNION
            SELECT 8, 'Agosto'
            UNION
            SELECT 9,'Septiembre'
            UNION
            SELECT 10,'Octubre'
            UNION
            SELECT 11,'Noviembree'
            UNION
            SELECT 12, 'Diciembre'


            ) x

    "
            );


  $datos_anterior = DB::select("
    SELECT x.nombreMes, IFNULL((SELECT SUM(d.total) FROM sales  h
                                          INNER JOIN product_sales d ON h.id= d.sale_id
                                          LEFT JOIN products p ON p.id= d.product_id
                      WHERE MONTH(h.created_at)  = x.mes 
                      AND YEAR(h.created_at)= YEAR(NOW())-1    
                      ".$where."    
                                  ),0) AS Venta
            FROM 
            (
            SELECT 1 mes, 'Enero' AS nombreMes
            UNION
            SELECT 2,'Febrero'
            UNION
            SELECT 3, 'Marzo'
            UNION
            SELECT 4,'Abril'
            UNION
            SELECT 5,'Mayo'
            UNION
            SELECT 6, 'Junio'
            UNION
            SELECT 7, 'Julio'
            UNION
            SELECT 8, 'Agosto'
            UNION
            SELECT 9,'Septiembre'
            UNION
            SELECT 10,'Octubre'
            UNION
            SELECT 11,'Noviembree'
            UNION
            SELECT 12, 'Diciembre'


            ) x

    "
            );


                 $datos2= array();
                 $datos3= array();
                 $i=0;
                 foreach ($datos_actual as $key) {
                    $datos2[$i]=  $key->nombreMes;
                 $i++;
                 }

                  $i=0;
                 foreach ($datos_actual as $key) {
                    $datos3[$i]=  $key->Venta;
                 $i++;
                 }

                $datos2_anterior= array();
                 $datos3_anterior= array();
                 $i=0;
                 foreach ($datos_anterior as $key) {
                    $datos2_anterior[$i]=  $key->nombreMes;
                 $i++;
                 }

                  $i=0;
                 foreach ($datos_anterior as $key) {
                    $datos3_anterior[$i]=  $key->Venta;
                 $i++;
                 }


                $datos2_diiferencia= array();
                 $datos3_diferencia= array();
                 $i=0;


                 foreach ($datos_anterior as $key) {
                    $datos3_diferencia[$i]=  $datos3[$i]-$key->Venta;
                 $i++;
                 }

   $datos_billers = DB::select('SELECT id, name FROM billers  '); 
   $datos_brands = DB::select('SELECT id, title FROM brands  '); 
   $datos_customer = DB::select('SELECT id, name, code FROM customers  '); 




           
            return view('report.sales_graph', compact('datos2','datos3','datos2_anterior','datos3_anterior', 'datos3_diferencia','datos_billers','datos_brands','brands','billers','datos_customer'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    
    
    public function OrdersGraph(Request $request)
    {
        $data = $request->all();
        $where =" ";
        $billers="";
        $brands="";

        if(count($data)>0){
            if(isset($data["billers"])!=""){
                $where.= " AND biller_id =".$data["billers"];
                $billers= $data["billers"];
            }

            if(isset($data["brands"])!=""){
                $where.= "  AND p.brand_id =".$data["brands"];
                $brands= $data["brands"];
            }

            if(isset($data["clientes"])!=""){
                $where.= "  AND h.customer_id =".$data["clientes"];
                $brands= $data["clientes"];
            }
        }

        $role = Role::find(Auth::user()->role_id);

        if($role->hasPermissionTo('sales-graph')){
            $datos_actual = DB::select("
                SELECT x.nombreMes,
                    IFNULL((SELECT SUM(d.total) FROM quotations  h
                    INNER JOIN product_quotation d ON h.id= d.quotation_id
                    LEFT JOIN products p ON p.id= d.product_id
                    WHERE MONTH(h.created_at)  = x.mes
                    AND YEAR(h.created_at)= YEAR(NOW())
                    AND h.quotation_status = 2
                    ".$where."
                ),0) AS Venta
                FROM
                (
                SELECT 1 mes, 'Enero' AS nombreMes
                UNION
                SELECT 2,'Febrero'
                UNION
                SELECT 3, 'Marzo'
                UNION
                SELECT 4,'Abril'
                UNION
                SELECT 5,'Mayo'
                UNION
                SELECT 6, 'Junio'
                UNION
                SELECT 7, 'Julio'
                UNION
                SELECT 8, 'Agosto'
                UNION
                SELECT 9,'Septiembre'
                UNION
                SELECT 10,'Octubre'
                UNION
                SELECT 11,'Noviembree'
                UNION
                SELECT 12, 'Diciembre'
                ) x
                "
            );

            $datos_anterior = DB::select("
                SELECT x.nombreMes,
                    IFNULL((SELECT SUM(d.total) FROM quotations  h
                    INNER JOIN product_quotation d ON h.id= d.quotation_id
                    LEFT JOIN products p ON p.id= d.product_id
                    WHERE MONTH(h.created_at)  = x.mes
                    AND YEAR(h.created_at)= YEAR(NOW())-1
                    AND h.quotation_status = 2
                    ".$where."
                    ),0) AS Venta
                FROM
                (
                SELECT 1 mes, 'Enero' AS nombreMes
                UNION
                SELECT 2,'Febrero'
                UNION
                SELECT 3, 'Marzo'
                UNION
                SELECT 4,'Abril'
                UNION
                SELECT 5,'Mayo'
                UNION
                SELECT 6, 'Junio'
                UNION
                SELECT 7, 'Julio'
                UNION
                SELECT 8, 'Agosto'
                UNION
                SELECT 9,'Septiembre'
                UNION
                SELECT 10,'Octubre'
                UNION
                SELECT 11,'Noviembree'
                UNION
                SELECT 12, 'Diciembre'
                ) x
                "
            );
                $datos2= array();
                $datos3= array();
                $i=0;
                foreach ($datos_actual as $key) {
                    $datos2[$i]=  $key->nombreMes;
                    $i++;
                }

                $i=0;
                foreach ($datos_actual as $key) {
                    $datos3[$i]=  $key->Venta;
                    $i++;
                }

                $datos2_anterior= array();
                $datos3_anterior= array();

                $i=0;
                foreach ($datos_anterior as $key) {
                    $datos2_anterior[$i]=  $key->nombreMes;
                    $i++;
                }

                $i=0;
                foreach ($datos_anterior as $key) {
                    $datos3_anterior[$i]=  $key->Venta;
                    $i++;
                }

                $datos2_diiferencia= array();
                $datos3_diferencia= array();

                $i=0;
                foreach ($datos_anterior as $key) {
                    $datos3_diferencia[$i]=  $datos3[$i]-$key->Venta;
                    $i++;
                }

                $datos_billers = DB::select('SELECT id, name FROM billers  ');
                $datos_brands = DB::select('SELECT id, title FROM brands  ');
                $datos_customer = DB::select('SELECT id, name, code FROM customers  ');

            return view('report.orders_graph', compact('datos2','datos3','datos2_anterior','datos3_anterior', 'datos3_diferencia','datos_billers','datos_brands','brands','billers','datos_customer'));
        }else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    
    public function SalesdayReport(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        
        if($role->hasPermissionTo('daily-sale')){
            $data = $request->all();
        
            if(isset($data["fecha_inicio"])==""){
            
               $start_date= date("Y-m-d");
               $end_date  =$start_date;
            }else{

               $start_date=$data["fecha_inicio"];
               $end_date  =$data["fecha_final"];
            }

            $where ="";
            
            if(isset($data["categoria"])!=""){
                $where = " AND  b.category_id = ".$data["categoria"];
            }

            if(isset($data["vendedor"])!=""){
                $where .= " AND  s.biller_id = ".$data["vendedor"];
            }

            $datos = DB::select('
                SELECT s.created_at, 
                    s.`reference_no`,
                    c.name,
                    round(s.grand_total/1.13,2) AS subtotal,
                    s.total_tax,
                    round((s.grand_total/1.13)*0.13,2) AS iva,
                    s.grand_total,
                    s.canceled
                FROM `sales` s
                left join customers c on c.id = s.customer_id
                WHERE CONVERT(s.created_at,DATE) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                   '.$where);

            $datos_vendedor  = DB::select('SELECT id, NAME FROM billers');

            $stotal = Sale::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total')/1.13;
            $siva = Sale::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total')/1.13*0.13;
            $sgrantotal = Sale::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total');

            return view('report.Sales_daily_report', compact('start_date','end_date','datos','datos_vendedor', 'stotal'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    
    public function boxCutReport(Request $request)
    {
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        //dd($start_date);

        $role = Role::find(Auth::user()->role_id);
        
        if($role->hasPermissionTo('daily-sale')){
            $data = $request->all();
                   
            //dd($data);
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            
            //dd($start_date);
            // if(isset($data["fecha_inicio"])==""){
            
            //    $start_date= date("Y-m-d");
            //    $end_date  =$start_date;
            // }else{

            //    $start_date=$data["fecha_inicio"];
            //    $end_date  =$data["fecha_final"];
            // }

            $where ="";
            
            if(isset($data["categoria"])!=""){
                $where = " AND  b.category_id = ".$data["categoria"];
            }

            if(isset($data["vendedor"])!=""){
                $where .= " AND  s.biller_id = ".$data["vendedor"];
            }

            $saleFac = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('document_id', '=', 2)->sum('grand_total');
            $total_sale_fac = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('document_id', '=', 2)->where('canceled', '=', 0)->count();

            $saleCcf = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('document_id', '=', 1)->sum('grand_total');
            $total_sale_ccf = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('document_id', '=', 1)->where('canceled', '=', 0)->count();

            $saleNumberCcfI = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('document_id', '=', 1)->first('reference_no');
            $saleNumberCcfF = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('document_id', '=', 1)->get()->last();

            $saleNumberFacI = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('document_id', '=', 2)->first('reference_no');
            $saleNumberFacF = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('document_id', '=', 2)->get()->last();

            //$boxesCuts=Boxe::join('boxe_details','boxes.id','=','boxe_details.boxe_id')
            //    ->join('users','boxes.user_id','=','users.id')
            //    ->join('tickets','boxe_details.ticket_id','=','tickets.id')
            //    ->select('boxes.id','boxes.user_id',
            //        'boxes.warehouse_id','boxes.cash_register_id','boxes.note',
            //        'boxe_details.ticket_id','boxe_details.total_dinero','boxe_details.qty_producto','tickets.name')
            //    ->whereDate('boxes.created_at', '>=' , $start_date)
            //    ->whereDate('boxes.created_at', '<=' , $end_date)
            //    ->where('boxes.user_id', auth()->user()->id)
            //    ->orderBy('boxes.id','desc')
            //    ->groupBy('boxes.id','boxes.user_id',
            //        'boxes.warehouse_id','boxes.cash_register_id','boxes.note',
            //        'boxe_details.ticket_id','boxe_details.total_dinero','boxe_details.qty_producto','tickets.name')
            //    ->paginate(12);
           
            $boxesCuts = Boxe::join('boxe_details', 'boxes.id', '=', 'boxe_details.boxe_id')
                ->select(DB::raw('boxe_details.ticket_id, sum(boxe_details.qty_producto) as sold_qty, sum(boxe_details.total_dinero) as sold_amount'))
                ->whereDate('boxe_details.created_at', '>=' , $start_date)
                ->whereDate('boxe_details.created_at', '<=' , $end_date)
                ->groupBy('boxe_details.ticket_id')
                ->get();
            
            //$boxesCuts = DB::table('boxe_details')
            //    ->whereDate('created_at', '>=' , $start_date)
            //    ->whereDate('created_at', '<=' , $end_date)->sum('boxe_details.total_dinero');
            
            //dd($boxesCuts);
            
            
            
            $expenses = Expense::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->get();    

            $expenses_sum = DB::table('expenses')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)->sum('expenses.amount');

            $cash_registers = DB::table('cash_registers')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)->sum('cash_registers.cash_in_hand');

            //$purchases_data = Purchase::join('suppliers','purchases.supplier_id','=','suppliers.id')
            //    ->select('purchases.id', 'purchases.created_at', 'purchases.invoice',
            //        'purchases.grand_total', 'suppliers.name')
            //    ->whereDate('purchases.created_at', '>=', $start_date)
            //    ->whereDate('purchases.created_at', '<=', $end_date)
            //    ->orderBy('purchases.id','asc')
            //    ->groupBy('purchases.id', 'purchases.created_at', 'purchases.invoice',
            //        'purchases.grand_total', 'suppliers.name')
            //    ->paginate(); 
            
            $purchases_data = DB::table('purchases')
                ->join('suppliers','purchases.supplier_id','=','suppliers.id')
                ->select('purchases.id', 'purchases.created_at', 'purchases.invoice','purchases.grand_total', 'suppliers.name')
                ->whereDate('purchases.created_at', '>=', $start_date)
                ->whereDate('purchases.created_at', '<=', $end_date)
                ->get();     
            
            
            
            //dd($start_date);
            //dd($end_date);
            //dd($purchases_data);
            $payrolls_data = DB::table('payrolls')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)->sum('payrolls.amount');
            
            $return_sales = DB::table('returns')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)->sum('returns.grand_total');  
            
            // $cheque_payment_sale = DB::table('payments')
            //     ->where('paying_method', 'Cheque')
            //     ->whereNotNull('payments.sale_id')
            //     ->whereDate('payments.created_at', '>=' , $start_date)
            //     ->whereDate('payments.created_at', '<=' , $end_date)->get();


            $payment_recieved = DB::table('payments')->whereNotNull('sale_id')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('payments.amount');
            $credit_card_payment_sale = DB::table('payments')
                ->where('paying_method', 'Credit Card')
                ->whereNotNull('payments.sale_id')
                ->whereDate('payments.created_at', '>=' , $start_date)
                ->whereDate('payments.created_at', '<=' , $end_date)->sum('payments.amount');
            $cheque_payment_sale = DB::table('payments')
                ->where('paying_method', 'Cheque')
                ->whereNotNull('payments.sale_id')
                ->whereDate('payments.created_at', '>=' , $start_date)
                ->whereDate('payments.created_at', '<=' , $end_date)->sum('payments.amount');
            $gift_card_payment_sale = DB::table('payments')
                ->where('paying_method', 'Gift Card')
                ->whereNotNull('sale_id')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)
                ->sum('amount');
            $paypal_payment_sale = DB::table('payments')
                ->where('paying_method', 'Paypal')
                ->whereNotNull('sale_id')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)
                ->sum('amount');
            $deposit_payment_sale = DB::table('payments')
                ->where('paying_method', 'Deposit')
                ->whereNotNull('sale_id')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)
                ->sum('amount');
            $cobro_payment_sale = DB::table('payments')
                ->where('paying_method', 'Cobro')
                ->whereNotNull('sale_id')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)
                ->sum('amount');    
            $cash_payment_sale =  $payment_recieved - $credit_card_payment_sale - $cheque_payment_sale - $gift_card_payment_sale - $paypal_payment_sale - $deposit_payment_sale - $cobro_payment_sale;
            
            $pagosCCF = DB::table('payments')
                ->whereNotNull('purchase_id')
                ->where('paying_method', 'Cash')
                ->whereDate('created_at', '>=' , $start_date)
                ->whereDate('created_at', '<=' , $end_date)
                ->sum('payments.amount');

            $pagosCCFDET = DB::table('payments')
                ->join('purchases','payments.purchase_id','=','purchases.id') 
                ->join('suppliers','purchases.supplier_id','=','suppliers.id')
                ->select('payments.payment_reference', 'payments.purchase_id', 'amount', 'suppliers.name', 'purchases.created_at', 'purchases.invoice', 'payments.payment_note')                
                ->where('paying_method', 'Cash')
                ->whereNotNull('purchase_id')
                ->whereDate('payments.created_at', '>=' , $start_date)
                ->whereDate('payments.created_at', '<=' , $end_date)
                ->get();
            
           // $pagos_cheque_sale = Payment::join('payment_with_cheque','payments.id','=','payment_id')
//                ->join('sales','payments.sale_id','=','sales.id')         
//                ->join('customers','sales.customer_id','=','customers.id')         
//                ->select('payments.payment_reference', 'payments.sale_id', 'amount', 'customers.name',
//                    'payment_with_cheque.cheque_no', 'payment_with_cheque.banco', 'payment_with_cheque.reserva', 'payment_with_cheque.banco_reserva')
//                ->where('paying_method', 'Cheque')
//                ->whereDate('payments.created_at', '>=' , $start_date)
//                ->whereDate('payments.created_at', '<=' , $end_date)
//                ->orderBy('payments.id','desc')
//                ->groupBy('payments.payment_reference', 'payments.sale_id', 'amount', 'customers.name',
//                    'payment_with_cheque.cheque_no', 'payment_with_cheque.banco', 'payment_with_cheque.reserva', 'payment_with_cheque.banco_reserva')
//            ->paginate();


$pagos_cheque_sale = DB::table('payments')
    ->join('payment_with_cheque','payments.id','=','payment_id')
    ->join('sales','payments.sale_id','=','sales.id')         
    ->join('customers','sales.customer_id','=','customers.id')         
    ->select('payments.payment_reference', 'payments.sale_id', 'amount', 'customers.name',
        'payment_with_cheque.cheque_no', 'payment_with_cheque.banco', 'payment_with_cheque.reserva', 'payment_with_cheque.banco_reserva')
    ->where('paying_method', 'Cheque')
    ->whereDate('payments.created_at', '>=' , $start_date)
    ->whereDate('payments.created_at', '<=' , $end_date)
    ->get();
            
            $pagos_cobros_sale = DB::table('payments')
                ->join('sales','payments.sale_id','=','sales.id') 
                ->join('customers','sales.customer_id','=','customers.id')
                ->select('payments.payment_reference', 'payments.sale_id', 'amount', 'customers.name', 'sales.created_at', 'sales.reference_no')                
                ->where('paying_method', 'Cobro')
                ->whereNotNull('sale_id')
                ->whereDate('payments.created_at', '>=' , $start_date)
                ->whereDate('payments.created_at', '<=' , $end_date)
                ->get();
                
            $credit_sales = DB::table('sales')
                ->join('customers','sales.customer_id','=','customers.id')
                ->select('sales.reference_no', 'sales.grand_total', 'customers.name', 'sales.created_at') 
                ->where('sales.payment_method', '=', 'credito')
                ->whereDate('sales.created_at', '>=' , $start_date)
                ->whereDate('sales.created_at', '<=' , $end_date)
                ->get();    
            
            $return_sales_data = DB::table('returns')
                ->join('customers','returns.customer_id','=','customers.id')
                ->select('returns.reference_no', 'returns.grand_total', 'customers.name', 'returns.created_at', 'returns.document')                
                ->whereDate('returns.created_at', '>=' , $start_date)
                ->whereDate('returns.created_at', '<=' , $end_date)
                ->get();                
                
            // ->where('sales.payment_status', '<>', '4')
            
            //dd($cheque_payment_sale);

            // $datos1 = Sale::join('customers','sales.customer_id','=','customers.id')
            //     ->select('sales.id', 'sales.created_at', 'sales.reference_no',
            //         'sales.grand_total', 'sales.total_tax', 'sales.canceled',
            //         'sales.document_id', 'customers.name')
            //     ->whereDate('sales.created_at', '>=', $start_date)
            //     ->whereDate('sales.created_at', '<=', $end_date)
            //     ->orderBy('sales.id','desc')
            //     ->groupBy('sales.id', 'sales.created_at', 'sales.reference_no',
            //         'sales.grand_total', 'sales.total_tax', 'sales.canceled',
            //         'sales.document_id', 'customers.name')
            //     ->paginate(12);

             //dd($datos1);

            //dd($expenses);
            //dd($boxesCut);
            //dd($saleFac,$saleCcf,$total_sale_fac,$total_sale_ccf,$saleNumberFacI,$saleNumberFacF);

            $datos = DB::select('
                SELECT s.created_at, 
                    s.`reference_no`,
                    c.name,
                    round(s.grand_total/1.13,2) AS subtotal,
                    s.total_tax,
                    round((s.grand_total/1.13)*0.13,2) AS iva,
                    s.grand_total,
                    s.canceled,
                    s.document_id,
                    s.serie
                FROM `sales` s
                left join customers c on c.id = s.customer_id
                WHERE CONVERT(s.created_at,DATE) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                   '.$where);
        
            //dd($datos);
            //dd($saleNumberCcfI);
            
            if(isset($saleNumberCcfI)==""){
                $vsaleNumberCcfI = 0;    
            } else {
                $vsaleNumberCcfI = $saleNumberCcfI["reference_no"];
            }

            if(isset($saleNumberCcfF)==""){
                $vsaleNumberCcfF = 0;    
            } else {
                $vsaleNumberCcfF = $saleNumberCcfF["reference_no"];
            }

            if(isset($saleNumberFacI)==""){
                $vsaleNumberFacI = 0;    
            } else {
                $vsaleNumberFacI = $saleNumberFacI["reference_no"];
            }

            if(isset($saleNumberFacF)==""){
                $vsaleNumberFacF = 0;    
            } else {
                $vsaleNumberFacF = $saleNumberFacF["reference_no"];
            }

            $datos_vendedor  = DB::select('SELECT id, NAME FROM billers');

            $stotal = Sale::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total')/1.13;
            $siva = Sale::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total')/1.13*0.13;
            $sgrantotal = Sale::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total');

            return view('report.box_cut', compact('start_date','end_date','datos','datos_vendedor', 'stotal', 'saleFac','saleCcf','total_sale_fac','total_sale_ccf','saleNumberFacI','saleNumberFacF','boxesCuts','expenses','vsaleNumberFacF','vsaleNumberFacI','vsaleNumberCcfI','vsaleNumberCcfF', 'cash_payment_sale', 'cobro_payment_sale', 'cheque_payment_sale', 'deposit_payment_sale', 'expenses_sum', 'cash_registers', 'purchases_data', 'payrolls_data', 'pagosCCF', 'pagos_cobros_sale', 'credit_sales', 'pagos_cheque_sale', 'return_sales', 'return_sales_data', 'pagosCCFDET'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    
    public function PurchasesdayReport(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        
        if($role->hasPermissionTo('daily-sale')){
            $data = $request->all();
        
            if(isset($data["fecha_inicio"])==""){
            
               $start_date= date("Y-m-d");
               $end_date  =$start_date;
            }else{

               $start_date=$data["fecha_inicio"];
               $end_date  =$data["fecha_final"];
            }

            $where ="";
            
            if(isset($data["categoria"])!=""){
                $where = " AND  b.category_id = ".$data["categoria"];
            }

            if(isset($data["vendedor"])!=""){
                $where .= " AND  s.biller_id = ".$data["vendedor"];
            }

            $datos = DB::select('
                SELECT s.created_at, 
                    s.`invoice`,
                    c.name,
                    round((s.grand_total-s.order_tax)/1.13,2) AS subtotal,
                    s.total_tax, s.order_tax,
                    round(((s.grand_total-s.order_tax)/1.13)*0.13,2) AS iva,
                    s.grand_total
                FROM `purchases` s
                left join suppliers c on c.id = s.supplier_id
                WHERE CONVERT(s.created_at,DATE) BETWEEN "'.$start_date.'" AND "'.$end_date.'"
                   '.$where);

            $datos_vendedor  = DB::select('SELECT id, NAME FROM billers');

            $stotal = Purchase::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total')/1.13;
            $siva = Purchase::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total')/1.13*0.13;
            $sgrantotal = Purchase::whereBetween('created_at', [$start_date, $end_date])->sum('grand_total');

            return view('report.Purchases_daily_report', compact('start_date','end_date','datos','datos_vendedor', 'stotal'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    
    public function warehouseStockReport(Request $request)
    {
        $data = $request->all();
        //dd($data);
        $warehouse_id = $data['warehouse_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
 
        $role = Role::find(Auth::user()->role_id);

        if($role->hasPermissionTo('warehouse-stock-report-existence')){
            $total_item = DB::table('product_warehouse')
                ->join('products', 'product_warehouse.product_id', '=', 'products.id')
                ->where([
                    ['products.is_active', true],
                    ['product_warehouse.qty', '>' , 0]
                ])->count();

        $lims_product_warehouse = DB::table('product_warehouse')
            ->join('products','product_warehouse.product_id','=','products.id')
            ->join('warehouses','product_warehouse.warehouse_id','=','warehouses.id')
            ->join('units','products.unit_id','=','units.id')
            ->select('product_warehouse.qty', 'product_warehouse.warehouse_id', 'products.name',
                'products.cost', 'units.unit_name',
                DB::raw('round(sum(product_warehouse.qty)*products.cost,2) as total'))                
            ->where('product_warehouse.warehouse_id', '=', $warehouse_id)
            ->where('product_warehouse.qty', '>' , 0)
            ->groupBy('product_warehouse.qty', 'product_warehouse.warehouse_id', 'products.name',
                'products.cost','warehouses.name', 'units.unit_name')
            ->get();

        
            //$lims_product_warehouse = DB::table('product_warehouse')->where([['warehouse_id', $warehouse_id], ['product_warehouse.qty', '>' , 0],])->get();
            $total_qty = Product::where('is_active', true)->sum('qty');
            $total_price = DB::table('products')->where('is_active', true)->sum(DB::raw('price * qty'));
            $total_cost = DB::table('products')->where('is_active', true)->sum(DB::raw('cost * qty'));
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $warehouse_id = 0;
            
            //dd($lims_product_warehouse);
            return view('report.warehouse_stock_existence', compact('total_item', 'total_qty', 'total_price', 'total_cost', 'lims_warehouse_list', 'warehouse_id', 'start_date', 'end_date', 'lims_product_warehouse'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

}
