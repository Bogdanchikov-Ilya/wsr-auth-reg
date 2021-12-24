<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;


class UserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        $user = User::where(['email' => $request->email])->first();
        if(!$user){
            return User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        }else {
            return 'Такой пользователь уже существует';
        }
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        $user = User::where(['email' => $validated['email']])->first();
        $password = Hash::check($validated['password'], $user->password);
        if($user && $password){
            $user->remember_token = Str::random(100);
            $user->save();
            return ['email' => $user->email, 'token' => $user->remember_token];
        } else {
            return ['Неверная почта или пароль'];
        }

    }
}
