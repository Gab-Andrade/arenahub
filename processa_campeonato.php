<?php
session_start();
require_once 'conexao.php';

// Proteção dupla de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $modalidade = $_POST['modalidade'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $limite_times = $_POST['limite_times'];
    
    // PEGANDO O ID DO ADMIN LOGADO:
    $organizador_id = $_SESSION['usuario_id']; 

    try {
        // ATUALIZAÇÃO: Inserindo o 'organizador_id' no banco
        $stmt = $pdo->prepare("INSERT INTO campeonatos (nome, modalidade, data_inicio, data_fim, limite_times, organizador_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $modalidade, $data_inicio, $data_fim, $limite_times, $organizador_id]);
        
        $_SESSION['mensagem'] = "<p style='color: green;'>Campeonato '$nome' criado com sucesso!</p>";
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red;'>Erro ao criar campeonato: " . $e->getMessage() . "</p>";
    }
    
    // Redireciona de volta para a tela de criação
    header("Location: criar_campeonato.php");
    exit;
}

// Se tentarem acessar direto pela URL
header("Location: painel_organizador.php");
exit;
?>