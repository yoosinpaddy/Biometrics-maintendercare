<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePremiumReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premium_reports', function (Blueprint $table) {
            $table->id();
            $table->string('sms_id');
            $table->string('status');//Sent: The message has successfully been sent by our network.
                                            //Submitted: The message has successfully been submitted to the MSP (Mobile Service Provider).
                                            //Buffered: The message has been queued by the MSP.
                                            // Rejected: The message has been rejected by the MSP. This is a final status.
                                            //Success: The message has successfully been delivered to the receiver’s handset. This is a final status.
                                            //Failed: The message could not be delivered to the receiver’s handset. This is a final status.
            $table->string('phoneNumber');
            $table->string('networkCode');
            $table->string('failureReason')->nullable();
            $table->string('retryCount');
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
        Schema::dropIfExists('premium_reports');
    }
}
