<?php

class Permisos
{
    protected function __construct(){
    }

    public static function seleccionar($request, $response)
    {
        $wine = json_decode($request->getBody());
        $roles="";
        //var_dump($wine);
        try {
            $comando = "SELECT idNivelAutorizacion,descripcion FROM ms_nivelAutorizacion WHERE  idNivelAutorizacion>(select idNivelAutorizacion from ms_usuario WHERE usuario=:usuario AND idSucursal=1)";
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("usuario", $wine->usuario);
            $sentencia->execute();
            $roles = $sentencia->fetchAll();
            foreach ($roles as &$rol) {
                $permisoDelRol = Roles::obtenerPermisosDelRol1($rol["idNivelAutorizacion"]);
                $comando = "SELECT idopcion,descripcion FROM ms_opcion";
                $sentencia = $db->prepare($comando);
                $sentencia->execute();
                $permisos = $sentencia->fetchAll();
                foreach ($permisos as &$permiso) {
                    if ($permisoDelRol->tienePermiso($permiso['descripcion'])) {
                        $permiso['activo'] = true;
                    } else {
                        $permiso['activo'] = false;
                    }
                }
                $rol['permisos'] = $permisos;
            }
        } catch (PDOException $e) {
            echo general::traducirMensaje($e->getCode(),$e);
        }

        finally{
            $db=null;
            return ($roles);
        }
    }

    public static function actualizar($request, $response)
    {
        $bandera = false;
        $postrequest = json_decode($request->getBody());
        $db = getConnection();
        $db->beginTransaction();
        foreach ($postrequest->permisos as &$permiso) {
            $estado = $permiso->activo ? 'A' : 'I';
            if (self::Bdactualizar($permiso, $postrequest->idNivelAutorizacion, $estado, $db)) {
                $bandera = true;
            } else {
                $bandera = false;
                $db->rollBack();
                break;
            }
        }
        if ($bandera) {
            $arreglo = [
                "estado" => 200,
                "success" => "La información de los permisos fue guardada con éxito",
                "datos" => ""
            ];
            $db->commit();
            return $response->withJson($arreglo,200);
        } else {
            $arreglo = [
                "estado" => 400,
                "error" => "Error de transacción, favor de reportarlo.",
                "datos" => ""
            ];
            return $response->withJson($arreglo,400);
        }
    }

   private static function Bdactualizar($request, $idNivelAutorizacion, $estado, $db)    {
        $wine = $request;
        $sql = "UPDATE ms_permiso SET idEstado=:idEstado WHERE idNivelAutorizacion=:idNivelAutorizacion AND idOpcion=:idOpcion";
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam("idNivelAutorizacion", $idNivelAutorizacion);
            $stmt->bindParam("idOpcion", $wine->idopcion);
            $stmt->bindParam("idEstado", $estado);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;  }
    }
}
