 <?php $__env->startSection('content'); ?>
<?php if($errors->has('phone_number')): ?>
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e($errors->first('phone_number')); ?></div>
<?php endif; ?>
<?php if(session()->has('message')): ?>
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo session()->get('message'); ?></div> 
<?php endif; ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>
<!-- Side Navbar -->
<nav class="side-navbar shrink">
    <div class="side-navbar-wrapper">
      <!-- Sidebar Header    -->
      <!-- Sidebar Navigation Menus-->
      <div class="main-menu">
        <ul id="side-main-menu" class="side-menu list-unstyled">                  
          <li><a href="<?php echo e(url('/')); ?>"> <i class="dripicons-meter"></i><span><?php echo e(__('file.dashboard')); ?></span></a></li>
          <?php
            $role = DB::table('roles')->find(Auth::user()->role_id);
            $index_permission = DB::table('permissions')->where('name', 'products-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                ['permission_id', $index_permission->id],
                ['role_id', $role->id]
            ])->first();

            $print_barcode = DB::table('permissions')->where('name', 'print_barcode')->first();
                  $print_barcode_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $print_barcode->id],
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
          
          <li><a href="#product" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-list"></i><span><?php echo e(__('file.product')); ?></span><span></a>
            <ul id="product" class="collapse list-unstyled ">
              <li id="category-menu"><a href="<?php echo e(route('category.index')); ?>"><?php echo e(__('file.category')); ?></a></li>
              <?php if($index_permission_active): ?>
              <li id="product-list-menu"><a href="<?php echo e(route('products.index')); ?>"><?php echo e(__('file.product_list')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'products-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li id="product-create-menu"><a href="<?php echo e(route('products.create')); ?>"><?php echo e(__('file.add_product')); ?></a></li>
              <?php endif; ?>
              <?php endif; ?>
              <?php if($print_barcode_active): ?>
              <li id="printBarcode-menu"><a href="<?php echo e(route('product.printBarcode')); ?>"><?php echo e(__('file.print_barcode')); ?></a></li>
              <?php endif; ?>
              <?php if($adjustment_active): ?>
                <li id="adjustment-list-menu"><a href="<?php echo e(route('qty_adjustment.index')); ?>"><?php echo e(trans('file.Adjustment List')); ?></a></li>
                <li id="adjustment-create-menu"><a href="<?php echo e(route('qty_adjustment.create')); ?>"><?php echo e(trans('file.Add Adjustment')); ?></a></li>
              <?php endif; ?>
              <?php if($stock_count_active): ?>
                <li id="stock-count-menu"><a href="<?php echo e(route('stock-count.index')); ?>"><?php echo e(trans('file.Stock Count')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'purchases-index')->first();
              $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          <?php if($index_permission_active): ?>
          <li><a href="#purchase" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-card"></i><span><?php echo e(trans('file.Purchase')); ?></span></a>
            <ul id="purchase" class="collapse list-unstyled ">
              <li id="purchase-list-menu"><a href="<?php echo e(route('purchases.index')); ?>"><?php echo e(trans('file.Purchase List')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'purchases-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li id="purchase-create-menu"><a href="<?php echo e(route('purchases.create')); ?>"><?php echo e(trans('file.Add Purchase')); ?></a></li>
              <li id="purchase-import-menu"><a href="<?php echo e(url('purchases/purchase_by_csv')); ?>"><?php echo e(trans('file.Import Purchase By CSV')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'sales-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
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
          ?>
          
          <li><a href="#sale" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-cart"></i><span><?php echo e(trans('file.Sale')); ?></span></a>
            <ul id="sale" class="collapse list-unstyled ">
              <?php if($index_permission_active): ?>
              <li id="sale-list-menu"><a href="<?php echo e(route('sales.index')); ?>"><?php echo e(trans('file.Sale List')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'sales-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li><a href="<?php echo e(route('sale.pos')); ?>">POS</a></li>
              <li id="sale-create-menu"><a href="<?php echo e(route('sales.create')); ?>"><?php echo e(trans('file.Add Sale')); ?></a></li>
              <li id="sale-import-menu"><a href="<?php echo e(url('sales/sale_by_csv')); ?>"><?php echo e(trans('file.Import Sale By CSV')); ?></a></li>
              <?php endif; ?>
              <?php endif; ?>
              <?php if($gift_card_permission_active): ?>
              <li id="gift-card-menu"><a href="<?php echo e(route('gift_cards.index')); ?>"><?php echo e(trans('file.Gift Card List')); ?></a> </li>
              <?php endif; ?>
              <?php if($coupon_permission_active): ?>
              <li id="coupon-menu"><a href="<?php echo e(route('coupons.index')); ?>"><?php echo e(trans('file.Coupon List')); ?></a> </li>
              <?php endif; ?>
              <li id="delivery-menu"><a href="<?php echo e(route('delivery.index')); ?>"><?php echo e(trans('file.Delivery List')); ?></a></li>
            </ul>
          </li>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'expenses-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          <?php if($index_permission_active): ?>
          <li><a href="#expense" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-wallet"></i><span><?php echo e(trans('file.Expense')); ?></span></a>
            <ul id="expense" class="collapse list-unstyled ">
              <li id="exp-cat-menu"><a href="<?php echo e(route('expense_categories.index')); ?>"><?php echo e(trans('file.Expense Category')); ?></a></li>
              <li id="exp-list-menu"><a href="<?php echo e(route('expenses.index')); ?>"><?php echo e(trans('file.Expense List')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'expenses-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li><a id="add-expense" href=""> <?php echo e(trans('file.Add Expense')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'quotes-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          <?php if($index_permission_active): ?>
          <li><a href="#quotation" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-document"></i><span><?php echo e(trans('file.Quotation')); ?></span><span></a>
            <ul id="quotation" class="collapse list-unstyled ">
              <li id="quotation-list-menu"><a href="<?php echo e(route('quotations.index')); ?>"><?php echo e(trans('file.Quotation List')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'quotes-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li id="quotation-create-menu"><a href="<?php echo e(route('quotations.create')); ?>"><?php echo e(trans('file.Add Quotation')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'transfers-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          <?php if($index_permission_active): ?>
          <li><a href="#transfer" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-export"></i><span><?php echo e(trans('file.Transfer')); ?></span></a>
            <ul id="transfer" class="collapse list-unstyled ">
              <li id="transfer-list-menu"><a href="<?php echo e(route('transfers.index')); ?>"><?php echo e(trans('file.Transfer List')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'transfers-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li id="transfer-create-menu"><a href="<?php echo e(route('transfers.create')); ?>"><?php echo e(trans('file.Add Transfer')); ?></a></li>
              <li id="transfer-import-menu"><a href="<?php echo e(url('transfers/transfer_by_csv')); ?>"><?php echo e(trans('file.Import Transfer By CSV')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>
          
          <li><a href="#return" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-return"></i><span><?php echo e(trans('file.return')); ?></span></a>
            <ul id="return" class="collapse list-unstyled ">
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'returns-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              <?php if($index_permission_active): ?>
              <li id="sale-return-menu"><a href="<?php echo e(route('return-sale.index')); ?>"><?php echo e(trans('file.Sale')); ?></a></li>
              <?php endif; ?>
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'purchase-return-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              <?php if($index_permission_active): ?>
              <li id="purchase-return-menu"><a href="<?php echo e(route('return-purchase.index')); ?>"><?php echo e(trans('file.Purchase')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php 
            $index_permission = DB::table('permissions')->where('name', 'account-index')->first();
            $index_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_permission->id],
                    ['role_id', $role->id]
                ])->first();

            $money_transfer_permission = DB::table('permissions')->where('name', 'money-transfer')->first();
            $money_transfer_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $money_transfer_permission->id],
                    ['role_id', $role->id]
                ])->first();

            $balance_sheet_permission = DB::table('permissions')->where('name', 'balance-sheet')->first();
            $balance_sheet_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $balance_sheet_permission->id],
                    ['role_id', $role->id]
                ])->first();

            $account_statement_permission = DB::table('permissions')->where('name', 'account-statement')->first();
            $account_statement_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $account_statement_permission->id],
                    ['role_id', $role->id]
                ])->first();

          ?>
          <?php if($index_permission_active || $balance_sheet_permission_active || $account_statement_permission_active): ?>
          <li class=""><a href="#account" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-briefcase"></i><span><?php echo e(trans('file.Accounting')); ?></span></a>
            <ul id="account" class="collapse list-unstyled ">
              <?php if($index_permission_active): ?>
              <li id="account-list-menu"><a href="<?php echo e(route('accounts.index')); ?>"><?php echo e(trans('file.Account List')); ?></a></li>
              <li><a id="add-account" href=""><?php echo e(trans('file.Add Account')); ?></a></li>
              <?php endif; ?>
              <?php if($money_transfer_permission_active): ?>
              <li id="money-transfer-menu"><a href="<?php echo e(route('money-transfers.index')); ?>"><?php echo e(trans('file.Money Transfer')); ?></a></li>
              <?php endif; ?>
              <?php if($balance_sheet_permission_active): ?>
              <li id="balance-sheet-menu"><a href="<?php echo e(route('accounts.balancesheet')); ?>"><?php echo e(trans('file.Balance Sheet')); ?></a></li>
              <?php endif; ?>
              <?php if($account_statement_permission_active): ?>
              <li id="account-statement-menu"><a id="account-statement" href=""><?php echo e(trans('file.Account Statement')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>
          <?php 
            $department = DB::table('permissions')->where('name', 'department')->first();
            $department_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $department->id],
                    ['role_id', $role->id]
                ])->first();
            $index_employee = DB::table('permissions')->where('name', 'employees-index')->first();
            $index_employee_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $index_employee->id],
                    ['role_id', $role->id]
                ])->first();
            $attendance = DB::table('permissions')->where('name', 'attendance')->first();
            $attendance_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $attendance->id],
                    ['role_id', $role->id]
                ])->first();
            $payroll = DB::table('permissions')->where('name', 'payroll')->first();
            $payroll_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $payroll->id],
                    ['role_id', $role->id]
                ])->first();
          ?>
          
          <li class=""><a href="#hrm" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-user-group"></i><span>HRM</span></a>
            <ul id="hrm" class="collapse list-unstyled ">
              <?php if($department_active): ?>
              <li id="dept-menu"><a href="<?php echo e(route('departments.index')); ?>"><?php echo e(trans('file.Department')); ?></a></li>
              <?php endif; ?>
              <?php if($index_employee_active): ?>
              <li id="employee-menu"><a href="<?php echo e(route('employees.index')); ?>"><?php echo e(trans('file.Employee')); ?></a></li>
              <?php endif; ?>
              <?php if($attendance_active): ?>
              <li id="attendance-menu"><a href="<?php echo e(route('attendance.index')); ?>"><?php echo e(trans('file.Attendance')); ?></a></li>
              <?php endif; ?>
              <?php if($payroll_active): ?>
              <li id="payroll-menu"><a href="<?php echo e(route('payroll.index')); ?>"><?php echo e(trans('file.Payroll')); ?></a></li>
              <?php endif; ?>
              <li id="holiday-menu"><a href="<?php echo e(route('holidays.index')); ?>"><?php echo e(trans('file.Holiday')); ?></a></li>
            </ul>
          </li>
          
          <li><a href="#people" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-user"></i><span><?php echo e(trans('file.People')); ?></span></a>
            <ul id="people" class="collapse list-unstyled ">
              <?php $index_permission_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'users-index'],
                      ['role_id', $role->id] ])->first();
              ?>
              <?php if($index_permission_active): ?>
              <li id="user-list-menu"><a href="<?php echo e(route('user.index')); ?>"><?php echo e(trans('file.User List')); ?></a></li>
              <?php $add_permission_active = DB::table('permissions')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where([
                      ['permissions.name', 'users-add'],
                      ['role_id', $role->id] ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li id="user-create-menu"><a href="<?php echo e(route('user.create')); ?>"><?php echo e(trans('file.Add User')); ?></a></li>
              <?php endif; ?>
              <?php endif; ?>
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'customers-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              <?php if($index_permission_active): ?>
              <li id="customer-list-menu"><a href="<?php echo e(route('customer.index')); ?>"><?php echo e(trans('file.Customer List')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'customers-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li id="customer-create-menu"><a href="<?php echo e(route('customer.create')); ?>"><?php echo e(trans('file.Add Customer')); ?></a></li>
              <?php endif; ?>
              <?php endif; ?>
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'billers-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              <?php if($index_permission_active): ?>
              <li id="biller-list-menu"><a href="<?php echo e(route('biller.index')); ?>"><?php echo e(trans('file.Biller List')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'billers-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li id="biller-create-menu"><a href="<?php echo e(route('biller.create')); ?>"><?php echo e(trans('file.Add Biller')); ?></a></li>
              <?php endif; ?>
              <?php endif; ?>
              <?php 
                $index_permission = DB::table('permissions')->where('name', 'suppliers-index')->first();
                $index_permission_active = DB::table('role_has_permissions')->where([
                        ['permission_id', $index_permission->id],
                        ['role_id', $role->id]
                    ])->first();
              ?>
              <?php if($index_permission_active): ?>
              <li id="supplier-list-menu"><a href="<?php echo e(route('supplier.index')); ?>"><?php echo e(trans('file.Supplier List')); ?></a></li>
              <?php 
                $add_permission = DB::table('permissions')->where('name', 'suppliers-add')->first();
                $add_permission_active = DB::table('role_has_permissions')->where([
                    ['permission_id', $add_permission->id],
                    ['role_id', $role->id]
                ])->first();
              ?>
              <?php if($add_permission_active): ?>
              <li id="supplier-create-menu"><a href="<?php echo e(route('supplier.create')); ?>"><?php echo e(trans('file.Add Supplier')); ?></a></li>
              <?php endif; ?>
              <?php endif; ?>
            </ul>
          </li>
          <li><a href="#report" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-document-remove"></i><span><?php echo e(trans('file.Reports')); ?></span></a>
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
            ?>
            <ul id="report" class="collapse list-unstyled ">
              <?php if($profit_loss_active): ?>
              <li id="profit-loss-report-menu">
                <?php echo Form::open(['route' => 'report.profitLoss', 'method' => 'post', 'id' => 'profitLoss-report-form']); ?>

                <input type="hidden" name="start_date" value="<?php echo e(date('Y-m').'-'.'01'); ?>" />
                <input type="hidden" name="end_date" value="<?php echo e(date('Y-m-d')); ?>" />
                <a id="profitLoss-link" href=""><?php echo e(trans('file.Summary Report')); ?></a>
                <?php echo Form::close(); ?>

              </li>
              <?php endif; ?>
              <?php if($best_seller_active): ?>
              <li id="best-seller-report-menu">
                <a href="<?php echo e(url('report/best_seller')); ?>"><?php echo e(trans('file.Best Seller')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($product_report_active): ?>
              <li id="product-report-menu">
                <?php echo Form::open(['route' => 'report.product', 'method' => 'post', 'id' => 'product-report-form']); ?>

                <input type="hidden" name="start_date" value="<?php echo e(date('Y-m').'-'.'01'); ?>" />
                <input type="hidden" name="end_date" value="<?php echo e(date('Y-m-d')); ?>" />
                <input type="hidden" name="warehouse_id" value="0" />
                <a id="report-link" href=""><?php echo e(trans('file.Product Report')); ?></a>
                <?php echo Form::close(); ?>

              </li>
              <?php endif; ?>
              <?php if($daily_sale_active): ?>
              <li id="daily-sale-report-menu">
                <a href="<?php echo e(url('report/daily_sale/'.date('Y').'/'.date('m'))); ?>"><?php echo e(trans('file.Daily Sale')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($monthly_sale_active): ?>
              <li id="monthly-sale-report-menu">
                <a href="<?php echo e(url('report/monthly_sale/'.date('Y'))); ?>"><?php echo e(trans('file.Monthly Sale')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($daily_purchase_active): ?>
              <li id="daily-purchase-report-menu">
                <a href="<?php echo e(url('report/daily_purchase/'.date('Y').'/'.date('m'))); ?>"><?php echo e(trans('file.Daily Purchase')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($monthly_purchase_active): ?>
              <li id="monthly-purchase-report-menu">
                <a href="<?php echo e(url('report/monthly_purchase/'.date('Y'))); ?>"><?php echo e(trans('file.Monthly Purchase')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($sale_report_active): ?>
              <li id="sale-report-menu">
                <?php echo Form::open(['route' => 'report.sale', 'method' => 'post', 'id' => 'sale-report-form']); ?>

                <input type="hidden" name="start_date" value="<?php echo e(date('Y-m').'-'.'01'); ?>" />
                <input type="hidden" name="end_date" value="<?php echo e(date('Y-m-d')); ?>" />
                <input type="hidden" name="warehouse_id" value="0" />
                <a id="sale-report-link" href=""><?php echo e(trans('file.Sale Report')); ?></a>
                <?php echo Form::close(); ?>

              </li>
              <?php endif; ?>
              <?php if($payment_report_active): ?>
              <li id="payment-report-menu">
                <?php echo Form::open(['route' => 'report.paymentByDate', 'method' => 'post', 'id' => 'payment-report-form']); ?>

                <input type="hidden" name="start_date" value="<?php echo e(date('Y-m').'-'.'01'); ?>" />
                <input type="hidden" name="end_date" value="<?php echo e(date('Y-m-d')); ?>" />
                <a id="payment-report-link" href=""><?php echo e(trans('file.Payment Report')); ?></a>
                <?php echo Form::close(); ?>

              </li>
              <?php endif; ?>
              <?php if($purchase_report_active): ?>
              <li id="purchase-report-menu">
                <?php echo Form::open(['route' => 'report.purchase', 'method' => 'post', 'id' => 'purchase-report-form']); ?>

                <input type="hidden" name="start_date" value="<?php echo e(date('Y-m').'-'.'01'); ?>" />
                <input type="hidden" name="end_date" value="<?php echo e(date('Y-m-d')); ?>" />
                <input type="hidden" name="warehouse_id" value="0" />
                <a id="purchase-report-link" href=""><?php echo e(trans('file.Purchase Report')); ?></a>
                <?php echo Form::close(); ?>

              </li>
              <?php endif; ?>
              <?php if($warehouse_report_active): ?>
              <li id="warehouse-report-menu">
                <a id="warehouse-report-link" href=""><?php echo e(trans('file.Warehouse Report')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($warehouse_stock_report_active): ?>
              <li id="warehouse-stock-report-menu">
                <a href="<?php echo e(route('report.warehouseStock')); ?>"><?php echo e(trans('file.Warehouse Stock Chart')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($product_qty_alert_active): ?>
              <li id="qtyAlert-report-menu">
                <a href="<?php echo e(route('report.qtyAlert')); ?>"><?php echo e(trans('file.Product Quantity Alert')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($user_report_active): ?>
              <li id="user-report-menu">
                <a id="user-report-link" href=""><?php echo e(trans('file.User Report')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($customer_report_active): ?>
              <li id="customer-report-menu">
                <a id="customer-report-link" href=""><?php echo e(trans('file.Customer Report')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($supplier_report_active): ?>
              <li id="supplier-report-menu">
                <a id="supplier-report-link" href=""><?php echo e(trans('file.Supplier Report')); ?></a>
              </li>
              <?php endif; ?>
              <?php if($due_report_active): ?>
              <li id="due-report-menu">
                <?php echo Form::open(['route' => 'report.dueByDate', 'method' => 'post', 'id' => 'due-report-form']); ?>

                <input type="hidden" name="start_date" value="<?php echo e(date('Y-m').'-'.'01'); ?>" />
                <input type="hidden" name="end_date" value="<?php echo e(date('Y-m-d')); ?>" />
                <a id="due-report-link" href=""><?php echo e(trans('file.Due Report')); ?></a>
                <?php echo Form::close(); ?>

              </li>
              <?php endif; ?>
            </ul>
          </li>
          <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-gear"></i><span><?php echo e(trans('file.settings')); ?></span></a>
            <ul id="setting" class="collapse list-unstyled ">
              <?php

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

                  $mail_setting_permission = DB::table('permissions')->where('name', 'mail_setting')->first();
                  $mail_setting_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $mail_setting_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $sms_setting_permission = DB::table('permissions')->where('name', 'sms_setting')->first();
                  $sms_setting_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $sms_setting_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $create_sms_permission = DB::table('permissions')->where('name', 'create_sms')->first();
                  $create_sms_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $create_sms_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $pos_setting_permission = DB::table('permissions')->where('name', 'pos_setting')->first();
                  $pos_setting_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $pos_setting_permission->id],
                      ['role_id', $role->id]
                  ])->first();

                  $hrm_setting_permission = DB::table('permissions')->where('name', 'hrm_setting')->first();
                  $hrm_setting_permission_active = DB::table('role_has_permissions')->where([
                      ['permission_id', $hrm_setting_permission->id],
                      ['role_id', $role->id]
                  ])->first();
              ?>
              <?php if($role->id <= 2): ?>
              <li id="role-menu"><a href="<?php echo e(route('role.index')); ?>"><?php echo e(trans('file.Role Permission')); ?></a></li>
              <?php endif; ?>
              <?php if($warehouse_permission_active): ?>
              <li id="warehouse-menu"><a href="<?php echo e(route('warehouse.index')); ?>"><?php echo e(trans('file.Warehouse')); ?></a></li>
              <?php endif; ?>
              <?php if($customer_group_permission_active): ?>
              <li id="customer-group-menu"><a href="<?php echo e(route('customer_group.index')); ?>"><?php echo e(trans('file.Customer Group')); ?></a></li>
              <?php endif; ?>
              <?php if($brand_permission_active): ?>
              <li id="brand-menu"><a href="<?php echo e(route('brand.index')); ?>"><?php echo e(trans('file.Brand')); ?></a></li>
              <?php endif; ?>
              <?php if($unit_permission_active): ?>
              <li id="unit-menu"><a href="<?php echo e(route('unit.index')); ?>"><?php echo e(trans('file.Unit')); ?></a></li>
              <?php endif; ?>
              <?php if($tax_permission_active): ?>
              <li id="tax-menu"><a href="<?php echo e(route('tax.index')); ?>"><?php echo e(trans('file.Tax')); ?></a></li>
              <?php endif; ?>
              <li id="user-menu"><a href="<?php echo e(route('user.profile', ['id' => Auth::id()])); ?>"><?php echo e(trans('file.User Profile')); ?></a></li>
              <?php if($create_sms_permission_active): ?>
              <li id="create-sms-menu"><a href="<?php echo e(route('setting.createSms')); ?>"><?php echo e(trans('file.Create SMS')); ?></a></li>
              <?php endif; ?>
              <?php if($general_setting_permission_active): ?>
              <li id="general-setting-menu"><a href="<?php echo e(route('setting.general')); ?>"><?php echo e(trans('file.General Setting')); ?></a></li>
              <?php endif; ?>
              <?php if($mail_setting_permission_active): ?>
              <li id="mail-setting-menu"><a href="<?php echo e(route('setting.mail')); ?>"><?php echo e(trans('file.Mail Setting')); ?></a></li>
              <?php endif; ?>
              <?php if($sms_setting_permission_active): ?>
              <li id="sms-setting-menu"><a href="<?php echo e(route('setting.sms')); ?>"><?php echo e(trans('file.SMS Setting')); ?></a></li>
              <?php endif; ?>
              <?php if($pos_setting_permission_active): ?>
              <li id="pos-setting-menu"><a href="<?php echo e(route('setting.pos')); ?>">POS <?php echo e(trans('file.settings')); ?></a></li>
              <?php endif; ?>
              <?php if($hrm_setting_permission_active): ?>
              <li id="hrm-setting-menu"><a href="<?php echo e(route('setting.hrm')); ?>"> <?php echo e(trans('file.HRM Setting')); ?></a></li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
      </div>
    </div>
</nav>
<section class="forms pos-section">
    <div class="container-fluid">
        <div class="row">
            <audio id="mysoundclip1" preload="auto">
                <source src="<?php echo e(url('public/beep/beep-timber.mp3')); ?>"></source>
            </audio>
            <audio id="mysoundclip2" preload="auto">
                <source src="<?php echo e(url('public/beep/beep-07.mp3')); ?>"></source>
            </audio>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="padding-bottom: 0">
                        <?php echo Form::open(['route' => 'sales.store', 'method' => 'post', 'files' => true, 'class' => 'payment-form']); ?>

                        <?php
                            if($lims_pos_setting_data)
                                $keybord_active = $lims_pos_setting_data->keybord_active;
                            else
                                $keybord_active = 0;

                            $customer_active = DB::table('permissions')
                              ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                              ->where([
                                ['permissions.name', 'customers-add'],
                                ['role_id', \Auth::user()->role_id] ])->first();
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                      <div class="form-group">
                                          <input type="text" id="reference-no" name="reference_no" class="form-control" placeholder="Type reference number" onkeyup='saveValue(this);'/>
                                      </div>
                                      <?php if($errors->has('reference_no')): ?>
                                       <span>
                                           <strong><?php echo e($errors->first('reference_no')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?php if($lims_pos_setting_data): ?>
                                            <input type="hidden" name="warehouse_id_hidden" value="<?php echo e($lims_pos_setting_data->warehouse_id); ?>">
                                            <?php endif; ?>
                                            <select required id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse...">
                                                <?php $__currentLoopData = $lims_warehouse_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?php if($lims_pos_setting_data): ?>
                                            <input type="hidden" name="biller_id_hidden" value="<?php echo e($lims_pos_setting_data->biller_id); ?>">
                                            <?php endif; ?>
                                            <select required id="biller_id" name="biller_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Biller...">
                                            <?php $__currentLoopData = $lims_biller_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($biller->id); ?>"><?php echo e($biller->name . ' (' . $biller->company_name . ')'); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?php if($lims_pos_setting_data): ?>
                                            <input type="hidden" name="customer_id_hidden" value="<?php echo e($lims_pos_setting_data->customer_id); ?>">
                                            <?php endif; ?>
                                            <div class="input-group pos">
                                                <?php if($customer_active): ?>
                                                <select required name="customer_id" id="customer_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select customer..." style="width: 100px">
                                                <?php $deposit = [] ?>
                                                <?php $__currentLoopData = $lims_customer_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $deposit[$customer->id] = $customer->deposit - $customer->expense; ?>
                                                    <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name . ' (' . $customer->phone_number . ')'); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#addCustomer"><i class="dripicons-plus"></i></button>
                                                <?php else: ?>
                                                <?php $deposit = [] ?>
                                                <select required name="customer_id" id="customer_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select customer...">
                                                <?php $__currentLoopData = $lims_customer_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $deposit[$customer->id] = $customer->deposit - $customer->expense; ?>
                                                    <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name . ' (' . $customer->phone_number . ')'); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="search-box form-group">
                                            <input type="text" name="product_code_name" id="lims_productcodeSearch" placeholder="Scan/Search product by name/code" class="form-control"  />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="table-responsive transaction-list">
                                        <table id="myTable" class="table table-hover table-striped order-list table-fixed">
                                            <thead>
                                                <tr>
                                                    <th class="col-sm-4"><?php echo e(trans('file.product')); ?></th>
                                                    <th class="col-sm-2"><?php echo e(trans('file.Price')); ?></th>
                                                    <th class="col-sm-3"><?php echo e(trans('file.Quantity')); ?></th>
                                                    <th class="col-sm-3"><?php echo e(trans('file.Subtotal')); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-id">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row" style="display: none;">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_qty" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_discount" value="0.00" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_tax" value="0.00"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_price" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="item" />
                                            <input type="hidden" name="order_tax" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="grand_total" />
                                            <input type="hidden" name="coupon_discount" />
                                            <input type="hidden" name="sale_status" value="1" />
                                            <input type="hidden" name="coupon_active">
                                            <input type="hidden" name="coupon_id">
                                            <input type="hidden" name="coupon_discount" />

                                            <input type="hidden" name="pos" value="1" />
                                            <input type="hidden" name="draft" value="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 totals" style="border-top: 2px solid #e4e6fc; padding-top: 10px;">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="totals-title"><?php echo e(trans('file.Items')); ?></span><span id="item">0</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title"><?php echo e(trans('file.Total')); ?></span><span id="subtotal">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title"><?php echo e(trans('file.Discount')); ?> <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#order-discount-modal"> <i class="dripicons-document-edit"></i></button></span><span id="discount">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title"><?php echo e(trans('file.Coupon')); ?> <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#coupon-modal"><i class="dripicons-document-edit"></i></button></span><span id="coupon-text">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title"><?php echo e(trans('file.Tax')); ?> <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#order-tax"><i class="dripicons-document-edit"></i></button></span><span id="tax">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title"><?php echo e(trans('file.Shipping')); ?> <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#shipping-cost-modal"><i class="dripicons-document-edit"></i></button></span><span id="shipping-cost">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="payment-amount">
                        <h2><?php echo e(trans('file.grand total')); ?> <span id="grand-total">0.00</span></h2>
                    </div>
                    <div class="payment-options">
                        <div class="column-5">
                            <button style="background: #0984e3" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="credit-card-btn"><i class="fa fa-credit-card"></i> Card</button>   
                        </div>
                        <div class="column-5">
                            <button style="background: #00cec9" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="cash-btn"><i class="fa fa-money"></i> Cash</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #213170" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="paypal-btn"><i class="fa fa-paypal"></i> Paypal</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #e28d02" type="button" class="btn btn-custom" id="draft-btn"><i class="dripicons-flag"></i> Draft</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #fd7272" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="cheque-btn"><i class="fa fa-money"></i> Cheque</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #5f27cd" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="gift-card-btn"><i class="fa fa-credit-card-alt"></i> GiftCard</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #b33771" type="button" class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment" id="deposit-btn"><i class="fa fa-university"></i> Deposit</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #d63031;" type="button" class="btn btn-custom" id="cancel-btn" onclick="return confirmCancel()"><i class="fa fa-close"></i> Cancel</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #ffc107;" type="button" class="btn btn-custom" data-toggle="modal" data-target="#recentTransaction"><i class="dripicons-clock"></i> Recent transaction</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- payment modal -->
            <div id="add-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title"><?php echo e(trans('file.Finalize Sale')); ?></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6 mt-1">
                                            <label><?php echo e(trans('file.Recieved Amount')); ?> *</label>
                                            <input type="text" name="paying_amount" class="form-control numkey" required step="any">
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <label><?php echo e(trans('file.Paying Amount')); ?> *</label>
                                            <input type="text" name="paid_amount" class="form-control numkey"  step="any">
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <label><?php echo e(trans('file.Change')); ?> : </label>
                                            <p id="change" class="ml-2">0.00</p>
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <input type="hidden" name="paid_by_id">
                                            <label><?php echo e(trans('file.Paid By')); ?></label>
                                            <select name="paid_by_id_select" class="form-control selectpicker">
                                                <option value="1">Cash</option>
                                                <option value="2">Gift Card</option>
                                                <option value="3">Credit Card</option>
                                                <option value="4">Cheque</option>
                                                <option value="5">Paypal</option>
                                                <option value="6">Deposit</option>
                                            </select>
                                        </div>

                                  


                                        <div class="form-group col-md-12 mt-3">
                                            <div class="card-element form-control">
                                            </div>
                                            <div class="card-errors" role="alert"></div>
                                        </div>
                                        <div class="form-group col-md-12 gift-card">
                                            <label> <?php echo e(trans('file.Gift Card')); ?> *</label>
                                            <input type="hidden" name="gift_card_id">
                                            <select id="gift_card_id_select" name="gift_card_id_select" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Gift Card..."></select>
                                        </div>
                                        <div class="form-group col-md-12 cheque">
                                            <label><?php echo e(trans('file.Cheque Number')); ?> *</label>
                                            <input type="text" name="cheque_no" class="form-control">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label><?php echo e(trans('file.Payment Note')); ?></label>
                                            <textarea id="payment_note" rows="2" class="form-control" name="payment_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-6 form-group">
                                            <label><?php echo e(trans('file.Sale Note')); ?></label>
                                            <textarea rows="3" class="form-control" name="sale_note"></textarea>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label><?php echo e(trans('file.Staff Note')); ?></label>
                                            <textarea rows="3" class="form-control" name="staff_note"></textarea>
                                        </div>
                                    </div>

                                           <div class="col-md-6 mt-1">
                                            <input type="hidden" name="document_id">
                                            <label>Documento a emitir</label>
                                            <select name="document_id_select" class="form-control selectpicker">
                                                <option value="1">Factura</option>
                                                <option value="2">CCF</option>
                                               
                                            </select>
                                        </div>

                                        
                                    <div class="mt-3">
                                        <button id="submit-btn" type="button" class="btn btn-primary"><?php echo e(trans('file.submit')); ?></button>
                                    </div>
                                </div>
                                <div class="col-md-2 qc" data-initial="1">
                                    <h4><strong><?php echo e(trans('file.Quick Cash')); ?></strong></h4>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="10" type="button">10</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="20" type="button">20</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="50" type="button">50</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="100" type="button">100</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="500" type="button">500</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="1000" type="button">1000</button>
                                    <button class="btn btn-block btn-danger qc-btn sound-btn" data-amount="0" type="button"><?php echo e(trans('file.Clear')); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- order_discount modal -->
            <div id="order-discount-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo e(trans('file.Order Discount')); ?></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="order_discount" class="form-control numkey" id="order-discount" onkeyup='saveValue(this);'>
                            </div>
                            <button type="button" name="order_discount_btn" class="btn btn-primary" data-dismiss="modal"><?php echo e(trans('file.submit')); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- coupon modal -->
            <div id="coupon-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo e(trans('file.Coupon Code')); ?></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" id="coupon-code" class="form-control" placeholder="Type Coupon Code...">
                            </div>
                            <button type="button" class="btn btn-primary coupon-check" data-dismiss="modal"><?php echo e(trans('file.submit')); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- order_tax modal -->
            <div id="order-tax" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo e(trans('file.Order Tax')); ?></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="hidden" name="order_tax_rate">
                                <select class="form-control" name="order_tax_rate_select" id="order-tax-rate-select">
                                    <option value="0">No Tax</option>
                                    <?php $__currentLoopData = $lims_tax_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tax->rate); ?>"><?php echo e($tax->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <button type="button" name="order_tax_btn" class="btn btn-primary" data-dismiss="modal"><?php echo e(trans('file.submit')); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- shipping_cost modal -->
            <div id="shipping-cost-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo e(trans('file.Shipping Cost')); ?></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="shipping_cost" class="form-control numkey" id="shipping-cost-val" step="any" onkeyup='saveValue(this);'>
                            </div>
                            <button type="button" name="shipping_cost_btn" class="btn btn-primary" data-dismiss="modal"><?php echo e(trans('file.submit')); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php echo Form::close(); ?>

            <!-- product list -->
            <div class="col-md-6">
                <!-- navbar-->
                <header class="header">
                    <nav class="navbar">
                      <div class="container-fluid">
                        <div class="navbar-holder d-flex align-items-center justify-content-between">
                          <a id="toggle-btn" href="#" class="menu-btn"><i class="fa fa-bars"> </i></a>
                          <div class="navbar-header">
                          
                          <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                            <li class="nav-item"><a id="btnFullscreen" title="Full Screen"><i class="dripicons-expand"></i></a></li> 
                            <?php 
                                $general_setting_permission = DB::table('permissions')->where('name', 'general_setting')->first();
                                $general_setting_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $general_setting_permission->id],
                                            ['role_id', Auth::user()->role_id]
                                        ])->first();

                                $pos_setting_permission = DB::table('permissions')->where('name', 'pos_setting')->first();

                                $pos_setting_permission_active = DB::table('role_has_permissions')->where([
                                    ['permission_id', $pos_setting_permission->id],
                                    ['role_id', Auth::user()->role_id]
                                ])->first();
                            ?>
                            <?php if($pos_setting_permission_active): ?>
                            <li class="nav-item"><a class="dropdown-item" href="<?php echo e(route('setting.pos')); ?>" title="<?php echo e(trans('file.POS Setting')); ?>"><i class="dripicons-gear"></i></a> </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route('sales.printLastReciept')); ?>" title="<?php echo e(trans('file.Print Last Reciept')); ?>"><i class="dripicons-print"></i></a>
                            </li>
                            <li class="nav-item">
                                <a href="" id="register-details-btn" title="<?php echo e(trans('file.Cash Register Details')); ?>"><i class="dripicons-briefcase"></i></a>
                            </li>
                            <?php
                                $today_sale_permission = DB::table('permissions')->where('name', 'today_sale')->first();
                                $today_sale_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $today_sale_permission->id],
                                            ['role_id', Auth::user()->role_id]
                                        ])->first();

                                $today_profit_permission = DB::table('permissions')->where('name', 'today_profit')->first();
                                $today_profit_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $today_profit_permission->id],
                                            ['role_id', Auth::user()->role_id]
                                        ])->first();
                            ?>

                            <?php if($today_sale_permission_active): ?>
                            <li class="nav-item">
                                <a href="" id="today-sale-btn" title="<?php echo e(trans('file.Today Sale')); ?>"><i class="dripicons-shopping-bag"></i></a>
                            </li>
                            <?php endif; ?>
                            <?php if($today_profit_permission_active): ?>
                            <li class="nav-item">
                                <a href="" id="today-profit-btn" title="<?php echo e(trans('file.Today Profit')); ?>"><i class="dripicons-graph-line"></i></a>
                            </li>
                            <?php endif; ?>
                            <?php if(($alert_product + count(\Auth::user()->unreadNotifications)) > 0): ?>
                            <li class="nav-item" id="notification-icon">
                                  <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-bell"></i><span class="badge badge-danger notification-number"><?php echo e($alert_product + count(\Auth::user()->unreadNotifications)); ?></span>
                                      <span class="caret"></span>
                                      <span class="sr-only">Toggle Dropdown</span>
                                  </a>
                                  <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default notifications" user="menu">
                                      <li class="notifications">
                                        <a href="<?php echo e(route('report.qtyAlert')); ?>" class="btn btn-link"><?php echo e($alert_product); ?> product exceeds alert quantity</a>
                                      </li>
                                      <?php $__currentLoopData = \Auth::user()->unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                          <li class="notifications">
                                              <a href="#" class="btn btn-link"><?php echo e($notification->data['message']); ?></a>
                                          </li>
                                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                  </ul>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item"> 
                                <a class="dropdown-item" href="<?php echo e(url('read_me')); ?>" target="_blank"><i class="dripicons-information"></i> <?php echo e(trans('file.Help')); ?></a>
                            </li>&nbsp;
                            <li class="nav-item">
                                  <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-user"></i> <span><?php echo e(ucfirst(Auth::user()->name)); ?></span> <i class="fa fa-angle-down"></i>
                                  </a>
                                  <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                      <li> 
                                        <a href="<?php echo e(route('user.profile', ['id' => Auth::id()])); ?>"><i class="dripicons-user"></i> <?php echo e(trans('file.profile')); ?></a>
                                      </li>
                                      <?php if($general_setting_permission_active): ?>
                                      <li> 
                                        <a href="<?php echo e(route('setting.general')); ?>"><i class="dripicons-gear"></i> <?php echo e(trans('file.settings')); ?></a>
                                      </li>
                                      <?php endif; ?>
                                      <li> 
                                        <a href="<?php echo e(url('my-transactions/'.date('Y').'/'.date('m'))); ?>"><i class="dripicons-swap"></i> <?php echo e(trans('file.My Transaction')); ?></a>
                                      </li>
                                      <li> 
                                        <a href="<?php echo e(url('holidays/my-holiday/'.date('Y').'/'.date('m'))); ?>"><i class="dripicons-vibrate"></i> <?php echo e(trans('file.My Holiday')); ?></a>
                                      </li>
                                      <li>
                                        <a href="<?php echo e(route('logout')); ?>"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();"><i class="dripicons-power"></i>
                                            <?php echo e(trans('file.logout')); ?>

                                        </a>
                                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                            <?php echo csrf_field(); ?>
                                        </form>
                                      </li>
                                  </ul>
                            </li> 
                          </ul>
                        </div>
                      </div>
                    </nav>
                </header>
                <div class="filter-window">
                    <div class="category mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">Choose category</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            <?php $__currentLoopData = $lims_category_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-3 category-img text-center" data-category="<?php echo e($category->id); ?>">
                                <?php if($category->image): ?>
                                    <img  src="<?php echo e(url('public/images/category', $category->image)); ?>" />
                                <?php else: ?>
                                    <img  src="<?php echo e(url('public/images/product/zummXD2dvAtI.png')); ?>" />
                                <?php endif; ?>
                                <p class="text-center"><?php echo e($category->name); ?></p>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="brand mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">Choose brand</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            <?php $__currentLoopData = $lims_brand_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($brand->image): ?>
                                <div class="col-md-3 brand-img text-center" data-brand="<?php echo e($brand->id); ?>">
                                    <img  src="<?php echo e(url('public/images/brand',$brand->image)); ?>" />
                                    <p class="text-center"><?php echo e($brand->title); ?></p>
                                </div>
                            <?php else: ?>
                                <div class="col-md-3 brand-img" data-brand="<?php echo e($brand->id); ?>">
                                    <img  src="<?php echo e(url('public/images/product/zummXD2dvAtI.png')); ?>" />
                                    <p class="text-center"><?php echo e($brand->title); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
    			<div class="row">
                    <div class="col-md-4">
                        <button class="btn btn-block btn-primary" id="category-filter"><?php echo e(trans('file.category')); ?></button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-block btn-info" id="brand-filter"><?php echo e(trans('file.Brand')); ?></button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-block btn-danger" id="featured-filter"><?php echo e(trans('file.Featured')); ?></button>
                    </div>
                    <div class="col-md-12 mt-1 table-container">
                        <table id="product-table" class="table no-shadow product-list">
                            <thead class="d-none">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php for($i=0; $i < ceil($product_number/5); $i++): ?>
                                <tr>
                                    <td class="product-img sound-btn" title="<?php echo e($lims_product_list[0+$i*5]->name); ?>" data-product ="<?php echo e($lims_product_list[0+$i*5]->code . ' (' . $lims_product_list[0+$i*5]->name . ')'); ?>"><img  src="<?php echo e(url('public/images/product',$lims_product_list[0+$i*5]->base_image)); ?>" width="100%" />
                                        <p><?php echo e($lims_product_list[0+$i*5]->name); ?></p>
                                        <span><?php echo e($lims_product_list[0+$i*5]->code); ?></span>
                                    </td>
                                    <?php if(!empty($lims_product_list[1+$i*5])): ?>
                                    <td class="product-img sound-btn" title="<?php echo e($lims_product_list[1+$i*5]->name); ?>" data-product ="<?php echo e($lims_product_list[1+$i*5]->code . ' (' . $lims_product_list[1+$i*5]->name . ')'); ?>"><img  src="<?php echo e(url('public/images/product',$lims_product_list[1+$i*5]->base_image)); ?>" width="100%" />
                                        <p><?php echo e($lims_product_list[1+$i*5]->name); ?></p>
                                        <span><?php echo e($lims_product_list[1+$i*5]->code); ?></span>
                                    </td>
                                    <?php else: ?>
                                    <td style="border:none;"></td>
                                    <?php endif; ?>
                                    <?php if(!empty($lims_product_list[2+$i*5])): ?>
                                    <td class="product-img sound-btn" title="<?php echo e($lims_product_list[2+$i*5]->name); ?>" data-product ="<?php echo e($lims_product_list[2+$i*5]->code . ' (' . $lims_product_list[2+$i*5]->name . ')'); ?>"><img  src="<?php echo e(url('public/images/product',$lims_product_list[2+$i*5]->base_image)); ?>" width="100%" />
                                        <p><?php echo e($lims_product_list[2+$i*5]->name); ?></p>
                                        <span><?php echo e($lims_product_list[2+$i*5]->code); ?></span>
                                    </td>
                                    <?php else: ?>
                                    <td style="border:none;"></td>
                                    <?php endif; ?>
                                    <?php if(!empty($lims_product_list[3+$i*5])): ?>
                                    <td class="product-img sound-btn" title="<?php echo e($lims_product_list[3+$i*5]->name); ?>" data-product ="<?php echo e($lims_product_list[3+$i*5]->code . ' (' . $lims_product_list[3+$i*5]->name . ')'); ?>"><img  src="<?php echo e(url('public/images/product',$lims_product_list[3+$i*5]->base_image)); ?>" width="100%" />
                                        <p><?php echo e($lims_product_list[3+$i*5]->name); ?></p>
                                        <span><?php echo e($lims_product_list[3+$i*5]->code); ?></span>
                                    </td>
                                    <?php else: ?>
                                    <td style="border:none;"></td>
                                    <?php endif; ?>
                                    <?php if(!empty($lims_product_list[4+$i*5])): ?>
                                    <td class="product-img sound-btn" title="<?php echo e($lims_product_list[4+$i*5]->name); ?>" data-product ="<?php echo e($lims_product_list[4+$i*5]->code . ' (' . $lims_product_list[4+$i*5]->name . ')'); ?>"><img  src="<?php echo e(url('public/images/product',$lims_product_list[4+$i*5]->base_image)); ?>" width="100%" />
                                        <p><?php echo e($lims_product_list[4+$i*5]->name); ?></p>
                                        <span><?php echo e($lims_product_list[4+$i*5]->code); ?></span>
                                    </td>
                                    <?php else: ?>
                                    <td style="border:none;"></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
            	</div>
            </div>
            <!-- product edit modal -->
            <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="modal_header" class="modal-title"></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Quantity')); ?></label>
                                    <input type="text" name="edit_qty" class="form-control numkey">
                                </div>
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Unit Discount')); ?></label>
                                    <input type="text" name="edit_discount" class="form-control numkey">
                                </div>
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Unit Price')); ?></label>
                                    <input type="text" name="edit_unit_price" class="form-control numkey" step="any">
                                </div>
                                <?php
                        $tax_name_all[] = 'No Tax';
                        $tax_rate_all[] = 0;
                        foreach($lims_tax_list as $tax) {
                            $tax_name_all[] = $tax->name;
                            $tax_rate_all[] = $tax->rate;
                        }
                    ?>
                                    <div class="form-group">
                                        <label><?php echo e(trans('file.Tax Rate')); ?></label>
                                        <select name="edit_tax_rate" class="form-control selectpicker">
                                            <?php $__currentLoopData = $tax_name_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div id="edit_unit" class="form-group">
                                        <label><?php echo e(trans('file.Product Unit')); ?></label>
                                        <select name="edit_unit" class="form-control selectpicker">
                                        </select>
                                    </div>
                                    <button type="button" name="update_btn" class="btn btn-primary"><?php echo e(trans('file.update')); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- add customer modal -->
            <div id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <?php echo Form::open(['route' => 'customer.store', 'method' => 'post', 'files' => true]); ?>

                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title"><?php echo e(trans('file.Add Customer')); ?></h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                      <p class="italic"><small><?php echo e(trans('file.The field labels marked with * are required input fields')); ?>.</small></p>
                        <div class="form-group">
                            <label><?php echo e(trans('file.Customer Group')); ?> *</strong> </label>
                            <select required class="form-control selectpicker" name="customer_group_id">
                                <?php $__currentLoopData = $lims_customer_group_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer_group->id); ?>"><?php echo e($customer_group->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo e(trans('file.name')); ?> *</strong> </label>
                            <input type="text" name="customer_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label><?php echo e(trans('file.Email')); ?></label>
                            <input type="text" name="email" placeholder="example@example.com" class="form-control">
                        </div>
                        <div class="form-group">
                            <label><?php echo e(trans('file.Phone Number')); ?> *</label>
                            <input type="text" name="phone_number" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label><?php echo e(trans('file.Address')); ?> *</label>
                            <input type="text" name="address" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label><?php echo e(trans('file.City')); ?> *</label>
                            <input type="text" name="city" required class="form-control">
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="pos" value="1">      
                          <input type="submit" value="<?php echo e(trans('file.submit')); ?>" class="btn btn-primary">
                        </div>
                    </div>
                    <?php echo e(Form::close()); ?>

                  </div>
                </div>
            </div>
            <!-- recent transaction modal -->
            <div id="recentTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title"><?php echo e(trans('file.Recent Transaction')); ?> <div class="badge badge-primary"><?php echo e(trans('file.latest')); ?> 10</div></h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" href="#sale-latest" role="tab" data-toggle="tab"><?php echo e(trans('file.Sale')); ?></a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="#draft-latest" role="tab" data-toggle="tab"><?php echo e(trans('file.Draft')); ?></a>
                          </li>
                        </ul>
                        <div class="tab-content">
                          <div role="tabpanel" class="tab-pane show active" id="sale-latest">
                              <div class="table-responsive">
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th><?php echo e(trans('file.date')); ?></th>
                                      <th><?php echo e(trans('file.reference')); ?></th>
                                      <th><?php echo e(trans('file.customer')); ?></th>
                                      <th><?php echo e(trans('file.grand total')); ?></th>
                                      <th><?php echo e(trans('file.action')); ?></th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $__currentLoopData = $recent_sale; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $customer = DB::table('customers')->find($sale->customer_id); ?>
                                    <tr>
                                      <td><?php echo e(date('d-m-Y', strtotime($sale->created_at))); ?></td>
                                      <td><?php echo e($sale->reference_no); ?></td>
                                      <td><?php echo e($customer->name); ?></td>
                                      <td><?php echo e($sale->grand_total); ?></td>
                                      <td>
                                        <div class="btn-group">
                                            <?php if(in_array("sales-edit", $all_permission)): ?>
                                            <a href="<?php echo e(route('sales.edit', $sale->id)); ?>" class="btn btn-success btn-sm" title="Edit"><i class="dripicons-document-edit"></i></a>&nbsp;
                                            <?php endif; ?>
                                            <?php if(in_array("sales-delete", $all_permission)): ?>
                                            <?php echo e(Form::open(['route' => ['sales.destroy', $sale->id], 'method' => 'DELETE'] )); ?>

                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirmDelete()" title="Delete"><i class="dripicons-trash"></i></button>
                                            <?php echo e(Form::close()); ?>

                                            <?php endif; ?>
                                        </div>
                                      </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                  </tbody>
                                </table>
                              </div>
                          </div>
                          <div role="tabpanel" class="tab-pane fade" id="draft-latest">
                              <div class="table-responsive">
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th><?php echo e(trans('file.date')); ?></th>
                                      <th><?php echo e(trans('file.reference')); ?></th>
                                      <th><?php echo e(trans('file.customer')); ?></th>
                                      <th><?php echo e(trans('file.grand total')); ?></th>
                                      <th><?php echo e(trans('file.action')); ?></th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $__currentLoopData = $recent_draft; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $draft): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $customer = DB::table('customers')->find($draft->customer_id); ?>
                                    <tr>
                                      <td><?php echo e(date('d-m-Y', strtotime($draft->created_at))); ?></td>
                                      <td><?php echo e($draft->reference_no); ?></td>
                                      <td><?php echo e($customer->name); ?></td>
                                      <td><?php echo e($draft->grand_total); ?></td>
                                      <td>
                                        <div class="btn-group">
                                            <?php if(in_array("sales-edit", $all_permission)): ?>
                                            <a href="<?php echo e(url('sales/'.$draft->id.'/create')); ?>" class="btn btn-success btn-sm" title="Edit"><i class="dripicons-document-edit"></i></a>&nbsp;
                                            <?php endif; ?>
                                            <?php if(in_array("sales-delete", $all_permission)): ?>
                                            <?php echo e(Form::open(['route' => ['sales.destroy', $draft->id], 'method' => 'DELETE'] )); ?>

                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirmDelete()" title="Delete"><i class="dripicons-trash"></i></button>
                                            <?php echo e(Form::close()); ?>

                                            <?php endif; ?>
                                        </div>
                                      </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                  </tbody>
                                </table>
                              </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- add cash register modal -->
            <div id="cash-register-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <?php echo Form::open(['route' => 'cashRegister.store', 'method' => 'post']); ?>

                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title"><?php echo e(trans('file.Add Cash Register')); ?></h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                      <p class="italic"><small><?php echo e(trans('file.The field labels marked with * are required input fields')); ?>.</small></p>
                        <div class="row">
                          <div class="col-md-6 form-group warehouse-section">
                              <label><?php echo e(trans('file.Warehouse')); ?> *</strong> </label>
                              <select required name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse...">
                                  <?php $__currentLoopData = $lims_warehouse_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                  <option value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </select>
                          </div>
                          <div class="col-md-6 form-group">
                              <label><?php echo e(trans('file.Cash in Hand')); ?> *</strong> </label>
                              <input type="number" name="cash_in_hand" required class="form-control">
                          </div>
                          <div class="col-md-12 form-group">
                              <button type="submit" class="btn btn-primary"><?php echo e(trans('file.submit')); ?></button>
                          </div>
                        </div>
                    </div>
                    <?php echo e(Form::close()); ?>

                  </div>
                </div>
            </div>
            <!-- cash register details modal -->
            <div id="register-details-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title"><?php echo e(trans('file.Cash Register Details')); ?></h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                      <p><?php echo e(trans('file.Please review the transaction and payments.')); ?></p>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                          <td><?php echo e(trans('file.Cash in Hand')); ?>:</td>
                                          <td id="cash_in_hand" class="text-right">0</td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Total Sale Amount')); ?>:</td>
                                          <td id="total_sale_amount" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Total Payment')); ?>:</td>
                                          <td id="total_payment" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Cash Payment')); ?>:</td>
                                          <td id="cash_payment" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Credit Card Payment')); ?>:</td>
                                          <td id="credit_card_payment" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Cheque Payment')); ?>:</td>
                                          <td id="cheque_payment" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Gift Card Payment')); ?>:</td>
                                          <td id="gift_card_payment" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Paypal Payment')); ?>:</td>
                                          <td id="paypal_payment" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Total Sale Return')); ?>:</td>
                                          <td id="total_sale_return" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Total Expense')); ?>:</td>
                                          <td id="total_expense" class="text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><strong><?php echo e(trans('file.Total Cash')); ?>:</strong></td>
                                          <td id="total_cash" class="text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6" id="closing-section">
                              <form action="<?php echo e(route('cashRegister.close')); ?>" method="POST">
                                  <?php echo csrf_field(); ?>
                                  <input type="hidden" name="cash_register_id">
                                  <button type="submit" class="btn btn-primary"><?php echo e(trans('file.Close Register')); ?></button>
                              </form>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- today sale modal -->
            <div id="today-sale-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title"><?php echo e(trans('file.Today Sale')); ?></h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                      <p><?php echo e(trans('file.Please review the transaction and payments.')); ?></p>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                          <td><?php echo e(trans('file.Total Sale Amount')); ?>:</td>
                                          <td class="total_sale_amount text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Cash Payment')); ?>:</td>
                                          <td class="cash_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Credit Card Payment')); ?>:</td>
                                          <td class="credit_card_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Cheque Payment')); ?>:</td>
                                          <td class="cheque_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Gift Card Payment')); ?>:</td>
                                          <td class="gift_card_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Paypal Payment')); ?>:</td>
                                          <td class="paypal_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Total Payment')); ?>:</td>
                                          <td class="total_payment text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Total Sale Return')); ?>:</td>
                                          <td class="total_sale_return text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Total Expense')); ?>:</td>
                                          <td class="total_expense text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><strong><?php echo e(trans('file.Total Cash')); ?>:</strong></td>
                                          <td class="total_cash text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- today profit modal -->
            <div id="today-profit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 id="exampleModalLabel" class="modal-title"><?php echo e(trans('file.Today Profit')); ?></h5>
                      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <select required name="warehouseId" class="form-control">
                                    <option value="0"><?php echo e(trans('file.All Warehouse')); ?></option>
                                    <?php $__currentLoopData = $lims_warehouse_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                          <td><?php echo e(trans('file.Product Revenue')); ?>:</td>
                                          <td class="product_revenue text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Product Cost')); ?>:</td>
                                          <td class="product_cost text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><?php echo e(trans('file.Expense')); ?>:</td>
                                          <td class="expense_amount text-right"></td>
                                        </tr>
                                        <tr>
                                          <td><strong><?php echo e(trans('file.Profit')); ?>:</strong></td>
                                          <td class="profit text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $("ul#sale").siblings('a').attr('aria-expanded','true');
    $("ul#sale").addClass("show");
    $("ul#sale #sale-pos-menu").addClass("active");

    var public_key = <?php echo json_encode($lims_pos_setting_data->stripe_public_key) ?>;
    var alert_product = <?php echo json_encode($alert_product) ?>;
    var currency = <?php echo json_encode($currency) ?>;
    var valid;

