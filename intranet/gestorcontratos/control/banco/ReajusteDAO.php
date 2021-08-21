<?php
if (!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$Trocas = array(' ','-','.',')','(','/');
$troca = array('linha', 'line',',');
$conexao = conexao::getInstance();
if(isset($_POST['Cliente']) && $_POST['Cliente']  ==  "Reajuste"){
	try{
        $conexao->beginTransaction();
		$sql = "SELECT * FROM itensproposta WHERE Proposta_idProposta = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $_POST['Proposta']);
        $stmt->execute();
        $rp = $stmt->fetchAll(PDO::FETCH_OBJ);
        $descricao = "<table class='table table-bordered'><thead><tr><th>Serviço</th><th>Valor Atual</th></tr></thead><tbody>";	
		foreach($rp as $r ){
            $descricao .= "<tr><td>".$r->Servico."</td><td>R$ ".$r->ValorUni." + ".$_POST['re'.$r->idItensProposta]."% = <strong>R$ ".$_POST['al'.$r->idItensProposta]."</strong></td></tr>";
			$Valor = str_replace(".","",$_POST['al'.$r->idItensProposta]);
			$Valor = str_replace(",",".", $Valor);
			$sql = "UPDATE itensproposta SET ValorUni = ? WHERE idItensProposta = ? AND Proposta_idProposta = (SELECT Proposta_idProposta FROM contrato WHERE idContrato = ?);";
			$stmt = $conexao->prepare($sql);
			$stmt->bindParam(1, $Valor);
			$stmt->bindParam(2, $r->idItensProposta);
			$stmt->bindParam(3, $_POST['Contrato']);
			$stmt->execute();
		}
		$sql = "SELECT c.Contratante_idContratante, c.DataReajuste, p.tReajuste FROM contrato c INNER JOIN proposta p ON p.idProposta = c.Proposta_idProposta WHERE idContrato = ?;";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $_POST['Contrato']);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_OBJ);
		switch ($row->tReajuste){
			case("Trimestral"):
				$tR = 3;
				break;
			case("Semestral"):
				$tR = 6;
				break;
			case("Anual"):
				$tR = 12;
				break;
			case("Bienal"):
				$tR = 24;
				break;
		}
		$dReajuste = date("Y-m-d", strtotime("+".$tR." month", strtotime($row->DataReajuste)));
		$tipo = utf8_encode("Reajuste");
		$data = date("Y-m-d", strtotime($_POST['dataReajuste']));
		$descricao .= '</tbody><tfoot><tr><td colspan="2"><strong>Próximo Reajuste: '.date("d/m/Y", strtotime($dReajuste)).'.</strong></td></tr></tfoot></table>';
		$descricao = utf8_encode($descricao);
		$sql = "INSERT INTO historial (Contratante_idContratante, Usuario_idUsuario, Tipo, DataVis, Descricao, DataCad) VALUES (?, ?, ?, ?, ?, ?);";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $row->Contratante_idContratante);
		$stmt->bindParam(2, $_SESSION['idusuarios']);
		$stmt->bindParam(3, $tipo);
		$stmt->bindParam(4, $data);
		$stmt->bindParam(5, $descricao);
		$stmt->bindParam(6, $data);
		$stmt->execute();
		$sql = "UPDATE contrato SET DataReajuste = ? WHERE idContrato = ?;";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $dReajuste);
		$stmt->bindParam(2, $_POST['Contrato']);
		$stmt->execute();
		
		$sql = "SELECT Servico, ValorUni FROM itensproposta WHERE Proposta_idProposta = (SELECT Proposta_idProposta FROM contrato WHERE idContrato = ?)";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $_POST['Contrato']);
		$stmt->execute();
		$case = '';
		while($r = $stmt->fetch(PDO::FETCH_OBJ)){
			$case .= "WHEN Servico = '".$r->Servico."' THEN ".$r->ValorUni." ";
		}
		$sql = "UPDATE lancamento SET ValorUni = CASE ".$case."	END WHERE Contrato_idContrato IN (SELECT idContrato FROM contrato WHERE Proposta_idProposta = (SELECT Proposta_idProposta FROM contrato WHERE idContrato = ?)) AND dLancamento >= ?";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $_POST['Contrato']);
		$stmt->bindParam(2, $data);
		$stmt->execute();
        $conexao->commit();
		echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Sucesso ao fazer o Reajuste!</p>
				<p>Foi alterado '.$stmt->rowCount().' registro(s).</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/Reajuste.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'clientes/Reajuste.php');exit;
	}catch(PDOException $er){
        $conexao->rollBack();
		echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao tentar realizar Reajuste...</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$er.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/Reajuste.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 10;URL='.BASE.'clientes/Reajuste.php');exit;
	}
}