<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\State;

class StateController extends Controller
{
  
    public function index()
    {
        $states =State::orderBy('name','ASC')->get();
        return view('masters.states.index', compact("states"));
    }


    public function store(Request $request)
    {              
        $request->validate([
            'name' => 'required|string|max:40',
            'short_code' => 'required|string|max:2',
            'num_code' => 'required|string|max:2',
        ]);

        $state = new State;
        $state->name = $request->name;
        $state->short_code= $request->short_code;
        $state->num_code= $request->num_code;
        $state->save();

        session()->flash("alert-success", "State added successfully!");        
        return back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:40',
            'short_code1' => 'required|string|max:2',
            'num_code1' => 'required|string|max:2',
        ]);

        $state = State::findOrFail($request->id);
        $state->name = $request->name1;
        $state->short_code= $request->short_code1;
        $state->num_code= $request->num_code1;
        $state->save();

        session()->flash("alert-success", "State updated successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        State::destroy($request->state_id);
        session()->flash("alert-success", "State deleted successfully!");
        return back();
    }

}
