<?php
require_once("../control/banco/conexao.php");
$valor = isset( $_GET['cargo'] ) ? (int)$_GET['cargo'] : 0;
getCargo($valor); 
function getCargo($cargo){
    $conexao = conexao::getInstance();
    $sql = 'SELECT * FROM cargo WHERE idCargo = ?';
    $stm = $conexao->prepare($sql);
    $stm->bindValue(1, $cargo);
    $stm->execute();
    $Dados = array();
    while($row = $stm->fetch(PDO::FETCH_OBJ)){
        $Itens = [
            "idCargo"     => utf8_decode($row->idCargo),
            "Cargo"     => utf8_decode($row->Cargo),
            "CodCargo"  => utf8_decode($row->CodCargo),
            "CBO"       => utf8_decode($row->CBO),
            "Funcao"    => utf8_decode($row->Funcao),
            "Salario"   => number_format($row->Salario,2,',','.')
        ];
        array_push($Dados, $Itens);
    }
    echo json_encode($Dados);
$pdo = null;	
}
?>