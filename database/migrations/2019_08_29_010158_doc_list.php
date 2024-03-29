<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_num');
            $table->string('sort1',45);
            $table->string('sort2',45);
            $table->string('sort3',45);
            $table->mediumText('func_text',200)->nullable();
            $table->string('func_important',40)->nullable();
            $table->string('func_state',40)->nullable();
            $table->string('memo',40)->nullable();
            $table->dateTime('create_date')->nullable();
            $table->dateTime('update_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc_list');
    }
}
