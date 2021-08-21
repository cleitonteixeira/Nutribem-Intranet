<?php
if (!isset($_SESSION)) session_start();
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Outras.php");
require_once("../control/classes/phpMailer/class.phpmailer.php");
function SalvaValorOS($idOs, $idUnidade, $idItem, $valor)
{
	$conexao = conexao::getInstance();
    $conexao->beginTransaction();
    $dados = array();
	try{
		$sql = "SELECT * FROM custoos WHERE OS_idOS = ? AND Item_idItem = ? AND Unidade_idUnidade = ?;";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $idOs);
		$stmt->bindParam(2, $idItem);
		$stmt->bindParam(3, $idUnidade);
		$stmt->execute();
		if($stmt->rowCount() === 0){
			$sql = "INSERT INTO custoos(OS_idOS, Item_idItem, Usuario_idUsuario, Unidade_idUnidade, Valor) VALUES (?,?,?,?,?);";
			$stmt = $conexao->prepare($sql);
			$stmt->bindParam(1, $idOs);
			$stmt->bindParam(2, $idItem);
			$stmt->bindParam(3, $_SESSION['idusuarios']);
			$stmt->bindParam(4, $idUnidade);
			$stmt->bindParam(5, $valor);
			$stmt->execute();
			$idCusto = $conexao->lastInsertId();
			$data = date("Y-m-d");
			$TipoMovimenta = utf8_encode("Cadastro");
			$sql = "INSERT INTO logcustoos(Usuario_idUsuario, Custo_idCusto, vAnterior, vAtual, DataMovimenta, TipoMovimenta) VALUES (?,?,?,?,?,?);";
			$stmt = $conexao->prepare($sql);
			$stmt->bindParam(1, $_SESSION['idusuarios']);
			$stmt->bindParam(2, $idCusto);
			$stmt->bindParam(3, $valor);
			$stmt->bindParam(4, $valor);
			$stmt->bindParam(5, $data);
			$stmt->bindParam(6, $TipoMovimenta);
			$stmt->execute();

		}else{
			$rt = $stmt->fetch(PDO::FETCH_OBJ);
			$sql = "UPDATE custoos SET Valor = ? WHERE OS_idOS = ? AND Item_idItem = ? AND Unidade_idUnidade = ?;";
			$stmt = $conexao->prepare($sql);
			$stmt->bindParam(1, $valor);
			$stmt->bindParam(2, $idOs);
			$stmt->bindParam(3, $idItem);
			$stmt->bindParam(4, $idUnidade);
			$stmt->execute();
			$idCusto = $conexao->lastInsertId();
			$data = date("Y-m-d");
			$TipoMovimenta = utf8_encode("Alteração");
			$sql = "INSERT INTO logcustoos(Usuario_idUsuario, Custo_idCusto, vAnterior, vAtual, DataMovimenta, TipoMovimenta) VALUES (?,?,?,?,?,?);";
			$stmt = $conexao->prepare($sql);
			$stmt->bindParam(1, $_SESSION['idusuarios']);
			$stmt->bindParam(2, $rt->idCustoOS);
			$stmt->bindParam(3, $rt->Valor);
			$stmt->bindParam(4, $valor);
			$stmt->bindParam(5, $data);
			$stmt->bindParam(6, $TipoMovimenta);
			$stmt->execute();

		}
		$conexao->commit();
		$sql  = "SELECT IFNULL(SUM(Valor),0) AS Valor FROM custoos WHERE OS_idOS = ?;";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $idOs);
		$stmt->execute();
		$Valor = $stmt->fetch(PDO::FETCH_OBJ);

		$sql = "SELECT l.vAnterior, l.vAtual, l.DataMovimenta, l.TipoMovimenta, u.Nome AS Alterador, up.Nome AS Responsavel, eq.Codigo, ce.Nome AS Equipamento FROM logcustoos l INNER JOIN usuarios u ON u.idusuarios = l.Usuario_idUsuario INNER JOIN custoos co ON co.idCustoOS = l.Custo_idCusto INNER JOIN itemos io ON io.idItemOS = co.Item_idItem INNER JOIN equipamento eq ON eq.idEquipamento = io.Equipamento_idEquipamento INNER JOIN cadastro ce ON ce.idCadastro = eq.Cadastro_idCadastro INNER JOIN usuarios up ON up.idusuarios = co.Usuario_idUsuario WHERE Custo_idcusto IN (SELECT idCustoOS FROM custoos WHERE OS_idOS = ?) ORDER BY l.idlogCustoOS DESC;";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $idOs);
		$stmt->execute();
		ob_start();
		while($x = $stmt->fetch(PDO::FETCH_OBJ)){
		?>
		<tr>
		  <td><?=utf8_decode($x->Responsavel);?></td>
		  <td><?=utf8_decode($x->Alterador);?></td>
		  <td><?=utf8_decode($x->TipoMovimenta);?></td>
		  <td><?=date("d/m/Y", strtotime($x->DataMovimenta));?></td>
		  <td>R$ <?=number_format($x->vAnterior,"2",",",".");?></td>
		  <td>R$ <?=number_format($x->vAtual,"2",",",".");?></td>
		  <td><?=utf8_decode($x->Codigo)." - ".utf8_decode($x->Equipamento);?></td>
		</tr>
		<?php
		}
		$log = ob_get_clean();
		$html = ["Dados" => "Sucesso", "Valor" => number_format($Valor->Valor,2,',','.'),"LogCusto" => $log];
	}catch(PDOException $e){
		$data_envio     = date('d/m/Y');
        $hora_envio     = date('H:i:s');
        $MsgEmail = "
        <html>
            <head>
            </head>
            <body>
                <table width='100%' border='0' cellspacing='0' cellpadding='20 background='".BASE."img/Fundo.png'>
                    <tr>
                        <td>
                            <p>".$e->getMessage()."</p>
                            <br />
                            <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
                        </td>
                    </tr>
                </table>
            </body>
        </html>
        ";
        
        //INICIO ENVIO DE E-MAIL
        $email = new PHPMailer();
        $email->CharSet = 'UTF-8';
        $email->From        = 'contato@nutribemrefeicoescoletivas.com.br';
        $email->FromName    = 'Manutencao On-line';
        $email->Subject     = 'Manutencao On-line: ERRO ADICIONAR VALOR OS';
        $email->IsHTML(true); // Define que o e-mail será enviado como HTML
        $email->Body        = $MsgEmail;
        $email->AddAddress( 'ti@nutribemrefeicoescoletivas.com.br' , 'TI Nutribem' );
        
        $enviado = $email->Send();
        // Limpa os destinatários e os anexos
        $email->ClearAllRecipients();
        $email->ClearAttachments();
        // Exibe uma mensagem de resultado
        //FIM ENVIO DE E-MAIL
		$conexao->rollback();
		$sql  = "SELECT IFNULL(SUM(Valor),0) AS Valor FROM custoos WHERE OS_idOS = ?;";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $idOs);
		$stmt->execute();
		$Valor = $stmt->fetch(PDO::FETCH_OBJ);
		$sql = "SELECT l.vAnterior, l.vAtual, l.DataMovimenta, l.TipoMovimenta, u.Nome AS Alterador, up.Nome AS Responsavel, eq.Codigo, ce.Nome AS Equipamento FROM logcustoos l INNER JOIN usuarios u ON u.idusuarios = l.Usuario_idUsuario INNER JOIN custoos co ON co.idCustoOS = l.Custo_idCusto INNER JOIN itemos io ON io.idItemOS = co.Item_idItem INNER JOIN equipamento eq ON eq.idEquipamento = io.Equipamento_idEquipamento INNER JOIN cadastro ce ON ce.idCadastro = eq.Cadastro_idCadastro INNER JOIN usuarios up ON up.idusuarios = co.Usuario_idUsuario WHERE Custo_idcusto IN (SELECT idCustoOS FROM custoos WHERE OS_idOS = ?) ORDER BY l.idlogCustoOS DESC;";
		$stmt = $conexao->prepare($sql);
		$stmt->bindParam(1, $idOs);
		$stmt->execute();
		ob_start();
		while($x = $stmt->fetch(PDO::FETCH_OBJ)){
		?>
		<tr>
		  <td class="FontP"><?=utf8_decode($x->Responsavel);?></td>
		  <td class="FontP"><?=utf8_decode($x->Alterador);?></td>
		  <td class="FontP"><?=utf8_decode($x->TipoMovimenta);?></td>
		  <td><?=date("d/m/Y", strtotime($x->DataMovimenta));?></td>
		  <td>R$ <?=number_format($x->vAnterior,"2",",",".");?></td>
		  <td>R$ <?=number_format($x->vAtual,"2",",",".");?></td>
		  <td class="FontP"><?=utf8_decode($x->Codigo)." - ".utf8_decode($x->Equipamento);?></td>
		</tr>
		<?php
		}
		$log = ob_get_clean();
		$html = ["Dados" => "Falha", "Valor" => number_format($Valor->Valor,2,',','.'),"LogCusto" => $log];
	}
	array_push($dados, $html);
	echo json_encode($dados);
}
if(isset($_POST['SalvaValor']) && $_POST['SalvaValor'] == 'Salvar'){
	$valor = (double)$_POST['ValorOS'];
	SalvaValorOS($_POST['idOS1'], $_POST['idUnidade1'], $_POST['idItemOS'],$valor);
}
?>