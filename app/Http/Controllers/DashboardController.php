<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $habits = Habit::with([
            'logs' => function ($query) use ($today) {
                $query->whereDate('date', $today);
            }
        ])
            ->get()
            ->filter(function ($habit) use ($today) {

                // آیا امروز باید نمایش داده شود؟
                if (!$habit->shouldAppearToday()) {
                    return false;
                }

                $todayLog = $habit->logs->first();

                // اگر هنوز لاگی ندارد → نمایش بده
                if (!$todayLog) {
                    return true;
                }

                // اگر کامل نشده → نمایش بده
                if (!$todayLog->completed) {
                    return true;
                }

                // اگر کامل شده → نمایش نده
                return false;
            });

        return view('dashboard', compact('habits'));
    }
}
