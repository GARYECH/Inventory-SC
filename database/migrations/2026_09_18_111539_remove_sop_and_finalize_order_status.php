<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | NORMALIZE OLD ORDER STATUS
        |--------------------------------------------------------------------------
        |
        | Data lama mungkin masih menggunakan:
        |
        | Pending SOP
        |
        | Status final sistem:
        |
        | Pending
        |
        */

        if (
            Schema::hasTable('orders') &&
            Schema::hasColumn('orders', 'status')
        ) {
            DB::table('orders')
                ->where(
                    'status',
                    'Pending SOP'
                )
                ->update([
                    'status' => 'Pending',
                ]);

            Schema::table(
                'orders',
                function (Blueprint $table) {
                    $table->string('status')
                        ->default('Pending')
                        ->change();
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REMOVE SOP ACCEPTANCE
        |--------------------------------------------------------------------------
        */

        if (
            Schema::hasTable('orders') &&
            Schema::hasColumn(
                'orders',
                'is_sop_accepted'
            )
        ) {
            Schema::table(
                'orders',
                function (Blueprint $table) {
                    $table->dropColumn(
                        'is_sop_accepted'
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REMOVE SOP SETTING
        |--------------------------------------------------------------------------
        */

        if (
            Schema::hasTable('settings')
        ) {
            $sopPath =
                DB::table('settings')
                    ->where(
                        'key',
                        'sop_pdf_path'
                    )
                    ->value('value');

            /*
            |--------------------------------------------------------------------------
            | DELETE SOP SETTING
            |--------------------------------------------------------------------------
            */

            DB::table('settings')
                ->where(
                    'key',
                    'sop_pdf_path'
                )
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | DELETE STORED SOP FILE
            |--------------------------------------------------------------------------
            */

            if (
                !empty($sopPath)
            ) {
                Storage::disk('public')
                    ->delete(
                        $sopPath
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE KNOWN DEFAULT SOP FILE
            |--------------------------------------------------------------------------
            */

            Storage::disk('public')
                ->delete(
                    'documents/SOP_Student_Council.pdf'
                );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | INTENTIONALLY EMPTY
        |--------------------------------------------------------------------------
        |
        | SOP adalah fitur yang sudah dihapus secara permanen
        | dari struktur aplikasi.
        |
        | Migration rollback tidak akan menghidupkan kembali:
        |
        | - Pending SOP
        | - is_sop_accepted
        | - sop_pdf_path
        |
        */
    }
};