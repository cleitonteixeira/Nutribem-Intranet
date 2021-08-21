<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/
ob_start();

$sql = "SELECT c.nContrato,cad.Nome, cad.CNPJ, ct.*, co.*, en.* FROM contrato c INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = ct.Cadastro_idCadastro INNER JOIN contato co ON co.idContato = ct.Contato_idContato INNER JOIN endereco en ON en.idEndereco = ct.Endereco_idEndereco WHERE c.idContrato = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $_POST['contrato']);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_OBJ);
$tel_array = str_split($r->Telefone);
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
$tel_array = str_split($r->Celular);
$contador = sizeof($tel_array);
$x = 0;
$Celular = "(";
while($x<=$contador){
	$Celular .= $tel_array[$x];
	if($x == 1){
		$Celular .= ") ";
	}
	if($x == 6){
		$Celular .= "-";
	}
	$x += 1;
	if($x == $contador){
		break;
	}
}
$Nome = utf8_decode($r->Nome);
$CNPJ = utf8_decode(CNPJ_Padrao(str_pad($r->CNPJ, 14,0,STR_PAD_LEFT)));
$Endereco = utf8_decode($r->Endereco.", N&ordm;: ".$r->Numero.", ".$r->Bairro." - ".$r->Cidade."-".$r->UF." - CEP: ".CEP_Padrao($r->CEP));
$Responsavel = utf8_decode($r->Responsavel);
$Telefone = utf8_decode($Telefone);
$Celular = utf8_decode($Celular);

$valor2x = explode('/',$_POST['dataIN']);
$dataIN = $valor2x[2]."-".$valor2x[1]."-".$valor2x[0];
$valor3x = explode('/',$_POST['dataFN']);
$dataFN = $valor3x[2]."-".$valor3x[1]."-".$valor3x[0];

$sql = "SELECT DISTINCT(ValorUni),SUM(Quantidade) AS Quant,Servico FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? GROUP BY ValorUni ORDER BY Servico;";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $_POST['contrato']);
$stmt->bindParam(2, $dataIN);
$stmt->bindParam(3, $dataFN);
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_OBJ);

$sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $_POST['contrato']);
$stmt->execute();
$itens = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link rel="shortcut icon" href="../img/Icone.png" type="image/x-icon" />
        <!-- Place favicon.ico in the root directory -->
        <!-- Fim Arquivos JS -->
        <!-- Início Arquivos CSS -->
        <link rel="stylesheet" href="../css/pdf.css">
    </head>
    <body>
		<div class="col-xs-12 col-lg-12 col-md-12">
			<h1 class="text-center">Medição</h1>
			<div class="panel panel-default">
				<div class="panel-heading">
					<p class="panel-title">Dados do Contrato</p>
				</div>
				<div class="panel-body">
					<div class="col-xs-8 col-md-8 col-lg-6"><p><strong>Nome: </strong><span id="nome"></span></p></div>
					<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>CNPJ: </strong><span id="cnpj"></span></p></div>
					<div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço: </strong><span id="endereco"></span></p></div>	
					<div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço de Cobranca: </strong><span id="eCobranca"></span></p></div>	

					<div class="col-xs-4 col-md-4 col-lg-5"><p><strong>Responsável Comercial: </strong><span id="rComercial"></span></p></div>
					<div class="col-xs-4 col-md-4 col-lg-3"><p><strong>Telefone: </strong><span id="tComercial"></span></p></div>
					<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><span id="eComercial"></span></p></div>

					<div class="col-xs-4 col-md-4 col-lg-5"><p><strong>Responsável Financeiro: </strong><span id="rFinanceiro"></span></p></div>
					<div class="col-xs-4 col-md-4 col-lg-3"><p><strong>Telefone: </strong><span id="tFinanceiro"></span></p></div>
					<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><span id="eFinanceiro"></span></p></div>

				</div>
			</div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Serviço</th>
						<th>Quantidade</th>
						<th>Valor Unitário</th>
						<th>Valor Total</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$vTotal = 0;
					foreach($rs as $x){
						$Total = $x->Quant * $x->ValorUni;
						$vTotal += $Total;
					?>
					<tr>
						<td><?php echo utf8_decode($x->Servico)?></td>
						<td><?php echo $x->Quant; ?></td>
						<td><?php echo "R$ ".number_format($x->ValorUni,2,',','.'); ?></td>
						<td><?php echo "R$ ".number_format($x->ValorUni*$x->Quant,2,',','.'); ?></td>
					</tr>
					<?php 
					}
					?>
				</tbody>
			</table>
			<p>Valor Total da medição R$ <strong><?php echo number_format($vTotal,2,',','.'); ?></strong>.</p>
			<p>Período de apuração da medição de <strong><?php echo $_POST['dataIN']; ?></strong> até <strong><?php echo $_POST['dataFN']; ?></strong>.</p>
		</div>
    </body>
</html>
<?php
$html = ob_get_clean();
//$html = utf8_encode($html);
define('MPDF_PATH', '../control/classes/mpdf60/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF('utf-8','A4-P');
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='utf-8';
$mpdf->SetHeader('Medição | Contrato: '.utf8_decode($r->nContrato).'|{PAGENO}');
$mpdf->SetAuthor('RH-Online');

// carrega uma folha de estilo – MAGICA!!!
$stylesheet = file_get_contents('../css/pdf.css');

// incorpora a folha de estilo ao PDF
// O parâmetro 1 diz que este é um css/style e deverá ser interpretado como tal
$mpdf->WriteHTML($stylesheet,1);
//Algumas configurações do PDF
$mpdf->SetDisplayMode('fullpage');
// modo de visualização
$mpdf->SetFooter('{DATE j/m/Y H:i}|{PAGENO}/{nb}|Gestor de Contrato');
//bacana este rodape, nao eh mesmo?      

$arquivo = 'Medicao_'.utf8_decode($r->nContrato).'_'.date("y-m-d_h.i.s").'.pdf';
$mpdf->WriteHTML($html,2);
$mpdf->Output();
exit($arquivo);
?>



