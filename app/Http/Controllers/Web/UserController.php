<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'staff'); // staff, customers
        $search = $request->input('search');
        $role = $request->input('role');
        $cafeId = auth()->user()->cafe_id ?? 1;

        $counts = [
            'staff' => User::where('cafe_id', $cafeId)->count(),
            'customers' => \App\Models\Customer::where('cafe_id', $cafeId)->count(),
        ];

        if ($tab === 'customers') {
            $query = \App\Models\Customer::where('cafe_id', $cafeId)->with(['tier', 'activeSubscription.plan']);
            if ($search) {
                $normalizedSearch = \App\Services\CustomerService::normalizePersianText($search);
                $cleanPhone = preg_replace('/[^0-9]/', '', $search);

                $query->where(function ($q) use ($search, $normalizedSearch, $cleanPhone) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$normalizedSearch}%");

                    if (!empty($cleanPhone)) {
                        $q->orWhere('phone', 'like', "%{$cleanPhone}%");
                    } else {
                        $q->orWhere('phone', 'like', "%{$search}%");
                    }
                });
            }
            $customers = $query->latest()->paginate(15)->withQueryString();
            return view('admin.users.index', compact('tab', 'customers', 'counts', 'search', 'role'));
        }

        // Default: Staff / Admin users
        $query = User::where('cafe_id', $cafeId);

        if ($role) {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('tab', 'users', 'counts', 'search', 'role'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff,cashier'],
        ], [
            'name.required' => 'نام الزامی است.',
            'phone.required' => 'شماره تماس الزامی است.',
            'phone.unique' => 'این شماره تماس قبلاً ثبت شده است.',
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.required' => 'رمز عبور الزامی است.',
            'password.min' => 'رمز عبور حداقل باید ۶ کاراکتر باشد.',
            'role.required' => 'نقش کاربری الزامی است.',
        ]);

        User::create([
            'cafe_id' => $cafeId,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'کاربر جدید با موفقیت ایجاد گردید.');
    }

    public function edit(User $user)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        abort_if($user->cafe_id !== $cafeId, 403, 'دسترسی غیرمجاز.');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        abort_if($user->cafe_id !== $cafeId, 403, 'دسترسی غیرمجاز.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff,cashier'],
        ], [
            'name.required' => 'نام الزامی است.',
            'phone.required' => 'شماره تماس الزامی است.',
            'phone.unique' => 'این شماره تماس قبلاً ثبت شده است.',
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.min' => 'رمز عبور حداقل باید ۶ کاراکتر باشد.',
            'role.required' => 'نقش کاربری الزامی است.',
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', "مشخصات کاربر {$user->name} با موفقیت به‌روزرسانی شد.");
    }

    public function destroy(User $user)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        abort_if($user->cafe_id !== $cafeId, 403, 'دسترسی غیرمجاز.');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'نمی‌توانید حساب کاربری خودتان را حذف کنید.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'کاربر با موفقیت حذف گردید.');
    }
}
