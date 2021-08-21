<?php 
    require_once("../arquivo/funcao/Outras.php");
    require_once("conexao.php");
    $conexao = conexao::getInstance();
    if(isset($_POST['Cargo']) && $_POST['Cargo']  ==  "Cadastro"):
        $Nome = anti_injection(utf8_encode($_POST['cargo']));
        $CBO = anti_injection(utf8_encode($_POST['cbo']));
        $CodCargo = anti_injection(utf8_encode($_POST['codigo']));
        $Unidade = anti_injection(utf8_encode($_POST['unidade']));
        $Funcao = anti_injection(utf8_encode($_POST['funcao']));
        $Salario = str_replace(".","",$_POST['salario']);
        $Salario = str_replace(",",".",$Salario);
        $SQL = "INSERT INTO cargo (Cargo, CBO, CodCargo, Salario, Unidade_idUnidade, Funcao) VALUES (?,?,?,?,?,?)";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Nome);
        $stmt->bindParam(2, $CBO);
        $stmt->bindParam(3, $CodCargo);
        $stmt->bindParam(4, $Salario);
        $stmt->bindParam(5, $Unidade);
        $stmt->bindParam(6, $Funcao);
        $Cargo = $stmt->execute();
        if($Cargo):
            echo "<script>alert('Cargo Cadastradado com Sucesso!');window.location.href='".BASE."cadastros/Cargo.php';</script>";
        else:
            echo "<script>alert('Falha ao cadastrar!');window.location.href='".BASE."cadastros/Cargo.php';</script>";
        endif;
    elseif(isset($_POST['Cargo']) && $_POST['Cargo']  ==  "Atualizar"):
        $Funcao = utf8_encode($_POST['funcao']);
        $IDCargo = utf8_encode($_POST['CargoID']);
        $Salario = str_replace(".","",$_POST['salario']);
        $Salario = str_replace(",",".",$Salario);
        $sql = "UPDATE cargo SET Funcao = ? , Salario = ? WHERE idCargo = ?";
        $conexao = conexao::getInstance();
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $Funcao);
        $stmt->bindParam(2, $Salario);
        $stmt->bindParam(3, $IDCargo);
        $AttCargo = $stmt->execute();
        if($AttCargo):
            echo "<script>alert('Atualizado com Sucesso!');window.location.href='".BASE."cadastros/AtualizaCargo.php';</script>";
        else:
            echo "<script>alert('Falha ao Atualizar!');window.location.href='".BASE."cadastros/AtualizaCargo.php';</script>";
        endif;
    else:
        header("Location: ".BASE);
    endif;
?>