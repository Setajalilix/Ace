<?php

namespace App\Domains\Shutdown\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\Shutdown\Http\Requests\UpdateShutdownRequest;
use App\Domains\Shutdown\Models\ShutdownLog;

class ShutdownController extends Controller
{
    public function update(UpdateShutdownRequest $request)
    {
        $log = ShutdownLog::updateOrCreate(
            ['user_id' => $request->user()->id, 'date' => $request->validated('date')],
            ['checklist' => $request->validated('checklist')]
        );

        $allDone = collect($request->validated('checklist'))->every(fn ($v) => $v);

        if ($allDone) {
            $log->update(['completed_at' => now()]);
        }

        return back()->with('success', 'Shutdown checklist updated.');
    }
}
