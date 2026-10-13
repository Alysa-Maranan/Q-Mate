<?php

namespace Tests\Feature;

use App\Models\FeederCommand;
use App\Models\FeedingSchedule;
use App\Models\ManualFeed;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class FeedHistoryRecordingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');

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

        Schema::create('feeding_schedules', function (Blueprint $table) {
            $table->id();
            $table->time('time');
            $table->text('days')->nullable();
            $table->integer('amount')->default(1);
            $table->integer('cage_number')->default(1);
            $table->boolean('enabled')->default(true);
            $table->string('status');
            $table->timestamp('last_feed_at')->nullable();
            $table->timestamp('feeding_started_at')->nullable();
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

        Schema::create('feed_history', function (Blueprint $table) {
            $table->id();
            $table->string('feed_type');
            $table->string('feed_brand')->nullable();
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->unsignedBigInteger('manual_feed_id')->nullable();
            $table->integer('duration');
            $table->text('days')->nullable();
            $table->integer('cage_number')->default(1);
            $table->string('status');
            $table->timestamp('fed_at');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Carbon::setTestNow();
        Schema::dropIfExists('feed_history');
        Schema::dropIfExists('feeder_commands');
        Schema::dropIfExists('feeding_schedules');
        Schema::dropIfExists('manual_feeds');

        parent::tearDown();
    }

    public function test_manual_simulation_records_one_history_entry(): void
    {
        $feed = $this->manualFeed();
        $command = FeederCommand::create([
            'type' => 'manual',
            'manual_feed_id' => $feed->id,
            'duration' => $feed->duration,
            'status' => 'pending',
        ]);

        $payload = [
            'feed_type' => 'manual',
            'feed_id' => $feed->id,
            'command_id' => $command->id,
            'cage_number' => $feed->cage_number,
        ];

        $this->postJson(route('feeder.complete'), $payload)->assertOk()->assertJson([
            'recorded' => true,
            'status' => 'completed',
        ]);
        $this->postJson(route('feeder.complete'), $payload)->assertOk();

        $this->assertDatabaseCount('feed_history', 1);
        $this->assertDatabaseHas('feed_history', [
            'manual_feed_id' => $feed->id,
            'feed_type' => 'manual',
            'status' => 'completed',
        ]);
    }

    public function test_scheduled_simulation_records_once_per_schedule_run(): void
    {
        $schedule = $this->feedingSchedule();
        $schedule->update([
            'status' => 'idle',
            'last_feed_at' => null,
            'feeding_started_at' => null,
        ]);

        $response = $this->getJson('/feeder/schedules/auto-check')->assertOk();
        $scheduledFeed = $response->json('triggered.0');
        $this->assertNotEmpty($scheduledFeed['command_id'] ?? null);

        $payload = [
            'feed_type' => 'scheduled',
            'feed_id' => $schedule->id,
            'command_id' => $scheduledFeed['command_id'],
            'cage_number' => $schedule->cage_number,
        ];

        $this->postJson(route('feeder.complete'), $payload)->assertOk()->assertJson([
            'recorded' => true,
            'status' => 'completed',
        ]);
        $this->postJson(route('feeder.complete'), $payload)->assertOk();

        $this->assertDatabaseCount('feed_history', 1);

        $this->assertDatabaseCount('feed_history', 1);
    }

    public function test_manual_hardware_callback_and_animation_report_record_once(): void
    {
        $feed = $this->manualFeed();
        $command = FeederCommand::create([
            'type' => 'manual',
            'manual_feed_id' => $feed->id,
            'duration' => $feed->duration,
            'status' => 'in_progress',
        ]);

        $this->postJson('/api/feeder/command-done', ['command_id' => $command->id])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->postJson(route('feeder.complete'), [
            'feed_type' => 'manual',
            'feed_id' => $feed->id,
            'command_id' => $command->id,
            'cage_number' => $feed->cage_number,
        ])->assertOk();

        $this->assertDatabaseCount('feed_history', 1);
        $this->assertDatabaseHas('feed_history', [
            'manual_feed_id' => $feed->id,
            'status' => 'completed',
        ]);
    }

    public function test_scheduled_hardware_callback_and_animation_report_record_once(): void
    {
        $schedule = $this->feedingSchedule();
        $command = FeederCommand::create([
            'type' => 'scheduled',
            'schedule_id' => $schedule->id,
            'duration' => $schedule->amount,
            'status' => 'in_progress',
        ]);

        $this->postJson('/api/feeder/command-done', ['command_id' => $command->id])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->postJson(route('feeder.complete'), [
            'feed_type' => 'scheduled',
            'feed_id' => $schedule->id,
            'command_id' => $command->id,
            'cage_number' => $schedule->cage_number,
        ])->assertOk();

        $this->assertDatabaseCount('feed_history', 1);
        $this->assertDatabaseHas('feed_history', [
            'schedule_id' => $schedule->id,
            'status' => 'completed',
        ]);
    }

    public function test_failed_hardware_command_does_not_create_history(): void
    {
        $feed = $this->manualFeed();
        $command = FeederCommand::create([
            'type' => 'manual',
            'manual_feed_id' => $feed->id,
            'duration' => $feed->duration,
            'status' => 'failed',
        ]);

        $this->postJson(route('feeder.complete'), [
            'feed_type' => 'manual',
            'feed_id' => $feed->id,
            'command_id' => $command->id,
        ])->assertOk()->assertJson([
            'recorded' => false,
            'status' => 'failed',
        ]);

        $this->assertDatabaseCount('feed_history', 0);
    }

    private function manualFeed(): ManualFeed
    {
        return ManualFeed::create([
            'duration' => 3,
            'cage_number' => 1,
            'status' => 'feeding_now',
            'started_at' => now(),
        ]);
    }

    private function feedingSchedule(): FeedingSchedule
    {
        return FeedingSchedule::create([
            'time' => now()->format('H:i:s'),
            'days' => [strtolower(now()->format('D'))],
            'amount' => 3,
            'cage_number' => 1,
            'enabled' => true,
            'status' => 'feeding_now',
            'last_feed_at' => now(),
            'feeding_started_at' => now(),
        ]);
    }
}
