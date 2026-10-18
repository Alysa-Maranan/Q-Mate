<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Auth\Middleware\Authenticate;
use Tests\TestCase;

class EggCollectionTimeFormatTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(Authenticate::class);

        Schema::create('egg_collections', function (Blueprint $table) {
            $table->id();
            $table->string('cage_pen');
            $table->integer('total_eggs');
            $table->integer('good_eggs');
            $table->integer('cracked_eggs');
            $table->time('collection_time');
            $table->text('notes')->nullable();
            $table->string('egg_size')->default('mixed');
            $table->string('feed_brand')->default('Quail Layer Smash');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('egg_collections');

        parent::tearDown();
    }

    public function test_collection_time_accepts_ampm_and_persists_normalized_time(): void
    {
        $response = $this->postJson('http://localhost/api/egg-collections', [
            'cage_pen' => '1',
            'total_eggs' => 10,
            'good_eggs' => 8,
            'cracked_eggs' => 2,
            'collection_time' => '7:30 PM',
        ]);
        $response->assertOk();

        $this->assertDatabaseHas('egg_collections', [
            'collection_time' => '19:30',
        ]);
        $this->assertSame('19:30', $response->json('collection.collection_time'));

        $id = $response->json('collection.id');
        $this->putJson('http://localhost/api/egg-collections/' . $id, [
            'cage_pen' => '1',
            'total_eggs' => 10,
            'good_eggs' => 8,
            'cracked_eggs' => 2,
            'collection_time' => '12:30 AM',
        ])->assertOk();

        $this->assertDatabaseHas('egg_collections', [
            'id' => $id,
            'collection_time' => '00:30',
        ]);
    }
}
