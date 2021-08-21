<?php
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
header("Content-type: application/msexcel");
header("Content-Disposition: attachment; filename=Ultimos_Lancamentos_".date('d-m-Y_H:i:s').".xls");
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Unidade</th>";
echo "<th>Data</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

$sqli = "SELECT DISTINCT(l.Unidade_idUnidade) AS Unidade, c.Nome, MAX(l.dCadastro) AS DataCadastro FROM lancamento l INNER JOIN unidadefaturamento u ON u.idUnidadeFaturamento = l.Unidade_idUnidade INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro GROUP BY l.Unidade_idUnidade ORDER BY l.dCadastro ASC;";
$stmt = $conexao->prepare($sqli);
$stmt->execute();
$res = $stmt->fetchAll(PDO::FETCH_OBJ);
foreach($res as $r){
    echo "<tr>";
    echo "<td>". $r->Unidade ."</td>";
    echo "<td>". utf8_decode($r->Nome) ."</td>";
    echo "<td>". date("d/m/Y", strtotime($r->DataCadastro)) ."</td>";
    echo "</tr>";
}
echo "</table>";
?>