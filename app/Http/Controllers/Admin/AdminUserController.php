<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all(); // берем всех пользователей
        return view('dashboard.admin.users.index', compact('users'));
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
