<?php
if (!isset($_SESSION)) session_start();
define("BASE",'http://www.nutribemrefeicoescoletivas.com.br/intranet/rh-online/');
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
require_once("../../control/arquivo/header/Header.php");
require_once("../control/classes/phpMailer/class.phpmailer.php");
$email = new PHPMailer();
$conexao = conexao::getInstance();
$troca = array('linha',',');
//header("Content-type: application/msexcel");
//header("Content-Disposition: attachment; filename=Teste.xls");
$c = str_split(str_replace($troca,'',$_POST['hidden1']));
if($_POST['hidden1'] != ''){
	$cont = count($c);
}else{
	$cont = 0;
}
$x = 1;
ob_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>NUTRIBEM - RH Online</title>
    <meta name="description" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" href="<?=BASE; ?>img/Icone.png" type="image/x-icon" />
    <!-- Place favicon.ico in the root directory -->
    <script src="<?=BASE;?>js/jquery.js"></script>
    <script src="<?=BASE;?>js/bootstrap-datepicker.min.js"></script>
    <script src="<?=BASE;?>js/jquery.maskMoney.js"></script>
    <script src="<?=BASE;?>js/jquery.maskedinput.min.js"></script>
    <script src="<?=BASE;?>js/bootstrap-select.min.js"></script>
    <script src="<?=BASE;?>js/bootstrap.min.js"></script>
    <script src="<?=BASE;?>js/app.js"></script>
    <script src="<?=BASE;?>js/jquery.dataTables.min.js"></script>
    <script src="<?=BASE;?>js/validator.js"></script>
    <script src="<?=BASE;?>js/validator.min.js"></script>
    <script src="<?=BASE;?>js/jquery.cpfcnpj.min.js"></script>
    <script src="<?=BASE;?>js/jquery.complexify.js"></script>
    <!-- Fim Arquivos JS -->
    <!-- Início Arquivos CSS -->
    <link rel="stylesheet" href="<?=BASE;?>css/bootstrap-datepicker.min.css"/>
    <link rel="stylesheet" href="<?=BASE;?>css/bootstrapValidator.css"/>
    <link rel="stylesheet" href="<?=BASE;?>css/bootstrapValidator.min.css"/>
    <link rel="stylesheet" href="<?=BASE;?>css/bootstrap.css">
    <link rel="stylesheet" href="<?=BASE;?>css/bootstrap.min.css">

	<link rel="stylesheet" href="<?=BASE;?>css/app.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?=BASE;?>css/dataTables.bootstrap.min.css" />
    <link rel="stylesheet" href="<?=BASE;?>css/bootstrap-select.min.css">
  </head>
  <body>
