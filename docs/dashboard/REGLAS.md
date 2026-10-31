# Reglas y fuentes de datos

## Ventas y saldos de operaciones

Motor: `OperacionesController@getVentasSaldosOperacionesDashboard`.

| Indicador | Selección |
| --- | --- |
| Ventas del mes | `operaciones.total`, por `fecha_operacion` dentro del mes elegido y operación registrada hasta el corte. |
| Por cobrar del mes | Pendiente actual de `pagos_programados` cuya `fecha_programada` pertenece al mes; incluye programados de ventas anteriores. |
| Vencido Acumulado Histórico del carrusel | Programados pendientes con fecha anterior al día de consulta, desde las operaciones más antiguas. Hoy no está vencido. |
| Vencido de la gráfica para un mes pasado | Lo que sigue pendiente actualmente de pagos que vencieron hasta el último día del mes elegido. No es una reconstrucción del saldo que existía entonces. |

Estados considerados: operaciones 1/2, programados 1 y pagos 1. Se usa `pagos_pagos_programados.monto` de pagos vigentes con `movimientos_pagos_id` 1/3/4/5; el movimiento de intereses 2 queda fuera. `pagos.total_pago` es el recibo completo y no debe repetirse por cada programado.

```text
capital_cubierto = suma de aplicaciones vigentes admisibles a cada programado
pendiente = máximo(0, redondear(monto_programado - capital_cubierto, 2))
por_cobrar = suma de pendientes con fecha_programada del mes
vencido = suma de pendientes con fecha_programada anterior al límite exclusivo
```

El límite es el inicio de hoy para el dashboard actual. Con `vencido_hasta_periodo=1`, un mes pasado usa el inicio del mes siguiente. Las aplicaciones recibidas después del mes elegido también reducen el pendiente actual. Los sobrepagos de un programado no compensan otro.

Ejemplo: una venta de enero con una mensualidad pendiente de marzo entra en el por cobrar de marzo, aunque no sea una venta de marzo. Si esa mensualidad ya venció, también puede formar parte del vencido; estos indicadores no se suman entre sí.

Los reportes individuales conservan tres alcances separados: ventas del mes, programados por cobrar del mes e histórico vencido. No limitar los tres a las ventas de ese mes. El usuario ve referencias de negocio y de pago, no `operaciones.id` ni el ID interno del programado.

## Tipos de operación

| Tipo | Responsable del detalle | Referencia |
| --- | --- | --- |
| 1 Ventas en cementerio | CementerioController | Venta/propiedad y ubicación traducida. |
| 2 Cuotas de mantenimiento | CementerioController | Cuota y descripción de `cuotas_cementerio`; resumen y detalle por cuota. |
| 3 Servicios funerarios | FunerariaController | Número de servicio y fallecido. |
| 4 Planes funerarios | FunerariaController | Venta de plan y sus datos propios. |
| 5 Ventas generales | FunerariaController | Venta general. |

El catálogo del carrusel se define en `getTiposOperacionesDashboard`. La fila `id=todas` ya viene calculada: no volver a sumarla con las individuales.

La gráfica compara 1/3/4/5. Tipo 2 se presenta aparte por su escala; el reporte general conserva todos los tipos en sus totales. Cambiar casillas visibles no filtra automáticamente el reporte elegido. El mes/año de la gráfica no cambia el resto del dashboard.

## Inventario

`getInventarioDashboard` devuelve agregados; `consultaReporteInventario` centraliza filtros `all`, `low`, `over`, `articles`, `services`. Separar conceptos totales de artículos inventariables y servicios. Los servicios no intervienen en el valor del inventario. La clave `costo_total_inventario` usa actualmente existencia por **precio_venta**: no interpretarla como costo de adquisición sin cambiar la regla de negocio.

Último ajuste: `MAX(fecha_movimiento)` de `movimientos_inventario`, con `tipo_movimientos_id=1`, `status=1` y fecha hasta el corte. Se devuelve formateado o `No disponible`; no se limita al mes actual.

## Servicios y solicitudes

Servicios en proceso: servicio vigente con algún evento futuro. Si ninguna de las cuatro fechas de evento existe, se usa una ventana móvil de 72 horas desde la solicitud. Se devuelven los 10 más recientes. Esto es distinto de las solicitudes sin contrato: recientes significa hoy y los 6 días anteriores; el resto queda como historial a revisar. La antigüedad no cancela automáticamente registros.

