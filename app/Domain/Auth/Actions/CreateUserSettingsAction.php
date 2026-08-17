<?php

namespace App\Domain\Auth\Actions;

use App\Models\User;
use App\Models\UserSetting;

class CreateUserSettingsAction
{
    public function execute(User $user): void
    {
        UserSetting::setValue(
            $user->id,
            'platform_mode',
            'buyer'
        );
    }
}