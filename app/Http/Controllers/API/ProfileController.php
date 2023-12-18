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
    $validator = Validator::make($request->all(), [
        'profile_photo' => 'nullable|image|mimes:jpg,png,bmp',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation fails',
            'errors' => $validator->errors(),
        ], 422);
    }

    $user = auth()->user();

    if ($request->hasFile('profile_photo')) {
        if ($user->profile_photo) {
            $old_path = public_path('images') . $user->profile_photo;
            if (File::exists($old_path)) {
                File::delete($old_path);
            }
        }

        $image = $request->file('profile_photo');
        $image_name = time() . '.' . $image->getClientOriginalExtension();

        $image->move(public_path('images'), $image_name);

        // Update the database with the new image name
        $user->update([
            'profile_photo' => $image_name,
        ]);

        // Check if the client requested an image
        if ($request->expectsJson()) {
            $imageUrl = url('images/' . $image_name);

            return response()->json([
                'message' => 'Profile successfully updated',
                'user' => $user,
                'image_url' => $imageUrl,
            ], 200);
        } else {
            // If the client didn't request JSON, return the image directly
            return response()->file(public_path('images/' . $image_name));
        }
    } else {
        // Handle non-image updates as before
        $image_name = $user->profile_photo;

        $user->update([
            'profile_photo' => $image_name,
        ]);

        $imageUrl = url('images/' . $image_name);

        return response()->json([
            'message' => 'Profile successfully updated',
            'user' => $user,
            'image_url' => $imageUrl,
        ], 200);
    }
}

    public function get_profile(Request $request)
{
    $user = auth()->user();

    $imagePath = public_path('images/' . $user->profile_photo);

    if (file_exists($imagePath)) {
        $imageUrl = url('images/' . $user->profile_photo);

        return response()->json([
            'message' => 'Profile image retrieved successfully',
            'user' => $user,
            'image_url' => $imageUrl,
        ], 200);
    } else {
        return response()->json(['error' => 'Image not found'], 404);
    }
}

}
