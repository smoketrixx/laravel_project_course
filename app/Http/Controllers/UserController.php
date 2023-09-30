<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use App\Events\ExampleEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function createAvatar(Request $request)
    {
        $request->validate([
            "avatar" => "required|image|max:2048",
        ]);
        $image = Image::make($request->file("avatar"))
            ->fit(120)
            ->encode("jpg");
        $user = auth()->user();
        $imgName = $user->id . "-" . uniqid() . ".jpg";
        Storage::put("public/avatars/" . $imgName, $image);
        $oldImgName = $user->avatar;
        $user->avatar = $imgName;
        $user->save();

        if ($oldImgName != "/fallback-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldImgName));
        }

        return back()->with("success", "Avatar updated");
    }
    public function showManageAvatar()
    {
        return view("manage-avatar");
    }

    public function admin()
    {
        return "only admins can visit this page";
    }

    private function getSharedData($user)
    {
        $currentFollow = 0;
        if (auth()->check()) {
            $currentFollow = Follow::where([
                ["user_id", "=", auth()->user()->id],
                ["following_id", "=", $user->id],
            ])->count();
        }

        return View::share("sharedData", [
            "currentFollow" => $currentFollow,
            "avatar" => $user->avatar,
            "username" => $user->username,
            "countPosts" => $user->posts()->count(),
            "countFollowers" => $user->followers()->count(),
            "countFollowing" => $user->following()->count(),
        ]);
    }

    public function showProfile(User $user)
    {
        $this->getSharedData($user);
        return view("profile-posts", [
            "posts" => $user
                ->posts()
                ->get()
                ->sortByDesc("created_at"),
        ]);
    }

    public function showProfileRaw(User $user)
    {
        return response()->json([
            "theHtml" => view("profile-posts-only", [
                "posts" => $user
                    ->posts()
                    ->latest()
                    ->get(),
            ])->render(),
            "docTitle" => $user->username . "'s profile",
        ]);
    }

    public function showProfileFollowers(User $user)
    {
        $this->getSharedData($user);
        # return  $user->followers()->latest()->get();
        return view("profile-followers", [
            "followers" => $user
                ->followers()
                ->latest()
                ->get(),
        ]);
    }
    public function showProfileFollowersRaw(User $user)
    {
        return response()->json([
            "theHtml" => view("profile-followers-only", [
                "followers" => $user
                    ->followers()
                    ->latest()
                    ->get(),
            ])->render(),
            "docTitle" => $user->username . "'s followers",
        ]);
    }

    public function showProfileFollowing(User $user)
    {
        $this->getSharedData($user);
        return view("profile-following", [
            "following" => $user
                ->following()
                ->latest()
                ->get(),
        ]);
    }

    public function showProfileFollowingRaw(User $user)
    {
        return response()->json([
            "theHtml" => view("profile-following-only", [
                "following" => $user
                    ->following()
                    ->latest()
                    ->get(),
            ])->render(),
            "docTitle" => $user->username . "'s following",
        ]);
    }

    public function logout()
    {
        event(
            new ExampleEvent([
                "username" => auth()->user()->username,
                "action" => "logout",
            ])
        );
        auth()->logout();
        return redirect("/")->with("success", "You are now logged out");
    }

    public function checkHome()
    {
        if (auth()->check()) {
            return view("homeLog", [
                "posts" => auth()
                    ->user()
                    ->feedPosts()
                    ->latest()
                    ->paginate(5),
            ]);
        } else {
          $postCount = Cache::remember("postCount", 4, function (){
            return Post::count();
          });

            return view("homePage", ['postCount'=>$postCount]);
        }
    }

    public function loginApi(Request $request)
    {
        $incommingFields = $request->validate([
            "username" => "required",
            "password" => "required",
        ]);


        if(auth()->attempt($incommingFields)){
            $user = User::where('username', $incommingFields['username'])->first();
            $token = $user->createToken('ourAppToken')->plainTextToken;
            return $token;
        }

        return 'failed';

    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            "loginusername" => "required",
            "loginpassword" => "required",
        ]);

        if (
            auth()->attempt([
                "username" => $incomingFields["loginusername"],
                "password" => $incomingFields["loginpassword"],
            ])
        ) {
            $request->session()->regenerate();
            event(
                new ExampleEvent([
                    "username" => auth()->user()->username,
                    "action" => "login",
                ])
            );
            return redirect("/")->with("success", "You are now logged in");
        } else {
            return redirect("/")->with(
                "failure",
                "Invalid Username or Password"
            );
        }
    }

    public function register(Request $request)
    {
        $incommingFields = $request->validate([
            "username" => [
                "required",
                "min:3",
                "max:20",
                Rule::unique("users", "username"),
            ],
            "email" => [
                "required",
                "email",
                "max:255",
                Rule::unique("users", "email"),
            ],
            "password" => ["required", "min:8", "max:30", "confirmed"],
        ]);
        $incommingFields["password"] = bcrypt($incommingFields["password"]);

        $user = User::create($incommingFields);
        auth()->login($user);
        return redirect("/")->with("success", "You are now registered");
    }
}

