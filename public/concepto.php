<?php

class concepto
{
    protected function __construct(){
    }

    public static function seleccionar($request, $response)
    {
        $comando = "SELECT idConcepto,descripcion,idEstado FROM ms_concepto";
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
                "error" => "Error al traer listado de Concepto",
                "data" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        } finally {
            $db = null;
        }
    }
}