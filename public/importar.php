<?php
class importar
{
    protected function __construct()
    {

    }

    public static function Departamento($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        try {
            $db = getConnection();
            $db->beginTransaction();
        foreach ($postrequest->data as $renglon ) {
            $dep_idLocal=$renglon->dep_idLocal;
            $idSucursal = $renglon->idSucursal;
            $nombre = $renglon->nombre;
            $restringido = $renglon->restringido;
            $porcentaje = $renglon->porcentaje;
            $status = $renglon->status;

            $comandoUpdate="insert into departamento(dep_idLocal,idSucursal,nombre,restringido,porcentaje,status)
                            values (:dep_id, :idSucursal, :nombre, :restringido, :porcentaje, :status) on
                            duplicate key update idSucursal=:idSucursal,nombre=:nombre,restringido=:restringido,porcentaje=:porcentaje,status=:status";

            $sentencia = $db->prepare($comandoUpdate);

            $sentencia->bindParam('dep_id', $dep_idLocal);
            $sentencia->bindParam('idSucursal', $idSucursal);
            $sentencia->bindParam('nombre', $nombre);
            $sentencia->bindParam('status', $status);
            $sentencia->bindParam('porcentaje',$porcentaje);
            $sentencia->bindParam('restringido',$restringido);
            $sentencia->execute();
            $comandoauto="ALTER TABLE departamento AUTO_INCREMENT = 1";
            $sentencia2=$db->prepare($comandoauto);
            $sentencia2->execute();
        }
            $arreglo = [
                "estado" => 200,
                "success" => "Se a importado la informacion",
                "datos" => $db->rowCount()
            ];
        $db->commit();
        return $response->withJson($arreglo, 200);
        } catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => "Error al Insertar o Actualizar un registro",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function Categoria($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;

        try {
            $db = getConnection();
            $db->beginTransaction();
        foreach ($postrequest->data as $renglon ) {
            $cat_id_Local=$renglon->cat_id_Local;
            $idSucursal = $renglon->idSucursal;
            $nombre= $renglon->nombre;
            $status = $renglon->status;
            $dep_id = $renglon->dep_id;

            $comandoUpdate="insert into categoria(cat_id_Local,idSucursal,nombre,status,dep_id)
                            values (:cat_id, :idSucursal, :nombre, :status, :dep_id) on
                            duplicate key update idSucursal=:idSucursal,nombre=:nombre,status=:status,dep_id=:dep_id;";

            $sentencia = $db->prepare($comandoUpdate);

            $sentencia->bindParam('cat_id', $cat_id_Local);
            $sentencia->bindParam('idSucursal', $idSucursal);
            $sentencia->bindParam('nombre', $nombre);
            $sentencia->bindParam('status', $status);
            $sentencia->bindParam('dep_id',$dep_id);
            $sentencia->execute();
            $comandoauto="ALTER TABLE categoria AUTO_INCREMENT = 1";
            $sentencia2=$db->prepare($comandoauto);
            $sentencia2->execute();
        }
            $arreglo = [
                "estado" => 200,
                "success" => "Se a importado la informacion",
                "datos" => $db->rowCount()
            ];
        $db->commit();
        return $response->withJson($arreglo, 200);
    } catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => "Error al Insertar o Actualizar un registro",
                "datos" => $e->getMessage()
            ];

            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function Articulo($request, $response, $args)
    {
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        $contador=0;
        try {

            $db = getConnection();
            $db->beginTransaction();
            var_dump($postrequest);
            return "";
            foreach ($postrequest[0]->data as $renglon) {
                $contador++;
                $art_idLocal = $renglon->art_idLocal!=''?$renglon->art_idLocal:null;
                $idSucursal = $renglon->idSucursal;
                $clave = $renglon->clave!=''?$renglon->clave:null;
                $claveAlterna = $renglon->claveAlterna;
                $descripcion = $renglon->descripcion!=''?$renglon->descripcion:null;
                $servicio = $renglon->servicio;
                $invMin = $renglon->invMin;
                $invMax = $renglon->invMax;
                $factor = $renglon->factor;
                $precioCompra = $renglon->precioCompra;
                $precioCompraProm = $renglon->precioCompraProm;
                $margen1 = $renglon->margen1;
                $precio1 = $renglon->precio1;
                $existencia = $renglon->existencia;
                $lote = $renglon->lote;
                $receta = $renglon->receta;
                $granel = $renglon->granel;
                $tipo = $renglon->tipo;
                $status = $renglon->status;
                $unidadCompra = $renglon->unidadCompra;
                $unidadVenta = $renglon->unidadVenta;
                $cat_id = $renglon->cat_id;
                $srp_id = $renglon->srp_id;


                $comandoUpdate = "INSERT INTO articulo(art_idLocal, idSucursal, clave, claveAlterna, descripcion, servicio, invMin, invMax, factor, precioCompra, precioCompraProm, margen1, precio1, existencia, lote, receta, granel, tipo, status, unidadCompra, unidadVenta, cat_id, srp_id)
                            VALUES (:art_id, :idSucursal, :clave, :claveAlterna, :descripcion, :servicio, :invMin, :invMax, :factor, :precioCompra, :precioCompraProm, :margen1, :precio1, :existencia, :lote, :receta, :granel, :tipo, :status, :unidadCompra, :unidadVenta, :cat_id, :srp_id) ON
                            DUPLICATE KEY UPDATE idSucursal=:idSucursal, clave=:clave, claveAlterna=:claveAlterna, descripcion=:descripcion, servicio=:servicio, invMin=:invMin, invMax=:invMax, factor=:factor, precioCompra=:precioCompra, precioCompraProm=:precioCompraProm, margen1=:margen1, precio1=:precio1, existencia=:existencia, lote=:lote, receta=:receta, granel=:granel, tipo=:tipo, status=:status, unidadCompra=:unidadCompra, unidadVenta=:unidadVenta, cat_id=:cat_id, srp_id=:srp_id;
                           ";

                $sentencia = $db->prepare($comandoUpdate);

                $sentencia->bindParam('art_id', $art_idLocal);
                $sentencia->bindParam('idSucursal', $idSucursal);
                $sentencia->bindParam('clave', $clave);
                $sentencia->bindParam('claveAlterna', $claveAlterna);
                $sentencia->bindParam('descripcion', $descripcion);
                $sentencia->bindParam('servicio', $servicio);
                $sentencia->bindParam('invMin', $invMin);
                $sentencia->bindParam('invMax', $invMax);
                $sentencia->bindParam('factor', $factor);
                $sentencia->bindParam('precioCompra', $precioCompra);
                $sentencia->bindParam('precioCompraProm', $precioCompraProm);
                $sentencia->bindParam('margen1', $margen1);
                $sentencia->bindParam('precio1', $precio1);
                $sentencia->bindParam('existencia', $existencia);
                $sentencia->bindParam('lote', $lote);
                $sentencia->bindParam('receta', $receta);
                $sentencia->bindParam('granel', $granel);
                $sentencia->bindParam('tipo', $tipo);
                $sentencia->bindParam('status', $status);
                $sentencia->bindParam('unidadCompra', $unidadCompra);
                $sentencia->bindParam('unidadVenta', $unidadVenta);
                $sentencia->bindParam('cat_id', $cat_id);
                $sentencia->bindParam('srp_id', $srp_id);

                $sentencia->execute();

                $comandoauto="ALTER TABLE articulo AUTO_INCREMENT = 1";
                $sentencia2=$db->prepare($comandoauto);
                $sentencia2->execute();
            }
            $arreglo = [
                "estado" => 200,
                "success" => "Se a importado la informacion: ".$contador,
                "datos" => $contador
            ];
            $db->commit();
            return $response->withJson($arreglo, 200);
        }
        catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => "Error al Insertar o Actualizar un registro",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }

    public static function Parametro($request,$response,$args){



      //  $token = $auth->Authorization;
        return "";
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
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }

}