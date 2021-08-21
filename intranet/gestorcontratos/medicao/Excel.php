<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
$sql = "SELECT * FROM medicao WHERE idMedicao = ?;";
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $_GET['id']);
$stm->execute();
$ct = $stm->fetch(PDO::FETCH_OBJ);
$idContrato = $ct->Contrato_idContrato;
$sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idContrato);
$stmt->execute();
$itens = $stmt->fetchAll(PDO::FETCH_OBJ);
header("Content-type: application/msexcel");
header("Content-Disposition: attachment; filename=".$ct->Medicao.".xls");
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>DATA</th>";
foreach($itens as $i){
    echo "<th>".utf8_decode($i->Servico)."</th>";
}
echo "</tr>";
echo "</thead>";
echo "<tbody>";
$vTotal = 0;
$dataInicial = $ct->dInicio;
while(strtotime($dataInicial) <= strtotime($ct->dFinal)){
    $sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento = ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idContrato);
    $stmt->bindParam(2, $dataInicial);
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
    if($stmt->rowCount() > 0){
        echo "<tr>";
        echo "<td>".date("d/m/Y", strtotime($dataInicial))."</td>";
        foreach($rs as $x){
            $Total = $x->Quantidade * $x->ValorUni;
            $vTotal += $Total;
            echo "<td>".$x->Quantidade."</td>";
        }
        echo "</tr>";
    }else{
        echo "<tr>";
        echo "<td>".date("d/m/Y", strtotime($dataInicial))."</td>";
        foreach($itens as $i){
            echo "<td>0</td>";
        }
        echo "</tr>";
    }
    $dataInicial = date("Y-m-d",strtotime('+1 day', strtotime($dataInicial)));
}
echo "<tfoot>"; 
echo "<tr>";
echo "<td><strong>Quantidade Total:</strong></td>";
foreach($itens as $i){

    $sqli = "SELECT DISTINCT(Servico),SUM(Quantidade) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? LIMIT 1;";
    $stmt = $conexao->prepare($sqli);
    $stmt->bindParam(1, $idContrato);
    $stmt->bindParam(2, $ct->dInicio);
    $stmt->bindParam(3, $ct->dFinal);
    $stmt->bindParam(4, $i->Servico);
    $stmt->execute();
    $rs = $stmt->fetch(PDO::FETCH_OBJ);
    echo "<td>".$rs->Total."</td>";
        }
echo "</tr>";
echo "<tr>";
echo "<td><strong>Valor Unitario:</strong></td>";
foreach($itens as $i){

    $sqli = "SELECT DISTINCT(Servico), ValorUni FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? LIMIT 1;";
    $stmt = $conexao->prepare($sqli);
    $stmt->bindParam(1, $idContrato);
    $stmt->bindParam(2, $ct->dInicio);
    $stmt->bindParam(3, $ct->dFinal);
    $stmt->bindParam(4, $i->Servico);
    $stmt->execute();
    $rs = $stmt->fetch(PDO::FETCH_OBJ);
    echo "<td>".number_format($rs->ValorUni,2,',','.')."</td>";
        }
echo "</tr>";
echo "<tr>";
echo "<td><strong>Valor do Servico:</strong></td>";
$vFinal = 0;
foreach($itens as $i){

    $sqli = "SELECT DISTINCT(Servico), (SUM(Quantidade)*ValorUni) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? LIMIT 1;";
    $stmt = $conexao->prepare($sqli);
    $stmt->bindParam(1, $idContrato);
    $stmt->bindParam(2, $ct->dInicio);
    $stmt->bindParam(3, $ct->dFinal);
    $stmt->bindParam(4, $i->Servico);
    $stmt->execute();
    $rs = $stmt->fetch(PDO::FETCH_OBJ);
    $vFinal += $rs->Total;
    echo "<td>".number_format($rs->Total,2,',','.')."</td>";
        }
echo "</tr>";
echo "<tr>";
echo "<td><strong>Valor Total:</strong></td>";
echo "<td>".$vFinal."</td>";
echo "</tr>";
echo "</tfoot>";
echo "</table>";
?>