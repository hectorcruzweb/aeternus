<?php

use App\SATImpuestos;
use App\TipoPagos;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PuestosSedder::class);
        /**ajustes para controlar la asginacion de numeros de convenios y titulos sistematizados */
        $this->call(Ajustes::class);
        /**sedders de SAT */
        $this->call(SATRegimenesSeeder::class);
        $this->call(SatImpuestosSeeder::class);
        $this->call(SatMetodosPagoSeeder::class);
        $this->call(SatUsosCfdiSeeder::class);
        $this->call(SATMonedasSeeder::class);
        $this->call(formasPagoSeeder::class);
        $this->call(SatPais::class);
        $this->call(SatUnidadesSeeder::class);
        $this->call(SatProductosServicios::class);
        $this->call(SatTipoComprobanteSeeder::class);
        $this->call(SatTipoRelacion::class);
        /**sedders de SAT */


        /**sedders de empresa */
        $this->call(FunerariaSeeder::class);
        $this->call(LugaresVelacion::class);
        $this->call(CementerioSeeder::class);
        $this->call(FacturacionSeeder::class);
        $this->call(RegistroPublicoSeeder::class);
        /**fin de sedders de empresa */


        /**seeders de usuarios */
        $this->call(ModulosSeeder::class);
        $this->call(Permisos::class);
        $this->call(RolesSeederTable::class);
        $this->call(UsuariosSeeder::class);
        //factory(User::class, 5)->create();
        /**fin seeders de usuarios */

        /**
         * clientes
         */
        $this->call(GenerosSeeder::class);
        $this->call(NacionalidadesSeeder::class);
        $this->call(ClientesSeeder::class);


        /**seeders para el control de ventas del cementerio y pagos */
        $this->call(AjustesPoliticas::class);
        /**seeder de cementerio */
        //
        $this->call(tiposPropiedadesSeeder::class);
        $this->call(PropiedadesSeeder::class);
        $this->call(columnasFilasSeeder::class);
        $this->call(TipoPreciosSeeder::class);
        $this->call(PreciosPropiedadesSeeder::class);
        /**pagos */
        //
        //
        /**fin de seeder de cementerio */
        /**seeders de operaciones pagos */


        /**pendiente, hechas por andres */

        //$this->call(MetodosPagoSeeder::class);
        //$this->call(EstadosTableSeeder::class);
        //$this->call(MunicipiosTableSeeder::class);
        //$this->call(SATImpuestosSeeder::class);
        //$this->call(SATProductosServiciosSeeder::class);
        //$this->call(TiposProductoSeeder::class);
        //$this->call(UnidadesSeeder::class);
        //$this->call(AlmacenesSeeder::class);
        //$this->call(CategoriasSeeder::class);
        //$this->call(FamiliasTableSeeder::class);
        //$this->call(GruposProfecoTableSeeder::class);
        // $this->call(PreciosSeeder::class);


    }
}