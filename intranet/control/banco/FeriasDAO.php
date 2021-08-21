<?php
require_once("../../control/arquivo/funcao/Dados.php");
require_once("../../control/arquivo/funcao/Outras.php");
require_once("../../control/banco/conexao.php");
require_once("../../control/classes/phpMailer/class.phpmailer.php");
// Inclui o arquivo class.phpmailer.php localizado na pasta class

if (!isset($_SESSION)) session_start();

$data_envio 	= date('d/m/Y');
$hora_envio 	= date('H:i:s');
$Nome           = $_POST['Registro']." - ".$_POST['Nome'];

$sql = "SELECT ch.Usuario_idUsuario AS Responsavel, u.Email, ca.Nome AS Unidade, un.idUnidade, c.idColaborador FROM colaborador c INNER JOIN chefia ch ON ch.Colaborador_idColaborador = c.idColaborador INNER JOIN contratacao co ON co.idContratacao = c.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = co.Unidade_idUnidade INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro INNER JOIN usuarios u ON  u.idusuarios = ch.Usuario_idUsuario WHERE c.CodColaborador = ?;"; 
$conexao = conexao::getInstance();
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $_POST['Registro']);
$stm->execute();
$row = $stm->fetch(PDO::FETCH_OBJ);
$Responsavel = $row->Responsavel;
$email = $row->Email;
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
            <h1>E-mail de Teste</h1>
            <h3><strong>Empregador: </strong>". $_POST['Empregador'] ."</h3>
            <h3><strong>CNPJ nº: </strong>". $_POST['cnpj'] ."</h3>
            <h3 class='solicitante'><strong>Solicitante: </strong>". utf8_decode($_SESSION['Nome']) ."</h3>
            <p><strong>Unidade: </strong>". utf8_decode($row->Unidade) ."</p>
            <p><strong>Funcionário: </strong>". $Nome .".</p>
            <p>
                <strong>Registro: </strong> ".$_POST['Registro']."
                <strong>Data de Admissão: </strong>". $_POST['admissao'].".
            </p>
            <p><strong>Período Aquisitivo de Férias: </strong>". date('d/m/Y' , strtotime($_POST['iAquisitivo'])) ." à ". date('d/m/Y' , strtotime($_POST['fAquisitivo'])) .".</p>
            <p><strong>Período Gozo das Férias: </strong> ". date('d/m/Y' , strtotime($_POST['iFerias'])) ." à ". date('d/m/Y' , strtotime($_POST['fFerias'])).".</p>
            <p><strong>Abono Pecuniário, sim ou não? </strong>". $_POST['Abono'] ."</p>
            <p>Este e-mail foi enviado em <b> $data_envio </b> às <b> $hora_envio </b></p>
        </body>
    </html>
  ";

$ArquisitivoInicio	= $_POST['iAquisitivo'];
$data 				= date('Y-m-d', strtotime('+1 year', strtotime($ArquisitivoInicio)));
$AquisitivoFinal	= date('Y-m-d', strtotime('-1 day', strtotime($data)));
$pGozoInicio 		= $_POST['iFerias'];
$pGozoFinal 		= $_POST['fFerias'];
$Abono		 		= utf8_encode($_POST['Abono']);

