<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
*/

$SqlFiltros = "";


if(isset($_POST['cargo']) and $_POST['cargo'] != 0):
	$ca = array();
	$ca = $_POST['cargo'];
	$cont = count($ca);
	$SqlFiltros = "AND ";
	$x = 1;
	$cargo = "(";
	foreach($ca as $c):
		$cargo .= $c;
		if($x < $cont):
			$cargo .= ", ";
		else:
			$cargo .= ") ";
		endif;
		$x += 1;
	endforeach;
	$SqlFiltros .= " con.Cargo_idCargo IN".$cargo;
endif;
if(isset($_POST["fData"]) and $_POST["fData"] === "Entre"):
	$dataINx = explode('/',$_POST['dataIN']);
	$dataIN = $dataINx[2]."-".$dataINx[1]."-".$dataINx[0];
	$dataFNx = explode('/',$_POST['dataFN']);
	$dataFN = $dataFNx[2]."-".$dataFNx[1]."-".$dataFNx[0];
	if(isset($_POST["filtro"]) and $_POST["filtro"] === "Todos"):
		if(isset($_POST["fData"]) and $_POST["fData"] === "Entre"):
			$SqlFiltros .= " AND con.dAdmissao BETWEEN '".$dataIN."' AND '".$dataFN."' ";
		 elseif(isset($_POST["fData"]) and $_POST["fData"] === "Antes"):
			$SqlFiltros .= " AND con.dAdmissao <= '".$dataIN."' ";
		 elseif(isset($_POST["fData"]) and $_POST["fData"] === "Depois"):
			$SqlFiltros .= " AND con.dAdmissao >= '".$dataIN."' ";
		  endif;
	elseif(isset($_POST["filtro"]) and $_POST["filtro"] == "Ativos"):
		if(isset($_POST["fData"]) and $_POST["fData"] === "Entre"):
			$SqlFiltros .= " AND con.dAdmissao BETWEEN '".$dataIN."' AND '".$dataFN."' AND con.dDemissao IS NULL ";
		 elseif(isset($_POST["fData"]) and $_POST["fData"] === "Antes"):
			$SqlFiltros .= " AND con.dDemissao <= '".$dataIN."' AND con.dDemissao IS NULL ";
		 elseif(isset($_POST["fData"]) and $_POST["fData"] === "Depois"):
			$SqlFiltros .= " AND con.dDemissao >= '".$dataIN."' AND con.dDemissao IS NULL ";
		 else:
		     $SqlFiltros .= " AND con.dDemissao IS NULL ";
		  endif;
	elseif(isset($_POST["filtro"]) and $_POST["filtro"] === "Inativos"):
		if(isset($_POST["fData"]) and $_POST["fData"] === "Entre"):
			$SqlFiltros .= " AND con.dDemissao BETWEEN '".$dataIN."' AND '".$dataFN."' AND con.dDemissao IS NOT NULL ";
		 elseif(isset($_POST["fData"]) and $_POST["fData"] === "Antes"):
			$SqlFiltros .= " AND con.dDemissao <= '".$dataIN."' AND con.dDemissao IS NOT NULL ";
		 elseif(isset($_POST["fData"]) and $_POST["fData"] === "Depois"):
			$SqlFiltros .= " AND con.dDemissao >= '".$dataIN."' AND con.dDemissao IS NOT NULL ";
		 endif;
	else:
		$SqlFiltros .= " ";
	endif;
elseif(!isset($_POST["fData"]) and isset($_POST["filtro"])):
	if(isset($_POST["filtro"]) and $_POST["filtro"] === "Inativos"):
		$SqlFiltros .= " AND con.dDemissao IS NOT NULL ";
	 elseif(isset($_POST["filtro"]) and $_POST["filtro"] === "Ativos"):
		$SqlFiltros .= " AND con.dDemissao IS NULL ";
	 elseif(isset($_POST["filtro"]) and $_POST["filtro"] === "Todos"):
		$SqlFiltros .= " ";
	 endif;
