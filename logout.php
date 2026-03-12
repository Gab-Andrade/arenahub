<?php
// Inicia a sessão para o PHP saber qual sessão ele precisa destruir
session_start();

// Destrói todas as informações da sessão (o "crachá" do usuário)
session_destroy();

// Redireciona o usuário de volta para a página pública inicial
header("Location: index.php");
exit;
?>