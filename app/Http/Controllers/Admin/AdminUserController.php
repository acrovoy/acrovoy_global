<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Buyer;
use App\Models\Supplier;


class AdminUserController extends Controller
{
    public function index(Request $request)
{
    $search = trim($request->get('search'));

    $sort = $request->get('sort', 'id');
    $direction = $request->get('direction', 'desc');

    $allowedSorts = [
        'id',
        'name',
        'email',
        'is_blocked',
        'created_at',
    ];

    if (! in_array($sort, $allowedSorts)) {
        $sort = 'id';
    }

    if (! in_array($direction, ['asc', 'desc'])) {
        $direction = 'desc';
    }

    $users = User::query()

        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");

            });

        })

        ->orderBy($sort, $direction)

        ->get();

    return view(
        'dashboard.admin.users.index',
        compact(
            'users',
            'search',
            'sort',
            'direction'
        )
    );
}

    public function show(User $user)
{
    $user->load([
        'addresses.countryLocation',
        'addresses.regionLocation',
        'defaultAddress',
        'premiumPlan',
        'buyerPremiumPlan',
        'companyMemberships.company',
        'settings',
    ]);

    return view('dashboard.admin.users.show', compact('user'));
}

    /**
     * Блокировка/разблокировка пользователя (тестовая логика).
     */
    public function toggleBlock(User $user)
    {
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        return redirect()->back()->with('success', 'User status updated.');
    }

    /**
     * Удаление пользователя (тестовая логика).
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted.');
    }

    /**
     * Форма редактирования пользователя (тестовая логика).
     */
    public function edit(User $user)
    {
        return view('dashboard.admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->only(['name', 'email']));
        return redirect()->route('dashboard.admin.users.index')->with('success', 'User updated.');
    }
}
