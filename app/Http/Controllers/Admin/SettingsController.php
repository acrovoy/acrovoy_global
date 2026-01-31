<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    public function index()
    {
        return view('dashboard.admin.settings.index');
    }

    public function constants()
    {
        return view('dashboard.admin.settings.constants');
    }

    public function units()
    {
        return view('dashboard.admin.settings.units');
    }
}
