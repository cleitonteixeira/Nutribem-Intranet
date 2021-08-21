<?php
    if (!isset($_SESSION)) session_start();
    require_once("../control/banco/conexao.php");
    require_once("../control/arquivo/funcao/Dados.php");
	function getConfirmaData($x){
		$conexao = conexao::getInstance();

        $data	  = date("Y-m-d");
        
        $sql 	= "SELECT * FROM PrazoLancamento WHERE Usuario_idUsuario = ? LIMIT 1";
        $stmt 	= $conexao->prepare($sql);
        $stmt->bindParam(1, $_SESSION['idusuarios']);
        $stmt->execute();
        $row 	= $stmt->fetch(PDO::FETCH_OBJ);
	    $dMax		 	= date("Y-m-d", strtotime("-".$row->Prazo." days", strtotime($data)));
		
		$dLancamento 	= $x;
		
		$Dados = array();
		$v = "V";
		$f = "F";
		$res = ["resultado" => $f];
		
		if($dLancamento >= $dMax && $dLancamento <= $data){
			$res = ["resultado" => $v];
        }else{
			$res = ["resultado" => $f];
		}
		array_push($Dados, $res);
		echo json_encode($Dados);
	}
   	if(isset($_POST['dLancamento'])){
        $valor = isset( $_POST['dLancamento'] ) ? $_POST['dLancamento'] : 0;
        getConfirmaData($valor);
    }

?>