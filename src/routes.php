<?php
// Routes
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
$app->post('/usuario/seleccionar',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::seleccionar($request,$response);
});
$app->post('/usuario/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
    return usuario::insertar($request,$response);
});
$app->post('/usuario/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::actualizar($request,$response);
});

$app->post('/usuario/actualizarContrasena',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::actualizarContrasena($request,$response);
});
$app->post('/usuario/obtenerNuevo',function(ServerRequestInterface $request,ResponseInterface $response){
    return usuario::obtenerNuevo($request,$response);
});
$app->post('/exportar/usuario',function(ServerRequestInterface $request,ResponseInterface $response){
    return exportar::usuario($request,$response);

});
$app->post('/usuario/login', function (ServerRequestInterface $request, ResponseInterface $response){
    return usuario::logIn($request,$response);
});
$app->post('/usuario/permisos/obtener/{id}', function (ServerRequestInterface $request, ResponseInterface $response){
    $route = $request->getAttribute('route');
    $courseId = $route->getArgument('id');
    $newResponse = $response->withJson(PrivilegiosUsuario::obtenerPorUsuario($courseId),200);
    return ($newResponse);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Sucursal
$app->post('/sucursal/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return sucursal::seleccionar($request,$response);
});
$app->post('/sucursal/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
    return sucursal::registrar($request,$response);
});
$app->post('/sucursal/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
    return sucursal::actualizar($request,$response);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Permisos
$app->post('/permisos/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return $response->withJson(permisos::seleccionar($request,$response),200);
});
$app->post('/permisos/actualizar', function (ServerRequestInterface $request, ResponseInterface $response){
    return permisos::actualizar($request,$response);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Departamento
$app->post('/departamento/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return departamento::seleccionar($request,$response);
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Categoria
$app->post('/categoria/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return categoria::seleccionar($request,$response);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Artículo
$app->post('/articulo/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return articulo::seleccionar($request,$response);
});
$app->post('/articulo/seleccionarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
    return articulo::seleccionarListaFija($request,$response);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Inventario
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
$app->post('/inventario/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
    return inventario::insertar($request,$response);
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Bitacora
$app->post('/bitacora/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return bitacora::seleccionar($request,$response);
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Parámetros
$app->post('/parametros/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionar($request,$response);
});
$app->post('/parametros/seleccionarEstado', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionarEstado($request,$response);
});
$app->post('/parametros/seleccionar/lista/fija', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionarListaFija($request,$response);
});
$app->post('/parametros/seleccionar/lista/excluyente', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionarListaFijaExcluyente($request,$response);
});
$app->post('/parametros/seleccionar/lista/fija/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::seleccionarListaFijaInventario($request,$response);
});
$app->post('/parametros/insertarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::insertarListaFija($request,$response);
});
$app->post('/parametros/eliminarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::eliminarListaFija($request,$response);
});
$app->post('/parametros/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
    return parametros::actualizar($request,$response);
});
$app->post('/parametros/actualizarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::actualizarListaFija($request,$response);
});
$app->post('/parametros/actualizarListaExcluyente', function (ServerRequestInterface $request, ResponseInterface $response){
    return parametros::actualizarListaExcluyente($request,$response);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Sexo
$app->post('/sexo/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return sexo::seleccionar($request,$response);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Nivel de Autorización
$app->post('/nivel_autorizacion/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return nivel_autorizacion::seleccionar($request,$response);
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Batch
$app->post('/importar/departamento', function (ServerRequestInterface $request, ResponseInterface $response){
    return importar::Departamento($request,$response);
});
$app->post('/importar/categoria', function (ServerRequestInterface $request, ResponseInterface $response){
    return importar::Categoria($request,$response);
});
$app->post('/importar/articulo', function (ServerRequestInterface $request, ResponseInterface $response){
    return importar::Articulo($request,$response);
});

$app->post('/exportar/parametro', function (ServerRequestInterface $request, ResponseInterface $response){
    return exportar::Parametro($request,$response);

});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Concepto
$app->post('/concepto/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
    return concepto::seleccionar($request,$response);
});
$app->post('/sucursal/login', function (ServerRequestInterface $request, ResponseInterface $response){
    return sucursal::login($request,$response);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$app->post('/exportar/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
    return exportar::exportarInventarioAPI($request,$response);
});
$app->post('/exportar/inventario/actualizar', function (ServerRequestInterface $request, ResponseInterface $response){
    return exportar::actualizarInventario($request,$response);
});
$app->post('/importar/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
    return importar::importarInventarioAPI($request,$response);
});

$app->post('/exportar/venta/obtener/ultima',function(ServerRequestInterface $request,ResponseInterface $response){
    return exportar::ultimaVenta($request,$response);
});

$app->post('/importar/venta',function(ServerRequestInterface $request,ResponseInterface $response){
    return importar::venta($request,$response);
});

$app->post('/importar/venta/detalle',function(ServerRequestInterface $request,ResponseInterface $response){
    return importar::detalleventa($request,$response);
});
$app->post('/importar/usuario',function(ServerRequestInterface $request,ResponseInterface $response){
    return importar::importarUsuarios($request,$response);
});

$app->post('/general/dashboard',function(ServerRequestInterface $request,ResponseInterface $response){

    return exportar::generarDashBoard($request,$response);
});

$app->post('/general/dashboard/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){

    return exportar::actualizarDashBoard($request,$response);
});
$app->post('/general/dashboard/tiempo',function(ServerRequestInterface $request,ResponseInterface $response){

    return exportar::GenerarDashBoardPorSucursal($request,$response);
});



