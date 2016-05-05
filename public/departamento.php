<?php
class departamento
{
    protected function __construct()
    {

    }

    public static function seleccionarDepartamento($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        //echo($postrequest);
       // console.log($postrequest);
        if($postrequest->sucursal=='TODOS'){
            $comando = "SELECT * FROM departamento";
        }
        else {
            $comando = "SELECT * FROM departamento WHERE idSucursal=:idSucursal";
        }
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            //if($postrequest!='todo') {
                $sentencia->bindParam('idSucursal', $postrequest->sucursal);
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
                    "error" => "Error al traer listado de Departamento",
                    "data" => $postrequest->sucursal
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Departamento",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }

    }
    public static function seleccionarDepartamento2($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        //echo($postrequest);
        // console.log($postrequest);
        if($postrequest->departamento=='TODOS'){
            $comando = "SELECT * FROM departamento";
        }
        else {
            $comando = "SELECT * FROM departamento WHERE idSucursal=:idSucursal AND dep_id=:dep_id";
        }
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            //if($postrequest!='todo') {
            $sentencia->bindParam('idSucursal', $postrequest->sucursal);
            $sentencia->bindParam('dep_id', $postrequest->departamento);
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
                    "error" => "Error al traer listado de Departamento",
                    "data" => $postrequest->sucursal
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Departamento",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }

    }
}