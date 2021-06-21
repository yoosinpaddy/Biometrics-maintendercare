<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_reports', function (Blueprint $table) {
            $table->id();
            $table->string('shortCode');
            $table->string('phoneNumber')->unique();
            $table->string('keyword');
            $table->string('updateType');//The value could either be addition or deletion
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_reports');
    }
}
