<?php

namespace Tests\Feature;

use App\Models\FarmSetting;
use App\Models\FeedingSchedule;
use Illuminate\Database\Schema\Blueprint;
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
