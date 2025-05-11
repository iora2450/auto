<?php

namespace App\Http\Controllers;

use App\customer_contact;
use Illuminate\Http\Request;

class CustomerContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\customer_contact  $customer_contact
     * @return \Illuminate\Http\Response
     */
    public function show(customer_contact $customer_contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\customer_contact  $customer_contact
     * @return \Illuminate\Http\Response
     */
    public function edit(customer_contact $customer_contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\customer_contact  $customer_contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customer_contact $customer_contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\customer_contact  $customer_contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(customer_contact $customer_contact)
    {
        //
    }

    public function save_contacts(Request $request){
            $lims_customer_data = $request->all();

            $user = customer_contact::create($lims_customer_data);
            $lims_customer_contact_list = customer_contact::where('customer_id', $lims_customer_data["customer_id"])->get();

           $this->list_contacts($lims_customer_data["customer_id"]);

    }

     public function list_contacts($customer_id){

            $lims_customer_contact_list = customer_contact::where('customer_id', $customer_id)->get();

            //print_r($lims_customer_contact_list);
           // exit();
           $tabla="<table class='table dataTable no-footer'>";
           $tabla.="<thead>";
           $tabla.="<tr>";
           $tabla.="<th>Telefono</th>";
           $tabla.="<th>Email</th>";
           $tabla.="<th>Descripcion</th>";
            $tabla.="<th>Acciones</th>";
           $tabla.="</tr>";
           $tabla.="</thead>";


            foreach ($lims_customer_contact_list as $contact) {
                $tabla .="<tr>";
                 $tabla.="<td>".$contact->contact_num."</td>";
                  $tabla.="<td>".$contact->email."</td>";
                  $tabla.="<td>".$contact->description."</td>";
                   $tabla.="<td>
                            <a href='' class='delete_contact btn btn-link' identificador =".$contact->id.">
                            <i class='dripicons-trash'></i> Borrar</button>
                            </a>
                           </td>";
                $tabla .="</tr>";
            }
          $tabla .="</table>";

          echo $tabla; 

    }

         public function delete_contacts($id){

        $lims_contact_data = customer_contact::find($id);
        $customer_id= $lims_contact_data->customer_id;

        
        $lims_contact_data->delete();
        
        $this->list_contacts($customer_id);


    }


}
