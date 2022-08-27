<?php

use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //administrador del sistema
        DB::table('usuarios')->insert([
            'nombre' => 'Administrador General',
            'email' => 'admin@admin.com',
            'genero' => 1,
            'telefono' => '',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => '',
            'roles_id' => 1, //rol de superusuario
            'status' => 1
        ]);

        //usuario del gerente princial
        DB::table('usuarios')->insert([
            'nombre' => 'Gerente Gral.',
            'email' => 'administracion@aeternus.com.mx',
            'genero' => 2,
            'telefono' => '',
            'email_verified_at' => now(),
            'password' => Hash::make('administracion'), // password
            'remember_token' => '',
            'roles_id' => 2, //rol de Admin
            'status' => 1
        ]);

        /**insertnado los puestos a los 2 primeros usuarios genrales */

        $puestos = DB::table('puestos')->get();

        foreach ($puestos as $puesto) {
            DB::table('usuarios_puestos')->insert([
                'usuarios_id' => 1,
                'puestos_id' => $puesto->id
            ]);
            DB::table('usuarios_puestos')->insert([
                'usuarios_id' => 2,
                'puestos_id' => $puesto->id
            ]);
        }
    }
}