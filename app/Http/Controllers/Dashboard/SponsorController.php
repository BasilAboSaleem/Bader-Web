<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Support\SponsorAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $sponsors = Sponsor::query()
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->withCount('activeSponsorships')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('dashboard.sponsors.index', compact('sponsors', 'search'));
    }

    public function create(): View
    {
        return view('dashboard.sponsors.form', ['sponsor' => new Sponsor(['preferred_currency' => 'USD']), 'isEdit' => false]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(), [], $this->attributeNames());

        [$sponsor, $link] = DB::transaction(function () use ($request, $validated): array {
            $sponsor = Sponsor::create([...$validated, 'created_by' => $request->user()->id]);

            return [$sponsor, SponsorAccount::sync($sponsor)];
        });

        return redirect()->route('dashboard.sponsors.show', $sponsor)
            ->with('status', __('sponsors.saved'))
            ->with('invitation_link', $link);
    }

    public function show(Sponsor $sponsor): View
    {
        $sponsor->load(['user', 'sponsorships' => fn ($query) => $query->with(['family', 'orphan'])->orderByRaw('ended_at is not null')->latest('started_at')]);

        return view('dashboard.sponsors.show', compact('sponsor'));
    }

    public function edit(Sponsor $sponsor): View
    {
        return view('dashboard.sponsors.form', ['sponsor' => $sponsor, 'isEdit' => true]);
    }

    public function update(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $validated = $request->validate($this->rules($sponsor), [], $this->attributeNames());

        $link = DB::transaction(function () use ($sponsor, $validated): ?string {
            $sponsor->update($validated);

            return SponsorAccount::sync($sponsor->refresh());
        });

        return redirect()->route('dashboard.sponsors.show', $sponsor)
            ->with('status', __('sponsors.saved'))
            ->with('invitation_link', $link);
    }

    /**
     * Sponsors behind any sponsorship, past or present, stay on record so the financial history keeps its owner.
     */
    public function destroy(Sponsor $sponsor): RedirectResponse
    {
        if ($sponsor->sponsorships()->exists()) {
            return redirect()->route('dashboard.sponsors.show', $sponsor)->withErrors(['sponsor' => __('sponsors.cannot_delete')]);
        }

        DB::transaction(function () use ($sponsor): void {
            $user = $sponsor->user;
            $sponsor->delete();
            $user?->delete();
        });

        return redirect()->route('dashboard.sponsors.index')->with('status', __('sponsors.deleted'));
    }

    /**
     * A new password link for a sponsor who lost or never used theirs.
     */
    public function invite(Sponsor $sponsor): RedirectResponse
    {
        abort_unless($sponsor->user, 404);

        return redirect()->route('dashboard.sponsors.show', $sponsor)
            ->with('status', __('sponsors.invitation_sent'))
            ->with('invitation_link', SponsorAccount::invite($sponsor->user));
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(?Sponsor $sponsor = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('sponsors', 'email')->ignore($sponsor?->id),
                Rule::unique('users', 'email')->ignore($sponsor?->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:100'],
            'preferred_currency' => ['required', Rule::in(config('bader.sponsorship_currencies'))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function attributeNames(): array
    {
        return collect(['name', 'email', 'phone', 'country', 'preferred_currency', 'notes'])
            ->mapWithKeys(fn (string $field): array => [$field => __('sponsors.field.'.$field)])
            ->all();
    }
}
