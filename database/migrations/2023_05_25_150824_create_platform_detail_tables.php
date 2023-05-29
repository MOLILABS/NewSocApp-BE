<?php

use App\Models\Channel;
use App\Models\Platform;
use App\Common\CustomSchema;
use App\Models\TiktokDetail;
use App\Models\YoutubeDetail;
use App\Models\FacebookDetail;
use App\Common\CustomBlueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformDetailTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomSchema::create(FacebookDetail::retrieveTableName(), function (CustomBlueprint $table) {
            $table->float('revenue');
            $table->timestamp('date')->useCurrent();
            $table->unsignedInteger('view');
            $table->unsignedInteger('reach');
            $table->unsignedInteger('follow');
            $table->unsignedInteger('post_amount');

            $table->audit();
        });

        CustomSchema::create(YoutubeDetail::retrieveTableName(), function (CustomBlueprint $table) {
            $table->float('revenue');
            $table->timestamp('date')->useCurrent();
            $table->unsignedInteger('view');
            $table->unsignedInteger('subscriber');
            $table->unsignedInteger('video_amount');

            $table->audit();
        });

        CustomSchema::create(TiktokDetail::retrieveTableName(), function (CustomBlueprint $table) {
            $table->float('revenue');
            $table->timestamp('date')->useCurrent();
            $table->unsignedInteger('follow');
            $table->unsignedInteger('like');
            $table->unsignedInteger('video_amount');

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
        CustomSchema::dropIfExists(FacebookDetail::retrieveTableName());
        CustomSchema::dropIfExists(YoutubeDetail::retrieveTableName());
        CustomSchema::dropIfExists(TiktokDetail::retrieveTableName());
    }
}
