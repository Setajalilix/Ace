<?php

/**
 * One-time domain modularization script.
 * Run: php scripts/modularize.php
 */

$root = dirname(__DIR__);

/** @var array<string, string> Old FQCN prefix/suffix => new FQCN */
$classMap = [
    // Shared
    'App\\Http\\Controllers\\Controller' => 'App\\Shared\\Http\\Controllers\\Controller',
    'App\\Http\\Requests\\LifeOSRequest' => 'App\\Shared\\Http\\Requests\\LifeOSRequest',
    'App\\Http\\Requests\\Concerns\\ParsesDates' => 'App\\Shared\\Http\\Requests\\Concerns\\ParsesDates',
    'App\\Services\\Calendar\\JalaliDateService' => 'App\\Shared\\Calendar\\JalaliDateService',
    'App\\Policies\\AuthorizesOwnership' => 'App\\Shared\\Policies\\AuthorizesOwnership',
    'App\\Models\\Tag' => 'App\\Shared\\Models\\Tag',

    // Auth
    'App\\Models\\User' => 'App\\Domains\\Auth\\Models\\User',
    'App\\Http\\Controllers\\Auth\\AuthenticatedSessionController' => 'App\\Domains\\Auth\\Http\\Controllers\\AuthenticatedSessionController',
    'App\\Http\\Controllers\\Auth\\RegisteredUserController' => 'App\\Domains\\Auth\\Http\\Controllers\\RegisteredUserController',
    'App\\Http\\Requests\\Auth\\LoginRequest' => 'App\\Domains\\Auth\\Http\\Requests\\LoginRequest',
    'App\\Http\\Requests\\Auth\\RegisterRequest' => 'App\\Domains\\Auth\\Http\\Requests\\RegisterRequest',

    // LifeAreas
    'App\\Models\\LifeArea' => 'App\\Domains\\LifeAreas\\Models\\LifeArea',
    'App\\Actions\\SeedDefaultLifeAreas' => 'App\\Domains\\LifeAreas\\Actions\\SeedDefaultLifeAreas',
    'App\\Http\\Controllers\\LifeAreas\\LifeAreaController' => 'App\\Domains\\LifeAreas\\Http\\Controllers\\LifeAreaController',
    'App\\Http\\Requests\\LifeAreas\\StoreLifeAreaRequest' => 'App\\Domains\\LifeAreas\\Http\\Requests\\StoreLifeAreaRequest',
    'App\\Http\\Requests\\LifeAreas\\UpdateLifeAreaRequest' => 'App\\Domains\\LifeAreas\\Http\\Requests\\UpdateLifeAreaRequest',

    // Goals
    'App\\Models\\Goal' => 'App\\Domains\\Goals\\Models\\Goal',
    'App\\Enums\\GoalType' => 'App\\Domains\\Goals\\Enums\\GoalType',
    'App\\Http\\Controllers\\Goals\\GoalController' => 'App\\Domains\\Goals\\Http\\Controllers\\GoalController',
    'App\\Http\\Requests\\Goals\\StoreGoalRequest' => 'App\\Domains\\Goals\\Http\\Requests\\StoreGoalRequest',
    'App\\Http\\Requests\\Goals\\UpdateGoalRequest' => 'App\\Domains\\Goals\\Http\\Requests\\UpdateGoalRequest',

    // Tasks
    'App\\Models\\Task' => 'App\\Domains\\Tasks\\Models\\Task',
    'App\\Models\\TaskHistory' => 'App\\Domains\\Tasks\\Models\\TaskHistory',
    'App\\Enums\\TaskStatus' => 'App\\Domains\\Tasks\\Enums\\TaskStatus',
    'App\\Enums\\TaskPriority' => 'App\\Domains\\Tasks\\Enums\\TaskPriority',
    'App\\Enums\\KanbanColumn' => 'App\\Domains\\Tasks\\Enums\\KanbanColumn',
    'App\\Services\\TaskSchedulerService' => 'App\\Domains\\Tasks\\Services\\TaskSchedulerService',
    'App\\Actions\\CompleteTask' => 'App\\Domains\\Tasks\\Actions\\CompleteTask',
    'App\\Actions\\ScheduleTaskAsTimeBlock' => 'App\\Domains\\Tasks\\Actions\\ScheduleTaskAsTimeBlock',
    'App\\Http\\Controllers\\Tasks\\TaskController' => 'App\\Domains\\Tasks\\Http\\Controllers\\TaskController',
    'App\\Http\\Controllers\\Tasks\\TaskBoardController' => 'App\\Domains\\Tasks\\Http\\Controllers\\TaskBoardController',
    'App\\Http\\Requests\\Tasks\\StoreTaskRequest' => 'App\\Domains\\Tasks\\Http\\Requests\\StoreTaskRequest',
    'App\\Http\\Requests\\Tasks\\UpdateTaskRequest' => 'App\\Domains\\Tasks\\Http\\Requests\\UpdateTaskRequest',
    'App\\Http\\Requests\\Tasks\\UpdateKanbanRequest' => 'App\\Domains\\Tasks\\Http\\Requests\\UpdateKanbanRequest',
    'App\\Http\\Requests\\Tasks\\ScheduleTaskTimeBlockRequest' => 'App\\Domains\\Tasks\\Http\\Requests\\ScheduleTaskTimeBlockRequest',

    // Inbox
    'App\\Models\\InboxItem' => 'App\\Domains\\Inbox\\Models\\InboxItem',
    'App\\Services\\InboxService' => 'App\\Domains\\Inbox\\Services\\InboxService',
    'App\\Http\\Controllers\\Inbox\\InboxController' => 'App\\Domains\\Inbox\\Http\\Controllers\\InboxController',
    'App\\Http\\Requests\\Inbox\\QuickCaptureRequest' => 'App\\Domains\\Inbox\\Http\\Requests\\QuickCaptureRequest',

    // Notes
    'App\\Models\\Note' => 'App\\Domains\\Notes\\Models\\Note',
    'App\\Enums\\NoteType' => 'App\\Domains\\Notes\\Enums\\NoteType',
    'App\\Http\\Controllers\\Notes\\NoteController' => 'App\\Domains\\Notes\\Http\\Controllers\\NoteController',
    'App\\Http\\Requests\\Notes\\StoreNoteRequest' => 'App\\Domains\\Notes\\Http\\Requests\\StoreNoteRequest',
    'App\\Http\\Requests\\Notes\\UpdateNoteRequest' => 'App\\Domains\\Notes\\Http\\Requests\\UpdateNoteRequest',

    // Journal
    'App\\Models\\JournalEntry' => 'App\\Domains\\Journal\\Models\\JournalEntry',
    'App\\Enums\\JournalType' => 'App\\Domains\\Journal\\Enums\\JournalType',
    'App\\Http\\Controllers\\Journal\\JournalController' => 'App\\Domains\\Journal\\Http\\Controllers\\JournalController',
    'App\\Http\\Requests\\Journal\\StoreJournalRequest' => 'App\\Domains\\Journal\\Http\\Requests\\StoreJournalRequest',

    // TimeBlocks
    'App\\Models\\TimeBlock' => 'App\\Domains\\TimeBlocks\\Models\\TimeBlock',
    'App\\Enums\\TimeBlockStatus' => 'App\\Domains\\TimeBlocks\\Enums\\TimeBlockStatus',
    'App\\Services\\TimeBlockService' => 'App\\Domains\\TimeBlocks\\Services\\TimeBlockService',
    'App\\Http\\Controllers\\TimeBlocks\\TimeBlockController' => 'App\\Domains\\TimeBlocks\\Http\\Controllers\\TimeBlockController',
    'App\\Http\\Requests\\TimeBlocks\\StoreTimeBlockRequest' => 'App\\Domains\\TimeBlocks\\Http\\Requests\\StoreTimeBlockRequest',

    // Events
    'App\\Models\\Event' => 'App\\Domains\\Events\\Models\\Event',
    'App\\Models\\EventOccurrence' => 'App\\Domains\\Events\\Models\\EventOccurrence',
    'App\\Enums\\EventStatus' => 'App\\Domains\\Events\\Enums\\EventStatus',
    'App\\Enums\\EventRecurrence' => 'App\\Domains\\Events\\Enums\\EventRecurrence',
    'App\\Services\\EventRecurrenceService' => 'App\\Domains\\Events\\Services\\EventRecurrenceService',
    'App\\Http\\Controllers\\Events\\EventController' => 'App\\Domains\\Events\\Http\\Controllers\\EventController',
    'App\\Http\\Requests\\Events\\StoreEventRequest' => 'App\\Domains\\Events\\Http\\Requests\\StoreEventRequest',
    'App\\Http\\Requests\\Events\\UpdateEventRequest' => 'App\\Domains\\Events\\Http\\Requests\\UpdateEventRequest',

    // Habits
    'App\\Models\\Habit' => 'App\\Domains\\Habits\\Models\\Habit',
    'App\\Models\\HabitLog' => 'App\\Domains\\Habits\\Models\\HabitLog',
    'App\\Services\\HabitStatsService' => 'App\\Domains\\Habits\\Services\\HabitStatsService',
    'App\\Http\\Controllers\\Habits\\HabitController' => 'App\\Domains\\Habits\\Http\\Controllers\\HabitController',
    'App\\Http\\Controllers\\Habits\\HabitLogController' => 'App\\Domains\\Habits\\Http\\Controllers\\HabitLogController',
    'App\\Http\\Requests\\Habits\\StoreHabitRequest' => 'App\\Domains\\Habits\\Http\\Requests\\StoreHabitRequest',
    'App\\Http\\Requests\\Habits\\UpdateHabitRequest' => 'App\\Domains\\Habits\\Http\\Requests\\UpdateHabitRequest',
    'App\\Http\\Requests\\Habits\\SaveTimerRequest' => 'App\\Domains\\Habits\\Http\\Requests\\SaveTimerRequest',
    'App\\Http\\Requests\\Habits\\SaveCounterRequest' => 'App\\Domains\\Habits\\Http\\Requests\\SaveCounterRequest',

    // Reviews
    'App\\Models\\WeeklyReview' => 'App\\Domains\\Reviews\\Models\\WeeklyReview',
    'App\\Models\\MonthlyReview' => 'App\\Domains\\Reviews\\Models\\MonthlyReview',
    'App\\Services\\ReviewService' => 'App\\Domains\\Reviews\\Services\\ReviewService',
    'App\\Http\\Controllers\\Reviews\\ReviewController' => 'App\\Domains\\Reviews\\Http\\Controllers\\ReviewController',
    'App\\Http\\Requests\\Reviews\\SaveWeeklyReviewRequest' => 'App\\Domains\\Reviews\\Http\\Requests\\SaveWeeklyReviewRequest',
    'App\\Http\\Requests\\Reviews\\SaveMonthlyReviewRequest' => 'App\\Domains\\Reviews\\Http\\Requests\\SaveMonthlyReviewRequest',

    // Shutdown
    'App\\Models\\ShutdownLog' => 'App\\Domains\\Shutdown\\Models\\ShutdownLog',
    'App\\Http\\Controllers\\Shutdown\\ShutdownController' => 'App\\Domains\\Shutdown\\Http\\Controllers\\ShutdownController',
    'App\\Http\\Requests\\Shutdown\\UpdateShutdownRequest' => 'App\\Domains\\Shutdown\\Http\\Requests\\UpdateShutdownRequest',

    // DailySuccess
    'App\\Models\\DailyScore' => 'App\\Domains\\DailySuccess\\Models\\DailyScore',
    'App\\Enums\\DayResult' => 'App\\Domains\\DailySuccess\\Enums\\DayResult',
    'App\\Services\\DailySuccessService' => 'App\\Domains\\DailySuccess\\Services\\DailySuccessService',

    // Planner
    'App\\ViewModels\\DailyPlannerViewModel' => 'App\\Domains\\Planner\\ViewModels\\DailyPlannerViewModel',
    'App\\Services\\TimelineService' => 'App\\Domains\\Planner\\Services\\TimelineService',
    'App\\Http\\Controllers\\Planner\\PlannerController' => 'App\\Domains\\Planner\\Http\\Controllers\\PlannerController',

    // Calendar
    'App\\Http\\Controllers\\Calendar\\CalendarController' => 'App\\Domains\\Calendar\\Http\\Controllers\\CalendarController',

    // Focus
    'App\\Http\\Controllers\\Focus\\FocusController' => 'App\\Domains\\Focus\\Http\\Controllers\\FocusController',

    // Statistics
    'App\\Services\\StatisticsService' => 'App\\Domains\\Statistics\\Services\\StatisticsService',
    'App\\Http\\Controllers\\Statistics\\StatisticsController' => 'App\\Domains\\Statistics\\Http\\Controllers\\StatisticsController',

    // Database seeding
    'App\\Actions\\SeedSampleContent' => 'App\\Database\\Actions\\SeedSampleContent',
];

