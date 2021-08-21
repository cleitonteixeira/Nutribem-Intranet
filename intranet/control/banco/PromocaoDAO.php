<?php
//echo '<pre>';
//var_dump($_POST);
//echo '</pre>';
require_once("../../control/arquivo/funcao/Dados.php");
require_once("../../control/arquivo/funcao/Outras.php");
require_once("../../control/banco/conexao.php");
require_once("../../control/classes/phpMailer/class.phpmailer.php");
if (!isset($_SESSION)) session_start();
$Demissao = '##/##/####';
$Admissao = '##/##/####';
$Salario = 'R$ #.###,##';
$SalarioS = 'R$ #.###,##';
$colSub = '##############';
$justificativa = '##############';
$justificativa1 = '##############';
$cargo = '##############';
$cargoS = '##############';
$orcamento = '##############';
$tipo = '##############';
if($_POST['motivo'] == "Substituição"):
    $conexao = conexao::getInstance();
    $sql = "SELECT ca.CodCargo, ca.Funcao, ca.Salario, cad.Nome, co.dDemissao, co.dAdmissao, his.Historico, his.Justificativa FROM cargo ca INNER JOIN colaborador col ON col.idColaborador = ? INNER JOIN contratacao co ON co.idContratacao = col.Contratacao_idContratacao INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN historico his ON his.Colaborador_idColaborador = col.idColaborador WHERE ca.idCargo = co.Cargo_idCargo AND his.Historico = 'DemissÃ£o'";
    $stm = $conexao->prepare($sql);
    $stm->bindValue(1, $_POST['subs']);
    $stm->execute();
    $row = $stm->fetch(PDO::FETCH_OBJ);
    $Demissao = date('d/m/Y', strtotime($row->dDemissao));
    $Admissao = date('d/m/Y', strtotime($row->dAdmissao));
    $Salario = "R$ ". number_format($row->Salario,2,',','.');
    $colSub = utf8_decode($row->Nome);
    $justificativa = utf8_decode($row->Justificativa);
    $cargo = utf8_decode($row->CodCargo).' - '.$row->Funcao;
elseif($_POST['motivo'] == "Aumento de Quadro"):
    $justificativa1 = $_POST['justificativa'];
    $conexao = conexao::getInstance();
    $sql = "SELECT * FROM cargo WHERE idCargo = ?";
    $stm = $conexao->prepare($sql);
    $stm->bindValue(1, $_POST['cargoP']);
    $stm->execute();
    $row = $stm->fetch(PDO::FETCH_OBJ);
    $cargoS = $row->CodCargo."-".$row->Funcao;
    $SalarioS = "R$ ". number_format($row->Salario,2,',','.');
endif;
$orcamento = $_POST['orcamento'];
$data_envio = date('d/m/Y');
$hora_envio = date('H:i:s');
$Nome       = $_POST['Registro']." - ".$_POST['Nome'];
$sql = "SELECT c.idColaborador, c.CodColaborador,u.Email, ca.Nome AS Unidade, un.idUnidade, ch.Usuario_idUsuario AS Responsavel, u.Nome AS Imediato FROM colaborador c INNER JOIN contratacao cn ON cn.idContratacao = c.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro INNER JOIN chefia ch ON ch.Colaborador_idColaborador = c.idColaborador INNER JOIN usuarios u ON  u.idusuarios = ch.Usuario_idUsuario WHERE c.CodColaborador = ?;"; 
$conexao = conexao::getInstance();
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $_POST['Registro']);
$stm->execute();
$row = $stm->fetch(PDO::FETCH_OBJ);
$resp = $row->Responsavel;
$email = $row->Email;
$Imediato = utf8_decode($row->Imediato);
$MsgEmail = "
  <style type='text/css'>
  body{
	  margin:0px;
	  font-family:Verdana;
	  font-size:12px;
	  color: #666666;
  }
  a{
  	color: #666666;
  	text-decoration: none;
  }
  a:hover{
  	color: #FF0000;
  	text-decoration: none;
  }
  .solicitante{
  	color: #FF0401;
  }
  </style>
    <html>
        <head>
        </head>
        <body>
            <h3><strong>Empregador: </strong>". $_POST['Empregador'] ."</h3>
            <h3><strong>CNPJ nº: </strong>". $_POST['cnpj'] ."</h3>
            <h3 class='solicitante'><strong>Solicitante: </strong>". utf8_decode($_SESSION['Nome']) ."</h3>
            <p><strong>Unidade: </strong>". utf8_decode($row->Unidade) ."</p>
            	<strong>Nome do Funcionário: </strong>".$Nome ."
            </p>
            <p>
            	<strong>Cargo: </strong>". $_POST['dCargo'] ."
            </p>
            <p>
            	<strong>Data de Admissão: </strong>". $_POST['aadmissao'] ."
            </p>
			
			<h3>
            	<strong>Tipo: </strong><small class='solicitante'>". $_POST['motivo'] ."</small>
            </h3>
			<p>
            	<strong>Tipo Vaga: </strong>". $_POST['tipoVaga'] ."
            </p>
            <p><strong>Perfil da vaga: </strong>". $_POST['perfilVaga'] ."</p>
            <p><strong>Data Prevista: </strong>". date("d/m/Y", strtotime($_POST['dataPev'])) ."</p>
            <p><strong>Esta solicitação está prevista no orçamento: </strong> $orcamento </p>
			
            <h3>Aumento de Quadro</h3>
            <p>
            <strong>Cargo: </strong> $cargoS
            <strong>Salário: </strong> $SalarioS
            </p>
            <p><strong>Justificativa: </strong>  $justificativa1</p>
            <h3>Substituição</h3>
            <p>
            <strong>Nome do Colaborador a ser substituido: </strong> $colSub

            <strong>Cargo: </strong> $cargo
            </p>
            <p>
            <strong>Admissão: </strong> $Admissao 

            <strong>Demissão: </strong> $Demissao

            <strong>Último Salário: </strong> $Salario
            </p>
            <p><strong>Justificativa da Demissão: </strong> $justificativa </p>

            <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
        </body>
    </html>
  ";

