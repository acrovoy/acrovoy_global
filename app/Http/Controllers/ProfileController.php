<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Domain\Media\Services\MediaService;
use App\Domain\Media\Jobs\DeleteMediaJob;
use App\Domain\Media\DTO\UploadMediaDTO;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(
    ProfileUpdateRequest $request,
    MediaService $mediaService
): RedirectResponse
{



    $user = $request->user();

    // 🔹 Запоминаем старую роль
    $oldRole = $user->role;

    // 1️⃣ Заполняем все обычные поля, которые прошли валидацию
    $user->fill($request->validated());

    // 2️⃣ Если email изменился — сбрасываем верификацию
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    // 3️⃣ Обрабатываем изменение роли
    if ($request->filled('role')) {
        $newRole = $request->input('role');

        // Разрешаем только допустимые роли для обычного пользователя
        if (in_array($newRole, ['buyer', 'manufacturer'])) {
            $user->role = $newRole;

            // Если роль стала manufacturer и supplier ещё нет — создаём запись
            if ($newRole === 'manufacturer' && !$user->supplier) {
                \App\Models\Supplier::create([
                    'user_id' => $user->id,
                    'name' => 'Company Name',
                ]);
            }
        }
    }

    // 4️⃣ Сохраняем пользователя
    $user->save();

    // 6️⃣ Обработка аватара
if ($request->hasFile('avatar')) {

    // Удаляем старую аватарку
    $oldAvatar = $user->media()
        ->where('collection', 'avatars')
        ->first();

    if ($oldAvatar) {
        DeleteMediaJob::dispatch($oldAvatar->uuid);
    }

    $file = $request->file('avatar');
    
    // Загружаем новую
    $dto = new UploadMediaDTO(
    file: $file,
    model: $user,
    collection: 'avatars',
    private: false,
    mediaRole: 'avatar',
    originalFileName: $file?->getClientOriginalName(),
    sortOrder: 0,      
    isMain: true       
);

    $mediaService->upload($dto);
}


    // ✅ 5️⃣ Если роль реально изменилась — обновляем UI-сессию
    if ($oldRole !== $user->role) {
        session(['dashboard_role' => $user->role]);
    }

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}



    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
