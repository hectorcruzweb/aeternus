{{-- Marca de espacio libre por sección. No modifica datos ni altera el logo
     propio del título. La envoltura blanca delimita el contenido real. --}}
@if(config('reportes.marca_agua.'.$seccionMarcaAgua, true))
<style>
html { background-image:url(data:image/png;base64,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)!important; background-repeat:repeat!important; background-position:center top!important; }
/* Fondo hasta el final de una hoja corta; no agregar elementos al flujo. */
html, body { min-height:{{ $altoMarcaAgua ?? 1190 }}px; }
body { background-color:transparent!important; }
html:has(.solicitud-visor-pie),body:has(.solicitud-visor-pie),html:has([data-marca-visor]),body:has([data-marca-visor]) { min-height:0!important; }
.marca-agua-contenido { background-color:#fff; display:flow-root; margin-left:-16px; margin-right:-16px; border-left:16px solid #fff; border-right:16px solid #fff; }
.marca-agua-contenido::after { content:""; display:table; clear:both; }
.solicitud-visor-pie,.pie { background-color:#fff!important; }
@media screen {
/* La hoja del visor debe seguir blanca aunque el PDF use un fondo transparente
   para revelar la trama. Estos selectores solo existen en la vista previa. */
body:has(.solicitud-visor-pie),body:has(.solicitud-visor-contenido),body:has([data-marca-visor]) { background-color:#fff!important; }

html:has(.solicitud-visor-pie),html:has(.solicitud-visor-contenido) { background-image:none!important; }
@if(isset($modoPdf) && !$modoPdf)
html,body { background-image:none!important; min-height:0!important; background-color:#fff!important; }
@endif
}
@media print { html,body { -webkit-print-color-adjust:exact; print-color-adjust:exact; } }
</style>
@endif
