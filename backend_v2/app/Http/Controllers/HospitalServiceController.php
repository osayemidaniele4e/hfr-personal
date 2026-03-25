<?php

namespace App\Http\Controllers;

use App\Models\HospitalServiceMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 * @group Administration - Master Data Hospital
 *
 * Manage the master list of hospital services under service categories.
 *
 * This controller handles listing, creating, updating, and deleting
 * hospital service records.
 *
 * @authenticated
 */
class HospitalServiceController extends Controller
{

      /**
     * List hospital services
     *
     * Displays all hospital services ordered by name.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $services = HospitalServiceMaster::orderBy('name', 'ASC')->get();

        return view('masters.hospital_services.index', compact('services'));
    }


     /**
     * Create a new hospital service
     *
     * Stores a new hospital service under a specified category.
     *
     * @bodyParam service_category_id integer required The ID of the service category.
     * @bodyParam name string required The name of the service. Max: 100 chars.
     *
     * @response 302 {
     *     "message": "Redirects back with success message"
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_category_id' => 'required',
            'name' => 'required|string|max:100',
        ]);


        $service = new HospitalServiceMaster;
        $service->name = $request->name;
        $service->service_category_id = $request->service_category_id;
        $service->save();

        session()->flash("alert-success", "Hospital service added successfully!");
        return back();
    }


     /**
     * Update an existing hospital service
     *
     * Updates the selected hospital service's name and category.
     *
     * @bodyParam id1 integer required The ID of the service to update.
     * @bodyParam service_category_id1 integer required The updated service category ID.
     * @bodyParam name1 string required The updated name of the service.
     *
     * @response 302 {
     *     "message": "Hospital service updated successfully"
     * }
     */
    public function update(Request $request)
    {
        $request->validate([
            'service_category_id1' => 'required',
            'name1' => 'required|string|max:100',
        ]);

        $service = HospitalServiceMaster::find($request->id1);
        $service->name = $request->name1;
        $service->service_category_id = $request->service_category_id1;
        $service->save();

        session()->flash("alert-success", "Hospital service updated successfully!");
        return back();
    }


     /**
     * Delete a hospital service
     *
     * Removes a hospital service from the master list.
     *
     * @bodyParam service_id integer required The ID of the service to delete.
     *
     * @response 302 {
     *     "message": "Hospital service deleted successfully"
     * }
     */
    public function destroy(Request $request)
    {
        HospitalServiceMaster::destroy($request->service_id);
        session()->flash("alert-success", "Hospital service deleted successfully!");
        return back();
    }
}
