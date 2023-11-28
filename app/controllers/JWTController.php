<?php
require_once './utils/AutentificadorJWT.php';

class JWTController
{
    public function SolicitarToken($request, $response, $args)
    {
        // Siempre vamos a recibir por POST
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];

        $datos = ['usuario' => $usuario, 'rol' => $rol];

        $respuesta = 'Tus credenciales no son validas';

        // Valido que exista coincidencia en la BD
        if ($this->validarCredenciales($usuario, $clave, $rol)) {
            $respuesta = AutentificadorJWT::CrearToken($datos);
        }

        $payload = json_encode(array("respuesta" => $respuesta));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Esta función valida que las credenciales coincidan en la BD y además que el usuario no este eliminado
    private function validarCredenciales($usuarioRecibido, $claveRecibida, $rolRecibido)
    {
        $retorno = false;
        $lista = Usuario::obtenerTodos(false, true);

        foreach ($lista as $usuario) {
            $hashAlmacenado = $usuario->clave;

            if (
                $usuarioRecibido === $usuario->usuario &&
                password_verify($claveRecibida, $hashAlmacenado) &&
                $rolRecibido === $usuario->rol && $usuario->estado !== 'Eliminado'
            ) {
                $retorno = true;
                break;
            }
        }
    
        return $retorno;
    }
}
