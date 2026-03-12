<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'];

    // ==========================================
    // LÓGICA DE CADASTRO
    // ==========================================
    if ($acao == 'cadastro') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $perfil = $_POST['perfil'];
        $senha = $_POST['senha'];
        
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha_hash, $perfil]);
            
            // Guarda a mensagem de sucesso na sessão e manda voltar pro login
            $_SESSION['mensagem'] = "<p style='color: green; text-align: center; font-weight: bold;'>Cadastro realizado com sucesso! Faça login.</p>";
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "<p style='color: red; text-align: center; font-weight: bold;'>Erro ao cadastrar. E-mail já em uso.</p>";
            header("Location: login.php");
            exit;
        }
    }

    // ==========================================
    // LÓGICA DE LOGIN
    // ==========================================
    if ($acao == 'login') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['perfil'] = $usuario['perfil'];

            // Redireciona para o painel correto
            if ($usuario['perfil'] == 'organizador') {
                header("Location: painel_organizador.php");
            } else {
                header("Location: painel_capitao.php");
            }
            exit;
        } else {
            $_SESSION['mensagem'] = "<p style='color: red; text-align: center; font-weight: bold;'>E-mail ou senha incorretos.</p>";
            header("Location: login.php");
            exit;
        }
    }
}
?>