En el resumen diario, si no hay contrato vigente se presentan los datos temporales del contratante (`nombre_contratante_temp`, `telefono_contratante_temp`, `parentesco_contratante_temp`); con operación vigente se busca el cliente relacionado.

## Pagos por vencer y clientes con vencidos

Hoy y próximos 7 días comparten el cálculo de capital. Próximos 7 incluye hoy y los 6 siguientes, ordenados de menor a mayor fecha. El reporte de clientes con vencidos usa todos los programados pendientes anteriores a hoy y agrupa los pagos por cliente. La cantidad del card es de clientes únicos, no de programados.

## Seguimientos

- Realizados del mes: por fecha efectiva de gestión, no fecha programada; también puede contar un intento sin contacto como gestión realizada.
- Agenda: solo programados pendientes de hoy y los 6 siguientes, excluyendo atendidos/cancelados.
- Vencidos: programados pendientes anteriores a hoy, sin fechas cero/nulas; vencen desde el día siguiente.
- Criterio vigente: excluir vínculos a operaciones canceladas de los tres reportes. Las cotizaciones tienen su identidad propia; no comparar su ID como si fuera una operación.
- La consulta histórica de un seguimiento en el formulario puede mostrar su operación cancelada en lectura. Esa excepción no permite nuevas capturas sobre la operación cancelada.

## Facturación: un saldo distinto

No usar el motor de programados para saldos CFDI. `consultaCfdisAdeudosBase` parte de ingresos vigentes: PPD para adeudo histórico y PUE/PPD del mes para facturado mensual.

Las aplicaciones fiscales proceden de `cfdis_tipo_relacion`, con el comprobante aplicador vigente (`status=1`), relaciones internas 2 (pagos) y 3 (egresos). PPD: total menos aplicaciones de pago y egreso; PUE se considera cubierto. El detalle muestra el importe aplicado a esa factura (`monto_relacion`), no el total del comprobante de pago.

Cubierto del mes incluye parciales de las facturas emitidas en el mes; no significa solo facturas liquidadas. El card es MXN y los reportes conservan las monedas por separado. El RFC `XAXX010101000` se cuenta como público en general. El usuario que timbró se relaciona mediante `cfdis.timbro_id → usuarios.id`. Vigencia de firma: `facturacion.fecha_vencimiento`.

## Timbres: libro de movimientos

Tabla `movimientos_timbres`:

| Campo | Uso |
| --- | --- |
| tipo | recarga/saldo_inicial/consumo/ajuste. |
| cantidad | Positivo para lote; -1 para consumo; con signo para ajuste. |
| fecha_movimiento | Fecha efectiva; en consumo, fecha de timbrado. |
| fecha_compra / fecha_activacion | Compra conocida y disponibilidad del lote. Compra histórica desconocida queda NULL. |
| cfdis_id | FK del CFDI que consumió el timbre; única para evitar doble consumo. |
| recarga_id | FK a la recarga en la misma tabla; no es la factura. |
| referencia | Identificador del lote o UUID; no reemplaza las FK. |
| asignacion_estimada | Reconstrucción histórica por fechas/disponibilidad, no prueba del lote usado por el PAC. |
| nota | Motivo o aclaración de un movimiento. |

Disponible por lote = cantidad inicial más movimientos hijos al corte; disponible global = suma de lotes activados. Cancelar posteriormente un CFDI no devuelve el timbre consumido.

`HistorialTimbresSeeder` configura tres lotes: 2000 (03/Mar/2022 12:55:28), 2000 (04/Jun/2024 11:29:47), 500 (19/May/2026 23:32:48). Asigna FIFO al lote activado más antiguo con saldo. Detecta inconsistencias y revierte su transacción. No es una sincronización automática con el proveedor ni registra consumos futuros por sí solo.

## Mantenimiento de saldo/status guardado

Comando `operaciones:actualizar-saldos`: simula por defecto; `--tipo` limita tipos y `--aplicar` guarda con commit/rollback automático. El arreglo `OPERACIONES_STATUS_0` contiene **operaciones.id** (3412 y 897), no los IDs de los servicios. Se dejan status 0 conservando saldo; no elimina registros ni cancela sus pagos/facturas.

El método `mantenerSaldosStatusOperaciones` no reactiva canceladas y reporta operaciones omitidas por programación ausente/incoherente. Una salida exitosa no certifica que todas las incidencias se hayan resuelto. Nunca ejecutar el comando solo por abrir el dashboard.
