<?php

namespace App\Domains\Planner\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\Planner\ViewModels\DailyPlannerViewModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlannerController extends Controller
{
    public function today(Request $request, DailyPlannerViewModel $viewModel)
    {
        $date = $request->date ? Carbon::parse($request->date) : today();

        return view('planner.daily', $viewModel->build($request->user(), $date));
    }
}
