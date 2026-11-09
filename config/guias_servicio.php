<?php
/** Catálogo único de guías de consulta del menú.
 * Cada escenario aporta orientación de coordinación, no requisitos normativos.
 * La documentación común se conserva en la guía aprobada del proyecto.
 * Agregar aquí una entrada actualiza las hojas del modal sin duplicar el catálogo en Vue.
 */
return [
    'guia-general' => ['nombre' => 'Guía general del servicio funerario', 'icono' => 'FileTextIcon', 'grupo' => 'Orientación general', 'pasos' => [
        ['titulo' => 'Recepción de la solicitud', 'texto' => 'Informe al personal el nombre del fallecido, dónde se encuentra y los datos de contacto de la persona responsable.'],
        ['titulo' => 'Definición del servicio', 'texto' => 'Revise con el personal los servicios, artículos, lugares y horarios que desea contratar. Las actividades se coordinan según el servicio acordado.'],
    ]],
    'guia-cremacion' => ['nombre' => 'Servicio de cremación', 'icono' => 'urna', 'grupo' => 'Servicio en Específico', 'pasos' => [
        ['titulo' => 'Coordinación de la cremación', 'texto' => 'Confirme con el personal la fecha, el horario y la persona responsable de autorizar el servicio.'],
        ['titulo' => 'Entrega de cenizas', 'texto' => 'Acuerde la urna, la fecha de entrega y la persona que recibirá las cenizas. Revise los documentos del servicio con el personal.'],
    ]],
    'guia-cremacion-velacion' => ['nombre' => 'Cremación con velación', 'icono' => 'urna', 'grupo' => 'Servicio en Específico', 'pasos' => [
        ['titulo' => 'Velación previa', 'texto' => 'Confirme el lugar, la dirección y los horarios de velación, así como los artículos y servicios contratados.'],
        ['titulo' => 'Cremación y entrega', 'texto' => 'Coordine el término de la velación, la cremación y la entrega de cenizas con el personal responsable.'],
    ]],
    'guia-cremacion-directa' => ['nombre' => 'Cremación sin velación', 'icono' => 'urna', 'grupo' => 'Servicio en Específico', 'pasos' => [
        ['titulo' => 'Servicio sin velación', 'texto' => 'Indique al personal que no se realizará velación para coordinar directamente las actividades incluidas en su servicio.'],
        ['titulo' => 'Cremación y cenizas', 'texto' => 'Confirme la programación de la cremación, la urna y los datos de la persona responsable de recibir las cenizas.'],
    ]],
    'guia-inhumacion' => ['nombre' => 'Servicio de inhumación', 'icono' => 'cruz', 'grupo' => 'Servicio en Específico', 'pasos' => [
        ['titulo' => 'Confirmación del destino', 'texto' => 'Indique el cementerio y la ubicación de la propiedad que se utilizará. Confirme quién es su titular o responsable.'],
        ['titulo' => 'Programación de la inhumación', 'texto' => 'Revise con el personal la fecha, la hora y el traslado al cementerio, conforme al servicio contratado.'],
    ]],
    'guia-inhumacion-velacion' => ['nombre' => 'Inhumación con velación', 'icono' => 'cruz', 'grupo' => 'Servicio en Específico', 'pasos' => [
        ['titulo' => 'Organización de la velación', 'texto' => 'Confirme lugar, horarios, artículos y servicios de la velación con el personal de Aeternus Funerales.'],
        ['titulo' => 'Salida al cementerio', 'texto' => 'Coordine la salida de la velación y la llegada al cementerio. Tenga identificada la ubicación de la propiedad.'],
    ]],
    'guia-inhumacion-directa' => ['nombre' => 'Inhumación sin velación', 'icono' => 'cruz', 'grupo' => 'Servicio en Específico', 'pasos' => [
        ['titulo' => 'Servicio sin velación', 'texto' => 'Comunique al personal que el servicio no incluye velación y confirme los servicios que sí se realizarán.'],
        ['titulo' => 'Destino y horario', 'texto' => 'Confirme el cementerio, la ubicación de la propiedad y el horario de inhumación para coordinar el traslado.'],
    ]],
    'guia-velacion-empresa' => ['nombre' => 'Velación en Aeternus', 'icono' => 'HomeIcon', 'grupo' => 'Velación y ceremonia', 'pasos' => [
        ['titulo' => 'Sala y horario', 'texto' => 'Confirme con el personal la sala asignada, la fecha y el horario de velación.'],
        ['titulo' => 'Servicios incluidos', 'texto' => 'Revise los artículos y servicios acordados para la sala e informe si se realizará una ceremonia religiosa.'],
    ]],
    'guia-velacion-otro' => ['nombre' => 'Velación en domicilio u otro lugar', 'icono' => 'MapPinIcon', 'grupo' => 'Velación y ceremonia', 'pasos' => [
        ['titulo' => 'Ubicación de la velación', 'texto' => 'Proporcione la dirección completa y un teléfono de contacto. Confirme el lugar con el personal antes de coordinar el servicio.'],
        ['titulo' => 'Entrega y término', 'texto' => 'Acuerde los horarios de traslado, instalación y término de la velación según los servicios contratados.'],
    ]],
    'guia-ceremonia' => ['nombre' => 'Ceremonia religiosa', 'icono' => 'SunIcon', 'grupo' => 'Velación y ceremonia', 'pasos' => [
        ['titulo' => 'Lugar de la ceremonia', 'texto' => 'Confirme el templo o lugar de celebración y su dirección con las personas responsables.'],
        ['titulo' => 'Coordinación del horario', 'texto' => 'Comunique la fecha y la hora al personal para coordinar su relación con la velación, cremación o inhumación.'],
    ]],
    'guia-traslado' => ['nombre' => 'Servicio de traslado', 'icono' => 'TruckIcon', 'grupo' => 'Traslados', 'pasos' => [
        ['titulo' => 'Origen y destino', 'texto' => 'Proporcione el lugar de origen y el destino completo, con nombres y teléfonos de contacto de quienes entregan y reciben.'],
        ['titulo' => 'Coordinación del servicio', 'texto' => 'Confirme con el personal los horarios y los documentos correspondientes al traslado antes de su realización.'],
    ]],
    'guia-traslado-nacional' => ['nombre' => 'Traslado nacional', 'icono' => 'TruckIcon', 'grupo' => 'Traslados', 'pasos' => [
        ['titulo' => 'Datos del destino', 'texto' => 'Indique ciudad, estado, dirección y contacto de recepción. Informe si el destino es una funeraria o un cementerio.'],
        ['titulo' => 'Confirmación del traslado', 'texto' => 'Revise con el personal la programación y la documentación que corresponda al destino. Confirme quién recibirá al fallecido.'],
    ]],
    'guia-traslado-internacional' => ['nombre' => 'Traslado internacional', 'icono' => 'GlobeIcon', 'grupo' => 'Traslados', 'pasos' => [
        ['titulo' => 'Información de recepción', 'texto' => 'Indique país, ciudad, dirección y los datos de la funeraria o persona que recibirá al fallecido.'],
        ['titulo' => 'Revisión con el personal', 'texto' => 'Solicite la revisión de los documentos y la coordinación necesarios para el destino elegido. Confirme la programación con el personal antes de organizar la recepción.'],
    ]],
    'guia-entrega-cenizas' => ['nombre' => 'Entrega de cenizas', 'icono' => 'urna', 'grupo' => 'Atención complementaria', 'pasos' => [
        ['titulo' => 'Programación de entrega', 'texto' => 'Confirme la fecha y la hora de entrega y la urna correspondiente al servicio.'],
        ['titulo' => 'Persona que recibe', 'texto' => 'Acuerde con el personal quién recibirá las cenizas y revise la información del documento de entrega.'],
    ]],
    'guia-embalsamiento' => ['nombre' => 'Servicio con embalsamamiento', 'icono' => 'HeartIcon', 'grupo' => 'Atención complementaria', 'pasos' => [
        ['titulo' => 'Confirmación del servicio', 'texto' => 'Revise con el personal si el embalsamamiento está incluido y quién coordina su realización.'],
        ['titulo' => 'Coordinación posterior', 'texto' => 'Informe los horarios previstos de velación y destino final para organizar las actividades del servicio.'],
    ]],
    'guia-sin-embalsamiento' => ['nombre' => 'Servicio sin embalsamamiento', 'icono' => 'HeartIcon', 'grupo' => 'Atención complementaria', 'pasos' => [
        ['titulo' => 'Alcance del servicio', 'texto' => 'Confirme con el personal que no se realizará embalsamamiento y revise los servicios incluidos.'],
        ['titulo' => 'Programación y documentos', 'texto' => 'Coordine los horarios y solicite al personal los documentos que correspondan al servicio acordado.'],
    ]],
    'guia-aseguradora' => ['nombre' => 'Servicio mediante aseguradora', 'icono' => 'ShieldIcon', 'grupo' => 'Cobertura y contratación', 'pasos' => [
        ['titulo' => 'Identificación de la cobertura', 'texto' => 'Proporcione el nombre de la aseguradora, el convenio o referencia y un contacto para la coordinación.'],
        ['titulo' => 'Servicios autorizados', 'texto' => 'Revise con el personal qué servicios cubre la autorización de la aseguradora y cuáles requieren una contratación adicional.'],
    ]],
    'guia-custodia' => ['nombre' => 'Servicio con custodia', 'icono' => 'LockIcon', 'grupo' => 'Atención complementaria', 'pasos' => [
        ['titulo' => 'Identificación del caso', 'texto' => 'Proporcione al personal el nombre del responsable y los folios de custodia o liberación disponibles.'],
        ['titulo' => 'Coordinación de actividades', 'texto' => 'Confirme con el personal la situación del caso antes de programar traslados y demás actividades del servicio.'],
    ]],
    'guia-propiedad' => ['nombre' => 'Uso de propiedad en cementerio', 'icono' => 'cruz', 'grupo' => 'Cobertura y contratación', 'pasos' => [
        ['titulo' => 'Datos de la propiedad', 'texto' => 'Indique el titular y la ubicación de la propiedad que desea utilizar, así como la referencia de venta o convenio si la tiene.'],
        ['titulo' => 'Revisión del uso', 'texto' => 'Solicite al personal la revisión de la propiedad y los servicios disponibles antes de confirmar la inhumación.'],
    ]],
    'guia-plan' => ['nombre' => 'Uso de plan funerario', 'icono' => 'LayersIcon', 'grupo' => 'Cobertura y contratación', 'pasos' => [
        ['titulo' => 'Identificación del plan', 'texto' => 'Proporcione el nombre del titular y la referencia de solicitud o convenio para localizar su plan funerario.'],
        ['titulo' => 'Revisión del alcance', 'texto' => 'Revise con el personal los servicios y artículos incluidos, así como cualquier diferencia respecto del servicio solicitado.'],
    ]],
    'guia-exhumacion' => ['nombre' => 'Servicio de exhumación', 'icono' => 'cruz', 'grupo' => 'Atención complementaria', 'pasos' => [
        ['titulo' => 'Identificación de la ubicación', 'texto' => 'Proporcione al personal los datos del fallecido y la ubicación de la propiedad donde se encuentran sus restos.'],
        ['titulo' => 'Revisión y coordinación', 'texto' => 'Solicite al personal la revisión del caso, los documentos correspondientes y el destino previsto antes de confirmar la programación.'],
    ]],
];
