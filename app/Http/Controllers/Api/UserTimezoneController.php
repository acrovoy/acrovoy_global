<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTimezoneController extends Controller
{
    public function update(Request $request)
    {
        // Валидация
        $data = $request->validate([
            'timezone' => 'required|string|timezone',
        ]);

        $user = Auth::user();
        $user->timezone = $data['timezone'];
        $user->save();

        return response()->json([
            'message' => 'Timezone updated successfully',
            'timezone' => $user->timezone
        ]);
    }
}