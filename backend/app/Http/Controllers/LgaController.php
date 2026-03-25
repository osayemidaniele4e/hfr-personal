<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lga;
use App\State;

class LgaController extends Controller
{
 

    public function index()
    {
        $lgas =Lga::orderBy('state_id','ASC')->paginate(10);
        return view('masters.lgas.index', compact("lgas"));
    }


    public function store(Request $request)
    {              
        $request->validate([
            'name' => 'required|string|max:40',
            'state_id' => 'required',
            'lga_code' => 'required|string|max:2',
        ]);

        $state = State::find($request->state_id);

        $lga = new Lga;
        $lga->name = $request->name;
        $lga->lga_code= $request->lga_code;
        $lga->state_code= $state->num_code;
        $lga->map_code= $state->num_code .'_'. $request->lga_code;
        $lga->state_id= $request->state_id;
        $lga->save();

        session()->flash("alert-success", "LGA added successfully!");        
        return back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:40',
            'state_id1' => 'required',
            'lga_code1' => 'required|string|max:2',
        ]);

        $state = State::find($request->state_id1);

        $lga = Lga::find($request->id1);
        $lga->name = $request->name1;
        $lga->lga_code= $request->lga_code1;
        $lga->state_code= $state->num_code;
        $lga->map_code= $state->num_code .'_'. $request->lga_code1;
        $lga->state_id= $request->state_id1;
        $lga->save();

        session()->flash("alert-success", "LGA updated successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        Lga::destroy($request->lga_id);
        session()->flash("alert-success", "LGA deleted successfully!");
        return back();
    }

    public function search(Request $request)
    {
        $lgas = Lga::where('state_id','like','%'. $request->state .'%')
            ->where('name','like','%'. $request->name .'%')
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->all());

        return view('masters.lgas.index', compact("lgas"));
    }


}
