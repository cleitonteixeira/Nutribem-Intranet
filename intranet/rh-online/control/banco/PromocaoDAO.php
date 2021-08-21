<?php
if (!isset($_SESSION)) session_start();
require_once("../../control/banco/conexao.php");
$conexao        = conexao::getInstance();
require_once("../../control/arquivo/funcao/Dados.php");
require_once("../../control/arquivo/funcao/Outras.php");
require_once("../../control/classes/phpMailer/class.phpmailer.php");
require_once("../../control/arquivo/header/Header.php");
error_reporting(true);
/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/
$data_envio = date('d/m/Y');
$hora_envio = date('H:i:s');
$motivo         = "";
$Demissao       = "###";    
$Admissao       = "###";    
$Salario        = "###";    
$colSub         = "###";
$justificativa  = "###";    
$cargo          = "###";
$cargoS         = "###";
$SalarioS       = "###";
$jusAum         = "###";
$aSalario       = "###";
$jusASal        = "###";
switch($_POST['motivo']){
    case("aQuadro"):
        $motivo = "Aumento de Quadro";
        $sql            = "SELECT * FROM cargo WHERE idCargo = ?";
        $stm            = $conexao->prepare($sql);
        $stm->bindValue(1, $_POST['cargoP']);
        $stm->execute();
        $row            = $stm->fetch(PDO::FETCH_OBJ);
        $cargoS         = $row->CodCargo."-".$row->Funcao;
        $SalarioS       = "R$ ". number_format($row->Salario,2,',','.');
        $jusAum = $_POST['justificativa'];
        break;
    case("aSalario"):
        $motivo = "Aumento de Salário";
        $jusASal = $_POST['justificativa1'];
        //$aSalario = "R$ ". number_format($_POST['sAumento'],2,'.',',');
        $aSalario = "R$ ". $_POST['sAumento'];
        break;
    case("substituicao"):
        $motivo = "Substituição";
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
        break;
}
$Nome       = $_POST['Registro']." - ".$_POST['Nome'];
$sql = "SELECT c.idColaborador, c.CodColaborador,u.Email, ca.Nome AS Unidade, un.idUnidade, ch.Usuario_idUsuario AS Responsavel, u.Nome AS Imediato FROM colaborador c INNER JOIN contratacao cn ON cn.idContratacao = c.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro INNER JOIN chefia ch ON ch.Colaborador_idColaborador = c.idColaborador INNER JOIN usuarios u ON  u.idusuarios = ch.Usuario_idUsuario WHERE c.CodColaborador = ?;"; 
$conexao = conexao::getInstance();
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $_POST['Registro']);
$stm->execute();
$row        = $stm->fetch(PDO::FETCH_OBJ);
$resp       = $row->Responsavel;
$email      = $row->Email;
$Imediato   = utf8_decode($row->Imediato);
$IDUnidade  = $row->idUnidade;
$orcamento  = $_POST['orcamento'];

