<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;

class LikeController extends Controller
{
    public function like(Request $request){
        $like = Like::where('post_id',$request->id)->where('user_id',Auth::user()->id)->first();

        //check if it returns 0 then this post is not liked and should be liked or unliked
        if($like){
            //prevent like more than 1 time
            $like->delete();
            return response()->json([
                'success' => true,
                'message' => 'Unliked'
            ]);
        }

        $like = new Like;
        $like->user_id = Auth::user()->id;
        $like->post_id = $request->id;
        $like->save();
        return response()->json([
            'success' => true,
            'message' => 'Liked'
        ]);
    }
}
