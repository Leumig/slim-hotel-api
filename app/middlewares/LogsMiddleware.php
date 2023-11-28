<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LogsMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Antes de la acción de la ruta
        $response = $handler->handle($request);





        // Después de la acción de la ruta
        $this->registrarLog($request);

        return $response;
    }

    private function registrarLog($request)
    {
        // Aquí colocas la lógica para registrar el log en la base de datos
        // Puedes obtener información de la solicitud, como la ruta, el método, etc.
        $ruta = $request->getUri()->getPath();
        $metodo = $request->getMethod();

        echo $ruta;
        // echo $metodo;
        // $idUsuario = $this->obtenerIdUsuario(); // Debes implementar esta función según cómo gestionas la autenticación

        // Lógica para insertar el log en la base de datos
        // ...
    }
}