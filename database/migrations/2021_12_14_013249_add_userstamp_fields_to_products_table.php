<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserstampFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->dropColumn('image');

            $table->boolean('is_active')->default(true)->change();

            $table->foreign('updated_by')->references('id')->on('users')->onDelete('NO ACTION');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('updated_by');
            $table->dropColumn('deleted_by');
            $table->dropColumn('deleted_at');
            $table->text('image')->nullable();
            $table->boolean('is_active')->nullable()->change();
        });
    }
}
