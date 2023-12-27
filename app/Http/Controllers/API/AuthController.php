<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');
    $token = Auth::attempt($credentials);

    if (!$token) {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }

    $user = Auth::user();
    $profile_photo_url = null;

    if ($user->profile_photo) {
        $profile_photo_url = url('images/' . $user->profile_photo);
    }

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => $profile_photo_url,
        ],
        'authorization' => [
            'token' => $token,
            'type' => 'bearer',
        ]
    ]);
}

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
{
    $user = Auth::user();

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => $user->profile_photo,
        ],
        'authorization' => [
            'token' => Auth::refresh(),
            'type' => 'bearer',
        ]
    ]);
}

    public function saveUserInfo(Request $request){
        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $photo='';
        if($request->photo!=''){
        $photo = time().'.jpg';
        file_put_contents('storage/profiles/'.$photo,base64_decode($request->photo));
        $user->photo = $photo;
        }
        $user->update();
        return response()->json([
            'success'=>true,
            'photo'=>$photo
        ]);
    }

    
}
