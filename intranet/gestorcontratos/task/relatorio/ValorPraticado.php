<?php
setlocale(LC_ALL, 'pt_BR.utf8');
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");
$conexao = conexao::getInstance();
header('Content-Type: application/vnd.ms-excel; charset=iso-8859-1');
header("Content-Description: PHP Generated Data" );
header("Content-Disposition: attachment; filename=Valores_Praticados".date('d-m-Y_H:i:s').".xls;" );
$tabela = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$tabela .= "<table border='1'><thead><th>UNIDADE</th><th>CLIENTE</th><th>CONTRATO</th><th>SERVICO</th><th>VALOR</th></tr></thead><tbody>";

$sqli = "SELECT cdu.Nome AS Unidade, cdc.Nome as Cliente, ct.nContrato AS Contrato, ip.Servico, ip.ValorUni AS Valor FROM itensproposta ip INNER JOIN contrato ct ON ct.Proposta_idProposta = ip.Proposta_idProposta INNER JOIN unidadefaturamento uf ON uf.idUnidadeFaturamento = ct.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = uf.Cadastro_idCadastro INNER JOIN contratante ctt ON ctt.idContratante = ct.Contratante_idContratante INNER JOIN cadastro cdc ON cdc.idCadastro = ctt.Cadastro_idCadastro ORDER BY cdu.Nome, cdc.Nome, ct.nContrato;";
$stmt = $conexao->prepare($sqli);
$stmt->execute();
$res = $stmt->fetchAll(PDO::FETCH_OBJ);
foreach($res as $r){
    $tabela .= "<tr><td>". $r->Unidade ."</td><td>". utf8_decode($r->Cliente) ."</td><td>". $r->Contrato ."</td><td>". utf8_decode($r->Servico) ."</td><td>R$ ". number_format($r->Valor,2,',','.') ."</td></tr>";
}
$tabela .="</tbody></table>";
echo $tabela;
?>