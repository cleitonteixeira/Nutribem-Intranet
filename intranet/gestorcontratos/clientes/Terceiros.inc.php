<?php
    require_once("../control/banco/conexao.php");
    require_once("../control/arquivo/funcao/Dados.php");
	
	function getProposta($x){
		$conexao = conexao::getInstance();
        $sql = "SELECT idProposta, dProposta, nProposta FROM proposta WHERE Contratante_idContratante = ? AND Consolidada = 'S';";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
		$Dados = array();
		while($row = $stm->fetch(PDO::FETCH_OBJ)){
			$Itens = [
                "idProposta" => utf8_decode($row->idProposta),
                "dProposta" => utf8_decode(date("d/m/Y", strtotime($row->dProposta))),
                "nProposta" => utf8_decode($row->nProposta),
			];
			array_push($Dados, $Itens);
		}
		echo json_encode($Dados);
	}
	function getDProposta($x){
		$conexao = conexao::getInstance();
        $sql = 'SELECT ip.Servico, ip.ValorUni FROM proposta p INNER JOIN itensproposta ip ON ip.Proposta_idProposta = p.idProposta WHERE p.idProposta = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
		$Dados = array();
		while($row = $stm->fetch(PDO::FETCH_OBJ)){
			$Itens = [
                "Servico" => utf8_decode($row->Servico),
                "Valor" =>  "R$ ".number_format($row->ValorUni,2,',','.') ,
			];
			array_push($Dados, $Itens);
		}
		echo json_encode($Dados);
	}
	
	function getNContrato($x){
		$conexao = conexao::getInstance();
		$Ano = date("Y");
		$Cliente = str_pad($x, 3, 0, STR_PAD_LEFT);
		$Quant = 1;
		$nContrato = "CT.".$Cliente.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
        $sql = 'SELECT ct.nContrato FROM contratante c INNER JOIN contrato ct ON ct.Contratante_idContratante = c.idContratante = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
		$Dados = array();
		$nContratoL = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
			array_push($nContratoL,$row->nContrato);
		}
		while(in_array($nContrato, $nContratoL)){
			$Quant += 1;
			$nContrato = "CT.".$Cliente.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
		}
		$Itens = [
			"nContrato" => utf8_decode($nContrato),
		];
		array_push($Dados, $Itens);
		echo json_encode($Dados);
	}
	function getIProposta($x){
		$conexao = conexao::getInstance();
        $sql = 'SELECT * FROM proposta p WHERE p.idProposta = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
		$Dados = array();
		$row = $stm->fetch(PDO::FETCH_OBJ);
		$Itens = [
			"nProposta" => utf8_decode($row->nProposta),
			"tReajuste" => utf8_decode($row->tReajuste),
			"fMedicao" => utf8_decode($row->fMedicao),
			"Condicao" => utf8_decode($row->Condicao),
			"fPagamento" => utf8_decode($row->fPagamento),
			"tVigencia" => utf8_decode($row->pVigencia." meses.")
		];
		array_push($Dados, $Itens);
		echo json_encode($Dados);
	}
	
	if(isset($_POST['contratante'])){
		$valor = isset( $_POST['contratante'] ) ? (int)$_POST['contratante'] : 0;
		getProposta($valor);
	}
    if(isset($_POST['proposta'])){
		$valor = isset( $_POST['proposta'] ) ? (int)$_POST['proposta'] : 0;
		getDProposta($valor);
	
    }
    if(isset($_POST['iProposta'])){
		$valor = isset( $_POST['iProposta'] ) ? (int)$_POST['iProposta'] : 0;
		getIProposta($valor);
	}
	
?>