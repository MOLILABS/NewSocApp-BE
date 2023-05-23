<?php

use App\Common\CustomBlueprint;
use App\Common\CustomSchema;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use App\Common\Constant;

class CreateBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomSchema::create(User::TABLE_NAME, function (CustomBlueprint $table) {
            $roles = array_keys(User::ROLES);
            $table->string('name')->nullable(false);
            $table->string('email')->nullable(false)->unique();
            $table->string('avatar')->nullable();
            $table->string('password')->nullable(false);
            $table->boolean('confirm_email')->default(false);
            $table->dateTime('last_sent')->nullable();
            $table->string('otp',Constant::OTP_LENGHT)->nullable();
            $table->rememberToken()->default(null);
            $table->enum('role', $roles)->nullable(false)->default($roles[5]);
            $table->audit(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CustomSchema::dropIfExists(User::TABLE_NAME);
    }
}