// array data depend on warehouse
var lims_product_array = [];
var product_code = [];
var product_name = [];
var product_qty = [];
var product_type = [];
var product_id = [];
var product_list = [];
var qty_list = [];

// array data with selection
var product_price = [];
var product_discount = [];
var tax_rate = [];
var tax_name = [];
var tax_method = [];
var unit_name = [];
var unit_operator = [];
var unit_operation_value = [];
var gift_card_amount = [];
var gift_card_expense = [];

// temporary array
var temp_unit_name = [];
var temp_unit_operator = [];
var temp_unit_operation_value = [];

var deposit = <?php echo json_encode($deposit) ?>;
var product_row_number = <?php echo json_encode($lims_pos_setting_data->product_number) ?>;
var rowindex;
var customer_group_rate;
var row_product_price;
var pos;
var keyboard_active = <?php echo json_encode($keybord_active); ?>;
var role_id = <?php echo json_encode(\Auth::user()->role_id) ?>;
var warehouse_id = <?php echo json_encode(\Auth::user()->warehouse_id) ?>;
var biller_id = <?php echo json_encode(\Auth::user()->biller_id) ?>;
var coupon_list = <?php echo json_encode($lims_coupon_list) ?>;
var currency = <?php echo json_encode($currency) ?>;

var localStorageQty = [];
var localStorageProductId = [];
var localStorageProductDiscount = [];
var localStorageTaxRate = [];
var localStorageNetUnitPrice = [];
var localStorageTaxValue = [];
var localStorageTaxName = [];
var localStorageTaxMethod = [];
var localStorageSubTotalUnit = [];
var localStorageSubTotal = [];
var localStorageProductCode = [];
var localStorageSaleUnit = [];
var localStorageTempUnitName = [];
var localStorageSaleUnitOperator = [];
var localStorageSaleUnitOperationValue = [];

