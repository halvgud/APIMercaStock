<?php
class departamento
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
            $comando = "SELECT * FROM departamento WHERE idSucursal like :idSucursal";

      //  $db=null;
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
        finally{
            $db=null;
        }
    }

}