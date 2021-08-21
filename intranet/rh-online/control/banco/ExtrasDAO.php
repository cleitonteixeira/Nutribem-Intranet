<?php
    /*echo '<pre>';
    var_dump($_POST);
    echo '</pre>';*/
    require_once("conexao.php");
    $conexao = conexao::getInstance();
    require_once("../arquivo/funcao/Outras.php");
    if(isset($_POST['Demitir']) && $_POST['Demitir']  ==  "Demissão"):
        $Data = Anti_Injection(utf8_encode($_POST['data']));
        $Historico = Anti_Injection(utf8_encode($_POST['Demitir']));
        $Colaborador = Anti_Injection(utf8_encode($_POST['Colaborador']));
        $Contrato = Anti_Injection(utf8_encode($_POST['Contrato']));
        $Justificativa = Anti_Injection(utf8_encode($_POST['explicacao']));
        $sql = "UPDATE contratacao SET dDemissao = ? WHERE idContratacao = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $Data);
        $stmt->bindParam(2, $Contrato);
        $stmt->execute();
        $sql = "INSERT INTO historico (Colaborador_idColaborador, Historico, Data, Justificativa) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $Colaborador);
        $stmt->bindParam(2, $Historico);
        $stmt->bindParam(3, $Data);
        $stmt->bindParam(4, $Justificativa);
        $stmt->execute();
        echo "<script>alert('Atualizado com Sucesso!');window.location.href='".BASE."pesquisas/DetalheColaborador.php?cod=".$Colaborador."';</script>";
    elseif(isset($_POST['Promover']) && $_POST['Promover'] = "Promoção"):
        $Data = Anti_Injection(utf8_encode($_POST['data']));
        $Historico = Anti_Injection(utf8_encode($_POST['Promover']));
        $Colaborador = Anti_Injection(utf8_encode($_POST['Colaborador']));
        $Contrato = Anti_Injection(utf8_encode($_POST['Contrato']));
        $Cargo = Anti_Injection(utf8_encode($_POST['cargo']));
        $Justificativa = Anti_Injection(utf8_encode($_POST['justificativa']));
        $sql = "UPDATE contratacao SET Cargo_idCargo = ? WHERE idContratacao = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $Cargo);
        $stmt->bindParam(2, $Contrato);
        $stmt->execute();
        $sql = "INSERT INTO historico (Colaborador_idColaborador, Historico, Data, Justificativa, Cargo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $Colaborador);
        $stmt->bindParam(2, $Historico);
        $stmt->bindParam(3, $Data);
        $stmt->bindParam(4, $Justificativa);
        $stmt->bindParam(5, $Cargo);
        $stmt->execute();
        echo "<script>alert('Atualizado com Sucesso!');window.location.href='".BASE."pesquisas/DetalheColaborador.php?cod=".$Colaborador."';</script>";
    endif;

?>