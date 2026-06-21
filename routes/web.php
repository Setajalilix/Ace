<?php

/*
|--------------------------------------------------------------------------
| Ace — modular domain routes
|--------------------------------------------------------------------------
|
| Each feature domain owns its routes in routes/domains/{domain}.php.
| Auth guest routes load separately from authenticated domain routes.
|
*/

require __DIR__.'/domains/auth.php';

Route::middleware('auth')->group(function () {
    foreach ([
        'planner',
        'life-areas',
        'goals',
        'tasks',
        'inbox',
        'notes',
        'journal',
        'time-blocks',
        'events',
        'calendar',
        'habits',
        'focus',
        'shutdown',
        'reviews',
        'statistics',
        'settings',
    ] as $domain) {
        require __DIR__."/domains/{$domain}.php";
    }
});
