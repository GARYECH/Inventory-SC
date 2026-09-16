<?php

namespace App\Exports;

use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new OrdersSummarySheet(),

            new OrdersTransactionSheet(
                'Peralatan',
                'Peralatan'
            ),

            new OrdersTransactionSheet(
                'Handy Talkie',
                'Handy Talkie'
            ),

            new OrdersTransactionSheet(
                'Habis Pakai',
                'Habis Pakai'
            ),

            new OrdersTransactionSheet(
                'Merchandise',
                'Merchandise'
            ),
        ];
    }
}


/*
|--------------------------------------------------------------------------
| SUMMARY SHEET
|--------------------------------------------------------------------------
*/

class OrdersSummarySheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnWidths,
    WithStyles,
    WithEvents
{
    public function collection(): Collection
    {
        return OrderItem::with([
            'order.user',
            'item',
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Transaction Type',
            'Total Order',
            'Total Item Lines',
            'Total Quantity',
            'Total Value',
        ];
    }

    public function map($orderItem): array
    {
        return [
            $orderItem->item?->transaction_type ?? '-',
            0,
            0,
            0,
            0,
        ];
    }

    public function prepareRows($rows)
    {
        $items = collect($rows);

        $types = [
            'Peralatan',
            'Handy Talkie',
            'Habis Pakai',
            'Merchandise',
        ];

        return collect($types)->map(function ($type) use ($items) {

            $filtered = $items->filter(
                function ($orderItem) use ($type) {
                    return $orderItem->item?->transaction_type === $type;
                }
            );

            $orderIds = $filtered
                ->pluck('order_id')
                ->unique()
                ->values();

            $totalQuantity = $filtered->sum(
                function ($orderItem) {
                    return (int) $orderItem->quantity;
                }
            );

            $totalValue = $filtered->sum(
                function ($orderItem) {
                    return (int) ($orderItem->subtotal_price ?? 0);
                }
            );

            return [
                $type,
                $orderIds->count(),
                $filtered->count(),
                $totalQuantity,
                $totalValue,
            ];
        });
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24,
            'B' => 18,
            'C' => 20,
            'D' => 18,
            'E' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '111827',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (
                AfterSheet $event
            ) {
                $sheet =
                    $event->sheet
                        ->getDelegate();

                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue(
                    'A1',
                    'STUDENT COUNCIL INVENTORY'
                );

                $sheet->setCellValue(
                    'A2',
                    'TRANSACTION REPORT'
                );

                $sheet->setCellValue(
                    'A3',
                    'Summary of Inventory Transactions'
                );

                $sheet->setCellValue(
                    'A4',
                    'Generated: ' . now()->format('d M Y H:i')
                );

                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A4:E4');

                $sheet->getStyle('A1:E1')
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 16,
                            'color' => [
                                'rgb' => 'FFFFFF',
                            ],
                        ],
                        'fill' => [
                            'fillType' =>
                                Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => '111827',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' =>
                                Alignment::HORIZONTAL_CENTER,
                            'vertical' =>
                                Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                $sheet->getStyle('A2:E2')
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 13,
                            'color' => [
                                'rgb' => '4338CA',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' =>
                                Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);

                $sheet->getStyle('A3:E4')
                    ->applyFromArray([
                        'font' => [
                            'italic' => true,
                            'color' => [
                                'rgb' => '6B7280',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' =>
                                Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);

                $sheet->getStyle('A5:E9')
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                    Border::BORDER_THIN,
                                'color' => [
                                    'rgb' => 'D1D5DB',
                                ],
                            ],
                        ],
                    ]);

                $sheet->getStyle('E6:E9')
                    ->getNumberFormat()
                    ->setFormatCode(
                        '"Rp" #,##0'
                    );

                $sheet->freezePane('A6');
                $sheet->setAutoFilter('A5:E9');

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(5)->setRowHeight(24);
            },

        ];
    }
}


/*
|--------------------------------------------------------------------------
| TRANSACTION SHEET
|--------------------------------------------------------------------------
*/

class OrdersTransactionSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnWidths,
    WithStyles,
    WithEvents
{
    public function __construct(
        private string $title,
        private string $transactionType
    ) {
    }

    public function collection(): Collection
    {
        return OrderItem::with([
            'order.user',
            'item.category',
        ])
            ->whereHas(
                'item',
                function ($query) {
                    $query->where(
                        'transaction_type',
                        $this->transactionType
                    );
                }
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Order #',
            'Student Name',
            'Organization',
            'Position',
            'Proker / Event',
            'Transaction Type',
            'Transaction Detail',
            'Subcategory',
            'Item',
            'Qty',
            'Size',
            'Warna',
            'Unit Price',
            'Size Additional',
            'Subtotal',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Status',
        ];
    }

    public function map($orderItem): array
    {
        $order =
            $orderItem->order;

        $item =
            $orderItem->item;

        $color =
            null;

        if (
            $item &&
            $item->transaction_type === 'Merchandise' &&
            $item->subcategory === 'Baju'
        ) {
            $color =
                $orderItem->color_number;
        }

        return [
            $order?->order_number ?? '-',

            $order?->user?->name ?? '-',

            $order?->organization ?? '-',

            $order?->position ?? '-',

            $order?->proker_name ?? '-',

            $item?->transaction_type ?? '-',

            $item?->transaction_detail ?? '-',

            $item?->subcategory ?? '-',

            $item?->name ?? '-',

            (int) $orderItem->quantity,

            $orderItem->size ?? '-',

            $color ?? '-',

            (int) ($item?->price ?? 0),

            (int) (
                $orderItem->size_additional_price
                ?? 0
            ),

            (int) (
                $orderItem->subtotal_price
                ?? 0
            ),

            $order?->start_date
                ? $order->start_date->format('d M Y')
                : '-',

            $order?->start_time
                ? $order->start_time->format('H:i')
                : '-',

            $order?->end_date
                ? $order->end_date->format('d M Y')
                : '-',

            $order?->end_time
                ? $order->end_time->format('H:i')
                : '-',

            $order?->status ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 24,
            'C' => 24,
            'D' => 20,
            'E' => 26,
            'F' => 18,
            'G' => 22,
            'H' => 18,
            'I' => 28,
            'J' => 10,
            'K' => 12,
            'L' => 16,
            'M' => 16,
            'N' => 17,
            'O' => 18,
            'P' => 14,
            'Q' => 12,
            'R' => 14,
            'S' => 12,
            'T' => 24,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' =>
                        Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '111827',
                    ],
                ],
                'alignment' => [
                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER,
                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],

        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (
                AfterSheet $event
            ) {
                $sheet =
                    $event->sheet
                        ->getDelegate();

                $highestRow =
                    $sheet->getHighestRow();

                $highestColumn =
                    $sheet->getHighestColumn();

                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue(
                    'A1',
                    'STUDENT COUNCIL INVENTORY'
                );

                $sheet->setCellValue(
                    'A2',
                    'TRANSACTION REPORT — ' .
                    strtoupper(
                        $this->transactionType
                    )
                );

                $sheet->setCellValue(
                    'A3',
                    'Inventory transaction details'
                );

                $sheet->setCellValue(
                    'A4',
                    'Generated: ' .
                    now()->format(
                        'd M Y H:i'
                    )
                );

                $sheet->mergeCells(
                    "A1:{$highestColumn}1"
                );

                $sheet->mergeCells(
                    "A2:{$highestColumn}2"
                );

                $sheet->mergeCells(
                    "A3:{$highestColumn}3"
                );

                $sheet->mergeCells(
                    "A4:{$highestColumn}4"
                );

                $sheet->getStyle(
                    "A1:{$highestColumn}1"
                )->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => [
                            'rgb' => 'FFFFFF',
                        ],
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '111827',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle(
                    "A2:{$highestColumn}2"
                )->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 13,
                        'color' => [
                            'rgb' => '4338CA',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle(
                    "A3:{$highestColumn}4"
                )->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'color' => [
                            'rgb' => '6B7280',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $headerRow = 5;

                $dataStartRow = 6;

                $sheet->getStyle(
                    "A{$headerRow}:{$highestColumn}{$headerRow}"
                )->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => [
                            'rgb' => 'FFFFFF',
                        ],
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '374151',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if ($highestRow >= $dataStartRow) {

                    $sheet->getStyle(
                        "A{$headerRow}:{$highestColumn}{$highestRow}"
                    )->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                    Border::BORDER_THIN,
                                'color' => [
                                    'rgb' => 'D1D5DB',
                                ],
                            ],
                            'insideHorizontal' => [
                                'borderStyle' =>
                                    Border::BORDER_HAIR,
                                'color' => [
                                    'rgb' => 'E5E7EB',
                                ],
                            ],
                        ],
                        'alignment' => [
                            'vertical' =>
                                Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getStyle(
                        "J{$dataStartRow}:J{$highestRow}"
                    )->getAlignment()
                        ->setHorizontal(
                            Alignment::HORIZONTAL_CENTER
                        );

                    $sheet->getStyle(
                        "M{$dataStartRow}:O{$highestRow}"
                    )->getNumberFormat()
                        ->setFormatCode(
                            '"Rp" #,##0'
                        );
                }

                $sheet->freezePane(
                    'A6'
                );

                $sheet->setAutoFilter(
                    "A5:{$highestColumn}{$highestRow}"
                );

                $sheet->getRowDimension(1)
                    ->setRowHeight(28);

                $sheet->getRowDimension(2)
                    ->setRowHeight(22);

                $sheet->getRowDimension(5)
                    ->setRowHeight(34);
            },

        ];
    }
}