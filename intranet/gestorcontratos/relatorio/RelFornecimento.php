<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}
$unidade = '';
foreach ($_POST['Unidade'] as $u) {
    $unidade .= $u.',';
}
$unidade = substr_replace($unidade, '', -1);
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
$di = explode('/',$_POST['dataIN']);
$dataIN = $di[2]."-".$di[1]."-".$di[0];
$df = explode('/',$_POST['dataFN']);
$dataFN = $df[2]."-".$df[1]."-".$df[0];
header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Relatorio_Fornecimento_".date('d-m-Y_H:i:s').".xls");
header('Cache-Control: max-age=0');
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo "<table border='1'>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='8'><h2>PERIODO ".$_POST['dataIN']." ATE ".$_POST['dataFN']."</h2> </th>";
echo "</tr>";
echo "<tr>";
echo "<th>UNIDADE</th>";
echo "<th>CLIENTE</th>";
echo "<th>CENTRO DE CUSTO</th>";
echo "<th>MES/ANO</th>";
echo "<th>SERVICO</th>";
echo "<th>QUANTIDADE</th>";
echo "<th>VALOR UNITARIO</th>";
echo "<th>TOTAL</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

$sqli = "SELECT cdu.Nome as Unidade, u.idUnidadeFaturamento, cdt.Nome AS Cliente, ct.cCusto, l.Servico, SUM(l.Quantidade) AS Total, l.ValorUni AS Valor, (SUM(l.Quantidade)*l.ValorUni) AS 'V_TOTAL', DATE_FORMAT(l.dLancamento, '%Y-%m') AS MesAno FROM lancamento l INNER JOIN contrato ct ON ct.idContrato = l.Contrato_idContrato INNER JOIN contratante ctt ON ctt.idContratante = ct.Contratante_idContratante INNER JOIN cadastro cdt ON cdt.idCadastro = ctt.Cadastro_idCadastro INNER JOIN unidadefaturamento u ON u.idUnidadeFaturamento = l.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = u.Cadastro_idCadastro WHERE dLancamento BETWEEN ? AND ? AND l.Unidade_idUnidade IN (".$unidade.") GROUP BY l.Unidade_idUnidade, MONTH(l.dLancamento), l.Contrato_idContrato, ct.cCusto, l.Servico, l.ValorUni ORDER BY l.Unidade_idUnidade, MONTH(l.dLancamento), cdt.Nome, ct.cCusto";
$stmt = $conexao->prepare($sqli);
$stmt->bindParam(1, $dataIN);
$stmt->bindParam(2, $dataFN);
$stmt->execute();
$res = $stmt->fetchAll(PDO::FETCH_OBJ);
$Anterior = '';
$SGeral = 0;
$Atual = '';
foreach($res as $r){
    $Atual = $r->MesAno;
    if($Anterior == ''){
        $Anterior = $r->MesAno;
        $UAnterior = utf8_decode($r->Unidade);
    }elseif($Atual != $Anterior){
        $MesAno = explode('-', $Anterior);
        $sql = "SELECT (SUM(Quantidade)*ValorUni) AS TOTAL FROM lancamento WHERE Unidade_idUnidade = ? AND MONTH(dLancamento) = ? AND YEAR(dLancamento) = ? AND Quantidade > 0 GROUP BY Servico, ValorUni;";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $r->idUnidadeFaturamento);
        $stmt->bindParam(2, $MesAno[1]);
        $stmt->bindParam(3, $MesAno[0]);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
        $somador = 0;
        foreach($rs as $rx){
            $somador += $rx->TOTAL;
        }
        echo "<tr>";
        echo "<td colspan='6' ></td><td><strong>TOTAL: </strong></td><td>R$ ".number_format($somador,2,',','.')."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td colspan='8'></td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<th>UNIDADE</th>";
        echo "<th>CLIENTE</th>";
        echo "<th>CENTRO DE CUSTO</th>";
        echo "<th>MES/ANO</th>";
        echo "<th>SERVICO</th>";
        echo "<th>QUANTIDADE</th>";
        echo "<th>VALOR UNITARIO</th>";
        echo "<th>TOTAL</th>";
        echo "</tr>";
        $Anterior = $r->MesAno;
        $SGeral += $somador;
        $somador = 0;
    }
    if($UAnterior != utf8_decode($r->Unidade)){
        $UAnterior = utf8_decode($r->Unidade);
    }
    echo "<tr >";
    echo "<td>". utf8_decode($r->Unidade) ."</td>";
    echo "<td>". utf8_decode($r->Cliente) ."</td>";
    echo "<td>". $r->cCusto ."</td>";
    echo "<td>". date("m/Y", strtotime($r->MesAno)) ."</td>";
    echo "<td>". utf8_decode($r->Servico) ."</td>";
    echo "<td>". $r->Total ."</td>";
    echo "<td>R$ ". number_format($r->Valor,2,',','.') ."</td>";
    echo "<td>R$ ". number_format($r->V_TOTAL,2,',','.') ."</td>";
    echo "</tr>";
    $g = $r;
}
$g->idUnidadeFaturamento;
$MesAno1 = explode('-', $Atual);
$sql2 = "SELECT (SUM(Quantidade)*ValorUni) AS TOTAL FROM lancamento WHERE Unidade_idUnidade = ? AND MONTH(dLancamento) = ? AND YEAR(dLancamento) = ? AND Quantidade > 0 GROUP BY Servico, ValorUni;";
$stmt1 = $conexao->prepare($sql2);
$stmt1->bindParam(1, $g->idUnidadeFaturamento);
$stmt1->bindParam(2, $MesAno1[1]);
$stmt1->bindParam(3, $MesAno1[0]);
$stmt1->execute();
$rs1 = $stmt1->fetchAll(PDO::FETCH_OBJ);
$xSomador = 0;
foreach($rs1 as $rx1){
    $xSomador += $rx1->TOTAL;
}
echo "<tr>";
echo "<td colspan='6' ></td><td><strong>TOTAL: </strong></td><td>R$ ".number_format($xSomador,2,',','.')."</td>";
echo "</tr>";

echo "<tr>";
echo "<td colspan='6' ></td><td><strong>TOTAL GERAL: </strong></td><td>R$ ".number_format($SGeral,2,',','.')."</td>";
echo "</tr>";
echo "</table>";
?>