$TipoVaga 	= utf8_encode($_POST['tipoVaga']);
$DataPrev 	= $_POST['dataPev'];
$PerfilVaga = utf8_encode($_POST['perfilVaga']);
$Orcamento 	= utf8_encode($_POST['orcamento']);
$Motivo 	= utf8_encode($_POST['motivo']);
if($_POST['motivo'] === "Aumento de Quadro"):
	$JustificativaAum	= utf8_encode($_POST['justificativa']);
	$CargoP				= $_POST['cargoP'];
	$Substituicao		= 0;
else:
	$JustificativaAum 	= "* Sem dados *";
	$CargoP				= 0;
	$Substituicao		= $_POST['subs'];
endif;

try{
	$sql	= "INSERT INTO promocao (Colaborador_idColaborador, TipoVaga, DataPrev, PerfilVaga, Orcamento, Motivo, JustificativaAum, Cargo, ColSUb) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
	
	$stm = $conexao->prepare($sql);

	$stm->bindParam(1, $row->idColaborador);
	$stm->bindParam(2, $TipoVaga);
	$stm->bindParam(3, $DataPrev);
	$stm->bindParam(4, $PerfilVaga);
	$stm->bindParam(5, $Orcamento);
	$stm->bindParam(6, $Motivo);
	$stm->bindParam(7, $JustificativaAum);
	$stm->bindParam(8, $CargoP);
	$stm->bindParam(9, $Substituicao);
	$stm->execute();

	$IDPromocao = $conexao->lastInsertId();
	
}catch (PDOexception $error_insert){	
	
	echo 'Erro ao cadastrar: '.$error_insert->getMessage();
	exit;
}

$Data = date("Y-m-d");
$Tipo = "Promocao";
try{
	$email = new PHPMailer();
	$email->CharSet = 'UTF-8';
	$email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
	$email->FromName	= 'RH-Online';
	$email->Subject		= 'RH-Online: Requerimento de Promoção/Substituição';
	$email->IsHTML(true); // Define que o e-mail será enviado como HTML
	$email->Body		= $MsgEmail;

	//$email->AddAddress( 'cleitonteixeira@secservices.com.br' , 'Cleiton' );
	$email->AddAddress( $email , $Imediato );
	$email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
	$email->AddCC('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
	$email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
	$enviado = $email->Send();
	
	// Limpa os destinatários e os anexos
	$email->ClearAllRecipients();
	$email->ClearAttachments();
	// Exibe uma mensagem de resultado

	$sql = "INSERT INTO pendencias (Usuario_idUsuario, Responsavel_Colaborador, Unidade_idUnidade, Data, CodTipo, Tipo) VALUES (?, ?, ?, ?, ?, ?);";
	$stm = $conexao->prepare($sql);

	$stm->bindParam(1, $_SESSION['idusuarios']);
	$stm->bindParam(2, $resp);
	$stm->bindParam(3, $row->idUnidade);
	$stm->bindParam(4, $Data);
	$stm->bindParam(5, $IDPromocao);
	$stm->bindParam(6, $Tipo);
	$stm->execute();
	$Pendencia = $conexao->lastInsertId();

	if ($enviado) {
		echo "<script>alert('Pendência Cadastrada com Sucesso! E-mail enviado com sucesso!');window.location.href='".BASE."formularios/ValidaPendencia.php?p=".$Pendencia."';</script>";exit;
	} else {
		echo "<script>alert('Pendência Cadastrada com Sucesso! Falha ao enviar e-mail!');window.location.href='".BASE."formularios/ValidaPendencia.php?p=".$Pendencia."';</script>";exit;
	}

}catch (PDOexception $error_insert){	
	echo "<script>alert('Erro ao cadastrar ".$error_insert->getMessage().");window.location.href='".BASE.";</script>";exit;
}exit;
?>
