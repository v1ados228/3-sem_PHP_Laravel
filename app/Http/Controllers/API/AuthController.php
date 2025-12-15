<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Информационный эндпоинт вместо формы регистрации.
     */
    public function showRegister()
    {
        return response()->json([
            'message' => 'Используйте POST /api/auth/register для регистрации',
        ]);
    }
    
    /**
     * Регистрация нового пользователя.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Поле имя обязательно для заполнения',
            'email.required' => 'Поле email обязательно для заполнения',
            'email.email' => 'Email должен быть корректным адресом',
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.required' => 'Поле пароль обязательно для заполнения',
            'password.min' => 'Пароль должен содержать минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        return response()->json([
            'message' => 'Регистрация успешна! Теперь вы можете войти.',
            'user' => $user,
        ], 201);
    }
    
    /**
     * Информационный эндпоинт вместо формы логина.
     */
    public function showLogin()
    {
        return response()->json([
            'message' => 'Используйте POST /api/auth/login для входа',
        ]);
    }
    
    /**
     * Авторизация пользователя и выдача токена Sanctum.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Поле email обязательно для заполнения',
            'email.email' => 'Email должен быть корректным адресом',
            'password.required' => 'Поле пароль обязательно для заполнения',
        ]);
        
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['Неверный email или пароль.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;
        
        return response()->json([
            'message' => 'Вы успешно вошли в систему!',
            'token' => $token,
            'user' => $user,
        ]);
    }
    
    /**
     * Выход пользователя и отзыв токенов.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Вы успешно вышли из системы.',
        ]);
    }
}
