<?php
$troca = array('linha',',');
$Trocas = array(' ','-','.',')','(','/');
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$conexao = conexao::getInstance();
// Atribui uma conexão PDO

if(isset($_POST['Prazo']) && $_POST['Prazo']  ==  "Alteracao"){
    try{
        $conexao->beginTransaction();
        
        $Prazo = anti_injection(utf8_encode($_POST['dias']));
        $idUsuario = anti_injection(utf8_encode($_POST['idusuarios']));
        $SQL = "UPDATE PrazoLancamento SET Prazo = ? WHERE Usuario_idUsuario = ?;";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Prazo);
        $stmt->bindParam(2, $idUsuario);
        $stmt->execute();
        
        $conexao->commit();
		echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Sucesso ao Alterar Prazo!</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'lancamento/PrazoLancamento.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'lancamento/PrazoLancamento.php');exit;
	}catch(PDOException $er){
        $conexao->rollBack();
		echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao Alterar Prazo</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$er.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'lancamento/PrazoLancamento.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'lancamento/PrazoLancamento..php');exit;
	}

}else{
    header("Location: ".BASE);
}
?>