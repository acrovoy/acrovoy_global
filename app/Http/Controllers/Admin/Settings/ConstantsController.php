<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings\Constant;




class ConstantsController extends Controller
{
    public function index()
    {
        // Получаем список констант
        $constants = Constant::all();
        return view('dashboard.admin.settings.constants.index', compact('constants'));
    }

    public function create()
    {
        return view('dashboard.admin.settings.constants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        Constant::create($request->all());

        return redirect()->route('admin.settings.constants.index')->with('success', 'Константа добавлена');
    }

    public function edit($id)
    {
        $constant = Constant::findOrFail($id);
        return view('dashboard.admin.settings.constants.edit', compact('constant'));
    }

    public function update(Request $request, $id)
    {
        $constant = Constant::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        $constant->update($request->all());

        return redirect()->route('admin.settings.constants.index')->with('success', 'Константа обновлена');
    }

    public function destroy($id)
    {
        $constant = Constant::findOrFail($id);
        $constant->delete();

        return redirect()->route('admin.settings.constants.index')->with('success', 'Константа удалена');
    }
}
