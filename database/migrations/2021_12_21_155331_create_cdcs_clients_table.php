<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCdcsClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdcs_clients', function (Blueprint $table) {
            $table->unsignedBigInteger('clientID');
            $table->foreign('clientID')
                    ->references('id')->on('clients');

            $table->unsignedBigInteger('cdcID');
            $table->foreign('cdcID')
                    ->references('id')->on('cdcs');
                            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cdcs_clients');
    }
}
