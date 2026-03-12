<?php
session_start();
require_once 'conexao.php';

// Proteção de tela do Organizador
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: criar_campeonato.php");
    exit;
}

$campeonato_id = $_GET['id'];
$organizador_id = $_SESSION['usuario_id'];

// Busca as informações exclusivas DESTE campeonato
$stmt = $pdo->prepare("SELECT * FROM campeonatos WHERE id = ? AND organizador_id = ?");
$stmt->execute([$campeonato_id, $organizador_id]);
$campeonato = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$campeonato) {
    header("Location: criar_campeonato.php");
    exit;
}

// BUSCA AS INSCRIÇÕES E OS NOMES DOS TIMES
$stmt_inscricoes = $pdo->prepare("
    SELECT i.id AS inscricao_id, i.status, t.nome AS nome_time 
    FROM inscricoes i 
    JOIN times t ON i.time_id = t.id 
    WHERE i.campeonato_id = ?
");
$stmt_inscricoes->execute([$campeonato_id]);
$inscricoes = $stmt_inscricoes->fetchAll(PDO::FETCH_ASSOC);

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
        .msg { text-align: center; font-weight: bold; margin-bottom: 15px; }
        
        /* Lista de times pendentes */
        ul { list-style: none; padding: 0; }
        li { background: #ecf0f1; padding: 15px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .time-nome { font-weight: bold; font-size: 18px; color: #2c3e50; }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 12px; color: white; }
        .status-pendente { background-color: #f39c12; }
        .status-aprovada { background-color: #27ae60; }
        
        .acoes form { display: inline-block; margin-left: 5px; }
        .btn-aprovar { background: #27ae60; color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn-aprovar:hover { background: #2ecc71; }
        .btn-recusar { background: #e74c3c; color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn-recusar:hover { background: #c0392b; }
    </style>
</head>
<body>

    <div class="container">
        <div class="cabecalho-torneio">
            <h1><?php echo htmlspecialchars($campeonato['nome']); ?></h1>
            <p><strong>Modalidade:</strong> <?php echo htmlspecialchars($campeonato['modalidade']); ?></p>
            <p><strong>Vagas preenchidas:</strong> <?php echo count($inscricoes); ?> / <?php echo $campeonato['limite_times']; ?></p>
        </div>

        <div class="card">
            <h2>Times Inscritos</h2>
            
            <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>

            <ul>
                <?php if (count($inscricoes) > 0): ?>
                    <?php foreach ($inscricoes as $inscricao): ?>
                        <li>
                            <div>
                                <span class="time-nome"><?php echo htmlspecialchars($inscricao['nome_time']); ?></span>
                                <br>
                                <span class="status-badge status-<?php echo $inscricao['status']; ?>">
                                    <?php echo strtoupper($inscricao['status']); ?>
                                </span>
                            </div>

                            <div class="acoes">
                                <?php if ($inscricao['status'] === 'pendente'): ?>
                                    <form action="processa_aprovacao.php" method="POST">
                                        <input type="hidden" name="inscricao_id" value="<?php echo $inscricao['inscricao_id']; ?>">
                                        <input type="hidden" name="campeonato_id" value="<?php echo $campeonato_id; ?>">
                                        <input type="hidden" name="acao" value="aprovar">
                                        <button type="submit" class="btn-aprovar">Aprovar</button>
                                    </form>
                                    
                                    <form action="processa_aprovacao.php" method="POST">
                                        <input type="hidden" name="inscricao_id" value="<?php echo $inscricao['inscricao_id']; ?>">
                                        <input type="hidden" name="campeonato_id" value="<?php echo $campeonato_id; ?>">
                                        <input type="hidden" name="acao" value="recusar">
                                        <button type="submit" class="btn-recusar" onclick="return confirm('Tem certeza que deseja recusar este time?');">Recusar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="justify-content: center; color: #7f8c8d;">Nenhum time solicitou inscrição ainda.</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <a href="criar_campeonato.php" class="voltar">← Voltar à Lista de Campeonatos</a>
    </div>

</body>
</html>