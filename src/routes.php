<?php
// Routes
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
/*AJUSTE*/
$app->post('/ajuste/mostrar',function(ServerRequestInterface $request,ResponseInterface $response){
    return ajuste::seleccionarOpcionMostrar($request,$response);
});
$app->post('/ajuste/insertar',function(ServerRequestInterface $request, ResponseInterface $response){
    return ajuste::insertar($request,$response);
});
$app->post('/ajuste/seleccionar/cabecero',function(ServerRequestInterface $request, ResponseInterface $response){
    return ajuste::seleccionar($request,$response);
});
$app->post('/ajuste/seleccionar/detalle',function(ServerRequestInterface $request, ResponseInterface $response){
    return ajuste::detalle($request,$response);
});
$app->post('/ajuste/seleccionar/todo',function(ServerRequestInterface $request,ResponseInterface $response){
    return ajuste::seleccionarTodo($request,$response);
});
/*ARTICULO*/
$app->post('/articulo/reporte/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response) {
    return articulo::reporteMovimiento($request, $response);
});
$app->post('/articulo/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return articulo::seleccionar($request,$response);
});
$app->post('/articulo/seleccionarIndividualMovimiento', function (ServerRequestInterface $request, ResponseInterface $response) {
    return articulo::seleccionarIndividualMovimiento($request, $response);
});
$app->post('/articulo/seleccionarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
    return articulo::seleccionarListaFija($request,$response);
});
/*BITACORA*/
$app->post('/bitacora/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return bitacora::seleccionar($request,$response);
});
/*CATEGORIA*/
$app->post('/categoria/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return categoria::seleccionar($request,$response);
});
/*CONCEPTO*/
$app->post('/concepto/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return concepto::seleccionar($request,$response);
});
$app->post('/concepto/reporte',function(ServerRequestInterface $request,ResponseInterface $response){
    return concepto::seleccionarConceptoReporte($request,$response);
});
/*DEPARTAMENTO*/
$app->post('/departamento/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return departamento::seleccionar($request,$response);
});
/*DETALLES VENTA*/
$app->post('/detalles_venta/seleccionar',function(ServerRequestInterface $request,ResponseInterface $response){
    return detalles_venta::seleccionar($request,$response);
});
$app->post('/detalles_venta/seleccionarDetalles',function(ServerRequestInterface $request,ResponseInterface $response){
    return detalles_venta::seleccionarDetalles($request,$response);
});
/*EXPORTAR*/
$app->post('/exportar/ajuste',function(ServerRequestInterface $request,ResponseInterface $response){
    return exportar::exportarAjuste($request,$response);
});
$app->post('/exportar/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
    return exportar::exportarInventarioAPI($request,$response);
});
$app->post('/exportar/inventario/actualizar', function (ServerRequestInterface $request, ResponseInterface $response){
    return exportar::actualizarInventario($request,$response);
});
$app->post('/exportar/parametro', function (ServerRequestInterface $request, ResponseInterface $response){
    return exportar::Parametro($request,$response);
});
$app->post('/exportar/usuario',function(ServerRequestInterface $request,ResponseInterface $response){
    return exportar::usuario($request,$response);
});
$app->post('/exportar/venta/obtener/ultima/{opcion}',function(ServerRequestInterface $request,ResponseInterface $response){
    $route = $request->getAttribute('route');
    $courseId = $route->getArgument('opcion');
    switch($courseId)
    {
        case "detalleventa":{
            return exportar::ultimoDetalleVenta($request,$response);
        }break;
        case "ventatipopago":{
            return exportar::ultimoVentaTipoPago($request,$response);
        }break;
        default:{
            return exportar::ultimaVenta($request,$response);
        }break;
    }
});
/*GENERICO*/
$app->post('/general/dashboard',function(ServerRequestInterface $request,ResponseInterface $response){
    return exportar::generarDashBoard($request,$response);
});
$app->post('/general/dashboard/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
    return exportar::actualizarDashBoard($request,$response);
});
$app->post('/general/dashboard/tiempo',function(ServerRequestInterface $request,ResponseInterface $response){
    return exportar::GenerarDashBoardPorSucursal($request,$response);
});
/*IMPORTAR*/
$app->post('/importar/articulo', function (ServerRequestInterface $request, ResponseInterface $response){
    return importar::Articulo($request,$response);
});
$app->post('/importar/categoria', function (ServerRequestInterface $request, ResponseInterface $response){
    return importar::Categoria($request,$response);
});
$app->post('/importar/departamento', function (ServerRequestInterface $request, ResponseInterface $response){
    return importar::Departamento($request,$response);
});
$app->post('/importar/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
    return importar::importarInventarioAPI($request,$response);
});
$app->post('/importar/usuario',function(ServerRequestInterface $request,ResponseInterface $response){
    return importar::importarUsuarios($request,$response);
});
$app->post('/importar/venta',function(ServerRequestInterface $request,ResponseInterface $response){
    return importar::venta($request,$response);
});
$app->post('/importar/venta/cancelacion',function(ServerRequestInterface $request,ResponseInterface $response){
    return importar::cancelacion($request,$response);
});
$app->post('/importar/venta/detalle',function(ServerRequestInterface $request,ResponseInterface $response){
    return importar::detalleventa($request,$response);
});
$app->post('/importar/venta/tipo/pago',function(ServerRequestInterface $request,ResponseInterface $response){
    return importar::VentaTipoPago($request,$response);
});
/*INVENTARIO*/
$app->post('/inventario/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
    return inventario::insertar($request,$response);
});
$app->post('/inventario/reporte/actual',function(ServerRequestInterface $request,ResponseInterface $response){
    return inventario::reporteActual($request,$response);
});
$app->post('/inventario/reporte/cabecero',function(ServerRequestInterface $request,ResponseInterface $response){
    return inventario::reporteCabecero($request,$response);
});
$app->post('/inventario/reporte/detalle',function(ServerRequestInterface $request,ResponseInterface $response){
    return inventario::reporteDetalle($request,$response);
});
$app->post('/inventario/seleccionarAzar', function (ServerRequestInterface $request, ResponseInterface $response){
    return inventario::seleccionarAzar($request,$response);
});
$app->post('/inventario/seleccionarIndividual', function (ServerRequestInterface $request, ResponseInterface $response){
    return inventario::seleccionarIndividual($request,$response);
});
$app->post('/inventario/seleccionarMasConflictivos', function (ServerRequestInterface $request, ResponseInterface $response){
    return inventario::seleccionarMasConflictivos($request,$response);
});
$app->post('/inventario/seleccionarMasVendidos', function (ServerRequestInterface $request, ResponseInterface $response){
    return inventario::seleccionarMasVendidos($request,$response);
});
$app->post('/inventario/seleccionarPorProveedor',function(ServerRequestInterface $request,ResponseInterface $response){
    return inventario::seleccionarPorProveedor($request,$response);
});
$app->post('/inventario/temporal/agregar',function(ServerRequestInterface $request,ResponseInterface $response){
    return inventarioTemporal::insertar($request,$response);
});
$app->post('/inventario/temporal/seleccionar',function(ServerRequestInterface $request,ResponseInterface $response){
    return inventarioTemporal::seleccionar($request,$response);
});
    $app->post('/inventario/temporal/seleccionar/lista',function(ServerRequestInterface $request,ResponseInterface $response){
    return inventarioTemporal::seleccionarLista($request,$response);
});
/*NIVEL AUTORIZACION*/
$app->post('/nivel_autorizacion/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return nivel_autorizacion::seleccionar($request,$response);
});
/*PARAMETROS*/
$app->post('/parametros/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
    return parametros::actualizar($request,$response);
});
$app->post('/parametros/actualizarListaExcluyente', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::actualizarListaExcluyente($request,$response);
});
$app->post('/parametros/actualizarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::actualizarListaFija($request,$response);
});
$app->post('/parametros/eliminarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::eliminarListaFija($request,$response);
});
$app->post('/parametros/insertarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::insertarListaFija($request,$response);
});
$app->post('/parametros/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionar($request,$response);
});
$app->post('/parametros/seleccionar/lista/excluyente', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionarListaFijaExcluyente($request,$response);
});
$app->post('/parametros/seleccionar/lista/fija', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionarListaFija($request,$response);
});
$app->post('/parametros/seleccionar/lista/fija/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionarListaFijaInventario($request,$response);
});
$app->post('/parametros/seleccionarEstado', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionarEstado($request,$response);
});
/*PERDIDAS*/
$app->post('/perdidas/reporte/cabecero',function(ServerRequestInterface $request, ResponseInterface $response){
    return perdidas::reporteCabecero($request,$response);
});
$app->post('/perdidas/reporte/detalle',function(ServerRequestInterface $request,ResponseInterface $response){
    return perdidas::reporteDetalle($request,$response);
});
/*PERMISOS*/
$app->post('/permisos/actualizar', function (ServerRequestInterface $request, ResponseInterface $response){
    return permisos::actualizar($request,$response);
});
$app->post('/permisos/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return $response->withJson(permisos::seleccionar($request,$response),200);
});
/*PROVEEDOR*/
$app->post('/proveedor/seleccionar',function(ServerRequestInterface $request,ResponseInterface $response){
    return proveedor::seleccionar($request,$response);
});
/*SEXO*/
$app->post('/sexo/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return sexo::seleccionar($request,$response);
});
/*SUCURSAL*/
$app->post('/sucursal/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
    return sucursal::actualizar($request,$response);
});
$app->post('/sucursal/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
    return sucursal::registrar($request,$response);
});
$app->post('/sucursal/login', function (ServerRequestInterface $request, ResponseInterface $response){
    return sucursal::login($request,$response);
});
$app->post('/sucursal/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return sucursal::seleccionar($request,$response);
});
/*USUARIO*/
$app->post('/usuario/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::actualizar($request,$response);
});
$app->post('/usuario/actualizarContrasena',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::actualizarContrasena($request,$response);
});
$app->post('/usuario/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
    return usuario::insertar($request,$response);
});
$app->post('/usuario/login', function (ServerRequestInterface $request, ResponseInterface $response){
    return usuario::logIn($request,$response);
});
$app->post('/usuario/obtenerNuevo',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::obtenerNuevo($request,$response);
});
$app->post('/usuario/permisos/obtener/{id}', function (ServerRequestInterface $request, ResponseInterface $response){
    $route = $request->getAttribute('route');
    $courseId = $route->getArgument('id');
    $newResponse = $response->withJson(PrivilegiosUsuario::obtenerPorUsuario($courseId),200);
    return ($newResponse);
});
$app->post('/usuario/seleccionar',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::seleccionar($request,$response);
});
$app->post('/usuario/seleccionarApi',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::seleccionarApi($request,$response);
});
$app->get('/usuario/dummy',function(ServerRequestInterface $request,ResponseInterface $response){
    return $response;
});
$app->post('/inventario/nombre',function(ServerRequestInterface $request,ResponseInterface $response){

    return inventario::actualizarNombre($request,$response);
});
$app->post('/inventario/cancelar',function(ServerRequestInterface $request, ResponseInterface $response){
    return inventario::cancelarOrden($request,$response);
});
$app->post('/sucursal/lista/conexion',function(ServerRequestInterface $request,ResponseInterface $response){
    return sucursal::seleccionarConexiones($request,$response);
});

/*$app->post('/tipo_pago/insertar',function(ServerRequestInterface $request,ResponseInterface $response){
 return tipo_pago::insertar($request,$response);
});
 */
/*$app->post('/usuario/seleccionarApi', function (ServerRequestInterface $request, ResponseInterface $response){
 return usuario::seleccionarApi($request,$response);
});
 */