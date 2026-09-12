<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return OrderItem::with([
            'order.user',
            'item',
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Order #',
            'Student Name',
            'Item',
            'Qty',
            'Start Date',
            'End Date',
            'Status',
        ];
    }

    public function map($orderItem): array
    {
        return [
            $orderItem->order->order_number,
            $orderItem->order->user->name,
            $orderItem->item->name,
            $orderItem->quantity,
            $orderItem->order->start_date,
            $orderItem->order->end_date,
            $orderItem->order->status,
        ];
    }
}