<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Meu Time - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; padding-top: 50px; }
        .form-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 400px; }
        h2 { border-bottom: 2px solid #27ae60; padding-bottom: 10px; color: #2c3e50; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #bdc3c7; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #27ae60; color: white; border: none; padding: 12px; margin-top: 20px; font-weight: bold; cursor: pointer; border-radius: 4px; }
        .voltar { display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Cadastrar Equipe</h2>
        <form action="processa_time.php" method="POST">
            <label for="nome">Nome do Time</label>
            <input type="text" id="nome" name="nome" placeholder="Ex: Fênix F.C." required>

            <label for="contato">Telefone de Contato</label>
            <input type="text" id="contato" name="contato" placeholder="(34) 99999-9999">

            <label for="logo">Link do Escudo/Logo (URL)</label>
            <input type="text" id="logo" name="caminho_logo" placeholder="http://link-da-imagem.png">

            <button type="submit">Salvar Time</button>
        </form>
        <a href="painel_capitao.php" class="voltar">← Voltar ao Painel</a>
    </div>
</body>
</html>