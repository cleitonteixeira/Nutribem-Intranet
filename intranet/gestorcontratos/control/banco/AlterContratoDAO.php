<?php
if (!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$Trocas     = array(' ','-','.',')','(','/');
$troca      = array('linha', 'line','');
$conexao    = conexao::getInstance();
/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/
$x  = 0;
$x1 = 0;
$c = array("1");
$c1 = array("1");

if(!empty($_POST['hidden1'])){
    $cj = str_replace($troca,'',$_POST['hidden1']);
    $cj  = explode(",",$cj);
    foreach($cj as $cct){
        array_push($c, $cct);
    }
}
if(!empty($_POST['hidden5'])){
    $cj = str_replace($troca,'',$_POST['hidden5']);
    $cj  = explode(",",$cj);
    foreach($cj as $cct){
        array_push($c1, $cct);
    }
}

$cont   = count($c);
$cont1  = count($c1);

if(isset($_POST['cont']) && $_POST['cont']  ==  "alter"){
    try{
        $conexao->beginTransaction();
        $Sql = "SELECT * FROM contrato WHERE idContrato = ?;";
        $stm = $conexao->prepare($Sql);
        $stm->bindParam(1, $_POST['Contrato']);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_OBJ);
        $SQL = "INSERT INTO itensproposta (Proposta_idProposta, Servico, ValorUni) VALUES (?,?,?);";
        $stmt = $conexao->prepare($SQL);
        $Itens = 0;
        while($x < $cont){
            $Servico = utf8_encode(anti_injection($_POST['Evento'.$c[$x]]));
            $Valor = str_replace(".","",$_POST['valor'.$c[$x]]);
            $Valor = str_replace(",",".", $Valor);
            $stmt->bindParam(1, $result->Proposta_idProposta);
            $stmt->bindParam(2, $Servico);
            $stmt->bindParam(3, $Valor);
            $ItemServ = $stmt->execute();
            if($ItemServ){
                $Itens +=1;
            }
            $x+=1;
        }
        $Itens1 = 0;
        $conexao->commit();
        echo '
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong>Cadastrado com sucesso!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/EditarContrato.php">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'clientes/EditarContrato.php');exit;
    }catch(PDOException $erro_cad){
        $conexao->rollBack();
        echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao Alteração de Contrato...</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$erro_cad.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/EditarContrato.php">aqui</a>.</p>
			</div>
			';
        header('Refresh: 5;URL='.BASE.'clientes/EditarContrato.php');exit;
    }
}else{
    header("Location: ".BASE);
}
?>