<?php

namespace App\Http\Controllers;

use App\Domain\Contact\Models\Contact;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactDrawerController extends Controller
{
    /**
     * Display the contact manager drawer.
     */
    public function index(Request $request): Response
    {
        $request->validate([
            'type' => ['required', 'string'],
            'id'   => ['required', 'integer'],
        ]);

        $owner = match ($request->string('type')->value()) {

            'user' => User::query()
                ->with('contacts')
                ->findOrFail($request->integer('id')),

            'supplier' => Supplier::query()
                ->with('contacts')
                ->findOrFail($request->integer('id')),

            default => abort(404, 'Unsupported contact owner type.'),
        };

        return response()->view('contacts.drawers.index', [
            'owner'    => $owner,
            'contacts' => $owner->contacts()->orderBy('sort_order')->get(),
        ]);
    }
}