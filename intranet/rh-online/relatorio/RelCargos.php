<?php
require_once("../control/banco/conexao.php");
$conexao = conexao::getInstance();
date_default_timezone_set('America/Sao_Paulo');
ob_start();

$sql = "SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = ?";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $_GET['unidade']);
$stm->execute();

$Unidade = $stm->fetch(PDO::FETCH_OBJ);

$sql = "SELECT ca.CodCargo,ca.Cargo, ca.CBO, ca.Funcao, ca.Salario, (SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = ca.Unidade_idUnidade) unidade FROM cargo ca WHERE ca.Unidade_idUnidade = ? ORDER BY ca.Funcao, ca.CodCargo";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $_GET['unidade']);
$stm->execute();
$TotalRegistros = $stm->rowCount();
?>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>RH Manager</title>
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
                        <th>COD.</th>
                        <th>Cargo - CBO</th>
                        <th>Função</th>
                        <th>Salário</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row = $stm->fetch(PDO::FETCH_OBJ)):
                    ?>
                    <tr>
                        <td><?php echo $row->CodCargo; ?></td>
                        <td><?php echo $row->Cargo.' - '.$row->CBO; ?></td>
                        <td><?php echo strtoupper($row->Funcao); ?></td>
                        <td><?php echo "R$ ".number_format($row->Salario,2,',','.'); ?></td>
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
$mpdf->SetHeader('Relatório de Cargos|Unidade '.utf8_decode($Unidade->Unidade).'|{PAGENO}');
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
      
$arquivo = 'UN_'.utf8_decode($Unidade->Unidade).'_'.date("y-m-d_h.i.s").'_Cargos.pdf';
$mpdf->WriteHTML($html,2);
$mpdf->Output($arquivo, 'D');
exit();
?>