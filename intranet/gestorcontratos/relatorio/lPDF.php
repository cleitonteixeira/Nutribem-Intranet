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
            <h1 class="text-center">ULTIMOS LANÇAMENTOS POR UNIDADE</h1>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Unidade</th>
                    <th>Ultimo Envio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sqli = "SELECT DISTINCT(l.Unidade_idUnidade) AS Unidade, c.Nome, MAX(l.dCadastro) AS DataCadastro FROM lancamento l INNER JOIN unidadefaturamento u ON u.idUnidadeFaturamento = l.Unidade_idUnidade INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro GROUP BY l.Unidade_idUnidade ORDER BY l.dCadastro ASC;";
                $stmt = $conexao->prepare($sqli);
                $stmt->execute();
                $res = $stmt->fetchAll(PDO::FETCH_OBJ);
                foreach($res as $r){
                ?>
                <tr>
                    <td><?php echo $r->Unidade; ?></td>
                    <td><?php echo utf8_decode($r->Nome); ?></td>
                    <td><?php echo date("d/m/Y", strtotime($r->DataCadastro)); ?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
            
        </table>
        <p>Relatório Gerado em <strong><?php echo date("d/m/Y H:i:s"); ?></strong>.</p>
    </body>
</html>
<?php
$html = ob_get_clean();
//$html = utf8_encode($html);
define('MPDF_PATH', '../control/classes/mpdf60/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF('utf-8','A4-L');
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='utf-8';
$mpdf->SetHeader('RELATORIO LANCAMENTOS | {PAGENO}');
$mpdf->SetAuthor('Gestor de Contrato');

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

$arquivo = 'Ultimos_Lancamentos_'.date('d-m-Y_H:i:s').'.pdf';
$mpdf->WriteHTML($html,2);
$mpdf->Output($arquivo,'D');
exit($arquivo);
?>