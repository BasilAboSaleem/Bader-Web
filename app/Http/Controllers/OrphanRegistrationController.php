<?php

namespace App\Http\Controllers;

use App\Models\OrphanFamily;
use App\Support\OrphanFamilyInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class OrphanRegistrationController extends Controller
{
    /**
     * Hidden field that people never see; bots that fill every input give themselves away.
     */
    public const HONEYPOT_FIELD = 'website';

    public function create(): View
    {
        return view('pages.orphan-registration', [
            'family' => new OrphanFamily(['type' => OrphanFamily::TYPE_ORPHAN]),
            'submissionToken' => (string) Str::uuid(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->filled(self::HONEYPOT_FIELD)) {
            return $this->registered();
        }

        $validated = $request->validate(OrphanFamilyInput::rules(documentsRequired: true), [], OrphanFamilyInput::attributes());

        /*
         * Each page load carries its own token, so a double tap or a resend on a slow connection
         * saves the family once while the visitor still lands on the success message.
         */
        $tokenKey = Str::isUuid($token = (string) $request->input('submission_token')) ? 'orphan-registration:'.$token : null;

        if ($tokenKey && ! Cache::add($tokenKey, true, now()->addDay())) {
            return $this->registered();
        }

        try {
            DB::transaction(function () use ($request, $validated): void {
                $family = OrphanFamily::create([
                    ...OrphanFamilyInput::familyAttributes($validated),
                    'source' => OrphanFamily::SOURCE_WEBSITE,
                    'review_status' => OrphanFamily::REVIEW_PENDING,
                ]);

                OrphanFamilyInput::syncOrphans($family, $validated['orphans']);
                OrphanFamilyInput::storeDocuments($request, $family);
            });
        } catch (Throwable $exception) {
            if ($tokenKey) {
                Cache::forget($tokenKey);
            }

            throw $exception;
        }

        return $this->registered();
    }

    private function registered(): RedirectResponse
    {
        return redirect()->route('orphan-registration')->with('orphan_registered', true);
    }
}
