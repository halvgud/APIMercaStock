<?php
// Application middleware
/*
$corsOptions = array(
     "origin" => "mercastock.mercatto.mx",
    "Credentials "=>"true",
    "exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client","Authorization"),
    "maxAge" => 1728000,
    "allowCredentials" => True,
    "allowMethods" => array("POST, GET"),
    "allowHeaders" => array("Authorization")
);
$cors = new \CorsSlim\CorsSlim($corsOptions);

$app->add($cors);*/
$app->add(function ($request, $response, $next) {
    $headers =apache_request_headers();
    $Auth="";
    foreach($headers as $header=>$value){
        if($header=='Auth'){
            $Auth = $value;
        }
    }
    /////cambiar a getPath()!='sucursal/login'
    if($request->getUri()->getPath()!='usuario/login'
        &&$request->getUri()->getPath()!='sucursal/login'
        &&$request->getUri()->getPath()!='sucursal/lista/conexion'
        &&$request->getUri()->getPath()!='usuario/seleccionarApi'
        &&$Auth!='d93a1e0929c63a72d437ab44bcddcd60'){


        if(usuario::revisarToken($Auth)){
            return $response = $next($request, $response);
        }else{
            $arreglo=["estado"=>"-1","error"=>"no autenticado","apache_request"=>$request->getUri()->getPath()
                ,"token"=>$headers ];
            return $response->withJson($arreglo,401);
        }
    }else{
        return $response = $next($request, $response);
    }
});