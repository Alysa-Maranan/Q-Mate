<?php

namespace Tests\Feature;

use App\Models\FarmSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuailInventoryAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_and_dressed_quail_sales_reduce_stock_and_analytics_reflect_current_inventory(): void
    {
        $user = User::factory()->create();

        FarmSetting::set('live_quails', '10');
        FarmSetting::set('dead_quails', '1');
        FarmSetting::set('dressed_quails_stock', '5');

        $saleResponse = $this->actingAs($user)->postJson('/api/sales', [
            'product_type' => 'live_quail',
            'quantity' => 2,
            'price' => 180,
            'customer_name' => 'Customer A',
        ]);

        $saleResponse->assertOk();
        $this->assertSame('8', FarmSetting::get('live_quails', '0'));

        $this->actingAs($user)->postJson('/api/sales', [
            'product_type' => 'dressed_quail',
            'quantity' => 2,
            'price' => 250,
            'customer_name' => 'Customer B',
        ]);

        $this->assertSame('3', FarmSetting::get('dressed_quails_stock', '0'));

        $analyticsResponse = $this->actingAs($user)->getJson('/api/analytics?from_date=' . now()->subDays(30)->format('Y-m-d') . '&to_date=' . now()->format('Y-m-d'));

        $analyticsResponse->assertOk()
            ->assertJsonPath('data.farm_quail_stats.live_quails', 8)
            ->assertJsonPath('data.farm_quail_stats.dressed_quails', 3)
            ->assertJsonPath('data.farm_quail_stats.dead_quails', 1)
            ->assertJsonPath('data.farm_quail_stats.total_raised', 9)
            ->assertJsonPath('data.farm_quail_stats.mortality_rate', 11.11);
    }
}
