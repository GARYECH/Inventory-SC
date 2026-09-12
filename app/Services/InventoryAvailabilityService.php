<?php

namespace App\Services;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InventoryAvailabilityService
{
    /**
     * Status yang dianggap masih memakai stok returnable.
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

    /**
     * Status yang sudah tidak memakai stok returnable.
     */
    private const CLOSED_STATUSES = [
        'Returned',
        'Returned (Damaged)',
        'Resolved (Fine Paid)',
        'Rejected',
        'Cancelled',
    ];

    /**
     * Ambil seluruh booking aktif untuk item.
     */
    public function getActiveBookings(int $itemId): Collection
    {
        return OrderItem::query()
            ->where('item_id', $itemId)
            ->whereHas(
                'order',
                function ($query) {
                    $query->whereIn(
                        'status',
                        self::ACTIVE_STATUSES
                    );
                }
            )
            ->with('order')
            ->get();
    }

    /**
     * Hitung jumlah unit yang bentrok pada rentang waktu tertentu.
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

        $query = OrderItem::query()
            ->where('item_id', $itemId)
            ->whereHas(
                'order',
                function ($orderQuery) {
                    $orderQuery->whereIn(
                        'status',
                        self::ACTIVE_STATUSES
                    );
                }
            )
            ->with('order');

        if ($ignoreOrderId) {
            $query->whereHas(
                'order',
                function ($orderQuery) use ($ignoreOrderId) {
                    $orderQuery->where(
                        'id',
                        '!=',
                        $ignoreOrderId
                    );
                }
            );
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
             * Order lama yang belum mempunyai jam.
             *
             * Dianggap menggunakan satu hari penuh.
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

            $existingStart = Carbon::parse(
                $order->start_date . ' ' . $order->start_time
            );

            $existingEnd = Carbon::parse(
                $order->end_date . ' ' . $order->end_time
            );

            /*
             * Interval overlap:
             *
             * requestedStart < existingEnd
             * &&
             * requestedEnd > existingStart
             */
            if (
                $requestedStart < $existingEnd &&
                $requestedEnd > $existingStart
            ) {
                $total += (int) $orderItem->quantity;
            }
        }

        return $total;
    }

    /**
     * Hitung sisa stok returnable untuk jadwal tertentu.
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

    /**
     * Buat kalender availability berdasarkan tanggal.
     *
     * Nilai tanggal adalah jumlah stok minimum yang tersedia
     * pada hari tersebut berdasarkan seluruh booking aktif.
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
            $date = $startDate
                ->copy()
                ->addDays($i);

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
                );

                $orderEnd = Carbon::parse(
                    $order->end_date
                );

                if (
                    $date->copy()->startOfDay()
                        ->lte($orderEnd->endOfDay()) &&
                    $date->copy()->endOfDay()
                        ->gte($orderStart->startOfDay())
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

    /**
     * Daftar booking untuk halaman schedule.
     */
    public function getSchedule(
        int $itemId
    ): Collection {
        return $this->getActiveBookings(
            $itemId
        )
        ->sortBy(
            function ($orderItem) {
                $order = $orderItem->order;

                if (!$order) {
                    return '';
                }

                return Carbon::parse(
                    ($order->start_date ?? '1900-01-01')
                    . ' '
                    . ($order->start_time ?? '00:00')
                )->timestamp;
            }
        )
        ->values();
    }
}