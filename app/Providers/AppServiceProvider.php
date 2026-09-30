<?php

namespace App\Providers;

use App\SiteSetting;
use App\Block;
use App\Friend;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('site_settings')) {
            config(['app.name' => SiteSetting::getValue('site_name', config('app.name'))]);
        }

        View::composer('*', function ($view) {
            $settings = Schema::hasTable('site_settings')
                ? SiteSetting::allWithDefaults()
                : SiteSetting::DEFAULTS;
            $view->with('siteSettings', $settings);
        });

        View::composer('layout.right_sb', function ($view) {
            $contacts = collect();
            $viewer = auth()->user();

            if ($viewer) {
                $blockedIds = Block::where('user_id', $viewer->id)
                    ->orWhere('blocked_id', $viewer->id)
                    ->get(['user_id', 'blocked_id'])
                    ->map(fn ($block) => (int) ((int) $block->user_id === (int) $viewer->id ? $block->blocked_id : $block->user_id))
                    ->unique()
                    ->values();

                $friendIds = Friend::where('state', true)
                    ->where(fn ($query) => $query->where('user_id', $viewer->id)->orWhere('friends_id', $viewer->id))
                    ->get(['user_id', 'friends_id'])
                    ->map(fn ($friend) => (int) ((int) $friend->user_id === (int) $viewer->id ? $friend->friends_id : $friend->user_id))
                    ->reject(fn ($id) => $id === (int) $viewer->id || $blockedIds->contains($id))
                    ->unique()
                    ->values();

                if ($friendIds->isNotEmpty()) {
                    $contacts = User::with('photopro')
                        ->whereIn('id', $friendIds)
                        ->where('is_active', true)
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->limit(20)
                        ->get();
                }
            }

            $view->with('sidebarContacts', $contacts);
        });
    }
}
