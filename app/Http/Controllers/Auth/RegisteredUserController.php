<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Supplier;

use App\Domain\Contact\Services\ContactService;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

use App\Domain\Auth\Actions\CreateBuyerBusinessProfileAction;
use App\Domain\Auth\Actions\CreateSupplierBusinessProfileAction;
use App\Domain\Auth\Actions\CreateUserSettingsAction;

use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{

public function __construct(
        protected ContactService $contactService,
        protected CreateBuyerBusinessProfileAction $createBuyerBusinessProfile,
        protected CreateSupplierBusinessProfileAction $createSupplierBusinessProfile,
        protected CreateUserSettingsAction $createUserSettings,
    ) {
    }
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

        $user = DB::transaction(function () use ($request) {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'buyer',
        ]);

        // User settings
        $this->createUserSettings->execute($user);

        // Personal Buyer profile
        $this->createBuyerBusinessProfile->execute($user);

        // Personal Supplier profile
        $this->createSupplierBusinessProfile->execute($user);

        // Personal email contact
        $this->contactService->create([
            'contactable_type' => User::class,
            'contactable_id'   => $user->id,
            'created_by'       => $user->id,

            'type' => 'email',
            'value' => $user->email,
            'label' => 'Personal',

            'is_primary' => true,
            'is_public' => true,
            'show_in_profile' => true,
        ]);

        event(new Registered($user));

        return $user;
    });


        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }



    

}
