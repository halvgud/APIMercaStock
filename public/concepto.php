<?php

class concepto
{
    protected function __construct(){
    }

    public static function seleccionar($request, $response)
    {
        $comando = "SELECT idConcepto,descripcion,idEstado FROM ms_concepto where idEstado='1'";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200,JSON_UNESCAPED_UNICODE);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Concepto",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        } finally {
            $db = null;
        }
    }
    public static function seleccionarConceptoReporte($request, $response)
    {
        $comando = "SELECT parametro as concepto,valor as idConcepto from ms_parametro where accion='CONCEPTO_REPORTE_INVENTARIO'
                     order by cast(valor as unsigned)";
        $codigo=400;
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->execute();

            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                $codigo=200;
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Concepto",
                    "data" => $resultado
                ];
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => $e->getMessage()
            ];
        } finally {
            $db = null;
            return $response->withJson($arreglo, $codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function seleccionarConceptoComparativo($request,$response){
        $comando = "SELECT parametro as concepto,valor as idConcepto from ms_parametro where accion='CONCEPTO_REPORTE_COMPARATIVO'
                     order by cast(valor as unsigned)";
        $codigo=400;
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->execute();

            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                $codigo=200;
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Concepto",
                    "data" => $resultado
                ];
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => $e->getMessage()
            ];
        } finally {
            $db = null;
            return $response->withJson($arreglo, $codigo,JSON_UNESCAPED_UNICODE);
        }
    }
}