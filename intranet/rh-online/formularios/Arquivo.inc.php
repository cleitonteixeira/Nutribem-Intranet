<?php
if (!isset($_SESSION)) session_start();
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/funcao/Dados.php");

if(isset( $_POST['FiltroCompleto'] )):
    $param1 = isset( $_POST['pUnidade'] ) ? (int)$_POST['pUnidade'] : 0;
    $param2 = isset( $_POST['pUsuario'] ) ? (int)$_POST['pUsuario'] : 0;
    $param3 = isset( $_POST['pTipo'] ) ? $_POST['pTipo'] : "";
    getFiltroCompleto($param1,$param2,$param3);
endif;

/* INICIO MUDA UNIDADE */
if(isset( $_POST['pUnidadeS'] )):
    $valor = isset( $_POST['pUnidadeS'] ) ? (int)$_POST['pUnidadeS'] : 0;
    getSolicitanteU($valor);
endif;
if(isset( $_POST['pUnidadeT'] )):
    $valor = isset( $_POST['pUnidadeT'] ) ? (int)$_POST['pUnidadeT'] : 0;
    getTipoU($valor);
endif;
/* FIM MUDA UNIDADE */

/* INICIO MUDA SOLICITANTE */
if(isset( $_POST['pSolicitanteU'] )):
    $valor = isset( $_POST['pSolicitanteU'] ) ? (int)$_POST['pSolicitanteU'] : 0;
    getUnidadeS($valor);
endif;
if(isset( $_POST['pSolicitanteT'] )):
    $valor = isset( $_POST['pSolicitanteT'] ) ? (int)$_POST['pSolicitanteT'] : 0;
    getTipoS($valor);
endif;
/* FIM MUDA SOLICITANTE */

/* INICIO MUDA TIPO */
if(isset( $_POST['pTipoU'] )):
    $valor = isset( $_POST['pTipoU'] ) ? $_POST['pTipoU'] : "";
    getUnidadeT($valor);
endif;
if(isset( $_POST['pTipoS'] )):
    $valor = isset( $_POST['pTipoS'] ) ? $_POST['pTipoS'] : "";
    getSolicitanteT($valor);
endif;
/* FIM MUDA TIPO */


function getSolicitanteU($cod){
    $conexao = conexao::getInstance();
    $var = "";
    if($cod > 0){
        $var = "= ?";
        $var_c = $cod;
    }else{
        $var = "IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)";
        $var_c = $_SESSION['idusuarios'];
    }
    $sql = "SELECT DISTINCT(p.Usuario_idUsuario) AS idSolicitante, u.Nome AS Solicitante FROM pendencias p INNER JOIN usuarios u ON u.idusuarios = p.Usuario_idUsuario WHERE Unidade_idUnidade ".$var." AND Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)') AND Data < CURDATE() - INTERVAL 90 DAY ORDER BY Data DESC;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $var_c);
    $stm->execute();
    $Dados = array();
    $r = $stm->fetchAll(PDO::FETCH_OBJ);
    foreach($r as $row){
        $Itens = [
            "idSolicitante" => utf8_decode($row->idSolicitante),
            "Solicitante"   => utf8_decode($row->Solicitante),
            ];
        array_push($Dados, $Itens);
    }
    echo json_encode($Dados);
    //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;
}
function getTipoU($cod){
    $conexao = conexao::getInstance();
    $var = "";
    if($cod > 0){
        $var = "= ?";
        $var_c = $cod;
    }else{
        $var = "IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)";
        $var_c = $_SESSION['idusuarios'];
    }
    $sql = "SELECT DISTINCT(p.Tipo) AS Tipo FROM pendencias p INNER JOIN usuarios u ON u.idusuarios = p.Usuario_idUsuario WHERE p.Unidade_idUnidade ".$var." AND p.Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)') AND Data < CURDATE() - INTERVAL 90 DAY ORDER BY Data DESC;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $var_c);
    $stm->execute();
    $Dados = array();
    $r1 = $stm->fetchAll(PDO::FETCH_OBJ);
    foreach($r1 as $row){
        $ti = "";
        switch($row->Tipo){
            case "Ferias":
                $ti = utf8_encode("Férias");
                break;
            case "Promocao":
                $ti = utf8_encode("Promoção");
                break;
            case "Demissao":
                $ti = utf8_encode("Demissão");
                break;
            case "Admissao":
                $ti = utf8_encode("Admissão");
                break;
        }
        $Itens = [
            "Tipo" => utf8_decode($row->Tipo),
            "uTipo"   => utf8_decode($ti),
            ];
        array_push($Dados, $Itens);
    }
    echo json_encode($Dados);
    //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;
}

