<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function create(Request $request){
        $post = new Post;
        $post->user_id = Auth::user()->id;
        $post->desc = $request->desc;

        //check if post have photo
        if($request->photo !=''){
            $photo = time().'jpg';
            file_put_contents('storage/posts/'.$photo,base64_decode($request->photo));
            $post->photo = $photo;
        }
        $post->save();
        $post->user;

        return response()->json([
            'success'=>true,
            'message'=>'posted',
            'post'=>$post
        ]);
    }
}
