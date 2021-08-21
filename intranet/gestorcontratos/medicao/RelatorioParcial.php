<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
date_default_timezone_set( 'America/Sao_Paulo' );
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
function getNMedicao($x){
        $conexao = conexao::getInstance();
		$Ano = date("Y");
        $sql = "SELECT c.nContrato, fec.* FROM contrato c INNER JOIN fechamento fec ON fec.idFechamento = c.Fechamento INNER JOIN proposta p ON p.idProposta = c.Proposta_idProposta INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = ct.Cadastro_idCadastro WHERE c.idContrato = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
        $ct = $stm->fetch(PDO::FETCH_OBJ);
		
        $Contrato = $ct->nContrato;
        $pFechamento = $ct->Descricao;
		$Quant = 1;
        
		$nMedicao = "MD.".str_pad($Quant, 3, 0, STR_PAD_LEFT)."/".$Ano."-".$Contrato;
        
        $sql = 'SELECT Medicao FROM medicao WHERE Contrato_idContrato = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
        $medicao = array();
        //var_dump($stm->fetchAll(PDO::FETCH_OBJ));
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
        	array_push($medicao, $row->Medicao);
        }
        
        while(in_array($nMedicao, $medicao)){
        	$Quant += 1;
			$nMedicao = "MD.".str_pad($Quant, 3, 0, STR_PAD_LEFT)."/".$Ano."-".$Contrato;
        }
		$Itens = [
                "nMedicao" => utf8_decode($nMedicao),
                "Contrato" => utf8_decode($Contrato),
                "pFechamento" => utf8_decode($pFechamento),
		];
        $Dados = array();
		array_push($Dados, $Itens);
		
        echo $nMedicao;
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
$valor2x = explode('/',$_POST['dataIN']);
$valor2 = $valor2x[2]."-".$valor2x[1]."-".$valor2x[0];
$valor3x = explode('/',$_POST['dataFN']);
$valor3 = $valor3x[2]."-".$valor3x[1]."-".$valor3x[0];
$dataInicial = $valor2;
$dataInicial1 = $valor2;
$dataFinal = $valor3;
$idContrato = $_POST['contrato'];
$sql = 'SELECT p.*, cont.*, c.idContratante, c.IE, fec.Descricao, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca INNER JOIN contrato cont ON cont.idContrato = ? INNER JOIN fechamento fec ON fec.idFechamento = cont.Fechamento INNER JOIN proposta p ON p.idProposta = cont.Proposta_idProposta WHERE c.idContratante = cont.Contratante_idContratante;';
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
$idContratante = $row->idContratante;
if(empty($row->IE)){
    $IE = "ISENTO";
}else{
    $IE = $row->IE;
}
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
$sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idContrato);
$stmt->execute();
$itens = $stmt->fetchAll(PDO::FETCH_OBJ);
$sql = "SELECT cd.Nome AS Empresa, cd.CNPJ AS CNPJ, cdu.Nome AS Unidade FROM contrato c INNER JOIN unidadefaturamento uf ON uf.idUnidadeFaturamento = c.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = uf.Cadastro_idCadastro INNER JOIN empresa e ON e.idEmpresa = c.Empresa_idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = e.Cadastro_idCadastro WHERE c.idContrato = ?;";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idContrato);
$stmt->execute();
$rst = $stmt->fetch(PDO::FETCH_OBJ);
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
            <h1 class="text-center" style="margin-top: 15% !important;"><img class="text-left" src="../img/IconeRel.png" /><span class="text-center">MEDIÇÃO: <?php getNMedicao($idContrato); ?></span></h1>
            <div class="panel panel-default text-justify" style="margin-top: 5% !important;">
                <div class="panel-heading">
                    <h1 class="panel-title">Dados do Cliente</h1>
                </div>
                <div class="panel-body">
                    <div class="col-xs-7 col-md-7 col-lg-7"><p><strong>Nome: </strong><?php echo $Nome; ?></p></div>
                    <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>CNPJ: </strong><?php echo $CNPJ; ?></p></div>
                    <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>IE: </strong><?php echo $IE; ?></p></div>
                    <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>Centro de Custo: </strong><?php echo $Ccusto; ?></p></div>
                    <div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço: </strong><?php echo $Endereco; ?></p></div>	
                    <div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço de Cobranca: </strong><?php echo $eCobranca; ?></p></div>	
                    <div class="col-xs-6 col-md-6 col-lg-6"><p><strong>Fechamento de Medição: </strong><?php echo utf8_decode($row->Descricao); ?></p></div>
                    <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>Periodo Apurado: </strong><?php echo date("d/m/Y", strtotime($dataInicial)); ?> a <?php echo date("d/m/Y", strtotime($dataFinal)); ?></p></div>
                    <div class="col-xs-7 col-md-7 col-lg-7"><p><strong>Faturamento: </strong><?php echo utf8_decode($rst->Empresa)." - ".CNPJ_Padrao($rst->CNPJ); ?></p></div>
                    <div class="col-xs-4 col-md-4 col-lg-4"><strong>Unidade Faturamento: </strong><?php echo utf8_decode($rst->Unidade); ?></div>
                    <?php if(!empty($row->pCompra)){?>
                    <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>Pedido de Compra: </strong><?php echo utf8_decode($row->pCompra); ?></p></div>
                    <?php }else{ ?>
                    <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>Pedido de Compra: </strong>N/A</p></div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-lg-12 col-md-12">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Data</th>
                    <?php foreach($itens as $i){ ?>
                    <th><?php echo utf8_decode($i->Servico);?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $vTotal = 0;
                $dataInicial = $dataInicial;
                $servicos  = '';
                $x = count($itens);
                foreach($itens as $rst){
                    $servicos .= "'".$rst->Servico."'";
                    $x = $x-1;
                    if($x > 0){
                        $servicos .= ",";
                    }
                }
                while(strtotime($dataInicial) <= strtotime($dataFinal)){
                    $diaSemana = date("D", strtotime($dataInicial));
                    $Dia = '';
                    switch($diaSemana){
                        case('Mon'):
                            $Dia = 'Seg.';
                            break;
                        case('Tue'):
                            $Dia = 'Ter.';
                            break;
                        case('Wed'):
                            $Dia = 'Qua.';
                            break;
                        case('Thu'):
                            $Dia = 'Qui.';
                            break;
                        case('Fri'):
                            $Dia = 'Sex.';
                            break;
                        case('Sat'):
                            $Dia = 'Sab.';
                            break;
                        case('Sun'):
                            $Dia = 'Dom.';
                            break;
                    }
                    ?>
                    <tr>
                        <td><?php echo $Dia." ".date("d/m/Y", strtotime($dataInicial));?></td>
                    <?php
                    foreach($itens as $se){
                        $sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento = ? AND Servico = ?;";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $dataInicial);
                        $stmt->bindParam(3, $se->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetch(PDO::FETCH_OBJ);
                        ?>
                        <td><?php echo !empty($rs->Quantidade) ? $rs->Quantidade : '0'; ?></td>
                    <?php
                    }
                    ?>
                    </tr>
                    <?php
                    $dataInicial = date("Y-m-d",strtotime('+1 day', strtotime($dataInicial)));
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Total:</strong></td>
                    <?php
                    foreach($itens as $i){

                        $sqli = "SELECT DISTINCT(Servico),SUM(Quantidade) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? LIMIT 1;";
                        $stmt = $conexao->prepare($sqli);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $dataInicial1);
                        $stmt->bindParam(3, $dataFinal);
                        $stmt->bindParam(4, $i->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetch(PDO::FETCH_OBJ);
                    ?>
                    <td><?php echo $rs->Total; ?></td>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td><strong>Valor Unitário:</strong></td>
                    <?php
                    foreach($itens as $i){

                        $sqli = "SELECT DISTINCT(Servico), ValorUni FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? LIMIT 1;";
                        $stmt = $conexao->prepare($sqli);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $dataInicial1);
                        $stmt->bindParam(3, $dataFinal);
                        $stmt->bindParam(4, $i->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetch(PDO::FETCH_OBJ);
                    ?>
                    <td>R$ <?php echo number_format($rs->ValorUni,2,',','.'); ?></td>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td><strong>Valor Serviço:</strong></td>
                    <?php
                    foreach($itens as $i){
                    	$sTotal = 0;
                        $sqli = "SELECT DISTINCT(Servico), (SUM(Quantidade)*ROUND(ValorUni,2)) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? GROUP BY ValorUni;";
                        $stmt = $conexao->prepare($sqli);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $dataInicial1);
                        $stmt->bindParam(3, $dataFinal);
                        $stmt->bindParam(4, $i->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach ($rs as $a) {
                        	$sTotal += $a->Total;  
                        }
                    ?>
                    <td>R$ <?php echo number_format($sTotal,2,',','.'); ?></td>
                    <?php
                        $vTotal += $sTotal;
                    }
                    ?>
                </tr>
            </tfoot>
        </table>
            <p>Valor Total da medição <strong>R$ <?php echo number_format($vTotal,2,',','.'); ?></strong>.</p>
        </div>
        <?php
        $html = ob_get_clean();
        ob_start();
        ?>
        <div class="col-xs-12 col-lg-12 col-md-12 text-center assinatura_m1">
            <p></p>
            <div class="col-xs-offset-3 col-md-offset-3 col-lg-offset-3 col-xs-6 col-md-6 col-lg-6 text-center assinatura_m">
                <p><strong>Contratante:</strong> <?php echo $Nome; ?></p>
            </div>
        </div>
    </body>
</html>
<?php
$html2 = ob_get_clean();
//$html = utf8_encode($html);
define('MPDF_PATH', '../control/classes/mpdf60/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF('utf-8','A4-L',7,'MS Serif',5,5,5,15);
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='utf-8';
//$mpdf->SetHeader('Medição | NUTRIBEM REFEIÇÕES COLETIVAS |{PAGENO}');
$mpdf->SetAuthor('RH-Online');

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

//$arquivo = $ct->Medicao.'.pdf';
$mpdf->WriteHTML($html,2);
$mpdf->AddPage();
$mpdf->WriteHTML($html2,2);
$mpdf->Output();
exit($arquivo);
?>