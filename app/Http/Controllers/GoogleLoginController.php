<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Socialite;
use App\User;


class GoogleLoginController extends Controller
{
    //
    public function redirectToGoogle()
    {
        //googleの認証ページヘユーザーをリダイレクト
        return Socialite::driver('google')->redirect();
    }
    // Google 認証後の処理
    public function handleGoogleCallback()
    {
        // googleのユーザー情報を取得
        $googleUser = Socialite::driver('google')->stateless()->user();
        // emailが一致するユーザをDBから取得
        $user = User::where('email', $googleUser->email)->first();
        // DBに該当するユーザがなければ新しくユーザを作成
        if ($user == null) {
            $user = $this->createUser($googleUser);
        }

        // 作成したユーザでログイン処理
        Auth::login($user, true);

        // /homeへ
        return redirect('/home');
    }
    public function createUser($googleUser)
    {
        $user = User::create([
            'name'     => $googleUser->name,
            'email'    => $googleUser->email,
            'password' => Hash::make(uniqid()),
        ]);
        return $user;
    }
}
