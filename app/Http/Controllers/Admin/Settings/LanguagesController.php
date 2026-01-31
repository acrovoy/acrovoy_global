<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Language;

class LanguagesController extends Controller
{
    public function index()
    {
        $languages = Language::orderBy('sort_order')->get();

        return view('dashboard.admin.settings.languages.index', compact('languages'));
    }

    public function show(Language $language)
    {
        return view('dashboard.admin.settings.languages.show', compact('language'));
    }

    public function create()
    {
        return view('dashboard.admin.settings.languages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:5|unique:languages,code,' . ($language->id ?? ''),
            'name' => 'required|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'locale' => 'nullable|string|max:10',
            'direction' => 'in:ltr,rtl',
            'priority' => 'in:core,high,medium,low',
            'sort_order' => 'integer|min:1',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        Language::create([
            'code' => strtolower($request->code),
            'name' => $request->name,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.settings.languages.index')
            ->with('success', 'Language added');
    }

    public function edit(Language $language)
    {
        return view('dashboard.admin.settings.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $request->validate([
            'code' => 'required|string|max:5|unique:languages,code,' . ($language->id ?? ''),
            'name' => 'required|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'locale' => 'nullable|string|max:10',
            'direction' => 'in:ltr,rtl',
            'priority' => 'in:core,high,medium,low',
            'sort_order' => 'integer|min:1',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->update([
            'code' => strtolower($request->code),
            'name' => $request->name,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.settings.languages.index')
            ->with('success', 'Language updated');
    }

    public function destroy(Language $language)
    {
        // ❗ защита: нельзя удалить активный текущий язык
        if ($language->code === config('app.locale')) {
            return back()->with('error', 'Default language cannot be removed');
        }

        $language->delete();

        return back()->with('success', 'Language deleted');
    }
}
