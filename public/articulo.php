<?php
class articulo
{
    protected function __construct()
    {

    }

    public static function seleccionarArticulo($request, $response, $args)
    {

        $comando = "SELECT * FROM articulo";
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
                    "error" => "Error al traer listado de Articulos",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Articulos",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);//json_encode($wine);
        }

    }
}
