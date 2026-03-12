<?php
// Chama o nosso motor para fazer as contas e buscar no banco
require_once 'motor_index.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>ArenaHub - Tabela e Jogos</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .navbar h1 { margin: 0; font-size: 24px; color: #3498db; }
        .navbar h1 span { color: #27ae60; }
        .nav-links a { color: white; text-decoration: none; margin-left: 20px; font-weight: bold; font-size: 14px; padding: 8px 15px; border-radius: 4px; transition: 0.2s; }
        .nav-links a.btn-login { background-color: #3498db; }
        .nav-links a.btn-login:hover { background-color: #2980b9; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        .campeonato-card { background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 40px; overflow: hidden; }
        .camp-header { background: #3498db; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .camp-header h3 { margin: 0; font-size: 22px; }
        
        .camp-body { display: flex; flex-wrap: wrap; }
        .coluna-tabela { flex: 1; min-width: 300px; padding: 20px; border-right: 1px solid #ecf0f1; }
        .coluna-jogos { flex: 1.5; min-width: 400px; padding: 20px; background: #fafbfc; }

        h4 { color: #2c3e50; margin-top: 0; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        th, td { padding: 10px; text-align: center; border-bottom: 1px solid #ecf0f1; }
        th { background-color: #ecf0f1; color: #34495e; font-weight: bold; }
        td.time-col { text-align: left; font-weight: bold; color: #2c3e50; }
        .pos-1 { color: #27ae60; font-weight: bold; }

        .jogo-card { background: white; border: 1px solid #ddd; border-radius: 6px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .jogo-header { display: flex; justify-content: space-between; font-size: 12px; color: #7f8c8d; font-weight: bold; margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
        .jogo-times { display: flex; justify-content: center; align-items: center; gap: 20px; }
        .time-nome { font-size: 16px; font-weight: bold; color: #2c3e50; flex: 1; text-align: right; }
        .time-nome.visitante { text-align: left; }
        
        .placar-box { background: #34495e; color: white; padding: 5px 15px; border-radius: 4px; font-size: 20px; font-weight: bold; letter-spacing: 2px; }
        .x-agendado { font-weight: bold; color: #bdc3c7; }
        .status-badge { font-size: 11px; padding: 3px 6px; border-radius: 4px; color: white; }
        .bg-verde { background: #27ae60; }
        .bg-laranja { background: #f39c12; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Arena<span>Hub</span></h1>
        <div class="nav-links">
            <a href="cadastro.php">Cadastrar</a>
            <a href="login.php" class="btn-login">Login do Sistema</a>
        </div>
    </div>

    <div class="container">
        <?php if (count($campeonatos) > 0): ?>
            <?php foreach ($campeonatos as $camp): $cid = $camp['id']; ?>
                <div class="campeonato-card">
                    <div class="camp-header">
                        <h3><?php echo htmlspecialchars($camp['nome']); ?></h3>
                        <span><?php echo htmlspecialchars($camp['modalidade']); ?></span>
                    </div>

                    <div class="camp-body">
                        
                        <div class="coluna-tabela">
                            <h4>Classificação</h4>
                            <table>
                                <tr>
                                    <th>#</th>
                                    <th style="text-align: left;">Time</th>
                                    <th title="Pontos">P</th>
                                    <th title="Jogos">J</th>
                                    <th title="Vitórias">V</th>
                                    <th title="Derrotas">D</th>
                                </tr>
                                <?php 
                                if (isset($classificacao[$cid]) && count($classificacao[$cid]) > 0): 
                                    $pos = 1;
                                    foreach ($classificacao[$cid] as $time_rank): 
                                ?>
                                    <tr>
                                        <td class="<?php echo ($pos == 1) ? 'pos-1' : ''; ?>"><?php echo $pos; ?>º</td>
                                        <td class="time-col"><?php echo htmlspecialchars($time_rank['nome']); ?></td>
                                        <td style="font-weight: bold;"><?php echo $time_rank['pontos']; ?></td>
                                        <td><?php echo $time_rank['jogos']; ?></td>
                                        <td><?php echo $time_rank['vitorias']; ?></td>
                                        <td><?php echo $time_rank['derrotas']; ?></td>
                                    </tr>
                                <?php 
                                    $pos++;
                                    endforeach; 
                                else: 
                                ?>
                                    <tr><td colspan="6" style="color:#7f8c8d;">Nenhum time inscrito ainda.</td></tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <div class="coluna-jogos">
                            <h4>Jogos e Resultados</h4>
                            <?php 
                            if (isset($partidas_por_camp[$cid])): 
                                foreach ($partidas_por_camp[$cid] as $jogo): 
                                    $finalizado = ($jogo['placar_a'] !== null && $jogo['placar_b'] !== null);
                            ?>
                                <div class="jogo-card">
                                    <div class="jogo-header">
                                        <span>📅 <?php echo date('d/m/Y \à\s H:i', strtotime($jogo['data_hora'])); ?></span>

                                        <?php if ($finalizado): ?>
                                            <span class="status-badge bg-verde">Encerrado</span>
                                        <?php else: ?>
                                            <span class="status-badge bg-laranja">Agendado</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="jogo-times">
                                        <div class="time-nome"><?php echo htmlspecialchars($jogo['time_a']); ?></div>
                                        
                                        <?php if ($finalizado): ?>
                                            <div class="placar-box"><?php echo $jogo['placar_a']; ?> - <?php echo $jogo['placar_b']; ?></div>
                                        <?php else: ?>
                                            <div class="x-agendado">X</div>
                                        <?php endif; ?>

                                        <div class="time-nome visitante"><?php echo htmlspecialchars($jogo['time_b']); ?></div>
                                    </div>
                                </div>
                            <?php 
                                endforeach; 
                            else: 
                            ?>
                                <p style="color: #7f8c8d;">Nenhuma partida agendada.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <h3 style="text-align:center; color:#7f8c8d;">Nenhum campeonato ativo.</h3>
        <?php endif; ?>
    </div>

</body>
</html>