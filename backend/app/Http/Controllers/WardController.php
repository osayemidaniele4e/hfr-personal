<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Ward;


class WardController extends Controller
{

    public function index()
    {
        $wards = DB::table('wards')
            ->orderByRaw('state,lga,name ASC')
            ->paginate(10);

        return view('masters.wards.index', compact("wards"));
    }


   
    public function store(Request $request)
    {              
        $request->validate([
            'name' => 'required|string|max:100',
            'state_id' => 'required',
            'lga_id' => 'required',
        ]);

        $ward = new Ward;
        $ward->name = $request->name;
        $ward->lga_id= $request->lga_id;
        $ward->save();

        session()->flash("alert-success", "Ward added successfully!");        
        return back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:100',
            'state_id1' => 'required',
            'lga_id1' => 'required',
        ]);

        $ward = Ward::find($request->id1);
        $ward->name = $request->name1;
        $ward->lga_id= $request->lga_id1;
        $ward->save();

        session()->flash("alert-success", "Ward updated successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        Ward::destroy($request->ward_id);
        session()->flash("alert-success", "Ward deleted successfully!");
        return back();
    }

    public function search(Request $request)
    {
        $wards = DB::table('wards')
            ->where('state_id','like','%'. $request->state .'%')
            ->where('lga_id','like','%'. $request->lga .'%')
            ->where('name','like','%'. $request->ward_name .'%')
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->all());

        return view('masters.wards.index', compact("wards"));
    }


}
