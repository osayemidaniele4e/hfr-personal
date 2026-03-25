<?php

namespace App\Http\Controllers;

use App\Models\Website\API\AboutUs;
use App\Models\Website\API\Origin;
use App\Models\Website\API\Process;
use App\Models\Website\API\ProcessItem;
use Illuminate\Http\Request;


/**
 * @group Public API (Frontend) - About Us
 *
 * APIs for managing the About Us section of the website including origin story, 
 * process description, and process items.
 */
class AboutUsController extends Controller
{


    /**
     * Display Origin Section
     *
     * Displays the "About Us - Origin" page with title and content.
     * Always retrieves the single origin record (ID: 1).
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "view": "about.index",
     *   "aboutOrigin": {
     *     "id": 1,
     *     "title": "Our Origin Story",
     *     "content": "We started our journey in 2020...",
     *     "created_at": "2024-01-01 00:00:00",
     *     "updated_at": "2025-01-15 10:30:00"
     *   }
     * }
     */
    public function origin()
    {
        $aboutOrigin = Origin::where('id', 1)->first();

        return view('about.index', compact('aboutOrigin'));
    }



      /**
     * Update Origin Section
     *
     * Updates the origin story content (title and main content).
     * Only one origin record exists (ID: 1) and is updated each time.
     *
     * @authenticated
     *
     * @bodyParam title string required The origin section title. Maximum 250 characters. Example: Our Origin Story
     * @bodyParam content string required The origin section content/description. Example: We began our journey in 2020 with a vision to transform healthcare...
     *
     * @response 302 scenario="Success" {
     *   "message": "Origin updated successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 422 scenario="Validation Error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "title": ["The title field is required."],
     *     "content": ["The content field is required."]
     *   }
     * }
     *
     * @response 500 scenario="Server Error" {
     *   "message": "There was an error updating the origin.",
     *   "redirect": "back"
     * }
     */
    public function updateOrigin(Request $request)
    {

        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:250',
            'content' => 'required',
        ], [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 50 characters.',

            'content.required' => 'The sub title field is required.',
            'content.string' => 'The sub title must be a string.',
        ]);

        // Find the role by ID and update
        try {

            $aboutOrigin = Origin::findOrFail(1);

            $aboutOrigin->title = $request->title;
            $aboutOrigin->content = $request->content;
            $aboutOrigin->save();

            // Flash a success message to the session
            session()->flash('success', 'Origin updated successfully!');
        } catch (\Exception $e) {
            // Flash an error message to the session
            session()->flash('error', 'There was an error updating the origin.');
        }

        return redirect()->back();
    }



 /**
     * Display Process Section
     *
     * Displays the "About Us - Process" page with process description and list of process items.
     * Retrieves the single process record (ID: 1) and all process items ordered by latest.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "view": "about.process",
     *   "aboutProcess": {
     *     "id": 1,
     *     "title": "Our Process",
     *     "content": "We follow a structured approach...",
     *     "created_at": "2024-01-01 00:00:00",
     *     "updated_at": "2025-01-15 10:30:00"
     *   },
     *   "aboutProcessItems": [
     *     {
     *       "id": 1,
     *       "title": "Step 1: Assessment",
     *       "created_at": "2024-01-15 10:00:00",
     *       "updated_at": "2024-01-15 10:00:00"
     *     },
     *     {
     *       "id": 2,
     *       "title": "Step 2: Planning",
     *       "created_at": "2024-01-20 14:30:00",
     *       "updated_at": "2024-01-20 14:30:00"
     *     }
     *   ]
     * }
     */
    public function process()
    {
        $aboutProcess = Process::where('id', 1)->first();
        $aboutProcessItems = ProcessItem::latest()->get();

        return view('about.process', compact('aboutProcess', 'aboutProcessItems'));
    }



  /**
     * Update Process Section
     *
     * Updates the process description content (title and main content).
     * Only one process record exists (ID: 1) and is updated each time.
     *
     * @authenticated
     *
     * @bodyParam title string required The process section title. Maximum 250 characters. Example: Our Process
     * @bodyParam content string required The process section content/description. Example: We follow a structured 5-step approach to deliver excellence...
     *
     * @response 302 scenario="Success" {
     *   "message": "Process updated successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 422 scenario="Validation Error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "title": ["The title field is required."],
     *     "content": ["The content field is required."]
     *   }
     * }
     *
     * @response 500 scenario="Server Error" {
     *   "message": "There was an error updating the process.",
     *   "redirect": "back"
     * }
     */
    public function updateProcess(Request $request)
    {

        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:250',
            'content' => 'required',
        ], [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 50 characters.',

            'content.required' => 'The sub title field is required.',
            'content.string' => 'The sub title must be a string.',
        ]);

        // Find the role by ID and update
        try {

            $aboutProcess = Process::findOrFail(1);

            $aboutProcess->title = $request->title;
            $aboutProcess->content = $request->content;
            $aboutProcess->save();

            // Flash a success message to the session
            session()->flash('success', 'Process updated successfully!');
        } catch (\Exception $e) {
            // Flash an error message to the session
            session()->flash('error', 'There was an error updating the process.');
        }

        return redirect()->back();
    }



