<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReturnCalendarTest extends TestCase
{
    use RefreshDatabase;

    private function createCalendarOrder(
        User $student,
        string $number,
        string $status,
        ?string $startDate,
        ?string $endDate,
        array $extra = []
    ): Order {
        return Order::create(array_merge([
            'order_number' => $number,
            'user_id' => $student->id,
            'full_name' => 'Test Student',
            'organization' => 'Student Council',
            'position' => 'Bendahara',
            'phone_number' => '08123456789',
            'proker_name' => 'Test Program',
            'ketua_acara' => 'Test Ketua',
            'department' => 'Student Council',
            'treasurer_name' => 'Test Treasurer',
            'address' => 'Test Address',
            'notes' => null,
            'order_type' => 'Peralatan',
            'funding_source' => 'program_treasurer',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $startDate ? '17:00' : null,
            'end_time' => $endDate ? '18:00' : null,
            'status' => $status,
        ], $extra));
    }

    public function test_admin_can_open_calendar_and_see_return_submission_activity(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $order = $this->createCalendarOrder(
            $student,
            'RETURN-CALENDAR-TEST-001',
            'Pending Return Review',
            '2026-10-12',
            '2026-10-14',
            [
                'return_drive_link' => 'https://drive.google.com/test-return-evidence',
                'return_submitted_at' => '2026-10-15 09:00:00',
            ]
        );

        $response = $this->actingAs($admin)->get(
            route('admin.returns.calendar', [
                'month' => '2026-10',
                'date' => '2026-10-15',
            ])
        );

        $response->assertOk();
        $response->assertSee('Inventory Calendar and Timeline');
        $response->assertSee($order->order_number);
        $response->assertSee('Pending Return Review');
        $response->assertSee('Bukti pengembalian dikirim');
        $response->assertSee('Buka Bukti Pengembalian');
    }

    public function test_admin_can_see_pending_order_on_its_scheduled_dates_and_filter_by_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $pendingOrder = $this->createCalendarOrder(
            $student,
            'PENDING-CALENDAR-TEST-001',
            'Pending',
            '2026-10-20',
            '2026-10-22'
        );

        $approvedOrder = $this->createCalendarOrder(
            $student,
            'APPROVED-CALENDAR-TEST-002',
            'Approved',
            '2026-10-20',
            '2026-10-22'
        );

        $response = $this->actingAs($admin)->get(
            route('admin.returns.calendar', [
                'month' => '2026-10',
                'date' => '2026-10-20',
                'status' => 'Pending',
            ])
        );

        $response->assertOk();
        $response->assertSee($pendingOrder->order_number);
        $response->assertSee('Pending');
        $response->assertSee('Mulai penggunaan');
        $response->assertDontSee($approvedOrder->order_number);
    }
}
