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
use App\Http\Controllers\HashtagController;
use App\Http\Controllers\LiveStreamController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\SiteSetting;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::put('/appearance', [AdminController::class, 'updateAppearance'])->name('appearance.update');
    Route::delete('/appearance', [AdminController::class, 'resetAppearance'])->name('appearance.reset');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/posts/{post}', [AdminController::class, 'destroyPost'])->name('posts.destroy');
    Route::put('/reports/{report}', [AdminController::class, 'reviewReport'])->name('reports.review');
});
Route::post('/posts/{post}/report', [ReportController::class, 'store'])->middleware(['auth', 'throttle:10,1'])->name('posts.report');
Route::get('/media/photos/{photo}', [MediaController::class, 'photo'])->name('media.photos.show');
Route::get('/media/videos/{video}/hls/{file}', [MediaController::class, 'videoHls'])->where('file', '[A-Za-z0-9_.-]+')->name('media.videos.hls');
Route::get('/media/videos/{video}/thumbnail', [MediaController::class, 'videoThumbnail'])->name('media.videos.thumbnail');
Route::get('/media/stories/{story}', [MediaController::class, 'story'])->middleware('auth')->name('media.stories.show');
Route::get('/media/comments/{comment}/{file}', [MediaController::class, 'comment'])->where('file', '[A-Za-z0-9_.-]+')->name('media.comments.show');
Route::get('/media/replies/{reply}/{file}', [MediaController::class, 'reply'])->where('file', '[A-Za-z0-9_.-]+')->name('media.replies.show');

Route::get('/', [PostController::class, 'index'])->middleware('postowner');
Route::get('/language/{locale}', function ($locale) { abort_unless(in_array($locale, ['en','ar'], true), 404); session(['locale'=>$locale]); return back(); })->name('language');
Route::get('/community', [CommunityController::class, 'index'])->middleware('auth');
Route::get('/watch', [ExploreController::class, 'watch'])->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/live/create', [LiveStreamController::class, 'create'])->name('live.create');
    Route::post('/live', [LiveStreamController::class, 'store'])->name('live.store');
    Route::get('/live/{stream}', [LiveStreamController::class, 'show'])->name('live.show');
    Route::post('/live/{stream}/start', [LiveStreamController::class, 'start']);
    Route::post('/live/{stream}/join', [LiveStreamController::class, 'join']);
    Route::post('/live/{stream}/leave', [LiveStreamController::class, 'leave']);
    Route::post('/live/{stream}/finish', [LiveStreamController::class, 'finish']);
    Route::get('/live/{stream}/status', [LiveStreamController::class, 'status']);
    Route::get('/live/{stream}/comments', [LiveStreamController::class, 'comments']);
    Route::post('/live/{stream}/comments', [LiveStreamController::class, 'comment']);
});
Route::get('/friends', [ExploreController::class, 'friends'])->middleware('auth');
Route::get('/saved', [ExploreController::class, 'saved'])->middleware('auth');
Route::post('/saved/{post}', [ExploreController::class, 'toggleSaved'])->middleware('auth');
Route::get('/memories', [ExploreController::class, 'memories'])->middleware('auth');
Route::get('/search', [ExploreController::class, 'search'])->middleware('auth');
Route::get('/hashtag/{slug}', [HashtagController::class, 'show'])->name('hashtags.show')->middleware('auth');
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
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/email/verify', [VerificationController::class, 'show'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');


Route::get('/reg', function(){
    if (!SiteSetting::getValue('registration_enabled', true)) {
        return redirect()->route('login')->with('registration_closed', true);
    }

    return view('reg');
});
Route::post('/regi', [RegisterController::class, 'reg'])->middleware('throttle:5,1');


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
Route::post('photovideo', [PhotoController::class, 'uploadMedia'])->middleware('auth');


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
Route::get('/messages/summary', 'MessangerController@summary')->middleware('auth');
Route::get('/messages/{message}/attachment', [MessangerController::class, 'attachment'])->middleware('auth')->name('messages.attachment');
Route::get('/messanger/{id}', 'MessangerController@index')->middleware('auth');
Route::post('/messanger/{id}', 'MessangerController@index')->middleware('auth');
Route::post('/messanger/{id}/seen', 'MessangerController@seen')->middleware('auth');
