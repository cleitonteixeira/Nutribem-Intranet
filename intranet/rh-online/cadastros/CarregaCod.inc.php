<?php
    require_once("../control/banco/conexao.php");
    $valor = isset( $_GET['cod'] ) ? (int)$_GET['cod'] : 0;
    getCargo2($valor);
    function getCargo2($cargo){
        $conexao = conexao::getInstance();
        $sql = 'SELECT MAX(col.CodColaborador) as CodColaborador, con.Unidade_idUnidade FROM Colaborador col INNER JOIN Cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN Contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN Cargo ca ON ca.idCargo = con.Cargo_idCargo WHERE con.Unidade_idUnidade = ? LIMIT 1';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cargo);
        $stm->execute();
        $row = $stm->fetch(PDO::FETCH_OBJ);

        $CodEx = $row->CodColaborador;
        $ex = explode('.', $CodEx);
        $ultima = $ex[count($ex)-1];

        $Veri = $ultima+1;
        $cod = str_pad($cargo,2,0,STR_PAD_LEFT).".". str_pad($Veri,2,0,STR_PAD_LEFT);

        while($Veri <= $ultima){
            echo $Veri += 1;
            $cod = str_pad($row->Unidade_idUnidade,2,0,STR_PAD_LEFT).".".str_pad($Veri,2,0,STR_PAD_LEFT);
        }

        $Dados = array();
        $Itens = [
            "Cod" => $cod,
        ];
        array_push($Dados, $Itens);
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;	
    }
?>