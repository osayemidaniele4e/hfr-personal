<?php

namespace App\Http\Controllers;

use App\Models\LaboratoryCertificate;
use Illuminate\Http\Request;


/**
 * @group Administration - Laboratory Certification
 *
 * APIs for managing laboratory certifications. Includes listing, creating, updating, and deleting certifications.
 */
class CertificationController extends Controller
{


    /**
     * List all laboratory certifications.
     *
     * @response 200 View with list of laboratory certifications
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $certificates =LaboratoryCertificate::orderBy('name','ASC')->get();
        return view('masters.lab_certificates.index',compact('certificates'));
    }


      /**
     * Add new laboratory certification.
     *
     * @bodyParam name string required Name of the certification. Example: ISO 15189
     * @bodyParam type string required Type of certification. Example: Accreditation
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if required fields are missing or too long
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required',
        ]);


        $cer = new LaboratoryCertificate;
        $cer->name = $request->name;
        $cer->type = $request->type;
        $cer->save();

        session()->flash("alert-success", "Laboratory certification added successfully!");
        return back();
    }


     /**
     * Update an existing laboratory certification.
     *
     * @bodyParam id integer required ID of the certification to update. Example: 1
     * @bodyParam name1 string required New name of the certification. Example: ISO 9001
     * @bodyParam type1 string required New type of the certification. Example: Accreditation
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if required fields are missing or too long
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:100',
            'type1' => 'required',
        ]);

        $cer = LaboratoryCertificate::find($request->id);
        $cer->name = $request->name1;
        $cer->type = $request->type1;
        $cer->save();
        session()->flash("alert-success", "Laboratory certification updated successfully!");
        return back();
    }


     /**
     * Delete a laboratory certification.
     *
     * @bodyParam certification_id integer required ID of the certification to delete. Example: 1
     *
     * @response 302 Redirect back with success message
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        LaboratoryCertificate::destroy($request->certification_id);
        session()->flash("alert-success", "Laboratory certification deleted successfully!");
        return back();
    }

}
