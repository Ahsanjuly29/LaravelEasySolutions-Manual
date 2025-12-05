<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('email', 191)->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->timestamps();

            // Example index: frequently queried by email/phone
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
