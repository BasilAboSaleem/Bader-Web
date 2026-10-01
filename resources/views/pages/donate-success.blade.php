@extends('layouts.public')

@section('title', 'Donation Successful — '.__('brand.name'))

@section('content')
    <section class="bg-teal-950 text-sand-50 section-pad !py-20 relative overflow-hidden min-h-[60vh] flex items-center">
        <div class="pointer-events-none absolute -end-24 top-0 h-80 w-80 rounded-full bg-forest-700/20 blur-3xl" aria-hidden="true"></div>
        <div class="max-w-3xl mx-auto relative text-center">
            <div class="mx-auto w-20 h-20 bg-gold-500 rounded-full flex items-center justify-center text-teal-950 mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <h1 class="font-display text-4xl sm:text-5xl font-bold mt-3 text-sand-50">
                Thank You for Your Generosity
            </h1>
            <p class="mt-4 text-base sm:text-lg text-sand-100/75 leading-relaxed">
                Your donation has been successfully processed. We have received your contribution and a receipt has been generated.
            </p>

            <div class="mt-10 bg-teal-900 rounded-2xl p-8 border border-teal-800 shadow-xl inline-block text-left w-full max-w-lg">
                <h3 class="text-xl font-display font-bold text-gold-400 mb-4 border-b border-teal-800 pb-4">Donation Receipt</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sand-100/70 text-sm uppercase tracking-wide">Receipt Number</span>
                        <span class="font-mono text-sand-50 font-bold">{{ $donation->receipt_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sand-100/70 text-sm uppercase tracking-wide">Date</span>
                        <span class="text-sand-50">{{ $donation->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sand-100/70 text-sm uppercase tracking-wide">Amount</span>
                        <span class="text-gold-400 font-bold text-lg">{{ number_format($donation->amount, 2) }} USD</span>
                    </div>
                    @if($donation->donor_name)
                    <div class="flex justify-between">
                        <span class="text-sand-100/70 text-sm uppercase tracking-wide">Donor</span>
                        <span class="text-sand-50">{{ $donation->donor_name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-sand-100/70 text-sm uppercase tracking-wide">Status</span>
                        <span class="text-green-400 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span> Verified
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-10">
                <a href="{{ route('home') }}" class="btn-primary">
                    Return to Home
                </a>
            </div>
        </div>
    </section>
@endsection
