<?php
require_once __DIR__ . '/controller/produtoController.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$routes = require_once __DIR__ . '/routes.php'; 


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = array_values(array_filter(explode('/', trim($uri, '/'))));

if (isset($path[0]) && $path[0] === 'API-Loja') array_shift($path);
if (isset($path[0]) && $path[0] === 'api.php') array_shift($path);

$method = $_SERVER['REQUEST_METHOD'];
$controller = new produtoController();

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
            
            try {
                
                // Requisições GET (index, show, filters)
                if ($method === 'GET') {
                    $result = call_user_func_array([$controller, $controllerMethod], $params);
                    echo json_encode($result);
                } 
                
                // Requisição PUT para dados (update)
                elseif ($method === 'PUT') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data === null) throw new Exception("JSON inválido.");
                    $controller->$controllerMethod($params[0], $data);
                    echo json_encode(['mensagem' => 'Dados atualizados com sucesso']);
                }

                // Requisição POST para criar (store)
                elseif ($method === 'POST' && $controllerMethod === 'store') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data === null) throw new Exception("JSON inválido.");
                    $controller->$controllerMethod($data);
                    http_response_code(201); 
                    echo json_encode(['mensagem' => 'Produto criado com sucesso']);
                } 
                
                // Requisição POST para imagem (updateImage)
                elseif ($method === 'POST' && $controllerMethod === 'updateImage') {
                    if (isset($_POST['_method']) && $_POST['_method'] === 'PATCH') {
                        $novoNome = $controller->updateImage($params[0], $_FILES);
                        echo json_encode([
                            'mensagem' => 'Imagem atualizada com sucesso!', 
                            'novaImagem' => $novoNome
                        ]);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Requisição POST para esta rota sem _method=PATCH não é permitida.']);
                    }
                } 
                
                // Requisição DELETE (destroy)
                elseif ($method === 'DELETE') {
                    call_user_func_array([$controller, $controllerMethod], $params);
                    echo json_encode(['mensagem' => 'Produto excluído com sucesso']);
                }

            } catch (Exception $e) {
                http_response_code(500); 
                echo json_encode(['error' => 'Ocorreu um erro no servidor: ' . $e->getMessage()]);
            }

            $found = true;
            break;
        }
    }
}

if (!$found) {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
}