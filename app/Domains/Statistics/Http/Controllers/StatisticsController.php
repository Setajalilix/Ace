<?php

namespace App\Domains\Statistics\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\Statistics\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index(Request $request, StatisticsService $stats)
    {
        $data = $stats->dashboard($request->user());

        return view('statistics.index', $data);
    }
}
