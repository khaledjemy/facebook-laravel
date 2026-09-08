<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    //
    public function reg (Request $request)
        {
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

//dd($user);

        Auth::login($user);

    
     return redirect('/profile/'.$user->id);


}


}
