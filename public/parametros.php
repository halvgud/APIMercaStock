<?php
class parametros
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT idSucursal, accion, parametro, valor, comentario, usuario, fechaActualizacion FROM ms_parametro WHERE idSucursal=:idSucursal";

        try {
            $idSucursal=$postrequest->idSucursal;
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$idSucursal );
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
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
                    "error" => "Error al traer listado de Parametros",
                    "data" => $idSucursal
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Parametros",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function seleccionarListaFija($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "select art_id, clave, descripcion from articulo where art_id in (select msp3.valor as art_id from ms_parametro msp1
                        inner join ms_parametro msp2 on (msp2.accion=msp1.accion and msp2.parametro='_COMPONENTE_LISTADO')
                        inner join ms_parametro msp3 on (msp2.valor=msp3.accion and msp3.parametro=msp1.valor)
                        where
                            msp1.accion='CONFIG_GENERAR_INVENTARIO' and
                            msp1.valor=:idSucursal);";

        try {
            $idSucursal=$postrequest->idSucursal;
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$idSucursal );
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
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
                    "error" => "Error al traer listado de Parametros",
                    "data" => $idSucursal
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Parametros",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function actualizar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        $query = "UPDATE ms_parametro SET valor=:valor,comentario=:comentario,usuario=:usuario,fechaActualizacion=NOW() WHERE idSucursal=:idSucursal AND accion=:accion AND parametro=:parametro";
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
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }

}