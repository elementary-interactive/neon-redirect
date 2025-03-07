<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up()
  {
    Schema::create('redirects', function (Blueprint $table) {
      $table->uuid('id'); // We are using UUID as primary key.

      $table->string('from');
	  $table->string('to');

      $table->timestamps();
      $table->softDeletes();

      $table->primary('id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down()
  {
      Schema::dropIfExists('redirects');
  }

};