<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\CorreoController;


Auth::routes();

Route::group(['middleware' => 'auth'], function() {
	Route::get('/dashboard', 'HomeController@dashboard');
});

Route::group(['middleware' => ['auth', 'active']], function() {

	Route::get('/', 'HomeController@index');
	Route::get('/dashboard-filter/{start_date}/{end_date}', 'HomeController@dashboardFilter');

	Route::get('language_switch/{locale}', 'LanguageController@switchLanguage');

	Route::get('role/permission/{id}', 'RoleController@permission')->name('role.permission');
	Route::post('role/set_permission', 'RoleController@setPermission')->name('role.setPermission');
	Route::resource('role', 'RoleController');

	Route::post('importunit', 'UnitController@importUnit')->name('unit.import');
	Route::post('unit/deletebyselection', 'UnitController@deleteBySelection');
	Route::get('unit/lims_unit_search', 'UnitController@limsUnitSearch')->name('unit.search');
	Route::resource('unit', 'UnitController');

	Route::post('category/import', 'CategoryController@import')->name('category.import');
	Route::post('category/deletebyselection', 'CategoryController@deleteBySelection');
	Route::post('category/category-data', 'CategoryController@categoryData');
	Route::resource('category', 'CategoryController');

	Route::post('importbrand', 'BrandController@importBrand')->name('brand.import');
	Route::post('brand/deletebyselection', 'BrandController@deleteBySelection');
	Route::get('brand/lims_brand_search', 'BrandController@limsBrandSearch')->name('brand.search');
	Route::resource('brand', 'BrandController');

	Route::post('importsupplier', 'SupplierController@importSupplier')->name('supplier.import');
	Route::post('supplier/deletebyselection', 'SupplierController@deleteBySelection');
	Route::get('supplier/lims_supplier_search', 'SupplierController@limsSupplierSearch')->name('supplier.search');
	Route::resource('supplier', 'SupplierController');

	Route::post('importwarehouse', 'WarehouseController@importWarehouse')->name('warehouse.import');
	Route::post('warehouse/deletebyselection', 'WarehouseController@deleteBySelection');
	Route::get('warehouse/lims_warehouse_search', 'WarehouseController@limsWarehouseSearch')->name('warehouse.search');
	Route::resource('warehouse', 'WarehouseController');

	Route::post('importtax', 'TaxController@importTax')->name('tax.import');
	Route::post('tax/deletebyselection', 'TaxController@deleteBySelection');
	Route::get('tax/lims_tax_search', 'TaxController@limsTaxSearch')->name('tax.search');
	Route::resource('tax', 'TaxController');

	//Route::get('products/getbarcode', 'ProductController@getBarcode');
	Route::post('products/product-data', 'ProductController@productData');
	Route::get('products/gencode', 'ProductController@generateCode');
	Route::get('products/search', 'ProductController@search');
	Route::get('products/saleunit/{id}', 'ProductController@saleUnit');
	Route::get('products/getdata/{id}', 'ProductController@getData');
	Route::get('products/product_warehouse/{id}', 'ProductController@productWarehouseData');
	Route::post('importproduct', 'ProductController@importProduct')->name('product.import');
	Route::post('exportproduct', 'ProductController@exportProduct')->name('product.export');
	Route::get('products/print_barcode','ProductController@printBarcode')->name('product.printBarcode');
	
	Route::get('products/lims_product_search', 'ProductController@limsProductSearch')->name('product.search');
	Route::post('products/deletebyselection', 'ProductController@deleteBySelection');
	Route::post('products/update', 'ProductController@updateProduct');
	Route::resource('products', 'ProductController');

	Route::post('importcustomer_group', 'CustomerGroupController@importCustomerGroup')->name('customer_group.import');
	Route::post('customer_group/deletebyselection', 'CustomerGroupController@deleteBySelection');
	Route::get('customer_group/lims_customer_group_search', 'CustomerGroupController@limsCustomerGroupSearch')->name('customer_group.search');
	Route::resource('customer_group', 'CustomerGroupController');

	Route::post('importcustomer', 'CustomerController@importCustomer')->name('customer.import');
	Route::get('customer/getDeposit/{id}', 'CustomerController@getDeposit');
	Route::post('customer/add_deposit', 'CustomerController@addDeposit')->name('customer.addDeposit');
	Route::post('customer/update_deposit', 'CustomerController@updateDeposit')->name('customer.updateDeposit');
	Route::post('customer/deleteDeposit', 'CustomerController@deleteDeposit')->name('customer.deleteDeposit');
	Route::post('customer/deletebyselection', 'CustomerController@deleteBySelection');
	Route::get('customer/lims_customer_search', 'CustomerController@limsCustomerSearch')->name('customer.search');
	Route::resource('customer', 'CustomerController');
    
    Route::resource('typeDocument', 'Types_documentController'); 

    Route::resource('CustomerContact', 'CustomerContactController');
    Route::post('CustomerContact/save_contacts', 'CustomerContactController@save_contacts');
    Route::post('CustomerContact/save_contacts', 'CustomerContactController@save_contacts')->name('CustomerContact.save_contacts');
     Route::get('CustomerContact/list_contacts/{customer_id}', 'CustomerContactController@list_contacts');
    Route::get('CustomerContact/delete_contacts/{id}', 'CustomerContactController@delete_contacts');
	Route::get('/sales/date-range', 'SaleController@salesByDate')->name('sales.byDate');

	
	Route::post('importbiller', 'BillerController@importBiller')->name('biller.import');
	Route::post('biller/deletebyselection', 'BillerController@deleteBySelection');
	Route::get('biller/lims_biller_search', 'BillerController@limsBillerSearch')->name('biller.search');
	Route::resource('biller', 'BillerController');
	Route::get('/sales/data', [SaleController::class, 'saleData'])->name('sales.saleData');

	Route::post('sales/sale-data', 'SaleController@saleData');
	Route::post('sales/sendmail', 'SaleController@sendMail')->name('sale.sendmail');
	Route::get('sales/sale_by_csv', 'SaleController@saleByCsv');
	Route::get('sales/product_sale/{id}','SaleController@productSaleData');
	Route::post('importsale', 'SaleController@importSale')->name('sale.import');
	Route::get('pos', 'SaleController@posSale')->name('sale.pos');
	Route::get('sales/lims_sale_search', 'SaleController@limsSaleSearch')->name('sale.search');
	Route::get('sales/lims_product_search', 'SaleController@limsProductSearch')->name('product_sale.search');
	Route::get('sales/getcustomergroup/{id}', 'SaleController@getCustomerGroup')->name('sale.getcustomergroup');
	Route::get('sales/getproduct/{id}', 'SaleController@getProduct')->name('sale.getproduct');
	Route::get('sales/getproduct/{category_id}/{brand_id}', 'SaleController@getProductByFilter');
	Route::get('sales/getfeatured', 'SaleController@getFeatured');
	Route::get('sales/get_gift_card', 'SaleController@getGiftCard');
	Route::get('sales/paypalSuccess', 'SaleController@paypalSuccess');
	Route::get('sales/paypalPaymentSuccess/{id}', 'SaleController@paypalPaymentSuccess');
	Route::get('sales/gen_invoice/{id}', 'SaleController@genInvoice_pdf')->name('sale.invoice');
	Route::get('sales/search_invoices', 'SaleController@searchInvoices')->name('sale.search_invoices');
	Route::get('sales/reporte', 'SaleController@report')->name('sales.report');
    Route::get('sales/libro', 'SaleController@libro')->name('sales.libro');
    Route::get('sales/excel', 'SaleController@excel')->name('sales.excel');
	Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');

    Route::get('sales/pdf', 'SaleController@ExportacionPdf')->name('sales.pdfreport');
    Route::get('sales/pdfFac', 'SaleController@ExportacionPdfFac')->name('sales.pdfreportFac');
    Route::get('/sales/download-json-ccf', 'SaleController@downloadJsonccfByDateRange')->name('sales.download-json-ccf');
    Route::get('/sales/download-json-fac', 'SaleController@downloadJsonfacByDateRange')->name('sales.download-json-fac');
	
	/*CCF*/
	Route::get('sales/gen_invoice_ccf/{id}', 'SaleController@genInvoice_ccf')->name('sale.invoice_ccf');
	Route::get('sales/gen_invoice_export/{id}', 'SaleController@genInvoice_export')->name('sale.invoice_export');
	Route::get('sales/anular/{id}', 'SaleController@anular')->name('sale.anular');
    Route::get('sales/anulardte/{id}', 'SaleController@showAnulardte')->name('sale.anulardte');
    Route::post('sales/anularDTEMH/{id}', 'SaleController@anularDTEMH')->name('sale.anulardtemh');

	Route::get('sales/aplicar_costos/{id}', 'SaleController@aplicar_costos')->name('sale.aplicar_costos');
	Route::get('sales/recalculoKardex/{id}', 'SaleController@recalculoKardex')->name('sale.recalculoKardex');


	Route::post('sales/add_payment', 'SaleController@addPayment')->name('sale.add-payment');
	Route::get('sales/getpayment/{id}', 'SaleController@getPayment')->name('sale.get-payment');
	Route::post('sales/updatepayment', 'SaleController@updatePayment')->name('sale.update-payment');
	Route::post('sales/deletepayment', 'SaleController@deletePayment')->name('sale.delete-payment');
	Route::get('sales/{id}/create', 'SaleController@createSale');
	Route::post('sales/deletebyselection', 'SaleController@deleteBySelection');
	Route::get('sales/print-last-reciept', 'SaleController@printLastReciept')->name('sales.printLastReciept');
	Route::get('sales/today-sale', 'SaleController@todaySale');
	Route::get('sales/today-profit/{warehouse_id}', 'SaleController@todayProfit');
	Route::resource('sales', 'SaleController');
    Route::post('sales/verdte', 'SaleController@getDteJson');
	Route::post('sales/reenviardte', 'SaleController@envioJson');
	
	Route::get('/export-documents', 'SaleController@Exportacion_a_pdf_genInvoice_ccf')->name('sales.pdfdownload');


	Route::get('delivery', 'DeliveryController@index')->name('delivery.index');
	Route::get('delivery/product_delivery/{id}','DeliveryController@productDeliveryData');
	Route::get('delivery/create/{id}', 'DeliveryController@create');
	Route::post('delivery/store', 'DeliveryController@store')->name('delivery.store');
	Route::post('delivery/sendmail', 'DeliveryController@sendMail')->name('delivery.sendMail');
	Route::get('delivery/{id}/edit', 'DeliveryController@edit');
	Route::post('delivery/update', 'DeliveryController@update')->name('delivery.update');
	Route::post('delivery/deletebyselection', 'DeliveryController@deleteBySelection');
	Route::post('delivery/delete/{id}', 'DeliveryController@delete')->name('delivery.delete');

	Route::get('quotations/product_quotation/{id}','QuotationController@productQuotationData');
	Route::get('quotations/lims_product_search', 'QuotationController@limsProductSearch')->name('product_quotation.search');
	Route::get('quotations/getcustomergroup/{id}', 'QuotationController@getCustomerGroup')->name('quotation.getcustomergroup');
	Route::get('quotations/getproduct/{id}', 'QuotationController@getProduct')->name('quotation.getproduct');
	Route::get('quotations/{id}/create_sale', 'QuotationController@createSale')->name('quotation.create_sale');
	Route::get('quotations/{id}/create_purchase', 'QuotationController@createPurchase')->name('quotation.create_purchase');
	Route::post('quotations/sendmail', 'QuotationController@sendMail')->name('quotation.sendmail');
	Route::post('quotations/deletebyselection', 'QuotationController@deleteBySelection');
	Route::resource('quotations', 'QuotationController');

	Route::post('purchases/purchase-data', 'PurchaseController@purchaseData');
	Route::get('purchases/product_purchase/{id}','PurchaseController@productPurchaseData');
	Route::get('purchases/lims_product_search', 'PurchaseController@limsProductSearch')->name('product_purchase.search');
	Route::post('purchases/add_payment', 'PurchaseController@addPayment')->name('purchase.add-payment');
	Route::get('purchases/getpayment/{id}', 'PurchaseController@getPayment')->name('purchase.get-payment');
	Route::post('purchases/updatepayment', 'PurchaseController@updatePayment')->name('purchase.update-payment');
	Route::post('purchases/deletepayment', 'PurchaseController@deletePayment')->name('purchase.delete-payment');
	Route::get('purchases/purchase_by_csv', 'PurchaseController@purchaseByCsv');
	Route::post('importpurchase', 'PurchaseController@importPurchase')->name('purchase.import');
	Route::post('purchases/deletebyselection', 'PurchaseController@deleteBySelection');
	Route::resource('purchases', 'PurchaseController');
    Route::get('purchases/info_purchase/{id_purchase}', 'PurchaseController@info_purchase')->name('purchase.info_purchase');
    Route::post('purchases/save_purchase_dates', 'PurchaseController@save_purchase_dates');
    Route::get('purchases/download/{id}', 'PurchaseController@download')->name('purchases.download');
   	Route::get('purchases/{id}/document', 'PurchaseController@showDocument')->name('purchases.document');

	Route::get('transfers/product_transfer/{id}','TransferController@productTransferData');
	Route::get('transfers/transfer_by_csv', 'TransferController@transferByCsv');
	Route::post('importtransfer', 'TransferController@importTransfer')->name('transfer.import');
	Route::get('transfers/getproduct/{id}', 'TransferController@getProduct')->name('transfer.getproduct');
	Route::get('transfers/lims_product_search', 'TransferController@limsProductSearch')->name('product_transfer.search');
	Route::post('transfers/deletebyselection', 'TransferController@deleteBySelection');
	Route::resource('transfers', 'TransferController');

	Route::get('qty_adjustment/getproduct/{id}', 'AdjustmentController@getProduct')->name('adjustment.getproduct');
	Route::get('qty_adjustment/lims_product_search', 'AdjustmentController@limsProductSearch')->name('product_adjustment.search');
	Route::post('qty_adjustment/deletebyselection', 'AdjustmentController@deleteBySelection');
	Route::resource('qty_adjustment', 'AdjustmentController');
	Route::get('adjustment/gen_adjustment/{id}', 'AdjustmentController@genAdjustment')->name('adjustment.invoice');

	Route::get('return-sale/getcustomergroup/{id}', 'ReturnController@getCustomerGroup')->name('return-sale.getcustomergroup');
	Route::post('return-sale/sendmail', 'ReturnController@sendMail')->name('return-sale.sendmail');
	Route::get('return-sale/getproduct/{id}', 'ReturnController@getProduct')->name('return-sale.getproduct');
	Route::get('return-sale/lims_product_search', 'ReturnController@limsProductSearch')->name('product_return-sale.search');
	Route::get('return-sale/product_return/{id}','ReturnController@productReturnData');
	Route::post('return-sale/deletebyselection', 'ReturnController@deleteBySelection');
	Route::resource('return-sale', 'ReturnController');
	Route::post('return-sale/verdte', 'ReturnController@getDteJson');
	Route::post('return-sale/reenviardte', 'ReturnController@envioJson');

	
	Route::get('return-sale/genReturn_nc/{id}', 'ReturnController@genReturn_nc')->name('return-sale.invoice_nc');

	Route::get('return-purchase/getcustomergroup/{id}', 'ReturnPurchaseController@getCustomerGroup')->name('return-purchase.getcustomergroup');
	Route::post('return-purchase/sendmail', 'ReturnPurchaseController@sendMail')->name('return-purchase.sendmail');
	Route::get('return-purchase/getproduct/{id}', 'ReturnPurchaseController@getProduct')->name('return-purchase.getproduct');
	Route::get('return-purchase/lims_product_search', 'ReturnPurchaseController@limsProductSearch')->name('product_return-purchase.search');
	Route::get('return-purchase/product_return/{id}','ReturnPurchaseController@productReturnData');
	Route::post('return-purchase/deletebyselection', 'ReturnPurchaseController@deleteBySelection');
	Route::resource('return-purchase', 'ReturnPurchaseController');
    Route::resource('return-sale', 'ReturnController');
	Route::get('return-sale/genReturn_nc/{id}', 'ReturnController@genReturn_nc')->name('return-sale.invoice_nc');
    Route::get('return-purchase/download/{id}', 'ReturnPurchaseController@download')->name('return-purchase.download');
   	Route::get('return-purchase/{id}/document', 'ReturnPurchaseController@showDocument')->name('return-purchase.document');

	Route::post('importexcluded', 'ExcludeController@importCustomer')->name('excluded.import');
	Route::resource('excluded', 'ExcludedController');		
	Route::get('excluded/stateunit/{id}', 'ExcludedController@stateunit');

	Route::post('sexcluded/sexcluded-data', 'SexcludedController@saleData');
	Route::post('sexcluded/sexcluded-data', 'SexcludedController@purchaseData');
	Route::get('sexcluded/product_sexcluded/{id}','SexcludedController@productPurchaseData');
	Route::get('sexcluded/lims_product_search', 'SexcludedController@limsProductSearch')->name('product_sexcluded.search');
	
	Route::get('sexcluded/getproduct/{id}', 'SexcludedController@getProduct')->name('sexcluded.getproduct');
	Route::get('sexcluded/gen_invoice_ccf/{id}', 'SexcludedController@genInvoice_ccf')->name('sexcluded.invoice_ccf');
	Route::post('sexcluded/verdte', 'SexcludedController@getDteJson');
	Route::post('sexcluded/reenviardte', 'SexcludedController@envioJson');
    Route::post('sexcluded/dwdte', 'SexcludedController@dowjson');
    Route::get('sexcluded/reporte', 'SexcludedController@report')->name('sexcluded.report');
    Route::get('sexcluded/excel', 'SexcludedController@excel')->name('sexcluded.excel');	
    Route::resource('sexcluded', 'SexcludedController');
    Route::post('sales/dwdte', 'SaleController@dowjson');


    Route::resource('retention', 'RetentionController');		
	Route::get('retention/create/{supplier_id}', 'RetentionController@create');	
	Route::get('Retention/get_info_pos/{document_id}', 'RetentionController@get_info_pos')->name('retention.get_info_pos');
	Route::get('retention/gen_invoice/{id}', 'RetentionController@genInvoice')->name('retention.invoice');
	Route::post('retention/verdte', 'RetentionController@getDteJson');
	Route::post('retention/reenviardte', 'RetentionController@envioJson');
	Route::post('retention/verdte', 'RetentionController@getDteJson');
	Route::post('retention/dwdte', 'RetentionController@dowjson');
	Route::get('retention/efretention/{id}', 'RetentionController@genRetention')->name('retention.invoice');

	

	Route::get('report/product_quantity_alert', 'ReportController@productQuantityAlert')->name('report.qtyAlert');
	Route::get('report/warehouse_stock', 'ReportController@warehouseStock')->name('report.warehouseStock');
	Route::post('report/warehouse_stock', 'ReportController@warehouseStockById')->name('report.warehouseStock');
	Route::get('report/daily_sale/{year}/{month}', 'ReportController@dailySale');
	Route::post('report/daily_sale/{year}/{month}', 'ReportController@dailySaleByWarehouse')->name('report.dailySaleByWarehouse');
	Route::get('report/monthly_sale/{year}', 'ReportController@monthlySale');
	Route::post('report/monthly_sale/{year}', 'ReportController@monthlySaleByWarehouse')->name('report.monthlySaleByWarehouse');
	Route::get('report/daily_purchase/{year}/{month}', 'ReportController@dailyPurchase');
	Route::post('report/daily_purchase/{year}/{month}', 'ReportController@dailyPurchaseByWarehouse')->name('report.dailyPurchaseByWarehouse');
	Route::get('report/monthly_purchase/{year}', 'ReportController@monthlyPurchase');
	Route::post('report/monthly_purchase/{year}', 'ReportController@monthlyPurchaseByWarehouse')->name('report.monthlyPurchaseByWarehouse');
	Route::get('report/best_seller', 'ReportController@bestSeller');

	Route::get('report/SalesGraph', 'ReportController@SalesGraph')->name('report.SalesGraph');
	Route::get('report/OrdersGraph', 'ReportController@OrdersGraph')->name('report.OrdersGraph');
	
	Route::post('report/best_seller', 'ReportController@bestSellerByWarehouse')->name('report.bestSellerByWarehouse');
	Route::post('report/profit_loss', 'ReportController@profitLoss')->name('report.profitLoss');
	Route::post('report/box_cut_report', 'ReportController@boxCutReport')->name('report.boxCutReport');
	Route::post('report/product_report', 'ReportController@productReport')->name('report.product');
	Route::post('report/purchase', 'ReportController@purchaseReport')->name('report.purchase');
	Route::post('report/sale_report', 'ReportController@saleReport')->name('report.sale');
	Route::post('report/payment_report_by_date', 'ReportController@paymentReportByDate')->name('report.paymentByDate');
	Route::post('report/warehouse_report', 'ReportController@warehouseReport')->name('report.warehouse');
	Route::post('report/user_report', 'ReportController@userReport')->name('report.user');
	Route::post('report/customer_report', 'ReportController@customerReport')->name('report.customer');
	Route::post('report/supplier', 'ReportController@supplierReport')->name('report.supplier');
	Route::post('report/due_report_by_date', 'ReportController@dueReportByDate')->name('report.dueByDate');
    Route::get('report/existence_report', 'ReportController@existenceReport')->name('report.existence');
    Route::get('report/cost_per_day_report', 'ReportController@costperdayReport')->name('report.costperday');
    Route::get('report/kardexReport', 'ReportController@kardexReport')->name('report.kardexReport');
    Route::get('report/warehouse_stock_report_existence', 'ReportController@warehouseStockReport')->name('report.warehouseStockReport');

    Route::get('report/cost_per_seller_report', 'ReportController@costpersellerReport')->name('report.costperseller');

    Route::get('report/sale_report_daily', 'ReportController@SalesdayReport')->name('report.SalesdayReport');
    Route::get('report/purchase_report_daily', 'ReportController@PurchasesdayReport')->name('report.PurchasesdayReport');
    
	Route::get('user/profile/{id}', 'UserController@profile')->name('user.profile');
	Route::put('user/update_profile/{id}', 'UserController@profileUpdate')->name('user.profileUpdate');
	Route::put('user/changepass/{id}', 'UserController@changePassword')->name('user.password');
	Route::get('user/genpass', 'UserController@generatePassword');
	Route::post('user/deletebyselection', 'UserController@deleteBySelection');
	Route::resource('user','UserController');

	Route::get('setting/general_setting', 'SettingController@generalSetting')->name('setting.general');
	Route::post('setting/general_setting_store', 'SettingController@generalSettingStore')->name('setting.generalStore');
	Route::get('backup', 'SettingController@backup')->name('setting.backup');
	Route::get('setting/general_setting/change-theme/{theme}', 'SettingController@changeTheme');
	Route::get('setting/mail_setting', 'SettingController@mailSetting')->name('setting.mail');
	Route::get('setting/sms_setting', 'SettingController@smsSetting')->name('setting.sms');
	Route::get('setting/createsms', 'SettingController@createSms')->name('setting.createSms');
	Route::post('setting/sendsms', 'SettingController@sendSms')->name('setting.sendSms');
	Route::get('setting/hrm_setting', 'SettingController@hrmSetting')->name('setting.hrm');
	Route::post('setting/hrm_setting_store', 'SettingController@hrmSettingStore')->name('setting.hrmStore');
	Route::post('setting/mail_setting_store', 'SettingController@mailSettingStore')->name('setting.mailStore');
	Route::post('setting/sms_setting_store', 'SettingController@smsSettingStore')->name('setting.smsStore');
	Route::get('setting/pos_setting', 'SettingController@posSetting')->name('setting.pos');
	Route::post('setting/pos_setting_store', 'SettingController@posSettingStore')->name('setting.posStore');
	Route::get('setting/empty-database', 'SettingController@emptyDatabase')->name('setting.emptyDatabase');

	Route::get('expense_categories/gencode', 'ExpenseCategoryController@generateCode');
	Route::post('expense_categories/import', 'ExpenseCategoryController@import')->name('expense_category.import');
	Route::post('expense_categories/deletebyselection', 'ExpenseCategoryController@deleteBySelection');
	Route::resource('expense_categories', 'ExpenseCategoryController');

	Route::post('expenses/deletebyselection', 'ExpenseController@deleteBySelection');
	Route::resource('expenses', 'ExpenseController');

	Route::get('boxcuts/getproduct/{id}', 'BoxeController@getProduct')->name('boxe.getproduct');
	Route::get('boxcuts/lims_product_search', 'BoxeController@limsProductSearch')->name('boxe.search');
	Route::resource('boxcuts', 'BoxeController');
	Route::resource('tickets', 'TicketController');	

	Route::get('gift_cards/gencode', 'GiftCardController@generateCode');
	Route::post('gift_cards/recharge/{id}', 'GiftCardController@recharge')->name('gift_cards.recharge');
	Route::post('gift_cards/deletebyselection', 'GiftCardController@deleteBySelection');
	Route::resource('gift_cards', 'GiftCardController');

	Route::get('coupons/gencode', 'CouponController@generateCode');
	Route::post('coupons/deletebyselection', 'CouponController@deleteBySelection');
	Route::resource('coupons', 'CouponController');
	//accounting routes
	Route::get('accounts/make-default/{id}', 'AccountsController@makeDefault');
	Route::get('accounts/balancesheet', 'AccountsController@balanceSheet')->name('accounts.balancesheet');
	Route::post('accounts/account-statement', 'AccountsController@accountStatement')->name('accounts.statement');
	Route::resource('accounts', 'AccountsController');
	Route::resource('money-transfers', 'MoneyTransferController');
	//HRM routes
	Route::post('departments/deletebyselection', 'DepartmentController@deleteBySelection');
	Route::resource('departments', 'DepartmentController');

	Route::post('employees/deletebyselection', 'EmployeeController@deleteBySelection');
	Route::resource('employees', 'EmployeeController');

	Route::post('payroll/deletebyselection', 'PayrollController@deleteBySelection');
	Route::resource('payroll', 'PayrollController');

	Route::post('attendance/deletebyselection', 'AttendanceController@deleteBySelection');
	Route::resource('attendance', 'AttendanceController');

	Route::resource('stock-count', 'StockCountController');
	Route::post('stock-count/finalize', 'StockCountController@finalize')->name('stock-count.finalize');
	Route::get('stock-count/stockdif/{id}', 'StockCountController@stockDif');
	Route::get('stock-count/{id}/qty_adjustment', 'StockCountController@qtyAdjustment')->name('stock-count.adjustment');

	Route::post('holidays/deletebyselection', 'HolidayController@deleteBySelection');
	Route::get('approve-holiday/{id}', 'HolidayController@approveHoliday')->name('approveHoliday');
	Route::get('holidays/my-holiday/{year}/{month}', 'HolidayController@myHoliday')->name('myHoliday');
	Route::resource('holidays', 'HolidayController');

	Route::get('cash-register', 'CashRegisterController@index')->name('cashRegister.index');
	Route::get('cash-register/check-availability/{warehouse_id}', 'CashRegisterController@checkAvailability')->name('cashRegister.checkAvailability');
	Route::post('cash-register/store', 'CashRegisterController@store')->name('cashRegister.store');
	Route::get('cash-register/getDetails/{id}', 'CashRegisterController@getDetails');
	Route::get('cash-register/showDetails/{warehouse_id}', 'CashRegisterController@showDetails');
	Route::post('cash-register/close', 'CashRegisterController@close')->name('cashRegister.close');
    Route::get('Sale/get_info_pos/{document_id}', 'SaleController@get_info_pos')->name('sale.get_info_pos');


	Route::post('notifications/store', 'NotificationController@store')->name('notifications.store');
	Route::get('notifications/mark-as-read', 'NotificationController@markAsRead');

	Route::resource('currency', 'CurrencyController');

	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('my-transactions/{year}/{month}', 'HomeController@myTransaction');

	Route::resource('quedan', 'QuedanController');
	Route::get('quedan/create/{customer_id}', 'QuedanController@create');
	Route::post('quedan/update_quedan', 'QuedanController@update_quedan');
	Route::post('quedan/buscar_facturas', 'QuedanController@buscar_facturas');
	Route::post('quedan/obtener_facturas', 'QuedanController@obtener_facturas');
	
	Route::resource('quedan_purchase', 'QuedanPurchaseController');
	Route::get('quedan_purchase/create/{supplier_id}', 'QuedanPurchaseController@create');	
	Route::post('quedan_purchase/update_quedan_purchase', 'QuedanPurchase@update_quedan_purchase');
	Route::post('quedan_purchase/buscar_facturas_purchase', 'QuedanPurchase@buscar_facturas_purchase');
	Route::post('quedan_purchase/obtener_facturas_purchsase', 'QuedanPurchase@obtener_facturas_purchase');
	Route::get('quedan_purchase/product_return/{id}','QuedanPurchase@productReturnData');
	Route::get('quedan_purchase/genQuedan/{id}', 'QuedanPurchaseController@genQuedan');
    Route::get('/probar-correo', [CorreoController::class, 'probarEnvioCorreo']);
});

