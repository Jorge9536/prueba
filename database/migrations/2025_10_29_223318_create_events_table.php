<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->boolean('all_day')->default(false);
            $table->boolean('has_reminder')->default(false);
            $table->integer('reminder_minutes')->nullable();
            $table->string('color')->default('#3498db');
            $table->string('location')->nullable();
            $table->enum('visibility', ['public', 'private'])->default('private');
            $table->boolean('is_admin_event')->default(false);
            $table->json('target_users')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}