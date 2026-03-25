<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            ['id' => 77, 'name' => 'View API Keys',   'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 78, 'name' => 'Create API Keys',  'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 79, 'name' => 'Manage API Keys',  'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 80, 'name' => 'Delete API Keys',  'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['id' => $permission['id']],
                $permission
            );
        }

        // Clear the Spatie permission cache
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->whereIn('id', [77, 78, 79, 80])->delete();

        // Also remove from role_has_permissions pivot
        DB::table('role_has_permissions')->whereIn('permission_id', [77, 78, 79, 80])->delete();

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
