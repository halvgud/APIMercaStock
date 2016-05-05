<?php
class inventario
{
    protected function __construct()
    {

    }

    public static function seleccionarInventario($request, $response, $args)
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
            return $response->withJson($arreglo, 400);//json_encode($wine);
        }

    }
}