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
    $posts = Post::with(['comments', 'likes'])->orderBy('id', 'desc')->get();
    
    foreach ($posts as $post) {
        // get user post
        $post->user;

        // comments count
        $post['commentsCount'] = $post->comments ? $post->comments->count() : 0;

        // likes count
        $post['likesCount'] = $post->likes ? $post->likes->count() : 0;

        // check if the user likes their own post
        $post['selfLike'] = $post->likes ? $post->likes->contains('user_id', optional(Auth::user())->id) : false;
    }

    return response()->json([
        'success' => true,
        'posts' => $posts
    ]);
    }



}
