<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('businessName', 200);
            $table->string('email', 100)->unique();
            $table->string('pIva', 15)->unique();
            $table->string('address', 255)->nullable();
            $table->string('buldingNum', 10)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('province', 2)->nullable();
            $table->string('country', 255)->nullable();
            $table->string('postalCode', 5)->nullable();
            $table->string('phone', 10)->nullable();
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
        Schema::dropIfExists('clients');
    }
}
