<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');
        $status = $request->query('status');

        $query = FormSubmission::query()->latest();

        if ($type && in_array($type, ['contact', 'partnership', 'volunteer', 'sponsorship', 'assistance', 'donation_transfer'], true)) {
            $query->where('type', $type);
        }

        if ($status && in_array($status, ['unread', 'in_progress', 'resolved', 'archived'], true)) {
            $query->where('status', $status);
        }

        $submissions = $query->paginate(20)->withQueryString();

        $counts = [
            'all' => FormSubmission::count(),
            'unread' => FormSubmission::where('status', 'unread')->count(),
            'contact' => FormSubmission::where('type', 'contact')->count(),
            'partnership' => FormSubmission::where('type', 'partnership')->count(),
            'volunteer' => FormSubmission::where('type', 'volunteer')->count(),
            'sponsorship' => FormSubmission::where('type', 'sponsorship')->count(),
            'assistance' => FormSubmission::where('type', 'assistance')->count(),
            'donation_transfer' => FormSubmission::where('type', 'donation_transfer')->count(),
        ];

        return view('dashboard.inbox.index', compact('submissions', 'counts', 'type', 'status'));
    }

    public function show(FormSubmission $inbox): View
    {
        if ($inbox->status === 'unread') {
            $inbox->update(['status' => 'in_progress']);
        }

        return view('dashboard.inbox.show', [
            'submission' => $inbox,
        ]);
    }

    public function update(Request $request, FormSubmission $inbox): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:unread,in_progress,resolved,archived'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $inbox->update($validated);

        return redirect()->route('dashboard.inbox.show', $inbox)
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(FormSubmission $inbox): RedirectResponse
    {
        $inbox->delete();

        return redirect()->route('dashboard.inbox.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }
}
