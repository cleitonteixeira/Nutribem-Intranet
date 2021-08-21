<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
$di = explode('/',$_POST['dataIN']);
$dataIN = $di[2]."-".$di[1]."-".$di[0];
$df = explode('/',$_POST['dataFN']);
$dataFN = $df[2]."-".$df[1]."-".$df[0];
$sqli = "SELECT c.Nome AS Unidade FROM unidadefaturamento u INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro WHERE u.idUnidadeFaturamento = ?;";
$stmt = $conexao->prepare($sqli);
$stmt->bindParam(1, $_POST['Unidade']);
$stmt->execute();
$rs = $stmt->fetch(PDO::FETCH_OBJ);
header("Content-type: application/msexcel");
header("Content-Disposition: attachment; filename=Consumo_".$rs->Unidade."_".date('d-m-Y_H:i:s').".xls");
echo "<table border='1'>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='2'>CONSUMO UNIDADE: ".$rs->Unidade."</th>";
echo "<th colspan='2'>PERIODO:".$_POST['dataIN']." - ".$_POST['dataFN']."</th>";
echo "</tr>";
echo "<tr>";
echo "<th>SERVICO</th>";
echo "<th>QUANTIDADE</th>";
echo "<th>VALOR UNITARIO</th>";
echo "<th>TOTAL</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

$sqli = "SELECT SUM(Quantidade) AS Quantidade, Servico, ValorUni, (ValorUni*SUM(Quantidade)) AS Total FROM lancamento WHERE Unidade_idUnidade = ? AND Quantidade > 0 AND dLancamento BETWEEN ? AND ? GROUP BY Servico, ValorUni;";
$stmt = $conexao->prepare($sqli);
$stmt->bindParam(1, $_POST['Unidade']);
$stmt->bindParam(2, $dataIN);
$stmt->bindParam(3, $dataFN);
$stmt->execute();
$res = $stmt->fetchAll(PDO::FETCH_OBJ);
foreach($res as $r){
    echo "<tr>";
    echo "<td>". $r->Servico ."</td>";
    echo "<td>". $r->Quantidade ."</td>";
    echo "<td>R$ ". number_format($r->ValorUni,2,',','.') ."</td>";
    echo "<td>R$ ". number_format($r->Total,2,',','.') ."</td>";
    echo "</tr>";
}
echo "</table>";
?>