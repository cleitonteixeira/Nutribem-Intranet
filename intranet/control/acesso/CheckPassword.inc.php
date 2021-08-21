<?php
	if (!isset($_SESSION)) session_start();
    require_once("../banco/conexao.php");
    require_once("../arquivo/funcao/Dados.php");
    require_once("../arquivo/funcao/Outras.php");
    if(isset( $_POST['p'] )):
        $valor = isset( $_POST['p'] ) ? $_POST['p'] : 0;	
        getCol($valor);
    endif;
    function getCol($Senha){
        $conexao = conexao::getInstance();
        $senha = anti_injection(Cript($_SESSION['Login'],$Senha));
		$sql = "SELECT Senha FROM usuarios WHERE idusuarios = ? AND Senha = ? AND Ativo = 0 LIMIT 1;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $_SESSION['idusuarios']);
        $stm->bindValue(2, $senha);
        $stm->execute();
        $Dados = array();
		$row = $stm->fetch(PDO::FETCH_OBJ);
		if($stm->rowCount() == 1):
			$Itens = [
				"Retorno" => 1
			];
		else:
			$Itens = [
				"Retorno" => 0
			];
		endif;
		array_push($Dados, $Itens);
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;	
    }
    
    
?>