$("#reference-no").val(getSavedValue("reference-no"));
$("#order-discount").val(getSavedValue("order-discount"));
$("#order-tax-rate-select").val(getSavedValue("order-tax-rate-select"));

    
$("#shipping-cost-val").val(getSavedValue("shipping-cost-val"));

if(localStorage.getItem("tbody-id")) {
  $("#tbody-id").html(localStorage.getItem("tbody-id"));
}

function saveValue(e) {
    var id = e.id;  // get the sender's id to save it.
    var val = e.value; // get the value.
    localStorage.setItem(id, val);// Every time user writing something, the localStorage's value will override.
}
//get the saved value function - return the value of "v" from localStorage. 
function getSavedValue  (v) {
    if (!localStorage.getItem(v)) {
        return "";// You can change this to your defualt value. 
    }
    return localStorage.getItem(v);
}

if(getSavedValue("localStorageQty")) {
  localStorageQty = getSavedValue("localStorageQty").split(",");
  localStorageProductDiscount = getSavedValue("localStorageProductDiscount").split(",");
  localStorageTaxRate = getSavedValue("localStorageTaxRate").split(",");
  localStorageNetUnitPrice = getSavedValue("localStorageNetUnitPrice").split(",");
  localStorageTaxValue = getSavedValue("localStorageTaxValue").split(",");
  localStorageTaxName = getSavedValue("localStorageTaxName").split(",");
  localStorageTaxMethod = getSavedValue("localStorageTaxMethod").split(",");
  localStorageSubTotalUnit = getSavedValue("localStorageSubTotalUnit").split(",");
  localStorageSubTotal = getSavedValue("localStorageSubTotal").split(",");
  localStorageProductId = getSavedValue("localStorageProductId").split(",");
  localStorageProductCode = getSavedValue("localStorageProductCode").split(",");
  localStorageSaleUnit = getSavedValue("localStorageSaleUnit").split(",");
  localStorageTempUnitName = getSavedValue("localStorageTempUnitName").split(",,");
  localStorageSaleUnitOperator = getSavedValue("localStorageSaleUnitOperator").split(",,");
  localStorageSaleUnitOperationValue = getSavedValue("localStorageSaleUnitOperationValue").split(",,");
  /*localStorageQty.pop();
  localStorage.setItem("localStorageQty", localStorageQty);*/
  for(var i = 0; i < localStorageQty.length; i++) {
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ') .qty').val(localStorageQty[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.discount-value').val(localStorageProductDiscount[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.tax-rate').val(localStorageTaxRate[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.net_unit_price').val(localStorageNetUnitPrice[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.tax-value').val(localStorageTaxValue[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.tax-name').val(localStorageTaxName[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.tax-method').val(localStorageTaxMethod[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('td:nth-child(2)').text(localStorageSubTotalUnit[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('td:nth-child(4)').text(localStorageSubTotal[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.subtotal-value').val(localStorageSubTotal[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.product-id').val(localStorageProductId[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.product-code').val(localStorageProductCode[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.sale-unit').val(localStorageSaleUnit[i]);
    if(i==0) {
      localStorageTempUnitName[i] += ',';
      localStorageSaleUnitOperator[i] += ',';
      localStorageSaleUnitOperationValue[i] += ',';
    }
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.sale-unit-operator').val(localStorageSaleUnitOperator[i]);
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.sale-unit-operation-value').val(localStorageSaleUnitOperationValue[i]);

    product_price.push(parseFloat($('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.product_price').val()));
    var quantity = parseFloat($('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.qty').val());
    product_discount.push(parseFloat(localStorageProductDiscount[i] / localStorageQty[i]).toFixed(2));
    tax_rate.push(parseFloat($('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.tax-rate').val()));
    tax_name.push($('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.tax-name').val());
    tax_method.push($('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.tax-method').val());
    temp_unit_name = $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.sale-unit').val().split(',');
    unit_name.push(localStorageTempUnitName[i]);
    unit_operator.push($('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.sale-unit-operator').val());
    unit_operation_value.push($('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.sale-unit-operation-value').val());
    $('table.order-list tbody tr:nth-child(' + (i + 1) + ')').find('.sale-unit').val(temp_unit_name[0]);
    calculateTotal();
    //calculateRowProductData(localStorageQty[i]);
  }
}


$('.selectpicker').selectpicker({
	style: 'btn-link',
});

if(keyboard_active==1){

    $("input.numkey:text").keyboard({
        usePreview: false,
        layout: 'custom',
        display: {
        'accept'  : '&#10004;',
        'cancel'  : '&#10006;'
        },
        customLayout : {
          'normal' : ['1 2 3', '4 5 6', '7 8 9','0 {dec} {bksp}','{clear} {cancel} {accept}']
        },
        restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
        preventPaste : true,  // prevent ctrl-v and right click
        autoAccept : true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active'
        },
    });

    $('input[type="text"]').keyboard({
        usePreview: false,
        autoAccept: true,
        autoAcceptOnEsc: true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active',
            // used when disabling the decimal button {dec}
            // when a decimal exists in the input area
            buttonDisabled: 'disabled'
        },
        change: function(e, keyboard) {
                keyboard.$el.val(keyboard.$preview.val())
                keyboard.$el.trigger('propertychange')        
              }
    });

    $('textarea').keyboard({
        usePreview: false,
        autoAccept: true,
        autoAcceptOnEsc: true,
        css: {
            // input & preview
            // keyboard container
            container: 'center-block dropdown-menu', // jumbotron
            // default state
            buttonDefault: 'btn btn-default',
            // hovered button
            buttonHover: 'btn-primary',
            // Action keys (e.g. Accept, Cancel, Tab, etc);
            // this replaces "actionClass" option
            buttonAction: 'active',
            // used when disabling the decimal button {dec}
            // when a decimal exists in the input area
            buttonDisabled: 'disabled'
        },
        change: function(e, keyboard) {
                keyboard.$el.val(keyboard.$preview.val())
                keyboard.$el.trigger('propertychange')        
              }
    });

    $('#lims_productcodeSearch').keyboard().autocomplete().addAutocomplete({
        // add autocomplete window positioning
        // options here (using position utility)
        position: {
          of: '#lims_productcodeSearch',
          my: 'top+18px',
          at: 'center',
          collision: 'flip'
        }
    });
}

  $("li#notification-icon").on("click", function (argument) {
      $.get('notifications/mark-as-read', function(data) {
          $("span.notification-number").text(alert_product);
      });
  });

  $("#register-details-btn").on("click", function (e) {
      e.preventDefault();
      $.ajax({
          url: 'cash-register/showDetails/'+warehouse_id,
          type: "GET",
          success:function(data) {
              $('#register-details-modal #cash_in_hand').text(data['cash_in_hand']);
              $('#register-details-modal #total_sale_amount').text(data['total_sale_amount']);
              $('#register-details-modal #total_payment').text(data['total_payment']);
              $('#register-details-modal #cash_payment').text(data['cash_payment']);
              $('#register-details-modal #credit_card_payment').text(data['credit_card_payment']);
              $('#register-details-modal #cheque_payment').text(data['cheque_payment']);
              $('#register-details-modal #gift_card_payment').text(data['gift_card_payment']);
              $('#register-details-modal #paypal_payment').text(data['paypal_payment']);
              $('#register-details-modal #total_sale_return').text(data['total_sale_return']);
              $('#register-details-modal #total_expense').text(data['total_expense']);
              $('#register-details-modal #total_cash').text(data['total_cash']);
              $('#register-details-modal input[name=cash_register_id]').val(data['id']);
          }
      });
      $('#register-details-modal').modal('show');
  });

  $("#today-sale-btn").on("click", function (e) {
      e.preventDefault();
      $.ajax({
          url: 'sales/today-sale/',
          type: "GET",
          success:function(data) {
              $('#today-sale-modal .total_sale_amount').text(data['total_sale_amount']);
              $('#today-sale-modal .total_payment').text(data['total_payment']);
              $('#today-sale-modal .cash_payment').text(data['cash_payment']);
              $('#today-sale-modal .credit_card_payment').text(data['credit_card_payment']);
              $('#today-sale-modal .cheque_payment').text(data['cheque_payment']);
              $('#today-sale-modal .gift_card_payment').text(data['gift_card_payment']);
              $('#today-sale-modal .paypal_payment').text(data['paypal_payment']);
              $('#today-sale-modal .total_sale_return').text(data['total_sale_return']);
              $('#today-sale-modal .total_expense').text(data['total_expense']);
              $('#today-sale-modal .total_cash').text(data['total_cash']);
          }
      });
      $('#today-sale-modal').modal('show');
  });

  $("#today-profit-btn").on("click", function (e) {
      e.preventDefault();
      calculateTodayProfit(0);
  });

  $("#today-profit-modal select[name=warehouseId]").on("change", function() {
      calculateTodayProfit($(this).val());
  });

  function calculateTodayProfit(warehouse_id) {
      $.ajax({
            url: 'sales/today-profit/' + warehouse_id,
            type: "GET",
            success:function(data) {
                $('#today-profit-modal .product_revenue').text(data['product_revenue']);
                $('#today-profit-modal .product_cost').text(data['product_cost']);
                $('#today-profit-modal .expense_amount').text(data['expense_amount']);
                $('#today-profit-modal .profit').text(data['profit']);
            }
        });
      $('#today-profit-modal').modal('show');
  }

if(role_id > 2){
    $('#biller_id').addClass('d-none');
    $('#warehouse_id').addClass('d-none');
    $('select[name=warehouse_id]').val(warehouse_id);
    $('select[name=biller_id]').val(biller_id);
    isCashRegisterAvailable(warehouse_id);
}
else {
    if(getSavedValue("warehouse_id")){
      warehouse_id = getSavedValue("warehouse_id");
    }
    else {
      warehouse_id = $("input[name='warehouse_id_hidden']").val();
    }

    if(getSavedValue("biller_id")){
      biller_id = getSavedValue("biller_id");
    }
    else {
      biller_id = $("input[name='biller_id_hidden']").val();
    }
    $('select[name=warehouse_id]').val(warehouse_id);
    $('select[name=biller_id]').val(biller_id);
}

  if(getSavedValue("biller_id")) {
    $('select[name=customer_id]').val(getSavedValue("customer_id"));
  }
  else {
    $('select[name=customer_id]').val($("input[name='customer_id_hidden']").val());
  }

$('.selectpicker').selectpicker('refresh');

var id = $("#customer_id").val();
$.get('sales/getcustomergroup/' + id, function(data) {
    customer_group_rate = (data / 100);
});

var id = $("#warehouse_id").val();
$.get('sales/getproduct/' + id, function(data) {
    lims_product_array = [];
    product_code = data[0];
    product_name = data[1];
    product_qty = data[2];
    product_type = data[3];
    product_id = data[4];
    product_list = data[5];
    qty_list = data[6];
    product_warehouse_price = data[7];
    $.each(product_code, function(index) {
        lims_product_array.push(product_code[index] + ' (' + product_name[index] + ')');
    });
});

isCashRegisterAvailable(id);

function isCashRegisterAvailable(warehouse_id) {
    $.ajax({
        url: 'cash-register/check-availability/'+warehouse_id,
        type: "GET",
        success:function(data) {
            if(data == 'false') {
              $("#register-details-btn").addClass('d-none');
              $('#cash-register-modal select[name=warehouse_id]').val(warehouse_id);

              if(role_id <= 2)
                $("#cash-register-modal .warehouse-section").removeClass('d-none');
              else
                $("#cash-register-modal .warehouse-section").addClass('d-none');

              $('.selectpicker').selectpicker('refresh');
              $("#cash-register-modal").modal('show');
            }
            else
              $("#register-details-btn").removeClass('d-none');
        }
    });
}

if(keyboard_active==1){
    $('#lims_productcodeSearch').bind('keyboardChange', function (e, keyboard, el) {
        var customer_id = $('#customer_id').val();
        var warehouse_id = $('select[name="warehouse_id"]').val();
        temp_data = $('#lims_productcodeSearch').val();
        if(!customer_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Customer!');
        }
        else if(!warehouse_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Warehouse!');
        }
    });
}
else{
    $('#lims_productcodeSearch').on('input', function(){
        var customer_id = $('#customer_id').val();
        var warehouse_id = $('#warehouse_id').val();
        temp_data = $('#lims_productcodeSearch').val();
        if(!customer_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Customer!');
        }
        else if(!warehouse_id){
            $('#lims_productcodeSearch').val(temp_data.substring(0, temp_data.length - 1));
            alert('Please select Warehouse!');
        }

    });
}

$("#print-btn").on("click", function(){
      var divToPrint=document.getElementById('sale-details');
      var newWin=window.open('','Print-Window');
      newWin.document.open();
      newWin.document.write('<link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css"><style type="text/css">@media  print {.modal-dialog { max-width: 1000px;} }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
      newWin.document.close();
      setTimeout(function(){newWin.close();},10);
});

$('body').on('click', function(e){
    $('.filter-window').hide('slide', {direction: 'right'}, 'fast');
});

$('#category-filter').on('click', function(e){
    e.stopPropagation();
    $('.filter-window').show('slide', {direction: 'right'}, 'fast');
    $('.category').show();
    $('.brand').hide();
});

$('.category-img').on('click', function(){
    var category_id = $(this).data('category');
    var brand_id = 0;

    $(".table-container").children().remove();
    $.get('sales/getproduct/' + category_id + '/' + brand_id, function(data) {
        populateProduct(data);
    });
});

$('#brand-filter').on('click', function(e){
    e.stopPropagation();
    $('.filter-window').show('slide', {direction: 'right'}, 'fast');
    $('.brand').show();
    $('.category').hide();
});

$('.brand-img').on('click', function(){
    var brand_id = $(this).data('brand');
    var category_id = 0;

    $(".table-container").children().remove();
    $.get('sales/getproduct/' + category_id + '/' + brand_id, function(data) {
        populateProduct(data);
    });
});

$('#featured-filter').on('click', function(){
    $(".table-container").children().remove();
    $.get('sales/getfeatured', function(data) {
        populateProduct(data);
    });
});

function populateProduct(data) {
    var tableData = '<table id="product-table" class="table no-shadow product-list"> <thead class="d-none"> <tr> <th></th> <th></th> <th></th> <th></th> <th></th> </tr></thead> <tbody><tr>';

    if (Object.keys(data).length != 0) {
        $.each(data['name'], function(index) {
            var product_info = data['code'][index]+' (' + data['name'][index] + ')';
            if(index % 5 == 0 && index != 0)
                tableData += '</tr><tr><td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><img  src="public/images/product/'+data['image'][index]+'" width="100%" /><p>'+data['name'][index]+'</p><span>'+data['code'][index]+'</span></td>';
            else
                tableData += '<td class="product-img sound-btn" title="'+data['name'][index]+'" data-product = "'+product_info+'"><img  src="public/images/product/'+data['image'][index]+'" width="100%" /><p>'+data['name'][index]+'</p><span>'+data['code'][index]+'</span></td>';
        });

        if(data['name'].length % 5){
            var number = 5 - (data['name'].length % 5);
            while(number > 0)
            {
                tableData += '<td style="border:none;"></td>';
                number--;
            }
        }

        tableData += '</tr></tbody></table>';
        $(".table-container").html(tableData);
        $('#product-table').DataTable( {
          "order": [],
          'pageLength': product_row_number,
           'language': {
              'paginate': {
                  'previous': '<i class="fa fa-angle-left"></i>',
                  'next': '<i class="fa fa-angle-right"></i>'
              }
          },
          dom: 'tp'
        });
        $('table.product-list').hide();
        $('table.product-list').show(500);
    }
    else{
        tableData += '<td class="text-center">No data avaialable</td></tr></tbody></table>'
        $(".table-container").html(tableData);
    }
}

$('select[name="customer_id"]').on('change', function() {
    saveValue(this);
    var id = $(this).val();
    $.get('sales/getcustomergroup/' + id, function(data) {
        customer_group_rate = (data / 100);
    });
});

$('select[name="biller_id"]').on('change', function() {
    saveValue(this);
});

$('select[name="warehouse_id"]').on('change', function() {
    saveValue(this);
    warehouse_id = $(this).val();
    $.get('sales/getproduct/' + warehouse_id, function(data) {
        lims_product_array = [];
        product_code = data[0];
        product_name = data[1];
        product_qty = data[2];
        product_type = data[3];
        product_id = data[4];
        product_list = data[5];
        qty_list = data[6];
        product_warehouse_price = data[7];
        $.each(product_code, function(index) {
            lims_product_array.push(product_code[index] + ' (' + product_name[index] + ')');
        });
    });

    isCashRegisterAvailable(warehouse_id);
});

var lims_productcodeSearch = $('#lims_productcodeSearch');

lims_productcodeSearch.autocomplete({
    source: function(request, response) {
        var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
        response($.grep(lims_product_array, function(item) {
            return matcher.test(item);
        }));
    },
    response: function(event, ui) {
        if (ui.content.length == 1) {
            var data = ui.content[0].value;
            $(this).autocomplete( "close" );
            productSearch(data);
        };
    },
    select: function(event, ui) {
        var data = ui.item.value;
        productSearch(data);
    },
});

$('#myTable').keyboard({
        accepted : function(event, keyboard, el) {
            checkQuantity(el.value, true);
      }
    });

$("#myTable").on('click', '.plus', function() {
    rowindex = $(this).closest('tr').index();
    var qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val();

    if(!qty)
      qty = 1;
    else
      qty = parseFloat(qty) + 1;

    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
    checkQuantity(String(qty), true);
    localStorage.setItem("tbody-id", $("table.order-list tbody").html());
});

$("#myTable").on('click', '.minus', function() {
    rowindex = $(this).closest('tr').index();
    var qty = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val()) - 1;
    if (qty > 0) {
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
    } else {
        qty = 1;
    }
    checkQuantity(String(qty), true);
});

//Change quantity
$("#myTable").on('input', '.qty', function() {
    rowindex = $(this).closest('tr').index();
    if($(this).val() < 1 && $(this).val() != '') {
      $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(1);
      alert("Quantity can't be less than 1");
    }
    checkQuantity($(this).val(), true);
});

$("#myTable").on('click', '.qty', function() {
    rowindex = $(this).closest('tr').index();
});

$(document).on('click', '.sound-btn', function() {
    var audio = $("#mysoundclip1")[0];
    audio.play();
});

$(document).on('click', '.product-img', function() {
    var customer_id = $('#customer_id').val();
    var warehouse_id = $('select[name="warehouse_id"]').val();
    if(!customer_id)
        alert('Please select Customer!');
    else if(!warehouse_id)
        alert('Please select Warehouse!');
    else{
        var data = $(this).data('product');
        data = data.split(" ");
        pos = product_code.indexOf(data[0]);
        if(pos < 0)
            alert('Product is not avaialable in the selected warehouse');
        else{
            productSearch(data[0]);
        }
    }
});
//Delete product
$("table.order-list tbody").on("click", ".ibtnDel", function(event) {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    rowindex = $(this).closest('tr').index();
    product_price.splice(rowindex, 1);
    product_discount.splice(rowindex, 1);
    tax_rate.splice(rowindex, 1);
    tax_name.splice(rowindex, 1);
    tax_method.splice(rowindex, 1);
    unit_name.splice(rowindex, 1);
    unit_operator.splice(rowindex, 1);
    unit_operation_value.splice(rowindex, 1);

    localStorageProductId.splice(rowindex, 1);
    localStorageQty.splice(rowindex, 1);
    localStorageSaleUnit.splice(rowindex, 1);
    localStorageProductDiscount.splice(rowindex, 1);
    localStorageTaxRate.splice(rowindex, 1);
    localStorageNetUnitPrice.splice(rowindex, 1);
    localStorageTaxValue.splice(rowindex, 1);
    localStorageSubTotalUnit.splice(rowindex, 1);
    localStorageSubTotal.splice(rowindex, 1);
    localStorageProductCode.splice(rowindex, 1);

    localStorageTaxName.splice(rowindex, 1);
    localStorageTaxMethod.splice(rowindex, 1);
    localStorageTempUnitName.splice(rowindex, 1);
    localStorageSaleUnitOperator.splice(rowindex, 1);
    localStorageSaleUnitOperationValue.splice(rowindex, 1);
    
    localStorage.setItem("localStorageProductId", localStorageProductId);
    localStorage.setItem("localStorageQty", localStorageQty);
    localStorage.setItem("localStorageSaleUnit", localStorageSaleUnit);
    localStorage.setItem("localStorageProductCode", localStorageProductCode);
    localStorage.setItem("localStorageProductDiscount", localStorageProductDiscount);
    localStorage.setItem("localStorageTaxRate", localStorageTaxRate);
    localStorage.setItem("localStorageTaxName", localStorageTaxName);
    localStorage.setItem("localStorageTaxMethod", localStorageTaxMethod);
    localStorage.setItem("localStorageTempUnitName", localStorageTempUnitName);
    localStorage.setItem("localStorageSaleUnitOperator", localStorageSaleUnitOperator);
    localStorage.setItem("localStorageSaleUnitOperationValue", localStorageSaleUnitOperationValue);
    localStorage.setItem("localStorageNetUnitPrice", localStorageNetUnitPrice);
    localStorage.setItem("localStorageTaxValue", localStorageTaxValue);
    localStorage.setItem("localStorageSubTotalUnit", localStorageSubTotalUnit);
    localStorage.setItem("localStorageSubTotal", localStorageSubTotal);

    $(this).closest("tr").remove();
    localStorage.setItem("tbody-id", $("table.order-list tbody").html());
    calculateTotal();
});

//Edit product
$("table.order-list").on("click", ".edit-product", function() {
    rowindex = $(this).closest('tr').index();
    edit();
});

//Update product
$('button[name="update_btn"]').on("click", function() {
    var edit_discount = $('input[name="edit_discount"]').val();
    var edit_qty = $('input[name="edit_qty"]').val();
    var edit_unit_price = $('input[name="edit_unit_price"]').val();

    if (parseFloat(edit_discount) > parseFloat(edit_unit_price)) {
        alert('Invalid Discount Input!');
        return;
    }

    if(edit_qty < 1) {
        $('input[name="edit_qty"]').val(1);
        edit_qty = 1;
        alert("Quantity can't be less than 1");
    }
    
    var tax_rate_all = <?php echo json_encode($tax_rate_all) ?>;

    tax_rate[rowindex] = localStorageTaxRate[rowindex] = parseFloat(tax_rate_all[$('select[name="edit_tax_rate"]').val()]);
    tax_name[rowindex] = localStorageTaxName[rowindex] = $('select[name="edit_tax_rate"] option:selected').text();

    product_discount[rowindex] = $('input[name="edit_discount"]').val();
    if(product_type[pos] == 'standard'){
        var row_unit_operator = unit_operator[rowindex].slice(0, unit_operator[rowindex].indexOf(","));
        var row_unit_operation_value = unit_operation_value[rowindex].slice(0, unit_operation_value[rowindex].indexOf(","));
        if (row_unit_operator == '*') {
            product_price[rowindex] = $('input[name="edit_unit_price"]').val() / row_unit_operation_value;
        } else {
            product_price[rowindex] = $('input[name="edit_unit_price"]').val() * row_unit_operation_value;
        }
        var position = $('select[name="edit_unit"]').val();
        var temp_operator = temp_unit_operator[position];
        var temp_operation_value = temp_unit_operation_value[position];
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.sale-unit').val(temp_unit_name[position]);
        temp_unit_name.splice(position, 1);
        temp_unit_operator.splice(position, 1);
        temp_unit_operation_value.splice(position, 1);

        temp_unit_name.unshift($('select[name="edit_unit"] option:selected').text());
        temp_unit_operator.unshift(temp_operator);
        temp_unit_operation_value.unshift(temp_operation_value);

        unit_name[rowindex] = localStorageTempUnitName[rowindex] = temp_unit_name.toString() + ',';
        unit_operator[rowindex] = localStorageSaleUnitOperator[rowindex] = temp_unit_operator.toString() + ',';
        unit_operation_value[rowindex] = localStorageSaleUnitOperationValue[rowindex] = temp_unit_operation_value.toString() + ',';

        localStorage.setItem("localStorageTaxRate", localStorageTaxRate);
        localStorage.setItem("localStorageTaxName", localStorageTaxName);
        localStorage.setItem("localStorageTempUnitName", localStorageTempUnitName);
        localStorage.setItem("localStorageSaleUnitOperator", localStorageSaleUnitOperator);
        localStorage.setItem("localStorageSaleUnitOperationValue", localStorageSaleUnitOperationValue);
    }
    else {
        product_price[rowindex] = $('input[name="edit_unit_price"]').val();
    }
    checkQuantity(edit_qty, false);
});

$('button[name="order_discount_btn"]').on("click", function() {
    calculateGrandTotal();
});

$('button[name="shipping_cost_btn"]').on("click", function() {
    calculateGrandTotal();
});

$('button[name="order_tax_btn"]').on("click", function() {
    calculateGrandTotal();
});

$(".coupon-check").on("click",function() {
    couponDiscount();
});

$(".payment-btn").on("click", function() {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    $('input[name="paid_amount"]').val($("#grand-total").text());
    $('input[name="paying_amount"]').val($("#grand-total").text());
    $('.qc').data('initial', 1);
});

$("#draft-btn").on("click",function(){
    var audio = $("#mysoundclip2")[0];
    audio.play();
    $('input[name="sale_status"]').val(3);
    $('input[name="paying_amount"]').prop('required',false);
    $('input[name="paid_amount"]').prop('required',false);
    var rownumber = $('table.order-list tbody tr:last').index();
    if (rownumber < 0) {
        alert("Please insert product to order table!")
    }
    else
        $('.payment-form').submit();
});

$("#submit-btn").on("click", function() {
    $('.payment-form').submit();
});

$("#gift-card-btn").on("click",function() {
    $('select[name="paid_by_id_select"]').val(2);
    $('.selectpicker').selectpicker('refresh');
    $('div.qc').hide();
    giftCard();
});

$("#credit-card-btn").on("click",function() {
    $('select[name="paid_by_id_select"]').val(3);
    $('.selectpicker').selectpicker('refresh');
    $('div.qc').hide();
    creditCard();
});

$("#cheque-btn").on("click",function() {
    $('select[name="paid_by_id_select"]').val(4);
    $('.selectpicker').selectpicker('refresh');
    $('div.qc').hide();
    cheque();
});

$("#cash-btn").on("click",function() {
    $('select[name="paid_by_id_select"]').val(1);
    $('.selectpicker').selectpicker('refresh');
    $('div.qc').show();
    hide();
});

$("#paypal-btn").on("click",function() {
    $('select[name="paid_by_id_select"]').val(5);
    $('.selectpicker').selectpicker('refresh');
    $('div.qc').hide();
    hide();
});

$("#deposit-btn").on("click",function() {
    $('select[name="paid_by_id_select"]').val(6);
    $('.selectpicker').selectpicker('refresh');
    $('div.qc').hide();
    hide();
    deposits();
});

$('select[name="paid_by_id_select"]').on("change", function() {       
    var id = $(this).val();
    $(".payment-form").off("submit");
    if(id == 2) {
        $('div.qc').hide();
        giftCard();
    }
    else if (id == 3) {
        $('div.qc').hide();
        creditCard();
    } else if (id == 4) {
        $('div.qc').hide();
        cheque();
    } else {
        hide();
        if(id == 1)
            $('div.qc').show();
        else if(id == 6) {
            $('div.qc').hide();
            deposits();
        }
    }
});

$('#add-payment select[name="gift_card_id_select"]').on("change", function() {
    var balance = gift_card_amount[$(this).val()] - gift_card_expense[$(this).val()];
    $('#add-payment input[name="gift_card_id"]').val($(this).val());
    if($('input[name="paid_amount"]').val() > balance){
        alert('Amount exceeds card balance! Gift Card balance: '+ balance);
    }
});

$('#add-payment input[name="paying_amount"]').on("input", function() {
    change($(this).val(), $('input[name="paid_amount"]').val());
});

$('input[name="paid_amount"]').on("input", function() {
    if( $(this).val() > parseFloat($('input[name="paying_amount"]').val()) ) {
        alert('Paying amount cannot be bigger than recieved amount');
        $(this).val('');
    }
    else if( $(this).val() > parseFloat($('#grand-total').text()) ){
        alert('Paying amount cannot be bigger than grand total');
        $(this).val('');
    }

    change( $('input[name="paying_amount"]').val(), $(this).val() );
    var id = $('select[name="paid_by_id_select"]').val();
    if(id == 2){
        var balance = gift_card_amount[$("#gift_card_id_select").val()] - gift_card_expense[$("#gift_card_id_select").val()];
        if($(this).val() > balance)
            alert('Amount exceeds card balance! Gift Card balance: '+ balance);
    }
    else if(id == 6){
        if( $('input[name="paid_amount"]').val() > deposit[$('#customer_id').val()] )
            alert('Amount exceeds customer deposit! Customer deposit : '+ deposit[$('#customer_id').val()]);
    }
});

$('.transaction-btn-plus').on("click", function() {
    $(this).addClass('d-none');
    $('.transaction-btn-close').removeClass('d-none');
});

$('.transaction-btn-close').on("click", function() {
    $(this).addClass('d-none');
    $('.transaction-btn-plus').removeClass('d-none');
});

$('.coupon-btn-plus').on("click", function() {
    $(this).addClass('d-none');
    $('.coupon-btn-close').removeClass('d-none');
});

$('.coupon-btn-close').on("click", function() {
    $(this).addClass('d-none');
    $('.coupon-btn-plus').removeClass('d-none');
});

$(document).on('click', '.qc-btn', function(e) {
    if($(this).data('amount')) {
        if($('.qc').data('initial')) {
            $('input[name="paying_amount"]').val( $(this).data('amount').toFixed(2) );
            $('.qc').data('initial', 0);
        }
        else {
            $('input[name="paying_amount"]').val( (parseFloat($('input[name="paying_amount"]').val()) + $(this).data('amount')).toFixed(2) );
        }
    }
    else
        $('input[name="paying_amount"]').val('0.00');
    change( $('input[name="paying_amount"]').val(), $('input[name="paid_amount"]').val() );
});

function change(paying_amount, paid_amount) {
    $("#change").text( parseFloat(paying_amount - paid_amount).toFixed(2) );
}

function confirmDelete() {
    if (confirm("Are you sure want to delete?")) {
        return true;
    }
    return false;
}

function productSearch(data) {
    $.ajax({
        type: 'GET',
        url: 'sales/lims_product_search',
        data: {
            data: data
        },
        success: function(data) {
            var flag = 1;
            $(".product-id").each(function(i) {
                if ($(this).val() == data[9]) {
                    rowindex = i;
                    var pre_qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val();
                    if(pre_qty)
                        var qty = parseFloat(pre_qty) + 1;
                    else
                        var qty = 1;
                    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
                    flag = 0;
                    checkQuantity(String(qty), true);
                    flag = 0;
                    localStorage.setItem("tbody-id", $("table.order-list tbody").html());
                }
            });
            $("input[name='product_code_name']").val('');
            if(flag){
                addNewProduct(data);
            }
        }
    });
}

function addNewProduct(data){
    var newRow = $("<tr>");
    var cols = '';
    temp_unit_name = (data[6]).split(',');
    cols += '<td class="col-sm-4 product-title"><button type="button" class="edit-product btn btn-link" data-toggle="modal" data-target="#editModal"><strong>' + data[0] + '</strong></button> [' + data[1] + '] <p>In Stock: <span class="in-stock"></span></p></td>';
    cols += '<td class="col-sm-2 product-price"></td>';
    cols += '<td class="col-sm-3"><div class="input-group"><span class="input-group-btn"><button type="button" class="btn btn-default minus"><span class="dripicons-minus"></span></button></span><input type="text" name="qty[]" class="form-control qty numkey input-number" step="any" required><span class="input-group-btn"><button type="button" class="btn btn-default plus"><span class="dripicons-plus"></span></button></span></div></td>';
    cols += '<td class="col-sm-2 sub-total"></td>';
    cols += '<td class="col-sm-1"><button type="button" class="ibtnDel btn btn-danger btn-sm"><i class="dripicons-cross"></i></button></td>';
    cols += '<input type="hidden" class="product-code" name="product_code[]" value="' + data[1] + '"/>';
    cols += '<input type="hidden" class="product-id" name="product_id[]" value="' + data[9] + '"/>';
    cols += '<input type="hidden" class="product_price" />';
    cols += '<input type="hidden" class="sale-unit" name="sale_unit[]" value="' + temp_unit_name[0] + '"/>';
    cols += '<input type="hidden" class="net_unit_price" name="net_unit_price[]" />';
    cols += '<input type="hidden" class="discount-value" name="discount[]" />';
    cols += '<input type="hidden" class="tax-rate" name="tax_rate[]" value="' + data[3] + '"/>';
    cols += '<input type="hidden" class="tax-value" name="tax[]" />';
    cols += '<input type="hidden" class="tax-name" value="'+data[4]+'" />';
    cols += '<input type="hidden" class="tax-method" value="'+data[5]+'" />';
    cols += '<input type="hidden" class="sale-unit-operator" value="'+data[7]+'" />';
    cols += '<input type="hidden" class="sale-unit-operation-value" value="'+data[8]+'" />';
    cols += '<input type="hidden" class="subtotal-value" name="subtotal[]" />';

    newRow.append(cols);
    if(keyboard_active==1) {
        $("table.order-list tbody").prepend(newRow).find('.qty').keyboard({usePreview: false, layout: 'custom', display: { 'accept'  : '&#10004;', 'cancel'  : '&#10006;' }, customLayout : {
          'normal' : ['1 2 3', '4 5 6', '7 8 9','0 {dec} {bksp}','{clear} {cancel} {accept}']}, restrictInput : true, preventPaste : true, autoAccept : true, css: { container: 'center-block dropdown-menu', buttonDefault: 'btn btn-default', buttonHover: 'btn-primary',buttonAction: 'active', buttonDisabled: 'disabled'},});
    }
    else
        $("table.order-list tbody").prepend(newRow);

    rowindex = newRow.index();
    pos = product_code.indexOf(data[1]);
    if(!data[11] && product_warehouse_price[pos]) {
        product_price.splice(rowindex, 0, parseFloat(product_warehouse_price[pos] * currency['exchange_rate']) + parseFloat(product_warehouse_price[pos] * currency['exchange_rate'] * customer_group_rate));
    }
    else {
        product_price.splice(rowindex, 0, parseFloat(data[2] * currency['exchange_rate']) + parseFloat(data[2] * currency['exchange_rate'] * customer_group_rate));
    }
    product_discount.splice(rowindex, 0, '0.00');
    tax_rate.splice(rowindex, 0, parseFloat(data[3]));
    tax_name.splice(rowindex, 0, data[4]);
    tax_method.splice(rowindex, 0, data[5]);
    unit_name.splice(rowindex, 0, data[6]);
    unit_operator.splice(rowindex, 0, data[7]);
    unit_operation_value.splice(rowindex, 0, data[8]);
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(1);
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product_price').val(product_price[rowindex]);
    localStorageQty.splice(rowindex, 0, 1);
    localStorageProductId.splice(rowindex, 0, data[9]);
    localStorageProductCode.splice(rowindex, 0, data[1]);
    localStorageSaleUnit.splice(rowindex, 0, temp_unit_name[0]);
    localStorageProductDiscount.splice(rowindex, 0, product_discount[rowindex]);
    localStorageTaxRate.splice(rowindex, 0, tax_rate[rowindex].toFixed(2));
    localStorageTaxName.splice(rowindex, 0, data[4]);
    localStorageTaxMethod.splice(rowindex, 0, data[5]);
    localStorageTempUnitName.splice(rowindex, 0, data[6]);
    localStorageSaleUnitOperator.splice(rowindex, 0, data[7]);
    localStorageSaleUnitOperationValue.splice(rowindex, 0, data[8]);
    //put some dummy value
    localStorageNetUnitPrice.splice(rowindex, 0, '0.00');
    localStorageTaxValue.splice(rowindex, 0, '0.00');
    localStorageSubTotalUnit.splice(rowindex, 0, '0.00');
    localStorageSubTotal.splice(rowindex, 0, '0.00');
    
    localStorage.setItem("localStorageProductId", localStorageProductId);
    localStorage.setItem("localStorageSaleUnit", localStorageSaleUnit);
    localStorage.setItem("localStorageProductCode", localStorageProductCode);
    localStorage.setItem("localStorageTaxName", localStorageTaxName);
    localStorage.setItem("localStorageTaxMethod", localStorageTaxMethod);
    localStorage.setItem("localStorageTempUnitName", localStorageTempUnitName);
    localStorage.setItem("localStorageSaleUnitOperator", localStorageSaleUnitOperator);
    localStorage.setItem("localStorageSaleUnitOperationValue", localStorageSaleUnitOperationValue);
    checkQuantity(1, true);
    localStorage.setItem("tbody-id", $("table.order-list tbody").html());
}

function edit(){
    var row_product_name_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(1)').text();
    $('#modal_header').text(row_product_name_code);

    var qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val();
    $('input[name="edit_qty"]').val(qty);

    $('input[name="edit_discount"]').val(parseFloat(product_discount[rowindex]).toFixed(2));

    var tax_name_all = <?php echo json_encode($tax_name_all) ?>;
    pos = tax_name_all.indexOf(tax_name[rowindex]);
    $('select[name="edit_tax_rate"]').val(pos);

    var row_product_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-code').val();
    pos = product_code.indexOf(row_product_code);
    if(product_type[pos] == 'standard'){
        unitConversion();
        temp_unit_name = (unit_name[rowindex]).split(',');
        temp_unit_name.pop();
        temp_unit_operator = (unit_operator[rowindex]).split(',');
        temp_unit_operator.pop();
        temp_unit_operation_value = (unit_operation_value[rowindex]).split(',');
        temp_unit_operation_value.pop();
        $('select[name="edit_unit"]').empty();
        $.each(temp_unit_name, function(key, value) {
            $('select[name="edit_unit"]').append('<option value="' + key + '">' + value + '</option>');
        });
        $("#edit_unit").show();
    }
    else{
        row_product_price = product_price[rowindex];
        $("#edit_unit").hide();
    }
    $('input[name="edit_unit_price"]').val(row_product_price.toFixed(2));
    $('.selectpicker').selectpicker('refresh');
}

function couponDiscount() {
    var rownumber = $('table.order-list tbody tr:last').index();
    if (rownumber < 0) {
        alert("Please insert product to order table!")
    }
    else if($("#coupon-code").val() != ''){
        valid = 0;
        $.each(coupon_list, function(key, value) {
            if($("#coupon-code").val() == value['code']){
                valid = 1;
                todyDate = <?php echo json_encode(date('Y-m-d'))?>;
                if(parseFloat(value['quantity']) <= parseFloat(value['used']))
                    alert('This Coupon is no longer available');
                else if(todyDate > value['expired_date'])
                    alert('This Coupon has expired!');
                else if(value['type'] == 'fixed'){
                    if(parseFloat($('input[name="grand_total"]').val()) >= value['minimum_amount']) {
                        $('input[name="grand_total"]').val($('input[name="grand_total"]').val() - value['amount']);
                        $('#grand-total').text(parseFloat($('input[name="grand_total"]').val()).toFixed(2));
                        if(!$('input[name="coupon_active"]').val())
                            alert('Congratulation! You got '+value['amount']+' '+currency+' discount');
                        $(".coupon-check").prop("disabled",true);
                        $("#coupon-code").prop("disabled",true);
                        $('input[name="coupon_active"]').val(1);
                        $("#coupon-modal").modal('hide');
                        $('input[name="coupon_id"]').val(value['id']);
                        $('input[name="coupon_discount"]').val(value['amount']);
                        $('#coupon-text').text(parseFloat(value['amount']).toFixed(2));
                    }
                    else
                        alert('Grand Total is not sufficient for discount! Required '+value['minimum_amount']+' '+currency);
                }
                else{
                    var grand_total = $('input[name="grand_total"]').val();
                    var coupon_discount = grand_total * (value['amount'] / 100);
                    grand_total = grand_total - coupon_discount;
                    $('input[name="grand_total"]').val(grand_total);
                    $('#grand-total').text(parseFloat(grand_total).toFixed(2));
                    if(!$('input[name="coupon_active"]').val())
                            alert('Congratulation! You got '+value['amount']+'% discount');
                    $(".coupon-check").prop("disabled",true);
                    $("#coupon-code").prop("disabled",true);
                    $('input[name="coupon_active"]').val(1);
                    $("#coupon-modal").modal('hide');
                    $('input[name="coupon_id"]').val(value['id']);
                    $('input[name="coupon_discount"]').val(coupon_discount);
                    $('#coupon-text').text(parseFloat(coupon_discount).toFixed(2));
                }
            }
        });
        if(!valid)
            alert('Invalid coupon code!');
    }
}

function checkQuantity(sale_qty, flag) {
    var row_product_code = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.product-code').val();
    pos = product_code.indexOf(row_product_code);
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.in-stock').text(product_qty[pos]);
    localStorageQty[rowindex] = sale_qty;
    localStorage.setItem("localStorageQty", localStorageQty);
    if(product_type[pos] == 'standard') {
        var operator = unit_operator[rowindex].split(',');
        var operation_value = unit_operation_value[rowindex].split(',');
        if(operator[0] == '*')
            total_qty = sale_qty * operation_value[0];
        else if(operator[0] == '/')
            total_qty = sale_qty / operation_value[0];
        if (total_qty > parseFloat(product_qty[pos])) {
            alert('Quantity exceeds stock quantity!');
            if (flag) {
                sale_qty = sale_qty.substring(0, sale_qty.length - 1);
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
                localStorageQty[rowindex] = sale_qty;
                localStorage.setItem("localStorageQty", localStorageQty);
                checkQuantity(sale_qty, true);
            } else {
                localStorageQty[rowindex] = sale_qty;
                localStorage.setItem("localStorageQty", localStorageQty);
                edit();
                return;
            }
        }
    }
    else if(product_type[pos] == 'combo'){
        child_id = product_list[pos].split(',');
        child_qty = qty_list[pos].split(',');
        $(child_id).each(function(index) {
            var position = product_id.indexOf(parseInt(child_id[index]));
            if( parseFloat(sale_qty * child_qty[index]) > product_qty[position] ) {
                alert('Quantity exceeds stock quantity!');
                if (flag) {
                    sale_qty = sale_qty.substring(0, sale_qty.length - 1);
                    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
                }
                else {
                    edit();
                    flag = true;
                    return false;
                }
            }
        });
    }

    if(!flag){
        $('#editModal').modal('hide');
        $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.qty').val(sale_qty);
    }
    calculateRowProductData(sale_qty);

}

function unitConversion() {
    var row_unit_operator = unit_operator[rowindex].slice(0, unit_operator[rowindex].indexOf(","));
    var row_unit_operation_value = unit_operation_value[rowindex].slice(0, unit_operation_value[rowindex].indexOf(","));

    if (row_unit_operator == '*') {
        row_product_price = product_price[rowindex] * row_unit_operation_value;
    } else {
        row_product_price = product_price[rowindex] / row_unit_operation_value;
    }
}

function calculateRowProductData(quantity) {
    if(product_type[pos] == 'standard')
        unitConversion();
    else
        row_product_price = product_price[rowindex];

    if (tax_method[rowindex] == 1) {
        var net_unit_price = row_product_price - product_discount[rowindex];
        var tax = net_unit_price * quantity * (tax_rate[rowindex] / 100);
        var sub_total = (net_unit_price * quantity) + tax;
        
        if(parseFloat(quantity))
            var sub_total_unit = sub_total / quantity;
        else
            var sub_total_unit = sub_total;
    } 
    else {
        var sub_total_unit = row_product_price - product_discount[rowindex];
        var net_unit_price = (100 / (100 + tax_rate[rowindex])) * sub_total_unit;
        var tax = (sub_total_unit - net_unit_price) * quantity;
        var sub_total = sub_total_unit * quantity;
    }

    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.discount-value').val((product_discount[rowindex] * quantity).toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-rate').val(tax_rate[rowindex].toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.net_unit_price').val(net_unit_price.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax-value').val(tax.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(2)').text(sub_total_unit.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('td:nth-child(4)').text(sub_total.toFixed(2));
    $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.subtotal-value').val(sub_total.toFixed(2));

    localStorageProductDiscount.splice(rowindex, 1, (product_discount[rowindex] * quantity).toFixed(2));
    localStorageTaxRate.splice(rowindex, 1, tax_rate[rowindex].toFixed(2));
    localStorageNetUnitPrice.splice(rowindex, 1, net_unit_price.toFixed(2));
    localStorageTaxValue.splice(rowindex, 1, tax.toFixed(2));
    localStorageSubTotalUnit.splice(rowindex, 1, sub_total_unit.toFixed(2));
    localStorageSubTotal.splice(rowindex, 1, sub_total.toFixed(2));
    console.log(localStorageNetUnitPrice);
    localStorage.setItem("localStorageProductDiscount", localStorageProductDiscount);
    localStorage.setItem("localStorageTaxRate", localStorageTaxRate);
    localStorage.setItem("localStorageNetUnitPrice", localStorageNetUnitPrice);
    localStorage.setItem("localStorageTaxValue", localStorageTaxValue);
    localStorage.setItem("localStorageSubTotalUnit", localStorageSubTotalUnit);
    localStorage.setItem("localStorageSubTotal", localStorageSubTotal);

    calculateTotal();
}

function calculateTotal() {
    //Sum of quantity
    var total_qty = 0;
    $("table.order-list tbody .qty").each(function(index) {
        if ($(this).val() == '') {
            total_qty += 0;
        } else {
            total_qty += parseFloat($(this).val());
        }
    });
    $('input[name="total_qty"]').val(total_qty);

    //Sum of discount
    var total_discount = 0;
    $("table.order-list tbody .discount-value").each(function() {
        total_discount += parseFloat($(this).val());
    });

    $('input[name="total_discount"]').val(total_discount.toFixed(2));

    //Sum of tax
    var total_tax = 0;
    $(".tax-value").each(function() {
        total_tax += parseFloat($(this).val());
    });

    $('input[name="total_tax"]').val(total_tax.toFixed(2));

    //Sum of subtotal
    var total = 0;
    $(".sub-total").each(function() {
        total += parseFloat($(this).text());
    });
    $('input[name="total_price"]').val(total.toFixed(2));

    calculateGrandTotal();
}

function calculateGrandTotal() {
    var item = $('table.order-list tbody tr:last').index();
    var total_qty = parseFloat($('input[name="total_qty"]').val());
    var subtotal = parseFloat($('input[name="total_price"]').val());
    var order_tax = parseFloat($('select[name="order_tax_rate_select"]').val());
    //alert(order_tax);
    localStorage.setItem("order-tax-rate-select", order_tax);
    var order_discount = parseFloat($('input[name="order_discount"]').val());
    if (!order_discount)
        order_discount = 0.00;
    $("#discount").text(order_discount.toFixed(2));

    var shipping_cost = parseFloat($('input[name="shipping_cost"]').val());
    if (!shipping_cost)
        shipping_cost = 0.00;

    item = ++item + '(' + total_qty + ')';
    order_tax = (subtotal - order_discount) * (order_tax / 100);
    var grand_total = (subtotal + order_tax + shipping_cost) - order_discount;
    $('input[name="grand_total"]').val(grand_total.toFixed(2));

    couponDiscount();
    var coupon_discount = parseFloat($('input[name="coupon_discount"]').val());
    if (!coupon_discount)
        coupon_discount = 0.00;
    grand_total -= coupon_discount;

    $('#item').text(item);
    $('input[name="item"]').val($('table.order-list tbody tr:last').index() + 1);
    $('#subtotal').text(subtotal.toFixed(2));
    $('#tax').text(order_tax.toFixed(2));
    $('input[name="order_tax"]').val(order_tax.toFixed(2));
    $('#shipping-cost').text(shipping_cost.toFixed(2));
    $('#grand-total').text(grand_total.toFixed(2));
    $('input[name="grand_total"]').val(grand_total.toFixed(2));
}

function hide() {
    $(".card-element").hide();
    $(".card-errors").hide();
    $(".cheque").hide();
    $(".gift-card").hide();
    $('input[name="cheque_no"]').attr('required', false);
}

function giftCard() {
    $(".gift-card").show();
    $.ajax({
        url: 'sales/get_gift_card',
        type: "GET",
        dataType: "json",
        success:function(data) {
            $('#add-payment select[name="gift_card_id_select"]').empty();
            $.each(data, function(index) {
                gift_card_amount[data[index]['id']] = data[index]['amount'];
                gift_card_expense[data[index]['id']] = data[index]['expense'];
                $('#add-payment select[name="gift_card_id_select"]').append('<option value="'+ data[index]['id'] +'">'+ data[index]['card_no'] +'</option>');
            });
            $('.selectpicker').selectpicker('refresh');
            $('.selectpicker').selectpicker();
        }
    });
    $(".card-element").hide();
    $(".card-errors").hide();
    $(".cheque").hide();
    $('input[name="cheque_no"]').attr('required', false);
}

function cheque() {
    $(".cheque").show();
    $('input[name="cheque_no"]').attr('required', true);
    $(".card-element").hide();
    $(".card-errors").hide();
    $(".gift-card").hide();
}

function creditCard() {
    $.getScript( "public/vendor/stripe/checkout.js" );
    $(".card-element").show();
    $(".card-errors").show();
    $(".cheque").hide();
    $(".gift-card").hide();
    $('input[name="cheque_no"]').attr('required', false);
}

function deposits() {
    if($('input[name="paid_amount"]').val() > deposit[$('#customer_id').val()]){
        alert('Amount exceeds customer deposit! Customer deposit : '+ deposit[$('#customer_id').val()]);
    }
    $('input[name="cheque_no"]').attr('required', false);
    $('#add-payment select[name="gift_card_id_select"]').attr('required', false);
}

function cancel(rownumber) {
    while(rownumber >= 0) {
        product_price.pop();
        product_discount.pop();
        tax_rate.pop();
        tax_name.pop();
        tax_method.pop();
        unit_name.pop();
        unit_operator.pop();
        unit_operation_value.pop();
        $('table.order-list tbody tr:last').remove();
        rownumber--;
    }
    $('input[name="shipping_cost"]').val('');
    $('input[name="order_discount"]').val('');
    $('select[name="order_tax_rate_select"]').val(0);
    calculateTotal();
}

function confirmCancel() {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    if (confirm("Are you sure want to cancel?")) {
        cancel($('table.order-list tbody tr:last').index());
    }
    return false;
}

$(document).on('submit', '.payment-form', function(e) {
    var rownumber = $('table.order-list tbody tr:last').index();
    if (rownumber < 0) {
        alert("Please insert product to order table!")
        e.preventDefault();
    }
    else if( parseFloat( $('input[name="paying_amount"]').val() ) < parseFloat( $('input[name="paid_amount"]').val() ) ){
        alert('Paying amount cannot be bigger than recieved amount');
        e.preventDefault();
    }
    $('input[name="paid_by_id"]').val($('select[name="paid_by_id_select"]').val());
    $('input[name="order_tax_rate"]').val($('select[name="order_tax_rate_select"]').val());

});

$('#product-table').DataTable( {
    "order": [],
    'pageLength': product_row_number,
     'language': {
        'paginate': {
            'previous': '<i class="fa fa-angle-left"></i>',
            'next': '<i class="fa fa-angle-right"></i>'
        }
    },
    dom: 'tp'
});
</script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layout.top-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home3/sofinvercs/public_html/demo/resources/views/sale/pos.blade.php ENDPATH**/ ?>