function getTipoS($cod){
    $conexao = conexao::getInstance();
    $var = "";
    if($cod > 0){
        $var = "p.Usuario_idUsuario = ?";
        $var_c = $cod;
    }else{
        $var = "p.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)";
        $var_c = $_SESSION['idusuarios'];
    }
    $sql = "SELECT DISTINCT(p.Tipo) AS Tipo FROM pendencias p INNER JOIN usuarios u ON u.idusuarios = p.Usuario_idUsuario WHERE ".$var." AND Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)') AND Data < CURDATE() - INTERVAL 90 DAY ORDER BY Data DESC;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $var_c);
    $stm->execute();
    $Dados = array();
    $r = $stm->fetchAll(PDO::FETCH_OBJ);
    foreach($r as $row){
        $ti = "";
        switch($row->Tipo){
            case "Ferias":
                $ti = utf8_encode("Férias");
                break;
            case "Promocao":
                $ti = utf8_encode("Promoção");
                break;
            case "Demissao":
                $ti = utf8_encode("Demissão");
                break;
            case "Admissao":
                $ti = utf8_encode("Admissão");
                break;
        }
        $Itens = [
            "Tipo" => utf8_decode($row->Tipo),
            "uTipo"   => utf8_decode($ti),
            ];
        array_push($Dados, $Itens);
    }
    echo json_encode($Dados);
    //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;
}
function getUnidadeS($cod){
    $conexao = conexao::getInstance();
    $var = "";
    if($cod > 0){
        $var = "p.Usuario_idUsuario = ?";
        $var_c = $cod;
    }else{
        $var = "p.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)";
        $var_c = $_SESSION['idusuarios'];
    }
    $sql = "SELECT DISTINCT(p.Unidade_idUnidade) AS idUnidade, c.Nome AS Unidade  FROM pendencias p INNER JOIN unidade u ON u.idUnidade = p.Unidade_idUnidade INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro WHERE ".$var." AND Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)') AND Data < CURDATE() - INTERVAL 90 DAY ORDER BY Data DESC;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $var_c);
    $stm->execute();
    $Dados = array();
    $r = $stm->fetchAll(PDO::FETCH_OBJ);
    foreach($r as $row){
        $Itens = [
            "idUnidade" => utf8_decode($row->idUnidade),
            "Unidade"   => utf8_decode($row->Unidade),
            ];
        array_push($Dados, $Itens);
    }
    echo json_encode($Dados);
    //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;
}

function getUnidadeT($cod){
    $conexao = conexao::getInstance();
    $var = "";
    if($cod > 0){
        $var = "p.Tipo = ?";
        $var_c = $cod;
    }else{
        $var = "p.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)";
        $var_c = $_SESSION['idusuarios'];
    }
    $sql = "SELECT DISTINCT(p.Unidade_idUnidade) AS idUnidade, c.Nome AS Unidade  FROM pendencias p INNER JOIN unidade u ON u.idUnidade = p.Unidade_idUnidade INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro WHERE ".$var." AND Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)') AND Data < CURDATE() - INTERVAL 90 DAY ORDER BY Data DESC;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $var_c);
    $stm->execute();
    $Dados = array();
    $r = $stm->fetchAll(PDO::FETCH_OBJ);
    foreach($r as $row){
        $Itens = [
            "idUnidade" => utf8_decode($row->idUnidade),
            "Unidade"   => utf8_decode($row->Unidade),
            ];
        array_push($Dados, $Itens);
    }
    echo json_encode($Dados);
    //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;
}
function getSolicitanteT($cod){
    $conexao = conexao::getInstance();
    $var = "";
    if($cod > 0){
        $var = "Tipo = ?";
        $var_c = $cod;
    }else{
        $var = "IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)";
        $var_c = $_SESSION['idusuarios'];
    }
    $sql = "SELECT DISTINCT(p.Usuario_idUsuario) AS idSolicitante, u.Nome AS Solicitante FROM pendencias p INNER JOIN usuarios u ON u.idusuarios = p.Usuario_idUsuario WHERE ".$var." AND Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)') AND Data < CURDATE() - INTERVAL 90 DAY ORDER BY Data DESC;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $var_c);
    $stm->execute();
    $Dados = array();
    $r = $stm->fetchAll(PDO::FETCH_OBJ);
    foreach($r as $row){
        $Itens = [
            "idSolicitante" => utf8_decode($row->idSolicitante),
            "Solicitante"   => utf8_decode($row->Solicitante),
            ];
        array_push($Dados, $Itens);
    }
    echo json_encode($Dados);
    //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;
}

