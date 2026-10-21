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
        Schema::table('specific_classifications', function (Blueprint $table) {
            $table->unsignedInteger('source_row')->nullable()->after('name');
            $table->foreignId('parent_classification_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('specific_classifications', function (Blueprint $table) {
            $table->dropColumn('source_row');
            $table->foreignId('parent_classification_id')->nullable(false)->change();
        });
    }
};
