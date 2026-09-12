<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // service_id/category_id are denormalized alongside subcategory_id
            // (technically reachable via subcategory->category->service) so
            // product listings/filters don't need to join three levels deep.
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('status')->default('active'); // active|inactive
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['service_id', 'slug']); // slug only needs to be unique within its service
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
