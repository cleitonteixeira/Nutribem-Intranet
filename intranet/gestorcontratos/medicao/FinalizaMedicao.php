<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Outras.php");
require_once("../control/arquivo/funcao/Dados.php");
require_once("../control/arquivo/header/Header.php");
$conexao = conexao::getInstance();
$conexao->beginTransaction();
try{
    $data = date("Y-m-d H:i:s");
    $idMedicao = $_POST['idMedicao'];
    $sql = "UPDATE medicao SET Finalizada = 'S', FinalizadaPor = ?, dFinalizada = ? WHERE idMedicao = ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $_SESSION['Nome']);
    $stmt->bindParam(2, $data);
    $stmt->bindParam(3, $idMedicao);
    $stmt->execute();
    $conexao->commit();
    echo '
        <div class="col-xs-6 col-md-6 col-lg-6">
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong> Mediçao Finalizada!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'medicao/MedicoesAprovadas.php">aqui</a>.</p>
            </div>
        </div>
        ';
    header('Refresh: 5;URL='.BASE.'medicao/MedicoesAprovadas.php');exit;
}catch(PDOException $e){
    $conexao->rollBack();
    echo '
        <div class="col-xs-6 col-md-6 col-lg-6">
            <div class="alert alert-danger">
                <p><strong>Falha!</strong> Mediçao Não Finalizada</p>
                <p><strong>O sistema apresentou o seguinte erro: </strong>'.$e.'</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'medicao/MedicoesAprovadas.php">aqui</a>.</p>
            </div>
        </div>
        ';
    header('Refresh: 5;URL='.BASE.'medicao/MedicoesAprovadas.php');exit;
}
?>