/** @var array<string, string> Old relative path from app/ => new relative path from app/ */
$fileMap = [
    'Http/Controllers/Controller.php' => 'Shared/Http/Controllers/Controller.php',
    'Http/Requests/LifeOSRequest.php' => 'Shared/Http/Requests/LifeOSRequest.php',
    'Http/Requests/Concerns/ParsesDates.php' => 'Shared/Http/Requests/Concerns/ParsesDates.php',
    'Services/Calendar/JalaliDateService.php' => 'Shared/Calendar/JalaliDateService.php',
    'Policies/AuthorizesOwnership.php' => 'Shared/Policies/AuthorizesOwnership.php',
    'Models/Tag.php' => 'Shared/Models/Tag.php',

    'Models/User.php' => 'Domains/Auth/Models/User.php',
    'Http/Controllers/Auth/AuthenticatedSessionController.php' => 'Domains/Auth/Http/Controllers/AuthenticatedSessionController.php',
    'Http/Controllers/Auth/RegisteredUserController.php' => 'Domains/Auth/Http/Controllers/RegisteredUserController.php',
    'Http/Requests/Auth/LoginRequest.php' => 'Domains/Auth/Http/Requests/LoginRequest.php',
    'Http/Requests/Auth/RegisterRequest.php' => 'Domains/Auth/Http/Requests/RegisterRequest.php',

    'Models/LifeArea.php' => 'Domains/LifeAreas/Models/LifeArea.php',
    'Actions/SeedDefaultLifeAreas.php' => 'Domains/LifeAreas/Actions/SeedDefaultLifeAreas.php',
    'Http/Controllers/LifeAreas/LifeAreaController.php' => 'Domains/LifeAreas/Http/Controllers/LifeAreaController.php',
    'Http/Requests/LifeAreas/StoreLifeAreaRequest.php' => 'Domains/LifeAreas/Http/Requests/StoreLifeAreaRequest.php',
    'Http/Requests/LifeAreas/UpdateLifeAreaRequest.php' => 'Domains/LifeAreas/Http/Requests/UpdateLifeAreaRequest.php',

    'Models/Goal.php' => 'Domains/Goals/Models/Goal.php',
    'Enums/GoalType.php' => 'Domains/Goals/Enums/GoalType.php',
    'Http/Controllers/Goals/GoalController.php' => 'Domains/Goals/Http/Controllers/GoalController.php',
    'Http/Requests/Goals/StoreGoalRequest.php' => 'Domains/Goals/Http/Requests/StoreGoalRequest.php',
    'Http/Requests/Goals/UpdateGoalRequest.php' => 'Domains/Goals/Http/Requests/UpdateGoalRequest.php',

    'Models/Task.php' => 'Domains/Tasks/Models/Task.php',
    'Models/TaskHistory.php' => 'Domains/Tasks/Models/TaskHistory.php',
    'Enums/TaskStatus.php' => 'Domains/Tasks/Enums/TaskStatus.php',
    'Enums/TaskPriority.php' => 'Domains/Tasks/Enums/TaskPriority.php',
    'Enums/KanbanColumn.php' => 'Domains/Tasks/Enums/KanbanColumn.php',
    'Services/TaskSchedulerService.php' => 'Domains/Tasks/Services/TaskSchedulerService.php',
    'Actions/CompleteTask.php' => 'Domains/Tasks/Actions/CompleteTask.php',
    'Actions/ScheduleTaskAsTimeBlock.php' => 'Domains/Tasks/Actions/ScheduleTaskAsTimeBlock.php',
    'Http/Controllers/Tasks/TaskController.php' => 'Domains/Tasks/Http/Controllers/TaskController.php',
    'Http/Controllers/Tasks/TaskBoardController.php' => 'Domains/Tasks/Http/Controllers/TaskBoardController.php',
    'Http/Requests/Tasks/StoreTaskRequest.php' => 'Domains/Tasks/Http/Requests/StoreTaskRequest.php',
    'Http/Requests/Tasks/UpdateTaskRequest.php' => 'Domains/Tasks/Http/Requests/UpdateTaskRequest.php',
    'Http/Requests/Tasks/UpdateKanbanRequest.php' => 'Domains/Tasks/Http/Requests/UpdateKanbanRequest.php',
    'Http/Requests/Tasks/ScheduleTaskTimeBlockRequest.php' => 'Domains/Tasks/Http/Requests/ScheduleTaskTimeBlockRequest.php',

    'Models/InboxItem.php' => 'Domains/Inbox/Models/InboxItem.php',
    'Services/InboxService.php' => 'Domains/Inbox/Services/InboxService.php',
    'Http/Controllers/Inbox/InboxController.php' => 'Domains/Inbox/Http/Controllers/InboxController.php',
    'Http/Requests/Inbox/QuickCaptureRequest.php' => 'Domains/Inbox/Http/Requests/QuickCaptureRequest.php',

    'Models/Note.php' => 'Domains/Notes/Models/Note.php',
    'Enums/NoteType.php' => 'Domains/Notes/Enums/NoteType.php',
    'Http/Controllers/Notes/NoteController.php' => 'Domains/Notes/Http/Controllers/NoteController.php',
    'Http/Requests/Notes/StoreNoteRequest.php' => 'Domains/Notes/Http/Requests/StoreNoteRequest.php',
    'Http/Requests/Notes/UpdateNoteRequest.php' => 'Domains/Notes/Http/Requests/UpdateNoteRequest.php',

    'Models/JournalEntry.php' => 'Domains/Journal/Models/JournalEntry.php',
    'Enums/JournalType.php' => 'Domains/Journal/Enums/JournalType.php',
    'Http/Controllers/Journal/JournalController.php' => 'Domains/Journal/Http/Controllers/JournalController.php',
    'Http/Requests/Journal/StoreJournalRequest.php' => 'Domains/Journal/Http/Requests/StoreJournalRequest.php',

    'Models/TimeBlock.php' => 'Domains/TimeBlocks/Models/TimeBlock.php',
    'Enums/TimeBlockStatus.php' => 'Domains/TimeBlocks/Enums/TimeBlockStatus.php',
    'Services/TimeBlockService.php' => 'Domains/TimeBlocks/Services/TimeBlockService.php',
    'Http/Controllers/TimeBlocks/TimeBlockController.php' => 'Domains/TimeBlocks/Http/Controllers/TimeBlockController.php',
    'Http/Requests/TimeBlocks/StoreTimeBlockRequest.php' => 'Domains/TimeBlocks/Http/Requests/StoreTimeBlockRequest.php',

    'Models/Event.php' => 'Domains/Events/Models/Event.php',
    'Models/EventOccurrence.php' => 'Domains/Events/Models/EventOccurrence.php',
    'Enums/EventStatus.php' => 'Domains/Events/Enums/EventStatus.php',
    'Enums/EventRecurrence.php' => 'Domains/Events/Enums/EventRecurrence.php',
    'Services/EventRecurrenceService.php' => 'Domains/Events/Services/EventRecurrenceService.php',
    'Http/Controllers/Events/EventController.php' => 'Domains/Events/Http/Controllers/EventController.php',
    'Http/Requests/Events/StoreEventRequest.php' => 'Domains/Events/Http/Requests/StoreEventRequest.php',
    'Http/Requests/Events/UpdateEventRequest.php' => 'Domains/Events/Http/Requests/UpdateEventRequest.php',

    'Models/Habit.php' => 'Domains/Habits/Models/Habit.php',
    'Models/HabitLog.php' => 'Domains/Habits/Models/HabitLog.php',
    'Services/HabitStatsService.php' => 'Domains/Habits/Services/HabitStatsService.php',
    'Http/Controllers/Habits/HabitController.php' => 'Domains/Habits/Http/Controllers/HabitController.php',
    'Http/Controllers/Habits/HabitLogController.php' => 'Domains/Habits/Http/Controllers/HabitLogController.php',
    'Http/Requests/Habits/StoreHabitRequest.php' => 'Domains/Habits/Http/Requests/StoreHabitRequest.php',
    'Http/Requests/Habits/UpdateHabitRequest.php' => 'Domains/Habits/Http/Requests/UpdateHabitRequest.php',
    'Http/Requests/Habits/SaveTimerRequest.php' => 'Domains/Habits/Http/Requests/SaveTimerRequest.php',
    'Http/Requests/Habits/SaveCounterRequest.php' => 'Domains/Habits/Http/Requests/SaveCounterRequest.php',

    'Models/WeeklyReview.php' => 'Domains/Reviews/Models/WeeklyReview.php',
    'Models/MonthlyReview.php' => 'Domains/Reviews/Models/MonthlyReview.php',
    'Services/ReviewService.php' => 'Domains/Reviews/Services/ReviewService.php',
    'Http/Controllers/Reviews/ReviewController.php' => 'Domains/Reviews/Http/Controllers/ReviewController.php',
    'Http/Requests/Reviews/SaveWeeklyReviewRequest.php' => 'Domains/Reviews/Http/Requests/SaveWeeklyReviewRequest.php',
    'Http/Requests/Reviews/SaveMonthlyReviewRequest.php' => 'Domains/Reviews/Http/Requests/SaveMonthlyReviewRequest.php',

    'Models/ShutdownLog.php' => 'Domains/Shutdown/Models/ShutdownLog.php',
    'Http/Controllers/Shutdown/ShutdownController.php' => 'Domains/Shutdown/Http/Controllers/ShutdownController.php',
    'Http/Requests/Shutdown/UpdateShutdownRequest.php' => 'Domains/Shutdown/Http/Requests/UpdateShutdownRequest.php',

    'Models/DailyScore.php' => 'Domains/DailySuccess/Models/DailyScore.php',
    'Enums/DayResult.php' => 'Domains/DailySuccess/Enums/DayResult.php',
    'Services/DailySuccessService.php' => 'Domains/DailySuccess/Services/DailySuccessService.php',

    'ViewModels/DailyPlannerViewModel.php' => 'Domains/Planner/ViewModels/DailyPlannerViewModel.php',
    'Services/TimelineService.php' => 'Domains/Planner/Services/TimelineService.php',
    'Http/Controllers/Planner/PlannerController.php' => 'Domains/Planner/Http/Controllers/PlannerController.php',

    'Http/Controllers/Calendar/CalendarController.php' => 'Domains/Calendar/Http/Controllers/CalendarController.php',
    'Http/Controllers/Focus/FocusController.php' => 'Domains/Focus/Http/Controllers/FocusController.php',
    'Services/StatisticsService.php' => 'Domains/Statistics/Services/StatisticsService.php',
    'Http/Controllers/Statistics/StatisticsController.php' => 'Domains/Statistics/Http/Controllers/StatisticsController.php',

    'Actions/SeedSampleContent.php' => 'Database/Actions/SeedSampleContent.php',
];

