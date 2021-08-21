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
if(isset($_POST['Categoria']) && $_POST['Categoria']  ==  "Cadastrar"){
    try{
        $conexao->beginTransaction();
        
        $Categoria = utf8_encode(anti_injection($_POST['categoria']));
        $SQL = "INSERT INTO categoriaequipamento (Nome) VALUES (?);";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Categoria);
        $stmt->execute();
        $conexao->commit();
		echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Sucesso ao Cadastrar Categoria de Equipamento!</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'equipamento/Categoria.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'equipamento/Categoria.php');exit;
	}catch(PDOException $er){
        $conexao->rollBack();
		echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao Cadastrar Categoria de Equipamento!</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$er->getMessage().'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'equipamento/Categoria.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'equipamento/Categoria.php');exit;
	}

}else{
    header("Location: ".BASE);
}
?>