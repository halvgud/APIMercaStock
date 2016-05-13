<?php
class nivel_autorizacion
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        $idUsuario = $postrequest->idGenerico;
        $comando = "SELECT idNivelAutorizacion, descripcion FROM ms_nivelAutorizacion WHERE idNivelAutorizacion>(
                    SELECT idNivelAutorizacion FROM ms_usuario WHERE idUsuario=:idUsuario)";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idUsuario", $idUsuario, PDO::PARAM_STR);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "Lista de niveles",
                    "data" => [$resultado]
                ];;
                return $response->withJson($arreglo, 200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Sexo",
                "data" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
    }
}