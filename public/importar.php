<?php
class importar
{
    protected function __construct()
    {

    }

    public static function Departamento($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        try {
            $db = getConnection();
            $db->beginTransaction();
            foreach ($postrequest->data as $renglon ) {
                $dep_id=$renglon->dep_id;
                $idSucursal = $renglon->idSucursal;
                $nombre = $renglon->nombre;
                $restringido = $renglon->restringido;
                $porcentaje = $renglon->porcentaje;
                $status = $renglon->status;

                $comandoUpdate="insert into departamento(dep_id,idSucursal,nombre,restringido,porcentaje,status)
                            values (:dep_id, :idSucursal, :nombre, :restringido, :porcentaje, :status) on
                            duplicate key update idSucursal=:idSucursal,nombre=:nombre,restringido=:restringido,porcentaje=:porcentaje,status=:status";

                $sentencia = $db->prepare($comandoUpdate);

                $sentencia->bindParam('dep_id', $dep_id);
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
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function Categoria($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;

        try {
            $db = getConnection();
            $db->beginTransaction();
            foreach ($postrequest->data as $renglon ) {
                $cat_id=$renglon->cat_id;
                $idSucursal = $renglon->idSucursal;
                $nombre= $renglon->nombre;
                $status = $renglon->status;
                $dep_id = $renglon->dep_id;

                $comandoUpdate="insert into categoria(cat_id,idSucursal,nombre,status,dep_id)
                            values (:cat_id, :idSucursal, :nombre, :status, :dep_id) on
                            duplicate key update idSucursal=:idSucursal,nombre=:nombre,status=:status,dep_id=:dep_id;";

                $sentencia = $db->prepare($comandoUpdate);

                $sentencia->bindParam('cat_id', $cat_id);
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
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];

            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function Articulo($request, $response)
    {
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        $contador=0;
        try {
            $db = getConnection();
            $db->beginTransaction();
            foreach ($postrequest->data as $renglon) {
                $contador++;
                $art_id = $renglon->art_id!=''?$renglon->art_id:null;
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

                $comandoUpdate = "INSERT INTO articulo(art_id, idSucursal, clave, claveAlterna, descripcion, servicio, invMin, invMax, factor, precioCompra, precioCompraProm, margen1, precio1, existencia, lote, receta, granel, tipo, status, unidadCompra, unidadVenta, cat_id, srp_id)
                            VALUES (:art_id, :idSucursal, :clave, :claveAlterna, :descripcion, :servicio, :invMin, :invMax, :factor, :precioCompra, :precioCompraProm, :margen1, :precio1, :existencia, :lote, :receta, :granel, :tipo, :status, :unidadCompra, :unidadVenta, :cat_id, :srp_id) ON
                            DUPLICATE KEY UPDATE clave=:clave, claveAlterna=:claveAlterna, descripcion=:descripcion, servicio=:servicio, invMin=:invMin, invMax=:invMax, factor=:factor, precioCompra=:precioCompra, precioCompraProm=:precioCompraProm, margen1=:margen1, precio1=:precio1, existencia=:existencia, lote=:lote, receta=:receta, granel=:granel, tipo=:tipo, status=:status, unidadCompra=:unidadCompra, unidadVenta=:unidadVenta, cat_id=:cat_id, srp_id=:srp_id;
                           ";

                $sentencia = $db->prepare($comandoUpdate);

                $sentencia->bindParam('art_id', $art_id);
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
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }


    public static function Venta($request,$response)
    {
        set_time_limit(0);
        $contador=0;
        $postrequest = json_decode($request->getBody());
        $db=null;
        try {
            $db = getConnection();
            $db->beginTransaction();
            if (isset($postrequest->data) && gettype($postrequest->data) == 'array') {
                $comando = "insert into venta(ven_id, ven_idLocal, fecha, subtotal0, subtotal, descuento, total, cambio, letra, monSubtotal0, monSubtotal, monDescuento, monTotal, monCambio, monLetra, monAbr, monTipoCambio, comentario, decimales, porPeriodo, ventaPorAjuste, puntos, monedas, status, tic_id, not_id, rem_id, caj_id, mon_id, rcc_id, can_caj_id, can_rcc_id, vnd_id, idSucursal)
                values(:ven_id,:ven_id,:fecha,:subtotal0,:subtotal,:descuento,:total,:cambio,:letra,:monSubtotal0, :monSubtotal, :monDescuento, :monTotal,:monCambio, :monLetra, :monAbr, :monTipoCambio, :comentario, :decimales, :porPeriodo, :ventaPorAjuste, :puntos, :monedas, :status, :tic_id, :not_id, :rem_id, :caj_id, :mon_id, :rcc_id, :can_caj_id, :can_rcc_id, :vnd_id, :idSucursal)
                on duplicate key update ven_idLocal=:ven_id,fecha=:fecha,subtotal0=:subtotal0,subtotal=:subtotal,descuento=:descuento,total=:total,cambio=:cambio,letra=:letra,monSubtotal0=:monSubtotal0,monSubtotal=:monSubtotal,monDescuento=:monDescuento,monTotal=:monTotal,monCambio=:monCambio,monLetra=:monLetra,monAbr=:monAbr,monTipoCambio=:monTipoCambio,comentario=:comentario,decimales=:decimales,porPeriodo=:porPeriodo,ventaPorAjuste=:ventaPorAjuste,puntos=:puntos,monedas=:monedas,status=:status,tic_id=:tic_id,not_id=:not_id,rem_id=:rem_id,caj_id=:caj_id,mon_id=:mon_id,rcc_id=:rcc_id,can_caj_id=:can_caj_id,can_rcc_id=:can_rcc_id,vnd_id=:vnd_id,idSucursal=:idSucursal";
                foreach ($postrequest->data as $renglon) {
                    $contador++;
                    $sentencia = $db->prepare($comando);
                    $sentencia->bindParam("ven_id",$renglon->ven_id);
                    $sentencia->bindParam("fecha",$renglon->fecha);
                    $sentencia->bindParam("subtotal0",$renglon->subtotal0);
                    $sentencia->bindParam("subtotal",$renglon->subtotal);
                    $sentencia->bindParam("descuento",$renglon->descuento);
                    $sentencia->bindParam("total",$renglon->total);
                    $sentencia->bindParam("cambio",$renglon->cambio);
                    $sentencia->bindParam("letra",$renglon->letra);
                    $sentencia->bindParam("monSubtotal0",$renglon->monSubtotal0);
                    $sentencia->bindParam("monSubtotal",$renglon->monSubtotal);
                    $sentencia->bindParam("monDescuento",$renglon->monDescuento);
                    $sentencia->bindParam("monTotal",$renglon->monTotal);
                    $sentencia->bindParam("monCambio",$renglon->monCambio);
                    $sentencia->bindParam("monLetra",$renglon->monLetra);
                    $sentencia->bindParam("monAbr",$renglon->monAbr);
                    $sentencia->bindParam("monTipoCambio",$renglon->monTipoCambio);
                    $sentencia->bindParam("comentario",$renglon->comentario);
                    $sentencia->bindParam("decimales",$renglon->decimales);
                    $sentencia->bindParam("porPeriodo",$renglon->porPeriodo);
                    $sentencia->bindParam("ventaPorAjuste",$renglon->ventaPorAjuste);
                    $sentencia->bindParam("puntos",$renglon->puntos);
                    $sentencia->bindParam("monedas",$renglon->monedas);
                    $sentencia->bindParam("status",$renglon->status);
                    $sentencia->bindParam("tic_id",$renglon->tic_id);
                    $sentencia->bindParam("not_id",$renglon->not_id);
                    $sentencia->bindParam("rem_id",$renglon->rem_id);
                    $sentencia->bindParam("caj_id",$renglon->caj_id);
                    $sentencia->bindParam("mon_id",$renglon->mon_id);
                    $sentencia->bindParam("rcc_id",$renglon->rcc_id);
                    $sentencia->bindParam("can_caj_id",$renglon->can_caj_id);
                    $sentencia->bindParam("can_rcc_id",$renglon->can_rcc_id);
                    $sentencia->bindParam("vnd_id",$renglon->vnd_id);
                    $sentencia->bindParam("idSucursal",$renglon->idSucursal);
                    $sentencia->execute();

                    $comandoauto = "ALTER TABLE venta AUTO_INCREMENT = 1";
                    $sentencia2 = $db->prepare($comandoauto);
                    $sentencia2->execute();
                }
                $arreglo = [
                    "estado" => 200,
                    "success" => "Se a importado la informacion: " . $contador,
                    "datos" => $contador
                ];
                $db->commit();
                return $response->withJson($arreglo, 200);
            }else{
                $db->rollBack();
                $arreglo=[
                    "estado"=>"400",
                    "error"=>"error de formato",
                    "datos"=>$postrequest];
                return $response->withJson($arreglo,400);
            }
        }
        catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, 200);
        }

    }

    public static function DetalleVenta($request,$response)
    {
        set_time_limit(0);
        $contador=0;
        $postrequest = json_decode($request->getBody());
        $db=null;
        try {
            $db = getConnection();
            $db->beginTransaction();
            //var_dump($postrequest[0]->data);
            if (isset($postrequest->data) && gettype($postrequest->data) == 'array') {
                $comando = "insert into  detallev (ven_idLocal, idSucursal, art_id, clave, descripcion, cantidad, unidad, precioNorSin, precioNorCon, precioSin, precioCon, importeNorSin, importeNorCon, importeSin, importeCon, descPorcentaje, descTotal, precioCompra, importeCompra, sinGravar, caracteristicas, orden, detImp, iepsActivo, cuotaIeps, cuentaPredial, movVen, movVenC, monPrecioNorSin, monPrecioNorCon, monPrecioSin, monPrecioCon, monImporteNorSin, monImporteNorCon, monImporteSin, monImporteCon, nombreAduana, fechaDocAduanero, numeroDocAduanero, lote, receta, tipo, trr_id, ncr_id)
                values(:ven_id,:idSucursal,:art_id,:clave,:descripcion,:cantidad,:unidad,:precioNorSin,:precioNorCon,:precioSin,:precioCon,:importeNorSin,:importeNorCon,:importeSin,:importeCon,:descPorcentaje,:descTotal,:precioCompra,:importeCompra,:sinGravar,:caracteristicas,:orden,:detImp,:iepsActivo,:cuotaIeps,:cuentaPredial,:movVen,:movVenC,:monPrecioNorSin,:monPrecioNorCon,:monPrecioSin,:monPrecioCon,:monImporteNorSin,:monImporteNorCon,:monImporteSin,:monImporteCon,:nombreAduana,:fechaDocAduanero,:numeroDocAduanero,:lote,:receta,:tipo,:trr_id,:ncr_id)
                on duplicate key update art_id=:art_id,clave=:clave,descripcion=:descripcion,cantidad=:cantidad,unidad=:unidad,precioNorSin=:precioNorSin,precioNorCon=:precioNorCon,precioSin=:precioSin,precioCon=:precioCon,importeNorSin=:importeNorSin,importeNorCon=:importeNorCon,importeSin=:importeSin,importeCon=:importeCon,descPorcentaje=:descPorcentaje,descTotal=:descTotal,precioCompra=:precioCompra,importeCompra=:importeCompra,sinGravar=:sinGravar,caracteristicas=:caracteristicas,orden=:orden,detImp=:detImp,iepsActivo=:iepsActivo,cuotaIeps=:cuotaIeps,cuentaPredial=:cuentaPredial,movVen=:movVen,movVenC=:movVenC,monPrecioNorSin=:monPrecioNorSin,monPrecioNorCon=:monPrecioNorCon,monPrecioSin=:monPrecioSin,monPrecioCon=:monPrecioCon,monImporteNorSin=:monImporteNorSin,monImporteNorCon=:monImporteNorCon,monImporteSin=:monImporteSin,monImporteCon=:monImporteCon,nombreAduana=:nombreAduana,fechaDocAduanero=:fechaDocAduanero,numeroDocAduanero=:numeroDocAduanero,lote=:lote,receta=:receta,tipo=:tipo,trr_id=:trr_id,ncr_id=:ncr_id";
                foreach ($postrequest->data as $renglon) {
                    $contador++;
                    $sentencia = $db->prepare($comando);
                    $sentencia->bindParam("ven_id",$renglon->ven_id);
                    $sentencia->bindParam("idSucursal",$renglon->idSucursal);
                    $sentencia->bindParam("art_id",$renglon->art_id);
                    $sentencia->bindParam("clave",$renglon->clave);
                    $sentencia->bindParam("descripcion",$renglon->descripcion);
                    $sentencia->bindParam("cantidad",$renglon->cantidad);
                    $sentencia->bindParam("unidad",$renglon->unidad);
                    $sentencia->bindParam("precioNorSin",$renglon->precioNorSin);
                    $sentencia->bindParam("precioNorCon",$renglon->precioNorCon);
                    $sentencia->bindParam("precioSin",$renglon->precioSin);
                    $sentencia->bindParam("precioCon",$renglon->precioCon);
                    $sentencia->bindParam("importeNorSin",$renglon->importeNorSin);
                    $sentencia->bindParam("importeNorCon",$renglon->importeNorCon);
                    $sentencia->bindParam("importeSin",$renglon->importeSin);
                    $sentencia->bindParam("importeCon",$renglon->importeCon);
                    $sentencia->bindParam("descPorcentaje",$renglon->descPorcentaje);
                    $sentencia->bindParam("descTotal",$renglon->descTotal);
                    $sentencia->bindParam("precioCompra",$renglon->precioCompra);
                    $sentencia->bindParam("importeCompra",$renglon->importeCompra);
                    $sentencia->bindParam("sinGravar",$renglon->sinGravar);
                    $sentencia->bindParam("caracteristicas",$renglon->caracteristicas);
                    $sentencia->bindParam("orden",$renglon->orden);
                    $sentencia->bindParam("detImp",$renglon->detImp);
                    $sentencia->bindParam("iepsActivo",$renglon->iepsActivo);
                    $sentencia->bindParam("cuotaIeps",$renglon->cuotaIeps);
                    $sentencia->bindParam("cuentaPredial",$renglon->cuentaPredial);
                    $sentencia->bindParam("movVen",$renglon->movVen);
                    $sentencia->bindParam("movVenC",$renglon->movVenC);
                    $sentencia->bindParam("monPrecioNorSin",$renglon->monPrecioNorSin);
                    $sentencia->bindParam("monPrecioNorCon",$renglon->monPrecioNorCon);
                    $sentencia->bindParam("monPrecioSin",$renglon->monPrecioSin);
                    $sentencia->bindParam("monPrecioCon",$renglon->monPrecioCon);
                    $sentencia->bindParam("monImporteNorSin",$renglon->monImporteNorSin);
                    $sentencia->bindParam("monImporteNorCon",$renglon->monImporteNorCon);
                    $sentencia->bindParam("monImporteSin",$renglon->monImporteSin);
                    $sentencia->bindParam("monImporteCon",$renglon->monImporteCon);
                    $sentencia->bindParam("nombreAduana",$renglon->nombreAduana);
                    $sentencia->bindParam("fechaDocAduanero",$renglon->fechaDocAduanero);
                    $sentencia->bindParam("numeroDocAduanero",$renglon->numeroDocAduanero);
                    $sentencia->bindParam("lote",$renglon->lote);
                    $sentencia->bindParam("receta",$renglon->receta);
                    $sentencia->bindParam("tipo",$renglon->tipo);
                    $sentencia->bindParam("trr_id",$renglon->trr_id);
                    $sentencia->bindParam("ncr_id",$renglon->ncr_id);
                    $sentencia->execute();
                }
                $arreglo = [
                    "estado" => 200,
                    "success" => "Se a importado la informacion: " . $contador,
                    "datos" => $contador
                ];
                $db->commit();
                return $response->withJson($arreglo, 200);
            }else{
                $db->rollBack();
                $arreglo=[
                    "estado"=>"400",
                    "error"=>"error de formato",
                    "datos"=>$postrequest];
                return $response->withJson($arreglo,400);
            }
        }
        catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }

    }

    public static function importarUsuarios($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "insert into ms_usuario values(:idUsuario,:usuario,:password,:nombre,:apellido,:sexo,:contacto,:idSucursal,:claveApi,:idEstado,:fechaEstado,
                  :fechaSesion,:claveGCM,:idNivelAutorizacion)on duplicate key update  password=:password,claveGCM=:claveGCM,fechaEstado=:fechaEstado,
                      fechaSesion=:fechaSesion";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            $resultado=false;
            foreach ($postrequest->data as $renglon ) {
                $sentencia->bindParam("idUsuario",$renglon->idUsuario, PDO::PARAM_STR);
                $sentencia->bindParam("usuario", $renglon->usuario, PDO::PARAM_STR);
                $sentencia->bindParam("password", $renglon->contrasena, PDO::PARAM_STR);
                $sentencia->bindParam("nombre", $renglon->nombre, PDO::PARAM_STR);
                $sentencia->bindParam("apellido",$renglon->apellido, PDO::PARAM_STR);
                $sentencia->bindParam("sexo",$renglon->sexo, PDO::PARAM_STR);
                $sentencia->bindParam("idSucursal",$renglon->idSucursal, PDO::PARAM_STR);
                $sentencia->bindParam("contacto",$renglon->contacto, PDO::PARAM_STR);
                $sentencia->bindParam("claveApi",$renglon->claveApi, PDO::PARAM_STR);
                $sentencia->bindParam("idEstado",$renglon->idEstado, PDO::PARAM_STR);
                $sentencia->bindParam("fechaEstado",$renglon->fechaEstado, PDO::PARAM_STR);
                $sentencia->bindParam("fechaSesion",$renglon->fechaSesion, PDO::PARAM_STR);
                $sentencia->bindParam("claveGCM",$renglon->claveGCM, PDO::PARAM_STR);
                $sentencia->bindParam("idNivelAutorizacion",$renglon->idNivelAutorizacion, PDO::PARAM_STR);

                $resultado = $sentencia->execute();
            }

            if($resultado){
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "",
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
        }catch(PDOException $e){
            $codigoDeError=$e->getCode();
            $error =logIn::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($postrequest->data)
            ];
            return $response->withJson($arreglo,400);
        }
    }

    public static function importarInventarioAPI($request, $response){
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        try {
            $db = getConnection();
            $db->beginTransaction();
            $query = "UPDATE ms_inventario SET idInventarioLocal=:idInventario, existenciaSolicitud=:existenciaSolicitud, existenciaRespuesta=:existenciaRespuesta, idUsuario=:idUsuario, fechaSolicitud=:fechaSolicitud, fechaRespuesta=:fechaRespuesta, existenciaEjecucion=:existenciaEjecucion, idEstado=:idEstado

                    WHERE idInventario=:idInventario AND art_id=:art_id AND ms_inventario.idSucursal=:idSucursal ;";
            $sentencia = $db->prepare($query);

            foreach ($postrequest->data as $renglon ) {

                $sentencia->bindParam("idInventario", $renglon->idInventario, PDO::PARAM_STR);
                $sentencia->bindParam("art_id", $renglon->art_id, PDO::PARAM_STR);
                $sentencia->bindParam("idSucursal", $renglon->idSucursal, PDO::PARAM_STR);

                $sentencia->bindParam("existenciaSolicitud", $renglon->existenciaSolicitud, PDO::PARAM_STR);
                $sentencia->bindParam("existenciaRespuesta", $renglon->existenciaRespuesta, PDO::PARAM_STR);
                $sentencia->bindParam("idUsuario", $renglon->idUsuario, PDO::PARAM_STR);
                $sentencia->bindParam("fechaSolicitud", $renglon->fechaSolicitud, PDO::PARAM_STR);
                $sentencia->bindParam("fechaRespuesta", $renglon->fechaRespuesta, PDO::PARAM_STR);
                $sentencia->bindParam("existenciaEjecucion", $renglon->existenciaEjecucion, PDO::PARAM_STR);
                $sentencia->bindParam("idEstado",$renglon->idEstado, PDO::PARAM_STR);
                $resultado = $sentencia->execute();
            }
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado con éxito",
                        "data" => $resultado
                    ];
                $db->commit();
                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            } else {
                $db->rollBack();
                $arreglo =
                    [
                        "estado" => "warning",
                        "mensaje" => "No se cambió ningún dato",
                        "data" => $resultado
                    ];
                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),

                "data" => json_encode($postrequest)
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
    public static function VentaTipoPago($request, $response)
    {
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        try {
            $db = getConnection();
            $db->beginTransaction();
            $banderaEjecucion=false;

            foreach ($postrequest->data as $renglon ) {
                $ven_id=$renglon->ven_id;
                $tpa_id = $renglon->tpa_id;
                $total = $renglon->total;
                $monTotal = $renglon->monTotal;
                $idSucursal = $renglon->idSucursal;
                $comando="INSERT INTO ventatipopago (ven_id, tpa_id, total, monTotal,idSucursal) VALUES (:ven_id,:tpa_id,:total,:monTotal,:idSucursal) on
                            duplicate key update total=:total,monTotal=:monTotal;";

                $sentencia = $db->prepare($comando);

                $sentencia->bindParam('ven_id', $ven_id);
                $sentencia->bindParam('tpa_id', $tpa_id);
                $sentencia->bindParam('total', $total);
                $sentencia->bindParam('monTotal', $monTotal);
                $sentencia->bindParam('idSucursal', $idSucursal);
                $banderaEjecucion=$sentencia->execute();
                if($banderaEjecucion==false){
                    break;
                }
            }
            if($banderaEjecucion){
                $arreglo = [
                    "estado" => 200,
                    "success" => "Se a importado la informacion",
                    "datos" => "ok"
                ];
                $db->commit();
                $codigo=200;
            }else{
                $db->rollBack();
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al Insertar o Actualizar un registro",
                    "datos" => "error"
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
}