<table class="table table-bordered">
	<thead>
		<tr>
			<th class="text-center" colspan="6"><h2>FICHA DE CADASTRO FUNCIONAL</h2></th>
		</tr>
	</thead>
	<tbody class="text-left">
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"><h4>DADOS PESSOAIS</h4></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Nome Completo:</strong></td>
			<td colspan="5" class="text-left"><?=$_POST['Nome'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Data de Nascimento:</strong></td>
			<td colspan="2" class="text-left"><?=date("d/m/Y", strtotime($_POST['dNascimento']));?></td>
			<td class="text-right"><strong>Naturalidade:</strong></td>
			<td colspan="2" class="text-left"><?=$_POST['Naturalidade'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Sexo:</strong></td>
			<td colspan="2" class="text-left"><?=$_POST['Sexo'] == 'F' ? 'Feminino' : 'Masculino';?></td>
			<td class="text-right"><strong>Cor:</strong></td>
			<td colspan="2" class="text-left"><?=$_POST['Cor'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>CPF:</strong></td>
			<td class="text-left"><?=$_POST['CPF']?></td>
			<td class="text-right"><strong>RG:</strong></td>
			<td class="text-left"><?=$_POST['RG'];?></td>
			<td class="text-right"><strong>Data Emissão RG:</strong></td>
			<td class="text-left"><?=date("d/m/Y", strtotime($_POST['dRG']));?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Título de Eleitor:</strong></td>
			<td class="text-left"><?=$_POST['Titulo']?></td>
			<td class="text-right"><strong>Seção:</strong></td>
			<td class="text-left"><?=$_POST['Secao'];?></td>
			<td class="text-right"><strong>Zona:</strong></td>
			<td class="text-left"><?=$_POST['Zona'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Habilitação:</strong></td>
			<td colspan="2" class="text-left"><?=$_POST['Habilitacao']?></td>
			<td class="text-right"><strong>Categoria:</strong></td>
			<td colspan="2" class="text-left"><?=$_POST['Categoria'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Grau de Instrução:</strong></td>
			<td colspan="2" class="text-left"><?=$_POST['gInstrucao']?></td>
			<td class="text-right"><strong>Estado Civil:</strong></td>
			<td colspan="2" class="text-left"><?=$_POST['eCivil'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Cônjuge:</strong></td>
			<td class="text-left"><?=$_POST['Conjuge']?></td>
			<td class="text-right"><strong>Data de Nascimento: <small>Cônjuge</small></strong></td>
			<td class="text-left"><?=$_POST['cNascimento'];?></td>
			<td class="text-right"><strong>CPF: <small>Cônjuge</small></strong></td>
			<td class="text-left"><?=$_POST['cCPF'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Nome da Mãe: <small>Cônjuge</small></strong></td>
			<td class="text-left"><?=$_POST['cMae']?></td>
			<td class="text-right"><strong>Certidão de Casamento: </strong></td>
			<td class="text-left"><?=$_POST['cCasamento'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"><h4>DADOS DE ENDEREÇO</h4></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Endereço:</strong></td>
			<td class="text-left"><?=$_POST['Endereco']?></td>
			<td class="text-right"><strong>Número:</strong></td>
			<td class="text-left"><?=$_POST['Numero'];?></td>
			<td class="text-right"><strong>Cidade:</strong></td>
			<td class="text-left"><?=$_POST['Cidade'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Bairro:</strong></td>
			<td class="text-left"><?=$_POST['Bairro']?></td>
			<td class="text-right"><strong>CEP: </strong></td>
			<td class="text-left"><?=$_POST['CEP'];?></td>
			<td class="text-right"><strong>UF: </strong></td>
			<td class="text-left"><?=$_POST['UF'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"><h4>DADOS DA CONTRATAÇÃO</h4></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Unidade:</strong></td>
			<td class="text-left"><?=$_POST['Unidade']?></td>
			<td class="text-right"><strong>Data de Admissão:</strong></td>
			<td class="text-left"><?=date("d/m/Y", strtotime($_POST['Admissao']));?></td>
			<td class="text-right"><strong>Função:</strong></td>
			<td class="text-left"><?=$_POST['Funcao'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>CTPS:</strong></td>
			<td class="text-left"><?=$_POST['CTPS']?></td>
			<td class="text-right"><strong>Série: </strong></td>
			<td class="text-left"><?=$_POST['Serie'];?></td>
			<td class="text-right"><strong>PIS: </strong></td>
			<td class="text-left"><?=$_POST['PIS'];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Data Emissão CTPS:</strong></td>
			<td class="text-left"><?=date("d/m/Y", strtotime($_POST['dCTPS']));?></td>
			<td class="text-right"><strong>UF CTPS: </strong></td>
			<td class="text-left"><?=$_POST['uCTPS'];?></td>
			<td class="text-right"><strong>ASO: </strong></td>
			<td class="text-left"><?=date("d/m/Y", strtotime($_POST['Aso']));?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"><h4>DADOS DOS FILHOS</h4></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<?php
		if($cont > 0){
			while($x <= $cont){
		?>
		<tr>
			<td class="text-right"><strong>Nome do Filho:</strong></td>
			<td class="text-left"><?=$_POST['dNome'.$x];?></td>
			<td class="text-right"><strong>CPF:</strong></td>
			<td class="text-left"><?=$_POST['dCpf'.$x];?></td>
			<td class="text-right"><strong>Parentesco:</strong></td>
			<td class="text-left"><?=$_POST['Parentesco'.$x];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Mãe:</strong></td>
			<td class="text-left"><?=$_POST['fMae'.$x];?></td>
			<td class="text-right"><strong>DNV:</strong></td>
			<td class="text-left"><?=$_POST['dnv'.$x];?></td>
			<td class="text-right"><strong>Data de Nascimento:</strong></td>
			<td class="text-left"><?=date("d/m/Y", strtotime($_POST['dFilho'.$x]));?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Termo/Registo:</strong></td>
			<td class="text-left"><?=$_POST['fRegistro'.$x];?></td>
			<td class="text-right"><strong>Livro:</strong></td>
			<td class="text-left"><?=$_POST['fLivro'.$x];?></td>
			<td class="text-right"><strong>Folha:</strong></td>
			<td class="text-left"><?=$_POST['fFolha'.$x];?></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<?php
				$x++;
			}
		}else{
		?>
		<tr>
			<td class="text-center" colspan="6"><strong>SEM DADOS</strong></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"><h4>DADOS DE EPI</h4></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"></td>
		</tr>
		<tr>
			<td class="text-right"><strong>Calça:</strong></td>
			<td class="text-left"><?=$_POST['Calca']?></td>
			<td class="text-right"><strong>Camiseta:</strong></td>
			<td class="text-left"><?=$_POST['Camiseta'];?></td>
			<td class="text-right"><strong>Botina/Bota:</strong></td>
			<td class="text-left"><?=$_POST['Bota'];?></td>
		</tr>
	</tbody>
</table>
<?php
$dados = ob_get_clean();
$fp = fopen($_POST['Nome']." - ".$_POST['CPF'].".xls", "w");
$escreve = fwrite($fp, $dados);
fclose($fp);
$msg = "
	<html>
		<body>
			<p>
				A ficha de cadastro da pessoa ".$_POST['Nome']." e CPF: ".CPF_Padrao($_POST['CPF'])." foi enviada por ".$_SESSION['Nome'].".
			</p>
			<p>
				E-mail enviado às ".date("H:i")." do dia ".date("d/m/Y").".
			</p>
		</body>
	</html>
	";
$email->CharSet = 'UTF-8';
$email->From		= 'contato@nutribemrefeicoescoletivas.com.br';
$email->FromName	= 'RH-Online';
$email->Subject		= 'Ficha de Cadastro Unidade: '.$_POST['Unidade'];
$email->IsHTML(true); // Define que o e-mail será enviado como HTML

$email->Body = $msg;
$email->AddAttachment( $_POST['Nome']." - ".$_POST['CPF'].".xls" );
$email->AddAddress('ti@nutribemrefeicoescoletivas.com.br', 'Cleiton dos Santos - Nutribem'); // Copia
$email->AddAddress('rh@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
//$email->AddAddress('rh02@nutribemrefeicoescoletivas.com.br', 'RH Nutribem'); // Copia
//$email->AddAddress('patricia.lopes@nutribemrefeicoescoletivas.com.br', 'Patricia Lopes - RH'); // Copia
//$email->AddCC($_SESSION['email], utf8_decode($_SESSION['Nome'])); // Copia
//$email->AddAddress( 'cleitonteixeira@secservices.com.br' , 'Cleiton' );
//$email->AddCC('virgiliofaria@nutribemrefeicoescoletivas.com.br', 'Virgilio Faria'); // Copia
//$email->AddCC('anderson@nutribemrefeicoescoletivas.com.br', 'Anderson R. M. Dengo'); // Copia
//$email->AddCC('estagiario.dp@nutribemrefeicoescoletivas.com.br', 'Estagiario(a) DP'); // Copia
//$email->AddCC('estagiario.rh@nutribemrefeicoescoletivas.com.br', 'Estagiario(a) RH');

$enviado = $email->Send();
// Limpa os destinatários e os anexos
$email->ClearAllRecipients();

$email->ClearAttachments();
if($enviado){
	echo '
	    <div class="alert alert-success">
	        <p><strong>Sucesso!</strong> E-mail enviado com Sucesso!</p>
	        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/FichaCadastro.php">aqui</a>.</p>
	    </div>
	    ';
	//unlink($_POST['Nome']." - ".$_POST['CPF'].".xls");
	header('Refresh: 5;URL='.BASE.'formularios/FichaCadastro.php');exit;
}else{
	echo '
	    <div class="alert alert-danger">
	        <p><strong>Falha!</strong> E-mail não enviado!</p>
	        <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'formularios/FichaCadastro.php">aqui</a>.</p>
	    </div>
	    ';
	//unlink($_POST['Nome']." - ".$_POST['CPF'].".xls");
	header('Refresh: 5;URL='.BASE.'formularios/FichaCadastro.php');exit;
}

?>