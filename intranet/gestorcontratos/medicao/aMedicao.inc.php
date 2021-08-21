<?php
    require_once("../control/banco/conexao.php");
    function upQuantidade($x,$y){
        $conexao = conexao::getInstance();
        $dados = array();
        
        $conexao->beginTransaction();
        $sql = 'UPDATE lancamento SET Quantidade = ? WHERE idLancamento = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->bindValue(2, $y);
        $up = $stm->execute();
        
        if($up){
            $Itens = [
                "resultado" => utf8_decode("Sucesso")
            ];
            array_push($dados, $Itens);
            $conexao->commit();
        }else{
            $conexao->rollBack();
            $Itens = [
                "resultado" => utf8_decode("Erro")
                ];
            array_push($dados, $Itens);
        }
        echo json_encode($dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    function upDatas($x,$y,$z){
        $conexao = conexao::getInstance();
        $dados = array();
        
        $conexao->beginTransaction();
        $sql = "UPDATE medicao SET dInicio = ?, dFinal = ?, Situacao = 'Reenviado, Aguardando Resposta' WHERE idMedicao = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $y);
        $stm->bindValue(2, $z);
        $stm->bindValue(3, $x);
        $up = $stm->execute();
        
        if($up){
            $Itens = [
                "resultado" => utf8_decode("Sucesso")
            ];
            array_push($dados, $Itens);
            $conexao->commit();
        }else{
            $conexao->rollBack();
            $Itens = [
                "resultado" => utf8_decode("Erro")
                ];
            array_push($dados, $Itens);
        }
        echo json_encode($dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    function Reenviado($x){
        $conexao = conexao::getInstance();
        $dados = array();
        $conexao->beginTransaction();
        $sql = "UPDATE medicao SET Situacao = 'Reenviado, Aguardando Resposta' WHERE idMedicao = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $up = $stm->execute();
        //echo "UPDATE medicao SET Situacao = 'Reenviado, Aguardando Resposta' WHERE idMedicao =".$x;
        if($up){
            $Itens = [
                "resultado" => utf8_decode("Sucesso")
            ];
            array_push($dados, $Itens);
            $conexao->commit();
        }else{
            $conexao->rollBack();
            $Itens = [
                "resultado" => utf8_decode("Erro")
                ];
            array_push($dados, $Itens);
        }
        echo json_encode($dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    
    if(isset($_POST['medicao1'])){
        $valor1 = isset( $_POST['medicao1'] ) ? (int)$_POST['medicao1'] : 0;
        Reenviado($valor1);
    }
    if(isset($_POST['alterQuant'])){
        $valor1 = isset( $_POST['alterQuant'] ) ? (int)$_POST['alterQuant'] : 0;
		$valor2 = isset( $_POST['quant'] ) ? (int)$_POST['quant'] : 0;        
        upQuantidade($valor2,$valor1);
    }
    if(isset($_POST['medicao'])){
        $valor1 = isset( $_POST['medicao'] ) ? (int)$_POST['medicao'] : 0;
		$valor2x = explode('/',$_POST['dInicio']);
		$valor2 = $valor2x[2]."-".$valor2x[1]."-".$valor2x[0];
		$valor3x = explode('/',$_POST['dFinal']);
		$valor3 = $valor3x[2]."-".$valor3x[1]."-".$valor3x[0];
        upDatas($valor1,$valor2,$valor3);
    }
?>