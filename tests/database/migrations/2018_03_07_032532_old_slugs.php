<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;


/**
 * Class OldSlugs
 */
class OldSlugs extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_slugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model');
            $table->string('entity_id');
            $table->string('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('old_slugs');
    }
}
