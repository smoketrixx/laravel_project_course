<?php

namespace App\Http\Controllers;

use App\Jobs\SendNewPostEmail;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{


    public function search($term){
        $result = Post::search($term)->get();
        $result = $result->load('user');
        return $result;
    }

    public function update(Post $post, Request $request){

        $incommingFields = $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $incommingFields['title'] = strip_tags($incommingFields['title']);
        $incommingFields['content'] = strip_tags($incommingFields['content']);
        $post->update($incommingFields);
       return redirect('/profile/' . auth()->user()->username)->with('success', 'Post updated');
    }

    public function updateApi(Post $post, Request $request){

        $incommingFields = $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $incommingFields['title'] = strip_tags($incommingFields['title']);
        $incommingFields['content'] = strip_tags($incommingFields['content']);
        $post->update($incommingFields);
       return 'success updated';
    }

    public function showEditPost(Post $post){
        return view('edit-post', ['post' => $post]);
    }

    public function delete(Post $post){
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'Post deleted');
    }

    public function deleteApi(Post $post){
        $post->delete();
        return 'success';
    }


    public function showPost(Post $post) {
        $post['content'] = Str::markdown($post->content);
        return view('single-post', ['post' => $post]);
    }

   

   public function showCreatePost(){
    return view('create-post');
   }

   public function createPost(Request $request){
       $incommingFields = $request->validate([
           'title' => 'required',
           'content' => 'required'
       ]);

       
       $incommingFields['title'] = strip_tags($incommingFields['title']);
       $incommingFields['content'] = strip_tags($incommingFields['content']);
       $incommingFields['user_id'] = auth()->id();

       $newPost = Post::create($incommingFields);

       dispatch(new SendNewPostEmail(['toUser' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title]));

       
       return redirect("/post/{$newPost->id}")->with('success', 'Post created');
   }

   public function createPostApi(Request $request){
    $incommingFields = $request->validate([
        'title' => 'required',
        'content' => 'required'
    ]);

    
    $incommingFields['title'] = strip_tags($incommingFields['title']);
    $incommingFields['content'] = strip_tags($incommingFields['content']);
    $incommingFields['user_id'] = auth()->id();

    $newPost = Post::create($incommingFields);

    dispatch(new SendNewPostEmail(['toUser' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title]));

    return $newPost->id;

   }
}
