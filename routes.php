<?php
// Caminho para o diretório de upload
// Ajuste este caminho se necessário.
define('UPLOAD_DIR_BACKEND', $_SERVER['DOCUMENT_ROOT'] . '/Front-Loja/img/');

return [
    ['GET',    ['produtos'],                           'index',                 0],
    ['GET',    ['produtos', '{id}'],                    'show',                  1],
    ['POST',   ['produtos'],                           'store',                 0],
    
    // Rota de DADOS (JSON)
    ['PUT',    ['produtos', '{id}'],                    'update',                1],
    
    // Rota de IMAGEM (FormData)
    ['POST',   ['produtos', '{id}'],                   'updateImage',           1], 
    
    ['DELETE', ['produtos', '{id}'],                    'destroy',               1],

    // --- Suas rotas de filtro ---
    ['GET',    ['produtos', 'categoria', '{categoria}'], 'filterByCategoria',     1],
    ['GET',    ['produtos', 'nome', '{nome}'],          'filterByNome',          1],
    ['GET',    ['produtos', 'marca', '{marca}'],        'filterByMarca',         1],
    ['GET',    ['produtos', 'valorMenor', '{valor}'],   'filterByValorMenor',    1],
    ['GET',    ['produtos', 'valorMaior', '{valor}'],   'filterByValorMaior',    1],
    ['GET',    ['produtos', 'valorEntre', '{min}', '{max}'],'filterByValorEntre', 2],
    ['GET',    ['produtos', 'disponibilidade', '{disp}'], 'filterByDisponibilidade',1],
];