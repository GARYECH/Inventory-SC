<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminItemTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function createStudent(): User
    {
        return User::factory()->create([
            'role' => 'student',
        ]);
    }

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Test Category',
            'slug' => Str::slug('Test Category'),
        ]);
    }

    private function createItem(
        int $stockQuantity = 100,
        bool $requiresReturn = false
    ): Item {
        $category = $this->createCategory();

        return Item::create([
            'name' => 'Test Item',
            'category_id' => $category->id,
            'stock_quantity' => $stockQuantity,
            'requires_return' => $requiresReturn,
            'price' => 100000,
            'is_active' => true,
        ]);
    }

    private function createOrder(
        User $student,
        string $status,
        Item $item,
        int $quantity = 1,
        array $extraOrderData = []
    ): Order {
        $order = Order::create(array_merge([
            'user_id' => $student->id,
            'transaction_type' => $item->requires_return
                ? 'Peralatan'
                : 'Habis Pakai',
            'status' => $status,
        ], $extraOrderData));

        OrderItem::create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => $quantity,
            'unit_price' => $item->price,
            'subtotal_price' => $item->price * $quantity,
            'requires_return' => $item->requires_return,
        ]);

        return $order->fresh([
            'orderItems.item',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pending Status Tests
    |--------------------------------------------------------------------------
    */

    public function test_pending_to_approved_reduces_stock_for_non_returnable_item(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Pending',
            item: $item,
            quantity: 5
        );

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Approved',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Approved',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 95,
        ]);
    }

    public function test_pending_to_rejected_does_not_reduce_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Pending',
            item: $item,
            quantity: 5
        );

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Rejected',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Rejected',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    public function test_pending_to_cancelled_does_not_reduce_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Pending',
            item: $item,
            quantity: 5
        );

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Cancelled',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Cancelled',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore Stock Tests
    |--------------------------------------------------------------------------
    */

    public function test_approved_to_cancelled_restores_non_returnable_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Approved',
            item: $item,
            quantity: 5
        );

        $item->update([
            'stock_quantity' => 95,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Cancelled',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Cancelled',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    public function test_approved_to_rejected_restores_non_returnable_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Approved',
            item: $item,
            quantity: 8
        );

        $item->update([
            'stock_quantity' => 92,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Rejected',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Rejected',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate Approval Safety
    |--------------------------------------------------------------------------
    */

    public function test_approved_to_approved_does_not_double_decrement_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Approved',
            item: $item,
            quantity: 10
        );

        $item->update([
            'stock_quantity' => 90,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Approved',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 90,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Returnable Item Tests
    |--------------------------------------------------------------------------
    */

    public function test_approving_returnable_item_does_not_reduce_physical_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, true);

        $order = $this->createOrder(
            student: $student,
            status: 'Pending',
            item: $item,
            quantity: 10,
            extraOrderData: [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'start_time' => '17:00',
                'end_time' => '18:00',
            ]
        );

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Approved',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Approved',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    public function test_cancelling_returnable_item_does_not_restore_physical_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, true);

        $order = $this->createOrder(
            student: $student,
            status: 'Approved',
            item: $item,
            quantity: 10,
            extraOrderData: [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'start_time' => '17:00',
                'end_time' => '18:00',
            ]
        );

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Cancelled',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Cancelled',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Validation
    |--------------------------------------------------------------------------
    */

    public function test_approval_is_rejected_when_non_returnable_stock_is_insufficient(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(5, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Pending',
            item: $item,
            quantity: 10
        );

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Approved',
            ]);

        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Pending',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 5,
        ]);
    }

    public function test_non_returnable_stock_is_decremented_only_once_when_order_is_approved(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Pending',
            item: $item,
            quantity: 7
        );

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Approved',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 93,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Approved',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 93,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Order Tests
    |--------------------------------------------------------------------------
    */

    public function test_destroy_order_restores_consumed_non_returnable_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Approved',
            item: $item,
            quantity: 6
        );

        $item->update([
            'stock_quantity' => 94,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    public function test_destroy_pending_order_does_not_restore_non_returnable_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $student,
            status: 'Pending',
            item: $item,
            quantity: 6
        );

        $response = $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    public function test_destroy_returnable_order_does_not_change_physical_stock(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();
        $item = $this->createItem(100, true);

        $order = $this->createOrder(
            student: $student,
            status: 'Approved',
            item: $item,
            quantity: 6,
            extraOrderData: [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'start_time' => '17:00',
                'end_time' => '18:00',
            ]
        );

        $response = $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    public function test_student_cannot_update_order_status(): void
    {
        $student = $this->createStudent();
        $owner = $this->createStudent();
        $item = $this->createItem(100, false);

        $order = $this->createOrder(
            student: $owner,
            status: 'Pending',
            item: $item,
            quantity: 5
        );

        $response = $this->actingAs($student)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'Approved',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Pending',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_quantity' => 100,
        ]);
    }
}