$MsgEmail = "<html><head></head><body><style type='text/css'>body{margin:0px;font-family:Verdana;font-size:12px;color: #666666;}a{color: #666666;text-decoration: none;}a:hover{color: #FF0000;text-decoration: none;}.solicitante{color: #FF0401;}</style>";
$MsgEmail .= "<h3><strong>Empregador: </strong>". $_POST['Empregador'] ."</h3>";
$MsgEmail .= "<h3><strong>CNPJ nº: </strong>". $_POST['cnpj'] ."</h3>";
$MsgEmail .= "<h3><strong>Solicitante: </strong><span  class='solicitante'>". utf8_decode($_SESSION['Nome']) ."</span></h3>";
$MsgEmail .= "<p><strong>Unidade: </strong>". utf8_decode($row->Unidade) ."</p>";
$MsgEmail .= "<p><strong>Nome do Colaborador: </strong>".$Nome ."</p>";
$MsgEmail .= "<p><strong>Cargo: </strong>". $_POST['dCargo'] ."</p>";
$MsgEmail .= "<p><strong>Data de Admissão: </strong>". $_POST['aadmissao'] ."</p>";
$MsgEmail .= "<h3><strong>Tipo: </strong><small class='solicitante'>". $motivo ."</small></h3>";
$MsgEmail .= "<p><strong>Tipo Vaga: </strong>". $_POST['tipoVaga'] ."</p>";
$MsgEmail .= "<p><strong>Perfil da vaga: </strong>". $_POST['perfilVaga'] ."</p>";
$MsgEmail .= "<p><strong>Data Prevista: </strong>". date("d/m/Y", strtotime($_POST['dataPev'])) ."</p>";
$MsgEmail .= "<p><strong>Esta solicitação está prevista no orçamento: </strong> ". $orcamento ." </p>";
$MsgEmail .= "<h3>Aumento de Quadro</h3>";
$MsgEmail .= "<p><strong>Cargo: </strong> ". $cargoS ."<strong> Salário: </strong> ". $SalarioS ."</p>";
$MsgEmail .= "<p><strong>Justificativa: </strong>". $jusAum ."</p>";
$MsgEmail .= "<h3>Substituição</h3>";
$MsgEmail .= "<p><strong>Nome do Colaborador a ser substituido: </strong> ". $colSub ."<strong> Cargo: </strong>". $cargo ."</p>";
$MsgEmail .= "<p><strong>Admissão: </strong> ". $Admissao ." <strong> Demissão: </strong> ". $Demissao ." <strong> Último Salário: </strong> ". $Salario ;"</p>";
$MsgEmail .= "<p><strong>Justificativa da Demissão: </strong> ". $justificativa ." </p>";
$MsgEmail .= "<h3>Aumento de Salário</h3>";
$MsgEmail .= "<p><strong>Justificativa: </strong> ". $jusASal ."</p>";
$MsgEmail .= "<p><strong>Salário: </strong> ". $aSalario ."</p>";
$MsgEmail .= "<p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>";
$MsgEmail .= "</body></html>";

$motivo         = $_POST['motivo'];
$DataPrev       = $_POST['dataPev'];
$PerfilVaga     = utf8_encode($_POST['perfilVaga']);
$TipoVaga       = $_POST['tipoVaga'];

