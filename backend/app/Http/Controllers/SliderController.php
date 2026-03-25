<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Website\API\Slider;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Slider::latest()->get();

        return view('slider.index', compact('sliders'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:250',
            'sub_title' => 'required|string|max:250',
            'image_url' => 'nullable|mimes:jpeg,png,jpg',
        ], [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 50 characters.',

            'sub_title.required' => 'The sub title field is required.',
            'sub_title.string' => 'The sub title must be a string.',
            'sub_title.max' => 'The sub title may not be greater than 250 characters.',


            'image_url.mimes' => 'Only jpeg, png, and jpg image formats are allowed.',
        ]);

        // \Log::info($validated);
        try {
            // Handle file uploads
            $filePath = $request->hasFile('image_url')
                ? env('APP_URL') . "/storage/" . $request->file('image_url')->store('sliders', 'public')
                : null;

            // Create the slider record
            $slider = Slider::create([
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'image_url' => $filePath, // Save the file path in the database
            ]);

            return response()->json([
                'message' => 'Slider added successfully!',
                'name' => $slider->title, // Data to be returned to frontend
                'redirect' => route('slider.index'),
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:250',
            'sub_title' => 'required|string|max:250',
            'image_url' => 'nullable|mimes:jpeg,png,jpg',
        ], [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 50 characters.',

            'sub_title.required' => 'The sub title field is required.',
            'sub_title.string' => 'The sub title must be a string.',
            'sub_title.max' => 'The sub title may not be greater than 250 characters.',


            'image_url.mimes' => 'Only jpeg, png, and jpg image formats are allowed.',
        ]);

        // Find the role by ID and update
        try {

            $slider = Slider::findOrFail($request->id);

            // Handle file file upload
            if ($request->hasFile('image_url')) {
                // Delete the old file if it exists
                if ($slider->image_url) {
                    Storage::disk('public')->delete(str_replace(env('APP_URL') . "/storage/", '', $slider->image_url));
                }

                // Store the new file
                $filePath = env('APP_URL') . "/storage/" . $request->file('image_url')->store('sliders', 'public');
                $slider->image_url = $filePath;
            }

            $slider->title = $request->title;
            $slider->sub_title = $request->sub_title;
            $slider->save();

            // Flash a success message to the session
            session()->flash('success', 'Slider updated successfully!');
        } catch (\Exception $e) {
            // Flash an error message to the session
            session()->flash('error', 'There was an error updating the slider.');
        }

        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        try {
            // Find and delete the record
            $slider = Slider::findOrFail($id);

            // Unlink (delete) the photo file if it exists
            // if ($slider->image_url) {
            //     Storage::disk('public')->delete(str_replace(env('APP_URL') . "/storage/", '', $slider->image_url));
            // }


            $slider->delete();

            return response()->json([
                'success' => true,
                'message' => 'Slider deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while deleting the slider.',
            ]);
        }
    }
}
