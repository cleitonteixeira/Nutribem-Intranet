<?php
$troca = array('linha',',');
$Trocas = array(' ','-','.',')','(','/');
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
// Atribui uma conexão PDO
$x = 0;
$conexao = conexao::getInstance();

if(isset($_POST['CadAgenda']) && $_POST['CadAgenda']  ==  "Cadastrar"){
 	try{
        $conexao->beginTransaction();
        $data	= $_POST['DataEnvia'];
        $controle = count($_POST['manutencao'])-1;
        $sql = "INSERT INTO agenda ( Unidade_idUnidade, Manutencao_idManutencao, Data ) VALUES (?, ?, ?);";
        $sqlU = "UPDATE agenda SET Unidade_idUnidade = ? WHERE Manutencao_idManutencao = ? AND Data = ?;";
        $sqlS = "SELECT Unidade_idUnidade FROM agenda WHERE Manutencao_idManutencao = ? AND Data = ?;";
        for($x = 0; $x <= $controle; $x++ ){
        	$stm = $conexao->prepare($sqlS);
        	$stm->bindParam(1, $_POST['manutencao'][$x]);
        	$stm->bindParam(2, $data);
        	$stm->execute();
        	if( $stm->rowCount() > 0 ){
        		$stmt = $conexao->prepare($sqlU);
        	}else{
        		$stmt = $conexao->prepare($sql);
        	}
        	$stmt->bindParam(1, $_POST['unidade'][$x]);
        	$stmt->bindParam(2, $_POST['manutencao'][$x]);
        	$stmt->bindParam(3, $data);
        	$stmt->execute();
        }
        $conexao->commit();
        echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Agendado com sucesso!</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'agenda/CadastrarAgenda.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'equipamento/Categoria.php');exit;
    }catch(PDOException $e){
        $conexao->rollback();
        echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao salvar agenda!</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$er->getMessage().'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'agenda/CadastrarAgenda.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'agenda/CadastrarAgenda.php');exit;
    }
}else{
	header("Location: ".BASE);
}
?>