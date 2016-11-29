<?php
class inventarioTemporal
{
    public static function insertar($request, $response)
    {
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        $resultado=null;
        $codigo = 200;
        try {
            $db = getConnection();
            $db->beginTransaction();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $comando = "INSERT INTO ms_inventarioTemporal(art_id, idEstado, fecha, idSucursal)
                                values(:art_id,
                                        'A',
                                        NOW(),
                                        :idSucursal
                                      )";

            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->bindParam("art_id", $postrequest->art_id);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                $db->commit();

            } else {
                $db->rollBack();
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al insertar Registros, asegurese que la lista no este vacia",
                    "datos" => $resultado
                ];
                $codigo=400;
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function seleccionar($request,$response){
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        $resultado=null;
        $codigo = 200;
        $edicion=(isset($postrequest->edicion)?',"'.$postrequest->edicion.'" as '.$postrequest->edicion:'');
        $query = "SELECT distinct msit.idInventarioTemporal,a.clave,a.descripcion,a.art_id,a.existencia $edicion from ms_inventarioTemporal msit
                  inner join articulo a on (a.art_id = msit.art_id and a.idSucursal = msit.idSucursal)
                  where msit.idSucursal=:idSucursal and msit.idEstado='A'";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario temporal",
                    "data" => $resultado
                ];
                $codigo=202;

            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function seleccionarLista($request,$response){
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        $resultado=null;
        $codigo = 200;
        $query = "SELECT distinct a.clave,a.descripcion,a.art_id,a.existencia from ms_inventarioTemporal msit
                  inner join articulo a on (a.art_id = msit.art_id and a.idSucursal = msit.idSucursal)
                  where msit.idSucursal=:idSucursal and msit.idEstado='A'";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Conflictivos",
                    "data" => $resultado
                ];
                $codigo=202;

            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }


}