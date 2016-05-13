<?php
class categoria
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        if((isset($postrequest->idGenerico[0])&&$postrequest->idGenerico[0]!='TODOS'&&$postrequest->idGenerico[0]!=null)
            ||(isset($postrequest->idGenerico[1])&&$postrequest->idGenerico[1]!='TODOS'&&$postrequest->idGenerico[1]!=null)){
            $comando = "SELECT cat_id, nombre FROM categoria WHERE idSucursal=:idSucursal and dep_id=:dep_id";
        }
        else if($postrequest->idSucursal=='TODOS'){
            $comando = "SELECT cat_id, cat_id_Local, idSucursal, nombre, dep_id FROM categoria";
        }
        else {
            $comando = "SELECT cat_id, cat_id_Local, idSucursal, nombre, dep_id FROM categoria WHERE idSucursal=:idSucursal and dep_id=:dep_id";
        }
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            if(isset($postrequest->idGenerico[0])){
                $sentencia->bindParam('idSucursal', $postrequest->idGenerico[0]);
                $sentencia->bindParam('dep_id', $postrequest->idGenerico[1]);
            }else{
            $sentencia->bindParam('idSucursal', $postrequest->idSucursal);
                $sentencia->bindParam('dep_id', $postrequest->dep_id);
            }

            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" =>  isset($postrequest->idGenerico[0])?[$resultado]:$resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "No se encontraron registros con los parámetros de búsqueda",
                    "data" => ""
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => logIn::traducirMensaje($e->getCode(),$e),
                "datos" =>"Error al traer listado de Categoria"
            ];
            return $response->withJson($arreglo, 400);//json_encode($wine);
        }

    }
    public static function seleccionarCategoria2($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        //var_dump($postrequest->idGenerico1);
        if((isset($postrequest->idGenerico1)&&$postrequest->idGenerico1!='TODOS'&&$postrequest->idGenerico1!=null)
            ||(isset($postrequest->idGenerico2)&&$postrequest->idGenerico2!='TODOS'&&$postrequest->idGenerico2!=null)){
            $comando = "SELECT cat_id, nombre FROM categoria WHERE idSucursal=:idSucursal and dep_id=:dep_id";
        }
        else {
            $comando = "SELECT cat_id, nombre FROM categoria";
        }
        try {
            //$idSucursal=isset($postrequest->idGenerico1)?$postrequest->idGenerico1:(isset($postrequest->idGenerico2)?$postrequest->idGenerico2:0);
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            //$sentencia->bindParam('idSucursal', $postrequest->idSucursal);
            $sentencia->bindParam('idSucursal', $postrequest->idGenerico1);
            $sentencia->bindParam('dep_id', $postrequest->idGenerico2);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" =>  [$resultado]
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "No se encontraron registros con los parámetros de búsqueda",
                    "data" => ""
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => logIn::traducirMensaje($e->getCode(),$e),
                "datos" =>"Error al traer listado de Categoria"
            ];
            return $response->withJson($arreglo, 400);//json_encode($wine);
        }

    }
}