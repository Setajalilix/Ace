<?php

namespace Tests\Feature;

use App\Domains\Events\Enums\EventStatus;
use App\Domains\Events\Models\Event;
use App\Domains\Events\Services\EventRecurrenceService;
use App\Domains\LifeAreas\Actions\SeedDefaultLifeAreas;
use App\Domains\DailySuccess\Enums\DayResult;
use App\Domains\Tasks\Enums\TaskPriority;
use App\Domains\Tasks\Enums\TaskStatus;
use App\Domains\TimeBlocks\Enums\TimeBlockStatus;
use App\Domains\Tasks\Models\Task;
use App\Domains\TimeBlocks\Models\TimeBlock;
use App\Domains\Auth\Models\User;
use App\Domains\DailySuccess\Services\DailySuccessService;
use App\Domains\Inbox\Services\InboxService;
use App\Domains\TimeBlocks\Services\TimeBlockService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LifeOSTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        app(SeedDefaultLifeAreas::class)->execute($this->user);
    }

    public function test_daily_success_requires_all_p1_and_80_percent_p2(): void
    {
        $service = app(DailySuccessService::class);
        $date = today();

        Task::create(['user_id' => $this->user->id, 'title' => 'P1', 'priority' => TaskPriority::P1, 'due_date' => $date, 'status' => TaskStatus::Pending]);
        Task::create(['user_id' => $this->user->id, 'title' => 'P2a', 'priority' => TaskPriority::P2, 'due_date' => $date, 'status' => TaskStatus::Completed, 'completed_at' => now()]);
        Task::create(['user_id' => $this->user->id, 'title' => 'P2b', 'priority' => TaskPriority::P2, 'due_date' => $date, 'status' => TaskStatus::Pending]);

        $this->assertEquals(DayResult::Failed, $service->calculate($this->user, $date));

        Task::where('title', 'P1')->update(['status' => TaskStatus::Completed, 'completed_at' => now()]);
        Task::where('title', 'P2b')->update(['status' => TaskStatus::Completed, 'completed_at' => now()]);

        $this->assertEquals(DayResult::Success, $service->calculate($this->user, $date));
    }

    public function test_time_block_missed_when_not_started_before_latest_start(): void
    {
        $block = TimeBlock::create([
            'user_id' => $this->user->id,
            'date' => today(),
            'title' => 'Deep Work',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'latest_start_time' => '00:01',
            'status' => TimeBlockStatus::Scheduled,
        ]);

        app(TimeBlockService::class)->checkMissedBlocks($this->user, today());

        $this->assertEquals(TimeBlockStatus::Missed, $block->fresh()->status);
    }

    public function test_inbox_capture_and_convert_to_task(): void
    {
        $inbox = app(InboxService::class);

        $item = $inbox->capture($this->user, 'Buy groceries');
        $this->assertNull($item->processed_at);

        $task = $inbox->convertToTask($item, ['title' => 'Buy groceries']);
        $this->assertNotNull($item->fresh()->processed_at);
        $this->assertEquals('Buy groceries', $task->title);
    }

    public function test_planner_requires_authentication(): void
    {
        $this->get(route('planner.today'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_planner(): void
    {
        $this->actingAs($this->user)
            ->get(route('planner.today'))
            ->assertOk()
            ->assertSee('Today');
    }

    public function test_registration_seeds_life_areas(): void
    {
        $this->post(route('register'), [
            'name' => 'New User',
            'email' => 'new@lifeos.app',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('planner.today'));

        $user = User::where('email', 'new@lifeos.app')->first();
        $this->assertCount(4, $user->lifeAreas);
    }

    public function test_settings_page_shows_profile_and_life_areas(): void
    {
        $this->actingAs($this->user)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Settings')
            ->assertSee($this->user->name)
            ->assertSee('Life areas');
    }

    public function test_user_can_update_profile(): void
    {
        $this->actingAs($this->user)
            ->put(route('settings.profile'), [
                'name' => 'Updated Name',
                'email' => 'updated@lifeos.app',
            ])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('updated@lifeos.app', $this->user->email);
    }

    public function test_user_can_create_life_area_from_settings(): void
    {
        $this->actingAs($this->user)
            ->post(route('life-areas.store'), [
                'name' => 'Creative',
                'color' => '#8B5CF6',
            ])
            ->assertRedirect(route('settings.index').'#life-areas');

        $this->assertDatabaseHas('life_areas', [
            'user_id' => $this->user->id,
            'name' => 'Creative',
            'slug' => 'creative',
            'color' => '#8B5CF6',
        ]);
    }

    public function test_task_due_today_appears_on_calendar_day_view(): void
    {
        $task = Task::create([
            'user_id' => $this->user->id,
            'title' => 'Calendar task',
            'priority' => TaskPriority::P2,
            'due_date' => today()->toDateString(),
            'status' => TaskStatus::Pending,
        ]);

        $this->actingAs($this->user)
            ->get(route('calendar.index', ['view' => 'day', 'date' => today()->toDateString()]))
            ->assertOk()
            ->assertSee('Calendar task');
    }

    public function test_task_with_jalali_due_date_appears_on_calendar(): void
    {
        $jalali = app(\App\Shared\Calendar\JalaliDateService::class);
        $todayGregorian = today()->toDateString();
        $jalaliString = $jalali->format(today(), 'Y/n/j');

        $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'title' => 'Jalali dated task',
                'priority' => 2,
                'due_date_jalali' => $jalaliString,
            ], ['Accept' => 'application/json'])
            ->assertOk();

        $this->actingAs($this->user)
            ->get(route('calendar.index', ['view' => 'day', 'date' => $todayGregorian]))
            ->assertOk()
            ->assertSee('Jalali dated task');
    }

    public function test_event_today_appears_on_planner_today(): void
    {
        Event::create([
            'user_id' => $this->user->id,
            'title' => 'Today meeting',
            'starts_at' => today()->setTimeFromTimeString('10:00'),
            'status' => EventStatus::Scheduled,
        ]);

        $this->actingAs($this->user)
            ->get(route('planner.today'))
            ->assertOk()
            ->assertSee('Today meeting');
    }

    public function test_recurring_event_appears_on_planner_today(): void
    {
        Event::create([
            'user_id' => $this->user->id,
            'title' => 'Daily standup',
            'starts_at' => today()->subWeek()->setTimeFromTimeString('09:00'),
            'recurrence_rule' => json_encode(['type' => 'daily']),
            'status' => EventStatus::Scheduled,
        ]);

        $this->actingAs($this->user)
            ->get(route('planner.today'))
            ->assertOk()
            ->assertSee('Daily standup');
    }

    public function test_iranian_weekdays_recurrence_is_saturday_through_wednesday(): void
    {
        $service = app(EventRecurrenceService::class);
        $saturday = Carbon::parse('2026-06-20', config('app.timezone'))->startOfDay();
        $this->assertSame(Carbon::SATURDAY, $saturday->dayOfWeek);

        $event = Event::create([
            'user_id' => $this->user->id,
            'title' => 'Work week event',
            'starts_at' => $saturday->copy()->subWeek()->setTime(9, 0),
            'recurrence_rule' => json_encode(['type' => 'weekdays']),
            'status' => EventStatus::Scheduled,
        ]);

        $this->assertTrue($service->occursOn($event, $saturday));
        $this->assertTrue($service->occursOn($event, $saturday->copy()->subDays(3))); // Wednesday
        $this->assertFalse($service->occursOn($event, $saturday->copy()->subDay())); // Friday
        $this->assertFalse($service->occursOn($event, $saturday->copy()->subDays(2))); // Thursday
    }
}
