<?php

namespace App\Http\Controllers\Admin\SupplierLead;

use App\Domain\SupplierLead\Models\SupplierLead;
use App\Domain\SupplierLead\Models\SupplierLeadContact;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;



class SupplierLeadContactController extends Controller
{

public function show(
    SupplierLead $supplierLead,
    SupplierLeadContact $contact
): View {
    $contact = $supplierLead->contacts()
        ->whereKey($contact->id)
        ->firstOrFail();

    return view('dashboard.admin.supplier-leads.contacts.show', [
        'supplierLead' => $supplierLead,
        'contact' => $contact,
    ]);
}

    public function create(SupplierLead $supplierLead): View
    {
        return view('dashboard.admin.supplier-leads.contacts.form', [
            'supplierLead' => $supplierLead,
            'contact' => new SupplierLeadContact(),
            'isEditing' => false,
        ]);
    }

    public function store(Request $request, SupplierLead $supplierLead): RedirectResponse
    {
        $validated = $this->validateContact($request);

        DB::transaction(function () use ($supplierLead, $validated) {
            if ($validated['is_primary']) {
                $supplierLead->contacts()->update(['is_primary' => false]);
            }

            $supplierLead->contacts()->create($validated);
        });

        return redirect()
            ->route('admin.supplier-leads.show', $supplierLead)
            ->with('success', 'Contact added successfully.');
    }

    public function edit(
        SupplierLead $supplierLead,
        SupplierLeadContact $contact
    ): View {
        $contact = $supplierLead->contacts()
            ->whereKey($contact->id)
            ->firstOrFail();

        return view('dashboard.admin.supplier-leads.contacts.form', [
            'supplierLead' => $supplierLead,
            'contact' => $contact,
            'isEditing' => true,
        ]);
    }

    public function update(
        Request $request,
        SupplierLead $supplierLead,
        SupplierLeadContact $contact
    ): RedirectResponse {
        $contact = $supplierLead->contacts()
            ->whereKey($contact->id)
            ->firstOrFail();

        $validated = $this->validateContact($request);

        DB::transaction(function () use ($supplierLead, $contact, $validated) {
            if ($validated['is_primary']) {
                $supplierLead->contacts()
    ->where('id', '!=', $contact->id)
    ->update(['is_primary' => false]);
            }

            $contact->update($validated);
        });

        return redirect()
            ->route('admin.supplier-leads.show', $supplierLead)
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy(
        SupplierLead $supplierLead,
        SupplierLeadContact $contact
    ): RedirectResponse {
        $contact = $supplierLead->contacts()
            ->whereKey($contact->id)
            ->firstOrFail();

        $contact->delete();

        return redirect()
            ->route('admin.supplier-leads.show', $supplierLead)
            ->with('success', 'Contact deleted successfully.');
    }

    private function validateContact(Request $request): array
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:100'],
            'telegram' => ['nullable', 'string', 'max:100'],
            'linkedin_url' => ['nullable', 'url', 'max:2048'],
            'is_primary' => ['sometimes', 'boolean'],
            'is_decision_maker' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_primary'] = $request->boolean('is_primary');
        $validated['is_decision_maker'] = $request->boolean('is_decision_maker');
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}