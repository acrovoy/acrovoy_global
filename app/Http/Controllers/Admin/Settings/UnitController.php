<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Units index
     */
    public function index(Request $request)
    {
        $query = Unit::query()
            ->with('translations');

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        |
        | Search by:
        | - code
        | - symbol
        | - unit group
        | - translated name
        |
        */

        if ($request->filled('search')) {

            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {

                $q->where('code', 'like', "%{$search}%")

                    ->orWhere('symbol', 'like', "%{$search}%")

                    ->orWhere('unit_group', 'like', "%{$search}%")

                    ->orWhereHas('translations', function ($translationQuery) use ($search) {

                        $translationQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );

                    });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | UNIT GROUP
        |--------------------------------------------------------------------------
        |
        | Blade:
        | name="unit_group"
        |
        */

        if ($request->filled('unit_group')) {

            $query->where(
                'unit_group',
                $request->input('unit_group')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            match ($request->input('status')) {

                'active' => $query->where('is_active', true),

                'inactive' => $query->where('is_active', false),

                default => null,
            };
        }


        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'sort_order',
            'name',
            'code',
            'symbol',
            'unit_group',
            'conversion_factor',
            'created_at',
        ];

        $sort = $request->input(
            'sort',
            'sort_order'
        );

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'sort_order';
        }


        /*
        |--------------------------------------------------------------------------
        | SORT DIRECTION
        |--------------------------------------------------------------------------
        */

        $direction = strtolower(
            $request->input(
                'direction',
                'asc'
            )
        );

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }


        /*
        |--------------------------------------------------------------------------
        | SORT BY TRANSLATED NAME
        |--------------------------------------------------------------------------
        |
        | name находится в unit_translations.
        |
        | Используем subquery, чтобы:
        | - не делать JOIN
        | - не получить дубликаты units
        | - сохранить pagination
        |
        | Берём перевод текущей локали.
        | Если его нет — fallback на первый доступный перевод.
        |
        */

        if ($sort === 'name') {

            $locale = app()->getLocale();

            $query->orderByRaw(
                "
                COALESCE(
                    (
                        SELECT ut.name
                        FROM unit_translations ut
                        WHERE ut.unit_id = units.id
                        AND ut.locale = ?
                        LIMIT 1
                    ),
                    (
                        SELECT ut2.name
                        FROM unit_translations ut2
                        WHERE ut2.unit_id = units.id
                        ORDER BY ut2.id
                        LIMIT 1
                    )
                ) {$direction}
                ",
                [$locale]
            );

        } else {

            $query->orderBy(
                $sort,
                $direction
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SECONDARY SORT
        |--------------------------------------------------------------------------
        |
        | Чтобы порядок был стабильным,
        | особенно при pagination.
        |
        */

        $query->orderBy(
            'id',
            'asc'
        );


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $units = $query
            ->paginate(25)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | UNIT GROUPS
        |--------------------------------------------------------------------------
        |
        | Используются в select фильтра.
        |
        */

        $groups = Unit::query()
            ->whereNotNull('unit_group')
            ->where('unit_group', '!=', '')
            ->distinct()
            ->orderBy('unit_group')
            ->pluck('unit_group');


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard.admin.settings.units.index',
            [
                'units' => $units,
                'groups' => $groups,
                'sort' => $sort,
                'direction' => $direction,
            ]
        );
    }


    public function create()
    {
        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $groups = Unit::query()
            ->whereNotNull('unit_group')
            ->where('unit_group', '!=', '')
            ->distinct()
            ->orderBy('unit_group')
            ->pluck('unit_group');

        return view('dashboard.admin.settings.units.create', [
            'languages' => $languages,
            'groups' => $groups,
        ]);
    }

    public function store(Request $request)
    {
        $languages = Language::query()
            ->where('is_active', true)
            ->pluck('code');

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:units,code',
            ],
            'symbol' => [
                'required',
                'string',
                'max:50',
            ],
            'unit_group' => [
                'required',
                'string',
                'max:50',
            ],
            'conversion_factor' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'conversion_offset' => [
                'required',
                'numeric',
            ],
            'is_base' => [
                'nullable',
                'boolean',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'translations' => [
                'nullable',
                'array',
            ],
            'translations.*' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $unit = Unit::create([
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'unit_group' => $data['unit_group'],
            'conversion_factor' => $data['conversion_factor'],
            'conversion_offset' => $data['conversion_offset'] ?? 0,
            'is_base' => $request->boolean('is_base'),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        foreach ($languages as $locale) {
            $value = $request->input("translations.{$locale}");

            if ($value !== null && trim($value) !== '') {
                $unit->translations()->create([
                    'locale' => $locale,
                    'name' => trim($value),
                ]);
            }
        }

        return redirect()
            ->route('admin.settings.units.index')
            ->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        $unit->load('translations');

        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $groups = Unit::query()
            ->whereNotNull('unit_group')
            ->where('unit_group', '!=', '')
            ->where('id', '!=', $unit->id)
            ->distinct()
            ->orderBy('unit_group')
            ->pluck('unit_group');

        return view('dashboard.admin.settings.units.edit', [
            'unit' => $unit,
            'languages' => $languages,
            'groups' => $groups,
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $languages = Language::query()
            ->where('is_active', true)
            ->pluck('code');

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('units', 'code')->ignore($unit->id),
            ],
            'symbol' => [
                'required',
                'string',
                'max:50',
            ],
            'unit_group' => [
                'required',
                'string',
                'max:50',
            ],
            'conversion_factor' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'conversion_offset' => [
                'required',
                'numeric',
            ],
            'is_base' => [
                'nullable',
                'boolean',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'translations' => [
                'nullable',
                'array',
            ],
            'translations.*' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $unit->update([
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'unit_group' => $data['unit_group'],
            'conversion_factor' => $data['conversion_factor'],
            'conversion_offset' => $data['conversion_offset'] ?? 0,
            'is_base' => $request->boolean('is_base'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        foreach ($languages as $locale) {
            $value = $request->input("translations.{$locale}");

            if ($value !== null && trim($value) !== '') {
                $unit->translations()->updateOrCreate(
                    [
                        'locale' => $locale,
                    ],
                    [
                        'name' => trim($value),
                    ]
                );
            } else {
                $unit->translations()
                    ->where('locale', $locale)
                    ->delete();
            }
        }

        return redirect()
            ->route('admin.settings.units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        $unit->translations()->delete();
        $unit->delete();

        return redirect()
            ->route('admin.settings.units.index')
            ->with('success', 'Unit deleted successfully.');
    }

    public function toggle(Unit $unit)
    {
        $unit->update([
            'is_active' => !$unit->is_active,
        ]);

        return back()->with(
            'success',
            $unit->is_active
                ? 'Unit activated successfully.'
                : 'Unit deactivated successfully.'
        );
    }
}