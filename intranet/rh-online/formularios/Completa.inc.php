<?php
    require_once("../control/banco/conexao.php");
    require_once("../control/arquivo/funcao/Dados.php");
    if(isset( $_GET['cargo'] )):
        $valor = isset( $_GET['cargo'] ) ? (int)$_GET['cargo'] : 0;
        getCol($valor);
    endif;
    if(isset( $_GET['cod'] )):
        $valor = isset( $_GET['cod'] ) ? (int)$_GET['cod'] : 0;
        getDados($valor);
    endif;
    if(isset( $_GET['codi'] )):
        $valor = isset( $_GET['codi'] ) ? (int)$_GET['codi'] : 0;
        getComplemento($valor);
    endif;
    if(isset( $_GET['sub'] )):
        $valor = isset( $_GET['sub'] ) ? (int)$_GET['sub'] : 0;
        getColSub($valor);
    endif;
    if(isset( $_GET['ca'] )):
        $valor = isset( $_GET['ca'] ) ? (int)$_GET['ca'] : 0;
        getCaSub($valor);
    endif;
    function getCol($cargo){
        $conexao = conexao::getInstance();
        $sql = 'SELECT ca.CodCargo, ca.Funcao, ca.Salario, col.CodColaborador, con.dAdmissao, cad.Nome,(SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER  JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ  FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = con.Unidade_idUnidade INNER JOIN cargo ca ON ca.idCargo = con.Cargo_idCargo WHERE col.idColaborador = ?';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cargo);
        $stm->execute();
        $Dados = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)):
            $Itens = [
                "Colaborador"	 	=> utf8_decode($row->Nome),
                "CodColaborador" 	=> utf8_decode($row->CodColaborador),
                "Empresa" 			=> strtoupper(utf8_decode($row->Empresa)),
                "CNPJ" 				=> CNPJ_Padrao($row->CNPJ),
                "dAdmissao" 		=> date('d/m/Y',strtotime($row->dAdmissao)),
                "Cargo" 			=> $row->CodCargo."-".$row->Funcao,
                "Salario" 			=> "R$ ". number_format($row->Salario,2,',','.'),
            ];
            array_push($Dados, $Itens);
        endwhile;
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;	
    }
    function getDados($cod){
        $conexao = conexao::getInstance();
        $sql = "SELECT ca.* FROM cargo ca INNER JOIN colaborador col ON col.idColaborador = ? INNER JOIN contratacao co ON co.idContratacao = col.Contratacao_idContratacao WHERE ca.idCargo <> co.Cargo_idCargo AND ca.Unidade_idUnidade = co.Unidade_idUnidade";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cod);
        $stm->execute();
        $row = $stm->fetchAll(PDO::FETCH_OBJ);
        $Dados = array();
        foreach($row as $r):
            $Itens = [
                "idCargo" => $r->idCargo,
                "Funcao" => $r->CodCargo." - ".$r->Funcao
            ];
            array_push($Dados, $Itens);
        endforeach;
        //echo json_encode($stm->fetch(PDO::FETCH_ASSOC));
        echo json_encode($Dados);
    }
    function getComplemento($cod){
        $conexao = conexao::getInstance();
        $sql = "SELECT * FROM cargo WHERE idCargo = ?";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cod);
        $stm->execute();
        $row = $stm->fetch(PDO::FETCH_OBJ);
        $Dados = array();
        $Itens = [
            "Cargo" => $row->Cargo." - ".$row->CBO,
            "Funcao" => $row->CodCargo." - ".$row->Funcao,
            "Salario" => "R$ ". number_format($row->Salario,2,',','.'),
        ];
        array_push($Dados, $Itens);
        //echo json_encode($stm->fetch(PDO::FETCH_ASSOC));
        echo json_encode($Dados);
    }
    function getColSub($cod){
        $conexao = conexao::getInstance();
        $sql = "SELECT col.idColaborador, col.CodColaborador, cad.Nome FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao WHERE con.dDemissao IS NOT NULL AND con.Unidade_idUnidade IN (SELECT co.Unidade_idUnidade FROM contratacao co INNER JOIN colaborador cl ON cl.idColaborador = ? WHERE co.idContratacao = cl.Contratacao_idContratacao);";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cod);
        $stm->execute();
		$row = $stm->fetchAll(PDO::FETCH_OBJ);
        $Dados = array();
        foreach($row as $r){
            $Itens = [
                "idColaborador" => $r->idColaborador,
                "CodColaborador" => $r->CodColaborador,
                "Nome" => utf8_decode($r->Nome),
            ];
            array_push($Dados, $Itens);
        }
        //echo json_encode($stm->fetch(PDO::FETCH_ASSOC));
        echo json_encode($Dados);
    }
    function getCaSub($cod){
        $conexao = conexao::getInstance();
        $sql = "SELECT ca.*, co.dDemissao, co.dAdmissao FROM cargo ca INNER JOIN colaborador col ON col.idColaborador = ? INNER JOIN contratacao co ON co.idContratacao = col.Contratacao_idContratacao WHERE ca.idCargo = co.Cargo_idCargo";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $cod);
        $stm->execute();
        $row = $stm->fetch(PDO::FETCH_OBJ);
        $Dados = array();
        $Itens = [
            "Cargo" => $row->CodCargo." - ".$row->Funcao,
            "Salario" => "R$ ". number_format($row->Salario,2,',','.'),
            "Demissao" => $row->dDemissao,
            "Admissao" => $row->dAdmissao,
        ];
        array_push($Dados, $Itens);
        //echo json_encode($stm->fetch(PDO::FETCH_ASSOC));
        echo json_encode($Dados);
    }
    
?>