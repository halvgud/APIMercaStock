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
                            return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                        } else {
                            $arreglo =
                                [
                                    "estado" => "warning",
                                    "mensaje" => "",
                                    "data" => $resultado
                                ];
                            return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                          }//else
            }else{
                $arreglo =
                    [
                        "estado" => 400,
                        "error" => "id de Sucursal necesaria",
                        "data" => $postrequest
                    ];
                return $response->withJson($arreglo, 400, JSON_UNESCAPED_UNICODE);
            }
        }catch(PDOException $e){
            $codigoDeError=$e->getCode();
            $error =self::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($postrequest)
            ];
            return $response->withJson($arreglo,400);
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

}