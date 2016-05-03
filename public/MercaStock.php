<?php
class mercaStock
{
    protected function __construct()
    {

    }

    public static function seleccionarDepartamento ($request,$response,$args){

        $comando = "SELECT * from departamento";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado){
                return $response->withJson($resultado,200);
            }else{
                $arreglo = [
                    "estado" => 400,
                    "error"=>"Error al traer listado de Departamento",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo,400);
            }
        }catch(PDOException $e){
            $arreglo = [
                "estado" => 400,
                "error"=>"Error al traer listado de Departamento",
                "datos" => $e
            ];
            return $response->withJson($arreglo,400);//json_encode($wine);
        }

    }
    public static function seleccionarCategoria ($request,$response,$args){

        $comando = "SELECT * from categoria";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado){
                return $response->withJson($resultado,200);
            }else{
                $arreglo = [
                    "estado" => 400,
                    "error"=>"Error al traer listado de Categoria",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo,400);
            }
        }catch(PDOException $e){
            $arreglo = [
                "estado" => 400,
                "error"=>"Error al traer listado de Categoria",
                "datos" => $e
            ];
            return $response->withJson($arreglo,400);//json_encode($wine);
        }

    }
    public static function seleccionarInventario ($request,$response,$args){

        $comando = "SELECT * from ms_inventario";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado){
                return $response->withJson($resultado,200);
            }else{
                $arreglo = [
                    "estado" => 400,
                    "error"=>"Error al traer listado de Inventario",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo,400);
            }
        }catch(PDOException $e){
            $arreglo = [
                "estado" => 400,
                "error"=>"Error al traer listado de Inventario",
                "datos" => $e
            ];
            return $response->withJson($arreglo,400);//json_encode($wine);
        }

    }
    public static function seleccionarArticulo ($request,$response,$args){

        $comando = "SELECT * from articulo";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado){
                return $response->withJson($resultado,200);
            }else{
                $arreglo = [
                    "estado" => 400,
                    "error"=>"Error al traer listado de Articulos",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo,400);
            }
        }catch(PDOException $e){
            $arreglo = [
                "estado" => 400,
                "error"=>"Error al traer listado de Articulos",
                "datos" => $e
            ];
            return $response->withJson($arreglo,400);//json_encode($wine);
        }

    }
    public static function seleccionarBitacora ($request,$response,$args){
       /* $postrequest = json_decode($request->getBody());
        //return $response->withJson(var_dump($postrequest),400);;
        $fechaI = $postrequest->hora_inicio;
        $fechaF = $postrequest->hora_fin;
        $fechaI = $fechaI.' 00:00:00';
        $fechaF = $fechaF.' 23:59:59';*/
        $comando = "SELECT * from ms_bitacora";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
        //    $sentencia->bindParam("fechaIni",$fechaI);
        //    $sentencia->bindParam("fechaFin",$fechaF);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado){
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "datos" => $resultado[0]
                ];
                return $response->withJson($arreglo,200);
            }else{
                $arreglo = [
                    "estado" => "warning",
                    "success"=>"No se encontraron registros en el rango solicitado",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo,200);
            }
        }catch(PDOException $e){
            $arreglo = [
                "estado" => 400,
                "error"=>"Error al traer la Bitácora",
                "datos" => $e
            ];
            return $response->withJson($arreglo,400);//json_encode($wine);
        }

    }
}
