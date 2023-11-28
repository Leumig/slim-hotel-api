<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Argentina/Buenos_Aires');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ReservaController.php';
require_once './controllers/AjusteController.php';
require_once './controllers/AdminController.php';
require_once './controllers/JWTController.php';
require_once './middlewares/AuthMiddleware.php';
require_once './middlewares/ParamsMiddleware.php';
require_once './middlewares/LogsMiddleware.php';
require_once './utils/Validaciones.php';

// Cargar ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

// Le aplico un middleware a toda la app
// $app->add(new LogsMiddleware());

// Levantar Servidor en puerto 777
// php -S localhost:777 -t app

// Rutas
// Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    // $group->get('/{numero_cliente}', \UsuarioController::class . ':TraerUno');

    $group->get('/ConsultarCliente', \UsuarioController::class . ':ConsultarCliente')
    ->add(new ParamsMiddleware(['numero_cliente', 'numero_documento']));

    $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add(new ParamsMiddleware(['nombre', 'apellido', 'email', 'tipo_cliente', 'numero_documento', 'pais', 'ciudad', 'telefono']));

    $group->put('/{numero_cliente}', \UsuarioController::class . ':ModificarUno')
    ->add(new ParamsMiddleware(['nombre', 'apellido', 'email', 'tipo_cliente', 'numero_documento', 'pais', 'ciudad', 'telefono']));

    $group->delete('/BorrarCliente', \UsuarioController::class . ':BorrarUno')
    ->add(new ParamsMiddleware(['numero_cliente', 'numero_documento', 'tipo_cliente']));
})
->add(new AuthMiddleware('Admin')); // Valido que sea del sector que yo quiero


// Reservas
$app->group('/reservas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ReservaController::class . ':TraerTodos')
    ->add(new AuthMiddleware('Admin')); // Valido que sea del sector que yo quiero
    // $group->get('/{id}', \ReservaController::class . ':TraerUno');

    $group->post('[/]', \ReservaController::class . ':CargarUno')
    ->add(new ParamsMiddleware(['tipo_cliente', 'numero_cliente', 'tipo_habitacion', 'importe', 'fecha_entrada', 'fecha_salida']))
    ->add(new AuthMiddleware('Cliente')); // Valido que sea del sector que yo quiero

    $group->get('/ConsultarReserva', \ReservaController::class . ':ConsultarReserva')
    ->add(new ParamsMiddleware(['consulta']))
    ->add(new AuthMiddleware('Admin')); // Valido que sea del sector que yo quiero

    $group->put('/{id}', \ReservaController::class . ':ModificarUno')
    ->add(new ParamsMiddleware(['tipo_cliente', 'numero_cliente', 'tipo_habitacion', 'importe', 'fecha_entrada', 'fecha_salida']))
    ->add(new AuthMiddleware('Admin')); // Valido que sea del sector que yo quiero

    $group->delete('/{id}', \ReservaController::class . ':BorrarUno')
    ->add(new ParamsMiddleware(['numero_cliente', 'numero_documento']))
    ->add(new AuthMiddleware('Cliente')); // Valido que sea del sector que yo quiero
});

// Ajustes
$app->group('/ajustes', function (RouteCollectorProxy $group) {
    $group->get('[/]', \AjusteController::class . ':TraerTodos');
    $group->get('/{id}', \AjusteController::class . ':TraerUno');

    $group->post('[/]', \AjusteController::class . ':CargarUno')
    ->add(new ParamsMiddleware(['id_reserva', 'motivo', 'monto']));
})
->add(new AuthMiddleware('Admin')); // Valido que sea del sector que yo quiero

// Admins
$app->group('/admins', function (RouteCollectorProxy $group) {
    $group->get('[/]', \AdminController::class . ':TraerTodos');

    $group->post('[/]', \AdminController::class . ':CargarUno')
    ->add(new ParamsMiddleware(['usuario', 'clave', 'nombre', 'apellido', 'email', 'pais', 'ciudad', 'telefono']));
});

// Autenticacion
$app->group('/auth', function (RouteCollectorProxy $group) {
    $group->post('/login', \JWTController::class . ':SolicitarToken');
})
->add(new ParamsMiddleware(['usuario', 'clave', 'rol']));

$app->get('[/]', function (Request $request, Response $response) {
    $payload = json_encode(array("mensaje" => "Hola Mundo. Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
?>