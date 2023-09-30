<?php

use App\Events\ChatMessage;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\FollowsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

#User related routes
Route::get('/', [UserController::class, 'checkHome'])->name('login');
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('MustBeLoggedIn');
Route::get('/admin', [UserController::class, 'admin'])->middleware('can:adminVisit');
Route::get('/manage-avatar', [UserController::class, 'showManageAvatar'])->middleware('MustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, 'createAvatar'])->middleware('MustBeLoggedIn');

#Follows related routes
Route::post("create-follows/{user:username}", [FollowsController::class, 'createFollows'])->middleware('MustBeLoggedIn');
Route::post('remove-follows/{user:username}', [FollowsController::class, 'removeFollows'])->middleware('MustBeLoggedIn');


#Post related routes
Route::get('/create-post', [PostController::class, 'showCreatePost'])->middleware('MustBeLoggedIn');
Route::post('/create-post', [PostController::class, 'createPost'])->middleware('MustBeLoggedIn');;
Route::get('/post/{post}', [PostController::class, 'showPost']);
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class, 'showEditPost'])->middleware('can:update,post');
#Route::get('/post/{post}/edit', [PostController::class, 'showEditPost']);
Route::put('/post/{post}', [PostController::class, 'update'])->middleware('can:update,post');
Route::get('/search/{term}',[PostController::class, 'search'] );


#Profile related routes
Route::get('/profile/{user:username}', [UserController::class, 'showProfile']);
Route::get('/profile/{user:username}/followers', [UserController::class, 'showProfileFollowers']);
Route::get('/profile/{user:username}/following', [UserController::class, 'showProfileFollowing']);



Route::middleware('cache.headers:public;max_age=10;etag')->group(function () {
  Route::get('/profile/{user:username}/raw', [UserController::class, 'showProfileRaw']);
  Route::get('/profile/{user:username}/followers/raw', [UserController::class, 'showProfileFollowersRaw']);
  Route::get('/profile/{user:username}/following/raw', [UserController::class, 'showProfileFollowingRaw']);
});

// Chat route
// Route::post('/send-chat-message', function (Request $request) {
//     $formFields = $request->validate([
//       'textvalue' => 'required'
//     ]);
  
//     if (!trim(strip_tags($formFields['textvalue']))) {
//       return response()->noContent();
//     }
  
//     broadcast(new ChatMessage(['username' =>auth()->user()->username, 'textvalue' => strip_tags($request->textvalue), 'avatar' => auth()->user()->avatar]))->toOthers();
//     return response()->noContent();
  
//   })->middleware('MustBeLoggedIn');

Route::post('/send-chat-message', function (Request $request) {
    $incommingFields = $request->validate([
      'textvalue' => 'required'
    ]);

    if (!trim(strip_tags($incommingFields['textvalue']))) {
      return response()->noContent();
    }

    broadcast(new ChatMessage(['username' =>auth()->user()->username, 
    'textvalue' => strip_tags($request->textvalue),
    'avatar' => auth()->user()->avatar]))->toOthers();
    return response()->noContent();
  })->middleware('MustBeLoggedIn');