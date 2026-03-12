<?php
session_start();
require_once 'conexao.php';

// Proteção de tela do Organizador
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

// Verifica se o ID do campeonato foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: criar_campeonato.php");
    exit;
}

$campeonato_id = $_GET['id'];
$organizador_id = $_SESSION['usuario_id'];

// Busca as informações exclusivas DESTE campeonato e garante que pertence a este organizador
$stmt = $pdo->prepare("SELECT * FROM campeonatos WHERE id = ? AND organizador_id = ?");
$stmt->execute([$campeonato_id, $organizador_id]);
$campeonato = $stmt->fetch(PDO::FETCH_ASSOC);

// Se tentar acessar um ID de campeonato que não existe ou que é de outro organizador:
if (!$campeonato) {
    header("Location: criar_campeonato.php");
    exit;
}

// Opcional: Buscar as inscrições (vamos usar isso em breve)
$stmt_inscricoes = $pdo->prepare("
    SELECT i.id, i.status, t.nome AS nome_time 
    FROM inscricoes i 
    JOIN times t ON i.time_id = t.id 
    WHERE i.campeonato_id = ?
");
$stmt_inscricoes->execute([$campeonato_id]);
$inscricoes = $stmt_inscricoes->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar <?php echo htmlspecialchars($campeonato['nome']); ?> - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .container { width: 100%; max-width: 800px; }
        .cabecalho-torneio { background: #3498db; color: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .cabecalho-torneio h1 { margin: 0 0 10px 0; }
        .cabecalho-torneio p { margin: 5px 0; font-size: 16px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h2 { color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; margin-top: 0; }
        .voltar { display: block; text-align: center; color: #7f8c8d; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .voltar:hover { color: #2c3e50; }
        
        /* Lista de times pendentes */
        ul { list-style: none; padding: 0; }
        li { background: #ecf0f1; padding: 15px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="container">
        <div class="cabecalho-torneio">
            <h1><?php echo htmlspecialchars($campeonato['nome']); ?></h1>
            <p><strong>Modalidade:</strong> <?php echo htmlspecialchars($campeonato['modalidade']); ?></p>
            <p><strong>Período:</strong> <?php echo date('d/m/Y', strtotime($campeonato['data_inicio'])); ?> até <?php echo date('d/m/Y', strtotime($campeonato['data_fim'])); ?></p>
            <p><strong>Vagas preenchidas:</strong> <?php echo count($inscricoes); ?> / <?php echo $campeonato['limite_times']; ?></p>
        </div>

        <div class="card">
            <h2>Times Inscritos</h2>
            <ul>
                <?php if (count($inscricoes) > 0): ?>
                    <p>Você tem times aguardando aprovação!</p>
                <?php else: ?>
                    <li style="justify-content: center; color: #7f8c8d;">Nenhum time solicitou inscrição ainda.</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <a href="criar_campeonato.php" class="voltar">← Voltar à Lista de Campeonatos</a>
    </div>

</body>
</html>