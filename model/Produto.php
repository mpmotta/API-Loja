<?php
require_once __DIR__ . '/../config.php';

class Produto {
    private $pdo;
    private $tabela = 'produtos';

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function consulta() {
        $sql = "SELECT * FROM $this->tabela";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function consultaID($id) {
        $sql = "SELECT * FROM $this->tabela WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function inserir(Produto $produto) {
        $sql = "INSERT INTO $this->tabela (nome, marca, categoria, descricao, valor, disponibilidade)
                VALUES (:nome, :marca, :categoria, :descricao, :valor, :disponibilidade)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $produto->getNome(), PDO::PARAM_STR);
        $stmt->bindParam(':marca', $produto->getMarca(), PDO::PARAM_STR);
        $stmt->bindParam(':categoria', $produto->getCategoria(), PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $produto->getDescricao(), PDO::PARAM_STR);
        $stmt->bindParam(':valor', $produto->getValor());
        $stmt->bindParam(':disponibilidade', $produto->getDisponibilidade(), PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function editar(produto $produto, $id) {
        $sql = "UPDATE $this->tabela SET nome = :nome, marca = :marca, categoria = :categoria, descricao = :descricao, valor = :valor, disponibilidade = :disponibilidade WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $produto->getNome(), PDO::PARAM_STR);
        $stmt->bindParam(':marca', $produto->getMarca(), PDO::PARAM_STR);
        $stmt->bindParam(':categoria', $produto->getCategoria(), PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $produto->getDescricao(), PDO::PARAM_STR);
        $stmt->bindParam(':valor', $produto->getValor());
        $stmt->bindParam(':disponibilidade', $produto->getDisponibilidade(), PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function excluir($id) {
        $sql = "DELETE FROM $this->tabela WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function consultaPorCategoria($categoria) {
        $sql = "SELECT * FROM $this->tabela WHERE categoria = :categoria";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }
	
    public function consultaPorNome($nome) {
        $like = "%$nome%";
        $sql = "SELECT * FROM $this->tabela WHERE nome LIKE :nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $like, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function consultaPorMarca($marca) {
        $like = "%$marca%";
        $sql = "SELECT * FROM $this->tabela WHERE marca LIKE :marca";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':marca', $like, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function consultaPorValorMenor($valor) {
        $sql = "SELECT * FROM $this->tabela WHERE valor < :valor";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function consultaPorValorMaior($valor) {
        $sql = "SELECT * FROM $this->tabela WHERE valor > :valor";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function consultaPorValorEntre($min, $max) {
        $sql = "SELECT * FROM $this->tabela WHERE valor BETWEEN :min AND :max";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':min', $min);
        $stmt->bindParam(':max', $max);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function consultaPorDisponibilidade($disponibilidade) {
        $sql = "SELECT * FROM $this->tabela WHERE disponibilidade = :disponibilidade";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':disponibilidade', $disponibilidade, PDO::PARAM_BOOL);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Getters e setters
    private $id;
    private $nome;
    private $marca;
    private $categoria;
    private $descricao;
    private $valor;
    private $disponibilidade;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getMarca() { return $this->marca; }
    public function setMarca($marca) { $this->marca = $marca; }
    public function getCategoria() { return $this->categoria; }
    public function setCategoria($categoria) { $this->categoria = $categoria; }
    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function getValor() { return $this->valor; }
    public function setValor($valor) { $this->valor = $valor; }
    public function getDisponibilidade() { return $this->disponibilidade; }
    public function setDisponibilidade($disponibilidade) { $this->disponibilidade = $disponibilidade; }
}
