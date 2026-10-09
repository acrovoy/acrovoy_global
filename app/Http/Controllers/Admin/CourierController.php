<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Courier\Models\Courier;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;


class CourierController extends Controller
{
    /**
     * Display couriers.
     */
    public function index(): View
    {
        $couriers = Courier::query()
            ->with('translations')
            ->ordered()
            ->paginate(20);

        return view('dashboard.admin.couriers.index', compact('couriers'));
    }

    /**
     * Show the create form.
     */
    public function create(): View
    {
        $courier = new Courier();

        return view('dashboard.admin.couriers.create', compact('courier'));
    }

    /**
     * Store a new courier.
     */
    
public function store(Request $request): RedirectResponse
{
    $validated = $this->validateCourier($request);

    \Log::debug('Courier logo upload debug', [
        'has_file' => $request->hasFile('logo'),
        'file_present' => $request->file('logo') !== null,
        'file_valid' => $request->file('logo')?->isValid(),
        'file_name' => $request->file('logo')?->getClientOriginalName(),
        'file_mime' => $request->file('logo')?->getMimeType(),
        'file_size' => $request->file('logo')?->getSize(),
        'upload_error' => $request->file('logo')?->getError(),
        'upload_error_message' => $request->file('logo')?->getErrorMessage(),
        'content_length' => $request->server('CONTENT_LENGTH'),
    ]);

    $logoPath = null;

    try {
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            if (!$file->isValid()) {
                throw new \RuntimeException(
                    'Courier logo upload failed: ' . $file->getErrorMessage()
                );
            }

            $logoPath = $file->store('couriers', 'public');

            \Log::debug('Courier logo storage debug', [
                'logo_path' => $logoPath,
                'disk' => config('filesystems.default'),
                'public_root' => config('filesystems.disks.public.root'),
                'public_url' => config('filesystems.disks.public.url'),
                'file_exists' => $logoPath
                    ? Storage::disk('public')->exists($logoPath)
                    : false,
                'absolute_path' => $logoPath
                    ? Storage::disk('public')->path($logoPath)
                    : null,
            ]);

            if (!$logoPath) {
                throw new \RuntimeException(
                    'Unable to save courier logo to the public disk.'
                );
            }

            if (!Storage::disk('public')->exists($logoPath)) {
                throw new \RuntimeException(
                    'Courier logo was stored but cannot be found on the public disk.'
                );
            }
        }

        $courier = DB::transaction(function () use ($validated, $logoPath) {
            $courier = Courier::create([
                'code' => $validated['code'],
                'logo' => $logoPath,
                'website' => $validated['website'] ?? null,
                'tracking_url' => $validated['tracking_url'] ?? null,
                'is_active' => $validated['is_active'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            $this->syncTranslations(
                $courier,
                $validated['translations'] ?? []
            );

            return $courier;
        });

        \Log::debug('Courier created debug', [
            'courier_id' => $courier->id,
            'courier_code' => $courier->code,
            'logo_path_in_database' => $courier->fresh()->logo,
            'logo_exists_after_create' => $courier->logo
                ? Storage::disk('public')->exists($courier->logo)
                : false,
        ]);
    } catch (Throwable $e) {
        if ($logoPath) {
            Storage::disk('public')->delete($logoPath);
        }

        \Log::error('Courier creation failed', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'logo_path' => $logoPath,
        ]);

        report($e);

        return back()
            ->withInput()
            ->with('error', __('Unable to create courier. Please check the uploaded logo and try again.'));
    }

    return redirect()
        ->route('admin.couriers.index')
        ->with('success', __('Courier created successfully.'));
}



    /**
     * Show the edit form.
     */
    public function edit(Courier $courier): View
    {
        $courier->load('translations');

        return view('dashboard.admin.couriers.edit', compact('courier'));
    }

    /**
     * Update a courier.
     */
    public function update(Request $request, Courier $courier): RedirectResponse
{
    $validated = $this->validateCourier($request, $courier);

    $oldLogo = $courier->logo;
    $newLogo = null;

    try {
        $newLogo = $request->file('logo')?->store('couriers', 'public');

        DB::transaction(function () use ($validated, $courier, $newLogo, $oldLogo) {
            $courier->update([
                'code' => $validated['code'],
                'logo' => $newLogo ?? $oldLogo,
                'website' => $validated['website'] ?? null,
                'tracking_url' => $validated['tracking_url'] ?? null,
                'is_active' => $validated['is_active'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            $this->syncTranslations(
                $courier,
                $validated['translations'] ?? []
            );
        });
    } catch (Throwable $e) {
        if ($newLogo) {
            Storage::disk('public')->delete($newLogo);
        }

        report($e);

        return back()
            ->withInput()
            ->with('error', __('Unable to update courier. Please try again.'));
    }

    if ($newLogo && $oldLogo) {
        Storage::disk('public')->delete($oldLogo);
    }

    return redirect()
        ->route('admin.couriers.index')
        ->with('success', __('Courier updated successfully.'));
}
    /**
     * Delete a courier.
     */
    public function destroy(Courier $courier): RedirectResponse
    {
        try {
            DB::transaction(function () use ($courier) {
                $courier->delete();
            });
        } catch (Throwable $e) {
            report($e);

            return back()
                ->with('error', __('Unable to delete courier. Please try again.'));
        }

        if ($courier->logo && !str_starts_with($courier->logo, 'http')) {
            Storage::disk('public')->delete($courier->logo);
        }

        return redirect()
            ->route('admin.couriers.index')
            ->with('success', __('Courier deleted successfully.'));
    }

    /**
     * Toggle courier activity.
     */
    public function toggleStatus(Courier $courier): RedirectResponse
    {
        $courier->update([
            'is_active' => !$courier->is_active,
        ]);

        return back()->with(
            'success',
            $courier->is_active
                ? __('Courier activated successfully.')
                : __('Courier deactivated successfully.')
        );
    }

    /**
     * Validate courier data.
     */
    private function validateCourier(
        Request $request,
        ?Courier $courier = null
    ): array {
        $locales = ['en', 'es', 'fr', 'ru', 'ar', 'zh', 'tr', 'uk'];

        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('couriers', 'code')->ignore($courier?->id),
            ],

            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'website' => ['nullable', 'url', 'max:255'],
            'tracking_url' => ['nullable', 'url', 'max:255'],

            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            'translations' => ['required', 'array'],
            'translations.*' => ['nullable', 'array'],
            'translations.*.name' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    /**
     * Synchronize courier translations.
     */
    private function syncTranslations(
        Courier $courier,
        array $translations
    ): void {
        $locales = ['en', 'es', 'fr', 'ru', 'ar', 'zh', 'tr', 'uk'];

        foreach ($locales as $locale) {
            $translation = $translations[$locale] ?? [];

            $name = trim($translation['name'] ?? '');
            $description = trim($translation['description'] ?? '');

            if ($name === '' && $description === '') {
                $courier->translations()
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            if ($name === '') {
                continue;
            }

            $courier->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $name,
                    'description' => $description !== '' ? $description : null,
                ]
            );
        }
    }
}