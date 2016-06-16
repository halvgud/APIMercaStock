<?php
// Application middleware


$app->add(function ($request, $response, $next) {
    $headers = apache_request_headers();
    $Auth="";

                                                            /////cambiar a getPath()!='sucursal/login'
    if($request->getUri()->getPath()!='usuario/login'&&$request->getUri()->getPath()=='sucursal/login'){
        foreach ($headers as $header => $value) {
            if($header=='Authorization'){
                $Auth = $value;
         }
       }

        if(usuario::revisarToken($Auth)){
            return $response = $next($request, $response);
        }else{
            $arreglo=["estado"=>"-1","error"=>"no autenticado","data"=>$headers];
            return $response->withJson($arreglo,401);
        }
    }else{
        return $response = $next($request, $response);
    }
});