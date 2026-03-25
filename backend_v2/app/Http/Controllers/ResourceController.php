<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use Illuminate\Support\Facades\Storage;


/**
 * @group Administration - Resources
 *
 * Endpoints for managing document resources.
 * Supports upload, download, listing, updating, and deletion.
 */
class ResourceController extends Controller
{


     /**
     * List All Resources (Admin)
     *
     * Retrieves all resources for administrative view.
     *
     * @response view resources.index
     */
    public function index()
    {
        $resources = Resource::all();
        return view('resources.index', compact("resources"));
    }


     /**
     * List All Resources (Public)
     *
     * Retrieves all resources for the public-facing page.
     *
     * @response view public.resources
     */
    public function public_index()
    {
        $resources = Resource::all();
        return view('public.resources', compact("resources"));
    }



     /**
     * Show Resource Upload Form
     *
     * Displays the form to upload a new document.
     *
     * @response view resources.upload
     */
    public function upload()
    {
        return view('resources.upload');
    }


     /**
     * Store a Resource
     *
     * Uploads and stores a new resource document.
     *
     * @bodyParam filename string required Description of the file. Max: 90 characters.
     * @bodyParam resourcefile file required Allowed types: doc, pdf, xls, xlsx, docx. Max size: 1999 KB.
     *
     * @response redirect 302 Redirects to resources.index with success message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'filename' => 'required|string|max:90',
            'resourcefile' => 'mimes:doc,pdf,xls,xlsx,docx|required|max:30720',
        ]);

        // Handle file uploads
        $filePath = $request->hasFile('resourcefile')
            ? env('APP_URL') . "/storage/" . $request->file('resourcefile')->store('resources', 'public')
            : null;

        $extension = $request->file('resourcefile')->getClientOriginalExtension();

        $resource = new Resource;
        $resource->filename = $filePath;
        $resource->description = $request->filename;
        $resource->format = $extension;
        $resource->save();

        session()->flash("alert-success", "File uploaded successfully!");
        return redirect()->route('resources');
    }


    /**
     * Update an Existing Resource
     *
     * Updates resource metadata and optionally replaces the file.
     *
     * @bodyParam id integer required Resource ID
     * @bodyParam filename1 string required Updated description of the file. Max: 90 characters.
     * @bodyParam resourcefile file Optional. Allowed types: doc, pdf, xls, xlsx, docx. Max size: 1999 KB.
     *
     * @response redirect 302 Redirects to resources.index with success message.
     */
    public function update(Request $request)
    {
        $request->validate([
            'filename1' => 'required|string|max:90',
        ]);


        // Handle file uploads
        $filePath = $request->hasFile('resourcefile')
            ? env('APP_URL') . "/storage/" . $request->file('resourcefile')->store('resources', 'public')
            : null;

        $extension = $request->file('resourcefile')->getClientOriginalExtension();

        $resource = Resource::find($request->id);
        $resource->filename = $filePath;
        $resource->format = $extension;
        $resource->description = $request->filename1;
        $resource->save();

        session()->flash("alert-success", "Document updated successfully!");
        return redirect()->route('resources');
    }


     /**
     * Download a Resource
     *
     * Downloads the specified resource file.
     *
     * @urlParam filename string required The name of the stored file.
     *
     * @response file Returns the resource file for download.
     */
    public function download($filename)
    {
        // $file= public_path(). "/storage/resources/".$filename;
        // // dd($file);
        // return response()->download($file);
        $file_path = storage_path('app/public/resources/' . $filename);
        return response()->download($file_path);
    }



     /**
     * Delete a Resource
     *
     * Deletes the resource record and the file from storage.
     *
     * @bodyParam filename string required Full URL or path of the file
     * @bodyParam doc_id integer required Resource ID
     *
     * @response redirect 302 Redirects to resources.index with success message.
     */
    public function destroy(Request $request)
    {
        // Extract the base filename only
        $filename = basename($request->filename);
        $docId = $request->doc_id;

        $file_path = storage_path('app/public/resources/' . $filename);
        \Log::info("File path: " . $file_path);

        if (file_exists($file_path)) {
            unlink($file_path);
            \Log::info("File deleted with unlink(): " . $filename);
        } else {
            \Log::warning("File not found, skipping unlink(): " . $request->filename);
        }

        Resource::destroy($docId);

        session()->flash("alert-success", "Document deleted successfully!");
        return redirect()->route('resources');
    }
}
