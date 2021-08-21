<?php
	session_start(); // Inicia a sessão
	session_destroy(); // Destrói a sessão limpando todos os valores salvos
	header("Location: Http://www.nutribemrefeicoescoletivas.com.br/intranet/");
	exit;
?>