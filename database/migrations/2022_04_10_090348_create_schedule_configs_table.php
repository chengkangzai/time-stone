<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('schedule_configs', function (Blueprint $table) {
            $table->id();
            $table->string('intake_code');
            $table->string('grouping');
            $table->text('except')->nullable();
            $table->text('emails')->nullable();
            $table->boolean('is_subscribed')->default(false);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_configs');
    }
};
