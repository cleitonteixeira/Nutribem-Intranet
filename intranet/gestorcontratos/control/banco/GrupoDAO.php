<?php
if (!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$Trocas = array(' ','-','.',')','(','/');
$troca = array('linha',',');
$conexao = conexao::getInstance();
/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/
if(isset($_POST['Grupo']) && $_POST['Grupo']  ==  "Cadastro"){
	try{
        $conexao->beginTransaction();
      	$Nome	  		= anti_injection(utf8_encode($_POST['nome']));
        $Descricao		= anti_injection(utf8_encode($_POST['descricao']));
		
		$SQL = "INSERT INTO grupo (Nome, Descricao) VALUES (?, ?);";
		$stmt = $conexao->prepare($SQL);
		$stmt->bindParam(1, $Nome);
		$stmt->bindParam(2, $Descricao);
		$Cadastro = $stmt->execute();
        $conexao->commit();
		echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Sucesso ao fazer o Cadastro!</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'cadastros/Grupo.php?">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'cadastros/Grupo.php');exit;
	}catch(PDOException $erro_cad){
        $conexao->rollBack();
		echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao tentar realizar Cadastro...</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$erro_cad.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'cadastros/Grupo.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'cadastros/Grupo.php');exit;
	}
}else{
	header("Location: ".BASE);
}
?>