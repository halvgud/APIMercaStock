<?php
class inventario
{
    protected function __construct(){
    }

    public static function seleccionarAzar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo = null;
        $codigo = 200;
        foreach($postrequest->articulos as $category){
            $new_arr[] = $category;
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
            $comando = "SELECT art_id,
                               clave,
                               descripcion,
                               existencia
                        FROM articulo a
                        INNER JOIN categoria c ON (a.cat_id=c.cat_id
                                                   AND c.idSucursal=:idSucursal)
                        INNER JOIN departamento d ON (d.dep_id=c.dep_id
                                                      AND d.idSucursal=:idSucursal)
                        WHERE a.servicio!=1
                          AND a.cat_id LIKE :cat_id
                          AND d.dep_id LIKE :dep_id
                          AND a.idSucursal LIKE :idSucursal
                          AND art_id NOT IN (".$res_arr.")
                          AND art_id NOT IN
                            (SELECT art_id
                             FROM ms_inventario
                             WHERE fechaSolicitud>curdate()-7
                               AND idEstado NOT IN ('E',
                                                    'A')
                             union all
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                          AND a.art_id NOT IN
                            ( SELECT msp33.valor
                             FROM ms_parametro msp11
                             INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion
                                                               AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                             INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion
                                                               AND msp33.parametro=msp11.valor)
                             WHERE msp11.accion = 'CONFIG_GENERAR_INVENTARIO'
                               AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                               AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE'
                               AND msp11.comentario='TRUE'
                               AND msp33.parametro=:idSucursal)
                          AND a.idSucursal=:idSucursal
                          and a.status=1
                        ORDER BY rand() LIMIT :sta1;";

        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);
            $sentencia->bindParam("dep_id", $postrequest->dep_id);
            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Azar",
                    "data" => $resultado
                ];;
                $codigo =202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo,$codigo);
        }
    }

    public static function seleccionarIndividual($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
        foreach($postrequest->articulos as $category)
        {
            $new_arr[] = $category;
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
            $comando = "SELECT art_id,
                               clave,
                               descripcion,
                               existencia
                        FROM articulo a
                        WHERE a.servicio!=1

                          AND (a.clave=:input
                               OR descripcion LIKE CONCAT('%',:input,'%'))
                          AND a.art_id NOT IN
                            (
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                          AND art_id NOT IN ($res_arr)
                          AND a.art_id NOT IN
                            ( SELECT msp33.valor
                             FROM ms_parametro msp11
                             INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion
                                                               AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                             INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion
                                                               AND msp33.parametro=msp11.valor)
                             INNER JOIN articulo a ON (a.art_id = msp33.valor)
                             WHERE msp11.accion = 'CONFIG_GENERAR_INVENTARIO'
                               AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                               AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE'
                               AND msp11.comentario='TRUE')
                               and a.status=1
                          AND a.idSucursal=:idSucursal";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);

            $sentencia->bindParam("input", $postrequest->input);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Individual",
                    "data" => $postrequest
                ];
                $codigo=202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function seleccionarMasVendidos($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo = null;
        $codigo = 200;
        if(isset($postrequest->articulos)) {
            foreach ($postrequest->articulos as $category) {
                $new_arr[] = $category;
            }
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
            $comando = "select a.art_id,a.clave,a.descripcion,a.existencia from venta v
                                inner join detallev dv on (dv.ven_id = v.ven_id)
                                inner join articulo a on (a.art_id = dv.art_id)
                                inner join categoria c on (c.cat_id = a.cat_id)
                      INNER JOIN departamento d on (d.dep_id=c.dep_id)
                                inner join ms_sucursal ms on (ms.idSucursal =:idSucursal)
                                where
                                v.fecha>=:fechaInicio
                                and v.fecha<=:fechaFin
                                and c.cat_id like :cat_id

                                and a.art_id not in ($res_arr)
                                and a.art_id not in
                                (SELECT art_id
                             FROM ms_inventario
                             WHERE fechaSolicitud>curdate()-7
                               AND idEstado NOT IN ('E',
                                                    'A')
                             union all
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                                 and a.art_id NOT IN (
                            SELECT msp33.valor FROM ms_parametro msp11

                        INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                        INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion AND msp33.parametro=msp11.valor)
                        INNER JOIN articulo a ON (a.art_id = msp33.valor)
                        WHERE
                        msp11.accion = 'CONFIG_GENERAR_INVENTARIO' AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                        AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE'
                        and msp11.comentario='TRUE')
                        and d.dep_id like :dep_id
                        and a.status=1
                                group by a.art_id
                                order by count(*) desc
                            limit :sta1;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("fechaInicio", $postrequest->fechaInicio);
            $sentencia->bindParam("fechaFin", $postrequest->fechaFin);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);
            $sentencia->bindParam("dep_id",$postrequest->dep_id);
            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Vendido",
                    "data" => $resultado
                ];;
                $codigo =202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo = 400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function insertar($request, $response)
    {
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        $resultado=null;
        $codigo = 200;
        try {
            $db = getConnection();
            $db->beginTransaction();

            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $comandoNombre = "insert into ms_inventario_etiqueta(nombre) values(:nombreInventario)";
            $db->prepare($comandoNombre);
            $sentenciaNombre = $db->prepare($comandoNombre);
            $sentenciaNombre->bindParam('nombreInventario',$postrequest->nombreInventario);
            $sentenciaNombre->execute();

            $comando = "INSERT INTO ms_inventario(idInventario, idInventarioLocal, idSucursal, art_id,
                existenciaSolicitud, existenciaRespuesta, idUsuario, fechaSolicitud, existenciaEjecucion, idEstado,
                idInventarioExterno)
                            VALUES (0,
                                    0,
                                    :idSucursal,
                                    :art_id,
                                    0,
                                    0,
                                    :idUsuario,
                                    NOW(),
                                    0,
                                    'A',
                                    (select max(mse.idInventarioExterno) from ms_inventario_etiqueta mse))";
            foreach ($postrequest->art_id as $renglon ) {
                $art_id = $renglon;



                $sentencia = $db->prepare($comando);
                $sentencia->bindParam('idSucursal', $postrequest->idSucursal);
                $sentencia->bindParam('art_id', $art_id);
                $sentencia->bindParam('idUsuario', $postrequest->idUsuario);
                $resultado = $sentencia->execute();
                self::removerDeListaTemporal($art_id,$postrequest->idSucursal);
            }
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                $db->commit();

            } else {
                $db->rollBack();
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al insertar Registros, asegurese que la lista no este vacia",
                    "datos" => $resultado
                ];
                $codigo=400;
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    private static function removerDeListaTemporal($art_id,$idSucursal){
        $arreglo=null;
        $comando = "update ms_inventarioTemporal set idEstado='P' where art_id=:art_id and idSucursal=:idSucursal
                    and idEstado='A'";
        $db=null;
        try{
            $db = getConnection();
            $db->beginTransaction();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal",$idSucursal);
            $sentencia->bindParam("art_id", $art_id);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $db->commit();

            } else {
                $db->rollBack();
            }
        }catch(Exception $e){
            $db->rollBack();
        }
    }

    public static function seleccionarMasConflictivos($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo=200;
        if(isset($postrequest->articulos)) {
            foreach ($postrequest->articulos as $category) {
                $new_arr[] = $category;
            }
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
            $comando = "select a.art_id,a.clave,a.descripcion,a.existencia,count(*) as repetido from
                                ms_inventario msi
                                inner join articulo a on (a.art_id = msi.art_id)
                                inner join categoria c on (c.cat_id = a.cat_id)
                                inner join ms_sucursal ms on (ms.idSucursal =:idSucursal)
                                INNER JOIN departamento d on (d.dep_id=c.dep_id)
                                where
                                msi.fechaSolicitud>=:fechaInicio
                                and msi.fechaSolicitud<=:fechaFin

                                and msi.existenciaRespuesta!=msi.existenciaEjecucion
                                and c.cat_id like :cat_id
                                and a.art_id not in ($res_arr)
                                and msi.art_id not in
                                (SELECT art_id
                             FROM ms_inventario
                             WHERE fechaSolicitud>curdate()-7
                               AND idEstado NOT IN ('E',
                                                    'A')
                             union all
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                                 and a.art_id NOT IN (
                            SELECT msp33.valor FROM ms_parametro msp11

                        INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                        INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion AND msp33.parametro=msp11.valor)
                        INNER JOIN articulo a ON (a.art_id = msp33.valor)
                        WHERE
                        msp11.accion = 'CONFIG_GENERAR_INVENTARIO' AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                        AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE')
                        and d.dep_id like :dep_id
                        and a.status=1
                                group by a.art_id
                               having count(*)>=3
                                order by count(*) desc
                            limit :sta1;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("fechaInicio", $postrequest->fechaInicio);
            $sentencia->bindParam("fechaFin", $postrequest->fechaFin);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);
            $sentencia->bindParam("dep_id",$postrequest->dep_id);
            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                $codigo=200;
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Conflictivos",
                    "data" => $resultado
                ];
                $codigo=202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function seleccionarPorProveedor($request,$response){
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
        foreach($postrequest->articulos as $category)
        {
            $new_arr[] = $category;
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
        $comando = "SELECT art_id,
                               clave,
                               descripcion,
                               existencia
                        FROM articulo a
                        WHERE a.servicio!=1
                          AND a.art_id in
                          (
                            select a.art_id from articulo as a
                            inner join proveedorarticulo pa on (pa.art_id=a.art_id)
                            inner join proveedor p on (p.pro_id = pa.pro_id)
                            where pa.fecha = (
                            select max(pa1.fecha) from articulo as ab
                                                  inner join proveedorarticulo pa1 on (pa1.art_id = ab.art_id)
                                                  inner join proveedor p1 on (p1.pro_id = pa1.pro_id)
                                                  where a.art_id = ab.art_id
                          ) and p.pro_id=:pro_id
                              group by a.art_id
                          )
                          AND a.art_id NOT IN
                            (
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                          AND art_id NOT IN ($res_arr)
                          AND a.art_id NOT IN
                            ( SELECT msp33.valor
                             FROM ms_parametro msp11
                             INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion
                                                               AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                             INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion
                                                               AND msp33.parametro=msp11.valor)
                             INNER JOIN articulo a ON (a.art_id = msp33.valor)
                             WHERE msp11.accion = 'CONFIG_GENERAR_INVENTARIO'
                               AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                               AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE'
                               AND msp11.comentario='TRUE')
                               and a.status=1
                          AND a.idSucursal=:idSucursal";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("pro_id", $postrequest->pro_id);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->execute();
            $resultado=$sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Individual",
                    "data" => $postrequest
                ];
                $codigo=202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function reporteCabecero($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
        if(isset($postrequest->concepto)){
            switch($postrequest->concepto){
                case 1:
                    $comando = "select msi.idSucursal,
                             msie.nombre nombre,msie.idInventarioExterno, date(msi.fechaSolicitud) as fecha, count(1) total,
							sum(case when existenciaRespuesta=existenciaEjecucion and msi.idEstado!='E'
                            then '1' else 0 end) as totalAcertado,
                            sum(case when existenciaRespuesta!=existenciaEjecucion and msi.idEstado!='E'
                            then '1' else 0 end) as totalFallado,
                            sum(case when msi.idEstado = 'E' then 1 else 0 end) as totalRestante,
                            round(sum(case when existenciaRespuesta < existenciaEjecucion then
                                    (precioCompra/factor)*(existenciaRespuesta-existenciaEjecucion) else 0 end),2) as costo,
							round((sum(case when existenciaEjecucion>=existenciaRespuesta then existenciaRespuesta/existenciaEjecucion
            else existenciaEjecucion/existenciaRespuesta end)/count(*)),2) as bandera,1 as detalle,1 as cancelar
            	             from
                            ms_inventario msi
                            left join ms_inventario_etiqueta msie on (msie.idInventarioExterno=msi.idInventarioExterno)
                            inner join ms_sucursal mss on (mss.idSucursal = msi.idSucursal and mss.idSucursal=:idSucursal)
                            INNER JOIN articulo a ON (a.art_id = msi.art_id
                                              AND a.idSucursal = msi.idSucursal and a.status=1)
                            LEFT JOIN ms_parametro msp on (msp.accion='TITULO_INVENTARIO' and date(msp.parametro)=date(msi.fechaSolicitud)
                                                           and msp.valor=msi.idSucursal)
                            where date(msi.fechaSolicitud)>=date(:fechaIni)
                            and date(msi.fechaSolicitud)<=date(:fechaFin)
                            and msi.idEstado!='I'
                            group by msie.idInventarioExterno
                            order by fecha,a.art_id";
                    break;
                case 2:
                    $comando = "select d.dep_id as idSucursal,
                                d.nombre nombre,msie.idInventarioExterno,date(msi.fechaSolicitud) as fecha,count(1) as total,
                                sum(case when existenciaRespuesta=existenciaEjecucion and msi.idEstado!='E'
                                then '1' ELSE 0 end) as totalAcertado,
                                sum(case when existenciaRespuesta!=existenciaEjecucion and msi.idEstado!='E'
                                then '1' else 0 end) as totalFallado,
                                sum(case when msi.idEstado = 'E' then 1 else 0 end) as totalRestante,
                                round(sum(case when existenciaRespuesta < existenciaEjecucion then
                                          (precioCompra/factor)*(existenciaRespuesta-existenciaEjecucion) else 0 end),2) as costo,
                                round((sum(case when existenciaEjecucion>=existenciaRespuesta then existenciaRespuesta/existenciaEjecucion
                                 else existenciaEjecucion/existenciaRespuesta end)/count(*)),2) as bandera,1 as detalle,1 as cancelar
                                 from
                                 ms_inventario msi
                                 left join ms_inventario_etiqueta msie on (msie.idInventarioExterno = msi.idInventarioExterno)
                                 inner join ms_sucursal mss on (mss.idSucursal = msi.idSucursal and mss.idSucursal =:idSucursal)
                                 inner join articulo a on (a.art_id = msi.art_id
                                                            and a.idSucursal=msi.idSucursal and a.status=1)
                                 inner join categoria c on (c.cat_id = a.cat_id)
                                 inner join departamento d on (d.dep_id = c.dep_id)
                                 where date(msi.fechaSolicitud)>=date(:fechaIni)
                                 and date(msi.fechaSolicitud)<=date(:fechaFin)
                                 and msi.idEstado!='I'
                                 group by d.dep_id
                                 order by fecha,a.art_id";
                    break;
                case 3:
                    break;
                default:
                    throw new Exception("Error al recibir los parametros");
            }
        }else{
            throw new Exception("Error al recibir los parametros");
        }
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $fechaIni=trim($postrequest->fechaInicio);
            $fechaFin=trim($postrequest->fechaFin);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("fechaIni",$fechaIni);
            $sentencia->bindParam("fechaFin",$fechaFin);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];

            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventarios  con los parámetros solicitados",
                    "data" => $resultado
                ];
                $codigo=202;
            }
        } catch (Exception $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function reporteDetalle($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo=200;
        $comando="";
        if(isset($postrequest->idConcepto)) {
            switch ($postrequest->idConcepto) {
                case 1:
                    $comando = "SELECT a.art_id,
                           msi.fechaRespuesta as fechaSolicitud,
                           d.dep_id as idDepartamento,
                           d.nombre as departamento,
                           a.clave,
                           a.descripcion,
                           msi.existenciaSolicitud,/*cuantos habia*/
                           msi.existenciaEjecucion,/*cuantos hubo*/
                           a.existencia,
                           msi.existenciaRespuesta,/*cuantos hay*/
                           round((msi.existenciaRespuesta-msi.existenciaEjecucion),2) AS diferencia,
                           round((CASE
                              WHEN msi.existenciaEjecucion>=msi.existenciaRespuesta THEN
                              msi.existenciaRespuesta/msi.existenciaEjecucion ELSE
                              msi.existenciaEjecucion/msi.existenciaRespuesta
                            END),2) AS bandera,
                           round((precioCompra/factor)*(msi.existenciaRespuesta-msi.existenciaEjecucion),2) as costo,
                           CASE WHEN msit.art_id is not null
                                 then 'YA ESTA EN LISTA DE ESPERA'
                                 WHEN msi2.art_id is not null
                                 then 'ACTUALMENTE EN ESPERA'
                                   else 'edicion'
                                  end as edicion
                    FROM ms_inventario msi
                    INNER JOIN articulo a ON (a.art_id = msi.art_id
                                              AND a.idSucursal = msi.idSucursal and a.status=1)
                    inner join categoria c on (c.cat_id = a.cat_id)
                    inner join departamento d on (d.dep_id = c.dep_id)
                    LEFT JOIN ms_inventarioTemporal msit on (a.art_id = msit.art_id and msit.idSucursal = msi.idSucursal
                    and msit.idEstado='A')
                    left join ms_inventario msi2 on (msi2.art_id = msi.art_id and msi2.idSucursal=msi.idSucursal and msi2.idEstado in ('E','A'))
                    WHERE msi.idSucursal =:idSucursal
                      AND date(msi.fechaSolicitud)=:fecha
                      and d.dep_id like :busqueda
                      and msi.idEstado!='I'
                    GROUP BY msi.idSucursal,
                             a.art_id
                             order by d.nombre desc";
                    break;
                case 2:
                    $comando="SELECT a.art_id,
                           msi.fechaRespuesta as fechaSolicitud,
                           c.cat_id as idDepartamento,
                           c.nombre as departamento,
                           a.clave,
                           a.descripcion,
                           msi.existenciaSolicitud,/*cuantos habia*/
                           msi.existenciaEjecucion,/*cuantos hubo*/
                           a.existencia,
                           msi.existenciaRespuesta,/*cuantos hay*/
                           round((msi.existenciaRespuesta-msi.existenciaEjecucion),2) AS diferencia,
                           round((CASE
                              WHEN msi.existenciaEjecucion>=msi.existenciaRespuesta THEN
                              msi.existenciaRespuesta/msi.existenciaEjecucion ELSE
                              msi.existenciaEjecucion/msi.existenciaRespuesta
                            END),2) AS bandera,
                           round((precioCompra/factor)*(msi.existenciaRespuesta-msi.existenciaEjecucion),2) as costo,
                           CASE WHEN msit.art_id is not null
                                 then 'YA ESTA EN LISTA DE ESPERA'
                                 WHEN msi2.art_id is not null
                                 then 'ACTUALMENTE EN ESPERA'
                                   else 'edicion'
                                  end as edicion
                    FROM ms_inventario msi
                    INNER JOIN articulo a ON (a.art_id = msi.art_id
                                              AND a.idSucursal = msi.idSucursal and a.status=1)
                    inner join categoria c on (c.cat_id = a.cat_id)
                    inner join departamento d on (d.dep_id = c.dep_id)
                    LEFT JOIN ms_inventarioTemporal msit on (a.art_id = msit.art_id and msit.idSucursal = msi.idSucursal
                    and msit.idEstado='A')
                    left join ms_inventario msi2 on (msi2.art_id = msi.art_id and msi2.idSucursal=msi.idSucursal and msi2.idEstado in ('E','A'))
                    WHERE msi.idSucursal =:idSucursal
                     and date(msi.fechaSolicitud)>=date(:fechaIni)
                            and date(msi.fechaSolicitud)<=date(:fechaFin)
                      and d.dep_id like :busqueda
                      and msi.idEstado!='I'
                    GROUP BY msi.idSucursal,
                             a.art_id,
                             msi.idInventario
                             order by d.nombre desc";
                    break;
                default:
                throw new Exception("Error al recibir los parametross");
            }
        }else{
            throw new Exception("Error al recibir los parametros");
        }

        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            switch($postrequest->idConcepto){
                case 1:
                    $sentencia->bindParam("busqueda", $postrequest->busqueda);
                    $sentencia->bindParam("fecha", $postrequest->fecha);
                    $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
                    break;
                case 2:
                    $sentencia->bindParam("busqueda", $postrequest->busqueda);
                    $sentencia->bindParam("fechaIni", $postrequest->fechaIni);
                    $sentencia->bindParam("fechaFin", $postrequest->fechaFin);
                    $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
                    break;
                case 3:
                    break;
                default:

            }

            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer el detalle de inventario",
                    "data" => $postrequest
                ];
                $codigo=202;

            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function reporteActual($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
        $comando = "
                    SELECT  date(mi.fechaSolicitud) as fecha,
                            d.nombre as departamento,
                            a.descripcion as nombre,
                            mi.existenciaRespuesta as existenciareal,
                            mi.existenciaEjecucion as existenciasistema,
                            mp.valor as estado
                            from
                            ms_inventario mi
                            inner join articulo a on (mi.art_id = a.art_id)
                            inner join categoria c on (c.cat_id = a.cat_id)
                            inner join departamento d on (d.dep_id = c.dep_id)
                            inner join ms_parametro mp on (mp.parametro = mi.idEstado and mp.accion = 'RELACION_ID_ESTADO')
                            and mi.idEstado='E'
        ";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Conflictivos",
                    "data" => $resultado
                ];
                $codigo=202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }


    public static function actualizarNombre($request,$response){
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
        $comando = "update ms_inventario_etiqueta set nombre=:nombre where idInventarioExterno=:idInventarioExterno";
        $db=null;
        try{
            $db = getConnection();
            $db->beginTransaction();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("nombre",$postrequest->nombre);
            $sentencia->bindParam("idInventarioExterno", $postrequest->idInventarioExterno);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado el nombre con éxito",
                        "data" => $resultado
                    ];
                $db->commit();
            } else {
                $db->rollBack();
                $codigo=202;
                $arreglo =
                    [
                        "estado" => "warning",
                        "error" => "No se cambió ningún dato",
                        "data" => $resultado
                    ];
            }
        } catch (PDOException $e) {
            $codigo=400;
            $db->rollBack();
            $codigoDeError = $e->getCode();
            $error = self::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => $error,
                "data" => json_encode($postrequest)
            ];
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo,JSON_UNESCAPED_UNICODE);
        }
    }

    public static function cancelarOrden($request,$response){
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo=200;
        $comando ="UPDATE ms_inventario
                    set idEstado='I'
                    where idEstado in ('E','A')
                    and date(fechaSolicitud)=date(:fechaSolicitud)
                    and idSucursal=:idSucursal";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("fechaSolicitud", $postrequest->fechaSolicitud);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            if ($sentencia->execute()) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"Se a cancelado la petición de los productos",
                    "data" => ""
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Individual",
                    "data" => ""
                ];
                $codigo=202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

}