else:
    $SqlFiltros .= " ";
endif;

//echo $SqlFiltros;
//AND con.dAdmissao BETWEEN '2017-01-01' AND '2017-03-31' OR con.dDemissao BETWEEN '2017-01-01' AND '2017-03-31'

//echo "<br >";
//echo "SELECT col.CodColaborador, cad.Nome, cad.CPF,ca.Funcao, ca.CBO, con.dAdmissao, con.dDemissao, ca.Salario, (SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = ca.Unidade_idUnidade) unidade FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN cargo ca ON ca.idCargo = con.Cargo_idCargo WHERE con.Unidade_idUnidade = ? ".$SqlFiltros." ORDER BY cad.Nome";

$sql = "SELECT cad.Nome as unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = ?";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $_POST['unidade']);
$stm->execute();

$Unidade = $stm->fetch(PDO::FETCH_OBJ);

$sql = "SELECT col.CodColaborador, cad.Nome, cad.CPF,ca.Funcao, ca.CBO, con.dAdmissao, con.dDemissao, ca.Salario, (SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = ca.Unidade_idUnidade) unidade FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN cargo ca ON ca.idCargo = con.Cargo_idCargo WHERE con.Unidade_idUnidade = ? ".$SqlFiltros." ORDER BY cad.Nome";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $_POST['unidade']);
$stm->execute();
$TotalRegistros = $stm->rowCount();

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
    <link rel="stylesheet" href="../css/pdf.css">
  </head>
  <body>
<div class="container-fluid">
    <div class="row">
        <div style="">
            <div class="col-xs-12"><h5>Total de Registros: <?php echo $TotalRegistros; ?></h5></div>
            <table class="table table-resposive table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Função/CBO</th>
                        <th>Salário</th>
                        <th>Admissão</th>
                        <th>Demissão</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row = $stm->fetch(PDO::FETCH_OBJ)):
                    ?>
                    <tr>
                        <td><?php echo $row->CodColaborador." - ".utf8_decode($row->Nome); ?></td>
                        <td><?php echo CPF_Padrao(str_pad($row->CPF,11,0, STR_PAD_LEFT)); ?></td>
                        <td><?php echo strtoupper(utf8_decode($row->Funcao)).' - '.$row->CBO; ?></td>
                        <td><?php echo "R$ ".number_format($row->Salario,2,',','.'); ?></td>
                        <td><?php echo Muda_Data($row->dAdmissao); ?></td>
                        <?php
						if($row->dDemissao != null):
						?>
						<td><?php echo Muda_Data($row->dDemissao); ?></td>
						<?php
						else:
						?>
						<td><i>null</i></td>
						<?php
						endif;
						?>
                    </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$html = ob_get_clean();
//$html = utf8_encode($html);
define('MPDF_PATH', '../control/classes/mpdf60/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF('utf-8','A4-L');
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='utf-8';
$mpdf->SetHeader('Relatório de Colaboradores|Unidade '.utf8_decode($Unidade->unidade).'|{PAGENO}');
$mpdf->SetAuthor('RH-Online');

// carrega uma folha de estilo – MAGICA!!!
$stylesheet = file_get_contents('../css/pdf.css');

// incorpora a folha de estilo ao PDF
// O parâmetro 1 diz que este é um css/style e deverá ser interpretado como tal
$mpdf->WriteHTML($stylesheet,1);
//Algumas configurações do PDF
$mpdf->SetDisplayMode('fullpage');
// modo de visualização
$mpdf->SetFooter('{DATE j/m/Y H:i}|{PAGENO}/{nb}|RH-Online');
//bacana este rodape, nao eh mesmo?      
      
$arquivo = 'UN_'.utf8_decode($Unidade->unidade).'_'.date("y-m-d_h.i.s").'_Colaboradores.pdf';
$mpdf->WriteHTML($html,2);
$mpdf->Output(/*$arquivo, 'D'*/);
exit();
?>