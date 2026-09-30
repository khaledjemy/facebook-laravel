<?php

namespace App\Http\Controllers;

use App\AdminActivity;
use App\Commente;
use App\ContentReport;
use App\CommentReact;
use App\Notifications\NewCommentNotification;
use App\Notifications\NewReactionNotification;
use App\Post;
use App\Notifications\PostSharedNotification;
use App\React;
use App\Replie;
use App\SiteSetting;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $siteSettings = SiteSetting::allWithDefaults();
        $stats = [
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'posts' => Post::count(),
            'comments' => Commente::count(),
            'new_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'pending_reports' => ContentReport::where('status', 'pending')->count(),
        ];

        $users = User::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $request->string('q')).'%';
                $query->where(function ($query) use ($term) {
                    $query->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->latest()->paginate(12, ['*'], 'users_page')->withQueryString();

        $posts = Post::with('user')->latest()->paginate(10, ['*'], 'posts_page')->withQueryString();
        $reports = ContentReport::with(['user', 'post.user'])->where('status', 'pending')->latest()->limit(20)->get();
        $activities = AdminActivity::with('admin')->latest()->limit(20)->get();

        return view('admin.dashboard', compact('stats', 'users', 'posts', 'reports', 'activities', 'siteSettings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:80'],
            'site_description' => ['nullable', 'string', 'max:300'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'max_upload_mb' => ['required', 'integer', 'min:1', 'max:500'],
            'posts_per_page' => ['required', 'integer', Rule::in([5, 10, 15, 20, 30, 50])],
            'registration_enabled' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $data) {
            foreach (['site_name', 'site_description', 'support_email'] as $key) {
                SiteSetting::putValue($key, $data[$key] ?? '');
            }
            foreach (['max_upload_mb', 'posts_per_page'] as $key) {
                SiteSetting::putValue($key, $data[$key], 'integer');
            }
            foreach (['registration_enabled', 'maintenance_mode'] as $key) {
                SiteSetting::putValue($key, $request->boolean($key), 'boolean');
            }
            $this->log($request, 'settings.updated', null, ['keys' => array_keys($data)]);
        });

        return back()->with('success', 'تم حفظ إعدادات الموقع وتطبيقها.');
    }

    public function updateAppearance(Request $request)
    {
        $data = $request->validate([
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'page_background' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'navbar_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'font_family' => ['required', Rule::in(['Arial', 'Tahoma', 'Cairo', 'Plus Jakarta Sans'])],
            'card_radius' => ['required', 'integer', 'min:0', 'max:30'],
            'login_title' => ['required', 'string', 'max:160'],
            'login_subtitle' => ['nullable', 'string', 'max:300'],
            'footer_text' => ['nullable', 'string', 'max:120'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,ico', 'max:1024'],
            'login_background' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);

        foreach (['primary_color', 'accent_color', 'page_background', 'navbar_color', 'font_family', 'login_title', 'login_subtitle', 'footer_text'] as $key) {
            SiteSetting::putValue($key, $data[$key]);
        }
        SiteSetting::putValue('card_radius', $data['card_radius'], 'integer');

        foreach (['logo', 'favicon', 'login_background'] as $field) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $settingKey = $field.'_path';
            $oldPath = SiteSetting::getValue($settingKey, '');
            $path = $request->file($field)->store('branding', 'public');
            SiteSetting::putValue($settingKey, $path);

            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        $this->log($request, 'appearance.updated');

        return back()->with('success', 'تم تحديث مظهر الموقع.');
    }

    public function resetAppearance(Request $request)
    {
        foreach (['logo_path', 'favicon_path', 'login_background_path'] as $key) {
            $path = SiteSetting::getValue($key, '');
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            SiteSetting::putValue($key, '');
        }

        foreach (['primary_color', 'accent_color', 'page_background', 'navbar_color', 'font_family', 'login_title', 'login_subtitle', 'footer_text'] as $key) {
            SiteSetting::putValue($key, SiteSetting::DEFAULTS[$key]);
        }
        SiteSetting::putValue('card_radius', SiteSetting::DEFAULTS['card_radius'], 'integer');
        $this->log($request, 'appearance.reset');

        return back()->with('success', 'تم استرجاع التصميم الافتراضي.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'is_active' => ['required', 'boolean'],
            'is_admin' => ['required', 'boolean'],
        ]);

        if ($user->is($request->user()) && (!$data['is_active'] || !$data['is_admin'])) {
            return back()->withErrors(['user' => 'لا يمكنك إيقاف حسابك أو إزالة صلاحية الإدارة عن نفسك.']);
        }

        if ($user->is_admin && !$data['is_admin'] && User::where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['user' => 'يجب أن يبقى مدير واحد على الأقل للنظام.']);
        }

        DB::transaction(function () use ($request, $user, $data) {
            $user->update($data);
            $this->log($request, 'user.updated', $user, $data);
        });

        return back()->with('success', 'تم تحديث صلاحيات وحالة المستخدم.');
    }

    public function destroyPost(Request $request, Post $post)
    {
        $postId = $post->id;
        $ownerId = $post->user_id;
        DB::transaction(function () use ($request, $post, $postId, $ownerId) {
            $commentIds = Commente::where('post_id', $postId)->pluck('id');
            CommentReact::whereIn('comment_id', $commentIds)->delete();
            Replie::whereIn('comment_id', $commentIds)->delete();
            Commente::whereIn('id', $commentIds)->delete();
            React::where('post_id', $postId)->delete();
            $this->removePostNotifications($postId);
            $this->log($request, 'post.deleted', $post, ['post_id' => $postId, 'owner_id' => $ownerId]);
            $post->delete();
        });

        return back()->with('success', 'تم حذف المنشور المخالف.');
    }

    public function reviewReport(Request $request, ContentReport $report)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['reviewed', 'dismissed'])]]);
        DB::transaction(function () use ($request, $report, $data) {
            $report->update(['status' => $data['status'], 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
            $this->log($request, 'report.'.$data['status'], $report, ['post_id' => $report->post_id]);
        });

        return back()->with('success', 'تمت مراجعة البلاغ.');
    }

    private function log(Request $request, string $action, ?Model $subject = null, array $metadata = []): void
    {
        AdminActivity::create([
            'admin_id' => $request->user()?->id,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata ?: null,
            'ip_address' => $request->ip(),
        ]);
    }

    private function removePostNotifications(int $postId): void
    {
        $postIdExpression = match (DB::connection()->getDriverName()) {
            'sqlite', 'mysql', 'mariadb' => "json_extract(data, '$.post_id') = ?",
            'pgsql' => "(data::jsonb ->> 'post_id') = ?",
            'sqlsrv' => "JSON_VALUE(data, '$.post_id') = ?",
            default => null,
        };

        if ($postIdExpression === null) {
            return;
        }

        DB::table('notifications')
            ->whereIn('type', [NewCommentNotification::class, NewReactionNotification::class, PostSharedNotification::class])
            ->whereRaw($postIdExpression, [$postId])
            ->delete();
    }
}
