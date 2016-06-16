<?php

class usuario
{
    public function __construct(){
    }

    public static function seleccionar($request,$response){
        $comando = "SELECT mu.idUsuario,mu.usuario,'DEFAULTMERCASTOCK' as password,mu.nombre,mu.apellido,mu.idNivelAutorizacion,mu.idSucursal,mu.sexo,mu.idEstado,mu.contacto,mn.descripcion FROM ms_usuario mu INNER JOIN ms_nivelAutorizacion mn ON (mn.idNivelAutorizacion = mu.idNivelAutorizacion)
        WHERE mu.idNivelAutorizacion>0";// mayor que superadmin
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo="";
                return $response->withJson($resultado, 200);
            } else {
                $arreglo = [
                    "estado" => "warning",
                    "success" => "Error al traer listado de usuarios ya que no existen",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Sexo",
                "data" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
            //return $response->withJson($arreglo, 400);
        }
    }

    public static function insertar($request, $response)
    {
        $wine = json_decode($request->getBody());
        $claveApi = "";
        $idEstado = "A";
        $claveGCM = '0';
        $idUsuario = self::obtenerIdUsuario();

        $sql = "INSERT INTO ms_usuario (idUsuario,usuario,password,nombre,apellido,sexo,contacto,idSucursal,claveAPI,idEstado,fechaEstado,fechaSesion,claveGCM,idNivelAutorizacion) VALUES
                (:idUsuario,:usuario,:password,:nombre,:apellido,:sexo,:contacto,:idSucursal,:claveAPI,:idEstado,now(),now(),:claveGCM,:idNivelAutorizacion)";
        try {
            $password = self::encriptarContrasena($wine->password);
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(":usuario", $wine->usuario, PDO::PARAM_STR);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":nombre", $wine->nombre, PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $wine->apellido, PDO::PARAM_STR);
            $stmt->bindParam(":sexo", $wine->sexo, PDO::PARAM_STR);
            $stmt->bindParam(":contacto", $wine->contacto, PDO::PARAM_INT);
            $stmt->bindParam(":idSucursal", $claveGCM, PDO::PARAM_INT);
            $stmt->bindParam(":claveAPI", $claveApi);
            $stmt->bindParam(":idEstado", $idEstado);
            $stmt->bindParam(":claveGCM", $claveGCM);
            $stmt->bindParam(":idNivelAutorizacion", $wine->idNivelAutorizacion, PDO::PARAM_INT);
            $stmt->execute();
            $wine->id = $db->lastInsertId();
            $db = null;
            if ($wine->idUsuario > 0) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "Usuario " . $wine->usuario . " registrado correctamente",
                    "datos" => $wine
                ];
                $codigo = 200;

            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "transaccion sin terminar",
                    "datos" => $wine
                ];;
                $codigo = 400;
            }
        } catch (PDOException $e) {
            $codigoDeError = $e->getCode();
            $error = self::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => $error,
                "datos" => json_encode($wine)
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    private static function obtenerIdUsuario()
    {
        $sql = "SELECT max(idusuario)+1 AS idUsuario FROM ms_usuario";
        try {
            $db = getConnection();
            $query = $db->prepare($sql);
            $query->execute();
            $resultado = $query->fetchObject();
            return $resultado->idUsuario;
        } catch (PDOException $e) {
            return null;
        }
        finally{
            $db=null;
            return null;
        }
    }

    public static function actualizar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        if($postrequest->password!='DEFAULTMERCASTOCK'){
            $query = "UPDATE ms_usuario SET password=:password,nombre=:nombre,apellido=:apellido,sexo=:sexo,contacto=:contacto," .
                "idSucursal=:idSucursal,idEstado=:idEstado,idNivelAutorizacion=:idNivelAutorizacion WHERE usuario=:usuario";
        }else{
            $query = "UPDATE ms_usuario SET nombre=:nombre,apellido=:apellido,sexo=:sexo,contacto=:contacto," .
                "idSucursal=:idSucursal,idEstado=:idEstado,idNivelAutorizacion=:idNivelAutorizacion WHERE usuario=:usuario";
        }
        try {
            $db = getConnection();
            $password = self::encriptarContrasena($postrequest->password);
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("usuario", $postrequest->usuario);
            if($postrequest->password!='DEFAULTMERCASTOCK'){
                $sentencia->bindParam("password", $password);
            }
            $sentencia->bindParam("nombre", $postrequest->nombre);
            $sentencia->bindParam("apellido", $postrequest->apellido);
            $sentencia->bindParam("sexo", $postrequest->sexo);
            $sentencia->bindParam("contacto", $postrequest->contacto);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->bindParam("idEstado", $postrequest->idEstado);
            $sentencia->bindParam("idNivelAutorizacion", $postrequest->idNivelAutorizacion);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado el usuario con éxito",
                        "data" => $resultado
                    ];
                $codigo=200;
            } else {
                $arreglo =
                    [
                        "estado" => "warning",
                        "mensaje" => "No se cambió ningún dato",
                        "data" => $resultado
                    ];
                $codigo=200;
            }
        } catch (PDOException $e) {
            $codigoDeError = $e->getCode();
            $error = self::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => $error,
                "data" => json_encode($postrequest)
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function actualizarContrasena($request, $response)
    {

        $postrequest = json_decode($request->getBody());
        $comando = "SELECT idUsuario,Usuario,password,claveAPI,IDESTADO,idNivelAutorizacion FROM ms_usuario WHERE usuario=:usuario AND idSucursal=0";
        // var_dump($comando);
        $usuario=$postrequest->usuario;
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("usuario", $usuario);
            $sentencia->execute();

            if ($sentencia) {
                $resultado = $sentencia->fetchObject();
                if ($resultado) {
                    $query2 = "UPDATE ms_usuario SET password=:passwordNueva WHERE usuario=:usuario";
                    try {
                        $autenticar = self::autenticar($postrequest->usuario, $postrequest->passwordActual);

                        if ($autenticar['estado'] == '200') {
                            $db = getConnection();
                            //if(self::validarContrasena($postrequest->passwordActual, ''));
                            //$passwordActual = self::encriptarContrasena($postrequest->passwordActual);
                            $passwordNueva = self::encriptarContrasena($postrequest->passwordNueva);
                            $sentencia = $db->prepare($query2);
                            //$sentencia->bindParam("passwordActual", $postrequest->passwordActual);
                            $sentencia->bindParam("passwordNueva", $passwordNueva);
                            $sentencia->bindParam("usuario", $postrequest->usuario);


                            $resultado = $sentencia->execute();
                            if ($resultado) {
                                $arreglo =
                                    [
                                        "estado" => "success",
                                        "success" => "Se ha actualizado la contraseña con éxito",
                                        "data" => $resultado
                                    ];
                                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                            } else {
                                $arreglo =
                                    [
                                        "estado" => "warning",
                                        "mensaje" => "No se cambió ningún dato",
                                        "data" => $resultado
                                    ];
                                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                            }
                        } else {
                            $arreglo =
                                [
                                    "estado" => "warning",
                                    "mensaje" => "La contraseña actual no es correcta",
                                    "data" => "Error"
                                ];
                            return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                        }
                    } catch (PDOException $e) {
                        $arreglo =
                            [
                                "estado" => "warning",
                                "mensaje" => "La contraseña actual no es correcta",
                                "data" => "Error"
                            ];
                        return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                    } finally {
                        $db = null;
                    }
                } else {
                    return false;
                }
            }
        } catch (PDOException $e) {
            //throw new ExcepcionApi(1, $e->getMessage(), 401);
            $arreglo =
                [
                    "estado" => "warning",
                    "mensaje" => "La contraseña actual no es correcta",
                    "data" => "Error"
                ];
            return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
        }
        /*
        $postrequest = json_decode($request->getBody());
        $query = "UPDATE ms_usuario SET password=:passwordNueva WHERE usuario=:usuario";

        try {
            $autenticar=self::autenticar($postrequest->usuario,$postrequest->passwordActual);

            if ($autenticar['estado'] == '200') {
                $db = getConnection();
                //if(self::validarContrasena($postrequest->passwordActual, ''));
                //$passwordActual = self::encriptarContrasena($postrequest->passwordActual);
                $passwordNueva = self::encriptarContrasena($postrequest->passwordNueva);
                $sentencia = $db->prepare($query);
                //$sentencia->bindParam("passwordActual", $postrequest->passwordActual);
                $sentencia->bindParam("passwordNueva", $passwordNueva);
                $sentencia->bindParam("usuario", $postrequest->usuario);


                $resultado = $sentencia->execute();
                if ($resultado) {
                    $arreglo =
                        [
                            "estado" => 200,
                            "success" => "Se ha actualizado la contraseña con éxito",
                            "data" => $resultado
                        ];
                    return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                } else {
                    $arreglo =
                        [
                            "estado" => "warning",
                            "mensaje" => "No se cambió ningún dato",
                            "data" => $resultado
                        ];
                    return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                }
            }
            else{
                $arreglo =
                    [
                        "estado" => "warning",
                        "mensaje" => "No se cambió ningún dato",
                        "data" => "Error"
                    ];
                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            $codigoDeError = $e->getCode();
            $error = self::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => $error,
                "data" => json_encode($postrequest)
            ];;
            return $response->withJson($arreglo, 400);//json_encode($wine);
        }
        finally{
            $db=null;
        }*/
    }



    public static function encriptarContrasena($contrasenaPlana)
    {
        if ($contrasenaPlana)
            return password_hash($contrasenaPlana, PASSWORD_DEFAULT);
        else return null;
    }

    public static function logIn($request, $response)
    {
        $usuario = json_decode($request->getBody());
        $correo = $usuario->usuario;
        $contrasena = $usuario->contrasena;
        try {
            $autenticar = self::autenticar($correo, $contrasena);
            if ($autenticar['estado'] == '200') {
                $codigo = 200;

            } else {
                $codigo = 401;
            }
            return $response->withJson($autenticar, $codigo);
        } catch (PDOException $e) {
            $codigoDeError=$e->getCode();
            $error =LogIn::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($usuario)
            ];
            return $response->withJson($arreglo,400);
        }
        finally{
            $db=null;
        }
    }

    private static function autenticar($usuario, $contrasena)
    {
        $comando = "SELECT idUsuario,Usuario,password,claveAPI,IDESTADO,idNivelAutorizacion FROM ms_usuario WHERE usuario=:usuario AND idSucursal=1";
       // var_dump($comando);
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("usuario", $usuario);
            $sentencia->execute();

            if ($sentencia) {
                $resultado = $sentencia->fetchObject();
                if (($resultado) && self::validarContrasena($contrasena, $resultado->password)) {
                    self::actualizarSesion($usuario);
                    try {
                        return
                            [
                                "estado" => 200,
                                "mensaje" => "OK",
                                "datos" => ["idUsuario"=>$resultado->idUsuario,"Usuario"=>$resultado->Usuario,"ClaveAPI"=>self::$claveApi
                                    ,"IDESTADO"=>$resultado->IDESTADO,"idNivelAutorizacion"=>$resultado->idNivelAutorizacion]
                            ];
                    } catch (PDOException $e) {
                        $codigoDeError=$e->getCode();
                        $error =LogIn::traducirMensaje($codigoDeError,$e);
                        $arreglo = [
                            "estado" =>$e -> getCode(),
                            "error" =>$error,
                            "data" => json_encode($usuario)
                        ];
                        return $arreglo;
                    }
                } else {
                    return
                        [
                            "estado" => 101,
                            "mensaje" => "usuario inexistente"
                        ];
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $db=null;
            $codigoDeError=$e->getCode();
            $error =LogIn::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($usuario)
            ];
            return $arreglo;
        }
    }

    private static function validarContrasena($contrasenaPlana, $contrasenaHash)
    {
        return password_verify($contrasenaPlana, $contrasenaHash);
    }
    public static $claveApi;
    private static function actualizarSesion($usuario)
    {
        $comando2 = "UPDATE ms_usuario SET fechaSesion=NOW(),claveAPI=:claveApi WHERE usuario=:usuario";
        try {
            $db = getConnection();
            self::$claveApi = self::generarClaveApi();
            $sentencia2 = $db->prepare($comando2);
            $sentencia2->bindParam("usuario", $usuario);
            $sentencia2->bindParam("claveApi", self::$claveApi);
            if ($sentencia2->execute()) {
                return true;
            } else
                return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function generarClaveApi()
    {
        return md5(microtime() . rand());
    }

/*
    public static function obtener($username) {
        if (!empty($username)) {
            self::InicializarRoles($username);
            return (self::$roles);

        } else {
            return false;
        }
    }*/
/*
    protected static function InicializarRoles($usuario) {
        self::$roles = array();

        $comando = "SELECT mna.idNivelAutorizacion,mna.descripcion from ms_nivelAutorizacion mna inner join ms_usuario mu on (mu.idUsuario =:idUsuario and mu.idNivelAutorizacion = mna.idNivelAutorizacion) order by mna.idNivelAutorizacion asc";
        $db = getConnection();
        $sentencia = $db->prepare($comando);
        $sentencia->bindParam("idUsuario", $usuario);
        $sentencia->execute();
        $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
        foreach($resultado as $rol) {
            self::$roles[$rol->descripcion] = Roles::obtenerPermisosDelRol($rol->idNivelAutorizacion);
        }
        return self::$roles;
    }*/
    public static function revisarToken($token){
        $query = "select claveAPI from ms_sucursal where claveAPI=:claveApi and claveAPI!='' union all select claveAPI from ms_usuario where claveAPI=:claveApi
                  and claveAPI!=''";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("claveApi",$token);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if($resultado){
                return true;
            } else {
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }
}