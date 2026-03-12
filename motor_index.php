<?php
// motor_index.php
require_once 'conexao.php';

// 1. Busca os Campeonatos
$stmt_camp = $pdo->query("SELECT * FROM campeonatos ORDER BY data_inicio DESC");
$campeonatos = $stmt_camp->fetchAll(PDO::FETCH_ASSOC);

// 2. Busca TODAS as partidas
$stmt_partidas = $pdo->query("
    SELECT p.campeonato_id, p.time_a_id, p.time_b_id, t1.nome AS time_a, t2.nome AS time_b, p.placar_a, p.placar_b, p.data_hora 
    FROM partidas p
    JOIN times t1 ON p.time_a_id = t1.id
    JOIN times t2 ON p.time_b_id = t2.id
    ORDER BY p.data_hora ASC
");
$todas_partidas = $stmt_partidas->fetchAll(PDO::FETCH_ASSOC);

$partidas_por_camp = [];
foreach ($todas_partidas as $p) {
    $partidas_por_camp[$p['campeonato_id']][] = $p;
}

// 3. Busca todos os times aprovados nas inscrições
$stmt_times = $pdo->query("
    SELECT i.campeonato_id, t.id, t.nome 
    FROM inscricoes i 
    JOIN times t ON i.time_id = t.id 
    WHERE i.status = 'aprovada'
");
$inscricoes = $stmt_times->fetchAll(PDO::FETCH_ASSOC);

// 4. LÓGICA DA TABELA DE CLASSIFICAÇÃO (BLINDADA)
$classificacao = [];

// Função inteligente que garante que o time exista na tabela
function inicializaTime(&$array, $cid, $tid, $nome) {
    if (!isset($array[$cid][$tid])) {
        $array[$cid][$tid] = [
            'nome' => $nome, 'pontos' => 0, 'jogos' => 0, 
            'vitorias' => 0, 'empates' => 0, 'derrotas' => 0, 'saldo' => 0
        ];
    }
}

// Passo A: Coloca na tabela todos os times com inscrição aprovada
foreach ($inscricoes as $insc) {
    inicializaTime($classificacao, $insc['campeonato_id'], $insc['id'], $insc['nome']);
}

// Passo B: A MÁGICA SALVA-VIDAS! 
// Garante que os times que já têm jogos agendados também entrem na tabela, 
// mesmo que a inscrição deles tenha sumido do banco por algum motivo.
foreach ($todas_partidas as $p) {
    inicializaTime($classificacao, $p['campeonato_id'], $p['time_a_id'], $p['time_a']);
    inicializaTime($classificacao, $p['campeonato_id'], $p['time_b_id'], $p['time_b']);
}

// Passo C: Processa os pontos dos jogos reais
foreach ($todas_partidas as $p) {
    $cid = $p['campeonato_id'];
    $ida = $p['time_a_id'];
    $idb = $p['time_b_id'];

    // Só faz a matemática se o jogo já aconteceu (placares preenchidos)
    if ($p['placar_a'] !== null && $p['placar_b'] !== null) {
        
        $pa = (int)$p['placar_a'];
        $pb = (int)$p['placar_b'];

        // Soma os jogos e os saldos
        $classificacao[$cid][$ida]['jogos']++;
        $classificacao[$cid][$ida]['saldo'] += ($pa - $pb);
        
        $classificacao[$cid][$idb]['jogos']++;
        $classificacao[$cid][$idb]['saldo'] += ($pb - $pa);

        // Regra de Vitória (3 pts), Empate (1 pt) e Derrota (0 pts)
        if ($pa > $pb) {
            // Time A ganhou
            $classificacao[$cid][$ida]['pontos'] += 3; 
            $classificacao[$cid][$ida]['vitorias']++; 
            $classificacao[$cid][$idb]['derrotas']++; 
        } elseif ($pb > $pa) {
            // Time B ganhou
            $classificacao[$cid][$idb]['pontos'] += 3; 
            $classificacao[$cid][$idb]['vitorias']++; 
            $classificacao[$cid][$ida]['derrotas']++; 
        } else {
            // Empate
            $classificacao[$cid][$ida]['pontos'] += 1; 
            $classificacao[$cid][$ida]['empates']++; 
            $classificacao[$cid][$idb]['pontos'] += 1; 
            $classificacao[$cid][$idb]['empates']++; 
        }
    }
}

// Passo D: Ordena o Ranking (1º Pontos, 2º Vitórias, 3º Saldo de Gols)
foreach ($classificacao as $cid => $times_do_campeonato) {
    // Remove os IDs do array para a função usort() funcionar perfeitamente
    $array_limpo = array_values($times_do_campeonato);
    
    usort($array_limpo, function($a, $b) {
        if ($a['pontos'] !== $b['pontos']) return $b['pontos'] - $a['pontos'];
        if ($a['vitorias'] !== $b['vitorias']) return $b['vitorias'] - $a['vitorias'];
        return $b['saldo'] - $a['saldo'];
    });
    
    // Salva o ranking final organizado
    $classificacao[$cid] = $array_limpo; 
}
?>