$legacyDeletes = [
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
    'Enums/ProjectStatus.php',
    'Models/TimeSession.php',
];

function replaceClasses(string $content, array $classMap): string
{
    uksort($classMap, fn ($a, $b) => strlen($b) <=> strlen($a));
    return str_replace(array_keys($classMap), array_values($classMap), $content);
}

function namespaceFromClass(string $class): string
{
    return substr($class, 0, strrpos($class, '\\'));
}

// Step 1: Move files with updated namespace
foreach ($fileMap as $from => $to) {
    $source = $root.'/app/'.$from;
    if (! file_exists($source)) {
        echo "SKIP missing: {$from}\n";
        continue;
    }

    $content = file_get_contents($source);
    $newNamespace = 'App\\'.str_replace('/', '\\', dirname($to));
    $content = preg_replace('/^namespace\s+[^;]+;/m', "namespace {$newNamespace};", $content, 1);
    $content = replaceClasses($content, $classMap);

    $dest = $root.'/app/'.$to;
    $dir = dirname($dest);
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($dest, $content);
    echo "MOVED {$from} -> {$to}\n";
}

// Step 2: Replace imports across project
$scanDirs = [
    $root.'/app',
    $root.'/routes',
    $root.'/tests',
    $root.'/database',
    $root.'/config',
    $root.'/resources/views',
];

$extensions = ['php', 'blade.php'];

foreach ($scanDirs as $dir) {
    if (! is_dir($dir)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if (! $file->isFile()) {
            continue;
        }
        $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        if ($file->getExtension() === 'php' || str_ends_with($file->getFilename(), '.blade.php')) {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            $updated = replaceClasses($content, $classMap);
            if ($updated !== $content) {
                file_put_contents($path, $updated);
                echo "UPDATED refs: {$path}\n";
            }
        }
    }
}

// Step 3: Delete old source files (moved ones)
foreach (array_keys($fileMap) as $from) {
    $source = $root.'/app/'.$from;
    if (file_exists($source)) {
        unlink($source);
        echo "DELETED old: {$from}\n";
    }
}

// Step 4: Delete legacy files
foreach ($legacyDeletes as $path) {
    $full = $root.'/app/'.$path;
    if (file_exists($full)) {
        unlink($full);
        echo "DELETED legacy: {$path}\n";
    }
}

// Step 5: Clean empty directories
echo "Done. Run: composer dump-autoload && php artisan test\n";
