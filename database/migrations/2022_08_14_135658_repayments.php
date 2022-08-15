<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Repayments extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('loan_id');
            $table->double('amount')->unsigned()->default(0);
            $table->integer('emi_no')->unsigned()->default(0);
            $table->date('date');
            $table->integer('status')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('repayments');
    }

}
