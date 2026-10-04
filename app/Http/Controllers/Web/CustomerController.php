<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $customerService) {}

    public function index(Request $request)
    {
        $cafeId = auth()->user()->cafe_id;
        $search = $request->query('search');

        $query = Customer::where('cafe_id', $cafeId);

        if ($search) {
            $normalizedSearch = \App\Services\CustomerService::normalizePersianText($search);
            $cleanPhone = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $normalizedSearch, $cleanPhone) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$normalizedSearch}%")
                    ->orWhere('email', 'like', "%{$search}%");

                if (!empty($cleanPhone)) {
                    $q->orWhere('phone', 'like', "%{$cleanPhone}%");
                } else {
                    $q->orWhere('phone', 'like', "%{$search}%");
                }
            });
        }

        $customers = $query->with(['subscriptions' => function ($q) {
            $q->where('status', 'active');
        }])->latest()->paginate(15);

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $cafeId = auth()->user()->cafe_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => "required|string|max:20|unique:customers,phone,NULL,id,cafe_id,{$cafeId}",
            'email' => 'nullable|email|max:255',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'نام مشتری الزامی است.',
            'phone.required' => 'شماره موبایل الزامی است.',
            'phone.unique' => 'این شماره موبایل قبلاً برای مشتری دیگری ثبت شده است.',
        ]);

        $customer = $this->customerService->createCustomer($cafeId, $validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'مشتری جدید با موفقیت ثبت گردید.');
    }

    public function show(Customer $customer)
    {
        if ($customer->cafe_id !== auth()->user()->cafe_id) {
            abort(403);
        }

        $profile = $this->customerService->getCustomerProfile($customer);

        return view('customers.show', $profile);
    }

    public function edit(Customer $customer)
    {
        if ($customer->cafe_id !== auth()->user()->cafe_id) {
            abort(403);
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        if ($customer->cafe_id !== auth()->user()->cafe_id) {
            abort(403);
        }

        $cafeId = auth()->user()->cafe_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => "required|string|max:20|unique:customers,phone,{$customer->id},id,cafe_id,{$cafeId}",
            'email' => 'nullable|email|max:255',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ]);

        $this->customerService->updateCustomer($customer, $validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'اطلاعات مشتری با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->cafe_id !== auth()->user()->cafe_id) {
            abort(403);
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'مشتری و تمام اطلاعات مربوطه با موفقیت حذف گردید.');
    }
}
