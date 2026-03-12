<?php
session_start();
require_once 'conexao.php';

// Proteção: apenas capitão logado pode ver o histórico
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    header("Location: login.php");
    exit;
}

// Descobre o time do capitão
$stmt_time = $pdo->prepare("SELECT id, nome FROM times WHERE capitao_id = ?");
$stmt_time->execute([$_SESSION['usuario_id']]);
$time = $stmt_time->fetch(PDO::FETCH_ASSOC);

$partidas = [];

if ($time) {
    $time_id = $time['id'];

    // Busca todas as partidas em que o time participa (A ou B)
    $stmt_partidas = $pdo->prepare(" 
        SELECT 
            p.id,
            c.nome AS campeonato,
            t1.nome AS time_a,
            t2.nome AS time_b,
            p.placar_a,
            p.placar_b,
            p.data_hora
        FROM partidas p
        JOIN campeonatos c ON p.campeonato_id = c.id
        JOIN times t1 ON p.time_a_id = t1.id
        JOIN times t2 ON p.time_b_id = t2.id
        WHERE p.time_a_id = ? OR p.time_b_id = ?
        ORDER BY p.data_hora DESC
    ");
    $stmt_partidas->execute([$time_id, $time_id]);
    $partidas = $stmt_partidas->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Partidas - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        header { background-color: #2c3e50; color: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; }
        .btn-voltar { background-color: #3498db; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; }
        .container { width: 90%; max-width: 1000px; margin: 30px auto; }
        .card { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h2 { margin-top: 0; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #ecf0f1; text-align: center; }
        th { background-color: #ecf0f1; color: #2c3e50; }
        .status-agendado { color: #f39c12; font-weight: bold; }
        .status-finalizado { color: #27ae60; font-weight: bold; }
        .msg { text-align: center; font-weight: bold; margin-top: 10px; }
        .link-acao { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

<header>
    <h1>ArenaHub - Histórico de Partidas</h1>
    <a href="painel_capitao.php" class="btn-voltar">← Voltar ao Painel</a>
</header>

<div class="container">
    <div class="card">
        <?php if (!$time): ?>
            <h2>Você ainda não possui um time cadastrado</h2>
            <p class="msg">Antes de ver o histórico, é preciso <a class="link-acao" href="cadastrar_time.php">cadastrar o seu time</a>.</p>
        <?php else: ?>
            <h2>Histórico de Partidas do time: <?php echo htmlspecialchars($time['nome']); ?></h2>
            
            <?php if (count($partidas) === 0): ?>
                <p class="msg">Nenhuma partida agendada ou finalizada para este time ainda.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Campeonato</th>
                            <th>Confronto</th>
                            <th>Placar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partidas as $p): ?>
                            <?php
                                $data_formatada = date('d/m/Y H:i', strtotime($p['data_hora']));
                                $tem_placar = $p['placar_a'] !== null && $p['placar_b'] !== null;
                                $status_classe = $tem_placar ? 'status-finalizado' : 'status-agendado';
                                $status_texto = $tem_placar ? 'Finalizada' : 'Agendada';
                                $placar_texto = $tem_placar ? $p['placar_a'] . ' x ' . $p['placar_b'] : '-';
                            ?>
                            <tr>
                                <td><?php echo $data_formatada; ?></td>
                                <td><?php echo htmlspecialchars($p['campeonato']); ?></td>
                                <td><?php echo htmlspecialchars($p['time_a']); ?> x <?php echo htmlspecialchars($p['time_b']); ?></td>
                                <td><?php echo $placar_texto; ?></td>
                                <td class="<?php echo $status_classe; ?>"><?php echo $status_texto; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
