<?php
class inventario
{
    protected function __construct()
    {

    }

    public static function seleccionarAzar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
            foreach($postrequest->articulos as $category)
            {
                $new_arr[] = $category;
            }
            if(isset($new_arr)){
            $res_arr = implode(',',$new_arr);
            $comando = "select art_id,clave,descripcion,existencia from articulo a
                      INNER JOIN categoria c on (a.cat_id=c.cat_id_Local)
                      INNER JOIN departamento d on (d.dep_idLocal=c.dep_id)
                     where a.servicio!=1  and a.cat_id like :cat_id and a.idSucursal like :idSucursal and art_id not in (".$res_arr.") and art_id not in (select art_id from ms_inventario where fechaSolicitud>curdate())
                      and a.existencia>0
                     order by rand() limit :sta1;";
            }else{
            $comando = "select art_id,clave,descripcion,existencia from articulo a
                     where a.servicio!=1 and cat_id like :cat_id and idSucursal like :idSucursal
                      and a.existencia>0
                      and art_id not in (select art_id from ms_inventario where fechaSolicitud>curdate())
                     order by rand() limit :sta1;";
        }
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);
            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Azar",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function seleccionarIndividual($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        foreach($postrequest->articulos as $category)
        {
            $new_arr[] = $category;
        }
        if(isset($new_arr)) {
            $res_arr = implode(',', $new_arr);
            //echo var_dump($res_arr);
            $comando = "SELECT art_id,clave,descripcion,existencia FROM articulo a
                     WHERE a.servicio!=1 AND a.existencia>0 and a.clave=:input and a.art_id not in (select art_id from ms_inventario where fechaSolicitud>curdate()) and art_id not in ($res_arr)";
            $comando1 = "SELECT art_id,clave,descripcion,existencia FROM articulo a
                     WHERE a.servicio!=1  and a.existencia>0 and art_id not in (select a.art_id from ms_inventario where fechaSolicitud>curdate()) AND  descripcion LIKE CONCAT('%',:input,'%')and a.art_id not in ($res_arr);";

        }else {
            $comando = "SELECT art_id,clave,descripcion,existencia FROM articulo a
                     WHERE a.servicio!=1 and a.art_id not in (select art_id from ms_inventario where fechaSolicitud>curdate()) AND a.clave=:input;";
            $comando1 = "SELECT art_id,clave,descripcion,existencia FROM articulo a
                     WHERE a.servicio!=1 and a.art_id not in (select art_id from ms_inventario where fechaSolicitud>curdate())  AND descripcion LIKE CONCAT('%',:input,'%');";
        }
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("input", $postrequest->input);
           if($sentencia->execute()){
               $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
               //var_dump($resultado);
           }if($resultado==null){
                //echo ("oo");
                $sentencia1 = $db->prepare($comando1);
                $sentencia1->bindParam("input", $postrequest->input);
                $sentencia1->execute();
                $resultado = $sentencia1->fetchAll(PDO::FETCH_ASSOC);
            }
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Individual",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function seleccionarMasVendidos($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
      //  var_dump($postrequest);
        if(isset($postrequest->articulos)) {
            foreach ($postrequest->articulos as $category) {
                $new_arr[] = $category;

            }  
        }
        if(isset($new_arr)) {
            $res_arr = implode(',', $new_arr);
            //echo var_dump($res_arr);
            $comando = "select * from
                            (select a.art_id,a.clave,a.descripcion,a.existencia from venta v
                                inner join detallev dv on (dv.ven_id = v.ven_id)
                                inner join articulo a on (a.art_id = dv.art_id)
                                inner join categoria c on (c.cat_id = a.cat_id)

                                inner join ms_sucursal ms on (ms.idSucursal =:idSucursal)
                                where
                                v.fecha>=:fechaInicio
                                and v.fecha<=:fechaFin
                                and c.cat_id_Local like :cat_id
                                and a.existencia>0
                                and a.art_id not in ($res_arr)
                                and a.art_id not in (select art_id from ms_inventario where fechaSolicitud>curdate())
                                group by a.art_id
                                order by count(*) desc
                            ) tt
                            limit :sta1;";

        }else {
            $comando = "select * from
                            (select a.art_id,a.clave,a.descripcion,a.existencia from venta v
                                inner join detallev dv on (dv.ven_id = v.ven_id)
                                inner join articulo a on (a.art_id = dv.art_id)
                                inner join categoria c on (c.cat_id = a.cat_id)

                                inner join ms_sucursal ms on (ms.idSucursal =:idSucursal)
                                where
                                v.fecha>=:fechaInicio
                                and v.fecha<=:fechaFin
                                and c.cat_id_Local like :cat_id
                                and a.existencia>0
                                and a.art_id not in (select art_id from ms_inventario where fechaSolicitud>curdate())
                                group by a.art_id
                                order by count(*) desc
                            ) tt
                            limit :sta1;";

        }
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            //$sentencia->bindParam("input", $postrequest->input);
            $sentencia->bindParam("fechaInicio", $postrequest->fechaInicio);
            $sentencia->bindParam("fechaFin", $postrequest->fechaFin);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);

            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
                $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                //var_dump($resultado);

            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Vendido",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function insertar($request, $response, $args)
    {
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
$resultado="";

         //return var_dump($comando);
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            foreach ($postrequest->art_id as $renglon ) {
                $art_id = $renglon;


                $comando = "insert into ms_inventario(idInventario, idInventarioLocal, idSucursal, art_id, existenciaSolicitud, existenciaRespuesta, idUsuario, fechaSolicitud, existenciaEjecucion, idEstado) values (0,0,:idSucursal,:art_id,0,0,:idUsuario,NOW(),3,'A')";


                $sentencia = $db->prepare($comando);
                $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
                $sentencia->bindParam("art_id", $art_id);
                $sentencia->bindParam("idUsuario", $postrequest->idUsuario);

                $resultado = $sentencia->execute();
            }
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200);
            } else {
                $arreglo = [
                    "estado" => 401,
                    "error" => "Error al insertar Registros, asegurese que la lista no este vacia",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo, 401);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al insertar Registro",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }

        finally{
            $db=null;
        }
    }
    public static function seleccionarMasConflictivos($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        //  var_dump($postrequest);
        if(isset($postrequest->articulos)) {
            foreach ($postrequest->articulos as $category) {
                $new_arr[] = $category;

            }
        }
        if(isset($new_arr)) {
            $res_arr = implode(',', $new_arr);
            //echo var_dump($res_arr);
            $comando = "select * from
                            (select a.art_id,a.clave,a.descripcion,a.existencia,count(*) as repetido from
								ms_inventario msi
                                inner join articulo a on (a.art_id = msi.art_id)
                                inner join categoria c on (c.cat_id = a.cat_id)
                                inner join ms_sucursal ms on (ms.idSucursal =:idSucursal)
                                where
                                msi.fechaSolicitud>=:fechaInicio
                                and msi.fechaSolicitud<=:fechaFin
                                and a.existencia>0
                                and msi.existenciaRespuesta!=msi.existenciaEjecucion
                                and c.cat_id like :cat_id
                                and a.art_id not in ($res_arr)
                                and msi.art_id not in (select msi.art_id from ms_inventario msi where fechaSolicitud>curdate() and idEstado='A')
                                group by a.art_id
                               having count(*)>=3
                                order by count(*) desc

                            ) tt
                            limit :sta1;";

        }else {
            $comando = "select * from
                            (select a.art_id,a.clave,a.descripcion,a.existencia,count(*) as repetido from
								ms_inventario msi
                                inner join articulo a on (a.art_id = msi.art_id)
                                inner join categoria c on (c.cat_id = a.cat_id)
                                inner join ms_sucursal ms on (ms.idSucursal =:idSucursal)
                                where
                                msi.fechaSolicitud>=:fechaInicio
                                and msi.fechaSolicitud<=:fechaFin
                                and a.existencia>0
                                and msi.existenciaRespuesta!=msi.existenciaEjecucion
                                and c.cat_id like :cat_id
                                and msi.art_id not in (select msi.art_id from ms_inventario msi where fechaSolicitud>curdate() and idEstado='A')
                                group by a.art_id
                               having count(*)>=3
                                order by count(*) desc

                            ) tt
                            limit :sta1;";

        }
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            //$sentencia->bindParam("input", $postrequest->input);
            $sentencia->bindParam("fechaInicio", $postrequest->fechaInicio);
            $sentencia->bindParam("fechaFin", $postrequest->fechaFin);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);

            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($resultado);

            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Vendido",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
}