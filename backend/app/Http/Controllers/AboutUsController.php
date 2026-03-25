<?php

namespace App\Http\Controllers;

use App\Website\API\AboutUs;
use App\Website\API\Origin;
use App\Website\API\Process;
use App\Website\API\ProcessItem;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
   
    public function origin()
    {
        $aboutOrigin = Origin::where('id', 1)->first();

        return view('about.index', compact('aboutOrigin'));
    }


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


    public function process()
    {
        $aboutProcess = Process::where('id', 1)->first();
        $aboutProcessItems = ProcessItem::latest()->get();

        return view('about.process', compact('aboutProcess', 'aboutProcessItems'));
    }


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
