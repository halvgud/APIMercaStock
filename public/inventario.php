<?php
class inventario
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $comando = "SELECT * FROM ms_inventario";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                return $response->withJson($resultado, 200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Inventario",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function seleccionarAzar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "select art_id,clave,descripcion,existencia from articulo a
                     where a.servicio!=1 and cat_id=:cat_id and idSucursal=:idSucursal
                     order by rand() limit :sta1;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);
            $sentencia->bindValue('sta1', (int) $postrequest->can, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "success" => "Error al traer listado de Inventario Azar",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
}