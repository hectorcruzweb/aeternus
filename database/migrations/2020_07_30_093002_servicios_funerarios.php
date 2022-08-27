<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ServiciosFunerarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios_funerarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            /**campos requeridos para la solicitud del servicio */
            $table->unsignedBigInteger('tipo_solicitud_id');
            $table->foreign('tipo_solicitud_id')->references('id')->on('tipo_solicitud');
            $table->unsignedBigInteger('servicios_funerarios_exhumado_id')->unsigned()->nullable();
            $table->foreign('servicios_funerarios_exhumado_id')->references('id')->on('servicios_funerarios');
            $table->tinyInteger('llamada_b')->nullable();
            $table->string('nombre_afectado')->nullable();
            $table->dateTime('fechahora_solicitud')->nullable();
            $table->string('causa_muerte')->nullable();
            $table->tinyInteger('muerte_natural_b')->nullable();
            $table->tinyInteger('contagioso_b')->nullable();
            $table->string('nombre_informante')->nullable();
            $table->string('telefono_informante')->nullable();
            $table->string('parentesco_informante')->nullable();
            $table->string('ubicacion_recoger')->nullable();
            $table->unsignedBigInteger('recogio_id')->unsigned()->nullable();
            $table->foreign('recogio_id')->references('id')->on('usuarios');
            $table->mediumText('nota_al_recoger')->nullable();
            /**informacion capturada para el contrato */

            /**
             * 
             * resto del servicio 
             * 
             **/

            /**informacion del fallecido */
            $table->unsignedBigInteger('titulos_id')->unsigned()->nullable();
            $table->foreign('titulos_id')->references('id')->on('titulos');
            $table->date('fecha_nacimiento')->nullable();
            $table->unsignedBigInteger('generos_id')->unsigned()->nullable();
            $table->foreign('generos_id')->references('id')->on('generos');
            $table->unsignedBigInteger('nacionalidades_id')->unsigned()->nullable();
            $table->foreign('nacionalidades_id')->references('id')->on('nacionalidades');
            $table->string('lugar_nacimiento')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('direccion_fallecido')->nullable();
            $table->unsignedBigInteger('estados_civiles_id')->unsigned()->nullable();
            $table->foreign('estados_civiles_id')->references('id')->on('estados_civiles');
            $table->unsignedBigInteger('escolaridades_id')->unsigned()->nullable();
            $table->foreign('escolaridades_id')->references('id')->on('escolaridades');
            $table->unsignedBigInteger('afiliaciones_id')->unsigned()->nullable();
            $table->foreign('afiliaciones_id')->references('id')->on('afiliaciones');
            $table->string('afiliacion_nota')->nullable();
            /**fin informacion del fallecido */


            /**informacion del certificado */
            $table->string('folio_certificado')->nullable();
            $table->dateTime('fechahora_defuncion')->nullable();
            $table->unsignedBigInteger('sitios_muerte_id')->unsigned()->nullable();
            $table->foreign('sitios_muerte_id')->references('id')->on('sitios_muerte');
            $table->string('lugar_muerte')->nullable();
            $table->tinyInteger('atencion_medica_b')->nullable();
            $table->string('enfermedades_padecidas')->nullable();
            $table->string('certificado_informante')->nullable();
            $table->string('certificado_informante_telefono')->nullable();
            $table->string('certificado_informante_parentesco')->nullable();
            $table->string('medico_legista')->nullable();
            $table->unsignedBigInteger('estado_afectado_id')->unsigned()->nullable();
            $table->foreign('estado_afectado_id')->references('id')->on('estado_afectado');
            /**FIN informacion del certificado */


            /**informacion de los destinos */
            $table->tinyInteger('embalsamar_b')->default(0);
            $table->string('medico_responsable_embalsamado')->nullable();
            $table->string('preparador')->nullable();

            $table->tinyInteger('velacion_b')->default(0);
            $table->unsignedBigInteger('lugares_servicios_id')->unsigned()->nullable();
            $table->foreign('lugares_servicios_id')->references('id')->on('lugares_servicios');
            $table->string('direccion_velacion')->nullable();
            $table->tinyInteger('cremacion_b')->default(0);
            $table->dateTime('fechahora_cremacion')->nullable();
            $table->string('descripcion_urna')->nullable();
            $table->dateTime('fechahora_entrega_cenizas')->nullable();

            $table->tinyInteger('inhumacion_b')->default(0);
            $table->unsignedBigInteger('cementerios_servicio_id')->unsigned()->nullable();
            $table->foreign('cementerios_servicio_id')->references('id')->on('cementerios_servicio');
            $table->dateTime('fechahora_inhumacion')->nullable();
            $table->unsignedBigInteger('ventas_terrenos_id')->unsigned()->nullable();
            $table->foreign('ventas_terrenos_id')->references('id')->on('ventas_terrenos');
            $table->mediumText('nota_ubicacion')->nullable();

            $table->tinyInteger('traslado_b')->default(0);
            $table->dateTime('fechahora_traslado')->nullable();
            $table->string('destino_traslado')->nullable();

            $table->tinyInteger('aseguradora_b')->default(0);
            $table->string('numero_convenio_aseguradora')->nullable();
            $table->string('aseguradora')->nullable();
            $table->string('telefono_aseguradora')->nullable();

            $table->tinyInteger('misa_b')->nullable();
            $table->dateTime('fechahora_misa')->nullable();
            $table->string('iglesia_misa')->nullable();
            $table->string('direccion_iglesia')->nullable();

            $table->tinyInteger('custodia_b')->nullable();
            $table->string('responsable_custodia')->nullable();
            $table->string('folio_custodia')->nullable();
            $table->string('folio_liberacion')->nullable();
            /**FIN DE DATOS DE DESTINO */

            /**MATERIAL RENTADO */
            $table->tinyInteger('material_velacion_b')->default(0);
            /**FIN DE MATERIAL RENTADO */


            /**datos para el acta de defuncion */
            $table->tinyInteger('acta_b')->default(0);
            $table->string('folio_acta', 50)->nullable();
            $table->dateTime('fechahora_acta')->nullable();
            /**FIN DE datos para el acta de defuncion */


            /**DATOS DE CONTRATO */
            $table->dateTime('fechahora_contrato')->nullable();
            /**el id del cliente va en la tabla de operaciones */
            $table->string('parentesco_contratante')->nullable();
            $table->tinyInteger('plan_funerario_futuro_b')->default(0);
            $table->unsignedBigInteger('ventas_planes_id')->unsigned()->nullable();
            $table->foreign('ventas_planes_id')->references('id')->on('ventas_planes');
            $table->unsignedBigInteger('tipos_contratante_id')->unsigned()->nullable();
            $table->foreign('tipos_contratante_id')->references('id')->on('tipos_contratante');

            /**en caso de que el plan sea para el plan de uso inmediato*/
            $table->tinyInteger('plan_funerario_inmediato_b')->default(0);
            $table->unsignedBigInteger('planes_funerarios_id')->unsigned()->nullable();
            $table->foreign('planes_funerarios_id')->references('id')->on('planes_funerarios');
            $table->string('plan_funerario_original')->nullable();
            $table->unsignedDecimal('costo_plan_original', 10, 2)->nullable();
            /**FIN DE DATOS DE CONTRATO */

            //$table->tinyInteger('exhumar_b')->default(0);
            //$table->dateTime('fechahora_exhumacion')->nullable();
            //$table->tinyInteger('reinhumar_b')->default(0);
            //$table->dateTime('fechahora_reinhumacion')->nullable();

            /**RESTO DE DATOS DE CONTRATO */
            $table->mediumText('nota_servicio')->nullable();
            $table->dateTime('fechahora_registro')->nullable();
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('registro_contrato_id')->unsigned()->nullable();
            $table->foreign('registro_contrato_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('modifico_id')->unsigned()->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_modificacion')->nullable();
            $table->unsignedBigInteger('cancelo_id')->unsigned()->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->tinyInteger('status')->default(1);
            /**FIN DE RESTO DE DATOS DE CONTRATO */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicios_funerarios');
    }
}