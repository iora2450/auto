$(document).ready(function () {

    'use strict';
    var brandPrimary;
    var brandPrimaryRgba;
    var brandPrimaryRgbaAnterior;

    // ------------------------------------------------------- //
    // Line Chart
    // ------------------------------------------------------ //
    var CASHFLOW = $('#cashFlow');
    if (CASHFLOW.length > 0) {
        var recieved = CASHFLOW.data('recieved');
        brandPrimary = CASHFLOW.data('color');
        brandPrimaryRgba = CASHFLOW.data('color_rgba');
        var sent = CASHFLOW.data('sent');
        var month = CASHFLOW.data('month');
        var label1 = CASHFLOW.data('label1');
        var label2 = CASHFLOW.data('label2');
        var cashFlow_chart = new Chart(CASHFLOW, {
            type: 'line',
            data: {
                labels: [ month[0], month[1], month[2], month[3], month[4],
                 month[5], month[6] ],
                datasets: [
                    {
                        label: label1,
                        fill: true,
                        lineTension: 0.3,
                        backgroundColor: 'transparent',
                        borderColor: brandPrimary,
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        borderWidth: 3,
                        pointBorderColor: brandPrimary,
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 5,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: brandPrimary,
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: [recieved[0], recieved[1], recieved[2], recieved[3],
                         recieved[4], recieved[5], recieved[6] ],
                        spanGaps: false
                    },
                    {
                        label: label2,
                        fill: true,
                        lineTension: 0.3,
                        backgroundColor: 'transparent',
                        borderColor: "rgba(255, 137, 82, 1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        borderWidth: 3,
                        pointBorderColor: "#ff8952",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 5,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "#ff8952",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: [sent[0], sent[1], sent[2], sent[3],
                         sent[4], sent[5], sent[6] ],
                        spanGaps: false
                    }
                ]
            }
        });
    };

    var SALECHART    = $('#saleChart');

    if (SALECHART.length > 0) {
        var yearly_sale_amount = SALECHART.data('sale_chart_value');
        var yearly_purchase_amount = SALECHART.data('purchase_chart_value');
        var label1 = SALECHART.data('label1');
        var label2 = SALECHART.data('label2');
        var saleChart = new Chart(SALECHART, {
            type: 'bar',
            data: {
                labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                datasets: [
                    {
                        label: label1,
                        backgroundColor: [
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba
                        ],
                        borderColor: [
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary
                        ],
                        borderWidth: 1,
                        data: [ yearly_purchase_amount[0], yearly_purchase_amount[1],
                                yearly_purchase_amount[2], yearly_purchase_amount[3],
                                yearly_purchase_amount[4], yearly_purchase_amount[5],
                                yearly_purchase_amount[6], yearly_purchase_amount[7],
                                yearly_purchase_amount[8], yearly_purchase_amount[9],
                                yearly_purchase_amount[10], yearly_purchase_amount[11],
                                0],
                    },
                    {
                        label: label2,
                        backgroundColor: [
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)'
                        ],
                        borderColor: [
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)',
                            'rgba(255, 137, 82, 1)'
                        ],
                        borderWidth: 1,
                        data: [ yearly_sale_amount[0], yearly_sale_amount[1],
                                yearly_sale_amount[2], yearly_sale_amount[3],
                                yearly_sale_amount[4], yearly_sale_amount[5],
                                yearly_sale_amount[6], yearly_sale_amount[7],
                                yearly_sale_amount[8], yearly_sale_amount[9],
                                yearly_sale_amount[10], yearly_sale_amount[11],
                                0],
                    },
                ]
            }
        });
    };


 

   var SALES    = $('#salesGraph');
   //alert(SALES.length)

    if (SALES.length > 0) {
        var sold_venta = SALES.data('sold_venta');
        var sold_venta_anterior = SALES.data('sold_venta_anterior');
        var sold_venta_diferencia= SALES.data('sold_venta_diferencia');

        brandPrimary = SALES.data('color');

        brandPrimaryRgba = SALES.data('color_rgba');
        brandPrimaryRgbaAnterior = SALES.data('color_rgba_anterior');
        var brandPrimaryRgbaDiferencia = SALES.data('color_rgba_diferencia');
        var product_info = SALES.data('product');
        var bestSeller = new Chart(SALES, {
            type: 'bar',
            data: {
                labels: [ product_info[0], 
                product_info[1], 
                product_info[2],
                product_info[3],
                product_info[4],
                product_info[5],
                product_info[6],
                product_info[7],
                product_info[8],
                product_info[9],
                product_info[10],
                product_info[11]

                ],
                datasets: [
/*
                     {
                        label: "Sale Qty",
                    
                        
                        
                        data: [ 
                                sold_qty[0], sold_qty[1],
                                sold_qty[2], 0],
                                 type: 'line'
                    }

,
*/
                    {
                        label: "Año actual",
                        backgroundColor: [
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba
                        ],
                        borderColor: [
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary
                        ],
                        borderWidth: 1,
                        data: [ 
                                sold_venta[0], 
                                sold_venta[1],
                                sold_venta[2],
                                sold_venta[3],
                                sold_venta[4],
                                sold_venta[5],
                                sold_venta[6],
                                sold_venta[7],
                                sold_venta[8],
                                sold_venta[9],
                                sold_venta[10],
                                sold_venta[11], 
                                0],

                    },
                /*Dataset anio anterior*/
                 {
                        label: "Año anterior",
                        backgroundColor: [
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior,
                            brandPrimaryRgbaAnterior
                        ],
                        borderColor: [
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary
                        ],
                        borderWidth: 1,
                        data: [ 
                                sold_venta_anterior[0], 
                                sold_venta_anterior[1],
                                sold_venta_anterior[2],
                                sold_venta_anterior[3],
                                sold_venta_anterior[4],
                                sold_venta_anterior[5],
                                sold_venta_anterior[6],
                                sold_venta_anterior[7],
                                sold_venta_anterior[8],
                                sold_venta_anterior[9],
                                sold_venta_anterior[10],
                                sold_venta_anterior[11], 
                                0],

                    
                    }
             ,
              /*Dataset con la diferencia*/
                 {
                        label: "Diferencia",
                        
                        borderColor: [
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary
                        ],
                        borderWidth: 0,
                        data: [ 
                                sold_venta_diferencia[0], 
                                sold_venta_diferencia[1],
                                sold_venta_diferencia[2],
                                sold_venta_diferencia[3],
                                sold_venta_diferencia[4],
                                sold_venta_diferencia[5],
                                sold_venta_diferencia[6],
                                sold_venta_diferencia[7],
                                sold_venta_diferencia[8],
                                sold_venta_diferencia[9],
                                sold_venta_diferencia[10],
                                sold_venta_diferencia[11], 
                                0],

                    type:"line"
                    }

                    

                    

                ]
            }
        });
    };


    var BESTSELLER    = $('#bestSeller');

    if (BESTSELLER.length > 0) {
        var sold_qty = BESTSELLER.data('sold_qty');
        brandPrimary = BESTSELLER.data('color');
        brandPrimaryRgba = BESTSELLER.data('color_rgba');
        var product_info = BESTSELLER.data('product');
        var bestSeller = new Chart(BESTSELLER, {
            type: 'bar',
            data: {
                labels: [ product_info[0], product_info[1], product_info[2]],
                datasets: [
                    {
                        label: "Sale Qty",
                        backgroundColor: [
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba,
                            brandPrimaryRgba
                        ],
                        borderColor: [
                            brandPrimary,
                            brandPrimary,
                            brandPrimary,
                            brandPrimary
                        ],
                        borderWidth: 1,
                        data: [ 
                                sold_qty[0], sold_qty[1],
                                sold_qty[2], 0],
                    }
                ]
            }
        });
    };

    var PIECHART = $('#pieChart');
    if (PIECHART.length > 0) {
        var brandPrimary = PIECHART.data('color');
        var brandPrimaryRgba = PIECHART.data('color_rgba');
        var price = PIECHART.data('price');
        var cost = PIECHART.data('cost');
        var label1 = PIECHART.data('label1');
        var label2 = PIECHART.data('label2');
        var label3 = PIECHART.data('label3');
        var myPieChart = new Chart(PIECHART, {
            type: 'pie',
            data: {
                labels: [
                    label1,
                    label2,
                    label3
                ],
                datasets: [
                    {
                        data: [price, cost, price-cost],
                        borderWidth: [1, 1, 1],
                        backgroundColor: [
                            brandPrimary,
                            "#ff8952",
                            "#858c85"
                        ],
                        hoverBackgroundColor: [
                            brandPrimaryRgba,
                            "rgba(255, 137, 82, 0.8)",
                            "rgb(133, 140, 133, 0.8)"
                        ],
                        hoverBorderWidth: [4, 4, 4],
                        hoverBorderColor: [
                            brandPrimaryRgba,
                            "rgba(255, 137, 82, 0.8)",
                            "rgb(133, 140, 133, 0.8)",
                            
                        ],
                    }]
            },
            options: {
                //rotation: -0.7*Math.PI
            }
        });
    }

    var TRANSACTIONCHART = $('#transactionChart');
    if (TRANSACTIONCHART.length > 0) {
        brandPrimary = TRANSACTIONCHART.data('color');
        brandPrimaryRgba = TRANSACTIONCHART.data('color_rgba');
        var revenue = TRANSACTIONCHART.data('revenue');
        var purchase = TRANSACTIONCHART.data('purchase');
        var expense = TRANSACTIONCHART.data('expense');
        var label1 = TRANSACTIONCHART.data('label1');
        var label2 = TRANSACTIONCHART.data('label2');
        var label3 = TRANSACTIONCHART.data('label3');
        var myTransactionChart = new Chart(TRANSACTIONCHART, {
            type: 'doughnut',
            data: {
                labels: [
                    label1,
                    label2,
                    label3
                ],
                datasets: [
                    {
                        data: [purchase, revenue, expense],
                        borderWidth: [1, 1, 1],
                        backgroundColor: [
                            brandPrimary,
                            "#ff8952",
                            "#858c85",
                            
                        ],
                        hoverBackgroundColor: [
                            brandPrimaryRgba,
                            "rgba(255, 137, 82, 0.8)",
                            "rgb(133, 140, 133, 0.8)",
                            
                        ],
                       hoverBorderWidth: [4, 4, 4],
                       hoverBorderColor: [
                            brandPrimaryRgba,
                            "rgba(255, 137, 82, 0.8)",
                            "rgb(133, 140, 133, 0.8)",
                            
                        ],
                    }]
            }
        });
    }
});
