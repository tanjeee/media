<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'nullable|string', // Change the validation rule
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation fails',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        if ($request->has('profile_photo')) {
            $base64Image = $request->input('profile_photo');
            $imageData = base64_decode($base64Image);

            // Generate a unique filename
            $filename = time() . '_profile_photo.jpg';

            // Save the image to the storage disk (public disk in this example)
            Storage::disk('public')->put($filename, $imageData);

            // Delete the old profile photo if it exists
            if ($user->profile_photo) {
                $old_path = 'images/' . $user->profile_photo;
                if (Storage::disk('public')->exists($old_path)) {
                    Storage::disk('public')->delete($old_path);
                }
            }

            $user->update([
                'profile_photo' => $filename,
            ]);

            $imageUrl = url('images/' . $filename);

            return response()->json([
                'message' => 'Profile successfully updated',
                'user' => $user,
                'image_url' => $imageUrl,
            ], 200);
        } else {
            
            return response()->json([
                'success'=>false,
                'message' => 'Profile photo not provided',
            ], 400);
        }
    }
}
