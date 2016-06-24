<?php
// Application middleware


$app->add(function ($request, $response, $next) {
    $headers = apache_request_headers();
    $Auth="";

                                                            /////cambiar a getPath()!='sucursal/login'
    if($request->getUri()->getPath()!='usuario/login'&&$request->getUri()->getPath()!='sucursal/login'){
        foreach ($headers as $header => $value) {
            if($header=='Authorization'){
                $Auth = $value;
         }
       }

        if(usuario::revisarToken($Auth)){

            //$newResponse4 = $response->withHeader("Access-Control-Allow-Headers", "Access-Control-Allow-Headers,Authorization, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

            return $response = $next($request, $response);
        }else{
            $arreglo=["estado"=>"-1","error"=>"no autenticado","apache_request"=>$headers,"phpserver"=>$_SERVER,"getallheaders"=>getallheaders()];
            return $response->withJson($arreglo,401);
        }
    }else{

        $newResponse4 = $response->withHeader("Access-Control-Allow-Headers", "Access-Control-Allow-Headers,Authorization, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

        return $response = $next($request, $newResponse4);
    }
});