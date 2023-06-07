<?php

use App\Common\CustomBlueprint;
use App\Models\Category;
use App\Models\CategoryChannel;
use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\ChannelUser;
use App\Models\Group;
use App\Models\Growth;
use App\Models\Platform;
use App\Models\Team;
use App\Models\TeamUser;
use Illuminate\Database\Migrations\Migration;
use App\Common\CustomSchema;

class CreateChannelsTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomSchema::create(Category::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->char('name');
            $table->char('description');
        });

        CustomSchema::create(Group::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->char('name');
            $table->char('description');
        });

        CustomSchema::create(Platform::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->string('name');
            $table->string('description');
            $table->string('logo');
        });

        CustomSchema::create(Channel::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->char('channel_id');
            $table->char('link')->nullable();
            $table->char('name');
            $table->string('logo', '2000');
            $table->unsignedInteger('platform_id');
            $table->foreign('platform_id')->references('id')->on(Platform::retrieveTableName());
            $table->unique(['channel_id', 'platform_id']);
        });

        CustomSchema::create(Team::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->char('name');
            $table->char('description');
        });

        CustomSchema::create(Growth::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->char('detail');
            $table->date('date');
        });

        CustomSchema::create(CategoryChannel::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on(Category::retrieveTableName())->onDelete('cascade');
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on(Channel::retrieveTableName())->onDelete('cascade');
        });

        CustomSchema::create(ChannelGroup::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on(Group::retrieveTableName())->onDelete('cascade');
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on(Channel::retrieveTableName())->onDelete('cascade');
        });

        CustomSchema::create(ChannelUser::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on(Channel::retrieveTableName())->onDelete('cascade');
            $table->boolean('is_supporter')->default(false);
            $table->boolean('is_responsible')->default(false);
            $table->unique(['channel_id', 'user_id']);
        });

        CustomSchema::create(TeamUser::retrieveTableName(), function (CustomBlueprint $table) {
            $table->audit();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('team_id')->unsigned();
            $table->foreign('team_id')->references('id')->on(Team::retrieveTableName())->onDelete('cascade');
            $table->boolean('is_leader')->default(false);
            $table->unique(['team_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CustomSchema::dropIfExists(Category::retrieveTableName());
        CustomSchema::dropIfExists(Group::retrieveTableName());
        CustomSchema::dropIfExists(Platform::retrieveTableName());
        CustomSchema::dropIfExists(Team::retrieveTableName());
        CustomSchema::dropIfExists(Channel::retrieveTableName());
        CustomSchema::dropIfExists(Growth::retrieveTableName());
        CustomSchema::dropIfExists(CategoryChannel::retrieveTableName());
        CustomSchema::dropIfExists(ChannelGroup::retrieveTableName());
        CustomSchema::dropIfExists(ChannelUser::retrieveTableName());
        CustomSchema::dropIfExists(TeamUser::retrieveTableName());
    }
}
