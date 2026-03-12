<?php
session_start();
require_once 'conexao.php';

// Proteção: Apenas o Organizador pode acessar essa tela
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

$organizador_id = $_SESSION['usuario_id'];

// BUSCA NO BANCO: Pega todos os campeonatos criados por este organizador
$stmt = $pdo->prepare("SELECT * FROM campeonatos WHERE organizador_id = ? ORDER BY data_inicio DESC");
$stmt->execute([$organizador_id]);
$campeonatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pega a mensagem de sucesso ou erro (se houver)
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
    <title>Criar e Gerenciar Campeonatos - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; display: flex; flex-direction: column; align-items: center; padding-top: 40px; padding-bottom: 40px; }
        .container { width: 90%; max-width: 800px; display: flex; flex-direction: column; gap: 30px; }
        
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #3498db; padding-bottom: 10px; color: #2c3e50; margin-top: 0; }
        
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #34495e; }
        input, select { width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #3498db; color: white; border: none; padding: 12px; margin-top: 10px; font-weight: bold; cursor: pointer; border-radius: 4px; font-size: 16px; }
        button:hover { background: #2980b9; }
        .voltar { display: block; text-align: center; color: #7f8c8d; text-decoration: none; font-weight: bold; }
        .voltar:hover { color: #2c3e50; }
        .msg { text-align: center; font-weight: bold; margin-bottom: 15px; }

        /* Estilos exclusivos para a lista de campeonatos */
        .lista-camp { list-style: none; padding: 0; margin: 0; }
        .lista-camp li { background: #ecf0f1; margin-bottom: 15px; padding: 15px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #3498db; }
        .camp-info { display: flex; flex-direction: column; }
        .camp-nome { font-weight: bold; font-size: 18px; color: #2c3e50; }
        .camp-detalhes { font-size: 14px; color: #7f8c8d; margin-top: 8px; }
        .badge { display: inline-block; background: #3498db; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; margin-top: 5px; width: fit-content; }
        .btn-gerenciar { color: #3498db; font-weight: bold; text-decoration: none; padding: 8px 12px; border: 1px solid #3498db; border-radius: 4px; }
        .btn-gerenciar:hover { background: #3498db; color: white; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <h2>Abrir Novo Campeonato</h2>
            
            <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>

            <form action="processa_campeonato.php" method="POST">
                <div class="input-group">
                    <label for="nome">Nome do Torneio</label>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Copa Inverno 2026" required>
                </div>

                <div class="input-group">
                    <label for="modalidade">Modalidade (Esporte)</label>
                    <input type="text" id="modalidade" name="modalidade" placeholder="Ex: Futsal, Vôlei, CS:GO" required>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="input-group" style="flex: 1;">
                        <label for="data_inicio">Data de Início</label>
                        <input type="date" id="data_inicio" name="data_inicio" required>
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label for="data_fim">Data de Término</label>
                        <input type="date" id="data_fim" name="data_fim" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="limite_times">Limite de Times Inscritos</label>
                    <input type="number" id="limite_times" name="limite_times" min="2" max="64" placeholder="Ex: 8" required>
                </div>

                <button type="submit">Criar Campeonato</button>
            </form>
        </div>

        <div class="card">
            <h2>Meus Campeonatos</h2>
            <ul class="lista-camp">
                <?php if (count($campeonatos) > 0): ?>
                    <?php foreach ($campeonatos as $camp): ?>
                        <li>
                            <div class="camp-info">
                                <span class="camp-nome"><?php echo htmlspecialchars($camp['nome']); ?></span>
                                <span class="badge"><?php echo htmlspecialchars($camp['modalidade']); ?></span>
                                <span class="camp-detalhes">
                                    Início: <?php echo date('d/m/Y', strtotime($camp['data_inicio'])); ?> | 
                                    Fim: <?php echo date('d/m/Y', strtotime($camp['data_fim'])); ?> | 
                                    Limite: <?php echo $camp['limite_times']; ?> times
                                </span>
                            </div>
                            <a href="gerenciar_campeonato.php?id=<?php echo $camp['id']; ?>" class="btn-gerenciar">Gerenciar</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="justify-content: center; color: #7f8c8d; border-left: none; background: transparent;">
                        Nenhum campeonato criado ainda.
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <a href="painel_organizador.php" class="voltar">← Voltar ao Painel do Admin</a>
    </div>

</body>
</html>