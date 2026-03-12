<?php
session_start();

// PROTEÇÃO: Verifica se o usuário está logado e se o perfil dele é realmente 'organizador'
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    // Se não for organizador ou não estiver logado, manda de volta pro login
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Organizador - ArenaHub</title>
    <style>
        :root {
            --bg-body: #f4f7f6;
            --text-main: #2c3e50;
            --text-muted: #7f8c8d;
            --card-bg: #ffffff;
            --card-shadow: 0 4px 8px rgba(0,0,0,0.1);
            --accent-header: #2c3e50;
            --accent-danger: #e74c3c;
            --accent-blue: #3498db;
        }

        body.dark-theme {
            --bg-body: #121212;
            --text-main: #ecf0f1;
            --text-muted: #bdc3c7;
            --card-bg: #1f2933;
            --card-shadow: 0 4px 8px rgba(0,0,0,0.4);
            --accent-header: #111827;
            --accent-danger: #e74c3c;
            --accent-blue: #2980b9;
        }

        body { font-family: Arial, sans-serif; background-color: var(--bg-body); color: var(--text-main); margin: 0; padding: 0; transition: background-color 0.2s ease, color 0.2s ease; }
        header { background-color: var(--accent-header); color: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; }
        .btn-sair { background-color: var(--accent-danger); color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; }
        .btn-sair:hover { background-color: #c0392b; }
        .container { width: 90%; max-width: 1000px; margin: 30px auto; }
        
        /* Destaque azul para diferenciar visualmente do painel do capitão */
        .boas-vindas { background-color: var(--card-bg); padding: 20px; border-radius: 8px; box-shadow: var(--card-shadow); margin-bottom: 20px; border-left: 5px solid var(--accent-blue); }
        
        .menu-grid { display: flex; gap: 20px; flex-wrap: wrap; }
        .card-menu { background-color: var(--card-bg); padding: 20px; border-radius: 8px; box-shadow: var(--card-shadow); flex: 1; min-width: 200px; text-align: center; text-decoration: none; color: var(--text-main); font-weight: bold; border-top: 4px solid var(--accent-blue); transition: transform 0.2s; }
        .card-menu:hover { transform: translateY(-5px); }

    </style>
</head>
<body>

    <header>
        <h1>ArenaHub - Área do Organizador (Admin)</h1>
        <a href="logout.php" class="btn-sair">Sair do Sistema</a>
    </header>

    <div class="container">
        
        <div class="boas-vindas">
            <h2>Olá, Organizador(a) <?php echo $_SESSION['nome']; ?>!</h2>
            <p>Aqui você tem o controle total: crie campeonatos, aprove times e atualize os placares.</p>
        </div>

    <div class="menu-grid">
        <a href="criar_campeonato.php" class="card-menu">Criar / Gerenciar Campeonatos</a>
        <a href="agendar_partidas.php" class="card-menu">Agendar Partidas</a>
        <a href="atualizar_placares.php" class="card-menu">Atualizar Placares</a>
    </div>

    </div>

</body>
</html>