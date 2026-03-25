<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



/**
 * @group Administration - Ward Management
 *
 * APIs for managing Wards. Includes listing, creating, updating, deleting, and searching wards.
 */
class WardController extends Controller
{


     /**
     * List all wards with pagination.
     *
     * @response 200 View with paginated wards
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // $wards = DB::table('ou_wards')
        //     ->orderByRaw('lga_id,name ASC')
        //     ->paginate(10);

        $wards = DB::table('ou_wards')
            ->join('ou_lgas', 'ou_wards.lga_id', '=', 'ou_lgas.id')
            ->join('ou_states', 'ou_lgas.state_id', '=', 'ou_states.id')
            ->select(
                'ou_wards.*',
                'ou_lgas.name as lga',
                'ou_lgas.id as state_id',
                'ou_states.name as state',
                'ou_lgas.id as lga_id'
            )
            ->orderByRaw('ou_wards.lga_id, ou_wards.name ASC')
            ->paginate(10);


        return view('masters.wards.index', compact("wards"));
    }



     /**
     * Add a new ward.
     *
     * @bodyParam name string required Name of the ward. Example: Lagos Central
     * @bodyParam lga_id integer required ID of the LGA the ward belongs to. Example: 1
     * @bodyParam state_id integer optional ID of the state. Example: 1
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if required fields are missing or invalid
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'state_id' => 'nullable',
            'lga_id' => 'required',
        ]);

        \Log::info($request->all());


        $ward = new Ward();
        $ward->name = $request->name;
        $ward->lga_id = $request->lga_id;
        $ward->save();

        session()->flash("alert-success", "Ward added successfully!");
        return back();
    }


    /**
     * Update an existing ward.
     *
     * @bodyParam id1 integer required ID of the ward to update. Example: 1
     * @bodyParam name1 string required Updated name of the ward. Example: Lagos Central
     * @bodyParam lga_id1 integer required Updated LGA ID. Example: 1
     * @bodyParam state_id1 integer optional Updated state ID. Example: 1
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if required fields are missing or invalid
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:100',
            'state_id1' => 'nullable',
            'lga_id1' => 'required',
        ]);

        $ward = Ward::find($request->id1);
        $ward->name = $request->name1;
        $ward->lga_id = $request->lga_id1;
        $ward->save();

        session()->flash("alert-success", "Ward updated successfully!");
        return back();
    }


     /**
     * Delete a ward.
     *
     * @bodyParam ward_id integer required ID of the ward to delete. Example: 1
     *
     * @response 302 Redirect back with success message
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Ward::destroy($request->ward_id);
        session()->flash("alert-success", "Ward deleted successfully!");
        return back();
    }


     /**
     * Search wards by state, LGA, and ward name.
     *
     * @bodyParam state integer optional Filter by state ID. Example: 1
     * @bodyParam lga integer optional Filter by LGA ID. Example: 1
     * @bodyParam ward_name string optional Filter by ward name. Example: Lagos Central
     *
     * @response 200 View with paginated search results
     *
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        // $wards = DB::table('wards')
        //     ->where('state_id', 'like', '%' . $request->state . '%')
        //     ->where('lga_id', 'like', '%' . $request->lga . '%')
        //     ->where('name', 'like', '%' . $request->ward_name . '%')
        //     ->orderBy('state')
        //     ->orderBy('lga')
        //     ->orderBy('name')
        //     ->paginate(10)
        //     ->appends($request->all());

        $wards = DB::table('ou_wards')
            ->join('ou_lgas', 'ou_wards.lga_id', '=', 'ou_lgas.id')
            ->join('ou_states', 'ou_lgas.state_id', '=', 'ou_states.id')
            ->select(
                'ou_wards.*',
                'ou_lgas.name as lga',
                'ou_lgas.id as lga_id',
                'ou_states.name as state',
                'ou_states.id as state_id'
            )
            ->when($request->state, function ($query) use ($request) {
                $query->where('ou_states.id', 'like', '%' . $request->state . '%');
            })
            ->when($request->lga, function ($query) use ($request) {
                $query->where('ou_lgas.id', 'like', '%' . $request->lga . '%');
            })
            ->when($request->ward_name, function ($query) use ($request) {
                $query->where('ou_wards.name', 'like', '%' . $request->ward_name . '%');
            })
            ->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('ou_wards.name')
            ->paginate(10)
            ->appends($request->all());



        return view('masters.wards.index', compact("wards"));
    }
}
