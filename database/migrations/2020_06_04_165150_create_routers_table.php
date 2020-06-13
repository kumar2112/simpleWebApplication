<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sap_id',100);
            $table->string('internet_host_name',100);
            $table->string('client_ip_address',80);
            $table->string('mac_address',60);
            $table->tinyInteger('is_deleted')->default('0');
            $table->timestamps();

            $table->unique(['sap_id', 'internet_host_name','client_ip_address','mac_address']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routers');
    }
}
