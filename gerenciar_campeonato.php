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

// 1. Busca as informações DESTE campeonato
$stmt = $pdo->prepare("SELECT * FROM campeonatos WHERE id = ? AND organizador_id = ?");
$stmt->execute([$campeonato_id, $organizador_id]);
$campeonato = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$campeonato) {
    header("Location: criar_campeonato.php");
    exit;
}

// 2. Busca TODAS as inscrições (para aprovar/recusar)
$stmt_inscricoes = $pdo->prepare("
    SELECT i.id AS inscricao_id, i.status, t.nome AS nome_time 
    FROM inscricoes i 
    JOIN times t ON i.time_id = t.id 
    WHERE i.campeonato_id = ?
");
$stmt_inscricoes->execute([$campeonato_id]);
$inscricoes = $stmt_inscricoes->fetchAll(PDO::FETCH_ASSOC);

// 3. Busca APENAS os times APROVADOS (para o select de partidas)
$stmt_aprovados = $pdo->prepare("
    SELECT t.id, t.nome 
    FROM inscricoes i 
    JOIN times t ON i.time_id = t.id 
    WHERE i.campeonato_id = ? AND (i.status = 'aprovada' OR i.status = 'aprovado')
    ORDER BY t.nome ASC
");
$stmt_aprovados->execute([$campeonato_id]);
$times_aprovados = $stmt_aprovados->fetchAll(PDO::FETCH_ASSOC);

// 4. Busca as PARTIDAS já agendadas
$stmt_partidas = $pdo->prepare("
    SELECT p.id, t1.nome AS time_a, t2.nome AS time_b, p.data_hora 
    FROM partidas p
    JOIN times t1 ON p.time_a_id = t1.id
    JOIN times t2 ON p.time_b_id = t2.id
    WHERE p.campeonato_id = ?
    ORDER BY p.data_hora ASC
");
$stmt_partidas->execute([$campeonato_id]);
$partidas = $stmt_partidas->fetchAll(PDO::FETCH_ASSOC);

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
        .container { width: 100%; max-width: 900px; display: flex; flex-direction: column; gap: 20px; }
        .cabecalho-torneio { background: #3498db; color: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .cabecalho-torneio h1 { margin: 0 0 10px 0; }
        .cabecalho-torneio p { margin: 5px 0; font-size: 16px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; margin-top: 0; }
        
        /* Estilos do Formulário de Partida */
        .form-partida { display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; margin-bottom: 20px; background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
        .form-grupo { flex: 1; min-width: 200px; display: flex; flex-direction: column; }
        .form-grupo label { font-weight: bold; margin-bottom: 5px; color: #34495e; font-size: 14px; }
        .form-grupo select, .form-grupo input { padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; }
        .btn-agendar { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; height: 38px; }
        .btn-agendar:hover { background: #2980b9; }

        .voltar { display: block; text-align: center; color: #7f8c8d; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .voltar:hover { color: #2c3e50; }
        .msg { text-align: center; font-weight: bold; margin-bottom: 15px; }
        
        ul { list-style: none; padding: 0; margin: 0; }
        li { background: #ecf0f1; padding: 15px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .time-nome { font-weight: bold; font-size: 18px; color: #2c3e50; }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 12px; color: white; }
        .status-pendente { background-color: #f39c12; }
        .status-aprovada { background-color: #27ae60; }
        
        .partida-info { display: flex; flex-direction: column; text-align: center; flex: 1; }
        .confronto { font-size: 18px; font-weight: bold; color: #2c3e50; }
        .confronto span { color: #e74c3c; margin: 0 10px; }
        .data-jogo { font-size: 14px; color: #7f8c8d; margin-top: 5px; }

        .acoes form { display: inline-block; margin-left: 5px; }
        .btn-aprovar { background: #27ae60; color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn-recusar { background: #e74c3c; color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

    <div class="container">
        <div class="cabecalho-torneio">
            <h1><?php echo htmlspecialchars($campeonato['nome']); ?></h1>
            <p><strong>Modalidade:</strong> <?php echo htmlspecialchars($campeonato['modalidade']); ?></p>
        </div>

        <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>

        <div class="card">
            <h2>Agendar Confrontos</h2>
            
            <?php if (count($times_aprovados) >= 2): ?>
                <form class="form-partida" action="processa_partida.php" method="POST">
                    <input type="hidden" name="campeonato_id" value="<?php echo $campeonato_id; ?>">
                    
                    <div class="form-grupo">
                        <label>Time A</label>
                        <select name="time_a_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($times_aprovados as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="font-weight: bold; color: #e74c3c; margin-bottom: 10px;">X</div>

                    <div class="form-grupo">
                        <label>Time B</label>
                        <select name="time_b_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($times_aprovados as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-grupo">
                        <label>Data e Hora</label>
                        <input type="datetime-local" name="data_hora" required>
                    </div>

                    <button type="submit" class="btn-agendar">Salvar Jogo</button>
                </form>
            <?php else: ?>
                <p style="color: #f39c12; font-weight: bold; text-align: center; padding: 15px; background: #fdf5e6; border-radius: 4px;">
                    É necessário ter pelo menos 2 times APROVADOS para agendar uma partida.
                </p>
            <?php endif; ?>

            <h3 style="color: #34495e; margin-top: 30px; border-bottom: 1px solid #ecf0f1; padding-bottom: 5px;">Calendário de Jogos</h3>
            <ul>
                <?php if (count($partidas) > 0): ?>
                    <?php foreach ($partidas as $p): ?>
                        <li>
                            <div class="partida-info">
                                <div class="confronto">
                                    <?php echo htmlspecialchars($p['time_a']); ?> <span>X</span> <?php echo htmlspecialchars($p['time_b']); ?>
                                </div>
                                <div class="data-jogo">
                                    🗓️ <?php echo date('d/m/Y \à\s H:i', strtotime($p['data_hora'])); ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="justify-content: center; color: #7f8c8d;">Nenhum jogo agendado ainda.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="card">
            <h2>Times Inscritos</h2>
            <ul>
                <?php if (count($inscricoes) > 0): ?>
                    <?php foreach ($inscricoes as $inscricao): ?>
                        <li>
                            <div>
                                <span class="time-nome"><?php echo htmlspecialchars($inscricao['nome_time']); ?></span><br>
                                <span class="status-badge status-<?php echo $inscricao['status']; ?>"><?php echo strtoupper($inscricao['status']); ?></span>
                            </div>
                            <div class="acoes">
                                <?php if ($inscricao['status'] === 'pendente'): ?>
                                    <form action="processa_aprovacao.php" method="POST">
                                        <input type="hidden" name="inscricao_id" value="<?php echo $inscricao['inscricao_id']; ?>">
                                        <input type="hidden" name="campeonato_id" value="<?php echo $campeonato_id; ?>">
                                        <input type="hidden" name="acao" value="aprovar">
                                        <button type="submit" class="btn-aprovar">Aprovar</button>
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