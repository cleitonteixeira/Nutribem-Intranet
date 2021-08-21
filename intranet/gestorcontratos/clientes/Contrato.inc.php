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
    function getDadosContrato($x){
        $conexao = conexao::getInstance();
        //$dados = array();
        
        $sql = "SELECT c.*, cdt.*, em.idEmpresa AS Responsavel, cd.Nome AS Unidade FROM contrato c INNER JOIN unidadefaturamento u ON u.idUnidadeFaturamento = c.Unidade_idUnidade INNER JOIN cadastro cd ON cd.idCadastro = u.Cadastro_idCadastro INNER JOIN empresa em ON em.idEmpresa = c.Empresa_idEmpresa INNER JOIN cadastro cdt ON cdt.idCadastro = em.Cadastro_idCadastro WHERE c.idContrato = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $up = $stm->execute();
        
        //$Itens = [
          //  "resultado" => utf8_decode("Erro")
        //];
        //array_push($dados, $Itens);
        
        //echo json_encode($dados);
        echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    
    if(isset($_POST['contrato'])){
        $valor1 = isset( $_POST['contrato'] ) ? (int)$_POST['contrato'] : 0;
        getDadosContrato($valor1);
    }
   
?>