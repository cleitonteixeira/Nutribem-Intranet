<?php
    require_once("../control/banco/conexao.php");
    function getAsos($u){
    	$conexao = conexao::getInstance();
		$sql = "SELECT cn.idContratacao, co.CodColaborador, (SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = cn.Unidade_idUnidade) AS Unidade, ca.Nome, cn.dAso, cg.Funcao FROM contratacao cn INNER JOIN colaborador co ON co.Contratacao_idContratacao = cn.idContratacao INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro INNER JOIN cargo cg ON cg.idCargo = cn.Cargo_idCargo WHERE cg.Unidade_idUnidade = ? AND cn.dDemissao IS NULL ORDER BY cn.dAso ASC LIMIT 20;";
		$stm = $conexao->prepare($sql);
		$stm->bindParam(1, $u);
		$stm->execute();
		$rs = $stm->fetchAll(PDO::FETCH_OBJ);
        $Dados = array();
		foreach($rs AS $r):
			$dAso = date('Y-m-d', strtotime('+1 year', strtotime($r->dAso)));
			$data = strtotime((date('Y-m-d', strtotime('+1 year', strtotime($r->dAso)))));
			$data1 = strtotime(date('Y-m-d'));
			$diferenca = $data - $data1;
			$dias = (int)floor( $diferenca / (60 * 60 * 24));
			if($dias < 45):
				$Itens = [
					"Colaborador" => $r->CodColaborador.'-'.utf8_decode($r->Nome),
					"Cargo" => utf8_decode($r->Funcao),
					"Unidade" => utf8_decode($r->Unidade),
					"DataAso" => date('d/m/Y', strtotime($r->dAso)),
					"Dias" => $dias
				];
				array_push($Dados, $Itens);
			else:
				continue;
			endif;
            
		endforeach;
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    
    if(isset($_GET['u'])):
        $valor = isset( $_GET['u'] ) ? (int)$_GET['u'] : 0;
        getAsos($valor);
   	endif;
?>