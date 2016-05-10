<?php
class parametros
{
    protected function __construct()
    {

    }

    public static function seleccionarParametros($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
       // if((isset($postrequest->idSucursal)&&$postrequest->idSucursal!='TODOS'&&$postrequest->idSucursal!=null)){
            $comando = "SELECT idSucursal, accion, parametro, valor, comentario, usuario, fechaActualizacion FROM ms_parametro WHERE idSucursal=:idSucursal";

        //}

        try {
            $idSucursal=$postrequest->idSucursal;
//var_dump($idSucursal);
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            //if($postrequest!='todo') {
            $sentencia->bindParam('idSucursal',$idSucursal );
            //}
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
                //return $response->withJson($resultado, 200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Parametros",
                    "data" => $idSucursal
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Parametros",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }

    }
    public static function actualizarParametros($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        //if($postrequest->password!='DEFAULTMERCASTOCK') {
            $query = "UPDATE ms_parametro SET valor=:valor,comentario=:comentario,usuario=:usuario,fechaActualizacion=NOW() WHERE idSucursal=:idSucursal AND accion=:accion AND parametro=:parametro";
        ////  $query = "UPDATE ms_sucursal SET nombre=:nombre,usuario=:usuario,domicilio=:domicilio,contacto=:contacto,idEstado=:idEstado WHERE idSucursal=:idSucursal";
        //}
//return var_dump($postrequest);
        try {
            $db = getConnection();

            $sentencia = $db->prepare($query);
            $sentencia ->bindParam("idSucursal",$postrequest->idSucursal,PDO::PARAM_INT);
            $sentencia->bindParam("accion", $postrequest->accion,PDO::PARAM_STR);
            $sentencia->bindParam("parametro", $postrequest->parametro,PDO::PARAM_STR);
            $sentencia->bindParam("valor", $postrequest->valor,PDO::PARAM_STR);
            $sentencia->bindParam("comentario", $postrequest->comentario,PDO::PARAM_STR);
            $sentencia->bindParam("usuario", $postrequest->usuario,PDO::PARAM_STR);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado el usuario con éxito",
                        "data" => $resultado
                    ];
                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            } else {
                $arreglo =
                    [
                        "estado" => "warning",
                        "mensaje" => "No se cambió ningún dato",
                        "data" => $resultado
                    ];
                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            $codigoDeError = $e->getCode();
            $error = self::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => $error,
                "data" => json_encode($postrequest)
            ];;
            return $response->withJson($arreglo, 400);//json_encode($wine);
        }
    }

}