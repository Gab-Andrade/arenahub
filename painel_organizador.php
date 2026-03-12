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
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        header { background-color: #2c3e50; color: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; }
        .btn-sair { background-color: #e74c3c; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; }
        .btn-sair:hover { background-color: #c0392b; }
        .container { width: 90%; max-width: 1000px; margin: 30px auto; }
        
        /* Destaque azul para diferenciar visualmente do painel do capitão */
        .boas-vindas { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; border-left: 5px solid #3498db; }
        
        .menu-grid { display: flex; gap: 20px; flex-wrap: wrap; }
        .card-menu { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); flex: 1; min-width: 200px; text-align: center; text-decoration: none; color: #2c3e50; font-weight: bold; border-top: 4px solid #3498db; transition: transform 0.2s; }
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
        </div>

    </div>

</body>
</html>