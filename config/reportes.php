<?php

/**
 * Configuración compartida para los reportes que se migren al visor HTML.
 * REPORTES_VISOR_ZOOM es un porcentaje: 100 = tamaño normal; 135 = 35% más grande.
 * Solo afecta pantalla, nunca impresión ni archivos PDF. Por ahora lo consumen
 * Solicitud y Convenio de CementerioController; reutilizar esta clave al migrar otros.
 * Valores permitidos: 50 a 200. Un valor inválido recupera el predeterminado 135.
 * Leer env() aquí y config() en controladores para admitir config:cache de Laravel.
 */
$zoom = env('REPORTES_VISOR_ZOOM', 135);
$zoom = is_numeric($zoom) ? (float) $zoom : 135;

return [
    'visor_zoom_porcentaje' => is_finite($zoom) && $zoom >= 50 && $zoom <= 200 ? $zoom : 135,
    // Solo PDF/impresión. Leer con config() en Blade para soportar config:cache.
    'marca_agua' => [
        'cementerio' => filter_var(env('REPORTES_MARCA_AGUA_CEMENTERIO', true), FILTER_VALIDATE_BOOLEAN),
        'servicios_funerarios' => filter_var(env('REPORTES_MARCA_AGUA_SERVICIOS_FUNERARIOS', true), FILTER_VALIDATE_BOOLEAN),
        'planes_venta' => filter_var(env('REPORTES_MARCA_AGUA_PLANES_VENTA', true), FILTER_VALIDATE_BOOLEAN),
        'cuotas' => filter_var(env('REPORTES_MARCA_AGUA_CUOTAS', true), FILTER_VALIDATE_BOOLEAN),
        'pagos' => filter_var(env('REPORTES_MARCA_AGUA_PAGOS', true), FILTER_VALIDATE_BOOLEAN),
        'inventario' => filter_var(env('REPORTES_MARCA_AGUA_INVENTARIO', true), FILTER_VALIDATE_BOOLEAN),
        'ventas_saldos' => filter_var(env('REPORTES_MARCA_AGUA_VENTAS_SALDOS', true), FILTER_VALIDATE_BOOLEAN),
        'facturacion' => filter_var(env('REPORTES_MARCA_AGUA_FACTURACION', true), FILTER_VALIDATE_BOOLEAN),
        'seguimientos' => filter_var(env('REPORTES_MARCA_AGUA_SEGUIMIENTOS', true), FILTER_VALIDATE_BOOLEAN),
        'resumen_dia' => filter_var(env('REPORTES_MARCA_AGUA_RESUMEN_DIA', true), FILTER_VALIDATE_BOOLEAN),
    ],
];
