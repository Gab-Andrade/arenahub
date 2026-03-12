<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha_confirma = $_POST['senha_confirma'] ?? '';

    if ($nome === '' || $email === '') {
        $_SESSION['mensagem'] = "<p style='color: red; text-align: center; font-weight: bold;'>Nome e e-mail são obrigatórios.</p>";
        header("Location: editar_perfil.php");
        exit;
    }

    if ($senha !== '' && $senha !== $senha_confirma) {
        $_SESSION['mensagem'] = "<p style='color: red; text-align: center; font-weight: bold;'>As senhas não conferem.</p>";
        header("Location: editar_perfil.php");
        exit;
    }

    try {
        // Verifica se o e-mail já está em uso por outro usuário
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id <> ?");
        $stmt->execute([$email, $usuario_id]);
        $existe = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $_SESSION['mensagem'] = "<p style='color: red; text-align: center; font-weight: bold;'>Este e-mail já está sendo usado por outro usuário.</p>";
            header("Location: editar_perfil.php");
            exit;
        }

        if ($senha !== '') {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, senha_hash = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $senha_hash, $usuario_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $usuario_id]);
        }

        // Atualiza o nome na sessão para refletir no painel
        $_SESSION['nome'] = $nome;

        $_SESSION['mensagem'] = "<p style='color: green; text-align: center; font-weight: bold;'>Perfil atualizado com sucesso!</p>";

        // Redireciona de volta para o painel correto
        if ($_SESSION['perfil'] === 'organizador') {
            header("Location: painel_organizador.php");
        } else {
            header("Location: painel_capitao.php");
        }
        exit;

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red; text-align: center; font-weight: bold;'>Erro ao atualizar perfil: " . $e->getMessage() . "</p>";
        header("Location: editar_perfil.php");
        exit;
    }
}

header("Location: editar_perfil.php");
exit;
