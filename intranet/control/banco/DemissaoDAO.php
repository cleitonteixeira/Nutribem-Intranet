<?php
/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/
error_reporting(false);
require_once("../../control/arquivo/funcao/Dados.php");
require_once("../../control/arquivo/funcao/Outras.php");
require_once("../../control/banco/conexao.php");
require_once("../../control/classes/phpMailer/class.phpmailer.php");
if (!isset($_SESSION)) session_start();
$data_envio = date('d/m/Y');
$hora_envio = date('H:i:s');

$Nome           = $_POST['Registro']." - ".$_POST['Nome'];
$Cargo          = $_POST['dCargo'];
$DataAdmissao   = $_POST['admissao'];
$Tipo           = $_POST['tContrato'];
if(isset($_POST['periodo']) && $_POST['periodo'] != "" ){
	$Tipo       .= ": ".$_POST['periodo']." dias.";
}

$Justificativa  = $_POST['justificativa'];
$Exame          = $_POST['exame'];
if(isset($_POST['dExame']) && $_POST['dExame'] != "" ){
	$Exame      .= ", ".$_POST['dExame'];
}
$Doenca         = $_POST['doenca'];
if(isset($_POST['dDoenca']) && $_POST['dDoenca'] != "" ){
	$Doenca     .= ", ".$_POST['dDoenca'];
}
$adv            = $_POST['advSusp']; 
$promo          = $_POST['promo'];
$celular        = $_POST['celular'];
$computador     = $_POST['computador'];
$email          = $_POST['email'];
$estacionamento = $_POST['estacionamento'];
$ramal          = $_POST['ramal'];
$veiculo        = $_POST['veiculo'];

$Outros         = "###";
if(isset($_POST['outros']) && $_POST['outros'] != null){
	$Outros     = $_POST['outros'];
}
$sql = "SELECT c.idColaborador, u.Email, ca.Nome AS Unidade, un.idUnidade, ch.Usuario_idUsuario AS Responsavel FROM colaborador c INNER JOIN contratacao cn ON cn.idContratacao = c.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro INNER JOIN chefia ch ON ch.Colaborador_idColaborador = c.idColaborador INNER JOIN usuarios u ON  u.idusuarios = ch.Usuario_idUsuario WHERE c.CodColaborador = ?;"; 
$conexao = conexao::getInstance();
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $_POST['Registro']);
$stm->execute();
$row = $stm->fetch(PDO::FETCH_OBJ);

