<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up()
	{
		Schema::table('redirects', function (Blueprint $table) {
			$table->integer('code')->default(301)->after('to');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down()
	{
		Schema::table('redirects', function (Blueprint $table) {
			$table->dropColumn('code');
		});
	}
};