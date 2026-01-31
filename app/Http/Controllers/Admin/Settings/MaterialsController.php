<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\MaterialTranslation;

class MaterialsController extends Controller
{
    public function index()
    {
        $materials = Material::with('translations')->get();
        return view('dashboard.admin.settings.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('dashboard.admin.settings.materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.*' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:materials,slug',
        ]);

        $material = Material::create([
            'slug' => $request->slug,
        ]);

        foreach ($request->name as $locale => $name) {
            $material->translations()->create([
                'locale' => $locale,
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.settings.materials.index')->with('success', 'Материал добавлен');
    }

    public function edit(Material $material)
    {
        return view('dashboard.admin.settings.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name.*' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:materials,slug,' . $material->id,
        ]);

        $material->update([
            'slug' => $request->slug,
        ]);

        foreach ($request->name as $locale => $name) {
            $material->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $name]
            );
        }

        return redirect()->route('admin.settings.materials.index')->with('success', 'Материал обновлён');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('admin.settings.materials.index')->with('success', 'Материал удалён');
    }
}
