<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Показать форму регистрации
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Обработка регистрации нового пользователя
     */
    public function register(Request $request)
    {
        // Валидация данных
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

        // Создание нового пользователя
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Редирект на форму авторизации с сообщением
        return redirect()->route('auth.login.show')
            ->with('message', 'Регистрация успешна! Теперь вы можете войти.');
    }

    /**
     * Показать форму авторизации
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Обработка авторизации пользователя
     */
    public function login(Request $request)
    {
        // Валидация данных
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Поле email обязательно для заполнения',
            'email.email' => 'Email должен быть корректным адресом',
            'password.required' => 'Поле пароль обязательно для заполнения',
        ]);

        // Попытка аутентификации
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Создание токена Sanctum для пользователя
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            // Сохранение токена в сессии (опционально, для веб-приложения)
            $request->session()->put('sanctum_token', $token);

            // Редирект на главную страницу (обход middleware auth)
            return redirect()->route('main.index')
                ->with('message', 'Вы успешно вошли в систему!');
        }

        // Если аутентификация не удалась
        throw ValidationException::withMessages([
            'email' => ['Неверный email или пароль.'],
        ]);
    }

    /**
     * Выход пользователя из системы
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Удаление всех токенов пользователя
            $user->tokens()->delete();
        }

        // Выход из системы (удаление сессии)
        Auth::logout();

        // Аннулирование сессии
        $request->session()->invalidate();

        // Обновление CSRF токена
        $request->session()->regenerateToken();

        // Редирект на главную страницу
        return redirect()->route('main.index')
            ->with('message', 'Вы успешно вышли из системы.');
    }
}
