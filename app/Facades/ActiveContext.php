<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void resolve()
 *
 * @method static bool isPersonal()
 * @method static bool isCompany()
 * @method static bool isGuest()
 *
 * @method static \App\Models\User|null user()
 *
 * @method static int|null id()
 * @method static string|null type()
 * @method static string|null role()
 *
 * @method static string mode() // 'personal'|'company'|'guest'
 * @method static \Illuminate\Database\Eloquent\Model|null company()
 */
class ActiveContext extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'active-context';
    }
}