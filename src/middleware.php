<?php
// Application middleware


$app->add(function ($request, $response, $next) {
    $headers = getallheaders();
    $Auth="";
    if($request->getUri()->getPath()!='usuario/login'){
        foreach ($headers as $header => $value) {
            if($header=='Authorization'){
                $Auth = $value;
         }
       }
        if(usuario::revisarToken($Auth)){
            return $response = $next($request, $response);
        }else{
            return $response->withJson($Auth,401);
        }
    }else{
        return $response = $next($request, $response);
    }
});