<?php
class departamento
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        if((isset($postrequest->idSucursal)&&$postrequest->idSucursal!='TODOS'&&$postrequest->idSucursal!=null)
            ||(isset($postrequest->idGenerico)&&$postrequest->idGenerico!='TODOS'&&$postrequest->idGenerico!=null)){
            $comando = "SELECT * FROM departamento WHERE idSucursal=:idSucursal";
        }
        else {
            $comando = "SELECT * FROM departamento";
        }
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
                    "data" => isset($postrequest->idGenerico)?[$resultado]:$resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Departamento",
                    "data" => $idSucursal
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