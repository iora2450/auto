<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="{{url('public/logo', $general_setting->site_logo)}}" />
    <title>{{$general_setting->site_title}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{url('manifest.json')}}">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrap-datepicker.min.css') ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('public/vendor/jquery-timepicker/jquery.timepicker.min.css') ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/awesome-bootstrap-checkbox.css') ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrap-select.min.css') ?>" type="text/css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="<?php echo asset('public/vendor/font-awesome/css/font-awesome.min.css') ?>" type="text/css">
    <!-- Drip icon font-->
    <link rel="stylesheet" href="<?php echo asset('public/vendor/dripicons/webfont.css') ?>" type="text/css">
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700">
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="<?php echo asset('public/css/grasp_mobile_progress_circle-1.0.0.min.css') ?>" type="text/css">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="<?php echo asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') ?>" type="text/css">
    <!-- virtual keybord stylesheet-->
    <link rel="stylesheet" href="<?php echo asset('public/vendor/keyboard/css/keyboard.css') ?>" type="text/css">
    <!-- date range stylesheet-->
    <link rel="stylesheet" href="<?php echo asset('public/vendor/daterange/css/daterangepicker.min.css') ?>" type="text/css">
    <!-- table sorter stylesheet-->
    <link rel="stylesheet" type="text/css" href="<?php echo asset('public/vendor/datatable/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo asset('public/css/style.default.css') ?>" id="theme-stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('public/css/dropzone.css') ?>">
    <link rel="stylesheet" href="<?php echo asset('public/css/style.css') ?>">
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->

    <script type="text/javascript" src="<?php echo asset('public/vendor/jquery/jquery.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/jquery/jquery-ui.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/jquery/bootstrap-datepicker.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/jquery/jquery.timepicker.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/popper.js/umd/popper.min.js') ?>">
    </script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/bootstrap-toggle/js/bootstrap-toggle.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/bootstrap/js/bootstrap-select.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/keyboard/js/jquery.keyboard.js') ?>"></script>  
    <script type="text/javascript" src="<?php echo asset('public/vendor/keyboard/js/jquery.keyboard.extension-autocomplete.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/js/grasp_mobile_progress_circle-1.0.0.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/jquery.cookie/jquery.cookie.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/chart.js/Chart.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/jquery-validation/jquery.validate.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/js/charts-custom.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/js/front.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/daterange/js/moment.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/daterange/js/knockout-3.4.2.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/daterange/js/daterangepicker.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/tinymce/js/tinymce/tinymce.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/js/dropzone.js') ?>"></script>
    
    <!-- table sorter js-->
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/pdfmake.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/vfs_fonts.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/jquery.dataTables.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/dataTables.bootstrap4.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/dataTables.buttons.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/buttons.bootstrap4.min.js') ?>">"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/buttons.colVis.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/buttons.html5.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/buttons.print.min.js') ?>"></script>

    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/sum().js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('public/vendor/datatable/dataTables.checkboxes.min.js') ?>"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script> 
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?php echo asset('public/css/custom-'.$general_setting->theme) ?>" type="text/css" id="custom-style">
  </head>
  
  <body onload="myFunction()">
    <div id="loader"></div>
      <nav class="side-navbar">
        <div class="side-navbar-wrapper">
          <div class="main-menu">
            <ul id="side-main-menu" class="side-menu list-unstyled">                  
              <li><a href="{{url('/')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>
                <?php
                  $role = DB::table('roles')->find(Auth::user()->role_id);
                  $category_permission_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'category'],
                        ['role_id', $role->id] ])->first();
                  $index_permission = DB::table('permissions')->where('name', 'products-index')->first();
                  $index_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $index_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $stock_count = DB::table('permissions')->where('name', 'stock_count')->first();
                      $stock_count_active = DB::table('role_has_permissions')->where([
                          ['permission_id', $stock_count->id],
                          ['role_id', $role->id]
                      ])->first();

                    $adjustment = DB::table('permissions')->where('name', 'adjustment')->first();
                    $adjustment_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $adjustment->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              @if($category_permission_active || $index_permission_active || $stock_count_active || $adjustment_active)
              <li><a href="#product" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-list"></i><span>{{__('file.product')}}</span><span></a>
                <ul id="product" class="collapse list-unstyled ">
                  @if($category_permission_active)
                  <li id="category-menu"><a href="{{route('category.index')}}">{{__('file.category')}}</a></li>
                  @endif
                  @if($index_permission_active)
                  <li id="product-list-menu"><a href="{{route('products.index')}}">{{__('file.product_list')}}</a></li>
                  <?php 
                    $add_permission = DB::table('permissions')->where('name', 'products-add')->first();
                    $add_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $add_permission->id],
                        ['role_id', $role->id]
                    ])->first();
                  ?>
                  @if($add_permission_active)
                  <li id="product-create-menu"><a href="{{route('products.create')}}">{{__('file.add_product')}}</a></li>
                  @endif
                  @endif
                  @if($adjustment_active)
                    <li id="adjustment-list-menu"><a href="{{route('qty_adjustment.index')}}">{{trans('file.Adjustment List')}}</a></li>
                    <li id="adjustment-create-menu"><a href="{{route('qty_adjustment.create')}}">{{trans('file.Add Adjustment')}}</a></li>
                  @endif
                  @if($stock_count_active)
                    <li id="stock-count-menu"><a href="{{route('stock-count.index')}}">{{trans('file.Stock Count')}}</a></li>
                  @endif
                </ul>
              </li>
              @endif
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'purchases-index')->first();
                  $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              @if($index_permission_active)
              <li><a href="#purchase" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-card"></i><span>{{trans('file.Purchase')}}</span></a>
                <ul id="purchase" class="collapse list-unstyled ">
                  <li id="purchase-list-menu"><a href="{{route('purchases.index')}}">{{trans('file.Purchase List')}}</a></li>
                  <?php 
                    $add_permission = DB::table('permissions')->where('name', 'purchases-add')->first();
                    $add_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $add_permission->id],
                        ['role_id', $role->id]
                    ])->first();
                  ?>
                  @if($add_permission_active)
                  <li id="purchase-create-menu"><a href="{{route('purchases.create')}}">{{trans('file.Add Purchase')}}</a></li>
                  @endif
                </ul>
              </li>
              @endif


        <?php 
                $index_permission = DB::table('permissions')->where('name', 'quedan-index')->first();
                  $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
             @if($index_permission_active)
              <li><a href="#quedan" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-card"></i><span>Quedans</span></a>
                <ul id="quedan" class="collapse list-unstyled ">
                  <li id="quedan-list-menu"><a href="{{route('quedan.index')}}">Lista de quedans</a></li>
                  <?php 
                    $add_permission = DB::table('permissions')->where('name', 'quedan-add')->first();
                    
                    $add_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $add_permission->id],
                        ['role_id', $role->id]
                    ])->first();
                  ?>
                  @if($add_permission_active)
                  <li id="purchase-create-menu"><a href="{{route('quedan.create')}}">Agregar quedan</a></li>
                  @endif
                </ul>
              </li>
              @endif



              <?php 
                $sale_index_permission = DB::table('permissions')->where('name', 'sales-index')->first();
                $sale_index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $sale_index_permission->id],
                        ['role_id', $role->id]
                    ])->first();

                $gift_card_permission = DB::table('permissions')->where('name', 'gift_card')->first();
                $gift_card_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $gift_card_permission->id],
                        ['role_id', $role->id]
                    ])->first();

                $coupon_permission = DB::table('permissions')->where('name', 'coupon')->first();
                $coupon_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $coupon_permission->id],
                        ['role_id', $role->id]
                    ])->first();

                $delivery_permission_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'delivery'],
                        ['role_id', $role->id] ])->first();

                $sale_add_permission = DB::table('permissions')->where('name', 'sales-add')->first();
                $sale_add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $sale_add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>

               @if($index_permission_active)
            <li><a href="#transfer" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-export"></i><span>{{trans('file.Transfer')}}</span></a>
            <ul id="transfer" class="collapse list-unstyled ">
                <li id="transfer-list-menu"><a href="{{route('transfers.index')}}">{{trans('file.Transfer List')}}</a></li>
                <?php
                    $add_permission_active = DB::table('permissions')
                        ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                        ->where([
                            ['permissions.name', 'transfers-add'],
                            ['role_id', $role->id] 
                        ])->first();
                ?>
                @if($add_permission_active)
                <li id="transfer-create-menu"><a href="{{route('transfers.create')}}">{{trans('file.Add Transfer')}}</a></li>
                <li id="transfer-import-menu"><a href="{{url('transfers/transfer_by_csv')}}">{{trans('file.Import Transfer By CSV')}}</a></li>
                @endif
                @if($adjustment_active)
                <li id="adjustment-list-menu"><a href="{{route('qty_adjustment.index')}}">{{trans('file.Adjustment List')}}</a></li>
                <li id="adjustment-create-menu"><a href="{{route('qty_adjustment.create')}}">{{trans('file.Add Adjustment')}}</a></li>
                @endif
                @if($adjustment_active)
                <li id="adjustment-list-menu"><a href="{{route('qty_adjustment.index')}}">{{trans('file.Adjustment List')}}</a></li>
                <li id="adjustment-create-menu"><a href="{{route('qty_adjustment.create')}}">{{trans('file.Add Adjustment')}}</a></li>
                @endif
            </ul>
            </li>
            @endif

            
              @if($sale_index_permission_active || $gift_card_permission_active || $coupon_permission_active || $delivery_permission_active)
              <li><a href="#sale" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-cart"></i><span>{{trans('file.Sale')}}</span></a>
                <ul id="sale" class="collapse list-unstyled ">
                  @if($sale_index_permission_active)
                  <li id="sale-list-menu"><a href="{{route('sales.index')}}">{{trans('file.Sale List')}}</a></li>
                    @if($sale_add_permission_active)
                    <li id="sale-create-menu"><a href="{{route('sales.create')}}">{{trans('file.Add Sale')}}</a></li>
                    @endif
                  @endif
                </ul>
              </li>
              @endif
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'quotes-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              @if($index_permission_active)
              <li><a href="#quotation" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-document"></i><span>{{trans('file.Quotation')}}</span><span></a>
                <ul id="quotation" class="collapse list-unstyled ">
                  <li id="quotation-list-menu"><a href="{{route('quotations.index')}}">{{trans('file.Quotation List')}}</a></li>
                  <?php 
                    $add_permission = DB::table('permissions')->where('name', 'quotes-add')->first();
                    $add_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $add_permission->id],
                        ['role_id', $role->id]
                    ])->first();
                  ?>
                  @if($add_permission_active)
                  <li id="quotation-create-menu"><a href="{{route('quotations.create')}}">{{trans('file.Add Quotation')}}</a></li>
                  @endif
                </ul>
              </li>
              @endif
              
              <?php 
                $sale_return_index_permission = DB::table('permissions')->where('name', 'returns-index')->first();
                $sale_return_index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $sale_return_index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
                
                $purchase_return_index_permission = DB::table('permissions')->where('name', 'purchase-return-index')->first();
                
                $purchase_return_index_permission_active = DB::table('role_has_permissions')->where([
                            ['permission_id', $purchase_return_index_permission->id],
                            ['role_id', $role->id]
                        ])->first();
              ?>
              @if($sale_return_index_permission_active || $purchase_return_index_permission_active)
              <li><a href="#return" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-return"></i><span>{{trans('file.return')}}</span></a>
                <ul id="return" class="collapse list-unstyled ">
                  @if($sale_return_index_permission_active)
                  <li id="sale-return-menu"><a href="{{route('return-sale.index')}}">{{trans('file.Sale')}}</a></li>
                  @endif
                  @if($purchase_return_index_permission_active)
                  <li id="purchase-return-menu"><a href="{{route('return-purchase.index')}}">{{trans('file.Purchase')}}</a></li>
                  @endif
                </ul>
              </li>
              @endif
              
              <?php 
                  $user_index_permission_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'users-index'],
                        ['role_id', $role->id] ])->first();

                  $customer_index_permission = DB::table('permissions')->where('name', 'customers-index')->first();
                  
                  $customer_index_permission_active = DB::table('role_has_permissions')->where([
                            ['permission_id', $customer_index_permission->id],
                            ['role_id', $role->id]
                        ])->first();

                  $biller_index_permission = DB::table('permissions')->where('name', 'billers-index')->first();
                  
                  $biller_index_permission_active = DB::table('role_has_permissions')->where([
                            ['permission_id', $biller_index_permission->id],
                            ['role_id', $role->id]
                        ])->first();

                  $supplier_index_permission = DB::table('permissions')->where('name', 'suppliers-index')->first();
                  
                  $supplier_index_permission_active = DB::table('role_has_permissions')->where([
                            ['permission_id', $supplier_index_permission->id],
                            ['role_id', $role->id]
                        ])->first();
              ?>
              @if($user_index_permission_active || $customer_index_permission_active || $biller_index_permission_active || $supplier_index_permission_active)
              <li><a href="#people" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-user"></i><span>{{trans('file.People')}}</span></a>
                <ul id="people" class="collapse list-unstyled ">
                  
                  @if($user_index_permission_active)
                  <li id="user-list-menu"><a href="{{route('user.index')}}">{{trans('file.User List')}}</a></li>
                  <?php $user_add_permission_active = DB::table('permissions')
                        ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                        ->where([
                          ['permissions.name', 'users-add'],
                          ['role_id', $role->id] ])->first();
                  ?>
                  @if($user_add_permission_active)
                  <li id="user-create-menu"><a href="{{route('user.create')}}">{{trans('file.Add User')}}</a></li>
                  @endif
                  @endif
                  
                  @if($customer_index_permission_active)
                  <li id="customer-list-menu"><a href="{{route('customer.index')}}">{{trans('file.Customer List')}}</a></li>
                  <?php 
                    $customer_add_permission = DB::table('permissions')->where('name', 'customers-add')->first();
                    $customer_add_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $customer_add_permission->id],
                        ['role_id', $role->id]
                    ])->first();
                  ?>
                  @if($customer_add_permission_active)
                  <li id="customer-create-menu"><a href="{{route('customer.create')}}">{{trans('file.Add Customer')}}</a></li>
                  @endif
                  @endif
                  
                  @if($biller_index_permission_active)
                  <li id="biller-list-menu"><a href="{{route('biller.index')}}">{{trans('file.Biller List')}}</a></li>
                  <?php 
                    $biller_add_permission = DB::table('permissions')->where('name', 'billers-add')->first();
                    $biller_add_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $biller_add_permission->id],
                        ['role_id', $role->id]
                    ])->first();
                  ?>
                  @if($biller_add_permission_active)
                  <li id="biller-create-menu"><a href="{{route('biller.create')}}">{{trans('file.Add Biller')}}</a></li>
                  @endif
                  @endif
                  
                  @if($supplier_index_permission_active)
                  <li id="supplier-list-menu"><a href="{{route('supplier.index')}}">{{trans('file.Supplier List')}}</a></li>
                  <?php 
                    $supplier_add_permission = DB::table('permissions')->where('name', 'suppliers-add')->first();
                    $supplier_add_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $supplier_add_permission->id],
                        ['role_id', $role->id]
                    ])->first();
                  ?>
                  @if($supplier_add_permission_active)
                  <li id="supplier-create-menu"><a href="{{route('supplier.create')}}">{{trans('file.Add Supplier')}}</a></li>
                  @endif
                  @endif
                </ul>
              </li>
              @endif

              <?php
                $profit_loss_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'profit-loss'],
                        ['role_id', $role->id] ])->first();
                $best_seller_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'best-seller'],
                        ['role_id', $role->id] ])->first();

                       $sales_graph_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'sales-graph'],
                        ['role_id', $role->id] ])->first();


                $warehouse_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'warehouse-report'],
                        ['role_id', $role->id] ])->first();
                $warehouse_stock_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'warehouse-stock-report'],
                        ['role_id', $role->id] ])->first();
                $product_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'product-report'],
                        ['role_id', $role->id] ])->first();
                $daily_sale_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'daily-sale'],
                        ['role_id', $role->id] ])->first();
                $monthly_sale_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'monthly-sale'],
                        ['role_id', $role->id]])->first();
                $daily_purchase_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'daily-purchase'],
                        ['role_id', $role->id] ])->first();
                $monthly_purchase_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'monthly-purchase'],
                        ['role_id', $role->id] ])->first();
                $purchase_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'purchase-report'],
                        ['role_id', $role->id] ])->first();
                $sale_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'sale-report'],
                        ['role_id', $role->id] ])->first();
                $payment_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'payment-report'],
                        ['role_id', $role->id] ])->first();
                $product_qty_alert_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'product-qty-alert'],
                        ['role_id', $role->id] ])->first();
                $user_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'user-report'],
                        ['role_id', $role->id] ])->first();

                $customer_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'customer-report'],
                        ['role_id', $role->id] ])->first();
                $supplier_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'supplier-report'], 
                        ['role_id', $role->id] ])->first();
                $due_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'due-report'],
                        ['role_id', $role->id] ])->first();

                /*Reportes nuevos*/
                $existence_report_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'existence-report'],
                        ['role_id', $role->id] ])->first();

                 $cost_per_day_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'cost-per-day'],
                        ['role_id', $role->id] ])->first();

                  $cost_per_seller_active = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'cost-per-seller'],
                        ['role_id', $role->id] ])->first();

                     $kardexReport = DB::table('permissions')
                      ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                      ->where([
                        ['permissions.name', 'kardexReport'],
                        ['role_id', $role->id] ])->first();

              ?>
              @if($profit_loss_active || $best_seller_active || $warehouse_report_active || $warehouse_stock_report_active || $product_report_active || $daily_sale_active || $monthly_sale_active || $daily_purchase_active || $monthly_purchase_active || $purchase_report_active || $sale_report_active || $payment_report_active || $product_qty_alert_active || $user_report_active || $customer_report_active || $supplier_report_active || $due_report_active || $existence_report_active || $cost_per_day_active || $cost_per_seller_active ||  $kardexReport) 
              <li><a href="#report" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-document-remove"></i><span>{{trans('file.Reports')}}</span></a>
                <ul id="report" class="collapse list-unstyled ">
                  @if($profit_loss_active)
                  <li id="profit-loss-report-menu">
                    {!! Form::open(['route' => 'report.profitLoss', 'method' => 'post', 'id' => 'profitLoss-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <a id="profitLoss-link" href="">{{trans('file.Summary Report')}}</a>
                    {!! Form::close() !!}
                  </li>
                  @endif
                  @if($best_seller_active)
                  <li id="best-seller-report-menu">
                    <a href="{{url('report/best_seller')}}">{{trans('file.Best Seller')}}</a>
                  </li>
                  @endif

                      @if($sales_graph_active)
                  <li id="best-seller-report-menu">
                    <a href="{{url('report/SalesGraph')}}">Grafica de ventas</a>
                  </li>
                  @endif
                  
                  @if($sales_graph_active)
                  <li id="best-seller-report-menu">
                    <a href="{{url('report/OrdersGraph')}}">Grafica de pedidos</a>
                  </li>
                  @endif


                  @if($product_report_active)
                  <li id="product-report-menu">
                    {!! Form::open(['route' => 'report.product', 'method' => 'post', 'id' => 'product-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <input type="hidden" name="warehouse_id" value="0" />
                    <a id="report-link" href="">{{trans('file.Product Report')}}</a>
                    {!! Form::close() !!}
                  </li>
                  @endif
                  @if($daily_sale_active)
                  <li id="daily-sale-report-menu">
                    <a href="{{url('report/daily_sale/'.date('Y').'/'.date('m'))}}">{{trans('file.Daily Sale')}}</a>
                  </li>
                  @endif
                  @if($monthly_sale_active)
                  <li id="monthly-sale-report-menu">
                    <a href="{{url('report/monthly_sale/'.date('Y'))}}">{{trans('file.Monthly Sale')}}</a>
                  </li>
                  @endif
                  @if($daily_purchase_active)
                  <li id="daily-purchase-report-menu">
                    <a href="{{url('report/daily_purchase/'.date('Y').'/'.date('m'))}}">{{trans('file.Daily Purchase')}}</a>
                  </li>
                  @endif
                  @if($monthly_purchase_active)
                  <li id="monthly-purchase-report-menu">
                    <a href="{{url('report/monthly_purchase/'.date('Y'))}}">{{trans('file.Monthly Purchase')}}</a>
                  </li>
                  @endif
                  @if($sale_report_active)
                  <li id="sale-report-menu">
                    {!! Form::open(['route' => 'report.sale', 'method' => 'post', 'id' => 'sale-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <input type="hidden" name="warehouse_id" value="0" />
                    <a id="sale-report-link" href="">{{trans('file.Sale Report')}}</a>
                    {!! Form::close() !!}
                  </li>
                  @endif
                  @if($payment_report_active)
                  <li id="payment-report-menu">
                    {!! Form::open(['route' => 'report.paymentByDate', 'method' => 'post', 'id' => 'payment-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <a id="payment-report-link" href="">{{trans('file.Payment Report')}}</a>
                    {!! Form::close() !!}
                  </li>
                  @endif
                  @if($purchase_report_active)
                  <li id="purchase-report-menu">
                    {!! Form::open(['route' => 'report.purchase', 'method' => 'post', 'id' => 'purchase-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <input type="hidden" name="warehouse_id" value="0" />
                    <a id="purchase-report-link" href="">{{trans('file.Purchase Report')}}</a>
                    {!! Form::close() !!}
                  </li>
                  @endif
                  @if($warehouse_report_active)
                  <li id="warehouse-report-menu">
                    <a id="warehouse-report-link" href="">{{trans('file.Warehouse Report')}}</a>
                  </li>
                  @endif
                  @if($warehouse_stock_report_active)
                  <li id="warehouse-stock-report-menu">
                    <a href="{{route('report.warehouseStock')}}">{{trans('file.Warehouse Stock Chart')}}</a>
                  </li>
                  @endif
                  @if($product_qty_alert_active)
                  <li id="qtyAlert-report-menu">
                    <a href="{{route('report.qtyAlert')}}">{{trans('file.Product Quantity Alert')}}</a>
                  </li>
                  @endif
                  @if($user_report_active)
                  <li id="user-report-menu">
                    <a id="user-report-link" href="">{{trans('file.User Report')}}</a>
                  </li>
                  @endif
                  @if($customer_report_active)
                  <li id="customer-report-menu">
                    <a id="customer-report-link" href="">{{trans('file.Customer Report')}}</a>
                  </li>
                  @endif
                  @if($supplier_report_active)
                  <li id="supplier-report-menu">
                    <a id="supplier-report-link" href="">{{trans('file.Supplier Report')}}</a>
                  </li>
                  @endif
                  @if($due_report_active)
                  <li id="due-report-menu">
                    {!! Form::open(['route' => 'report.dueByDate', 'method' => 'post', 'id' => 'due-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <a id="due-report-link" href="">{{trans('file.Due Report')}}</a>
                    {!! Form::close() !!}
                  </li>
                  @endif

                 @if($existence_report_active)
                  <li id="existence-report-menu">
             
                     <a href="{{url('report/existence_report')}}">Existence report</a>

                  </li>
                  @endif

                
                 @if($cost_per_day_active)
                  <li id="cost-per-day-menu">
        
                     <a href="{{url('report/cost_per_day_report')}}">Costo por dia</a>
                  </li>
                  @endif

                 @if($cost_per_seller_active)
                  <li id="cost-per-seller-menu">
                     <a href="{{url('report/cost_per_seller_report')}}">Costo por vendedor</a>
                  </li>
                  @endif


                  @if($kardexReport)
                  <li id="cost-per-seller-menu">
                     <a href="{{url('report/kardexReport')}}">Kardex</a>
                  </li>
                  @endif





                </ul>
              </li>
              @endif
              
              <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
                <ul id="setting" class="collapse list-unstyled ">
                  <?php
                      $send_notification_permission = DB::table('permissions')->where('name', 'send_notification')->first();
                      $send_notification_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $send_notification_permission->id],
                                  ['role_id', $role->id]
                              ])->first();

                      $warehouse_permission = DB::table('permissions')->where('name', 'warehouse')->first();
                      $warehouse_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $warehouse_permission->id],
                                  ['role_id', $role->id]
                              ])->first();

                      $customer_group_permission = DB::table('permissions')->where('name', 'customer_group')->first();
                      $customer_group_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $customer_group_permission->id],
                                  ['role_id', $role->id]
                              ])->first();

                      $brand_permission = DB::table('permissions')->where('name', 'brand')->first();
                      $brand_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $brand_permission->id],
                                  ['role_id', $role->id]
                              ])->first();

                      $unit_permission = DB::table('permissions')->where('name', 'unit')->first();
                      $unit_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $unit_permission->id],
                                  ['role_id', $role->id]
                              ])->first();

                      $currency_permission = DB::table('permissions')->where('name', 'currency')->first();
                      $currency_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $currency_permission->id],
                                  ['role_id', $role->id]
                              ])->first();

                      $tax_permission = DB::table('permissions')->where('name', 'tax')->first();
                      $tax_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $tax_permission->id],
                                  ['role_id', $role->id]
                              ])->first();

                      $general_setting_permission = DB::table('permissions')->where('name', 'general_setting')->first();
                      $general_setting_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $general_setting_permission->id],
                                  ['role_id', $role->id]
                              ])->first();

                      $typedocument_index_permission = DB::table('permissions')->where('name', 'typedocument-index')->first();
                      $typedocument_index_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $typedocument_index_permission->id],
                                  ['role_id', $role->id]
                      ])->first();              

                      $backup_database_permission = DB::table('permissions')->where('name', 'backup_database')->first();
                      $backup_database_permission_active = DB::table('role_has_permissions')->where([
                                  ['permission_id', $backup_database_permission->id],
                                  ['role_id', $role->id]
                              ])->first();
                  ?>
                  @if($role->id <= 2)
                  <li id="role-menu"><a href="{{route('role.index')}}">{{trans('file.Role Permission')}}</a></li>
                  @endif
                  @if($send_notification_permission_active)
                  <li id="notification-menu">
                    <a href="" id="send-notification">{{trans('file.Send Notification')}}</a>
                  </li>
                  @endif
                  @if($warehouse_permission_active)
                  <li id="warehouse-menu"><a href="{{route('warehouse.index')}}">{{trans('file.Warehouse')}}</a></li>
                  @endif
                  @if($customer_group_permission_active)
                  <li id="customer-group-menu"><a href="{{route('customer_group.index')}}">{{trans('file.Customer Group')}}</a></li>
                  @endif
                  @if($brand_permission_active)
                  <li id="brand-menu"><a href="{{route('brand.index')}}">{{trans('file.Brand')}}</a></li>
                  @endif
                  @if($unit_permission_active)
                  <li id="unit-menu"><a href="{{route('unit.index')}}">{{trans('file.Unit')}}</a></li>
                  @endif
                  @if($currency_permission_active)
                  <li id="currency-menu"><a href="{{route('currency.index')}}">{{trans('file.Currency')}}</a></li>
                  @endif
                  @if($tax_permission_active)
                  <li id="tax-menu"><a href="{{route('tax.index')}}">{{trans('file.Tax')}}</a></li>
                  @endif
                  <li id="user-menu"><a href="{{route('user.profile', ['id' => Auth::id()])}}">{{trans('file.User Profile')}}</a></li>
                  @if($backup_database_permission_active)
                  <li><a href="{{route('setting.backup')}}">{{trans('file.Backup Database')}}</a></li>
                  @endif
                  @if($general_setting_permission_active)
                  <li id="general-setting-menu"><a href="{{route('setting.general')}}">{{trans('file.General Setting')}}</a></li>
                  @endif
                  @if($typedocument_index_permission_active)
                  <li id="typedocument-menu"><a href="{{route('typeDocument.index')}}">{{trans('file.Type Documents')}}</a></li>
                  @endif
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <!-- navbar-->
      <header class="header">
        <nav class="navbar">
          <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
              <a id="toggle-btn" href="#" class="menu-btn">
                <i class="fa fa-bars"></i>
              </a>
              <span class="brand-big">@if($general_setting->site_logo)<img src="{{url('public/logo', $general_setting->site_logo)}}" width="50">&nbsp;&nbsp;@endif<a href="{{url('/')}}"><h1 class="d-inline">{{$general_setting->site_title}}</h1></a></span>
              
              <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                <?php
                  $add_permission = DB::table('permissions')->where('name', 'sales-add')->first();
                  $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                  ])->first();
                  $empty_database_permission = DB::table('permissions')->where('name', 'empty_database')->first();
                  $empty_database_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $empty_database_permission->id],
                    ['role_id', $role->id]
                  ])->first();
                ?>
                <li class="nav-item">
                  <a id="btnFullscreen">
                    <i class="dripicons-expand"></i>
                  </a>
                </li>
                @if($product_qty_alert_active)
                  @if(($alert_product + count(\Auth::user()->unreadNotifications)) > 0)
                  <li class="nav-item" id="notification-icon">
                        <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-bell"></i><span class="badge badge-danger notification-number">{{$alert_product + count(\Auth::user()->unreadNotifications)}}</span>
                        </a>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default notifications" user="menu">
                            <li class="notifications">
                              <a href="{{route('report.qtyAlert')}}" class="btn btn-link"> {{$alert_product}} product exceeds alert quantity</a>
                            </li>
                            @foreach(\Auth::user()->unreadNotifications as $key => $notification)
                                <li class="notifications">
                                    <a href="#" class="btn btn-link">{{ $notification->data['message'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                  </li>
                  @elseif(count(\Auth::user()->unreadNotifications) > 0)
                  <li class="nav-item" id="notification-icon">
                        <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-bell"></i><span class="badge badge-danger notification-number">{{count(\Auth::user()->unreadNotifications)}}</span>
                        </a>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default notifications" user="menu">
                            @foreach(\Auth::user()->unreadNotifications as $key => $notification)
                                <li class="notifications">
                                    <a href="#" class="btn btn-link">{{ $notification->data['message'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                  </li>
                  @endif
                @endif
                <li class="nav-item">
                      <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-web"></i> <span>{{__('file.language')}}</span> <i class="fa fa-angle-down"></i></a>
                      <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                          <li>
                            <a href="{{ url('language_switch/en') }}" class="btn btn-link"> English</a>
                          </li>
                          <li>
                            <a href="{{ url('language_switch/es') }}" class="btn btn-link"> Español</a>
                          </li>
                      </ul>
                </li>
                <li class="nav-item">
                  <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-user"></i> <span>{{ucfirst(Auth::user()->name)}}</span> <i class="fa fa-angle-down"></i>
                  </a>
                  <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                      <li> 
                        <a href="{{route('user.profile', ['id' => Auth::id()])}}"><i class="dripicons-user"></i> {{trans('file.profile')}}</a>
                      </li>
                      @if($general_setting_permission_active)
                      <li> 
                        <a href="{{route('setting.general')}}"><i class="dripicons-gear"></i> {{trans('file.settings')}}</a>
                      </li>
                      @endif
                      <li>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();"><i class="dripicons-power"></i>
                            {{trans('file.logout')}}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                      </li>
                  </ul>
                </li> 
              </ul>
            </div>
          </div>
        </nav>
      </header>
    <div class="page">
      
      <!-- notification modal -->
      <div id="notification-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Send Notification')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                  <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => 'notifications.store', 'method' => 'post']) !!}
                      <div class="row">
                          <?php 
                              $lims_user_list = DB::table('users')->where([
                                ['is_active', true],
                                ['id', '!=', \Auth::user()->id]
                              ])->get();
                          ?>
                          <div class="col-md-6 form-group">
                              <label>{{trans('file.User')}} *</label>
                              <select name="user_id" class="selectpicker form-control" required data-live-search="true" data-live-search-style="begins" title="Select user...">
                                  @foreach($lims_user_list as $user)
                                  <option value="{{$user->id}}">{{$user->name . ' (' . $user->email. ')'}}</option>
                                  @endforeach
                              </select>
                          </div>
                          <div class="col-md-12 form-group">
                              <label>{{trans('file.Message')}} *</label>
                              <textarea rows="5" name="message" class="form-control" required></textarea>
                          </div>
                      </div>
                      <div class="form-group">
                          <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                      </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
      </div>
      <!-- end notification modal -->

      <!-- expense modal -->
      <div id="expense-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Expense')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                  <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => 'expenses.store', 'method' => 'post']) !!}
                    <?php 
                      $lims_expense_category_list = DB::table('expense_categories')->where('is_active', true)->get();
                      if(Auth::user()->role_id > 2)
                        $lims_warehouse_list = DB::table('warehouses')->where([
                          ['is_active', true],
                          ['id', Auth::user()->warehouse_id]
                        ])->get();
                      else
                        $lims_warehouse_list = DB::table('warehouses')->where('is_active', true)->get();
                      $lims_account_list = \App\Account::where('is_active', true)->get();
                    
                    ?>
                      <div class="row">
                        <div class="col-md-6 form-group">
                            <label>{{trans('file.Expense Category')}} *</label>
                            <select name="expense_category_id" class="selectpicker form-control" required data-live-search="true" data-live-search-style="begins" title="Select Expense Category...">
                                @foreach($lims_expense_category_list as $expense_category)
                                <option value="{{$expense_category->id}}">{{$expense_category->name . ' (' . $expense_category->code. ')'}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{trans('file.Warehouse')}} *</label>
                            <select name="warehouse_id" class="selectpicker form-control" required data-live-search="true" data-live-search-style="begins" title="Select Warehouse...">
                                @foreach($lims_warehouse_list as $warehouse)
                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{trans('file.Amount')}} *</label>
                            <input type="number" name="amount" step="any" required class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label> {{trans('file.Account')}}</label>
                            <select class="form-control selectpicker" name="account_id">
                            @foreach($lims_account_list as $account)
                                @if($account->is_default)
                                <option selected value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                                @else
                                <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                                @endif
                            @endforeach
                            </select>
                        </div>
                      </div>
                      <div class="form-group">
                          <label>{{trans('file.Note')}}</label>
                          <textarea name="note" rows="3" class="form-control"></textarea>
                      </div>
                      <div class="form-group">
                          <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                      </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
      </div>
      <!-- end expense modal -->

      <!-- account modal -->
      <div id="account-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Account')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                  <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => 'accounts.store', 'method' => 'post']) !!}
                      <div class="form-group">
                          <label>{{trans('file.Account No')}} *</label>
                          <input type="text" name="account_no" required class="form-control">
                      </div>
                      <div class="form-group">
                          <label>{{trans('file.name')}} *</label>
                          <input type="text" name="name" required class="form-control">
                      </div>
                      <div class="form-group">
                          <label>{{trans('file.Initial Balance')}}</label>
                          <input type="number" name="initial_balance" step="any" class="form-control">
                      </div>
                      <div class="form-group">
                          <label>{{trans('file.Note')}}</label>
                          <textarea name="note" rows="3" class="form-control"></textarea>
                      </div>
                      <div class="form-group">
                          <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                      </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
      </div>
      <!-- end account modal -->
      
      <!-- account statement modal -->
      <div id="account-statement-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Account Statement')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                  <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => 'accounts.statement', 'method' => 'post']) !!}
                      <div class="row">
                        <div class="col-md-6 form-group">
                            <label> {{trans('file.Account')}}</label>
                            <select class="form-control selectpicker" name="account_id">
                            @foreach($lims_account_list as $account)
                                <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label> {{trans('file.Type')}}</label>
                            <select class="form-control selectpicker" name="type">
                                <option value="0">{{trans('file.All')}}</option>
                                <option value="1">{{trans('file.Debit')}}</option>
                                <option value="2">{{trans('file.Credit')}}</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label>{{trans('file.Choose Your Date')}}</label>
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" required />
                                <input type="hidden" name="start_date" />
                                <input type="hidden" name="end_date" />
                            </div>
                        </div>
                      </div>
                      <div class="form-group">
                          <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                      </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
      </div>
      <!-- end account statement modal -->

      <!-- warehouse modal -->
      <div id="warehouse-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Warehouse Report')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                  <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => 'report.warehouse', 'method' => 'post']) !!}
                    <?php 
                      $lims_warehouse_list = DB::table('warehouses')->where('is_active', true)->get();
                    ?>
                      <div class="form-group">
                          <label>{{trans('file.Warehouse')}} *</label>
                          <select name="warehouse_id" class="selectpicker form-control" required data-live-search="true" id="warehouse-id" data-live-search-style="begins" title="Select warehouse...">
                              @foreach($lims_warehouse_list as $warehouse)
                              <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                              @endforeach
                          </select>
                      </div>

                      <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                      <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />

                      <div class="form-group">
                          <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                      </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
      </div>
      <!-- end warehouse modal -->

      <!-- user modal -->
      <div id="user-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{trans('file.User Report')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                  <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => 'report.user', 'method' => 'post']) !!}
                    <?php 
                      $lims_user_list = DB::table('users')->where('is_active', true)->get();
                    ?>
                      <div class="form-group">
                          <label>{{trans('file.User')}} *</label>
                          <select name="user_id" class="selectpicker form-control" required data-live-search="true" id="user-id" data-live-search-style="begins" title="Select user...">
                              @foreach($lims_user_list as $user)
                              <option value="{{$user->id}}">{{$user->name . ' (' . $user->phone. ')'}}</option>
                              @endforeach
                          </select>
                      </div>

                      <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                      <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />

                      <div class="form-group">
                          <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                      </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
      </div>
      <!-- end user modal -->

      <!-- customer modal -->
      <div id="customer-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Customer Report')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                  <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => 'report.customer', 'method' => 'post']) !!}
                    <?php 
                      $lims_customer_list = DB::table('customers')->where('is_active', true)->get();
                    ?>
                      <div class="form-group">
                          <label>{{trans('file.customer')}} *</label>
                          <select name="customer_id" class="selectpicker form-control" required data-live-search="true" id="customer-id" data-live-search-style="begins" title="Select customer...">
                              @foreach($lims_customer_list as $customer)
                              <option value="{{$customer->id}}">{{$customer->name . ' (' . $customer->phone_number. ')'}}</option>
                              @endforeach
                          </select>
                      </div>

                      <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                      <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />

                      <div class="form-group">
                          <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                      </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
      </div>
      <!-- end customer modal -->

      <!-- supplier modal -->
      <div id="supplier-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Supplier Report')}}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                  <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => 'report.supplier', 'method' => 'post']) !!}
                    <?php 
                      $lims_supplier_list = DB::table('suppliers')->where('is_active', true)->get();
                    ?>
                      <div class="form-group">
                          <label>{{trans('file.Supplier')}} *</label>
                          <select name="supplier_id" class="selectpicker form-control" required data-live-search="true" id="supplier-id" data-live-search-style="begins" title="Select Supplier...">
                              @foreach($lims_supplier_list as $supplier)
                              <option value="{{$supplier->id}}">{{$supplier->name . ' (' . $supplier->phone_number. ')'}}</option>
                              @endforeach
                          </select>
                      </div>

                      <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                      <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />

                      <div class="form-group">
                          <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                      </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
      </div>
      <!-- end supplier modal -->
      
      <div style="display:none" id="content" class="animate-bottom">
          @yield('content')
      </div>

      <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <p>&copy; {{$general_setting->site_title}} | {{trans('file.Developed')}} {{trans('file.By')}} <span class="external">{{$general_setting->developed_by}}</span></p>
            </div>
          </div>
        </div>
      </footer>
    </div>
    @yield('scripts')
    <script>
        if ('serviceWorker' in navigator ) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/saleproposmajed/service-worker.js').then(function(registration) {
                    // Registration was successful
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    // registration failed :(
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    <script type="text/javascript">
      
      var alert_product = <?php echo json_encode($alert_product) ?>;

      if ($(window).outerWidth() > 1199) {
          $('nav.side-navbar').removeClass('shrink');
      }
      function myFunction() {
          setTimeout(showPage, 150);
      }
      function showPage() {
        document.getElementById("loader").style.display = "none";
        document.getElementById("content").style.display = "block";
      }

      $("div.alert").delay(3000).slideUp(750);

      function confirmDelete() {
          if (confirm("Are you sure want to delete?")) {
              return true;
          }
          return false;
      }

      $("li#notification-icon").on("click", function (argument) {
          $.get('notifications/mark-as-read', function(data) {
              $("span.notification-number").text(alert_product);
          });
      });
      
      $("a#add-expense").click(function(e){
        e.preventDefault();
        $('#expense-modal').modal();
      });

      $("a#send-notification").click(function(e){
        e.preventDefault();
        $('#notification-modal').modal();
      });

      $("a#add-account").click(function(e){
        e.preventDefault();
        $('#account-modal').modal();
      });

      $("a#account-statement").click(function(e){
        e.preventDefault();
        $('#account-statement-modal').modal();
      });

      $("a#profitLoss-link").click(function(e){
        e.preventDefault();
        $("#profitLoss-report-form").submit();
      });

      $("a#report-link").click(function(e){
        e.preventDefault();
        $("#product-report-form").submit();
      });

      $("a#purchase-report-link").click(function(e){
        e.preventDefault();
        $("#purchase-report-form").submit();
      });

      $("a#sale-report-link").click(function(e){
        e.preventDefault();
        $("#sale-report-form").submit();
      });

      $("a#payment-report-link").click(function(e){
        e.preventDefault();
        $("#payment-report-form").submit();
      });

      $("a#warehouse-report-link").click(function(e){
        e.preventDefault();
        $('#warehouse-modal').modal();
      });

      $("a#user-report-link").click(function(e){
        e.preventDefault();
        $('#user-modal').modal();
      });

      $("a#customer-report-link").click(function(e){
        e.preventDefault();
        $('#customer-modal').modal();
      });

      $("a#supplier-report-link").click(function(e){
        e.preventDefault();
        $('#supplier-modal').modal();
      });

      $("a#due-report-link").click(function(e){
        e.preventDefault();
        $("#due-report-form").submit();
      });

      $(".daterangepicker-field").daterangepicker({
          callback: function(startDate, endDate, period){
            var start_date = startDate.format('YYYY-MM-DD');
            var end_date = endDate.format('YYYY-MM-DD');
            var title = start_date + ' To ' + end_date;
            $(this).val(title);
            $('#account-statement-modal input[name="start_date"]').val(start_date);
            $('#account-statement-modal input[name="end_date"]').val(end_date);
          }
      });

      $('.selectpicker').selectpicker({
          style: 'btn-link',
      });
    </script>
  </body>
</html>