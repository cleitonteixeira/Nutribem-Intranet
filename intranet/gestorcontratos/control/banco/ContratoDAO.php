<?php
if (!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$Trocas = array(' ','-','.',')','(','/');
$troca = array('linha',',');
$conexao = conexao::getInstance();
/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/
if(isset($_POST['Cliente']) && $_POST['Cliente']  ==  "Contrato"){
	try{
        $conexao->beginTransaction();
		$Data  				= date("Y-m-d");
        $Proposta	  		= anti_injection(utf8_encode($_POST['Proposta']));
        $Contratante		= anti_injection(utf8_encode($_POST['CodContratante']));
        $Empresa			= anti_injection(utf8_encode($_POST['empresa']));
        $Unidade			= anti_injection(utf8_encode($_POST['unidade']));
        $Proposta			= anti_injection(utf8_encode($_POST['Proposta']));
        $dReajuste			= anti_injection(utf8_encode($_POST['dReajuste']));
        $UF					= anti_injection(utf8_encode($_POST['uf']));
        $VigenciaIni		= anti_injection(utf8_encode($_POST['VigenciaIni']));
        $VigenciaFim		= anti_injection(utf8_encode($_POST['VigenciaFim']));
        $nContrato			= anti_injection(utf8_encode($_POST['nContrato']));
        $cCusto   			= anti_injection(utf8_encode($_POST['cCusto']));
        $obs     			= anti_injection(utf8_encode($_POST['obs']));
        $pCompra   			= anti_injection(utf8_encode($_POST['pCompra']));
        $Fechamento			= anti_injection(utf8_encode($_POST['fechamento']));
        $Condicao			= anti_injection(utf8_encode($_POST['condicao']));
        $fPagamento			= anti_injection(utf8_encode($_POST['fPagamento']));
        $ConsumacaoMinima	= anti_injection(utf8_encode($_POST['cMinimo']));
		
		$SQL = "INSERT INTO contrato (Contratante_idContratante, Empresa_idEmpresa, Unidade_idUnidade, Proposta_idProposta, DataReajuste, UF, VigenciaIni, VigenciaFim, DCad, nContrato, cCusto, Obs, pCompra, Fechamento, fPagamento, Condicao, ConsumacaoMinima) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $conexao->prepare($SQL);
		$stmt->bindParam(1, $Contratante);
		$stmt->bindParam(2, $Empresa);
		$stmt->bindParam(3, $Unidade);
		$stmt->bindParam(4, $Proposta);
		$stmt->bindParam(5, $dReajuste);
		$stmt->bindParam(6, $UF);
		$stmt->bindParam(7, $VigenciaIni);
		$stmt->bindParam(8, $VigenciaFim);
		$stmt->bindParam(9, $Data);
		$stmt->bindParam(10, $nContrato);
		$stmt->bindParam(11, $cCusto);
		$stmt->bindParam(12, $obs);
		$stmt->bindParam(13, $pCompra);
		$stmt->bindParam(14, $Fechamento);
		$stmt->bindParam(15, $fPagamento);
		$stmt->bindParam(16, $Condicao);
		$stmt->bindParam(17, $ConsumacaoMinima);
		$Cadastro = $stmt->execute();
        $conexao->commit();
		echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Sucesso ao fazer o Cadastro!</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'pesquisas/DetalheCliente.php?cod='.$Contratante.'">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'pesquisas/DetalheCliente.php?cod='.$Contratante);exit;
	}catch(PDOException $erro_cad){
        $conexao->rollBack();
		echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao tentar realizar Cadastro...</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$erro_cad.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/Contrato.php?p='.$Proposta.'">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'clientes/Proposta.php');exit;
	}
}else{
	header("Location: ".BASE);
}
?>