/**
     * Create Process Item
     *
     * Adds a new process step/item to the process list.
     * Process items represent individual steps or stages in the organization's workflow.
     *
     * @authenticated
     *
     * @bodyParam title string required The process item title/description. Example: Step 1: Initial Consultation
     *
     * @response 200 scenario="Success" {
     *   "message": "Process item added successfully!",
     *   "name": "Step 1: Initial Consultation",
     *   "redirect": "http://example.com/process"
     * }
     *
     * @response 422 scenario="Validation Error" {
     *   "success": false,
     *   "message": "Validation error occurred.",
     *   "errors": {
     *     "title": ["The title field is required."]
     *   }
     * }
     *
     * @response 500 scenario="Server Error" {
     *   "success": false,
     *   "message": "An unexpected error occurred. Please try again later.",
     *   "error": "Database connection failed"
     * }
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string',
        ], [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',

        ]);

        // \Log::info($validated);
        try {

            // Create the slider record
            $processItem = ProcessItem::create([
                'title' => $request->title,
            ]);

            return response()->json([
                'message' => 'Process item added successfully!',
                'name' => $processItem->title, // Data to be returned to frontend
                'redirect' => route('process.index'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation error occurred.',
                'errors' => $e->errors(), // Detailed validation errors
            ], 422);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error' => $e->getMessage(), // Optional: for debugging (remove in production)
            ], 500);
        }
    }



 /**
     * Update Process Item
     *
     * Updates an existing process step/item.
     * Used to modify the title/description of a specific process step.
     *
     * @authenticated
     *
     * @bodyParam id integer required The ID of the process item to update. Example: 5
     * @bodyParam title string required The updated process item title/description. Example: Step 1: Comprehensive Assessment
     *
     * @response 302 scenario="Success" {
     *   "message": "Process item updated successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 422 scenario="Validation Error" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "title": ["The title field is required."]
     *   }
     * }
     *
     * @response 404 scenario="Not Found" {
     *   "message": "There was an error updating the process item.",
     *   "redirect": "back"
     * }
     *
     * @response 500 scenario="Server Error" {
     *   "message": "There was an error updating the process item.",
     *   "redirect": "back"
     * }
     */
    public function update(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string',
        ], [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',

        ]);

        // Find the role by ID and update
        try {

            $processItem = ProcessItem::findOrFail($request->id);

            $processItem->title = $request->title;
            $processItem->save();

            // Flash a success message to the session
            session()->flash('success', 'Process item updated successfully!');
        } catch (\Exception $e) {
            // Flash an error message to the session
            session()->flash('error', 'There was an error updating the process item.');
        }

        return redirect()->back();
    }



 /**
     * Delete Process Item
     *
     * Permanently deletes a process step/item from the system.
     * This action cannot be undone.
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the process item to delete. Example: 5
     *
     * @response 200 scenario="Success" {
     *   "success": true,
     *   "message": "Process item deleted successfully!"
     * }
     *
     * @response 404 scenario="Not Found" {
     *   "success": false,
     *   "message": "Error occurred while deleting the headquarter."
     * }
     *
     * @response 500 scenario="Server Error" {
     *   "success": false,
     *   "message": "Error occurred while deleting the headquarter."
     * }
     */
    public function destroy(string $id)
    {
        try {
            // Find and delete the record
            $processItem = ProcessItem::findOrFail($id);

            $processItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Process item deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while deleting the headquarter.',
            ]);
        }
    }

}
