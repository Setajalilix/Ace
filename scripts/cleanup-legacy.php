<?php

/** Cleanup orphaned pre-modularization files. Run: php scripts/cleanup-legacy.php */

$root = dirname(__DIR__).'/app';

$removeDirs = [
    'Actions',
    'Enums',
    'Models',
    'Services',
    'ViewModels',
    'Policies',
];

$removePaths = [
    'Http/Controllers/CalendarController.php',
    'Http/Controllers/EventController.php',
    'Http/Controllers/FocusController.php',
    'Http/Controllers/GoalController.php',
    'Http/Controllers/HabitController.php',
    'Http/Controllers/HabitLogController.php',
    'Http/Controllers/InboxController.php',
    'Http/Controllers/JournalController.php',
    'Http/Controllers/NoteController.php',
    'Http/Controllers/PlannerController.php',
    'Http/Controllers/ProjectController.php',
    'Http/Controllers/ReviewController.php',
    'Http/Controllers/ShutdownController.php',
    'Http/Controllers/StatisticsController.php',
    'Http/Controllers/TaskController.php',
    'Http/Controllers/TimeBlockController.php',
    'Models/Project.php',
];

function deleteDir(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir.DIRECTORY_SEPARATOR.$item;
        is_dir($path) ? deleteDir($path) : unlink($path);
    }
    rmdir($dir);
}

foreach ($removePaths as $path) {
    $full = $root.'/'.$path;
    if (file_exists($full)) {
        unlink($full);
        echo "Deleted {$path}\n";
    }
}

foreach ($removeDirs as $dir) {
    deleteDir($root.'/'.$dir);
    echo "Removed dir {$dir}/\n";
}

// Remove empty Http subdirs if orphaned
foreach (['Auth', 'Calendar', 'Events', 'Focus', 'Goals', 'Habits', 'Inbox', 'Journal', 'LifeAreas', 'Notes', 'Planner', 'Reviews', 'Shutdown', 'Statistics', 'Tasks', 'TimeBlocks'] as $sub) {
    $path = $root.'/Http/Controllers/'.$sub;
    if (is_dir($path)) {
        deleteDir($path);
        echo "Removed Http/Controllers/{$sub}/\n";
    }
}

deleteDir($root.'/Http/Requests');
deleteDir($root.'/Http/Controllers');
echo "Cleanup complete.\n";
