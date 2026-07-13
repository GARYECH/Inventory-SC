<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection() {
        return Order::with(['user', 'item'])->get();
    }

    public function headings(): array {
        return ['Order #', 'Student Name', 'Item', 'Qty', 'Start Date', 'End Date', 'Status'];
    }

    public function map($order): array {
        return [
            $order->order_number,
            $order->user->name,
            $order->item->name,
            $order->quantity,
            $order->start_date,
            $order->end_date,
            $order->status,
        ];
    }
}