<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('message_id')->unique();
            $table->string('subject');
            $table->text('body_html')->nullable();
            $table->text('body_text')->nullable();
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->json('to')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->json('attachments')->nullable();
            $table->datetime('received_at');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->string('outlook_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('service_order_id');
            $table->index('received_at');
            $table->index('is_read');
            $table->index('is_archived');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};

