<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     
        // permission::create(['name' => 'users.index', 'guard_name' => 'api']);
        //  permission::create(['name' => 'users.edit', 'guard_name' => 'api']);
        //  permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        //  permission::create(['name' => 'users.delete', 'guard_name' => 'api']);  

        // permission::create(['name' => 'roles.index', 'guard_name' => 'api']);
        // permission::create(['name' => 'roles.edit', 'guard_name' => 'api']);
        // permission::create(['name' => 'roles.create', 'guard_name' => 'api']);
        // permission::create(['name' => 'roles.delete', 'guard_name' => 'api'])
        // ;
        // permission::create(['name' => 'school.index', 'guard_name' => 'api']);
        // permission::create(['name' => 'school.edit', 'guard_name' => 'api']);
        // permission::create(['name' => 'school.create', 'guard_name' => 'api']);
        // permission::create(['name' => 'school.delete', 'guard_name' => 'api']);

        // permission::create(['name' => 'jurnal.index', 'guard_name' => 'api']);
        // permission::create(['name' => 'jurnal.edit', 'guard_name' => 'api']);
        // permission::create(['name' => 'jurnal.create', 'guard_name' => 'api']);
        // permission::create(['name' => 'jurnal.delete', 'guard_name' => 'api']);

        permission::create(['name' => 'permission.index', 'guard_name' => 'api']);
    }
}
