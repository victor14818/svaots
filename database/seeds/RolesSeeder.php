<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new Role();
        $admin->name = 'admin';
        $admin->display_name = 'User Administrator';
        $admin->description = 'User allowed to manage all functionalities';
        $admin->save();

        $projectOwner = new Role();
        $projectOwner->name = 'owner';
        $projectOwner->display_name = 'Project Owner';
        $projectOwner->description = 'User allowed to manage functionalities of specific project';
        $projectOwner->save();

    }
}
