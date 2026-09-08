<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



//$user = new App\User;
//$user->email = 'example@example.com';
//$user->password = bcrypt('password');
//$user->first_name = '';
//$user->last_name = '';
//$user->status = '1';

//$user->save();



use App\Http\Controllers\BlockController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MessangerController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ReactController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\NotificationController;

Route::get('/', [PostController::class, 'index'])->middleware('postowner');
Route::get('/language/{locale}', function ($locale) { abort_unless(in_array($locale, ['en','ar'], true), 404); session(['locale'=>$locale]); return back(); })->name('language');
Route::get('/community', [CommunityController::class, 'index'])->middleware('auth');
Route::get('/watch', [ExploreController::class, 'watch'])->middleware('auth');
Route::get('/friends', [ExploreController::class, 'friends'])->middleware('auth');
Route::get('/saved', [ExploreController::class, 'saved'])->middleware('auth');
Route::post('/saved/{post}', [ExploreController::class, 'toggleSaved'])->middleware('auth');
Route::get('/memories', [ExploreController::class, 'memories'])->middleware('auth');
Route::get('/search', [ExploreController::class, 'search'])->middleware('auth');
Route::get('/notifications', [NotificationController::class, 'index'])->middleware('auth');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->middleware('auth');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->middleware('auth');
Route::get('/stories', [StoryController::class, 'index'])->middleware('auth');
Route::post('/stories', [StoryController::class, 'store'])->middleware('auth');
Route::delete('/stories/{id}', [StoryController::class, 'destroy'])->middleware('auth');
Route::post('/post/{id}/share', [PostController::class, 'share'])->middleware('auth');
Route::get('/blocked-users', [BlockController::class, 'index'])->middleware('auth');
Route::post('/users/{id}/block', [BlockController::class, 'block'])->middleware('auth');
Route::post('/users/{id}/unblock', [BlockController::class, 'unblock'])->middleware('auth');
Route::get('/settings', [SettingsController::class, 'index'])->name('settings')->middleware('auth');
Route::post('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.account')->middleware('auth');
Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password')->middleware('auth');
Route::post('/settings/privacy', [SettingsController::class, 'updatePrivacy'])->name('settings.privacy')->middleware('auth');
Route::post('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences')->middleware('auth');
Route::get('/websocket-ticket', function () {
    $expires = now()->addMinute()->timestamp;
    $userId = (string) auth()->id();
    $signature = hash_hmac('sha256', $userId.'|'.$expires, (string) config('services.websocket_secret'));

    return response()->json(['user_id' => $userId, 'expires' => $expires, 'signature' => $signature]);
})->middleware(['auth', 'throttle:30,1']);
Route::get('/groups', [CommunityController::class, 'index'])->middleware('auth');
Route::get('/pages', [CommunityController::class, 'index'])->middleware('auth');
Route::post('/pages', [CommunityController::class, 'storePage'])->middleware('auth');
Route::post('/groups', [CommunityController::class, 'storeGroup'])->middleware('auth');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');


Route::get('/reg', function(){
    return view('reg');
});
Route::post('/regi', [RegisterController::class, 'reg']);


Route::post('posts', [PostController::class, 'store'])->middleware('auth');
Route::get('/post/{id}', [PostController::class, 'index1']);
Route::post('/postdelete', [PostController::class, 'delete_post'])->middleware('auth');
Route::post('/post/{id}/update', [PostController::class, 'update'])->middleware('auth');


Route::post('comment', [CommentController::class, 'store'])->middleware('auth');
Route::post('profile/comment', [CommentController::class, 'store'])->middleware('auth');
Route::post('post/comment', [CommentController::class, 'store'])->middleware('auth');
Route::post('commentdelete', [CommentController::class, 'delete_comment'])->middleware('auth');


Route::post('commentphoto', [CommentController::class, 'photocommentstore'])->middleware('auth');
Route::post('commentvideo', [CommentController::class, 'videocommentstore'])->middleware('auth');
Route::post('post-reply', [CommentController::class, 'reply_store'])->middleware('auth');
Route::post('photo-reply', [CommentController::class, 'photo_reply_store'])->middleware('auth');
Route::post('photocommentdelete', [CommentController::class, 'delete_photocomment'])->middleware('auth');


Route::get('profile/{id}', [UsersController::class, 'profile']);
Route::get('users/{id}/hover-card', [UsersController::class, 'hoverCard'])->middleware('auth');
Route::post('/f_action', [UsersController::class, 'friend_action'])->middleware('auth');


Route::get('/photo/{id}', [PhotoController::class, 'photo'])->middleware('checkphotowner');
Route::get('/video/{id}', [VideoController::class, 'video']);

Route::post('make-profile-picture', [PhotoController::class, 'profile_pic'])->middleware('auth');


Route::post('like', [ReactController::class, 'react'])->middleware('auth');
Route::get('post/{post}/reactions', [ReactController::class, 'postReactions']);
Route::post('profile/like', [ReactController::class, 'react'])->middleware('auth');
Route::post('post/like', [ReactController::class, 'react'])->middleware('auth');
Route::post('photo/like', [ReactController::class, 'react_photo'])->middleware('auth');
Route::post('video/like', [ReactController::class, 'react_video'])->middleware('auth');
Route::post('likecomment', [ReactController::class, 'react_comment'])->middleware('auth');





// Route::get('/messanger/{id}', [MessangerController::class, 'messangerlol']);
// Route::post('/messanger/{id}', [MessangerController::class, 'messangerlol']);

// Route::get('/messanger/{id}', 'MessangerController@index');
// Route::post('/messanger/{id}', 'MessangerController@send');
// Route::post('/messanger/{id}/seen', 'MessangerController@seen');

Route::get('/messanger', 'MessangerController@inbox')->middleware('auth');
Route::get('/messanger/{id}', 'MessangerController@index')->middleware('auth');
Route::post('/messanger/{id}', 'MessangerController@index')->middleware('auth');
Route::post('/messanger/{id}/seen', 'MessangerController@seen')->middleware('auth');
