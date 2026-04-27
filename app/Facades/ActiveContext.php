<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void resolve()
 * @method static bool isPersonal()
 * @method static bool isCompany()
 * @method static mixed user()
 * @method static mixed id()
 * @method static mixed type()
 * @method static mixed role()
 * @method static string mode()
 */
class ActiveContext extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'active-context';
    }
}