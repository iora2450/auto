<?php

namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index()
    {
        $lims_ticket_all = Ticket::where('is_active', true)->get();
        return view('ticket.index', compact('lims_ticket_all'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => [
                'max:255',
                    Rule::unique('tickets')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        $data = $request->all();
        Ticket::create($data);
        return redirect('tickets')->with('message', 'Data inserted successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $lims_ticket_data = Ticket::find($id);
        return $lims_ticket_data;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => [
                'max:255',
                    Rule::unique('tickets')->ignore($request->ticket_id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        $data = $request->all();
        $lims_ticket_data = Ticket::find($data['ticket_id']);
        $lims_ticket_data->update($data);
        return redirect('tickets')->with('message', 'Data updated successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $ticket_id = $request['ticketIdArray'];
        foreach ($ticket_id as $id) {
            $lims_ticket_data = Ticket::find($id);
            $lims_ticket_data->is_active = false;
            $lims_ticket_data->save();
        }
        return 'Type Currency deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_ticket_data = Ticket::find($id);
        $lims_ticket_data->is_active = false;
        $lims_ticket_data->save();
        return redirect('tickets')->with('not_permitted', 'Data deleted successfully');
    }
}
