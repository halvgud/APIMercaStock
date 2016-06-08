<?php
class exportar
{
    protected function __construct()
    {

    }
    public static function ultimaVenta($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "select max(ven_idLocal) as ven_id from venta where idSucursal=:idSucursal";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            if(isset($postrequest->idSucursal)){
                $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
                $sentencia -> execute();
                $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
                if($sentencia){
                     $arreglo =
                                [
                                    "estado" => 200,
                                    "success" => "maxima id de venta",
                                    "data" => $resultado
                                ];
                            $codigo=200;
                        } else {
                            $arreglo =
                                [
                                    "estado" => "warning",
                                    "mensaje" => "",
                                    "data" => $resultado
                                ];
                            $codigo=200;
                          }//else
            }else{
                $arreglo =
                    [
                        "estado" => 400,
                        "error" => "id de Sucursal necesaria",
                        "data" => $postrequest
                    ];
                $codigo=400;
            }
        }catch(PDOException $e){
            $codigoDeError=$e->getCode();
            $error =self::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($postrequest)
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo,$codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function Parametro($request,$response){
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
               $codigo=200;
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Parametros",
                    "data" => $idSucursal
                ];;
                $codigo=400;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Parametros",
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }
    public static function exportarInventarioAPI($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT a.cat_id,ms.idInventario, ms.idInventarioLocal, ms.idSucursal, ms.art_id, ms.existenciaSolicitud, ms.existenciaRespuesta, ms.idUsuario, ms.fechaSolicitud, ms.fechaRespuesta, ms.existenciaEjecucion, ms.idEstado FROM ms_inventario ms
                          inner join articulo a on(a.art_id = ms.art_id)
                          WHERE ms.idEstado='A'
                          AND ms.idSucursal=:idSucursal;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($resultado);
            if($resultado){
                $comando2 = "UPDATE ms_inventario SET idEstado='E' WHERE idEstado='A' AND idSucursal=:idSucursal;";

                $sentencia2 = $db->prepare($comando2);
                $sentencia2->bindParam("idSucursal", $postrequest->idSucursal);
                $sentencia2->execute();
                $resultado2 = $sentencia2->rowCount();
                if ($resultado2>0) {
                    $arreglo = [
                        "estado" => 200,
                        "success"=>"OK",
                        "data" => $resultado
                    ];
                    $codigo=200;
                } else {
                    $arreglo = [
                        "estado" => 'warning',
                        "success" => "Error al cambiar Estado",
                        "data" => $resultado2
                    ];
                    $codigo=200;
                }
            }
            else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al exportar Inventario",
                    "data" => $resultado
                ];
                $codigo=200;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo,$codigo,JSON_UNESCAPED_UNICODE);
        }
    }

}