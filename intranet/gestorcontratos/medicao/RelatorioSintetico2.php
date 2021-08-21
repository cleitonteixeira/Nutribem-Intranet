<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/
function Telefone($dados){
    $tel_array = str_split($dados);
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
    return $Telefone;
}
function Celular($dados){
    $tel_array = str_split($dados);
    $contador = sizeof($tel_array);
    $x = 0;
    $Telefone = "(";
    while($x<=$contador){
        $Telefone .= $tel_array[$x];
        if($x == 1){
            $Telefone .= ") ";
        }
        if($x == 6){
            $Telefone .= "-";
        }
        $x += 1;
        if($x == $contador){
            break;
        }
    }
    return $Telefone;
}
ob_start();
$sql = "SELECT * FROM medicao WHERE idMedicao = ?;";
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $_GET['id']);
$stm->execute();
$ct = $stm->fetch(PDO::FETCH_OBJ);
$idContrato = $ct->Contrato_idContrato;
$sql = 'SELECT cdn.Nome, p.*, cont.*, c.idContratante,c.IE, fec.Descricao, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca INNER JOIN contrato cont ON cont.idContrato = ? INNER JOIN unidadefaturamento ufm ON ufm.idUnidadeFaturamento = cont.Unidade_idUnidade INNER JOIN cadastro cdn ON cdn.idCadastro = ufm.Cadastro_idCadastro INNER JOIN fechamento fec ON fec.idFechamento = cont.Fechamento INNER JOIN proposta p ON p.idProposta = cont.Proposta_idProposta WHERE c.idContratante = cont.Contratante_idContratante;';
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $idContrato);
$stm->execute();
$row = $stm->fetch(PDO::FETCH_OBJ);
$sql = "SELECT * FROM ccontratante WHERE Contratante_idContratante = ?;";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $row->idContratante);
$stm->execute();
$rx = $stm->fetchAll(PDO::FETCH_OBJ);
/* DADOS DO CLIENTE */
if(empty($row->IE)){
    $IE = "ISENTO";
}else{
    $IE = $row->IE;
}
$idContratante = $row->idContratante;
$Nome = utf8_decode($row->Cliente);
$Ccusto = utf8_decode($row->cCusto);
$CNPJ = utf8_decode(CNPJ_Padrao(str_pad($row->CNPJ, 14, 0, STR_PAD_LEFT)));
$Nome = utf8_decode($row->Cliente);
$Endereco = stripslashes(utf8_decode($row->Endereco.", N&ordm;: ".$row->Numero.", ".$row->Bairro." - ".$row->Cidade."-".$row->UF." - CEP: ".CEP_Padrao(str_pad($row->CEP, 8, 0, STR_PAD_LEFT))));
$eCobranca =stripslashes(utf8_decode($row->eCobranca.", N&ordm;: ".$row->nCobranca.", ".$row->bCobranca." - ".$row->cCobranca."-".$row->uCobranca." - CEP: ".CEP_Padrao(str_pad($row->ceCobranca, 8, 0, STR_PAD_LEFT))));
/* FIM DADOS DO CLIENTE */
foreach($rx as $c){
    if($c->Tipo == "Comercial"){
        $rComercial = utf8_decode($c->Responsavel);
        $eComercial = utf8_decode($c->Email);
        $tComercial = strlen($c->Telefone) == 11 ? Celular($c->Telefone) : Telefone($c->Telefone);
    }else{
        $rFinanceiro = utf8_decode($c->Responsavel);
        $eFinanceiro = utf8_decode($c->Email);
        $tFinanceiro = strlen($c->Telefone) == 11 ? Celular($c->Telefone) : Telefone($c->Telefone);
    }
}
$sql = "SELECT cd.Nome AS Empresa, cd.CNPJ AS CNPJ, cdu.Nome AS Unidade FROM contrato c INNER JOIN unidadefaturamento uf ON uf.idUnidadeFaturamento = c.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = uf.Cadastro_idCadastro INNER JOIN empresa e ON e.idEmpresa = c.Empresa_idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = e.Cadastro_idCadastro WHERE c.idContrato = ?;";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idContrato);
$stmt->execute();
$rst = $stmt->fetch(PDO::FETCH_OBJ);
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
        
        <div class="container-fluid">
            <div class="col-xs-12 col-lg-12 col-md-12" >
                <h1 class="text-center"><img class="text-left" src="../img/IconeRel.png" /><span class="text-center">MEDIÇÃO: <?php echo $ct->Medicao; ?></span></h1>
                <div class="panel panel-default text-justify" style="margin-top:1% !important;">
                    <div class="panel-heading">
                        <h1 class="panel-title">Dados Cliente</h1>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>Nome: </strong><?php echo $Nome; ?></p></div>
                        <div class="col-xs-3 col-md-3 col-lg-3"><p><strong>CNPJ: </strong><?php echo $CNPJ; ?></p></div>
                        <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>IE: </strong><?php echo $IE; ?></p></div>
                        <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>Centro de Custo: </strong><?php echo $Ccusto; ?></p></div>
                        <div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço de Cobrança: </strong><?php echo $eCobranca; ?></p></div>

                        <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>Responsável Financeiro: </strong><?php echo $rFinanceiro; ?></p></div>
                        <div class="col-xs-2 col-md-2 col-lg-2"><p><strong>Telefone: </strong><?php echo $tFinanceiro; ?></p></div>
                        <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><?php echo $eFinanceiro; ?></p></div>
                        <div class="col-xs-6 col-md-6 col-lg-6"><p><strong>Fechamento de Medição: </strong><?php echo utf8_decode($row->Descricao); ?></p></div>
                        <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>Periodo Apurado: </strong><?php echo date("d/m/Y", strtotime($ct->dInicio)); ?> a <?php echo date("d/m/Y", strtotime($ct->dFinal)); ?></p></div>
                        <div class="col-xs-7 col-md-7 col-lg-7"><p><strong>Faturamento: </strong><?php echo utf8_decode($rst->Empresa)." - ".CNPJ_Padrao($rst->CNPJ); ?></p></div>
                        <div class="col-xs-4 col-md-4 col-lg-4"><strong>Unidade Faturamento: </strong><?php echo utf8_decode($rst->Unidade); ?></div>
                        <?php if(!empty($row->pCompra)){?>
                        <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>Pedido de Compra: </strong><?php echo utf8_decode($row->pCompra); ?></p></div>
                        <?php }else{ ?>
                        <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>Pedido de Compra: </strong>N/A</p></div>
                        <?php } ?>
                         <div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Observações: </strong><?php echo utf8_decode($row->Obs); ?></p></div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-responsive" style="background: ">
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
                    $sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindParam(1, $idContrato);
                    $stmt->execute();
                    $itens = $stmt->fetchAll(PDO::FETCH_OBJ);

                    $vTotal = 0;
                    foreach($itens as $i){
                        $sql = "SELECT DISTINCT(ValorUni),SUM(Quantidade) AS Quantidade, Servico, (ROUND(ValorUni,2)*Quantidade) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND Servico = ? AND dLancamento BETWEEN ? AND ? GROUP BY ValorUni;";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $i->Servico);
                        $stmt->bindParam(3, $ct->dInicio);
                        $stmt->bindParam(4, $ct->dFinal);
                        $stmt->execute();
                        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach($rs as $x){
                            $Total = $x->Quantidade * $x->ValorUni;
                    ?>
                    <tr class="text-center">
                        <td><?php echo utf8_decode($x->Servico)?></td>
                        <td><?php echo $x->Quantidade; ?></td>
                        <td><?php echo "R$ ".number_format($x->ValorUni,2,',','.'); ?></td>
                        <td><?php echo "R$ ".number_format($Total,2,',','.'); ?></td>
                    </tr>
                    <?php
                        }
                    }
                    foreach($itens as $i){

                        $sqli = "SELECT DISTINCT(ValorUni), (SUM(Quantidade)*ROUND(ValorUni,2)) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? GROUP BY ValorUni;";
                        $stmt = $conexao->prepare($sqli);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $ct->dInicio);
                        $stmt->bindParam(3, $ct->dFinal);
                        $stmt->bindParam(4, $i->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach($rs as $r){
                            $vTotal += $r->Total;
                        }
                    }
                    ?>

                </tbody>
            </table>
            <?php
                
            $sqlq = 'SELECT SUM(Quantidade) AS Quantidade FROM lancamento WHERE dLancamento BETWEEN ? AND ? AND ValorUni > 9 AND Contrato_idContrato = ?';
            $stmt = $conexao->prepare($sqlq);
            $stmt->bindParam(1, $ct->dInicio);
            $stmt->bindParam(2, $ct->dFinal);
            $stmt->bindParam(3, $idContrato);
            $stmt->execute();
            $rsq = $stmt->fetch(PDO::FETCH_OBJ);
            $val = '%Desjejum%';
            $sqlq1 = "SELECT SUM(Quantidade) AS Quantidade FROM lancamento WHERE dLancamento BETWEEN ? AND ? AND Servico LIKE ? AND Contrato_idContrato = ?";
            $stmt = $conexao->prepare($sqlq1);
            $stmt->bindParam(1, $ct->dInicio);
            $stmt->bindParam(2, $ct->dFinal);
            $stmt->bindParam(3, $val);
            $stmt->bindParam(4, $idContrato);
            $stmt->execute();
            $rsq1 = $stmt->fetch(PDO::FETCH_OBJ);
            ?>
            
            </div>
            <p><strong>Quantidade Consumida Refeição:</strong> <?php echo utf8_decode($rsq->Quantidade); ?></p>
            <p><strong>Quantidade Consumida Desjejum:</strong> <?php echo !empty($rsq1->Quantidade) ? utf8_decode($rsq1->Quantidade) : '0'; ?></p>
            <p>Valor Total da medição <strong>R$ <?php echo number_format($vTotal,2,',','.'); ?></strong>.</p>
            <?php if($ct->Desconto > 0){ ?>

            <p>Valor do Desconto: R$ <strong><?php echo number_format($ct->Desconto,2,',','.'); ?></strong>.</p>
            <p>Valor Final da Medição: R$ <strong><?php echo number_format($total2-$ct->Desconto,2,',','.'); ?></strong>.</p>
            <?php } ?>
        </div>
    </body>
</html>
<?php
$html = ob_get_clean();
//$html = utf8_encode($html);
define('MPDF_PATH', '../control/classes/mpdf60/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF('utf-8','A4-L',7,'MS Serif',5,5,5,5);
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='utf-8';
//$mpdf->SetHeader('Medição | NUTRIBEM REFEIÇÕES COLETIVAS |{PAGENO}');
$mpdf->SetAuthor('Gestor de Contratos');

// carrega uma folha de estilo – MAGICA!!!
$stylesheet = file_get_contents('../css/pdf.css');

// incorpora a folha de estilo ao PDF
// O parâmetro 1 diz que este é um css/style e deverá ser interpretado como tal
$mpdf->WriteHTML($stylesheet,1);
//Algumas configurações do PDF
$mpdf->SetDisplayMode('fullpage');
// modo de visualização
$mpdf->SetFooter('{DATE j/m/Y H:i}|NUTRIBEM REFEIÇÕES COLETIVAS|Gestor de Contrato');
//bacana este rodape, nao eh mesmo?      

$arquivo = $ct->Medicao.'.pdf';
$mpdf->WriteHTML($html,2);
$mpdf->Output();
exit($arquivo);
?>