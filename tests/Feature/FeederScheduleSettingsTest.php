<?php

namespace Tests\Feature;

use App\Models\FarmSetting;
use App\Models\FeederCommand;
use App\Models\FeedingSchedule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class FeederScheduleSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');

        Schema::create('farm_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('feeding_schedules', function (Blueprint $table) {
            $table->id();
            $table->time('time');
            $table->text('days')->nullable();
            $table->integer('amount')->default(1);
            $table->integer('cage_number')->default(1);
            $table->boolean('enabled')->default(true);
            $table->string('status')->default('idle');
            $table->timestamp('last_feed_at')->nullable();
            $table->timestamp('feeding_started_at')->nullable();
            $table->timestamps();
        });

        Schema::create('feed_history', function (Blueprint $table) {
            $table->id();
            $table->string('feed_type');
            $table->string('feed_brand')->nullable();
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->unsignedBigInteger('manual_feed_id')->nullable();
            $table->integer('duration');
            $table->integer('cage_number')->default(1);
            $table->string('status');
            $table->timestamp('fed_at');
            $table->timestamps();
        });

        Schema::create('manual_feeds', function (Blueprint $table) {
            $table->id();
            $table->integer('duration');
            $table->integer('cage_number')->default(1);
            $table->text('days')->nullable();
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('feeder_commands', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->unsignedBigInteger('manual_feed_id')->nullable();
            $table->integer('duration')->default(5);
            $table->string('status');
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('quail_breeds', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('scientific_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('egg_production_rate')->nullable();
            $table->decimal('mature_weight', 8, 2)->nullable();
            $table->integer('maturity_age')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        FarmSetting::set('feed_time_morning', '08:00');
        FarmSetting::set('feed_time_afternoon', '12:00');
        FarmSetting::set('feed_time_evening', '17:00');

        foreach (['08:00:00', '12:00:00', '17:00:00'] as $time) {
            FeedingSchedule::create([
                'time' => $time,
                'days' => [],
                'amount' => 300,
                'cage_number' => 1,
                'enabled' => true,
                'status' => 'idle',
            ]);
        }
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Carbon::setTestNow();
        Schema::dropIfExists('quail_breeds');
        Schema::dropIfExists('feed_history');
        Schema::dropIfExists('feeder_commands');
        Schema::dropIfExists('manual_feeds');
        Schema::dropIfExists('feeding_schedules');
        Schema::dropIfExists('farm_settings');

        parent::tearDown();
    }

    public function test_saving_feeder_times_persists_and_updates_daily_schedules(): void
    {
        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 06:00:00', config('app.timezone'))
        );

        $this->post(route('settings.feeder-schedule'), [
            'feed_time_morning' => '07:15',
            'feed_time_afternoon' => '13:30',
            'feed_time_evening' => '19:45',
        ])->assertRedirect(route('settings'));

        $this->assertSame('07:15', FarmSetting::get('feed_time_morning'));
        $this->assertSame('13:30', FarmSetting::get('feed_time_afternoon'));
        $this->assertSame('19:45', FarmSetting::get('feed_time_evening'));
        $this->assertSame(
            ['07:15:00', '13:30:00', '19:45:00'],
            FeedingSchedule::orderBy('id')->pluck('time')->map(fn ($time) => (string) $time)->all()
        );

        $this->get('/settings')
            ->assertOk()
            ->assertSee('7:15 AM')
            ->assertSee('1:30 PM')
            ->assertSee('7:45 PM');
    }

    public function test_ampm_feeder_times_are_normalized_and_trigger_at_the_saved_time(): void
    {
        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 06:00:00', config('app.timezone'))
        );

        $this->post(route('settings.feeder-schedule'), [
            'feed_time_morning' => '7:15 AM',
            'feed_time_afternoon' => '1:30 PM',
            'feed_time_evening' => '7:45 PM',
        ])->assertRedirect(route('settings'));

        $this->assertSame('07:15', FarmSetting::get('feed_time_morning'));
        $this->assertSame('13:30', FarmSetting::get('feed_time_afternoon'));
        $this->assertSame('19:45', FarmSetting::get('feed_time_evening'));

        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 13:30:00', config('app.timezone'))
        );

        $this->getJson('/feeder/schedules/auto-check')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('triggered.0.time', '13:30:00');
    }

    public function test_feeder_times_can_be_changed_to_times_that_have_passed_today(): void
    {
        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 20:00:00', config('app.timezone'))
        );

        $this->post(route('settings.feeder-schedule'), [
            'feed_time_morning' => '7:15 AM',
            'feed_time_afternoon' => '1:30 PM',
            'feed_time_evening' => '8:30 PM',
        ])->assertRedirect(route('settings'))
            ->assertSessionHas('schedule_saved', true)
            ->assertSessionMissing('error');

        $this->assertSame('07:15', FarmSetting::get('feed_time_morning'));
        $this->assertSame('13:30', FarmSetting::get('feed_time_afternoon'));
        $this->assertSame('20:30', FarmSetting::get('feed_time_evening'));
        $this->assertSame(
            ['07:15:00', '13:30:00', '20:30:00'],
            FeedingSchedule::orderBy('id')->pluck('time')->map(fn ($time) => (string) $time)->all()
        );

        $this->get('/settings')
            ->assertOk()
            ->assertSee('7:15 AM')
            ->assertSee('1:30 PM')
            ->assertSee('8:30 PM');
    }

    public function test_scheduled_feeding_can_be_disabled_and_reenabled_without_changing_saved_times(): void
    {
        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 08:00:00', config('app.timezone'))
        );

        $this->get('/settings')
            ->assertOk()
            ->assertSee('id="scheduledFeedingToggle" name="scheduled_feeding_enabled" value="1" checked', false);

        $this->post(route('settings.feeder-schedule'), [
            'feed_time_morning' => '8:00 AM',
            'feed_time_afternoon' => '12:00 PM',
            'feed_time_evening' => '5:00 PM',
            'scheduled_feeding_enabled' => '0',
        ])->assertRedirect(route('settings'))
            ->assertSessionHas('schedule_saved', true);

        $this->assertSame('0', FarmSetting::get('scheduled_feeding_enabled'));
        $this->assertSame('08:00', FarmSetting::get('feed_time_morning'));
        $this->assertSame('12:00', FarmSetting::get('feed_time_afternoon'));
        $this->assertSame('17:00', FarmSetting::get('feed_time_evening'));

        $this->get('/settings')
            ->assertOk()
            ->assertSee('id="scheduledFeedingToggle" name="scheduled_feeding_enabled" value="1"', false)
            ->assertDontSee('id="scheduledFeedingToggle" name="scheduled_feeding_enabled" value="1" checked', false);

        $this->getJson('/feeder/schedules/auto-check')
            ->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonPath('scheduled_feeding_enabled', false);
        $this->assertSame(0, Artisan::call('feeder:check-automatic'));
        $this->assertSame('idle', FeedingSchedule::orderBy('id')->first()->status);
        $this->get('/feeder')
            ->assertOk()
            ->assertSee('Scheduled Feeding is OFF')
            ->assertSee('node-paused')
            ->assertSee('badge-paused">Paused', false)
            ->assertDontSee('badge-done">Done', false)
            ->assertSee('8:00 AM');

        $scheduledCommand = FeederCommand::create([
            'type' => 'scheduled',
            'duration' => 300,
            'status' => 'pending',
        ]);
        $manualCommand = FeederCommand::create([
            'type' => 'manual',
            'duration' => 5,
            'status' => 'pending',
        ]);
        $this->getJson('/api/feeder/pending-command')
            ->assertOk()
            ->assertJsonPath('has_command', true)
            ->assertJsonPath('command_id', $manualCommand->id)
            ->assertJsonPath('type', 'manual');
        $this->assertDatabaseHas('feeder_commands', [
            'id' => $scheduledCommand->id,
            'status' => 'failed',
        ]);

        $this->post(route('settings.feeder-schedule'), [
            'feed_time_morning' => '8:00 AM',
            'feed_time_afternoon' => '12:00 PM',
            'feed_time_evening' => '5:00 PM',
            'scheduled_feeding_enabled' => '1',
        ])->assertRedirect(route('settings'))
            ->assertSessionHas('schedule_saved', true);

        $this->assertSame('1', FarmSetting::get('scheduled_feeding_enabled'));
        $this->assertSame('08:00', FarmSetting::get('feed_time_morning'));
        $this->getJson('/feeder/schedules/auto-check')
            ->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonPath('scheduled_feeding_enabled', true);

        $this->assertSame('idle', FeedingSchedule::orderBy('id')->first()->status);
        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-14 08:00:00', config('app.timezone'))
        );
        $this->getJson('/feeder/schedules/auto-check')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('triggered.0.time', '08:00:00');

        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-14 08:01:00', config('app.timezone'))
        );
        $this->get('/feeder')
            ->assertOk()
            ->assertSee('>--:--:--</div>', false)
            ->assertSee('node-done')
            ->assertSee('badge-done">Done', false);
    }

    public function test_feeder_countdown_uses_the_earliest_saved_time(): void
    {
        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 10:00:00', config('app.timezone'))
        );
        FarmSetting::set('feed_time_morning', '19:00');
        FarmSetting::set('feed_time_afternoon', '12:00');
        FarmSetting::set('feed_time_evening', '17:00');

        $this->get('/feeder')
            ->assertOk()
            ->assertSee('12:00 PM')
            ->assertSee('Today');
    }

    public function test_custom_schedule_accepts_ampm_and_runs_at_normalized_time(): void
    {
        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 13:00:00', config('app.timezone'))
        );

        $this->post(route('feeder.schedules.store'), [
            'time' => '2:35 PM',
            'days' => [],
            'amount' => 8,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('feeding_schedules', [
            'time' => '14:35',
            'amount' => 8,
        ]);

        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 14:35:00', config('app.timezone'))
        );

        $this->getJson('/feeder/schedules/auto-check')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('triggered.0.time', '14:35');
    }

    public function test_auto_scheduler_triggers_at_the_saved_morning_time(): void
    {
        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 06:00:00', config('app.timezone'))
        );

        $this->post(route('settings.feeder-schedule'), [
            'feed_time_morning' => '07:15',
            'feed_time_afternoon' => '13:30',
            'feed_time_evening' => '19:45',
        ])->assertRedirect(route('settings'));

        \Illuminate\Support\Carbon::setTestNow(
            \Illuminate\Support\Carbon::parse('2026-10-13 07:15:00', config('app.timezone'))
        );

        $response = $this->getJson('/feeder/schedules/auto-check')->assertOk();
        $response->assertJsonPath('count', 1);
        $response->assertJsonPath('triggered.0.time', '07:15:00');
        $response->assertJsonPath('triggered.0.command_id', 1);

        $this->assertDatabaseHas('feeding_schedules', [
            'time' => '07:15:00',
            'status' => 'feeding_now',
        ]);
    }
}
