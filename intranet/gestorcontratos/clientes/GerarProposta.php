<?php
if (!isset($_SESSION)) session_start();
	if (!isset($_SESSION['idusuarios'])):
	session_destroy();
	header("Location: ".BASE);
	exit();
else:
require_once("../control/arquivo/funcao/Outras.php");
require_once("../control/arquivo/funcao/Dados.php");
require_once("../control/banco/conexao.php");
$conexao = conexao::getInstance();

$sql = "SELECT * FROM proposta p WHERE p.idProposta = ?";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $_GET['p']);
$stm->execute();
$rsx = $stm->fetch(PDO::FETCH_OBJ);
$sql = 'SELECT  c.idContratante,c.Responsavel, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, co.* FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN contato co ON co.idContato = c.Contato_idContato INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco WHERE c.idContratante = ?;';
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $rsx->Contratante_idContratante);
$stm->execute();
$row = $stm->fetch(PDO::FETCH_OBJ);
$tel_array = str_split($row->Telefone);
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
$tel_array = str_split($row->Celular);
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
$idContratante = utf8_decode($row->idContratante);
$Nome = utf8_decode($row->Cliente);
$CNPJ = utf8_decode(CNPJ_Padrao(str_pad($row->CNPJ, 14,0,STR_PAD_LEFT)));
switch($row->UF){
	case "AC":
		$uf = "Acre";
		break;
	case "AL":
		$uf = "Alagoas";
		break;
	case "AP":
		$uf = "Amapá";
		break;
	case "BA":
		$uf = "Bahia";
		break;
	case "CE":
		$uf = "Ceará";
		break;
	case "DF":
		$uf = "Distrito Federal";
		break;
	case "ES":
		$uf = "Espírito Santo";
		break;
	case "GO":
		$uf = "Goiás";
		break;
	case "MA":
		$uf = "Maranhão";
		break;
	case "MT":
		$uf = "Mato Grosso";
		break;
	case "MS":
		$uf = "Mato Grosso do Sul";
		break;
	case "MG":
		$uf = "Minas Gerais";
		break;
	case "PA":
		$uf = "Pará";
		break;
	case "PB":
		$uf = "Paraíba";
		break;
	case "PR":
		$uf = "Paraná";
		break;
	case "PE":
		$uf = "Pernambuco";
		break;
	case "PI":
		$uf = "Piauí";
		break;
	case "RJ":
		$uf = "Rio de Janeiro";
		break;
	case "RN":
		$uf = "Rio Grande do Norte";
		break;
	case "RS":
		$uf = "Rio Grande do Sul";
		break;
	case "RO":
		$uf = "Rondônia";
		break;
	case "RR":
		$uf = "Roraima";
		break;
	case "SC":
		$uf = "Santa Catarina";
		break;
	case "SP":
		$uf = "São Paulo";
		break;
	case "SE":
		$uf = "Sergipe";
		break;
	case "TO":
		$uf = "Tocantins";
		break;
}
$Endereco = utf8_decode($row->Endereco.", N&ordm;: ".$row->Numero.", bairro ".$row->Bairro.", em ".$row->Cidade.", Estado de ".$uf." - CEP: ".CEP_Padrao($row->CEP));
$Responsavel = utf8_decode($row->Responsavel);
$Telefone = utf8_decode($Telefone);
$Celular = utf8_decode($Celular);
$Email = utf8_decode($row->email);

// Definindo o tipo de arquivo (Ex: msexcel, msword, pdf ...)
//header("Content-type: application/pdf");
 
// Formato do arquivo (Ex: .xls, .doc, .pdf ...)
//header("Content-Disposition: attachment; filename=MeuArquivo.pdf");