$CargoP         = "0";
$jusAum         = "* Sem dados *";
$jusASal        = "* Sem dados *";
$aSalario       = "0";
$colSub         = "0";
$orcamento      = utf8_encode($_POST['orcamento']);
switch($_POST['motivo']){
    case("aQuadro"):
        
        $CargoP     = $_POST['cargoP'];
        $jusAum     = utf8_encode($_POST['justificativa']);
        break;
    
    case("aSalario"):
    
        $jusASal    = utf8_encode($_POST['justificativa1']);
        $aSalario   = str_replace(".","",$_POST['sAumento']);
        $aSalario   = str_replace(",",".",$aSalario);
        break;
    
    case("substituicao"):
    
        $colSub  =  $_POST['subs'];
        break;
    
}
$Data = date("Y-m-d");
$Tipo = "Promocao";
$TipoVaga = utf8_encode($TipoVaga);
try{
    $sql = "INSERT INTO promocao(Colaborador_idColaborador, TipoVaga, DataPrev, PerfilVaga, Orcamento, Motivo, JustificativaAum, Cargo, ColSub, JustSalario, Salario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $row->idColaborador);
	$stm->bindParam(2, $TipoVaga);
	$stm->bindParam(3, $DataPrev);
	$stm->bindParam(4, $PerfilVaga);
	$stm->bindParam(5, $orcamento);
	$stm->bindParam(6, $motivo);
	$stm->bindParam(7, $jusAum);
	$stm->bindParam(8, $CargoP);
	$stm->bindParam(9, $colSub);
	$stm->bindParam(10, $jusASal);
	$stm->bindParam(11, $aSalario);
    
    $stm->execute();
    
    $IDPromocao = $conexao->lastInsertId();
    
	$email = new PHPMailer();
	$email->CharSet     = 'UTF-8';
	$email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
	$email->FromName	= 'RH-Online';
	$email->Subject		= 'RH-Online: Requerimento de Promocao / Substituicao / Aumento de Salario';
	$email->IsHTML(true);
    // Define que o e-mail será enviado como HTML
	$email->Body		= $MsgEmail;

	//$email->AddAddress( 'cleitonteixeira@secservices.com.br' , 'Cleiton' );
	if($_SESSION["idusuarios"] != 1){
        $email->AddAddress( $row->Email , $row->Imediato );
        $email->AddCC('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
        $email->AddCC('rh02@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
        $email->AddCC('patricia.lopes@nutribemrefeicoescoletivas.com.br', 'Patricia Lope - Nutribem Refeições Coletivas'); // Copia
        $email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
        $email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
        //$email->AddCC('estagiario.dp@nutribemrefeicoescoletivas.com.br', 'Estagiario(a) DP'); // Copia
        //$email->AddCC('estagiario.rh@nutribemrefeicoescoletivas.com.br', 'Estagiario(a) RH');
    }else{
        $email->AddAddress( 'cleitonteixeirasantos@gmail.com' , 'Cleiton Teixeira' );
        $email->AddCC('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
    }
    $enviado = $email->Send();
	
	// Limpa os destinatários e os anexos
	$email->ClearAllRecipients();
	$email->ClearAttachments();
	// Exibe uma mensagem de resultado
    $chefia = array(4,5);
    
    if(in_array($_SESSION['idusuarios'], $chefia)){
        $result = "Aprovada";
        $sql = "INSERT INTO pendencias (Usuario_idUsuario, Responsavel_Colaborador, Unidade_idUnidade, Data, CodTipo, Tipo, Resultado) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $_SESSION['idusuarios']);
        $stm->bindParam(2, $resp);
        $stm->bindParam(3, $IDUnidade);
        $stm->bindParam(4, $Data);
        $stm->bindParam(5, $IDPromocao);
        $stm->bindParam(6, $Tipo);
        $stm->bindParam(7, $result);
        $stm->execute();
        $Pendencia = $conexao->lastInsertId();
        $data = date("Y-m-d");
        $hora = date("H:i:s");
        $Usuario = $_SESSION['idusuarios'];
        $sql = "INSERT INTO validapendencia (Usuario_idUsuario, Pendencia_idPendencia, Data, Hora, Voto) VALUES (?,?,?,?,?);";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $Usuario);
        $stmt->bindParam(2, $Pendencia);
        $stmt->bindParam(3, $data);
        $stmt->bindParam(4, $hora);
        $stmt->bindParam(5, $result);
        $stmt->execute();
    }else{
        $sql = "INSERT INTO pendencias (Usuario_idUsuario, Responsavel_Colaborador, Unidade_idUnidade, Data, CodTipo, Tipo) VALUES (?, ?, ?, ?, ?, ?);";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $_SESSION['idusuarios']);
        $stm->bindParam(2, $resp);
        $stm->bindParam(3, $IDUnidade);
        $stm->bindParam(4, $Data);
        $stm->bindParam(5, $IDPromocao);
        $stm->bindParam(6, $Tipo);
        $stm->execute();
        $Pendencia = $conexao->lastInsertId();
    }

	if ($enviado) {
        
         echo '
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong> Pendência Cadastrada!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/ValidaPendencia.php?p='.$Pendencia.'">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'formularios/ValidaPendencia.php?p='.$Pendencia);exit;
        
	} else {
		echo '
            <div class="alert alert-warning">
                <p><strong>Sucesso!</strong> Pendência Cadastrada! E-mail não enviado!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/ValidaPendencia.php?p='.$Pendencia.'">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'formularios/ValidaPendencia.php?p='.$Pendencia);exit;
    }
}catch (PDOException $error_insert){	
	echo "<script>alert('Erro ao cadastrar ".$error_insert->getMessage().");window.location.href='".BASE.";</script>";exit;
}exit;
?>