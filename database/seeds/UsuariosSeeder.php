<?php

use Illuminate\Database\Seeder;
use App\User;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	User::create([
            'name' => 'Ingenieria SVA',
            'email' => 'ingenieriasva@claro.com.gt',
            'password' => bcrypt('ingSVAClaro0105'),
        ]);
    }
}
