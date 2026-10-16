<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('s3_storages', 'signature_version')) {
            return;
        }
        Schema::table('s3_storages', function (Blueprint $table) {
            $table->string('signature_version')->default('S3v4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('s3_storages', function (Blueprint $table) {
            $table->dropColumn('signature_version');
        });
    }
};
