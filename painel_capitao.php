<?php
session_start();

// PROTEÇÃO: Verifica se o usuário está logado e se o perfil dele é realmente 'capitao'
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    // Se não for capitão ou não estiver logado, manda de volta pro login
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Capitão - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        header { background-color: #2c3e50; color: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; }
        .btn-sair { background-color: #e74c3c; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; }
        .btn-sair:hover { background-color: #c0392b; }
        .container { width: 90%; max-width: 1000px; margin: 30px auto; }
        .boas-vindas { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .menu-grid { display: flex; gap: 20px; flex-wrap: wrap; }
        .card-menu { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); flex: 1; min-width: 200px; text-align: center; text-decoration: none; color: #2c3e50; font-weight: bold; border-top: 4px solid #27ae60; transition: transform 0.2s; }
        .card-menu:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

    <header>
        <h1>ArenaHub - Área do Capitão</h1>
        <a href="logout.php" class="btn-sair">Sair do Sistema</a>
    </header>

    <div class="container">
        
        <div class="boas-vindas">
            <h2>Bem-vindo(a), <?php echo $_SESSION['nome']; ?>!</h2>
            <p>Gerencie sua equipe e participe dos melhores torneios.</p>
        </div>

        <div class="menu-grid">
            <a href="#" class="card-menu">Meu Time & Elenco</a>
            <a href="#" class="card-menu">Inscrever em Campeonato</a>
            <a href="#" class="card-menu">Histórico de Partidas</a>
            <a href="#" class="card-menu">Editar Perfil</a>
        </div>

    </div>

</body>
</html>