<?php
use Illuminate\Support\Facades\Schema; use Illuminate\Database\Schema\Blueprint; use Illuminate\Database\Migrations\Migration; class AddInventoryToProducts extends Migration { public function up() { if (!Schema::hasColumn('products', 'inventory')) { Schema::table('products', function (Blueprint $sp390b4b) { $sp390b4b->tinyInteger('inventory')->default(\App\User::INVENTORY_AUTO)->after('enabled'); $sp390b4b->tinyInteger('fee_type')->default(\App\User::FEE_TYPE_AUTO)->after('inventory'); }); } } public function down() { foreach (array('inventory', 'fee_type') as $sp77f92d) { try { Schema::table('products', function (Blueprint $sp390b4b) use($sp77f92d) { $sp390b4b->dropColumn($sp77f92d); }); } catch (\Throwable $sp3f4aab) { } } } }