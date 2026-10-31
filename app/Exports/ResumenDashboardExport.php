<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Contiene el organizador de secciones del resumen diario. La ruta actual limita este reporte a PDF; no
 * habilitar Excel solo por existir esta clase.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
class ResumenDashboardExport implements WithMultipleSheets {
 private $datos; public function __construct(array $datos){$this->datos=$datos;}
 /**
  * Detalle del método: Define títulos, columnas y filas de cada sección del reporte desde sus datos ya
  * calculados.
  * Entradas: $d (datos agregados y filas preparados para el reporte).
  * Salida / efectos: Arreglo de secciones reutilizable por la exportación; no consulta la base de
  * datos.
  */
 public static function secciones(array $d){
 $b=$d['dashboard'];$secciones=[];
 $filas=[];foreach($b['servicios_funerarios']??[] as $s)$filas[]=[$s['nombre_afectado']??'No disponible','Servicio en proceso'];
 $secciones['Servicios en proceso']=$filas?:[['Servicios en proceso','Sin servicios para mostrar']];
 $mapas=[
 'Inventario'=>['total_inventariables'=>'Artículos inventariables','total_servicios'=>'Servicios','articulos_bajo_minimo'=>'Desabastecidos','articulos_sobre_maximo'=>'Sobreabastecidos','costo_total_inventario'=>'Valor inventariable','ultimoAjuste'=>'Último ajuste'],
 'Facturación'=>['timbresDisponibles'=>'Timbres disponibles','saldoPendiente'=>'CFDI con adeudo histórico','facturasPendientes'=>'Facturas pendientes','ventasFacturadas'=>'Facturado del mes','saldadoFacturado'=>'Cubierto de lo facturado','fechaCaducidadFiel'=>'Firma electrónica válida hasta'],
 'Seguimientos'=>['realizadosMes'=>'Realizados del mes','programadosProximosSieteDias'=>'Programados próximos 7 días','vencidos'=>'Vencidos históricos','clientesConPagosVencidos'=>'Clientes con pagos vencidos']];
 foreach($mapas as $titulo=>$mapa){$clave=['Inventario'=>'inventario','Facturación'=>'facturacion','Seguimientos'=>'seguimientos'][$titulo];$filas=[];foreach($mapa as $k=>$etiqueta){$v=$b[$clave][$k]??'No disponible';if(in_array($k,['costo_total_inventario','saldoPendiente','ventasFacturadas','saldadoFacturado'])&&is_numeric($v))$v='$ '.number_format($v,2).' MXN';$filas[]=[$etiqueta,$v];}$secciones[$titulo]=$filas;}
 $filas=[];foreach($b['ventasSaldos']??[] as $f)$filas[]=[$f['nombre'],'Ventas: $ '.number_format($f['ventas'],2).' MXN | Por cobrar: $ '.number_format($f['porCobrar'],2).' MXN | Vencido histórico: $ '.number_format($f['vencido'],2).' MXN'];$secciones['Ventas y saldos']=$filas;
 $p=$b['pendientes']??[];$filas=[];foreach(['venceHoy'=>'Vence hoy','proximosSieteDias'=>'Próximos 7 días, incluye hoy'] as $k=>$nombre)$filas[]=[$nombre,($p[$k]['cantidad']??0).' pagos · $ '.number_format($p[$k]['importe']??0,2).' MXN'];foreach(['solicitudesSinContrato'=>'Solicitudes recientes sin contrato','solicitudesAntiguasSinContrato'=>'Solicitudes antiguas sin contrato'] as $k=>$nombre)$filas[]=[$nombre,$p[$k]??'No disponible'];$secciones['Pendientes']=$filas;return $secciones;
 }
 /**
  * Detalle del método: Organiza las pestañas del archivo a partir de las secciones de
  * ResumenDashboardExport.
  * Entradas: no recibe parámetros; usa datos/configuración de la instancia o del módulo.
  * Salida / efectos: Arreglo de objetos de hoja para Excel; solo genera detalle completo cuando el
  * contexto lo habilita.
  */
 public function sheets():array{$hojas=[];foreach(self::secciones($this->datos) as $titulo=>$filas)$hojas[]=new VentasCementerioHoja($this->datos,['nombre'=>$titulo,'nota'=>'Resumen a la consulta; no incluye la gráfica de periodo seleccionable.','columnas'=>['Indicador','Información',''],'filas'=>$filas,'moneda'=>[],'sinTotal'=>true,'anchos'=>['A'=>45,'B'=>120]]);return $hojas;}
}
