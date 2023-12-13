<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function create(Request $request){
        // Check if there is an authenticated user
        if ($user = Auth::user()) {
            $post = new Post;
            $post->user_id = $user->id;
            $post->desc = $request->desc;
    
            // Check if the post has a photo
            if ($request->photo != '') {
                $photo = time() . 'jpg';
                file_put_contents('storage/posts/' . $photo, base64_decode($request->photo));
                $post->photo = $photo;
            }
    
            $post->save();
            $post->user;
    
            return response()->json([
                'success' => true,
                'message' => 'posted',
                'post' => $post
            ]);
        }
    
        // Handle the case where there is no authenticated user
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated',
        ], 401);
    }
    
}
