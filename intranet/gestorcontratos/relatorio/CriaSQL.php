<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/arquivo/funcao/Outras.php");
    require_once("../control/arquivo/header/Header.php");
    require_once("../control/banco/conexao.php");
    $conexao = conexao::getInstance();
    $sql = "SELECT p.fPagamento, p.Condicao, c.idContrato, c.nContrato FROM contrato c INNER JOIN proposta p ON p.idProposta = c.Proposta_idProposta ORDER BY c.idContrato";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $r = $stmt->fetchAll(PDO::FETCH_OBJ);
    $consulta = "";
    foreach($r as $rs){
        $consulta .= "UPDATE contrato SET fPagamento = '".$rs->fPagamento."', Condicao = '".$rs->Condicao."' WHERE idContrato = ".$rs->idContrato." AND nContrato = '".$rs->nContrato."';";
        $consulta .= "<br />";
    }
    echo $consulta;
}
?>