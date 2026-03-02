<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManufacturingCapability;

class ManufacturingCapabilityController extends Controller
{
    public function index()
    {
        $capabilities = ManufacturingCapability::with('translations')
            ->ordered()
            ->get();

        return view(
            'dashboard.admin.settings.manufacturing-capabilities.index',
            compact('capabilities')
        );
    }

    public function create()
    {
        $languages = \App\Models\Language::where('is_active', 1)->get();
        return view(
            'dashboard.admin.settings.manufacturing-capabilities.create', compact('languages')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:manufacturing_capabilities,slug',
            'name.*' => 'required'
        ]);

        $capability = ManufacturingCapability::create([
            'slug' => $request->slug,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'visibility_flag' => $request->boolean('visibility_flag', true)
        ]);

        foreach ($request->name as $locale => $name) {
            $capability->translations()->create([
                'locale' => $locale,
                'name' => $name
            ]);
        }

        return redirect()
            ->route('admin.settings.manufacturing-capabilities.index')
            ->with('success', 'Capability created');
    }

    public function edit(ManufacturingCapability $capability)
    {
        $languages = \App\Models\Language::where('is_active', 1)->get();
        $capability->load('translations');
        return view(
            'dashboard.admin.settings.manufacturing-capabilities.edit',
            compact('capability', 'languages')
        );
    }

    public function update(Request $request, ManufacturingCapability $capability)
    {
        $request->validate([
            'slug' => 'required|unique:manufacturing_capabilities,slug,' . $capability->id,
            'name.*' => 'required'
        ]);

        $capability->update([
            'slug' => $request->slug,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'visibility_flag' => $request->boolean('visibility_flag', true)
        ]);

        foreach ($request->name as $locale => $name) {
            $capability->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $name]
            );
        }

        return redirect()
            ->route('admin.settings.manufacturing-capabilities.index')
            ->with('success', 'Updated');
    }

    public function destroy(ManufacturingCapability $capability)
    {
        $capability->delete();

        return back()->with('success', 'Deleted');
    }
}
