<?php

namespace Tests\Feature\User;

use App\Http\Controllers\User\CartController;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use ReflectionClass;
use Tests\TestCase;

class CartCheckoutFeeTest extends TestCase
{
    use RefreshDatabase;

    /** Invoke a private pricing helper without needing the controller's services. */
    private function callPricingHelper(string $methodName, array $arguments): mixed
    {
        $reflection = new ReflectionClass(CartController::class);
        $controller = $reflection->newInstanceWithoutConstructor();

        return $reflection
            ->getMethod($methodName)
            ->invokeArgs($controller, $arguments);
    }

    private function htCartItem(string $detail, int $price, int $quantity = 1): array
    {
        return [
            'transaction_type' => 'Handy Talkie',
            'transaction_detail' => $detail,
            'price' => $price,
            'quantity' => $quantity,
        ];
    }

    public function test_uv5r_only_does_not_trigger_transport_fee(): void
    {
        $fee = $this->callPricingHelper(
            'calculateTransportFee',
            [[
                $this->htCartItem('HT UV-5R', 50000, 3),
            ], 'Organisasi External', 2]
        );

        $this->assertSame(0, $fee);
    }

    public function test_eligible_ht_below_threshold_has_transport_fee(): void
    {
        // Rp25.000 x 2 hari x 2 unit = Rp100.000.
        $fee = $this->callPricingHelper(
            'calculateTransportFee',
            [[
                $this->htCartItem('HT UV-82', 25000, 2),
            ], 'Organisasi External', 2]
        );

        $this->assertSame(15000, $fee);
    }

    public function test_eligible_ht_at_threshold_has_free_transport(): void
    {
        // Rp25.000 x 2 hari x 3 unit = Rp150.000.
        $fee = $this->callPricingHelper(
            'calculateTransportFee',
            [[
                $this->htCartItem('HT 888s', 25000, 3),
            ], 'Organisasi External', 2]
        );

        $this->assertSame(0, $fee);
    }

    public function test_uv5r_price_does_not_count_toward_transport_threshold(): void
    {
        $fee = $this->callPricingHelper(
            'calculateTransportFee',
            [[
                $this->htCartItem('HT UV-5R', 100000, 5),
                $this->htCartItem('HT UV-82', 10000, 1),
            ], 'Organisasi External', 1]
        );

        // UV-5R value is excluded; eligible HT total is only Rp10.000.
        $this->assertSame(15000, $fee);
    }

    public function test_ht_fee_only_applies_to_uv82_or_888s_for_non_student_council(): void
    {
        $uv5rFee = $this->callPricingHelper(
            'calculateHtFee',
            [[ $this->htCartItem('HT UV-5R', 50000) ], 'Organisasi External']
        );
        $uv82Fee = $this->callPricingHelper(
            'calculateHtFee',
            [[ $this->htCartItem('HT UV-82', 25000) ], 'Organisasi External']
        );
        $studentCouncilFee = $this->callPricingHelper(
            'calculateHtFee',
            [[ $this->htCartItem('HT UV-82', 25000) ], 'Student Council']
        );

        $this->assertSame(0, $uv5rFee);
        $this->assertSame(20000, $uv82Fee);
        $this->assertSame(0, $studentCouncilFee);
    }

    public function test_rental_duration_never_drops_below_one_day(): void
    {
        // A one-day date gap previously resulted in (gap - 1) = 0.
        $days = $this->callPricingHelper(
            'getRentalDays',
            ['2026-10-18', '2026-10-19']
        );

        $this->assertSame(1, $days);
    }

    public function test_total_price_ignores_legacy_transport_fee_for_uv5r_only_order(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::create([
            'name' => 'Test HT Fee Category',
            'slug' => 'test-ht-fee-category',
        ]);

        $item = Item::create([
            'name' => 'HT UV-5R Test',
            'category_id' => $category->id,
            'transaction_type' => 'Handy Talkie',
            'transaction_detail' => 'HT UV-5R',
            'subcategory' => null,
            'requires_mou' => true,
            'description' => 'Test item',
            'item_photo' => 'test-item.jpg',
            'stock_quantity' => 10,
            'price' => 12000,
            'condition_status' => 'Good',
        ]);

        $order = Order::create([
            'order_number' => 'TEST-' . Str::uuid(),
            'user_id' => $student->id,
            'full_name' => 'Test Student',
            'organization' => 'Organisasi External',
            'position' => 'Bendahara',
            'phone_number' => '08123456789',
            'proker_name' => 'Test Program',
            'ketua_acara' => 'Test Ketua',
            'department' => '-',
            'treasurer_name' => 'Test Treasurer',
            'address' => 'Test Address',
            'notes' => null,
            'order_type' => 'Handy Talkie',
            'funding_source' => 'program_treasurer',
            'start_date' => '2026-10-18',
            'start_time' => '17:00',
            'end_date' => '2026-10-19',
            'end_time' => '18:00',
            'ht_fee' => 0,
            'transport_fee' => 15000,
            'status' => 'Pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'subtotal_price' => 12000,
        ]);

        $this->assertSame(12000, $order->fresh()->total_price);
    }
}
