<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Organiza el reporte Excel de CFDI con adeudo, operaciones relacionadas y pagos recibidos.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Mapeo compartido de importes y agrupación; no realiza consultas. */
class CfdisAdeudosExport implements WithMultipleSheets
{
    private $datos;
    /**
     * Detalle del método: Conserva las dependencias o datos recibidos para usarlos durante la generación
     * del componente/reporte.
     * Entradas: $datos (datos del reporte ya preparados o del envío validado).
     * Salida / efectos: Instancia inicializada; no ejecuta por sí mismo una descarga ni un envío.
     */
    public function __construct(array $datos) { $this->datos=$datos; }
    /**
     * Detalle del método: Agrupa pagos y operaciones relacionadas por factura para imprimirlos debajo de
     * su CFDI.
     * Entradas: $d (datos agregados y filas preparados para el reporte).
     * Salida / efectos: Bloques con factura, pagos y operaciones; agrupar no recalcula saldos.
     */
    public static function bloques(array $d): array
    {
        $pagos=[];$operaciones=[];
        foreach($d['pagos'] ?? [] as $p)$pagos[(string)$p['factura_id']][]=$p;
        foreach($d['operaciones'] ?? [] as $o)$operaciones[(string)$o['factura_id']][]=$o;
        $bloques=[];
        foreach($d['cfdis'] as $c)$bloques[]=['factura'=>$c,'pagos'=>$pagos[(string)$c['id']] ?? [],'operaciones'=>$operaciones[(string)$c['id']] ?? []];
        return $bloques;
    }
    /** Referencias visibles del módulo, nunca operaciones.id. 
     * Detalle del método: Describe una operación fiscal relacionada con su referencia comercial, fecha,
     * cliente y aviso de cancelación.
     * Entradas: $o (operación relacionada con un CFDI).
     * Salida / efectos: Texto legible que no expone operaciones.id como número de venta.
     */
    public static function operacion(array $o): string
    {
        $tipos=[1=>['Ventas en cementerio','Venta','ventas_terrenos_id'],2=>['Cuotas de Mantenimiento','Venta en cementerio','ventas_terrenos_id'],
            3=>['Servicios funerarios','Servicio','servicios_funerarios_id'],4=>['Venta de planes funerarios','Venta de plan','ventas_planes_id'],5=>['Ventas en general','Venta','ventas_generales_id']];
        $tipo=$tipos[(int)$o['empresa_operaciones_id']] ?? ['Operación relacionada','Referencia',null];
        $numero=$tipo[2] ? ($o[$tipo[2]] ?? null) : null;
        return $tipo[0].' · '.$tipo[1].' '.($numero ?: 'no disponible').' · '.TimbresRecargasExport::fecha($o['fecha_operacion'] ?? null)
            .' · Cliente: '.(trim($o['cliente_operacion'] ?? '') ?: 'No disponible')
            .((int)$o['status']===0 ? ' · Cancelada' : '');
    }
    /**
     * Detalle del método: Define títulos, columnas y filas de cada sección del reporte desde sus datos ya
     * calculados.
     * Entradas: $d (datos agregados y filas preparados para el reporte).
     * Salida / efectos: Arreglo de secciones reutilizable por la exportación; no consulta la base de
     * datos.
     */
    public static function secciones(array $d): array
    {
        $resumen=[];$resumenTipos=[];$varias=count($d['resumen'])>1;
        foreach($d['resumen'] as $r){
            if($varias){$resumen[]=['Importes en '.$r['moneda'],null,null,null,null];$resumenTipos[]='grupo_moneda';}
            $resumen[]=[$r['cantidad'],$r['facturado'],$r['pagos'],$r['egresos'],$r['pendiente']];$resumenTipos[]='resumen';
        }
        $filas=[];$tipos=[];$anterior=null;
        foreach(self::bloques($d) as $n=>$bloque){
            $c=$bloque['factura'];
            if($n>0){$filas[]=array_fill(0,10,null);$tipos[]='separador';}
            if($varias&&$anterior!==$c['moneda']){$fila=array_fill(0,10,null);$fila[0]='Importes en '.$c['moneda'];$filas[]=$fila;$tipos[]='grupo_moneda';$anterior=$c['moneda'];}
            $filas[]=[(string)$c['id'],$c['uuid'] ?: 'No disponible',$c['cliente'] ?: ($c['nombre_receptor'] ?: 'No disponible'),
                TimbresRecargasExport::fecha($c['fecha']),'Ingreso',((int)($c['sat_metodos_pago_id'] ?? 2)===1 ? 'PUE' : 'PPD'),$c['rfc_receptor'],(float)$c['total'],(float)$c['pagos_aplicados'],max(0,(float)$c['saldo_pendiente'])];
            $tipos[]='factura';
            foreach($bloque['operaciones'] as $o){$fila=array_fill(0,10,null);$fila[0]='Operación relacionada: '.self::operacion($o);$filas[]=$fila;$tipos[]='operacion';}
            if(!$bloque['pagos'] && (int)($c['sat_metodos_pago_id'] ?? 2)!==1){$fila=array_fill(0,10,null);$fila[0]='Sin pagos recibidos vigentes';$filas[]=$fila;$tipos[]='sin_pagos';}
            foreach($bloque['pagos'] as $p){
                $fila=array_fill(0,10,null);
                $fila[0]='Pago recibido · Comprobante '.$p['pago_cfdi_id'].' · UUID: '.($p['pago_uuid'] ?: 'No disponible').' · '.TimbresRecargasExport::fecha($p['fecha_pago'] ?: $p['fecha_comprobante']);
                $fila[8]=(float)$p['importe_aplicado'];$filas[]=$fila;$tipos[]='pago';
            }
        }
        return [
            ['nombre'=>'Resumen general','columnas'=>['CFDI por pagar','Total facturado','Total pagado','Egresos aplicados','Saldo pendiente'],
                'filas'=>$resumen,'sumar'=>[],'moneda'=>[1,2,3,4],'tipos_fila'=>$resumenTipos,'nota'=>'Total pagado corresponde a pagos aplicados. Los egresos se muestran por separado.'],
            ['nombre'=>'Facturas y pagos','columnas'=>['Núm. CFDI','UUID','Cliente','Fecha de emisión','Tipo de CFDI','Método de pago','RFC receptor','Total factura','Total pagado','Saldo pendiente'],
                'filas'=>$filas,'sumar'=>[],'moneda'=>[7,8,9],'tipos_fila'=>$tipos,
                'nota'=>'Las operaciones y pagos aparecen debajo de su factura. El importe de cada pago se alinea bajo Total pagado.'],
        ];
    }
    /**
     * Detalle del método: Organiza las pestañas del archivo a partir de las secciones de
     * CfdisAdeudosExport.
     * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
     * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
     * contexto lo habilita.
     */
    public function sheets(): array { return array_map(function($s){return new CfdisAdeudosHoja($this->datos,$s);},self::secciones($this->datos)); }
}
