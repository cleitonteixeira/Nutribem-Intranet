<?php
    require_once("../control/banco/conexao.php");
    if(isset( $_GET['Cargo'] )):
        $valor = isset( $_GET['Cargo'] ) ? (int)$_GET['Cargo'] : 0;
        getCargo($valor);
    endif;
    function getCargo($cargo){
        $conexao = conexao::getInstance();
        $sql = 'SELECT * FROM Cargo WHERE idCargo = ?';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cargo);
        $stm->execute();
        $Dados = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)):
            $Itens = [
                "Funcao" => utf8_decode($row->Funcao),
                "Salario" => number_format($row->Salario,2,',','.'),
            ];
            array_push($Dados, $Itens);
        endwhile;
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;	
    }

?>