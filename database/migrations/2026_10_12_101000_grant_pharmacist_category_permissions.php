<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;
return new class extends Migration { public function up(): void { $role=Role::where('slug',Role::PHARMACIST)->first(); if(!$role)return; $ids=Permission::whereIn('code',['categories.view','categories.create'])->pluck('id'); foreach($ids as $id) DB::table('permission_role')->insertOrIgnore(['role_id'=>$role->id,'permission_id'=>$id]); } public function down(): void {} };