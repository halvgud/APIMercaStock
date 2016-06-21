<?php

class categoria
{
    protected function __construct(){
    }

    public static function seleccionar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT cat_id, cat_id_Local,idSucursal,nombre,dep_id
                        FROM categoria
                        WHERE idSucursal LIKE :idSucursal
                          AND dep_id LIKE :dep_id and status=1";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal', $postrequest->idGenerico->idSucursal);
            $sentencia->bindParam('dep_id', $postrequest->idGenerico->dep_id);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" =>  isset($postrequest->dt)?$resultado:[$resultado]
                ];
                $respuesta= $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "No se encontraron registros con los parámetros de búsqueda",
                    "data" => ""
                ];;
                $respuesta= $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),

                "datos" =>"Error al traer listado de Categoria"
            ];
            $respuesta= $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
            return $respuesta;
        }
    }
}
