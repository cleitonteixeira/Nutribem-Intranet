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
        
        $Nome   = anti_injection(utf8_encode($_POST['nome']));
        $Grupo  = anti_injection(utf8_encode($_POST['Grupo']));

        $SQL = "INSERT INTO unidadefornecimento (Grupo_idGrupo, Nome) VALUES (?,?);";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Grupo);
        $stmt->bindParam(2, $Nome);
        $stmt->execute();
        $conexao->commit();
		echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Sucesso ao Cadastrar Unidade de Fornecimento!</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'cadastros/UnidadeFornecimento.php.">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'cadastros/UnidadeFornecimento.php');exit;
	}catch(PDOException $er){
        $conexao->rollBack();
		echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao Cadastrar Unidade de Fornecimento</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$er.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'cadastros/UnidadeFornecimento.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'cadastros/UnidadeFornecimento.php');exit;
	}

}else{
    header("Location: ".BASE);
}
?>