<?php

namespace App\Http\Controllers;

use App\Models\Lga;
use App\Models\State;
use Illuminate\Http\Request;


/**
 * @group Administration - LGA Management
 *
 * APIs for managing Local Government Areas (LGAs). Includes listing, creating, updating, deleting, and searching LGAs.
 */
class LgaController extends Controller
{


    /**
     * List all LGAs with pagination.
     *
     * @response 200 View with paginated LGAs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $lgas =Lga::orderBy('state_id','ASC')->paginate(10);
        return view('masters.lgas.index', compact("lgas"));
    }


     /**
     * Add a new LGA.
     *
     * @bodyParam name string required Name of the LGA. Example: Ikeja
     * @bodyParam state_id integer required ID of the state the LGA belongs to. Example: 1
     * @bodyParam lga_code string required LGA code. Example: 01
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if required fields are missing or invalid
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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


    /**
     * Update an existing LGA.
     *
     * @bodyParam id1 integer required ID of the LGA to update. Example: 1
     * @bodyParam name1 string required Updated name of the LGA. Example: Ikeja
     * @bodyParam state_id1 integer required Updated state ID. Example: 1
     * @bodyParam lga_code1 string required Updated LGA code. Example: 01
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if required fields are missing or invalid
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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


     /**
     * Delete a LGA.
     *
     * @bodyParam lga_id integer required ID of the LGA to delete. Example: 1
     *
     * @response 302 Redirect back with success message
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Lga::destroy($request->lga_id);
        session()->flash("alert-success", "LGA deleted successfully!");
        return back();
    }


     /**
     * Search LGAs by state and name.
     *
     * @bodyParam state string optional State ID to filter by. Example: 1
     * @bodyParam name string optional LGA name to filter by. Example: Ikeja
     *
     * @response 200 View with paginated search results
     *
     * @return \Illuminate\View\View
     */
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
