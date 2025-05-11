<?php

namespace App\Http\Controllers;

use App\Types_document;
use Illuminate\Validation\Rule;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class Types_documentController extends Controller
{

    public function index()
    {
        $role = Role::find(Auth::user()->role_id);                                   
        if($role->hasPermissionTo('typedocument-index')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            $lims_typesdocument_all = Types_document::get();
            
            return view('type_document.index', compact('lims_typesdocument_all', 'all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('typedocument-add')){            
            return view('type_document.create');
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lims_typedocument_data = $request->all();
        $message = 'Tipo de Documento creado Exitosamente';
        Types_document::create($lims_typedocument_data);
        return redirect('typeDocument')->with('create_message', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('typedocument-edit')){
            $lims_typedocument_data = Types_document::find($id);
            return view('type_document.edit', compact('lims_typedocument_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $message = 'Tipo de Documento Actualizado Exitosamente';
        $lims_typedocument_data = Types_document::findOrFail($id);
        $lims_typedocument_data->documento = $request->documento;
        $lims_typedocument_data->resolucion = $request->resolucion;
        $lims_typedocument_data->serie = $request->serie;
        $lims_typedocument_data->correlativo = $request->correlativo;
        $lims_typedocument_data->modulo = $request->modulo;
        $lims_typedocument_data->update();
        return redirect('typeDocument')->with('edit_message', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lims_typedocument_data = Types_document::find($id);
        $lims_typedocument_data->delete();
        return redirect('typeDocument')->with('not_permitted','Data Eliminada Exitosamente');
    }
}
