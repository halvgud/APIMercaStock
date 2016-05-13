<?php
class articulo
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        if($postrequest->dep_id!='TODOS'){
        $comando = "SELECT a.clave, a.descripcion, a.margen1, a.precio1, a.existencia  FROM articulo a inner join categoria c on (c.cat_id=a.cat_id) inner join departamento dp on (dp.dep_id=c.dep_id ) where a.idSucursal=:idSucursal and dp.dep_id=:dep_id and c.cat_id=:cat_id ";
        }else{
            $comando = "SELECT a.clave, a.descripcion, a.margen1, a.precio1, a.existencia  FROM articulo a inner join categoria c on (c.cat_id=a.cat_id)  where a.idSucursal=:idSucursal and c.cat_id=:cat_id ";

        }
            try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$postrequest->idSucursal );
                if($postrequest->dep_id!='TODOS') {
                    $sentencia->bindParam('dep_id', $postrequest->dep_id);
                }
            $sentencia->bindParam('cat_id',$postrequest->cat_id );
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" =>$resultado];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "No se encontraron artículos con los parámetros de búsqueda",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Articulos",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
}
