<?php
require_once __DIR__ . '/controller/produtoController.php';

header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = array_values(array_filter(explode('/', trim($uri, '/'))));

if (isset($path[0]) && $path[0] === 'API-Loja') array_shift($path);
if (isset($path[0]) && $path[0] === 'api.php') array_shift($path);

$method = $_SERVER['REQUEST_METHOD'];
$controller = new produtoController();

$routes = [
    ['GET',    ['produtos'],                                 'index',                 0],
    ['GET',    ['produtos', '{id}'],                           'show',                  1],
    ['POST',   ['produtos'],                                 'store',                 0],
    ['PUT',    ['produtos', '{id}'],                           'update',                1],
    ['DELETE', ['produtos', '{id}'],                           'destroy',               1],
    ['GET',    ['produtos', 'categoria', '{categoria}'],     'filterByCategoria',     1],
    ['GET',    ['produtos', 'nome', '{nome}'],               'filterByNome',          1],
    ['GET',    ['produtos', 'marca', '{marca}'],             'filterByMarca',         1],
    ['GET',    ['produtos', 'valorMenor', '{valor}'],        'filterByValorMenor',    1],
    ['GET',    ['produtos', 'valorMaior', '{valor}'],        'filterByValorMaior',    1],
    ['GET',    ['produtos', 'valorEntre', '{min}', '{max}'], 'filterByValorEntre',    2],
    ['GET',    ['produtos', 'disponibilidade', '{disp}'],   'filterByDisponibilidade', 1],
];

function matchRoute($routePattern, $path) {
    if (count($routePattern) !== count($path)) return false;
    $params = [];
    foreach ($routePattern as $i => $segment) {
        if (preg_match('/^{.+}$/', $segment)) {
            $params[] = $path[$i];
        } elseif ($segment !== $path[$i]) {
            return false;
        }
    }
    return $params;
}

$found = false;
foreach ($routes as $route) {
    list($routeMethod, $routePattern, $controllerMethod, $paramCount) = $route;
    if ($method === $routeMethod) {
        $params = matchRoute($routePattern, $path);
        if ($params !== false) {

            $decodedParams = array_map('urldecode', $params);

            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $controller->$controllerMethod($data);
                echo json_encode(['success' => true, 'result' => $result]);
            } elseif ($method === 'PUT') {
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $controller->$controllerMethod($decodedParams[0], $data); 
                echo json_encode(['success' => true, 'result' => $result]);
            } else {
                $result = call_user_func_array([$controller, $controllerMethod], $decodedParams);
                echo json_encode($result);
            }
            $found = true;
            break;
        }
    }
}

if (!$found && $path && $path[0] === 'login' && $method === 'POST') {
    require_once 'vendor/autoload.php';
    $data = json_decode(file_get_contents('php://input'), true);
    $usuario = $data['usuario'] ?? '';
    $senha = $data['senha'] ?? '';
    if ($usuario === 'admin' && $senha === '123456') {
        $key = 'sua-chave-secreta';
        $payload = [
            "user" => $usuario,
            "exp" => time() + 3600
        ];
        $jwt = \Firebase\JWT\JWT::encode($payload, $key, 'HS256');
        echo json_encode(['token' => $jwt]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Usuário ou senha inválidos']);
    }
    $found = true;
}

if (!$found) {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
}