<?php
class importar
{
    protected function __construct()
    {

    }

    public static function modificarEnBatch($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
// Preparar operaci�n de modificaci�n para cada contacto
        //$comando = "UPDATE categoria SET nombre=:nombre,status=:status, dep_id=:dep_id
          //          WHERE cat_id_Local=:cat_id AND idSucursal=:idSucursal";

// Preparar la sentencia update
        $db = getConnection();
        //$sentencia = $db->prepare($comando);

// Ligar parametros

// Procesar array de contactos
        foreach ($postrequest[0]->data as $renglon ) {
            $nombre = $renglon->nombre;
            $status = $renglon->status;
            $dep_id = $renglon->dep_id;
            $idSucursal = $renglon->idSucursal;

            $sentencia->bindParam('cat_id', $cat_id_Local);
            $sentencia->bindParam('idSucursal', $postrequest->idSucursal);
            $sentencia->bindParam('nombre', $postrequest->nombre);
            $sentencia->bindParam('status', $postrequest->status);
            $sentencia->bindParam('dep_id', $postrequest->dep_id);
            $sentencia->execute();
         var_dump($renglon);
        }


    }
    public static function modificarEnBatch2($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
// Preparar operaci�n de modificaci�n para cada contacto
        $comando="UPDATE departamento SET dep_idLocal=:dep_idLocal,idSucursal=:idSucursal, nombre=:nombre, restringido=:restringido,
                  porcentaje=:porcentaje, status=:status WHERE dep_id=:dep_id";

// Preparar la sentencia update
        $db = getConnection();
        $sentencia = $db->prepare($comando);

// Ligar parametros
        $sentencia->bindParam('dep_id', $postrequest->dep_id);
        $sentencia->bindParam('dep_idLocal', $postrequest->dep_idLocal);
        $sentencia->bindParam('idSucursal', $postrequest->idSucursal);
        $sentencia->bindParam('nombre', $postrequest->nombre);
        $sentencia->bindParam('restringido', $postrequest->restringido);
        $sentencia->bindParam('porcentaje', $postrequest->porcentaje);
        $sentencia->bindParam('status', $postrequest->status);

// Procesar array de contactos
        foreach ($arrayContactos as $contacto) {
            $idContacto = $contacto[self::ID_INVENTARIO];
            $primerNombre = $contacto[self::PRIMER_NOMBRE];
            $primerApellido = $contacto[self::EXISTENCIA_SOLICITUD];
            $telefono = $contacto[self::EXISENCIA_RESPUESTA];
            $correo = $contacto[self::FECHA_SOLICITUD];
            $version = $contacto[self::FECHA_RESPUESTA];
            $sentencia->execute();
        }

    }
    public static function modificarEnBatch3($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
// Preparar operaci�n de modificaci�n para cada contacto
        $comando="UPDATE departamento SET dep_idLocal=:dep_idLocal,idSucursal=:idSucursal, nombre=:nombre, restringido=:restringido,
                  porcentaje=:porcentaje, status=:status WHERE dep_id=:dep_id";

// Preparar la sentencia update
        $db = getConnection();
        $sentencia = $db->prepare($comando);

// Ligar parametros
        $sentencia->bindParam('dep_id', $postrequest->dep_id);
        $sentencia->bindParam('dep_idLocal', $postrequest->dep_idLocal);
        $sentencia->bindParam('idSucursal', $postrequest->idSucursal);
        $sentencia->bindParam('nombre', $postrequest->nombre);
        $sentencia->bindParam('restringido', $postrequest->restringido);
        $sentencia->bindParam('porcentaje', $postrequest->porcentaje);
        $sentencia->bindParam('status', $postrequest->status);

// Procesar array de contactos
        foreach ($arrayContactos as $contacto) {
            $idContacto = $contacto[self::ID_INVENTARIO];
            $primerNombre = $contacto[self::PRIMER_NOMBRE];
            $primerApellido = $contacto[self::EXISTENCIA_SOLICITUD];
            $telefono = $contacto[self::EXISENCIA_RESPUESTA];
            $correo = $contacto[self::FECHA_SOLICITUD];
            $version = $contacto[self::FECHA_RESPUESTA];
            $sentencia->execute();
        }

    }
}