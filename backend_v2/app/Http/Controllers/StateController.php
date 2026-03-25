<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;


/**
 * @group Administration - State Management
 *
 * APIs for managing states. Includes listing, creating, updating, and deleting states.
 */
class StateController extends Controller
{


    /**
     * List all states.
     *
     * @response 200 View with list of states
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $states = State::orderBy('name', 'ASC')->get();
        return view('masters.states.index', compact("states"));
    }


     /**
     * Add a new state.
     *
     * @bodyParam name string required Name of the state. Example: Lagos
     * @bodyParam short_code string required Short code of the state. Example: LA
     * @bodyParam num_code string required Numeric code of the state. Example: 01
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if required fields are missing or exceed max length
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'short_code' => 'required|string|max:2',
            'num_code' => 'required|string|max:2',
        ]);

        $state = new State;
        $state->name = $request->name;
        $state->short_code = $request->short_code;
        $state->num_code = $request->num_code;
        $state->save();

        session()->flash("alert-success", "State added successfully!");
        return back();
    }


     /**
     * Update an existing state.
     *
     * @bodyParam id integer required ID of the state to update. Example: 1
     * @bodyParam name1 string required Updated name of the state. Example: Lagos
     * @bodyParam short_code1 string required Updated short code. Example: LA
     * @bodyParam num_code1 string required Updated numeric code. Example: 01
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if required fields are missing or exceed max length
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:40',
            'short_code1' => 'required|string|max:2',
            'num_code1' => 'required|string|max:2',
        ]);

        $state = State::findOrFail($request->id);
        $state->name = $request->name1;
        $state->short_code = $request->short_code1;
        $state->num_code = $request->num_code1;
        $state->save();

        session()->flash("alert-success", "State updated successfully!");
        return back();
    }


     /**
     * Delete a state.
     *
     * @bodyParam state_id integer required ID of the state to delete. Example: 1
     *
     * @response 302 Redirect back with success message
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        State::destroy($request->state_id);
        session()->flash("alert-success", "State deleted successfully!");
        return back();
    }
}
