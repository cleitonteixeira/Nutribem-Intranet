<?php
    if (!isset($_SESSION)) session_start();
    require_once("../control/arquivo/funcao/Outras.php");
    require_once("../control/arquivo/funcao/Dados.php");
    require_once("../control/banco/conexao.php");
    function upQuantidade($x){
        $conexao = conexao::getInstance();
        $dados = array();
        try{
            $conexao->beginTransaction();
            $i = $x['alterQuant'];
            $sql = 'SELECT Contrato_idContrato, dLancamento, Quantidade, Servico FROM lancamento WHERE idLancamento = ?;';
            $stm = $conexao->prepare($sql);
            $stm->bindParam(1, $x['alterQuant']);
            $stm->execute();
            $rs = $stm->fetch(PDO::FETCH_OBJ);
            $sql = 'UPDATE lancamento SET Quantidade = ? WHERE idLancamento = ?;';
            $stm = $conexao->prepare($sql);
            $stm->bindParam(1, $x['quant']);
            $stm->bindParam(2, $i);
            $stm->execute();
            
            $desc = utf8_encode("O Evento ".$rs->Servico." do dia ". date("d/m/Y", strtotime($rs->dLancamento))." foi alterado de ".$rs->Quantidade." para ".$x['quant']).".";
            $jus = utf8_encode($x['justificativa']);
            
            $date = date("Y-m-d H:i:s");
            $sql = "INSERT INTO historicolancamento (Usuario_idUsuario, Contrato_idContrato, dLancamento, dAlteracao, Descricao, Justificativa, Lancamento_idLancamento) VALUES (?, ?, ?, ?, ?, ?, ?);";
            $stm = $conexao->prepare($sql);
            $stm->bindParam(1, $_SESSION['idusuarios']);
            $stm->bindParam(2, $rs->Contrato_idContrato);
            $stm->bindParam(3, $rs->dLancamento);
            $stm->bindParam(4, $date);
            $stm->bindParam(5, $desc);
            $stm->bindParam(6, $jus);
            $stm->bindParam(7, $i);
            $stm->execute();
            
            $Itens = [
                "resultado" => utf8_decode("Sucesso")
            ];
            array_push($dados, $Itens);
            $conexao->commit();
        }catch(PDOException $e){

            echo $e;
            $Itens = [
                "resultado" => utf8_decode("Erro")
                ];
            print_r("SELECT Contrato_idContrato, dLancamento, Quantidade, Servico FROM lancamento WHERE idLancamento =".$x['alterQuant']);
            array_push($dados, $Itens);
            $conexao->rollBack();
        }

        echo json_encode($dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    if(isset($_POST['alterQuant'])){
        $valor1 = isset( $_POST['alterQuant'] ) ? (int)$_POST['alterQuant'] : 0;
		$valor2 = isset( $_POST['quant'] ) ? (int)$_POST['quant'] : 0;        
        upQuantidade($_POST);
    }
?>