<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function createFollows(User $user){
        if(auth()->user()->id == $user->id){
            return back()->with('failure', 'You cannot follow yourself');
        }

        $existCheck = Follow::where( [['user_id', '=', auth()->user()->id], ['following_id','=', $user->id]])->count();

        if($existCheck){
            return back()->with('failure', 'You are already following this user');
        }



        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->following_id = $user->id;
        $newFollow->save();
        return back()->with('success', 'You are now following '.$user->username);


    }

    public function removeFollows(User $user){
        Follow::where([['user_id','=', auth()->user()->id], ['following_id', '=', $user->id]])->delete();
        return back()->with('success', 'You are no longer following '.$user->username);


    }
}
