<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/**en pruebas */


/**ruta para obtener tokens */
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
/**rutas de modulo en proceso */


/**rutas publicas_ entran sin token */
Route::get('funeraria/get_planes/{solo_a_futuro?}/{id_plan?}', 'FunerariaController@get_planes');
/**fin de rutas de modulo en proceso */

/**servicios accedidos desde el backend */
Route::middleware(['client'])->group(function () {
    Route::get('pagos/get_pagos_backend/{id_pago?}/{paginated?}/{ver_subpagos?}', 'PagosController@get_pagos');
});
/**fin de servicios accedidos desde el backend */

/**RUTAS PARA EL SISTEMA DE LOGUEADO*/
Route::middleware(['auth:api'])->group(function () {
    Route::post('firmas/firmar', 'FirmasController@firmar');
    Route::get('firmas/get_areas_firmar/{documento_id?}', 'FirmasController@get_areas_firmar');
    Route::get('firmas/get_firma/{operacion_id?}/{area_id?}/{tipo?}', 'FirmasController@get_firma');

    Route::get('pagos/get_pagos/{id_pago?}/{paginated?}/{ver_subpagos?}', 'PagosController@get_pagos');


    Route::post('logout_usuario', 'Usuarios\UsuariosController@logout_usuario');
    /**RUTA PARA OBTENER LOS PUESTOS DISPONIBLEN EN LA EMPRESA */
    /**RUTA PARA OBTENER LOS PERMISOS DEL USUARIO */
    Route::get('get_permisos', 'Usuarios\UsuariosController@get_permisos');
    /**RUTA PARA OBTENER EL PERFIL DEL USUARIO */
    Route::get('get_perfil', 'Usuarios\UsuariosController@get_perfil');
    /**RUTA PARA OBTENER LOS DATOS DEL USUARIO */
    Route::get('get_usuarioById', 'Usuarios\UsuariosController@get_usuarioById');

    /**LAS RUTAS QUE ESTAN CONTROLADAS CON EL MIDDLEWARE "permiso" RECIBEN 2 PARAMETROS
     * EL PRIMERO ES EL NUMERO DEL MODULO
     * //4 permisos (1-Agregar, 2-Editar, 3-Eliminar y 4-Consultar)
     * EL SEGUNDO EL NUMERO DE PERMISO
     */
    Route::get('get_puestos', 'Usuarios\UsuariosController@get_puestos');
    Route::get('get_usuarios', 'Usuarios\UsuariosController@index');
    Route::post('add_usuario', 'Usuarios\UsuariosController@add_usuario');
    Route::post('update_usuario', 'Usuarios\UsuariosController@update_usuario');
    Route::post('delete_usuario', 'Usuarios\UsuariosController@delete_usuario');
    Route::post('activate_usuario', 'Usuarios\UsuariosController@activate_usuario');
    Route::get('usuarios_pdfs', 'Usuarios\UsuariosController@pdfs');
    Route::post('/usuarios/actualizar_perfil', 'Usuarios\UsuariosController@actualizar_perfil');
    /**verificar el password del usuario recibe el request del token y el password */
    Route::post('verificar_password', 'Usuarios\UsuariosController@verificar_password');

    /**RUTAS PARA ROLES */
    Route::get('get_roles', 'Usuarios\RolesController@index');
    Route::get('get_roles_lista', 'Usuarios\RolesController@get_roles');
    Route::get('get_rol_id', 'Usuarios\RolesController@get_rol_id');
    Route::get('get_modulos_permisos', 'Usuarios\RolesController@get_modulos_permisos');
    Route::get('get_modulos_urls_permisos', 'Usuarios\RolesController@get_modulos_urls_permisos');
    Route::get('get_modulos', 'Usuarios\RolesController@get_modulos');
    Route::get('get_rol_permisos', 'Usuarios\RolesController@get_rol_permisos');
    Route::post('add_roles', 'Usuarios\RolesController@add_roles');
    Route::post('update_rol', 'Usuarios\RolesController@update_rol');
    Route::post('delete_rol', 'Usuarios\RolesController@delete_rol');

    //Empresa
    Route::get('empresa/get_datos_empresa', 'EmpresaController@get_datos_empresa');
    Route::get('empresa/get_regimenes', 'EmpresaController@get_regimenes');
    Route::post('empresa/modificar_datos', 'EmpresaController@modificar_datos');
    Route::get('empresa/funeraria', 'EmpresaController@get');
    Route::post('empresa/funeraria', 'EmpresaController@save');
    Route::post('empresa/registro-publico', 'EmpresaController@saveRegistroPublico');
    Route::get('empresa/registro-publico', 'EmpresaController@getRegistroPublico');
    //Cementerio
    Route::get('empresa/cementerio', 'EmpresaController@getCementerio');
    Route::post('empresa/cementerio', 'EmpresaController@saveCementerio');
    //crematorio
    Route::get('empresa/crematorio', 'EmpresaController@getCrematorio');
    Route::post('empresa/crematorio', 'EmpresaController@saveCrematorio');
    //velatorio
    Route::get('empresa/velatorio', 'EmpresaController@getVelatorio');
    Route::post('empresa/velatorio', 'EmpresaController@saveVelatorio');
    //validate cer file
    Route::post('empresa/facturacion/validateCER', 'EmpresaController@validateCERFile');
    Route::post('empresa/facturacion/validateKEY', 'EmpresaController@validateKEYFile');
    Route::post('empresa/facturacion', 'EmpresaController@saveFacturacion');
    ///SAT
    Route::get('regimenes/', 'SATRegimenesController@getAll');
    Route::get('monedas/', 'SATMonedasController@getAll');

    /**CLIENTES */

    Route::get('/clientes/get_nacionalidades', 'ClientesController@get_nacionalidades');
    Route::post('/clientes/guardar_cliente', 'ClientesController@guardar_cliente');
    Route::post('/clientes/modificar_cliente', 'ClientesController@modificar_cliente');
    Route::get('/clientes/get_cliente_id', 'ClientesController@get_cliente_id');
    Route::post('/clientes/baja_cliente', 'ClientesController@baja_cliente');
    Route::post('/clientes/alta_cliente', 'ClientesController@alta_cliente');
    Route::post('/clientes/servicios_gratis', 'ClientesController@servicios_gratis');
    Route::get('/clientes/get_clientes', 'ClientesController@get_clientes');
    /**rutas del cementerio */
    Route::get('generarNumeroTitulo', 'CementerioController@generarNumeroTitulo');
    Route::post('cementerio/control_ventas/{tipo_servicio}', 'CementerioController@control_ventas'); //agregar,modificar
    Route::post('cementerio/registrar_precio_propiedad', 'CementerioController@registrar_precio_propiedad');
    Route::post('cementerio/update_precio_propiedad', 'CementerioController@update_precio_propiedad');
    Route::post('cementerio/enable_disable', 'CementerioController@enable_disable');
    Route::get('cementerio/get_financiamientos', 'CementerioController@get_financiamientos');
    Route::get('cementerio/get_precio_by_id', 'CementerioController@get_precio_by_id');
    Route::get('cementerio/lista_precios_pdf/{idioma?}', 'CementerioController@lista_precios_pdf');
    Route::get('cementerio/get_vendedores', 'CementerioController@get_vendedores');
    Route::get('titulos/{operacion_id?}', 'CementerioController@generarNumeroTitulo');
    Route::get('cementerio/documento_estado_de_cuenta_cementerio', 'CementerioController@documento_estado_de_cuenta_cementerio');
    Route::get('cementerio/acuse_cancelacion', 'CementerioController@acuse_cancelacion');
    Route::get('cementerio/documento_solicitud', 'CementerioController@documento_solicitud');
    Route::get('cementerio/documento_convenio', 'CementerioController@documento_convenio');
    Route::get('cementerio/documento_titulo', 'CementerioController@documento_titulo');
    Route::get('cementerio/referencias_de_pago/{id_pago?}', 'CementerioController@referencias_de_pago');
    Route::get('cementerio/reglamento_pago', 'CementerioController@reglamento_pago');
    Route::get('cementerio/get_ventas/{id_venta?}/{paginated?}/', 'CementerioController@get_ventas');
    Route::get('cementerio/servicios_propiedad', 'CementerioController@servicios_propiedad');
    Route::post('cementerio/cancelar_venta', 'CementerioController@cancelar_venta');
    Route::post('cementerio/control_cuotas/{tipo_servicio?}', 'CementerioController@control_cuotas');
    Route::post('cementerio/cancelar_cuota', 'CementerioController@cancelar_cuota');
    Route::get('cementerio/get_cuotas/{id_cuota?}/{paginated?}/', 'CementerioController@get_cuotas');
    Route::get('cementerio/get_cuotas_simple/', 'CementerioController@get_cuotas_simple');
    Route::get('cementerio/get_cuota_pdf_todas', 'CementerioController@get_cuota_pdf_todas');
    Route::get('cementerio/get_cuota_pdf', 'CementerioController@get_cuota_pdf');
    Route::get('cementerio/get_abonos_vencidos_propiedades', 'CementerioController@get_abonos_vencidos_propiedades');
    Route::get('cementerio/get_cementerio', 'CementerioController@get_cementerio');
    Route::get('cementerio/get_tipo_propiedades', 'CementerioController@get_tipo_propiedades');
    Route::get('cementerio/get_mapeado', 'CementerioController@get_mapeado');
    Route::post('cementerio/actualizar_status_convenio', 'CementerioController@actualizar_status_convenio');

    /**rutas de funeraria ventas planes */
    Route::post('funeraria/control_ventas/{tipo_servicio}', 'FunerariaController@control_ventas'); //agregar,modificar
    Route::post('funeraria/control_planes/{tipo_servicio?}', 'FunerariaController@control_planes');
    Route::post('funeraria/enable_disable_planes', 'FunerariaController@enable_disable_planes');
    Route::post('funeraria/registrar_precio', 'FunerariaController@registrar_precio');
    Route::get('funeraria/get_precio_by_id', 'FunerariaController@get_precio_by_id');
    Route::post('funeraria/update_precio', 'FunerariaController@update_precio');
    Route::post('funeraria/enable_disable', 'FunerariaController@enable_disable');
    Route::get('funeraria/pdf_plan_funerario/{idioma?}', 'FunerariaController@pdf_plan_funerario');
    Route::get('funeraria/planes_funerarios/{idioma?}', 'FunerariaController@planes_funerarios');
    Route::post('funeraria/cancelar_venta', 'FunerariaController@cancelar_venta');
    Route::post('funeraria/cancelar_solicitud', 'FunerariaController@cancelar_solicitud');
    Route::get('funeraria/documento_solicitud', 'FunerariaController@documento_solicitud');
    Route::get('funeraria/documento_convenio', 'FunerariaController@documento_convenio');
    Route::get('funeraria/documento_finiquitado', 'FunerariaController@documento_finiquitado');
    Route::get('funeraria/servicio_funerario/acuse_cancelacion', 'FunerariaController@servicio_acuse_cancelacion');
    Route::get('funeraria/referencias_de_pago', 'FunerariaController@referencias_de_pago');
    Route::get('funeraria/reglamento_pago', 'FunerariaController@reglamento_pago');
    Route::get('funeraria/acuse_cancelacion', 'FunerariaController@acuse_cancelacion');
    Route::get('funeraria/documento_estado_de_cuenta_planes', 'FunerariaController@documento_estado_de_cuenta_planes');
    Route::get('funeraria/get_servicios_adeudos', 'FunerariaController@get_servicios_adeudos');
    Route::get('funeraria/get_abonos_vencidos_planes_funerarios', 'FunerariaController@get_abonos_vencidos_planes_funerarios');
    Route::get('funeraria/get_ventas/{id_venta?}/{paginated?}/', 'FunerariaController@get_ventas');
    Route::get('funeraria/get_solicitudes_servicios/{id_servicio?}/{paginated?}/{planes_funerarios_futuro?}/{uso_terreno_id?}/{unir_lotes_cantidad?}', 'FunerariaController@get_solicitudes_servicios');


    /**rutas de pagos */
    Route::get('pagos/calcular_adeudo/{referencia}/{fecha_pago}/{multipago?}', 'PagosController@calcular_adeudo');
    Route::post('pagos/guardar_pago', 'PagosController@guardar_pago');
    Route::get('pagos/get_formas_pago_sat', 'PagosController@get_formas_pago_sat');
    Route::get('pagos/get_monedas_sat', 'PagosController@get_monedas_sat');
    Route::get('pagos/get_cobradores', 'PagosController@get_cobradores');
    Route::get('pagos/recibo_de_pago/{id_pago?}', 'PagosController@recibo_de_pago');
    Route::post('pagos/cancelar_pago', 'PagosController@cancelar_pago');
    /**fin de rutas de pagos */

    /**proveedores */
    Route::post('/proveedores/guardar_proveedor', 'ProveedoresController@guardar_proveedor');
    Route::post('/proveedores/modificar_proveedor', 'ProveedoresController@modificar_proveedor')->middleware('permiso:10,28');
    Route::post('/proveedores/delete_proveedor', 'ProveedoresController@delete_proveedor');
    Route::post('/proveedores/alta_proveedor', 'ProveedoresController@alta_proveedor');
    Route::get('proveedores/get_proveedores/{id_provedor?}/{paginated?}', 'ProveedoresController@get_proveedores');
    /**invnetario */

    Route::post('inventario/control_articulos/{tipo_servicio?}', 'InventarioController@control_articulos');
    Route::post('inventario/enable_disable/{tipo}', 'InventarioController@enable_disable');
    Route::post('inventario/ajustar_inventario', 'InventarioController@ajustar_inventario');
    Route::get('inventarios/cementerio/propiedadesById', 'CementerioController@propiedadesById');
    Route::get('inventarios/cementerio/get_propiedades_by_tipo', 'CementerioController@get_propiedades_by_tipo');
    Route::get('inventarios/cementerio/get_usuarios_para_vendedores', 'CementerioController@get_usuarios_para_vendedores');
    Route::get('inventarios/cementerio/tipoPropiedades', 'CementerioController@tipoPropiedades');
    Route::get('inventarios/cementerio/get_ventas_referencias_propiedades', 'CementerioController@get_ventas_referencias_propiedades');
    Route::get('inventarios/cementerio/get_columna_fila_terraza', 'CementerioController@get_columna_fila_terraza');
    Route::get('inventarios/cementerio/precios_tarifas', 'CementerioController@precios_tarifas');
    Route::post('inventarios/cementerio/actualizar_precios_tarifas', 'CementerioController@actualizar_precios_tarifas');
    Route::get('inventario/get_inventario/{id_articulo?}/{paginated?}/{id_departamento?}/{id_categoria?}/{tipo_articulo?}/{solo_inventariable?}', 'InventarioController@get_articulos');
    Route::get('inventarios/cementerio/get_sat_formas_pago', 'CementerioController@get_sat_formas_pago');
    Route::get('inventarios/cementerio/get_antiguedades_venta', 'CementerioController@get_antiguedades_venta');
    Route::get('inventario/get_inventario_pdf', 'InventarioController@get_inventario_pdf');
    Route::get('inventario/pdf_nota_compra', 'InventarioController@pdf_nota_compra');
    Route::get('inventario/get_tipo_articulos', 'InventarioController@get_tipo_articulos');
    Route::get('inventario/get_categorias', 'InventarioController@get_categorias');
    Route::get('inventario/get_unidades', 'InventarioController@get_unidades');
    Route::get('inventario/get_sat_unidades', 'InventarioController@get_sat_unidades');

    Route::post('inventario/control_compras/{tipo?}', 'InventarioController@control_compras');
    Route::post('inventario/cancelar_compra', 'InventarioController@cancelar_compra');
    Route::get('inventario/get_compras/{id_compra?}/{paginated?}/', 'InventarioController@get_compras');
    Route::get('inventario/get_ajuste_pdf', 'InventarioController@get_ajuste_pdf');
    Route::get('inventario/get_ajustes/{id_ajuste?}/{paginated?}', 'InventarioController@get_ajustes');

    Route::get('inventario/get_inventario_conteo_pdf', 'InventarioController@get_inventario_conteo_pdf');
    Route::get('inventario/get_pdf_etiquetas', 'InventarioController@get_pdf_etiquetas');

    /**rutas de servicios funerarios */
    Route::post('funeraria/control_solicitud/{tipo_servicio}', 'FunerariaController@control_solicitud');
    Route::post('funeraria/control_contratos/{tipo_servicio}', 'FunerariaController@control_contratos');
    Route::get('funeraria/get_personal_recoger', 'FunerariaController@get_personal_recoger');

    Route::get('funeraria/get_hoja_solicitud', 'FunerariaController@get_hoja_solicitud');
    Route::get('funeraria/hoja_preautorizacion', 'FunerariaController@hoja_preautorizacion');
    Route::get('funeraria/certificado_defuncion', 'FunerariaController@certificado_defuncion');
    Route::get('funeraria/instrucciones_servicio_funerario', 'FunerariaController@instrucciones_servicio_funerario');
    Route::get('funeraria/contancia_de_embalsamiento', 'FunerariaController@contancia_de_embalsamiento');
    Route::get('funeraria/contrato_servicio_funerario', 'FunerariaController@contrato_servicio_funerario');
    Route::get('funeraria/material_velacion_rentado', 'FunerariaController@material_velacion_rentado');
    Route::get('funeraria/entrega_acta_defuncion', 'FunerariaController@entrega_acta_defuncion');
    Route::get('funeraria/entrega_cenizas', 'FunerariaController@entrega_cenizas');
    Route::get('funeraria/orden_servicio', 'FunerariaController@orden_servicio');
    Route::get('funeraria/get_estados_civiles', 'FunerariaController@get_estados_civiles');
    Route::get('funeraria/get_escolaridades', 'FunerariaController@get_escolaridades');
    Route::get('funeraria/get_afiliaciones', 'FunerariaController@get_afiliaciones');
    Route::get('funeraria/get_sitios_muerte', 'FunerariaController@get_sitios_muerte');
    Route::get('funeraria/get_titulos', 'FunerariaController@get_titulos');
    Route::get('funeraria/get_estados_afectado', 'FunerariaController@get_estados_afectado');
    Route::get('funeraria/get_lugares_velacion', 'FunerariaController@get_lugares_velacion');
    Route::get('funeraria/get_lugares_inhumacion', 'FunerariaController@get_lugares_inhumacion');
    Route::get('funeraria/get_material_velacion/{id_articulo?}/{paginated?}/{id_departamento?}/{id_categoria?}/{tipo_articulo?}/{solo_inventariable?}', 'InventarioController@get_articulos');
    Route::get('funeraria/get_tipos_contratante', 'FunerariaController@get_tipos_contratante');


    Route::get('funeraria/get_inventario/{id_articulo?}/{paginated?}/{solo_existencias?}/{con_material_velacion?}', 'FunerariaController@get_inventario');
    Route::get('funeraria/get_categorias_servicio', 'FunerariaController@get_categorias_servicio');
    /**fin de rutas del cementerio */

    /**RUTAS PARA FACTURACION */
    Route::get('facturacion/get_tipos_comprobante', 'FacturacionController@get_tipos_comprobante');
    Route::get('facturacion/get_metodos_pago', 'FacturacionController@get_metodos_pago');
    Route::get('facturacion/get_sat_formas_pago', 'FacturacionController@get_sat_formas_pago');
    Route::get('facturacion/get_tipos_relacion', 'FacturacionController@get_tipos_relacion');
    Route::get('facturacion/get_claves_productos_sat', 'FacturacionController@get_claves_productos_sat');
    Route::get('facturacion/get_sat_unidades', 'FacturacionController@get_sat_unidades');
    Route::get('facturacion/get_usos_cfdi', 'FacturacionController@get_usos_cfdi');
    Route::get('facturacion/get_sat_paises', 'FacturacionController@get_sat_paises');
    Route::get('facturacion/get_empresa_tipo_operaciones', 'FacturacionController@get_empresa_tipo_operaciones');
    Route::get('facturacion/get_operaciones/{id_operacion_local?}/{paginated?}/', 'FacturacionController@get_operaciones');
    Route::get('facturacion/get_cfdi_from_xml/{folio?}', 'FacturacionController@leer_xml');
    Route::get('facturacion/get_cfdis_timbrados/{folio_id?}/{paginated?}/{metodo_pago_id?}/{tipo_comprobante_id?}', 'FacturacionController@get_cfdis_timbrados');
    Route::get('facturacion/get_cfdi_download/{folio_id?}', 'FacturacionController@get_cfdi_download');
    Route::get('facturacion/consultar_cfdi_folio/{folio_id?}', 'FacturacionController@consultar_cfdi_folio');
    Route::get('facturacion/get_acuse_cancelacion_pdf/{folio_id?}', 'FacturacionController@get_acuse_cancelacion_pdf');
    Route::get('facturacion/get_cfdi_status_sat/{folio_id?}', 'FacturacionController@get_cfdi_status_sat');
    /**rutas de timbrado de cfdi */
    Route::get('facturacion/get_cfdi_pdf/{folio_id?}', 'FacturacionController@get_cfdi_pdf')->middleware(['permiso:21,62']);
    Route::post('facturacion/timbrar_cfdi', 'FacturacionController@timbrar_cfdi')->middleware(['permiso:21,60']);
    Route::post('facturacion/cancelar_cfdi_folio', 'FacturacionController@cancelar_cfdi_folio')->middleware(['permiso:21,61']);
    /**rutas de reportes */
    Route::get('reportes/get_reportes', 'ReportesController@get_reportes');
});






Route::get('inventarios/cementerio/documento_ubicacion_terreno', 'CementerioController@documento_ubicacion_terreno');

Route::get('pdfs', 'Usuarios\UsuariosController@pdfs');

//RUTA DEL LOGUIN
Route::post('login_usuario', 'Usuarios\UsuariosController@login_usuario');
//RUTA DEL REFRESH TOKEN
Route::post('refresh_token', 'Usuarios\UsuariosController@refresh_token');

//RECUPERAR CONTRASEÑA
Route::post('/password/email', 'Auth\Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\Api\ResetPasswordController@reset');

Route::get('ubicacion_texto', 'CementerioController@ubicacion_texto'); //borrar

//Route::resource('usuarios', 'UsuariosController',['only'=>['index']]);

/**FIN DE RUTAS DEL SISTEMA DE LOGUEADO */
