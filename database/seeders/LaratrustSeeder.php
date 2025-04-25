<?php

namespace Database\Seeders;

use App\Helpers\MyApp;
use App\Helpers\PermissionsProcess;
use App\Models\Role;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Truncating User, Role and Permission tables');

        //TODO: This command delete all records in the table
        $this->truncateLaratrustTables();

        $role = $this->createAdminRole();

        $mapPermission = config('laratrust_seeder');

        $permissions = $this->seedPermissions($mapPermission);

        $role->permissions()->sync($permissions);

        $this->createUserRole();
    }

    private function createAdminRole(){
        $faker = Faker::create();
        $name = PermissionsProcess::ROLE_ADMIN;
        $role = Role::query()->create([
            'name' => $name,
            'display_name' => ucwords(str_replace('_', ' ', $name)),
            'description' => ucwords(str_replace('_', ' ', $name)),
        ]);

        $this->command->info('Creating Role ' . strtoupper($name));

        $user = User::query()->create([
            "name" => "super admin",
            "first_name" => "super",
            "last_name" => "admin",
            "role" => $name,
            "email" => "super_admin@admin.com",
            "password" => Hash::make(MyApp::PASSWORD),
            'phone' => $faker->phoneNumber(),
            'address' => $faker->address(),
            'image' => $faker->imageUrl(),
            "email_verified_at" => now(),
        ]);

        $this->command->info("Creating Super Admin user");

        $user->addRole($role);

        return $role;
    }

    private function createUserRole(){
        $faker = Faker::create();
        $name = PermissionsProcess::ROLE_USER;
        $role = Role::query()->create([
            'name' => $name,
            'display_name' => ucwords(str_replace('_', ' ', $name)),
            'description' => ucwords(str_replace('_', ' ', $name)),
        ]);

        $this->command->info('Creating Role ' . strtoupper($name));

        $user = User::query()->create([
            "name" => "moner khalil",
            "first_name" => "moner",
            "last_name" => "khalil",
            "email" => "moner_khalil@user.com",
            "role" => $name,
            "password" => Hash::make(MyApp::PASSWORD),
            'phone' => $faker->phoneNumber(),
            'address' => $faker->address(),
            'image' => $faker->imageUrl(),
            "email_verified_at" => now(),
        ]);

        $this->command->info("Creating user");

        $user->addRole($role);

        return $role;
    }

    /**
     * Truncates all the laratrust tables and the users table
     *
     * @return  void
     */
    public function truncateLaratrustTables()
    {
        $this->command->info('Truncating User, Role and Permission tables');
        Schema::disableForeignKeyConstraints();

        DB::table('permission_role')->truncate();
        DB::table('permission_user')->truncate();
        DB::table('role_user')->truncate();

        if (Config::get('laratrust_seeder.truncate_tables')) {
            DB::table('roles')->truncate();
            DB::table('permissions')->truncate();

            if (Config::get('laratrust_seeder.create_users')) {
                $usersTable = (new \App\Models\User)->getTable();
                DB::table($usersTable)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    public function seedPermissions(array $permissionsConfig, $parentId = null, $level = 1)
    {
        $permissionIds = [];
        foreach ($permissionsConfig as $permission => $children) {
            if (is_array($children)) {
                // If it's a parent permission
                $parentPermission = DB::table("permissions")->where("name",$permission)->first();
                if (is_null($parentPermission)){
                    DB::table("permissions")->insert([
                        'name' => $permission,
                        'display_name' => ucwords(str_replace('_', ' ', $permission)),
                        'description' => ucwords(str_replace('_', ' ', $permission)),
                        'parent_id' => $parentId,
                        'level' => $level,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $parentPermission = DB::table("permissions")->where("name",$permission)->first();
                }

                $this->command->info('Creating Permission ' . $permission . ' at Level ' . $level);

                // Recursively seed child permissions, incrementing the level

                $childPermissionIds = $this->seedPermissions($children, $parentPermission->id, $level + 1);

                $permissionIds = array_merge($permissionIds, $childPermissionIds, [$parentPermission->id]);

            } else {

                $childPermission = DB::table("permissions")->where("name",$children)->first();
                if (is_null($childPermission)){
                    DB::table("permissions")->insert([
                        'name' => $children,
                        'display_name' => ucwords(str_replace('_', ' ', $children)),
                        'description' => ucwords(str_replace('_', ' ', $children)),
                        'parent_id' => $parentId,
                        'level' => $level,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $childPermission = DB::table("permissions")->where("name",$children)->first();
                }

                $this->command->info('Creating Permission ' . $children . ' at Level ' . $level);

                $permissionIds[] = $childPermission->id;
            }
        }

        return $permissionIds;
    }
}
