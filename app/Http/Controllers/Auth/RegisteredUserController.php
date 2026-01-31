<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Supplier;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'buyer',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    public function createManufacturer(): View
    {
        return view('auth.register-manufacturer');
    }

    public function storeManufacturer(Request $request): RedirectResponse
{
    // Валидация формы
    $request->validate([
        'name' => ['required', 'string', 'max:255'], // название компании
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    \DB::transaction(function () use ($request) {

        $user = User::create([
            'name' => 'Your name',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'manufacturer',
        ]);

        $supplier = new Supplier([
            'name' => $request->name, // название компании
        ]);

        $supplier->user()->associate($user); // теперь Eloquent знает связь
        $supplier->slug = Str::slug($supplier->name, '-');
        $supplier->save();

        event(new Registered($user));
        Auth::login($user);
    });

    return redirect(RouteServiceProvider::HOME);
}

    

}