function getFiltroCompleto($param1,$param2,$param3){
    $conexao = conexao::getInstance();
    $var = "";
    $x = false;
    $y = false;
    $z = false;
    if($param1 > 0){
        $var = "p.Unidade_idUnidade = ?";
        $x = true;
    }
    if($param2 > 0){
        if($x){
            $var .= " AND p.Usuario_idUsuario = ?";
        }else{
            $var = "p.Usuario_idUsuario = ?";
        }
        $y = true;
    }
    if($param3 != ""){
        if($x){
            $var .= " AND p.Tipo = ?";
        }elseif($y){
            $var .= " AND p.Tipo = ?";
        }else{
            $var = "p.Tipo = ?";
        }
        $z = true;
    }
    if($param1 == 0 && $param2 ==    0 && $param3 == ""){
        $var = "p.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)";
    }
    $sql = "SELECT * FROM pendencias p WHERE ".$var." AND p.Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)') AND Data < CURDATE() - INTERVAL 90 DAY ORDER BY p.Data DESC;";
    $stm = $conexao->prepare($sql);
    if($param1 > 0 && $param2 > 0 && $param3 != ""){
        $stm->bindParam(1, $param1);
        $stm->bindParam(2, $param2);
        $stm->bindParam(3, $param3);
    }
    if($param1 > 0 && $param2 > 0 && $param3 == ""){
        $stm->bindParam(1, $param1);
        $stm->bindParam(2, $param2);
    }
    if($param1 > 0 && $param2 == 0 && $param3 != ""){
        $stm->bindParam(1, $param1);
        $stm->bindParam(2, $param3);
    }
    if($param1 == 0 && $param2 > 0 && $param3 != ""){
        $stm->bindParam(1, $param2);
        $stm->bindParam(2, $param3);
    }
    if($param1 > 0 && $param2 == 0 && $param3 == ""){
        $stm->bindParam(1, $param1);
    }
    if($param1 == 0 && $param2 > 0 && $param3 == ""){
        $stm->bindParam(1, $param2);
    }
    if($param1 == 0 && $param2 == 0 && $param3 != ""){
        $stm->bindParam(1, $param3);
    }
    if($param1 == 0 && $param2 == 0 && $param3 == ""){
        $stm->bindParam(1, $_SESSION['idusuarios']);
    }
    $stm->execute();
    $r = $stm->fetchAll(PDO::FETCH_OBJ);
    $Dados = array();
    foreach($r as $row){
        $sql = "SELECT  u.Nome AS Solicitante, ca.Nome AS Unidade FROM usuarios u INNER JOIN usuarios c ON c.idUsuarios = ? INNER JOIN unidade un ON un.idUnidade = ? INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro WHERE u.idUsuarios = ? LIMIT 1";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $row->Responsavel_Colaborador);
        $stmt->bindParam(2, $row->Unidade_idUnidade);
        $stmt->bindParam(3, $row->Usuario_idUsuario);
        $stmt->execute();     
        $res = $stmt->fetch(PDO::FETCH_OBJ);
        $Solicitante    = utf8_decode($res->Solicitante);
        $Unidade        = utf8_decode($res->Unidade);
        $tabela         = strtolower($row->Tipo);
        $campo          = $row->Tipo;
        $sqli = "SELECT cad.Nome as Colaborador FROM ".$tabela." p INNER JOIN colaborador c ON c.idColaborador = p.Colaborador_idColaborador INNER JOIN cadastro cad ON cad.idCadastro = c.Cadastro_idCadastro WHERE p.id".$campo." = ?;";
        $stmtt = $conexao->prepare($sqli);
        $stmtt->bindParam(1, $row->CodTipo);
        $stmtt->execute();
        $co = $stmtt->fetch(PDO::FETCH_OBJ);
        $Col    = $co->Colaborador;
        $Col    = mb_strtoupper($Col,"UTF-8");
        $ti     = "";
        switch($row->Tipo){
            case "Ferias":
                $ti = utf8_encode("Férias");
                break;
            case "Promocao":
                $ti = utf8_encode("Promoção");
                break;
            case "Demissao":
                $ti = utf8_encode("Demissão");
                break;
            case "Admissao":
                $ti = utf8_encode("Admissão");
                break;
        }
        $Itens = [
            "Unidade"	 	    => $Unidade,
            "Solicitante"    	=> $Solicitante,
            "Tipo"   			=> utf8_decode($ti),
            "Data"       		=> utf8_decode(date("d/m/Y",strtotime($row->Data))),
            "Colaborador"		=> $Col,
            "Andamento"			=> utf8_decode($row->Resultado),
            "idPendencia"		=> $row->idPendencias,
        ];
        array_push($Dados, $Itens);
    }
    echo json_encode($Dados);
    //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    $pdo = null;
}
?>