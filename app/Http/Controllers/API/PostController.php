<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Storage;

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

    public function update(Request $request){
        $post = Post::find($request->id);

        //check if user is editing own post
        if(Auth::user()->id != $request->id){
            return response()->json([
                'success'=>false,
                'message'=>'Unauthorized user'
            ]);
        }
        $post->desc = $request->desc;
        $post->update();
        return response()->json([
            'success'=>true,
            'message'=> 'Post edited'
        ]);
    }

    public function delete(Request $request){
        $post = Post::find($request->id);

        //check if user is editing own post
        if(Auth::user()->id != $request->id){
            return response()->json([
                'success'=>false,
                'message'=>'Unauthorized user'
            ]);
        }
        
        //check if post has photo to delete
        if($post->photo != ''){
            Storage::delete('public/posts/'.$post->photo);
        }

        $post->delete();

        return response()->json([
            'success'=>true,
            'message'=> 'Post deleted'
        ]);
    }

    public function posts()
{
    $posts = Post::orderBy('id', 'desc')->get();
    
    foreach ($posts as $post) {
        // get user post
        $post->user;

        // comments count
        $post['commentsCount'] = count($post->comments ?? []);

        // likes count
        $post['likesCount'] = count($post->likes ?? []);

        // check if the user likes their own post
        $post['selfLike'] = false;
        
        foreach ($post->likes ?? [] as $like) {
            if ($like->user_id == Auth::user()->id) {
                $post['selfLike'] = true;
            }
        }
    }

    return response()->json([
        'success' => true,
        'posts' => $posts
    ]);
}

}
