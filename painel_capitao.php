<?php
session_start();
require_once 'conexao.php';

// PROTEÇÃO: Verifica se o usuário está logado e se é capitão
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    header("Location: login.php");
    exit;
}

// Busca se este capitão já tem um time cadastrado
$stmt = $pdo->prepare("SELECT id FROM times WHERE capitao_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$time = $stmt->fetch(PDO::FETCH_ASSOC);

// Define para onde o primeiro botão vai apontar
$link_time = $time ? "gerenciar_elenco.php" : "cadastrar_time.php";
$texto_time = $time ? "Meu Time & Elenco" : "Cadastrar Meu Time";

// Pega a mensagem de sucesso/erro (se houver)
$mensagem = '';
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Capitão - ArenaHub</title>
    <style>
        /* Mantendo o seu CSS anterior... */
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        header { background-color: #2c3e50; color: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; }
        .btn-sair { background-color: #e74c3c; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; }
        .container { width: 90%; max-width: 1000px; margin: 30px auto; }
        .boas-vindas { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .menu-grid { display: flex; gap: 20px; flex-wrap: wrap; }
        .card-menu { 
            background-color: #fff; padding: 20px; border-radius: 8px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); flex: 1; min-width: 250px; 
            text-align: center; text-decoration: none; color: #2c3e50; 
            font-weight: bold; border-top: 4px solid #27ae60; transition: transform 0.2s; 
        }
        .card-menu:hover { transform: translateY(-5px); background-color: #f9f9f9; }
        .msg { text-align: center; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

    <header>
        <h1>ArenaHub - Área do Capitão</h1>
        <a href="logout.php" class="btn-sair">Sair do Sistema</a>
    </header>

    <div class="container">
        
        <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>

        <div class="boas-vindas">
            <h2>Bem-vindo(a), <?php echo $_SESSION['nome']; ?>!</h2>
            <p>Gerencie sua equipe e participe dos melhores torneios.</p>
        </div>

        <div class="menu-grid">
            <a href="<?php echo $link_time; ?>" class="card-menu"><?php echo $texto_time; ?></a>
            
            <a href="inscrever_campeonato.php" class="card-menu">Inscrever em Campeonato</a>
            <a href="historico.php" class="card-menu">Histórico de Partidas</a>
            <a href="editar_perfil.php" class="card-menu">Editar Perfil</a>
        </div>

    </div>

</body>
</html>