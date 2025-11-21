<?php
require_once __DIR__ . '/../model/Produto.php';

class produtoController {

    public function index() {
        $produtoModel = new Produto();
        return $produtoModel->consulta();
    }
    public function show($id) {
        $produtoModel = new Produto();
        return $produtoModel->consultaID($id);
    }

    public function store($data) {
        $produto = $this->popularProduto($data);
        $produtoModel = new Produto();
        $produtoModel->inserir($produto);
    }

    // Método para atualizar DADOS (JSON)
    public function update($id, $data) {
        $produto = $this->popularProduto($data);
        $produto->setId($id);
        $produtoModel = new Produto();
        $produtoModel->editar($produto, $id);
    }

    public function destroy($id) {
        $produtoModel = new Produto();
        $produtoModel->excluir($id);
    }

    // --- Métodos de Filtro Completos ---
    public function filterByCategoria($categoria) {
        $produtoModel = new Produto();
        return $produtoModel->consultaPorCategoria($categoria);
    }

    public function filterByNome($nome) {
        $produtoModel = new Produto();
        // Você precisará criar 'consultaPorNome' no seu Model
        return $produtoModel->consultaPorNome($nome); 
    }

    public function filterByMarca($marca) {
        $produtoModel = new Produto();
        // Você precisará criar 'consultaPorMarca' no seu Model
        return $produtoModel->consultaPorMarca($marca); 
    }

    public function filterByValorMenor($valor) {
        $produtoModel = new Produto();
        // Você precisará criar 'consultaPorValorMenor' no seu Model
        return $produtoModel->consultaPorValorMenor($valor);
    }

    public function filterByValorMaior($valor) {
        $produtoModel = new Produto();
        // Você precisará criar 'consultaPorValorMaior' no seu Model
        return $produtoModel->consultaPorValorMaior($valor);
    }

    public function filterByValorEntre($min, $max) {
        $produtoModel = new Produto();
        // Você precisará criar 'consultaPorValorEntre' no seu Model
        return $produtoModel->consultaPorValorEntre($min, $max);
    }

    public function filterByDisponibilidade($disp) {
        $produtoModel = new Produto();
        // Você precisará criar 'consultaPorDisponibilidade' no seu Model
        return $produtoModel->consultaPorDisponibilidade($disp);
    }

    // --- Fim dos Filtros ---

    private function popularProduto($dados) {
        $produto = new Produto();
        $produto->setNome($dados['nome'] ?? '');
        $produto->setMarca($dados['marca'] ?? '');
        $produto->setCategoria($dados['categoria'] ?? '');
        $produto->setDescricao($dados['descricao'] ?? '');
        $produto->setValor($dados['valor'] ?? 0);
        $produto->setDisponibilidade($dados['disponibilidade'] ?? true);
        return $produto;
    }

    // Método para atualizar IMAGEM (FormData)
    public function updateImage($id, $files) {
        
        if (isset($files['imagem']) && $files['imagem']['error'] === UPLOAD_ERR_OK) {
            
            $nomeDoArquivo = $this->gerenciarUpload($files['imagem']); // Faz o upload

            try {
                $produtoModel = new Produto();
                $produtoModel->atualizarImagem($id, $nomeDoArquivo); // Salva no banco
                
                return $nomeDoArquivo; 

            } catch (Exception $e) {
                throw new Exception("Falha ao ATUALIZAR O BANCO: " . $e->getMessage());
            }

        } else {
            throw new Exception("Nenhum arquivo 'imagem' foi recebido pelo backend.");
        }
    }

    // Método de upload
    private function gerenciarUpload($file) {
        if (!is_dir(UPLOAD_DIR_BACKEND)) {
            mkdir(UPLOAD_DIR_BACKEND, 0777, true); 
        }
        
        $extensao = pathinfo($file['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $extensao; 
        $destino = UPLOAD_DIR_BACKEND . $novoNome;

        if (move_uploaded_file($file['tmp_name'], $destino)) {
            return $novoNome;
        } else {
            throw new Exception("Falha ao mover o arquivo de upload para o destino.");
        }
    }
}