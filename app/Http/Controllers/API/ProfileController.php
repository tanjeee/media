<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

class ProfileController extends Controller
{
    public function update_profile(Request $request)
    {
        $user = auth()->user();
    
        // Check if the file was included in the request
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
    
            // Generate a unique filename
            $filename = 'profile_photo_' . $user->id . '.' . $file->getClientOriginalExtension();
    
            // Move the file to the desired directory
            $file->move(public_path('images'), $filename);
    
            // Update the user's profile photo field in the database
            $user->update(['profile_photo' => $filename]);
    
            return response()->json(['message' => 'Image updated successfully']);
        } else {
            return response()->json(['error' => 'No file provided'], 400);
        }
    }
    

    public function get_profile(Request $request)
{
    $user = auth()->user();

    $imagePath = public_path('images/' . $user->profile_photo);

    if (file_exists($imagePath)) {
        return response()->file($imagePath);
    } else {
        return response()->json(['error' => 'Image not found'], 404);
    }
}

}
