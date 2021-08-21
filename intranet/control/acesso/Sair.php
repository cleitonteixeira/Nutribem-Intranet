<?php
    require_once("../arquivo/funcao/Outras.php");
	session_start(); // Inicia a sessão
	session_destroy(); // Destrói a sessão limpando todos os valores salvos
	header("Location:". BASE); exit; // Redireciona o visitante
?>