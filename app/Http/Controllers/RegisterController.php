<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use App\SiteSetting;

class RegisterController extends Controller
{
    //
    public function reg (Request $request)
        {
            abort_unless(SiteSetting::getValue('registration_enabled', true), 403, 'التسجيل مغلق مؤقتًا.');
            $request->merge(['emailsignup' => Str::lower(trim((string) $request->input('emailsignup')))]);
            $validate = $request->validate([
                                    'firstname'=>'required|string|max:50',
                                    'lastname'=>'required|string|max:50',
                                    'emailsignup'=>'required|string|email|max:255|unique:users,email',
                                    'passwordsignup'=>'required|string|min:8|max:72',
                                    'passwordsignup_confirm'=>'required|string|same:passwordsignup',
                                 ]);
                                 
            $user   =  User::create([
                'first_name'=>$validate['firstname'],
                'last_name'=>$validate['lastname'],
                'email'=>Str::lower($validate['emailsignup']),
                'password'=>bcrypt($validate['passwordsignup']),
                'image'=>0,
                'profile_photo_id'=>null,
                'cover_photo_id'=>null,
             ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');


}


}