ob_start();
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" href="../../css/pdf.css">
		<title>PROPOSTA Nº <?php echo $rsx->nProposta; ?></title>
    </head>
    <body>
		<div style="position: absolute; left: 0; right: 0; top: 0; bottom: 0; z-index: 0;"><img style="opacity: 0.7;
			filter: alpha(opacity=70);width: 210mm; height: 297mm; margin: 0;" alt="" src="../../img/Marca.jpg" /></div>
		<div style="z-index: 1;">
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12 text-center"><h1><u>Proposta Nº</u>: <?php echo $rsx->nProposta; ?></h1></div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<p>À <strong>NUTRIBEM REFEICOES EIRELI</strong>, inscrita no CNPJ/MF sob o n° <strong>10.560.908/0001-03</strong>, com sede na Rua Alamanda, Nº.: 710, bairro Jardim Serrano, em Paracatu, Estado de Minas Gerais, CEP 38.600-000 vem através desta apresentar sua proposta financeira de prestação de serviços.</p>
			</div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<p>À <strong><?php echo $Nome; ?></strong>, inscrita no CNPJ/MF sob o n° <strong><?php echo $CNPJ; ?></strong>, com sede na <?php echo $Endereco; ?>.</p>
			</div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<h3><strong>Dos valores e Eventos</strong></h3>
				<?php
				$sql = 'SELECT ip.Servico, ip.ValorUni FROM proposta p INNER JOIN itensproposta ip ON ip.Proposta_idProposta = p.idProposta WHERE p.idProposta = ?;';
				$stm = $conexao->prepare($sql);
				$stm->bindValue(1, $_GET['p']);
				$stm->execute();
				if($stm->rowCount() > 0){
					while($row = $stm->fetch(PDO::FETCH_OBJ)){
						echo "<p class='col-xs-8 col-md-8 col-lg-8'>";
						echo "<span><strong>Serviço:</strong> ".utf8_decode($row->Servico)."</span>";
						echo "<span><strong>&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;Valor Unitário:</strong> R$ ".number_format($row->ValorUni,2,',','.')."</span>";
						echo "</p>\n";
					}
				}else{
					echo "<p>Nenhum item encontrado!</p>";
				}
				?>
			</div>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<h3><strong>Do Prazo de Vigência</strong></h3>
				<?php
				echo "<p class='col-xs-8 col-md-8 col-lg-8'>";
				echo "<span>O prazo de vigência proposto é de: <strong>".$rsx->pVigencia."</strong> meses.</span>";
				echo "</p>\n";
				?>
			</div>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<h3><strong>Das Medições e Condições de Pagamento</strong></h3>
				<?php
				echo "<p class='col-xs-8 col-md-8 col-lg-8'>";
				echo "<span><strong>Medições:</strong> ".utf8_decode($rsx->fMedicao)."</span>";
				echo "</p>\n";
				echo "<p class='col-xs-8 col-md-8 col-lg-8'>";
				echo "<span><strong>Condição de pagamento:</strong> ".utf8_decode($rsx->Condicao)."</span>";
				echo "</p>\n";
				echo "<p class='col-xs-8 col-md-8 col-lg-8'>";
				echo "<span><strong>Froma de pagamento:</strong> ".utf8_decode($rsx->fPagamento)."</span>";
				echo "</p>\n";
				?>
			</div>
			<?php if($rsx->Equipe == "Sim"){ ?>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<h3><strong>Da Equipe</strong></h3>
				<div class="col-xs-6 col-md-6 col-lg-6">
					<table class='table'>
						<thead>
							<tr>
								<th>Função</th>
								<th>Quantidade</th>
							</tr>
							<thead>
						<tbody>
					<?php
					$sql = 'SELECT ep.Funcao, ep.Quantidade FROM proposta p INNER JOIN equipeproposta ep ON ep.Proposta_idProposta = p.idProposta WHERE p.idProposta = ?;';
					$stm = $conexao->prepare($sql);
					$stm->bindValue(1, $_GET['p']);
					$stm->execute();
					if($stm->rowCount() > 0){
						while($row = $stm->fetch(PDO::FETCH_OBJ)){
							echo "<tr><td>".utf8_decode($row->Funcao)."</td>";
							echo "<td>".$row->Quantidade."</td></tr>";
							echo "\n";
						}
					}
					?>
						</tbody>
					</table>
				</div>
			</div>
			<?php } ?>
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
$mpdf->SetAuthor('Gestor de Contratos');

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

$arquivo = $rsx->nProposta.'.pdf';
$mpdf->WriteHTML($html);
$mpdf->Output();

endif;
?>