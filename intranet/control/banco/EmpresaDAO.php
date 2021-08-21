<?php
    $troca = array('linha',',');
    $Trocas = array(' ','-','.',')','(','/');
    require_once("../arquivo/funcao/Outras.php");
    require_once("conexao.php");
    // Atribui uma conexão PDO
    $x = 0;
    $conexao = conexao::getInstance();
    if(isset($_POST['Empresa']) && $_POST['Empresa']  ==  "Cadastro"):
        $Nome = anti_injection(utf8_encode($_POST['razao']));
        $CPF = str_replace($Trocas,"",$_POST['cnpj']);
        $SQL = "INSERT INTO cadastro (Nome, CNPJ) VALUES (?,?)";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Nome);
        $stmt->bindParam(2, $CPF);
        $Cadastro = $stmt->execute();
        if($Cadastro):
            $IdCadastro = $conexao->lastInsertId();
            $Endereco = anti_injection(utf8_encode($_POST['logradouro']));
            $Bairro = anti_injection(utf8_encode($_POST['bairro']));
            $CEP = str_replace($Trocas,"",$_POST['cep']);
            $Cidade = anti_injection(utf8_encode($_POST['cidade']));
            $Numero = anti_injection(utf8_encode($_POST['numero']));
            $UF = anti_injection(utf8_encode($_POST['uf']));
            $SQL = "INSERT INTO endereco (Endereco, Bairro, CEP, Cidade, Numero, UF) VALUES (?,?,?,?,?,?)";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Endereco);
            $stmt->bindParam(2, $Bairro);
            $stmt->bindParam(3, $CEP);
            $stmt->bindParam(4, $Cidade);
            $stmt->bindParam(5, $Numero);
            $stmt->bindParam(6, $UF);
            $Endereco = $stmt->execute();
            if($Endereco):
                $IdEndereco = $conexao->lastInsertId();
                $SQL = "INSERT INTO empresa (Cadastro_idCadastro, Endereco_idEndereco) VALUES (?,?);";
                $stmt = $conexao->prepare($SQL);
                $stmt->bindParam(1, $IdCadastro);
                $stmt->bindParam(2,$IdEndereco);
                $Empresa = $stmt->execute();
                if($Empresa):
                    echo "<script>alert('Empresa Cadastrada com Sucesso!');window.location.href='".BASE."cadastros/Empresa.php';</script>";
                else:
                    $SQL = "DELETE FROM endereco WHERE idEndereco = ?;";
                    $stmt = $conexao->prepare($SQL);
                    $stmt->bindParam(1, $IdEndereco);
                    $stmt->execute();
                    $SQL = "DELETE FROM cadastro WHERE idCadastro = ?;";
                    $stmt = $conexao->prepare($SQL);
                    $stmt->bindParam(1, $IdCadastro);
                    $stmt->execute();
                    echo "<script>alert('Falha ao tentar executar o Cadastro!');window.location.href='".BASE."cadastros/Empresa.php';</script>";
                endif;
            else:
                $SQL = "DELETE FROM cadastro WHERE idCadastro = ?;";
                $stmt = $conexao->prepare($SQL);
                $stmt->bindParam(1, $IdCadastro);
                $stmt->execute();
                echo "<script>alert('Falha ao tentar executar o Cadastro!');window.location.href='".BASE."cadastros/Empresa.php';</script>";
            endif;
        else:
            echo "<script>alert('Falha ao tentar executar o Cadastro!');window.location.href='".BASE."cadastros/Empresa.php';</script>";
        endif;
    else:
        header("Location: ".BASE);
    endif;
?>