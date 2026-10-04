<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'لطفاً ایمیل یا شماره موبایل را وارد نمایید.',
            'password.required' => 'لطفاً رمز عبور را وارد نمایید.',
        ]);

        $loginInput = $credentials['login'];
        $password = $credentials['password'];

        // Determine if loginInput is email or phone
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::guard('web')->attempt([$fieldType => $loginInput, 'password' => $password], $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();
            if ($user && $user->phone) {
                $customer = \App\Models\Customer::firstOrCreate(
                    ['phone' => $user->phone],
                    ['name' => $user->name, 'cafe_id' => $user->cafe_id ?? 1]
                );
                Auth::guard('customer')->login($customer, $request->boolean('remember'));
            }
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'خوش آمدید! ورود با موفقیت انجام شد.');
        }

        // Also check if it's a customer with password (if applicable)
        if ($fieldType === 'phone') {
            $customer = \App\Models\Customer::where('phone', $loginInput)->first();
            if ($customer && $customer->password && \Illuminate\Support\Facades\Hash::check($password, $customer->password)) {
                Auth::guard('customer')->login($customer, $request->boolean('remember'));
                $request->session()->regenerate();

                $intended = session()->pull('url.intended');
                $redirectUrl = ($intended && $intended !== url('/')) ? $intended : route('customer.dashboard');
                return redirect($redirectUrl)->with('success', 'خوش آمدید به بی کافه!');
            }
        }

        return back()->withErrors([
            'login' => 'اطلاعات ورود (ایمیل/موبایل یا رمز عبور) نادرست است.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'شما از حساب کاربری خود خارج شدید.');
    }
}
