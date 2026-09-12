<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('orders', 'start_time')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->time('start_time')
                    ->nullable()
                    ->after('start_date');
            });
        }

        if (!Schema::hasColumn('orders', 'end_time')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->time('end_time')
                    ->nullable()
                    ->after('end_date');
            });
        }


        /*
        |--------------------------------------------------------------------------
        | ORDER ITEMS
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('order_items', 'size')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('size')
                    ->nullable()
                    ->after('quantity');
            });
        }

        if (!Schema::hasColumn('order_items', 'design_link')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->text('design_link')
                    ->nullable()
                    ->after('size');
            });
        }

        if (!Schema::hasColumn('order_items', 'size_additional_price')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->integer('size_additional_price')
                    ->default(0)
                    ->after('design_link');
            });
        }


        /*
        |--------------------------------------------------------------------------
        | ORDER MOU DOCUMENTS
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasTable('order_mou_documents')) {
            Schema::create('order_mou_documents', function (Blueprint $table) {
                $table->id();

                $table->foreignId('order_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('mou_type');

                $table->string('signed_file_path')
                    ->nullable();

                $table->timestamps();

                $table->unique([
                    'order_id',
                    'mou_type',
                ]);
            });
        }
    }


    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ORDER MOU DOCUMENTS
        |--------------------------------------------------------------------------
        */

        Schema::dropIfExists(
            'order_mou_documents'
        );


        /*
        |--------------------------------------------------------------------------
        | ORDER ITEMS
        |--------------------------------------------------------------------------
        */

        $orderItemColumns = [];

        foreach ([
            'size',
            'design_link',
            'size_additional_price',
        ] as $column) {

            if (Schema::hasColumn(
                'order_items',
                $column
            )) {
                $orderItemColumns[] = $column;
            }
        }

        if (!empty($orderItemColumns)) {
            Schema::table(
                'order_items',
                function (Blueprint $table) use ($orderItemColumns) {
                    $table->dropColumn(
                        $orderItemColumns
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

        $orderColumns = [];

        foreach ([
            'start_time',
            'end_time',
        ] as $column) {

            if (Schema::hasColumn(
                'orders',
                $column
            )) {
                $orderColumns[] = $column;
            }
        }

        if (!empty($orderColumns)) {
            Schema::table(
                'orders',
                function (Blueprint $table) use ($orderColumns) {
                    $table->dropColumn(
                        $orderColumns
                    );
                }
            );
        }
    }
};