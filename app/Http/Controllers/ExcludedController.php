<?php

namespace App\Http\Controllers;

use App\Excluded;
use App\Warehouse;
use Auth;
use App\Country;
use App\State;
use App\Municipality;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExcludedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('excludes-index')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            $lims_excluded_all = Excluded::where('is_active', true)->get();
            return view('excluded.index', compact('lims_excluded_all', 'all_permission'));
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
        if($role->hasPermissionTo('excludes-add')){

            $lims_customer_country = Country::all(); 
            $lims_supplier_state = State::all();
            $lims_supplier_municipalities = Municipality::with("state")->get();
            
            return view('excluded.create', compact('lims_customer_country','lims_supplier_state','lims_supplier_municipalities'));
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
        $lims_customer_data = $request->all();

        $this->validate($request, [     
            'email' => [
                'email',
                'max:255',
                    Rule::unique('excludeds')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        $excluded = new Excluded();
        $excluded->name = $request->name;
        $excluded->dui = $request->dui;
        $excluded->nit = $request->nit;
        $excluded->address = $request->address;
        $excluded->phone = $request->phone;
        $excluded->user_id = Auth::id();
        $excluded->email = $request->email;
        $excluded->state_id = $request->state_id;
        $excluded->municipality_id = $request->municipality_id;
        $excluded->is_active = true;
        $excluded->save();
        
        $message = 'Data inserted successfully';
        
        return redirect('excluded')->with('message', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Excluded  $excluded
     * @return \Illuminate\Http\Response
     */
    public function show(Excluded $excluded)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Excluded  $excluded
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('excludes-edit')) {
            $lims_excluded_data = Excluded::where('id',$id)->first();            
            $lims_excluded_state = State::all();
            $lims_excluded_municipality = Municipality::all();

            return view('excluded.edit',compact('lims_excluded_data','lims_excluded_state','lims_excluded_municipality'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Excluded  $excluded
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$lims_excluded_data = $request->all();
        //dd($lims_excluded_data);

        $this->validate($request, [
  
            'email' => [
                'email',
                'max:255',
                    Rule::unique('excludeds')->ignore($id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        $excluded = Excluded::findOrFail($id);
    
        $excluded->name = $request->name;
        $excluded->dui = $request->dui;
        $excluded->nit = $request->nit;
        $excluded->address = $request->address;
        $excluded->phone = $request->phone;
        $excluded->email = $request->email;
        $excluded->state_id = $request->state_id;
        $excluded->municipality_id = $request->municipality_id;    

        $excluded->save();

        //$lims_excluded_data = Excluded::findOrFail($id);
        return redirect('excluded')->with('message','Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Excluded  $excluded
     * @return \Illuminate\Http\Response
     */
    public function destroy(Excluded $excluded)
    {
        //
    }

    public function byDepar($id)
    {
       return Municipality::where('state_id', $id)->get();
    }

    public function stateUnit($id)
    {
        $unit = Municipality::where("state_id", $id)->pluck('name','id');

        //dd($unit);
        return json_encode($unit);
    }
}
