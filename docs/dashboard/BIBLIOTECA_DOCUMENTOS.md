# Guías y documentos del menú

El apartado de `VerticalNavMenu.vue` abre `BibliotecaDocumentos.vue`. No sustituye el menú de permisos ni las rutas de los módulos existentes. Es material de consulta para usuarios autenticados.

## Interacción

Cada acceso abre un modal de pantalla completa, sin logo ni botón de apertura adicional. Las hojas son botones accesibles por teclado. Al seleccionar una hoja se abre `VisorReportes.vue`; cerrar el visor conserva el catálogo y el idioma. El modal se cierra con X o Escape; los clics exteriores no lo cierran. PopupManager mantiene la pila y el foco. La biblioteca utiliza z-index 57000, debajo del visor existente (58000).

## Fuentes y reportes

- Guías de servicio: catálogo de 21 escenarios en config/guias_servicio.php, agrupados por destino final, velación/ceremonia, traslados, atención complementaria y cobertura/contratación. La clave guiás (sin acento en la URL: guias) en la ruta existente devuelve solo clave, nombre, icono y grupo. Cada guía aporta pasos de coordinación propios y mantiene la documentación del acta de defunción como referencia según corresponda. No lee expedientes ni incorpora requisitos normativos específicos. Para cambiar el contenido editorial o añadir escenarios, editar ese archivo de configuración y regenerar la caché de configuración si se usa config:cache.
- Planes funerarios: catálogo completo y fichas de planes activos devueltos por get_planes. Los importes se consultan al abrir el reporte, no se copian de las hojas.
- Cementerio: lista completa y lista por tipo de propiedad devueltos por get_financiamientos. Reutiliza planes-venta del visor de CementerioController.

Las guías se validan contra config/guias_servicio.php; DOCUMENTOS_SERVICIO_VISOR conserva los documentos del expediente. No se añaden rutas: usan las tres rutas existentes de documentos de servicios. Catálogos y fichas conservan sus rutas y controladores. No se ofrecen contratos, reglamentos o guías de propietario que requieran seleccionar un expediente real.

## PDF, correo y configuración

El visor recibe HTML primero; la descarga y el correo utilizan su instantánea privada. Los tokens vencidos no cierran la sesión. Se reutilizan REPORTES_VISOR_ZOOM y las variables de marca de agua por sección: REPORTES_MARCA_AGUA_SERVICIOS_FUNERARIOS, REPORTES_MARCA_AGUA_PLANES_VENTA y REPORTES_MARCA_AGUA_CEMENTERIO. No se crean variables nuevas ni se modifica .env. Las marcas de logo siguen el comportamiento propio de los templates existentes.

## Mantenimiento

Agregar guías en config/guias_servicio.php: el modal las obtiene automáticamente del backend. Para nuevas hojas comerciales, ajustar cargarCatalogo y su catálogo del controlador correspondiente. Mantener el catálogo cerrado; no ejecutar nombres de métodos recibidos de la URL. Las consultas de planes y propiedades se realizan al abrir su sección. La tabla/listados de los módulos originales no se modifican.
