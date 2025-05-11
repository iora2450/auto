<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warehouse;
use App\Product_Warehouse;
use App\Product;
use App\Adjustment;
use App\ProductAdjustment;
use DB;
use App\StockCount;
use Auth;
use NumberToWords\NumberToWords;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdjustmentController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if( $role->hasPermissionTo('adjustment') ) {
            if(Auth::user()->role_id > 2 && $general_setting->staff_access == 'own')
                $lims_adjustment_all = Adjustment::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_adjustment_all = Adjustment::orderBy('id', 'desc')->get();
            return view('adjustment.index', compact('lims_adjustment_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function getProduct($id)
    {
        $lims_product_warehouse_data = DB::table('products')
            ->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->where([ ['products.is_active', 1], ['product_warehouse.warehouse_id', $id] ])->select('product_warehouse.qty', 'products.code', 'products.name', 'products.cost')->get();
        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_cost = [];
        $product_data = [];
        foreach ($lims_product_warehouse_data as $product_warehouse) 
        {
            $product_qty[] = $product_warehouse->qty;
            $product_code[] =  $product_warehouse->code;
            $product_name[] = $product_warehouse->name;
            $product_cost[] = $product_warehouse->cost;
        }

        $product_data[] = $product_code;
        $product_data[] = $product_name;
        $product_data[] = $product_qty;
        $product_data[] = $product_cost;
        
        return $product_data;
    }

    public function limsProductSearch(Request $request)
    {
        $product_code = explode(" ", $request['data']);
        $lims_product_data = Product::where('code', $product_code[0])->first();

        $product[] = $lims_product_data->name;
        $product[] = $lims_product_data->code;
        $product[] = $lims_product_data->id;
        $product[] = $lims_product_data->cost;
        return $product;
    }

    public function create()
    {
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('adjustment.create', compact('lims_warehouse_list'));
    }

    public function store(Request $request)
    {
        

        $data = $request->except('document');
        //dd($data);
        if( isset($data['stock_count_id']) ){
            $lims_stock_count_data = StockCount::find($data['stock_count_id']);
            $lims_stock_count_data->is_adjusted = true;
            $lims_stock_count_data->save();
        }
        $data['reference_no'] = 'adr-' . date("Ymd") . '-'. date("his");
        $document = $request->document;
        if ($document) {
            $documentName = $document->getClientOriginalName();
            $document->move('public/documents/adjustment', $documentName);
            $data['document'] = $documentName;
        }
        Adjustment::create($data);

        $lims_adjustment_data = Adjustment::latest()->first();
        $product_id = $data['product_id'];
        $qty = $data['qty'];
        $product_cost = $data['product_cost'];
        $action = $data['action'];

        //dd($product_cost);
        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::find($pro_id);
            $lims_product_warehouse_data = Product_Warehouse::where([
                ['product_id', $pro_id],
                ['warehouse_id', $data['warehouse_id'] ],
                ])->first();
            if($action[$key] == '-'){
                $lims_product_data->qty -= $qty[$key];
                $lims_product_warehouse_data->qty -= $qty[$key];
            }
            elseif($action[$key] == '+'){
                $lims_product_data->qty += $qty[$key];
                $lims_product_warehouse_data->qty += $qty[$key];
            }
            $lims_product_data->save();
            $lims_product_warehouse_data->save();

            $product_adjustment['product_id'] = $pro_id;
            $product_adjustment['adjustment_id'] = $lims_adjustment_data->id;
            $product_adjustment['qty'] = $qty[$key];
            $product_adjustment['unit_cost'] = $product_cost[$key];
            $product_adjustment['action'] = $action[$key];            
            ProductAdjustment::create($product_adjustment);


        }

        $signo = 0;
        if($action[$key] == '-'){

           $signo =-1;
        }
        if($action[$key] == '+'){
        
            $signo =1;

        }
         

         $this->recordKardex($lims_adjustment_data->id, 3,$signo);


        return redirect('qty_adjustment')->with('message', 'Data inserted successfully');
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

        if($typetransaction ==3){
            //Datos de la venta disponibles. 
            if($signo == 1){
                $lims_purchase_data          = Adjustment::find($idtran);
                $lims_product_purchase_data = ProductAdjustment::where('adjustment_id', $idtran)->get();
                $concepto='Ajuste positiva'; 
                $sale_id = $idtran; 
            }else{
                $lims_purchase_data         =  Adjustment::find($idtran);
                $lims_product_purchase_data =  ProductAdjustment::where('adjustment_id', $idtran)->get();
                $concepto='Ajuste en contra'; 
                $sale_id = $idtran; 
            }
            
            $referencia = $lims_purchase_data->reference_no; 
            
            $lims_warehouse_data = Warehouse::find($lims_purchase_data->warehouse_id);
                //dd($lims_supplier_data);
            if($lims_warehouse_data->count() >0){
                $name_traslado = $lims_warehouse_data->id;  
                $signo1 = $signo;
            }else{
                $name_supplier =''; 
            } 
            
            $documento = $idtran;
            $name_supplier =''; 
 
        foreach ($lims_product_purchase_data as $key => $product_purchase_data) {
            $product = Product::find($product_purchase_data->product_id);
           
   

         $data = DB::select("SELECT IFNULL(MAX(correlativo),0)+1 as correlativo FROM kardex WHERE product_id=".$product_purchase_data->product_id); 


         $correlativo=  $data[0]->correlativo; 
         /*Esta operacion se hace porque a estas alturas el campo QTY ya fue afectado */
         if($signo>0){
                 $stock      =  $product->qty-$product_purchase_data->qty; 
            }else{

                 $stock      =  $product->qty+$product_purchase_data->qty; 
            }
         
         $saldo      =  $stock+($product_purchase_data->qty*$signo);
         $costo      =  $product->cost;   
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
                         ".$documento.",
                         ".$sale_id.",
                         '".$name_traslado."'

                      ) ;
                        ");

        }
         
      }



  
      
    }



    public function edit($id)
    {
        $lims_adjustment_data = Adjustment::find($id);
        $lims_product_adjustment_data = ProductAdjustment::where('adjustment_id', $id)->get();
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('adjustment.edit', compact('lims_adjustment_data', 'lims_warehouse_list', 'lims_product_adjustment_data'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('document');
        $document = $request->document;
        if ($document) {
            $documentName = $document->getClientOriginalName();
            $document->move('public/documents/adjustment', $documentName);
            $data['document'] = $documentName;
        }

        $lims_adjustment_data = Adjustment::find($id);
        $lims_product_adjustment_data = ProductAdjustment::where('adjustment_id', $id)->get();
        $product_id = $data['product_id'];
        $qty = $data['qty'];
        $action = $data['action'];

        foreach ($lims_product_adjustment_data as $key => $product_adjustment_data) {
            $old_product_id[] = $product_adjustment_data->product_id;
            $lims_product_data = Product::find($product_adjustment_data->product_id);
            $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $product_adjustment_data->product_id],
                    ['warehouse_id', $lims_adjustment_data->warehouse_id]
                ])->first();
            if($product_adjustment_data->action == '-'){
                $lims_product_data->qty += $product_adjustment_data->qty;
                $lims_product_warehouse_data->qty += $product_adjustment_data->qty;
            }
            elseif($product_adjustment_data->action == '+'){
                $lims_product_data->qty -= $product_adjustment_data->qty;
                $lims_product_warehouse_data->qty -= $product_adjustment_data->qty;
            }
            $lims_product_data->save();
            $lims_product_warehouse_data->save();

            if( !(in_array($old_product_id[$key], $product_id)) )
                $product_adjustment_data->delete();
        }

        foreach ($product_id as $key => $pro_id) {
            $lims_product_data = Product::find($pro_id);
            $lims_product_warehouse_data = Product_Warehouse::where([
                ['product_id', $pro_id],
                ['warehouse_id', $data['warehouse_id'] ],
                ])->first();
            if($action[$key] == '-'){
                $lims_product_data->qty -= $qty[$key];
                $lims_product_warehouse_data->qty -= $qty[$key];
            }
            elseif($action[$key] == '+'){
                $lims_product_data->qty += $qty[$key];
                $lims_product_warehouse_data->qty += $qty[$key];
            }
            $lims_product_data->save();
            $lims_product_warehouse_data->save();

            $product_adjustment['product_id'] = $pro_id;
            $product_adjustment['adjustment_id'] = $id;
            $product_adjustment['qty'] = $qty[$key];
            $product_adjustment['action'] = $action[$key];

            if(in_array($pro_id, $old_product_id)){
                ProductAdjustment::where([
                ['adjustment_id', $id],
                ['product_id', $pro_id]
                ])->update($product_adjustment);
            }
            else
                ProductAdjustment::create($product_adjustment);
        }
        $lims_adjustment_data->update($data);
        return redirect('qty_adjustment')->with('message', 'Data updated successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $adjustment_id = $request['adjustmentIdArray'];
        foreach ($adjustment_id as $id) {
            $lims_adjustment_data = Adjustment::find($id);
            $lims_product_adjustment_data = ProductAdjustment::where('adjustment_id', $id)->get();
            foreach ($lims_product_adjustment_data as $key => $product_adjustment_data) {
                $lims_product_data = Product::find($product_adjustment_data->product_id);
                $lims_product_warehouse_data = Product_Warehouse::where([
                        ['product_id', $product_adjustment_data->product_id],
                        ['warehouse_id', $lims_adjustment_data->warehouse_id]
                    ])->first();
                if($product_adjustment_data->action == '-'){
                    $lims_product_data->qty += $product_adjustment_data->qty;
                    $lims_product_warehouse_data->qty += $product_adjustment_data->qty;
                }
                elseif($product_adjustment_data->action == '+'){
                    $lims_product_data->qty -= $product_adjustment_data->qty;
                    $lims_product_warehouse_data->qty -= $product_adjustment_data->qty;
                }
                $lims_product_data->save();
                $lims_product_warehouse_data->save();
                $product_adjustment_data->delete();
            }
            $lims_adjustment_data->delete();
        }
        return 'Data deleted successfully';
    }

    public function destroy($id)
    {
        $lims_adjustment_data = Adjustment::find($id);
        $lims_product_adjustment_data = ProductAdjustment::where('adjustment_id', $id)->get();
        foreach ($lims_product_adjustment_data as $key => $product_adjustment_data) {
            $lims_product_data = Product::find($product_adjustment_data->product_id);
            $lims_product_warehouse_data = Product_Warehouse::where([
                    ['product_id', $product_adjustment_data->product_id],
                    ['warehouse_id', $lims_adjustment_data->warehouse_id]
                ])->first();
            if($product_adjustment_data->action == '-'){
                $lims_product_data->qty += $product_adjustment_data->qty;
                $lims_product_warehouse_data->qty += $product_adjustment_data->qty;
            }
            elseif($product_adjustment_data->action == '+'){
                $lims_product_data->qty -= $product_adjustment_data->qty;
                $lims_product_warehouse_data->qty -= $product_adjustment_data->qty;
            }
            $lims_product_data->save();
            $lims_product_warehouse_data->save();
            $product_adjustment_data->delete();
        }
        $lims_adjustment_data->delete();
        return redirect('qty_adjustment')->with('not_permitted', 'Data deleted successfully');
    }

    public function genAdjustment($id)
    {
        $lims_sale_data = Adjustment::find($id);
        $lims_product_sale_data = ProductAdjustment::where('adjustment_id', $id)->get();
        $lims_warehouse_data = Warehouse::find($lims_sale_data->warehouse_id);

        $numberToWords = new NumberToWords();
        if(\App::getLocale() == 'ar' || \App::getLocale() == 'hi' || \App::getLocale() == 'vi' || \App::getLocale() == 'en-gb')
            $numberTransformer = $numberToWords->getNumberTransformer('en');
        else
            $numberTransformer = $numberToWords->getNumberTransformer(\App::getLocale());
        $numberInWords = $numberTransformer->toWords($lims_sale_data->grand_total);


        dd($lims_product_sale_data);
        return view('sale.invoice', compact('lims_sale_data', 'lims_product_sale_data', 'lims_biller_data', 'lims_warehouse_data', 'lims_customer_data', 'lims_payment_data', 'numberInWords'));
    }

}
