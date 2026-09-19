<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'facilities' => ['water_plant', 'bakery', 'school'],
            'programs' => ['water', 'food', 'shelter', 'winter', 'health', 'education'],
            'impacts' => ['families', 'water', 'bread', 'students'],
        ]);
    }
}