$MsgEmail = "
  <style type='text/css'>
  body {
  margin:0px;
  font-family:Verdana;
  font-size:12px;
  color: #666666;
  }
  a{
  color: #666666;
  text-decoration: none;
  }
  a:hover {
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
            <p>Funcionário: $Nome</p>
            <p><strong>Cargo: </strong>$Cargo</p>
            <p><strong>Data de Admissão: </strong>$DataAdmissao</p>
            <p><strong>Data de Demissão: </strong>". date('d/m/Y',strtotime($_POST['dDemissao'])) ."</p>
            <p><strong>Tipo do desligamento: </strong>". $_POST['tDesligamento'] ."</p>
            <p><strong>Tipo do Aviso: </strong>". $_POST['avisoPrevio'] ."</p>
            <p><strong>Data do Aviso: </strong>". date('d/m/Y',strtotime($_POST['dataAviso'])) ."</p>
            <p><strong>Contrato Tipo: </strong>$Tipo</p>
            <h3>Justificativa</h3>
            <p>$Justificativa</p>
            <h3>Medicina</h3>
            <p><strong>Exame: </strong>$Exame</p>
            <p><strong>Impedimento para desligamento: </strong>$Doenca</p>
            <p><strong>Advertência ou Suspenção: </strong>$adv</p>
            <p><strong>Promoção: </strong>$promo</p>
            <h3>Bens de Posse da Empresa</h3>
            <p><strong>Celular: </strong>$celular</p>
            <p><strong>Computador: </strong>$computador</p>
            <p><strong>E-mail: </strong>$email</p>
            <p><strong>Estacionamento: </strong>$estacionamento</p>
            <p><strong>Ramal: </strong>$ramal</p>
            <p><strong>Veiculo: </strong>$veiculo</p>
            <p><strong>Outros: </strong>$Outros</p>
            <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
        </body>
    </html>
  "; 

$Bens 	= 	"<li><strong>Celular:</strong> ".$celular."</li>";
$Bens 	.=	"<li><strong>Computador:</strong> ".$computador."</li>";
$Bens 	.=	"<li><strong>E-mail:</strong> ".$email."</li>";
$Bens 	.=	"<li><strong>Estacionamento:</strong> ".$estacionamento."</li>";
$Bens 	.=	"<li><strong>Ramal:</strong> ".$ramal."</li>";
$Bens 	.=	"<li><strong>Veiculo:</strong> ".$veiculo."</li>";

$Tipo 				= utf8_encode($Tipo);
$Justificativa 		= utf8_encode($Justificativa);
$Exame				= utf8_encode($Exame);
$Doenca				= utf8_encode($Doenca);
$adv				= utf8_encode($adv);
$promo				= utf8_encode($promo);
$Bens 				= utf8_encode($Bens);
$Outros				= utf8_encode($Outros);
$tipoDesligamento	= utf8_encode($_POST['tDesligamento']);
$tipoAviso			= utf8_encode($_POST['avisoPrevio']);
$dataAviso			= utf8_encode($_POST['dataAviso']);

try{
	$sql	= "INSERT INTO demissao (Colaborador_idColaborador, DataRecisao, TipoContrato, Justificativa, ExameEspe, Impedimento, AdvSup, Promocao, BensEmpresa, Outros, DataAviso, TipoDesligamento, TipoAviso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

	$stm = $conexao->prepare($sql);

	$stm->bindParam(1, $row->idColaborador);
	$stm->bindParam(2, $_POST['dDemissao']);
	$stm->bindParam(3, $Tipo);
	$stm->bindParam(4, $Justificativa);
	$stm->bindParam(5, $Exame);
	$stm->bindParam(6, $Doenca);
	$stm->bindParam(7, $adv);
	$stm->bindParam(8, $promo);
	$stm->bindParam(9, $Bens);
	$stm->bindParam(10, $Outros);
	$stm->bindParam(11, $dataAviso);
	$stm->bindParam(12, $tipoDesligamento);
	$stm->bindParam(13, $tipoAviso);
	$stm->execute();

	$IDDemissao = $conexao->lastInsertId();

}catch (PDOexception $error_insert){	

	echo 'Erro ao cadastrar '.$error_insert->getMessage();
	exit;
}

ob_start();
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>RH-Online</title>
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="shortcut icon" href="../img/Icone.png" type="image/x-icon" />

		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/app.js"></script>

		<link rel="stylesheet" href="../../css/pdf.css">
	</head>
	<body>
		<div style="position: absolute; left: 0; right: 0; top: 0; bottom: 0; z-index: 0;"><img style="opacity: 0.5;
			filter: alpha(opacity=50);width: 210mm; height: 297mm; margin: 0;" alt="" src="../../img/Marca.jpg" /></div>
		<div class="container-fluid" style="z-index: 100;">
			<div class="row" id="form-ferias">
				<div class="col-md-12 conteudo">
					<h1 class="text-center">Análise para Desligamento</h1>
					<br >
					<div class="text-center col-xs-12">
						<p><strong>Empregador: </strong><?php echo $_POST['Empregador']; ?></p>
						<p><strong>CNPJ nº: </strong><?php echo $_POST['cnpj']; ?></p>
					</div>
					<hr>

					<div class="text-justify col-xs-12">
						<p><strong>Nome do Funcionário: </strong><?php echo $_POST['Registro']; ?> - <?php echo $_POST['Nome']; ?></p>
						<p><strong>Cargo: </strong><?php echo $_POST['dCargo']; ?></p>
						<p>
							<strong>Data de Admissão: </strong><?php echo $_POST['admissao']; ?>
						</p>
						<p>
							<strong>Data da Recisão: </strong><?php echo Muda_Data($_POST['dDemissao']); ?>
							<strong>Data do Aviso: </strong><?php echo Muda_Data($_POST['dataAviso']); ?>
						</p>
						<p>
							<strong>Tipo da Recisão: </strong><?php echo $_POST['tDesligamento']; ?>
							<strong>Tipo do Aviso: </strong><?php echo $_POST['avisoPrevio']; ?>
						</p>
						<p>
							<strong>Tipo de Contrato: </strong><?php echo $_POST['tContrato']; ?>
							<?php
							if(isset($_POST['periodo']) && $_POST['periodo'] != "" ){
								echo " ".$_POST['periodo']." dias.";
							}
							?>
						</p>
					</div>
					<hr>
					<div class="text-justify col-xs-12">
						<p><strong>Justificativa/Motivo: </strong><?php echo $_POST['justificativa']; ?></p>
					</div>

					<hr >

					<div class="col-xs-12 text-justify" >
						<p>
							<strong>Há Algum Exame Especifico: </strong><?php echo $_POST['exame']; ?>
							<?php
							if(isset($_POST['dExame']) && $_POST['dExame'] != "" ){
								echo ", ".$_POST['dExame'];
							}
							?>
						</p>
						<p>
							<strong>Há algum Impedimento para o Desligamento: </strong><?php echo $_POST['doenca']; ?>
							<?php
							if(isset($_POST['dDoenca']) && $_POST['dDoenca'] != "" ){
								echo ", ".$_POST['dDoenca'];
							}
							?>
						</p>
					</div>

					<hr />

					<div class="col-xs-12 text-justify">
						<p>
							<strong>Advertência ou Suspenções: </strong><?php echo $_POST['advSusp']; ?>
							&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
							<strong>Promoções ou Aumento Salariais: </strong><?php echo $_POST['promo']; ?>
						</p>

					</div>
					<hr />

					<div class="col-xs-5 text-justify">
						<p>
							<strong>Celular:</strong> <?php echo $_POST['celular']; ?>
						</p>
						<p>
							<strong>Computador:</strong> <?php echo $_POST['computador']; ?>
						</p>
						<p>
							<strong>E-mail:</strong> <?php echo $_POST['email']; ?>
						</p>
					</div>
					<div class="col-xs-5 text-justify">
						<p>
							<strong>Estacionamento:</strong> <?php echo $_POST['estacionamento']; ?>
						</p>
						<p>
							<strong>Ramal:</strong> <?php echo $_POST['ramal']; ?>
						</p>
						<p>
							<strong>Veículo:</strong> <?php echo $_POST['veiculo']; ?>
						</p>
					</div>
					<div class="col-xs-12 text-justify">
						<?php
						if(isset($_POST['outros']) && $_POST['outros'] != null){
						?>
						<p>
							<strong>Outros:</strong> <?php echo $_POST['outros']; ?>
						</p>
						<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
<?php
$html = ob_get_clean();
//$html = utf8_encode($html);
define('MPDF_PATH', '../../control/classes/mpdf60/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF('utf-8','A4-P');
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='utf-8';
//$mpdf->SetHeader('Relatório de Cargos|UNIDADE '.strtoupper($Unidade->Unidade).'|{PAGENO}');
$mpdf->SetAuthor('RH-Online');

// carrega uma folha de estilo – MAGICA!!!
$stylesheet = file_get_contents('../../css/pdf.css');

// incorpora a folha de estilo ao PDF
// O parâmetro 1 diz que este é um css/style e deverá ser interpretado como tal
$mpdf->WriteHTML($stylesheet,1);
//Algumas configurações do PDF
$mpdf->SetDisplayMode('fullpage');
// modo de visualização

//bacana este rodape, nao eh mesmo?      

$arquivo = 'Analise_Desligamento_'.$_POST['Registro'].'.pdf';
$mpdf->WriteHTML($html);
$mpdf->Output($arquivo, 'F');

$Data = date("Y-m-d");
$Tipo = "Demissao";
try{
	$email = new PHPMailer();
	$email->CharSet = 'UTF-8';
	$email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
	$email->FromName	= 'RH-Online';
	$email->Subject		= 'RH-Online: Análise para Desligamento';
	$email->IsHTML(true); // Define que o e-mail será enviado como HTML
	$email->Body		= $MsgEmail;

	//$email->AddAddress( 'cleitonteixeira@secservices.com.br' , 'Cleiton' );
	$email->AddAddress( $email , $Responsavel );
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
	$stm->bindParam(2, $row->Responsavel);
	$stm->bindParam(3, $row->idUnidade);
	$stm->bindParam(4, $Data);
	$stm->bindParam(5, $IDDemissao);
	$stm->bindParam(6, $Tipo);
	$stm->execute();
	$Pendencia = $conexao->lastInsertId();

	if ($enviado) {
		echo "<script>alert('Pendência Cadastradada com Sucesso! E-mail enviado com sucesso!');window.location.href='".BASE."formularios/ValidaPendencia.php?p=".$Pendencia."';</script>";exit;
	} else {
		echo "<script>alert('Pendência Cadastradada com Sucesso! Falha ao enviar e-mail!');window.location.href='".BASE."formularios/ValidaPendencia.php?p=".$Pendencia."';</script>";exit;
	}

}catch (PDOexception $error_insert){	
	echo "<script>alert('Erro ao cadastrar ".$error_insert->getMessage().");window.location.href='".BASE.";</script>";exit;
}exit;
?>