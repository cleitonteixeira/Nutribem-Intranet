<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
ob_start();
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
        	<?php
			$sql = "SELECT o.*, cd.Nome as Unidade, u.Nome as Responavel, su.Nome AS Superv, uma.Nome AS Manutencao FROM os o INNER JOIN unidademt um ON um.idUnidadeMT = o.Unidade_idUnidade INNER JOIN usuarios u ON u.idusuarios = o.Responsavel_idResponsavel INNER JOIN cadastro cd ON cd.idCadastro = um.Cadastro_idCadastro INNER JOIN usuarios su ON su.idusuarios = u.Superior INNER JOIN usuarios uma ON uma.idusuarios = o.Manutencao_idManutencao WHERE o.idOS = ? LIMIT 1";
			$stmt = $conexao->prepare($sql);
			$stmt->bindParam(1, $_GET['id']);
			$stmt->execute();
			$r = $stmt->fetch(PDO::FETCH_OBJ);
			?>
			<h1 class="text-center"><strong>OS Nº: </strong><?=$r->nOS;?></h1>
			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 conteudo"> </div>
			<div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
			<p><strong>Unidade: </strong><?=$r->Unidade;?></p>
			</div>
			<div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
			<p><strong>Data/Hora: </strong><?=date("d/m/Y H:i:s", strtotime($r->DataHoraAbertura));?></p>
			</div>
			<div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
			<p><strong>Responsável: </strong><?=$r->Responavel;?></p>
			</div>
			<div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
			<p><strong>Superior: </strong><?=$r->Superv;?></p>
			</div>
			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"></div>
			<div class="col-xs-4 col-md-4 col-sm-4 col-lg-4">
			<p><strong>Responsável Manutenção: </strong><?=$r->Manutencao;?></p>
			</div>
			<div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
			<p><strong>Aceite: </strong><?=date("d/m/Y H:i:s", strtotime($r->DataHoraAc));?></p>
			</div>
			<div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
			<p><strong>Agendado para: </strong><?=date("d/m/Y", strtotime($r->DataAgenda));?></p>
			</div>

			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /><h3 class="text-center"><u>ITENS OS</u></h3></div>
			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 conteudo"> </div>
			<?php
			$sql = "SELECT cd.Nome AS nEquipamento, io.*, ce.Nome AS Categoria, eq.* FROM itemos io INNER JOIN equipamento eq ON eq.idEquipamento = io.Equipamento_idEquipamento INNER JOIN cadastro cd ON cd.idCadastro = eq.Cadastro_idCadastro INNER JOIN categoriaequipamento ce ON ce.idCategoriaEquipamento = eq.Categoria_idCategoria WHERE io.OS_idOS = ?";
			$stmt = $conexao->prepare($sql);
			$stmt->bindParam(1, $_GET['id']);
			$stmt->execute();
			$rs = $stmt->fetchAll(PDO::FETCH_OBJ);
			$x = 1;
			foreach ($rs as $eq) {
			?>
			<div class="col-xs-4 col-md-4 col-sm-4 col-lg-4">
			<p><strong>Equipamento: </strong><?=utf8_decode($eq->nEquipamento);?></p>
			</div>
			<div class="col-xs-4 col-md-4 col-sm-4 col-lg-4">
			<p><strong>Modelo: </strong><?=utf8_decode($eq->Modelo);?></p>
			</div>
			<div class="col-xs-4 col-md-4 col-sm-4 col-lg-4">
			<p><strong>Fabricante: </strong><?=utf8_decode($eq->Fabricante);?></p>
			</div>
			<div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
			<p><strong>Alimentação: </strong><?=utf8_decode($eq->Alimentacao);?></p>
			</div>
			<div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
			<p><strong>Data Fabricação: </strong><?=date("d/m/Y", strtotime($eq->dFabrica));?></p>
			</div>
			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
			<p><strong>Defeito: </strong><?=utf8_decode($eq->Comentario);?></p>
			</div>
			<?php
				if($x < 3){
			?>

			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /></div>
		<?php 	}else{?>
			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"> </div>
			<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"> </div>
		<?php 
					$x = 0;
				}
				$x++;
			}?>
		</div>
    </body>
</html>
<?php 
$html = ob_get_clean();
//$html = utf8_encode($html);
define('MPDF_PATH', '../control/classes/mpdf/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF('utf-8','A4-L',7,'MS Serif',10,10,10,15);
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='utf-8';
//$mpdf->SetHeader('{PAGENO}');
$mpdf->SetAuthor('Manutenção Online');

// carrega uma folha de estilo – MAGICA!!!
$stylesheet = file_get_contents('../css/pdf.css');

// incorpora a folha de estilo ao PDF
// O parâmetro 1 diz que este é um css/style e deverá ser interpretado como tal
$mpdf->WriteHTML($stylesheet,1);
//Algumas configurações do PDF
$mpdf->SetDisplayMode('fullpage');
// modo de visualização
$mpdf->SetFooter('{DATE j/m/Y H:i}|{PAGENO}/{nb}|Manutenção Online');
//bacana este rodape, nao eh mesmo?      

$arquivo = 'Ultimos_Lancamentos_.pdf';
$mpdf->WriteHTML($html,2);
$mpdf->Output();
exit($arquivo);
?>