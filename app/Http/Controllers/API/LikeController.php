<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    public function like(Request $request)
    {
        $like = Like::where('post_id', $request->id)->where('user_id', Auth::user()->id)->first();

        // Check if the post is already liked
        if ($like) {
            // Prevent liking more than once
            $like->delete();

            // Update the like count on the post
            $post = Post::find($request->id);
            if ($post) {
                $post->likes_count = $post->likes_count > 0 ? $post->likes_count - 1 : 0;
                $post->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Unliked',
                'count' => $post->likes_count,
            ]);
        }

        // If not liked, create a new like
        $like = new Like;
        $like->user_id = Auth::user()->id;
        $like->post_id = $request->id;
        $like->save();

        // Update the like count on the post
        $post = Post::find($request->id);
        if ($post) {
            $post->likes_count += 1;
            $post->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Liked',
            'count' => $post->likes_count,
        ]);
    }
}
