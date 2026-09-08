<?php

namespace App\Http\Controllers;

use App\Block;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $blockedUsers = Block::with('blockedUser.photopro')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $profile = [
            'photopro' => $user->photopro,
            'profile_photo_id' => $user->profile_photo_id,
        ];

        return view('settings', compact('user', 'blockedUsers', 'profile'));
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'about' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'تم تحديث معلومات الحساب بنجاح.',
                'user' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'about' => $user->about,
                ],
            ]);
        }

        return back()->with('success', 'تم تحديث معلومات الحساب بنجاح.')->with('active_tab', 'general');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'كلمة المرور الحالية غير صحيحة.',
                ], 422);
            }
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.'])->with('active_tab', 'security');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'تم تغيير كلمة المرور بنجاح.',
            ]);
        }

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح.')->with('active_tab', 'security');
    }

    public function updatePrivacy(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'default_post_visibility' => 'required|in:public,friends,only_me',
        ]);

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'تم حفظ تفضيلات الخصوصية بنجاح.',
                'default_post_visibility' => $user->default_post_visibility,
            ]);
        }

        return back()->with('success', 'تم حفظ تفضيلات الخصوصية بنجاح.')->with('active_tab', 'privacy');
    }

    public function updatePreferences(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'dark_mode' => 'nullable|boolean',
            'locale' => 'nullable|in:ar,en',
        ]);

        if ($request->has('dark_mode')) {
            $user->update(['dark_mode' => (bool) $request->dark_mode]);
        }

        if ($request->filled('locale')) {
            session(['locale' => $request->locale]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'تم تحديث التفضيلات بنجاح.',
                'dark_mode' => $user->dark_mode,
                'locale' => session('locale', 'ar'),
            ]);
        }

        return back()->with('success', 'تم تحديث التفضيلات بنجاح.')->with('active_tab', 'display');
    }
}
