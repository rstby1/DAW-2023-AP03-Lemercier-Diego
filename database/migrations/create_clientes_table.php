<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('clientes', function (Blueprint $table) {
            $table->bigInteger('CUITCliente')->primary();
            $table->string('RazonSocial', 30);
            $table->integer('NroCliente')->unsigned();
            $table->double('YTD', 15, 2)->default(0)->nullable(); // Year to Date
            $table->integer('tier')->default(0)->nullable(); // Tier (1, 2, 3)
        });
        Schema::table('clientes', function (Blueprint $table) {
            $table->index('CUITCliente');
        });
        Schema::table('clientes', function (Blueprint $table) {
            $table->foreign('CUITCliente')->references('CUITCliente')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('clientes');
    }
};
