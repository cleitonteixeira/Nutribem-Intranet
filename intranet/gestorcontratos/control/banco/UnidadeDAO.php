<?php
$troca = array('linha',',');
$Trocas = array(' ','-','.',')','(','/');
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
// Atribui uma conexão PDO
$x = 0;
$conexao = conexao::getInstance();
$Celular = "99999999999";
$Telefone = "9999999999";
$Email = "Nenhum Registro";
if(isset($_POST['Unidade']) && $_POST['Unidade']  ==  "Cadastro"){
    try{
        $conexao->beginTransaction();
        
        $Nome = anti_injection(utf8_encode($_POST['nome']));
        $SQL = "INSERT INTO cadastro (Nome) VALUES (?)";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Nome);
        $Cadastro = $stmt->execute();
        $IdCadastro = $conexao->lastInsertId();
        
        $SQL = "INSERT INTO unidadefaturamento ( Cadastro_idCadastro, Fornecimento_idFornecimento  ) VALUES (?,?);";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $IdCadastro);
        $stmt->bindParam(2, $_POST['UnidadeF']);
        $Unidade = $stmt->execute();
        $UnidadeID = $conexao->lastInsertId();
        $conexao->commit();
		echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Sucesso ao Cadastrar Unidade de Fornecimento!</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'extras/UsuariosUnidade.php?un='.$UnidadeID.'">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'extras/UsuariosUnidade.php?un='.$UnidadeID);exit;
	}catch(PDOException $er){
        $conexao->rollBack();
		echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao Cadastrar Unidade de Fornecimento</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$er.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/Reajuste.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'cadastros/Unidade.php');exit;
	}

}else{
    header("Location: ".BASE);
}
?>