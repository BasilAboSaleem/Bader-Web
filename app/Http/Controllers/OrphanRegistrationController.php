<?php

namespace App\Http\Controllers;

use App\Models\OrphanFamily;
use App\Support\OrphanFamilyInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrphanRegistrationController extends Controller
{
    public function create(): View
    {
        return view('pages.orphan-registration', [
            'family' => new OrphanFamily(['type' => OrphanFamily::TYPE_ORPHAN]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(OrphanFamilyInput::rules(documentsRequired: true), [], OrphanFamilyInput::attributes());

        DB::transaction(function () use ($request, $validated): void {
            $family = OrphanFamily::create([
                ...OrphanFamilyInput::familyAttributes($validated),
                'source' => OrphanFamily::SOURCE_WEBSITE,
                'review_status' => OrphanFamily::REVIEW_PENDING,
            ]);

            OrphanFamilyInput::syncOrphans($family, $validated['orphans']);
            OrphanFamilyInput::storeDocuments($request, $family);
        });

        return redirect()->route('orphan-registration')->with('orphan_registered', true);
    }
}
