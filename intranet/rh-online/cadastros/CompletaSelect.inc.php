<?php
    require_once("../control/banco/conexao.php");
    function getUnidade($empresa){
        $conexao = conexao::getInstance();
        $sql = 'SELECT cad.Nome, un.idUnidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.Empresa_idEmpresa = ? ORDER BY cad.Nome ';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $empresa);
        $stm->execute();
        $Dados = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
            $Itens = [
                "Nome" => utf8_decode($row->Nome),
                "idUnidade" => $row->idUnidade
            ];
            array_push($Dados, $Itens);
        }
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    function getCargo($cargo){
        $conexao = conexao::getInstance();
        $sql = 'SELECT * FROM cargo WHERE Unidade_idUnidade = ?';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cargo);
        $stm->execute();
        $Dados = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
            $Itens = [
                "CodCargo" => utf8_decode($row->CodCargo),
                "Funcao" => utf8_decode($row->Funcao),
                "CBO" => utf8_decode($row->CBO),
                "idCargo" => $row->idCargo
            ];
            array_push($Dados, $Itens);
        }
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    function getCodigo($cargo){
        $conexao = conexao::getInstance();
        $sql = 'SELECT MAX(col.CodColaborador) as CodColaborador, con.Unidade_idUnidade FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN cargo ca ON ca.idCargo = con.Cargo_idCargo WHERE con.Unidade_idUnidade = ? LIMIT 1';
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
            $Veri += 1;
            $cod = str_pad($row->Unidade_idUnidade,2,0,STR_PAD_LEFT).".".str_pad($Veri,2,0,STR_PAD_LEFT);
        }
        $Dados = array();
        $Itens = [
            "Cod" => $cod
        ];
        array_push($Dados, $Itens);
        echo json_encode($Dados);
        $pdo = null;	
    }
    function getDadoCargo($cargo){
        $conexao = conexao::getInstance();
        $sql = 'SELECT * FROM cargo WHERE idCargo = ?';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cargo);
        $stm->execute();
        $Dados = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
            $Itens = [
                "Funcao" => utf8_decode($row->Funcao),
                "Salario" => number_format($row->Salario,2,',','.')
            ];
            array_push($Dados, $Itens);
        }
        echo json_encode($Dados);
        $pdo = null;	
    }
    
    if(isset($_GET['empresa'])){
        $valor = isset( $_GET['empresa'] ) ? (int)$_GET['empresa'] : 0;
        getUnidade($valor);
    }elseif(isset($_GET['unidade'])){
        $valor = isset( $_GET['unidade'] ) ? (int)$_GET['unidade'] : 0;
        getCargo($valor); 
    }elseif(isset($_GET['cod'])){
        $valor = isset( $_GET['cod'] ) ? (int)$_GET['cod'] : 0;
        getCodigo($valor);
    }elseif(isset($_GET['Cargo'])){
        $valor = isset( $_GET['Cargo'] ) ? (int)$_GET['Cargo'] : 0;
        getDadoCargo($valor);
    }

?>