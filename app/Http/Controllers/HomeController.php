<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Facility;
use App\Models\ImpactMetric;
use App\Models\Program;
use App\Models\Story;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $approvedMetrics = ImpactMetric::approved()->orderBy('order')->get();

        $campaigns = Campaign::published()->get();

        $programs = Program::published()->get();

        $facilities = Facility::where('status', 'published')->orderBy('order')->get();

        $stories = Story::published()->limit(3)->get();

        return view('home', compact('approvedMetrics', 'campaigns', 'programs', 'facilities', 'stories'));
    }
}
