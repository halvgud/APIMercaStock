<?php

class proveedor
{
    protected function __construct(){
    }

    public static function seleccionar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT pro_id,nombre,representante,telefono,rfc FROM proveedor WHERE idSucursal like :idSucursal and status='1'";
        $arreglo=null;
        $codigo = 200;
        try {
            $idSucursal=isset($postrequest->idSucursal)?$postrequest->idSucursal:(isset($postrequest->idGenerico)?$postrequest->idGenerico:0);
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$idSucursal );
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => isset($postrequest->dt)?$resultado:[$resultado]
                ];
                $codigo=200;
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Departamento",
                    "data" => $idSucursal
                ];
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

}