try{
	$sql = "INSERT INTO ferias (Colaborador_idColaborador,AquisitivoInicio, AquisitivoFinal, pGozoInicio, pGozoFinal, Abono) VALUES (?,?,?,?,?,?);";
	$stm = $conexao->prepare($sql);

	$stm->bindParam(1, $row->idColaborador);
	$stm->bindParam(2, $ArquisitivoInicio);
	$stm->bindParam(3, $AquisitivoFinal);
	$stm->bindParam(4, $pGozoInicio);
	$stm->bindParam(5, $pGozoFinal);
	$stm->bindParam(6, $Abono);
	$stm->execute();

	$IDFerias = $conexao->lastInsertId();

}catch (PDOexception $error_insert){	
	echo "<script>alert('Erro ao cadastrar ".$error_insert->getMessage().");window.location.href='".BASE.";</script>";exit;
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
		<!-- Place favicon.ico in the root directory -->
		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/app.js"></script>

		<!-- Fim Arquivos JS -->
		<!-- Início Arquivos CSS -->
		<link rel="stylesheet" href="../../css/pdf.css">
	</head>
	<body>
		<div style="position: absolute; left: 0; right: 0; top: 0; bottom: 0; z-index: 0;"><img style="opacity: 0.5;
			filter: alpha(opacity=50);width: 210mm; height: 297mm; margin: 0;" alt="" src="../../img/Marca.jpg" /></div>
		<div class="container-fluid" style="z-index: 1;">
			<div class="row">
				<div class="col-md-12 conteudo">
					<div class="col-xs-12" id="form-ferias">
						<div class="col-xs-12 ferias-t">
							<h3 class="text-center">Aviso de Férias</h3>
							<div class="text-center col-xs-12">
								<p><strong>Empregador: </strong><?php echo $_POST['Empregador']; ?></p>
								<p><strong>CNPJ nº: </strong><?php echo $_POST['cnpj']; ?></p>
							</div>
							<div class="col-xs-12">
								<p><strong>Nome do Funcionário: </strong><?php echo $_POST['Nome']; ?>.</p>
								<p><strong>Registro: </strong><?php echo $_POST['Registro']; ?>&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
									<strong>Data de Admissão: </strong><?php echo $_POST['admissao']; ?>.
								</p>
								<p><strong>Período Aquisitivo de Férias: </strong><?php echo date('d/m/Y' , strtotime($_POST['iAquisitivo'])); ?> à <?php echo date('d/m/Y' , strtotime($_POST['fAquisitivo'])); ?>.</p>
								<p><strong>Período Gozo das Férias: </strong><?php echo date('d/m/Y' , strtotime($_POST['iFerias'])); ?> à <?php echo date('d/m/Y' , strtotime($_POST['fFerias'])); ?>.</p>
								<p><strong>Abono Pecuniário, sim ou não? </strong><?php echo $_POST['Abono']; ?></p>
							</div>
							<p>&thinsp;</p>
							<div class="col-xs-12 text-justify">
								<p>
									O empregador, através do presente documento, e em conformidade com o art. 135 da CLT, vem notificar o empregado, com antecedência de 30 (trinta) dias, a concessão de suas férias relativas ao período aquisitivo descrito acima e conforme período de gozo apontado pelo mesmo.
								</p>
								<p>
									As férias serão remuneradas com o acréscimo de 1/3 constitucional, de acordo com o art. 7º, XVII da Constituição da República, e será pago até 2 (dois) dias antes do início do respectivo gozo de férias. 
								</p>
								<p> 
									Assim sendo, o empregado fica ciente desde já para comparecer ao departamento pessoal da empresa, para que o empregador possa lhe fornecer o demonstrativo de valores creditados.
								</p>
							</div>
							<p>&thinsp;</p>
							<div class="text-right col-xs-12">
								<p>________________________, <?php echo strftime("%d de %B de %Y"); ?>.</p>
							</div>
							<p>&thinsp;</p>
							<div class="text-center">
								<div class="col-xs-5"><p>_____________________________________</p><p>Assinatura do Gestor Imediato</p></div>
								<div class="col-xs-5"><p>_____________________________________</p><p>Assinatura do Empregado</p></div>
							</div>
							<p>&thinsp;</p>
							<div>
								<div class="col-sm-12 text-center"><p><strong>Recursos Humanos: </strong>________________________________________________________</p></div>
							</div>
							<p>&thinsp;</p>
						</div>
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
//$mpdf->SetFooter('{DATE j/m/Y H:i}|{PAGENO}/{nb}|RH Manager');
//bacana este rodape, nao eh mesmo?      

$arquivo = 'Ferias_REG.'.$_POST['Registro'].'.pdf';
$mpdf->WriteHTML($html);
$mpdf->Output($arquivo, 'F');
$Data = date("Y-m-d");
$Tipo = "Ferias";
try{
	$email = new PHPMailer();
	$email->CharSet = 'UTF-8';
	$email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
	$email->FromName	= 'RH-Online';
	$email->Subject		= 'RH-Online: Aviso de Férias';
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
	$stm->bindParam(5, $IDFerias);
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