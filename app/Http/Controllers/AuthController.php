<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Document;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $user = User::create($request->only(['name', 'email', 'password', 'phone']));
        if ($user) {
            $user->token = $user->createToken('api-token')->plainTextToken;
            $userFolder = 'uploads/' . $user->id;
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true);
            }
            $documents = [];
            foreach ($request->documents as $file) {
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->move($userFolder, $fileName);

                $documents[] = [
                    'path' => $fileName,
                    'extension' => strtolower($file->getClientOriginalExtension()),
                ];
            }
            $subjects = [];
            foreach ($request->subjects as $subject) {
                $subjects[] = [
                    'subject' => $subject,
                ];
            }
            DB::transaction(function () use ($user, $documents, $subjects) {
                $user->documents()->createMany($documents);
                $user->subjects()->createMany($subjects);
            });
            return response()->json(['message' => 'User registered successfully', 'data' => $user], 200);
        }
        return response()->json(['message' => 'Something went wrong'], 400);
    }
    public function login(loginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->token = $user->createToken('api-token')->plainTextToken;

            return response()->json(['message' => 'Login successful', 'data' => $user], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }
}
