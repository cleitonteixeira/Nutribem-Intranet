<?php
    require_once("../control/banco/conexao.php");
    require_once("../control/arquivo/funcao/Dados.php");
	function Telefone($dados){
		$tel_array = str_split($dados);
		$contador = sizeof($tel_array);
		$x = 0;
		$Telefone = "(";
		while($x<=$contador){
			$Telefone .= $tel_array[$x];
			if($x == 1){
				$Telefone .= ") ";
			}
			if($x == 5){
				$Telefone .= "-";
			}
			$x += 1;
			if($x == $contador){
				break;
			}
		}
		return $Telefone;
	}
	function Celular($dados){
		$tel_array = str_split($dados);
		$contador = sizeof($tel_array);
		$x = 0;
		$Telefone = "(";
		while($x<=$contador){
			$Telefone .= $tel_array[$x];
			if($x == 1){
				$Telefone .= ") ";
			}
			if($x == 6){
				$Telefone .= "-";
			}
			$x += 1;
			if($x == $contador){
				break;
			}
		}
		return $Telefone;
	}
	function getContratoCom($c){
		$conexao = conexao::getInstance();
        $sql = 'SELECT * FROM contrato WHERE idContrato = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $c);
        $stm->execute();
        $rs = $stm->fetch(PDO::FETCH_OBJ);
        $Dados = array();
        $Itens = [
            "vInicio" => $rs->VigenciaIni,
            "vFinal" => $rs->VigenciaFim,
            "dReajuste" => $rs->DataReajuste,
            "Finalizado" => $rs->Finalizado,
        ];
        array_push($Dados, $Itens);
        echo json_encode($Dados);
        $pdo = null;	
    }
    function getCliente($c){
		$conexao = conexao::getInstance();
        $sql = 'SELECT c.idContratante, c.IE, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca WHERE c.idContratante = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $c);
        $stm->execute();
        $Dados = array();
        $row = $stm->fetch(PDO::FETCH_OBJ);
        $sql = "SELECT * FROM ccontratante WHERE Contratante_idContratante = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $row->idContratante);
        $stm->execute();
        $rx = $stm->fetchAll(PDO::FETCH_OBJ);
        foreach($rx as $c){
            if($c->Tipo == "Comercial"){
                $rComercial = utf8_decode($c->Responsavel);
                $eComercial = utf8_decode($c->Email);
                $tComercial = $c->Telefone;
            }else{
                $rFinanceiro = utf8_decode($c->Responsavel);
                $eFinanceiro = utf8_decode($c->Email);
                $tFinanceiro = $c->Telefone;
            }
        }
         if(empty($row->IE)){
            $IE = "ISENTO";
        }else{
            $IE = $row->IE;
        }
        $Itens = [
            "idContratante" => utf8_decode($row->idContratante),
            "Nome" => utf8_decode($row->Cliente),
            "CNPJ" => utf8_decode(CNPJ_Padrao(str_pad($row->CNPJ, 14,0,STR_PAD_LEFT))),
            "IE" => $IE,
            "Endereco" => stripslashes(utf8_decode($row->Endereco.", N&ordm;: ".$row->Numero.", ".$row->Bairro." - ".$row->Cidade."-".$row->UF." - CEP: ".CEP_Padrao(str_pad($row->CEP, 8, 0, STR_PAD_LEFT)))),
            "eCobranca" => stripslashes(utf8_decode($row->eCobranca.", N&ordm;: ".$row->nCobranca.", ".$row->bCobranca." - ".$row->cCobranca."-".$row->uCobranca." - CEP: ".CEP_Padrao(str_pad($row->ceCobranca, 8, 0, STR_PAD_LEFT)))),
            "rComercial" => utf8_decode($rComercial),
            "eComercial" => utf8_decode($eComercial),
            "tComercial" => strlen($tComercial) == 11 ? Celular($tComercial) : Telefone($tComercial),
            "rFinanceiro" => utf8_decode($rFinanceiro),
            "eFinanceiro" => utf8_decode($eFinanceiro),
            "tFinanceiro" => strlen($tFinanceiro) == 11 ? Celular($tFinanceiro) : Telefone($tFinanceiro)
        ];
        array_push($Dados, $Itens);
        //print_r($Dados);
        echo json_encode($Dados);
        $pdo = null;	
    }
    function getClienteEditar($c){
		$conexao = conexao::getInstance();
        $sql = 'SELECT c.idContratante, c.IE, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca WHERE c.idContratante = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $c);
        $stm->execute();
        $row = $stm->fetch(PDO::FETCH_OBJ);
        print_r($row);
        $Dados = array();
        $sql = "SELECT * FROM ccontratante WHERE Contratante_idContratante = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $row->idContratante);
        $stm->execute();
        $rx = $stm->fetchAll(PDO::FETCH_OBJ);
        foreach($rx as $c){
            if($c->Tipo == "Comercial"){
                $rComercial = utf8_decode($c->Responsavel);
                $eComercial = utf8_decode($c->Email);
                $tComercial = $c->Telefone;
            }else{
                $rFinanceiro = utf8_decode($c->Responsavel);
                $eFinanceiro = utf8_decode($c->Email);
                $tFinanceiro = $c->Telefone;
            }
        }
         if(empty($row->IE)){
            $IE = "ISENTO";
        }else{
            $IE = $row->IE;
        }
        $Itens = [
            "idContratante" => utf8_decode($row->idContratante),
            "Nome" => utf8_decode($row->Cliente),
            "CNPJ" => utf8_decode(CNPJ_Padrao(str_pad($row->CNPJ, 14,0,STR_PAD_LEFT))),
            "IE" => $IE,
            "Endereco" => stripslashes(utf8_decode($row->Endereco.", N&ordm;: ".$row->Numero.", ".$row->Bairro." - ".$row->Cidade."-".$row->UF." - CEP: ".CEP_Padrao(str_pad($row->CEP, 8, 0, STR_PAD_LEFT)))),
            "eCobranca" => stripslashes(utf8_decode($row->eCobranca.", N&ordm;: ".$row->nCobranca.", ".$row->bCobranca." - ".$row->cCobranca."-".$row->uCobranca." - CEP: ".CEP_Padrao(str_pad($row->ceCobranca, 8, 0, STR_PAD_LEFT)))),
            "rComercial" => utf8_decode($rComercial),
            "eComercial" => utf8_decode($eComercial),
            "tComercial" => strlen($tComercial) == 11 ? Celular($tComercial) : Telefone($tComercial),
            "rFinanceiro" => utf8_decode($rFinanceiro),
            "eFinanceiro" => utf8_decode($eFinanceiro),
            "tFinanceiro" => strlen($tFinanceiro) == 11 ? Celular($tFinanceiro) : Telefone($tFinanceiro)
        ];
        array_push($Dados, $Itens);
        echo json_encode($Dados);
        $pdo = null;	
    }
	function getNProposta($x){
		$conexao = conexao::getInstance();
		$Ano = date("Y");
		$Cliente = str_pad($x, 3, 0, STR_PAD_LEFT);
		$Quant = 1;
		$nProposta = "PD.".$Cliente.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
        $sql = "SELECT p.nProposta FROM contratante c INNER JOIN proposta p ON p.Contratante_idContratante = ? WHERE p.Consolidada = 'S';";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
		$Dados = array();
		
		$nPropostaL = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
			array_push($nPropostaL,$row->nProposta);
		}
		while(in_array($nProposta, $nPropostaL)){
			$Quant += 1;
			$nProposta = "PD.".$Cliente.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
		}
		$Itens = [
                "nProposta" => utf8_decode($nProposta),
		];
		array_push($Dados, $Itens);
		echo json_encode($Dados);
	}
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
	function getCProposta($x){
		$conexao = conexao::getInstance();
        $sql = 'SELECT ip.Servico, ip.ValorUni FROM proposta p INNER JOIN contrato ct ON ct.idContrato = ? INNER JOIN itensproposta ip ON ip.Proposta_idProposta = p.idProposta WHERE p.idProposta = ct.Proposta_idProposta;';
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
	function getData($x,$y,$z){
		$data = date("Y-m-d", strtotime($x));
		$data1 = date("Y-m-d", strtotime("+".$z." month", strtotime($data)));
		$data = date("Y-m-d", strtotime("+".$y." month", strtotime($data)));
		$Dados = array();
		$Itens = [
			"fVigencia" => utf8_decode($data),
			"dReajuste" => utf8_decode($data1)
		];
		array_push($Dados, $Itens);
		echo json_encode($Dados);
	}
	function getContrato($x){
		$conexao = conexao::getInstance();
        $sql = "SELECT cd.Nome AS Unidade, c.idContrato, c.nContrato AS Contrato, c.DCad AS DataCadastro, c.DataReajuste, c.VigenciaFim, c.cCusto, c.Finalizado FROM contrato c INNER JOIN unidadefaturamento uf ON uf.idUnidadeFaturamento = c.Unidade_idUnidade INNER JOIN cadastro cd ON cd.idCadastro = uf.Cadastro_idCadastro WHERE Contratante_idContratante = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
		$Dados = array();
		while($row = $stm->fetch(PDO::FETCH_OBJ)){
			$Itens = [
                "idContrato" => utf8_decode($row->idContrato),
                "DataCadastro" => utf8_decode(date("d/m/Y", strtotime($row->DataCadastro))),
                "DataReajuste" => utf8_decode(date("d/m/Y", strtotime($row->DataReajuste))),
                "DataVigencia" => utf8_decode(date("d/m/Y", strtotime($row->VigenciaFim))),
                "Unidade" => utf8_decode($row->Unidade),
                "cCusto" => utf8_decode($row->cCusto),
                "Contrato" => utf8_decode($row->Contrato),
                "Finalizado" => utf8_decode($row->Finalizado),
			];
			array_push($Dados, $Itens);
		}
		echo json_encode($Dados);
	}
   	//if(isset($_POST['cliente'])){
        //$valor = isset( $_POST['cliente'] ) ? (int)$_POST['cliente'] : 0;
        //getCliente($valor);
    //}
    if(isset($_POST['cliente'])){
        $valor = isset( $_POST['cliente'] ) ? (int)$_POST['cliente'] : 0;
        getCliente($valor);
    }
	if(isset($_POST['proposta'])){
		$valor = isset( $_POST['proposta'] ) ? (int)$_POST['proposta'] : 0;
		getNProposta($valor);
	}
	if(isset($_POST['gproposta'])){
		$valor = isset( $_POST['gproposta'] ) ? (int)$_POST['gproposta'] : 0;
		getProposta($valor);
	}
	if(isset($_POST['dproposta'])){
		$valor = isset( $_POST['dproposta'] ) ? (int)$_POST['dproposta'] : 0;
		getDProposta($valor);
	}
	if(isset($_POST['cproposta'])){
		$valor = isset( $_POST['cproposta'] ) ? (int)$_POST['cproposta'] : 0;
		getCProposta($valor);
	}
	if(isset($_POST['ncontrato'])){
		$valor = isset( $_POST['ncontrato'] ) ? (int)$_POST['ncontrato'] : 0;
		getNContrato($valor);
	}
	if(isset($_POST['iproposta'])){
		$valor = isset( $_POST['iproposta'] ) ? (int)$_POST['iproposta'] : 0;
		getIProposta($valor);
	}
	if(isset($_POST['gcontrato'])){
		$valor = isset( $_POST['gcontrato'] ) ? (int)$_POST['gcontrato'] : 0;
		getContrato($valor);
	}
    if(isset($_POST['dContrato'])){
		$valor = isset( $_POST['dContrato'] ) ? (int)$_POST['dContrato'] : 0;
		getContratoCom($valor);
	}
	if(isset($_POST['gData'])){
		$valor = isset( $_POST['gData'] ) ? $_POST['gData'] : 0;
		$valor1 = isset( $_POST['pVigencia'] ) ? (int)$_POST['pVigencia'] : 0;
		$valor2 = isset( $_POST['dReajuste'] ) ? (int)$_POST['dReajuste'] : 0;
		getData($valor,$valor1, $valor2);
	}
?>