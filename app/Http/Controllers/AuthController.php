<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('form-login');
    }

    public function change_password()
    {
        return view('ubah_password');
    }

    public function update_password(Request $request){
        $request->validate([
            'password_lama' => ['required', 'current_password'], 
            'password_baru' => ['required', 'string', 'min:8'],
            'komfirm_password_baru'   => ['required', 'same:password_baru'],
        ]);

        // Update password
        $user = auth()->user();
        $user->password = bcrypt($request->password_baru);
        $user->save(); // <-- WAJIB ADA!

        return response()->json(['status'=>'success', 'messages'=>'Password berhasil diubah.'], 201);
    }

    // Proses login
    public function login(Request $request) {
        $validates 	= [
            "username"  => "required",
            "password"  => "required",
        ];
        $validation = Validator::make($request->all(), $validates);
        if($validation->fails()) {
            return response()->json([
                "status"    => "warning",
                "messages"   => $validation->errors()->first()
            ], 422);
        }
        
        try {
            $credentials = [
                'username' => $request->username,
                'password' => $request->password,
            ];
            if (!$login = auth()->attempt($credentials)) {
                return response()->json(['status' => 'warning','messages' => 'Username atau password yang anda masukkan tidak benar!'], 401);
            }
            return response()->json(['status'=>'success', 'messages'=>'Proses login yang kamu lakukan berhasil.'], 201);
        } catch(QueryException $e) { 
            return response()->json(['status'=>'error','messages'=> $e->errorInfo ], 500);
        }  
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
