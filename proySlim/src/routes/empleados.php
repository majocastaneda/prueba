<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/empleados', function(Request $request, Response $response){
    $sql = 'SELECT * FROM empleado';
    try {
        $db = new db();
        $db = $db->conexionDB();
        $resultado = $db->query($sql);
        if($resultado->rowCount()>0){
            $empleados = $resultado->fetchall(PDO::FETCH_OBJ);
            echo json_encode($empleados);
        }else{
            echo json_encode('No existen empleados');
        }
        $resultado = null;
        $db = null;

    }catch(PDOException $e){
        $response->getBody()->write("Error ".$e);
    return $response;
    }
    return $response;
});
$app->add(function (Request $request, RequestHandlerInterface $handler): Response {
    $routeContext = RouteContext::fromRequest($request);
    $routingResults = $routeContext->getRoutingResults();
    $methods = $routingResults->getAllowedMethods();
    $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

    $response = $handler->handle($request);

    $response = $response->withHeader('Access-Control-Allow-Origin', '*');
    $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
    $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);

    // Optional: Allow Ajax CORS requests with Authorization header
    // $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

    return $response;
});
$app->addRoutingMiddleware();

$app->post('/empleados/nuevo', function(Request $request, Response $response){
    $cedula = $request->getQueryParams()['cedula'];
    $nombre = $request->getQueryParams()['nombre'];
    $apellido = $request->getQueryParams()['apellido'];
    $sueldo = $request->getQueryParams()['sueldo'];
    $sql = "INSERT INTO empleado(cedula, nombre, apellido, sueldo) VALUES(:cedula, :nombre, :apellido, :sueldo)";
    try{
        $db = new db();
        $db = $db->conexionDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':cedula', $cedula);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':apellido', $apellido);
        $resultado->bindParam(':sueldo', $sueldo);
        $resultado->execute();
        echo json_encode('empleado agregado con exito');
        $resultado = null;
        $db = null;
    } catch(PDOException $e){
        $response->getBody()->write("Error ".$e);
    return $response;
    }
});
$app->put('/empleado/editar/{id}', function(Request $request, Response $response){
    $id_empleado = $request->getAttribute('id');
    $cedula = $request->getQueryParams()['cedula'];
    $nombre = $request->getQueryParams()['nombre'];
    $apellido = $request->getQueryParams()['apellido'];
    $sueldo = $request->getQueryParams()['sueldo'];
    $sql = "UPDATE empleado SET cedula = :cedula, nombre = :nombre, apellido = :apellido, sueldo = :sueldo WHERE id = :id ";
    try{
        $db = new db();
        $db = $db->conexionDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id', $id_empleado);
        $resultado->bindParam(':cedula', $cedula);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':apellido', $apellido);
        $resultado->bindParam(':sueldo', $sueldo);
        $resultado->execute();
        echo json_encode('empleado actualizado con exito');
        $resultado = null;
        $db = null;
    } catch(PDOException $e){
        $response->getBody()->write("Error ".$e);
    return $response;
    }
    return $response;
});
$app->delete('/empleado/eliminar/{id}', function(Request $request, Response $response){
    $id_empleado = $request->getAttribute('id');
    $sql = "DELETE FROM empleado WHERE id = :id ";
    try{
        $db = new db();
        $db = $db->conexionDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id', $id_empleado);
        $resultado->execute();
        echo json_encode('empleado borrado con exito');
        $resultado = null;
        $db = null;
    } catch(PDOException $e){
        $response->getBody()->write("Error ".$e);
    return $response;
    }
    return $response;
});

?>