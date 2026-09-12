<?php

namespace App\Services;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InventoryAvailabilityService
{
    /*
    |--------------------------------------------------------------------------
    | ACTIVE STATUSES
    |--------------------------------------------------------------------------
    |
    | Status yang masih menggunakan stok virtual returnable.
    |
    */

    private const ACTIVE_STATUSES = [
        'Pending',
        'Waiting for MoU',
        'Pending Review MoU',
        'Waiting for Payment',
        'Pending Review Payment',
        'Waiting for Kwitansi',
        'Pending Review Kwitansi',
        'Handed Over',
        'Pending Return Review',
        'Pending Review BA',
    ];

    /*
    |--------------------------------------------------------------------------
    | CLOSED STATUSES
    |--------------------------------------------------------------------------
    */

    private const CLOSED_STATUSES = [
        'Returned',
        'Returned (Damaged)',
        'Resolved (Fine Paid)',
        'Rejected',
        'Cancelled',
    ];

    /*
    |--------------------------------------------------------------------------
    | GET ACTIVE BOOKINGS
    |--------------------------------------------------------------------------
    */

    public function getActiveBookings(int $itemId): Collection
    {
        return OrderItem::query()
            ->where('item_id', $itemId)
            ->whereHas('order', function ($query) {
                $query->whereIn(
                    'status',
                    self::ACTIVE_STATUSES
                );
            })
            ->with('order')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | GET OVERLAPPING QUANTITY
    |--------------------------------------------------------------------------
    |
    | Exact datetime overlap:
    |
    | requestedStart < existingEnd
    | &&
    | requestedEnd > existingStart
    |
    | Jadi:
    |
    | 17:00 - 18:00
    | 18:00 - 19:00
    |
    | tidak dianggap bentrok.
    |
    */

    public function getOverlappingQuantity(
        int $itemId,
        string $startDate,
        string $startTime,
        ?string $endDate,
        ?string $endTime,
        ?int $ignoreOrderId = null
    ): int {
        if (!$endDate || !$endTime) {
            return 0;
        }

        $requestedStart = Carbon::parse(
            "{$startDate} {$startTime}"
        );

        $requestedEnd = Carbon::parse(
            "{$endDate} {$endTime}"
        );

        if (
            $requestedEnd->lessThanOrEqualTo(
                $requestedStart
            )
        ) {
            return 0;
        }

        $query = OrderItem::query()
            ->where('item_id', $itemId)
            ->whereHas('order', function ($query) {
                $query->whereIn(
                    'status',
                    self::ACTIVE_STATUSES
                );
            })
            ->with('order');

        if ($ignoreOrderId !== null) {
            $query->whereHas('order', function ($query) use ($ignoreOrderId) {
                $query->where(
                    'id',
                    '!=',
                    $ignoreOrderId
                );
            });
        }

        $orderItems = $query->get();

        $total = 0;

        foreach ($orderItems as $orderItem) {
            $order = $orderItem->order;

            if (!$order) {
                continue;
            }

            if (
                !$order->start_date ||
                !$order->end_date
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | LEGACY BOOKING TANPA JAM
            |--------------------------------------------------------------------------
            */

            if (
                !$order->start_time ||
                !$order->end_time
            ) {
                $existingStart = Carbon::parse(
                    $order->start_date
                )->startOfDay();

                $existingEnd = Carbon::parse(
                    $order->end_date
                )->endOfDay();

                if (
                    $requestedStart <= $existingEnd &&
                    $requestedEnd >= $existingStart
                ) {
                    $total += (int) $orderItem->quantity;
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | EXISTING BOOKING DENGAN JAM
            |--------------------------------------------------------------------------
            |
            | start_date dan end_date di-cast sebagai Carbon.
            | start_time dan end_time juga di-cast sebagai Carbon
            | melalui Model Order.
            |
            | Jangan menggabungkan object Carbon secara langsung
            | dengan string tanggal karena bisa menghasilkan:
            |
            | 2026-09-19 00:00:00 2026-09-12 17:30:00
            |
            | yang menyebabkan Carbon InvalidFormatException.
            |
            */

            $existingStart = Carbon::parse(
                $order->start_date->format('Y-m-d')
            )->setTimeFromTimeString(
                $order->start_time->format('H:i:s')
            );

            $existingEnd = Carbon::parse(
                $order->end_date->format('Y-m-d')
            )->setTimeFromTimeString(
                $order->end_time->format('H:i:s')
            );

            if (
                $requestedStart < $existingEnd &&
                $requestedEnd > $existingStart
            ) {
                $total += (int) $orderItem->quantity;
            }
        }

        return $total;
    }

    /*
    |--------------------------------------------------------------------------
    | REMAINING STOCK
    |--------------------------------------------------------------------------
    */

    public function getRemainingStock(
        int $totalStock,
        int $bookedQuantity
    ): int {
        return max(
            0,
            $totalStock - $bookedQuantity
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DAILY AVAILABILITY
    |--------------------------------------------------------------------------
    |
    | Dipakai frontend lama untuk menentukan apakah sebuah tanggal
    | memiliki stok yang tersedia.
    |
    | Checkout tetap melakukan pemeriksaan exact datetime lagi.
    |
    */

    public function getDailyAvailability(
        int $itemId,
        int $totalStock,
        int $days = 90
    ): array {
        $bookings = $this->getActiveBookings(
            $itemId
        );

        $availability = [];

        $startDate = Carbon::today();

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);

            $booked = 0;

            foreach ($bookings as $booking) {
                $order = $booking->order;

                if (
                    !$order ||
                    !$order->start_date ||
                    !$order->end_date
                ) {
                    continue;
                }

                $orderStart = Carbon::parse(
                    $order->start_date
                )->startOfDay();

                $orderEnd = Carbon::parse(
                    $order->end_date
                )->endOfDay();

                $dayStart = $date->copy()->startOfDay();
                $dayEnd = $date->copy()->endOfDay();

                if (
                    $dayStart <= $orderEnd &&
                    $dayEnd >= $orderStart
                ) {
                    $booked += (int) $booking->quantity;
                }
            }

            $availability[
                $date->toDateString()
            ] = $this->getRemainingStock(
                $totalStock,
                $booked
            );
        }

        return $availability;
    }

    /*
    |--------------------------------------------------------------------------
    | SCHEDULE
    |--------------------------------------------------------------------------
    */

    public function getSchedule(
        int $itemId
    ): Collection {
        return $this->getActiveBookings(
            $itemId
        )
        ->sortBy(function ($orderItem) {
            $order = $orderItem->order;

            if (!$order) {
                return 0;
            }

            $date = $order->start_date
                ? Carbon::parse(
                    $order->start_date
                )->format('Y-m-d')
                : '1900-01-01';

            $time = $order->start_time
                ? Carbon::parse(
                    $order->start_time
                )->format('H:i:s')
                : '00:00:00';

            return Carbon::parse(
                "{$date} {$time}"
            )->timestamp;
        })
        ->values();
    }
}