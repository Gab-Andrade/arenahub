<?php
require_once 'conexao.php';

// Aqui entrará a lógica PHP para processar o formulário futuramente
// (Verificar se a senha está certa, inserir novo usuário no banco, etc.)
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Cadastro - ArenaHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            gap: 20px;
            width: 90%;
            max-width: 800px;
        }

        .form-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            flex: 1;
        }

        h2 {
            color: #2c3e50;
            margin-top: 0;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #34495e;
            font-weight: bold;
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            box-sizing: border-box; /* Garante que o padding não quebre a largura */
        }

        button {
            width: 100%;
            background-color: #27ae60;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #2ecc71;
        }

        .voltar {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
            text-decoration: none;
        }

        .voltar:hover {
            color: #2c3e50;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="form-box">
            <h2>Acessar Conta</h2>
            <form action="login.php" method="POST">
                <input type="hidden" name="acao" value="login">
                
                <div class="input-group">
                    <label for="email_login">E-mail</label>
                    <input type="email" id="email_login" name="email" required>
                </div>
                
                <div class="input-group">
                    <label for="senha_login">Senha</label>
                    <input type="password" id="senha_login" name="senha" required>
                </div>
                
                <button type="submit">Entrar</button>
            </form>
            <a href="index.php" class="voltar">← Voltar para a página inicial</a>
        </div>

        <div class="form-box">
            <h2>Criar Nova Conta</h2>
            <form action="login.php" method="POST">
                <input type="hidden" name="acao" value="cadastro">

                <div class="input-group">
                    <label for="nome_cad">Nome Completo</label>
                    <input type="text" id="nome_cad" name="nome" required>
                </div>

                <div class="input-group">
                    <label for="email_cad">E-mail</label>
                    <input type="email" id="email_cad" name="email" required>
                </div>

                <div class="input-group">
                    <label for="perfil_cad">Eu sou um:</label>
                    <select id="perfil_cad" name="perfil" required>
                        <option value="">Selecione...</option>
                        <option value="capitao">Representante de Time (Capitão)</option>
                        <option value="organizador">Organizador de Torneio (Admin)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="senha_cad">Senha</label>
                    <input type="password" id="senha_cad" name="senha" required>
                </div>

                <button type="submit">Cadastrar</button>
            </form>
        </div>

    </div>

</body>
</html>