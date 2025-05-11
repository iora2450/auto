@extends('layout.main') @section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Creacion quedan</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>Cliente</small></p>
                        {!! Form::open(['route' => 'quedan.store', 'method' => 'post', 'files' => false]) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                 
                                    <select required class="form-control selectpicker" id="customer_id" name="customer_id" onchange='saveValue(this);'>
                                        <option value="">Selecciona un cliente</option>
                                        @foreach($lims_client_list  as $customer_group)
                                            @if($customer_id == $customer_group->id)
                                            <option value="{{$customer_group->id}}" selected>{{$customer_group->name}}</option>
                                            @endif
                                                   @if($customer_id != $customer_group->id)
                                            <option value="{{$customer_group->id}}" >{{$customer_group->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            

             

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Fecha de quedan*</strong> </label>
                                    <input type="date" id="date_quedan" name="date_quedan" required class="form-control" >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                  <label>Documentos  del quedan: </label>

                                    <select id='documentos' name='number_invoice[]' class="selectpicker" data-live-search="true" multiple style='width: 100%'>
                                          @foreach($datos_facturas  as $fact)
                                            <option data-monto="{{$fact->grand_total}}" value="{{$fact->id}}">{{$fact->reference_no}} - {{$fact->name}} - ${{$fact->grand_total}}</option>
                                        @endforeach
                                          
                                        </select>

                                    ,<!--<label>Numero de factura</label>
                                    <input type="text" name="number_invoice" id="number_invoice" class="form-control">
                                -->
                                </div>
                            </div>
                
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Total</label>
                                    <input type="number" name="total" id="total" class="form-control" step="any">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Fecha de vencimiento *</label>
                                    <input type="date" name="due_date" id="due_date" required class="form-control">
                                    
                                </div>
                            </div>
                          
                        <div class="form-group">
                            <input type="hidden" name="pos" value="0">
                            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

$("#customer_id").change(function(e){
   customer_id = $("#customer_id").val()
   url= "<?php echo url('');?> "
   url= url.trim();
   window.location.href = url+"/quedan/create/"+customer_id;
   
})

$("#documentos").change(function(event){

var options = $(this).find('option:selected').map(function() {
    return $(this).data('monto');
  }).get();

total=0
for (i = 0; i < options.length; ++i) {
    total+=options[i];
}
$("#total").val(total)

})



    $("ul#people").siblings('a').attr('aria-expanded','true');
    $("ul#people").addClass("show");
    $("ul#people #customer-create-menu").addClass("active");

    $(".user-input").hide();

    $('input[name="user"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('.user-input').show(300);
            $('input[name="name"]').prop('required',true);
            $('input[name="password"]').prop('required',true);
        }
        else{
            $('.user-input').hide(300);
            $('input[name="name"]').prop('required',false);
            $('input[name="password"]').prop('required',false);
        }
    });

    //$("#name").val(getSavedValue("name"));
    //$("#customer-group-id").val(getSavedValue("customer-group-id"));

    function saveValue(e) {
        var id = e.id;  // get the sender's id to save it.
        var val = e.value; // get the value.
        localStorage.setItem(id, val);// Every time user writing something, the localStorage's value will override.
    }
    //get the saved value function - return the value of "v" from localStorage. 
    function getSavedValue  (v){
        if (!localStorage.getItem(v)) {
            return "";// You can change this to your defualt value. 
        }
        return localStorage.getItem(v);
    }
</script>
@endsection