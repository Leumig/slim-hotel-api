<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    private $rolRequerido;
    private $rolOpcional1;
    private $rolOpcional2;
    private $rolOpcional3;
    private $rolOpcional4;

    public function __construct($rolRequerido, $rolOpcional1 = null,
        $rolOpcional2 = null, $rolOpcional3 = null, $rolOpcional4 = null)
    {
        $this->rolRequerido = $rolRequerido;
        $this->rolOpcional1 = $rolOpcional1;
        $this->rolOpcional2 = $rolOpcional2;
        $this->rolOpcional3 = $rolOpcional3;
        $this->rolOpcional4 = $rolOpcional4;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        try {
            // Intento guardar en '$header' lo que reciba del header Authorization
            $header = $request->getHeaderLine('Authorization');
            $response = new Response();

            if (strlen($header) > 0) { // Valido que a '$header' le haya llegado algo
                $token = trim(explode("Bearer", $header)[1]);
    
                $data = AutentificadorJWT::ObtenerData($token);

                $rol = $data->rol;
        
                if ($rol === $this->rolRequerido || $rol === $this->rolOpcional1 || $rol === $this->rolOpcional2 || $rol === $this->rolOpcional3 || $rol === $this->rolOpcional4)
                {
                    AutentificadorJWT::VerificarToken($token);
                    $response = $handler->handle($request);
                } else {
                    $payload = json_encode(array('mensaje' => 'No tenes permiso para realizar esta accion'));
                    $response->getBody()->write($payload);
                }
            } else {
                $payload = json_encode(array('mensaje' => 'No se recibio el header Authorization'));
                $response->getBody()->write($payload);
            }
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Ocurrio un error con el TOKEN'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
