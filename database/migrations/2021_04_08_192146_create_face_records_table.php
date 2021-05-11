<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('face_records', function (Blueprint $table) {
            $table->id();
            $table->string('upi_no');
            $table->foreign('upi_no')->references('upi_no')->on('students');//eno
            $table->string('time_taken');//scandatetime Punch time, milliseconds
            $table->string('device_serial');//macno Equipment serial number
            $table->string('event');//operatorno Punch Type :
                                    // face_0: successful face recognition
                                    // face_2: stranger brushes face
                                    // card_0: swipe card successfully
                                    // card_2: Invalid card
                                    // idcard_0: Witness matching successful
                                    // idcard_2: witness match failure
                                    // faceAndcard_0: successful card+face dual authentication
                                    // faceAndcard_2: card+face dual authentication failure
                                    // open_0: button to open the door.
                                    // qrcode_0: QR Code Success
                                    // qrcode_2: unregistered QR code (no counterpart for QR code)
                                    // password_0: password to open the door
                                    // -2020-02-25: Adding a record of failed witness comparison
                                    // --2020-09-14 (version 2.2.2): add open door password
            $table->string('status')->nullable();//enter/exit
            $table->string('photo_url')->nullable();//base64
            $table->string('temperature')->nullable();//temperature
            $table->softDeletes();
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
        Schema::dropIfExists('face_records');
    }
}
