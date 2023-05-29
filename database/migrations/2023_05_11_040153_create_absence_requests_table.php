<?php


use App\Common\Constant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use App\Common\CustomBlueprint;
use App\Models\AbsenceType;
use App\Common\CustomSchema;
use App\Models\AbsenceRequest;
use Illuminate\Support\Facades\Schema;


class CreateAbsenceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        CustomSchema::create(AbsenceRequest::retrieveTableName(), function (CustomBlueprint $table) {
            $status = AbsenceRequest::REQUEST_STATUS;
            $table->timestamp('date')->useCurrent();
            $table->unsignedInteger('absence_type_id')->nullable(false);
            $table->string('reason');
            $table->unsignedInteger('user_id')->nullable(false);
            $table->enum('status', $status)->default($status[0]);
            $table->foreign('absence_type_id')->references('id')->on(AbsenceType::retrieveTableName());
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('last_sent')->nullable();
            $table->string('otp',Constant::OTP_LENGTH)->nullable();


            $table->audit();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CustomSchema::dropIfExists(AbsenceRequest::retrieveTableName());
    }
}
