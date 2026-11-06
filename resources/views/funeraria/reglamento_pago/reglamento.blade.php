<!doctype html><html lang="es"><head><meta charset="UTF-8"><title>Reglamento de Pago</title></head><body class="reglamento-plan-documento">
@include('layouts.estilos')
<style>
.reglamento-plan-documento .stage{min-height:1100px;padding:12px 0;box-sizing:border-box}
.reglamento-plan-documento .hoja{background:#fff;color:#30383b;font:14px/1.55 Arial,sans-serif;padding:30px;box-sizing:border-box;min-height:1040px;display:flex;flex-direction:column;border:1px solid #e1e1e1}
.reglamento-plan-documento header{display:flex;justify-content:space-between;gap:18px;align-items:center;padding-bottom:20px;border-bottom:2px solid #ad8b28}
.reglamento-plan-documento .marca{font-size:25px;letter-spacing:4px;color:#242424}.reglamento-plan-documento .marca small{display:block;font-size:9px;letter-spacing:3px;text-align:center;color:#777}
.reglamento-plan-documento .empresa{text-align:right;font-size:11px;line-height:1.6}.reglamento-plan-documento .empresa span{display:block}
.reglamento-plan-documento .titulo{display:flex;justify-content:space-between;align-items:center;gap:15px;padding:22px 0}.reglamento-plan-documento .titulo span{font-size:10px;letter-spacing:1px;color:#8b712c}.reglamento-plan-documento h2{margin:4px 0;font-size:27px;color:#343b3e}.reglamento-plan-documento .folio{text-align:right;min-width:110px;font-size:12px}.reglamento-plan-documento .folio strong{color:#8b712c;font-size:22px;margin-left:5px}.reglamento-plan-documento .folio small{display:block;color:#737373}
.reglamento-plan-documento h3{font-size:16px;display:flex;align-items:center;gap:8px;margin:0 0 16px;color:#6e5a23}.reglamento-plan-documento h3 svg{width:18px;height:18px;flex-shrink:0}.reglamento-plan-documento h4{margin:0 0 4px;font-size:14px}.reglamento-plan-documento p{margin:0;font-size:13px;line-height:1.65}
.reglamento-plan-documento .paso{display:flex;gap:13px;margin-bottom:20px}.reglamento-plan-documento .paso b{width:27px;height:27px;flex-shrink:0;border-radius:50%;background:#f1eddf;color:#897024;text-align:center;line-height:27px}
.reglamento-plan-documento .documentos{margin-top:24px;padding:20px;background:#f7f6f2;border-top:2px solid #b69b51}.reglamento-plan-documento .documentos-grupo{margin-top:14px}.reglamento-plan-documento .documentos-grupo h4{color:#5c6265;font-size:12px}.reglamento-plan-documento .documento{display:flex;gap:9px;margin:9px 0;font-size:13px}.reglamento-plan-documento .casilla{width:12px;height:12px;border:1px solid #aaa;background:#fff;flex-shrink:0;margin-top:4px}.reglamento-plan-documento .documento small{display:block;color:#757575;font-size:11px}.reglamento-plan-documento footer{margin-top:auto;padding-top:24px;display:flex;justify-content:space-between;font-size:10px;color:#7b7b7b}
.reglamento-plan-documento .pasos .documentos{display:grid;grid-template-columns:1fr 1fr;gap:0 22px}.reglamento-plan-documento .pasos .documentos h3,.reglamento-plan-documento .pasos .responsable{grid-column:1/-1}
.reglamento-plan-documento .lista .contenido{display:grid;grid-template-columns:1fr 1.1fr;gap:24px}.reglamento-plan-documento .lista .documentos{margin-top:0;padding:17px}.reglamento-plan-documento .lista .paso{display:block;padding-bottom:20px;border-bottom:1px solid #e6e6e6;margin-bottom:20px}.reglamento-plan-documento .lista .paso b{display:block;margin-bottom:8px}.reglamento-plan-documento .lista p{font-size:13px}

/* Carta vertical: tablas de presentación compatibles con Qt antiguo.
   Contenido y condiciones originales; casillas para revisión manual. */
html body.reglamento-plan-documento {font-family:Arial,sans-serif!important;font-size:14px;margin:0;color:#30383b!important}
.reglamento-plan-documento .hoja{display:block;border:0;padding:0;min-height:0;background:white}
.reglamento-plan-documento header{display:table;width:100%;padding-bottom:16px}
.reglamento-plan-documento header>div{display:table-cell;vertical-align:middle}
.reglamento-plan-documento .marca{width:35%}
.reglamento-plan-documento .marca img{width:210px;max-width:100%}
.reglamento-plan-documento .empresa{font-size:13px}
.reglamento-plan-documento .titulo{display:table;width:100%;padding:22px 0}
.reglamento-plan-documento .titulo>div{display:table-cell;vertical-align:middle}
.reglamento-plan-documento .folio{width:25%;font-size:14px}
.reglamento-plan-documento h2{font-size:30px}
.reglamento-plan-documento h3{display:block;font-size:19px}
.reglamento-plan-documento h4{font-size:17px}
.reglamento-plan-documento p{font-size:16px;line-height:1.65}
.reglamento-plan-documento .paso{display:table;width:100%;margin-bottom:22px;page-break-inside:avoid}
.reglamento-plan-documento .paso>b{display:table-cell;width:34px;height:auto;background:transparent;vertical-align:top;color:#897024;font-size:22px}
.reglamento-plan-documento .paso>div{display:table-cell;padding-left:10px}
.reglamento-plan-documento .documentos{margin-top:25px;page-break-inside:avoid}
.reglamento-plan-documento .documentos-columnas{width:100%;table-layout:fixed;border-collapse:collapse}
.reglamento-plan-documento .documentos-columnas td{width:50%;vertical-align:top;padding-right:18px}
.reglamento-plan-documento .documento{display:block;font-size:16px}
.reglamento-plan-documento .documento>div{display:inline}
.reglamento-plan-documento .documento small{display:block;margin-left:20px;font-size:13px}
.reglamento-plan-documento .casilla{display:inline-block;margin-right:7px}
.reglamento-plan-documento .responsable{border-top:1px solid #dedbd2;padding-top:12px}
.reglamento-plan-documento .icono-guia{display:inline-block;width:12px;height:14px;border:1px solid #9b813d;margin-right:8px;vertical-align:middle;position:relative}
.reglamento-plan-documento .icono-guia:after{content:'';position:absolute;left:2px;right:2px;top:4px;height:3px;border-top:1px solid #9b813d;border-bottom:1px solid #9b813d}
/* Compensación visual del navegador, independiente de las medidas de Qt. */
@supports(display:grid){
.reglamento-plan-documento .empresa{font-size:10.4px}
.reglamento-plan-documento .marca img{width:168px}
.reglamento-plan-documento h2{font-size:24px}
.reglamento-plan-documento h3{font-size:15.2px}
.reglamento-plan-documento h4{font-size:13.6px}
.reglamento-plan-documento p,.reglamento-plan-documento .documento{font-size:12.8px}
.reglamento-plan-documento .documento small{font-size:10.4px}
.reglamento-plan-documento .paso{margin-bottom:17.6px}
}
</style><style>
/* Reglamento tipo 4: presentación de la Guía del cliente. Los cinco apartados
   conservan el contenido original; cada párrafo y el cierre con firmas evitan cortes. */
.reglamento-plan-documento .titulo{padding:12px 0 8px}
.reglamento-plan-documento h2{font-size:27px}
.reglamento-plan-documento .apartado{margin:12px 0 0}
.reglamento-plan-documento .apartado h3{font-size:17px;margin:0 0 8px;page-break-after:avoid;break-after:avoid}
.reglamento-plan-documento .numero{display:inline-block;width:25px;height:25px;line-height:25px;border-radius:50%;background:#f1eddf;color:#897024;text-align:center;margin-right:8px}
.reglamento-plan-documento .apartado-texto{font-size:14px;line-height:1.5}
.reglamento-plan-documento .apartado-texto p{font-size:14px!important;line-height:1.5!important;text-align:justify;margin:6px 0;page-break-inside:avoid;orphans:3;widows:3}
.reglamento-plan-documento .apartado-texto p span{font-size:inherit!important}
.reglamento-plan-documento .titular{background:#f7f6f2;border-left:2px solid #ad8b28;padding:10px 12px;font-size:13px;margin-bottom:12px}
.reglamento-plan-documento .reglamento-cierre{page-break-inside:avoid;break-inside:avoid}
.reglamento-plan-documento .firmas-reglamento{width:100%;table-layout:fixed;border-collapse:collapse;margin-top:16px;page-break-inside:avoid}
.reglamento-plan-documento .firmas-reglamento td{width:50%;padding:0 12px;text-align:center;vertical-align:bottom}
.reglamento-plan-documento .espacio-firma{height:85px}
.reglamento-plan-documento .espacio-firma img{max-width:210px;max-height:78px;width:auto;height:auto}
.reglamento-plan-documento .nombre-firma{border-top:1px solid #555;padding-top:6px;font-size:12px;letter-spacing:normal!important;line-height:1.4}
.reglamento-plan-documento .nombre-firma small{display:block;margin-top:4px}
.reglamento-plan-documento .hoja{position:relative}
.reglamento-plan-documento .guia-marca-titulo{position:absolute;top:30mm;right:0;width:58%;z-index:-1;pointer-events:none}
.reglamento-plan-documento .guia-marca-titulo svg{display:block;width:100%;height:180mm}
.reglamento-plan-documento .guia-contenido-frente{position:relative;z-index:1}
/* El fondo de espacio sin contenido queda fuera de párrafos y áreas de firma. */
/* El encabezado y el titular no deben recortar el logo transparente de fondo. */
.reglamento-plan-documento header,.reglamento-plan-documento .titulo,.reglamento-plan-documento .titular{background-color:transparent!important}
.reglamento-plan-documento .firmas-reglamento{background-color:#fff}
.reglamento-plan-documento .apartado-texto p,.reglamento-plan-documento .apartado h3{background-color:transparent!important;box-shadow:none}
@supports(display:grid){
.reglamento-plan-documento h2{font-size:21.6px}
.reglamento-plan-documento .apartado h3{font-size:13.6px}
.reglamento-plan-documento .apartado-texto,.reglamento-plan-documento .apartado-texto p{font-size:11.2px!important}.reglamento-plan-documento .titular{font-size:10.4px!important}
.reglamento-plan-documento .espacio-firma{height:68px}
.reglamento-plan-documento .espacio-firma img{max-width:168px;max-height:62.4px}
}
</style><div class="marca-agua-contenido"><article class="hoja" data-ajuste-una-pagina><div class="guia-contenido-frente" data-contenido-ajustable>{{-- Marca del logo transparente, independiente de la leyenda de espacio sin contenido. --}}
<div class="guia-marca-titulo" aria-hidden="true">{!! \App\Exports\MarcaTituloReportes::svg() !!}</div><header><div class="marca"><img src="{{ public_path(env('LOGOJPG')) }}" alt="Aeternus Funerales"></div><div class="empresa"><strong>{{ $empresa->razon_social }}</strong><span>{{ mb_convert_case(mb_strtolower($empresa->calle, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} núm. ext. {{ $empresa->num_ext }}</span><span>@if(trim($empresa->colonia))Col. {{ mb_convert_case(mb_strtolower($empresa->colonia, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') }} · @endif C. P. {{ $empresa->cp }} · {{ $empresa->ciudad }}, {{ $empresa->estado }}</span><span>Tel. {{ $empresa->telefono }} · Fax {{ $empresa->fax }}</span></div></header><div class="titulo"><div><span>INFORMACIÓN PARA EL TITULAR</span><h2>Reglamento de Pago</h2><p>Ventas de planes funerarios a futuro</p></div><div class="folio"><small>Convenio</small><strong>{{ $datos['numero_convenio'] }}</strong></div></div><div class="titular"><strong>Titular:</strong> {{ $datos['nombre'] }}</div><section class="apartado"><h3><span class="numero">1</span> Pago de abonos/liquidación</h3><div class="apartado-texto">
            <p>
                <span>a) </span>
                <span>
                    Las cuotas por liquidación, enganche o abono de la venta se pagarán en la modalidad que haya elegido
                    el titular al
                    momento de la compra del plan funerario a futuro en <span
                       >{{ $empresa->nombre_comercial }}</span> y deberán ser
                    cubiertas a
                    más tardar en la fecha programada
                    de cada pago para no
                    generar recargos por intereses moratorios, según las políticas de la empresa.
                </span>
            </p>
            <p>
                <span>b) </span>
                <span>
                    Según la claúsula <span>Vigésima sexta</span>,
                    “El Cliente” tendrá el derecho de rescindir este convenio dentro de los <span
                       >5
                        días</span> hábiles siguientes a su
                    firma, sin menoscabo de las aportaciones realizadas, comprometiéndose “La Empresa” a devolver
                    íntegramente
                    de las mismas en un plazo no mayor a los <span>5 dias</span> hábiles
                    siguientes a la fecha en que le sea notificada
                    por escrito con acuse de recibo dicha cancelación.
                </span>
            </p>
        </div></section><section class="apartado"><h3><span class="numero">2</span> Descuentos</h3><div class="apartado-texto">
            <p>
                <span>a) </span>
                <span>
                    Por el pago anticipado de las cuotas (antes de la fecha programada), la empresa podrá ofrecer los
                    descuentos que
                    considere según las términos en que se realizó la venta.
                </span>
            </p>
        </div></section><section class="apartado"><h3><span class="numero">3</span> Formas de pago</h3><div class="apartado-texto">
            <p>
                <span>a) </span>
                <span>
                    El titular podrá solicitar las fichas de pago con el monto a pagar al personal de
                    <span>{{ $empresa->nombre_comercial }}</span>, con dicho documento
                    (también conocido como talonario de pago) tendrá cada referencia para poder efectuar
                    su pago correspondiente en los bancos <span>Santander</span>.
                </span>
            </p>

            <p>
                <span>b) </span>
                <span>
                    Los pagos también se podrán efectuar en las instalaciones de <span
                       >{{ $empresa->nombre_comercial }}</span>, por medio de tarjeta de crédito,
                    débito o efectivo en los
                    horarios laborales de la empresa.
                </span>
            </p>

            <p>
                <span>c) </span>
                <span>
                    Cada pago a realizar tendrá un día límite de pago de acuerdo al número de pago y la fecha que
                    corresponde,
                    posterior a dicha fecha se deberá solicitar un nuevo talonario de pagos, mismo que ya incluirá los
                    recargos por
                    intereses moratorios según los términos de la venta.
                </span>
            </p>

            <p>
                <span>d) </span>
                <span>
                    Favor de conservar todos los comprobantes de pago para cualquier aclaración.
                </span>
            </p>
        </div></section><section class="apartado"><h3><span class="numero">4</span> Recargos por intereses moratorios</h3><div class="apartado-texto">
            <p>

                <span>a) </span>
                <span>
                    En caso de retrasarse en el pago mensual de las aportaciones, “El Cliente” deberá cubrir una pena
                    convencional sobre el total del monto de adeudado,
                    importe que se considerará como aportación
                    adicional complementaria al cliente. El contratante se obliga a pagar a la agencia funeraria interés
                    moratorio del <span>{{$datos['ajustes_politicas']['tasa_fija_anual']}}</span>%
                    ({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['tasa_fija_anual'],'',false) }} por
                    ciento)
                    fija
                    anual, la que se calculará y liquidará sobre
                    cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
                    calcularán multiplicando el monto de lo que adeude el contratante por la tasa de interés
                    anual, dividida entre 365, este resultado se multiplica por el número de días transcurridos
                    entre la fecha de pago que debió ser hecho y la fecha que el contratante liquide el adeudo.
                </span>
            </p>
        </div></section><section class="apartado"><h3><span class="numero">5</span> Consecuencias</h3><div class="apartado-texto">
            “La Empresa” podrá cancelar el convenio, si este incurriera en cualquiera de los
            supuestos siguientes:

            <p>
                <span>a) </span>
                <span>
                    El incumplimiento del pago de <span
                       >{{$datos['ajustes_politicas']['maximo_pagos_vencidos']}}</span>
                    de las aportaciones en
                    forma consecutiva.
                </span>
            </p>
            <p>
                <span>b) </span>
                <span>
                    En caso de que el retraso supere los <span
                       >{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> días, la agencia
                    funeraria podrá elegir entre exigir
                    el pago de todas las mensualidades aun no pagadas por el contratante y los intereses
                    moratorios acumulados o bien rescindir el contrato y aplicar como pena convencional por
                    incumplimiento el <span
                       >{{$datos['ajustes_politicas']['porcentaje_pena_convencional_minima']}}%</span> del
                    monto pagado por el contratante, debiendo a la Agencia
                    Funeraria regresar las cantidades en exceso y que sobren de dicha pena al contratante. En
                    caso de que el retraso en el pago sea superior a los <span
                       >{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> días, la Agencia
                    Funeraria podrá
                    igualmente rescindir el Contrato y aplicar como pena convencional la totalidad de los
                    pagos efectuados por el contratante.
                </span>
            </p>

            <div class="reglamento-cierre"><p>
                <span>c) </span>
                <span>
                    En los términos de los dispuestos por el artículo 71 de la Ley Federal de Protección al
                    consumidor, cuando el contratante haya pagado más de la tercera parte del precio o
                    número total de los pagos convenidos ante la notificación de rescisión que le realice la
                    Agencia Funeraria, el contratante podrá optar porque se aplique el mecanismo indicado
                    en el párrafo anterior o bien pagar el saldo del contrato más los intereses moratorios
                    generados por su incumplimiento. En el primer caso (rescisión con penalidad) solo si el
                    retraso fuera menor a <span
                       >{{$datos['ajustes_politicas']['maximo_dias_retraso']}}</span> días, la
                    agencia
                    funeraria devolverá al contratante la cantidad
                    que corresponda una vez aplicada la penalidad y los intereses moratorios. En el segundo
                    caso (pago total de saldo insoluto), la Agencia Funeraria entregará al contratante el recibo
                    de finiquito correspondiente solo si este paga la cantidad total adeudada (saldo insoluto
                    contratado más los intereses moratorios).
                </span>
            </p>
        <table class="firmas-reglamento"><tr><td><div class="espacio-firma">@if(!empty($firmas['gerente']))<img src="{{ $firmas['gerente'] }}" alt="Firma de la empresa">@endif</div><div class="nombre-firma">{{ $empresa->razon_social }}<small>“La empresa”</small></div></td><td><div class="espacio-firma">@if(!empty($firmas['cliente']))<img src="{{ $firmas['cliente'] }}" alt="Firma del cliente">@endif</div><div class="nombre-firma">{{ $datos['nombre'] }}<small>“El cliente”</small></div></td></tr></table></div></div></section></div></article></div>
@include('reportes.compartidos.marca_agua', ['seccionMarcaAgua' => 'planes_venta', 'altoMarcaAgua' => 1100])
<style>
/* Área de una carta, descontando márgenes y pie. El visor utiliza los mismos
   atributos de ajuste que los documentos de una página. Parte de una tipografía legible y solo reduce si hace falta;
   el contenido, las políticas y las dos firmas permanecen completos. */
.reglamento-plan-documento .hoja[data-ajuste-una-pagina]{position:relative;width:935px;height:315mm;overflow:hidden}
.reglamento-plan-documento [data-contenido-ajustable]{position:absolute;left:0;top:0;width:100%;transform-origin:top left}
.reglamento-plan-documento .hoja[data-ajuste-una-pagina] *{page-break-before:auto!important;page-break-after:auto!important;page-break-inside:auto!important;break-inside:auto!important}
@supports(display:grid){.reglamento-plan-documento .hoja[data-ajuste-una-pagina]{width:187mm;height:238mm}}
</style><script>(function(){
 function ajustar(){
  var hoja=document.querySelector('[data-ajuste-una-pagina]');
  if(!hoja)return;
  var contenido=hoja.querySelector('[data-contenido-ajustable]');
  contenido.style.webkitTransform='none';contenido.style.transform='none';contenido.style.zoom=1;
  // Buscar la mayor escala que cabe. Ampliar el ancho interno compensa la
  // transformación, mantiene el ancho visible y redistribuye los párrafos.
  var bajo=0.000001,alto=1,escala=bajo;
  for(var i=0;i<22;i++){
   var prueba=(bajo+alto)/2;
   contenido.style.width=(hoja.clientWidth/prueba)+'px';
   var cabe=Math.max(contenido.scrollHeight,contenido.offsetHeight)*prueba<=hoja.clientHeight-4;
   if(cabe){escala=prueba;bajo=prueba;}else{alto=prueba;}
  }
  // Reserva adicional para redondeos de filas y bordes del motor Qt.
  escala=escala*0.995;
  contenido.style.width=(hoja.clientWidth/escala)+'px';
  // Zoom participa en la paginación de Qt; transform dejaba saltos internos
  // calculados a tamaño natural y podía empujar las notas fuera del área.
  contenido.style.zoom=escala;
 }
 if(document.readyState==='complete')ajustar();
 else window.addEventListener('load',ajustar);
 window.addEventListener('beforeprint',ajustar);
})();
</script></body></html>