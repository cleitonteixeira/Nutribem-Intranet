<?php
    $troca = array('linha',',');
    $Trocas = array(' ','-','.',')','(','/');
    require_once("conexao.php");
    $conexao = conexao::getInstance();
    require_once("../arquivo/funcao/Outras.php");
    // Atribui uma conexão PDO
    $x = 0;
    $Celular = "99999999999";
    $Telefone = "9999999999";
    $Email = "Nenhum Registro";
    if(isset($_POST['Unidade']) && $_POST['Unidade']  ==  "Cadastro"):
        if($_POST['celular'] != "" || $_POST['telefone'] != ""):
            if($_POST['celular'] != ""):
                $Celular = anti_injection($_POST['celular']);
                $Celular = str_replace($Trocas,"",$Celular);
            endif;
            if($_POST['telefone']!= ""):
                $Telefone = anti_injection($_POST['telefone']);
                $Telefone = str_replace($Trocas,"",$Telefone);
            endif;
        else:
            echo "<script>alert('Digite pelo menos um número para contato!');window.setTimeout('history.go(-1)', 0);</script>";
        endif;
        if($_POST['email'] != ""):
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)):
                echo "<script>alert('Formato de e-mail incorreto!');window.setTimeout('history.go(-1)', 0);</script>";
            else:
                $Email = anti_injection($_POST['email']);
            endif;
        endif; 
        $Nome = anti_injection(utf8_encode($_POST['nome']));
        $SQL = "INSERT INTO cadastro (Nome) VALUES (?)";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Nome);
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
                $SQL = "INSERT INTO contato (Telefone, Celular, email) VALUES (?,?,?)";
                $stmt = $conexao->prepare($SQL);
                $stmt->bindParam(1, $Telefone);
                $stmt->bindParam(2, $Celular);
                $stmt->bindParam(3, $Email);
                $Contato = $stmt->execute();
                if($Cadastro):
                    $IdContato = $conexao->lastInsertId();
                    $SQL = "INSERT INTO unidade (Empresa_idEmpresa, Contato_idContato,Cadastro_idCadastro, Endereco_idEndereco) VALUES (?,?,?,?);";
                    $stmt = $conexao->prepare($SQL);
                    $stmt->bindParam(1, $_POST['Empresa']);
                    $stmt->bindParam(2, $IdContato);
                    $stmt->bindParam(3, $IdCadastro);
                    $stmt->bindParam(4, $IdEndereco);
                    $Unidade = $stmt->execute();
                    if($Unidade):
                        $UnidadeID = $conexao->lastInsertId();  
                        echo "<script>alert('Unidade Cadastrada com Sucesso!');window.location.href='".BASE."extras/UsuariosUnidade.php?un=".$UnidadeID."';</script>";
                    else:
                        $SQL = "DELETE FROM endereco WHERE idEndereco = ?;";
                        $stmt = $conexao->prepare($SQL);
                        $stmt->bindParam(1, $IdEndereco);
                        $stmt->execute();
                        $SQL = "DELETE FROM contato WHERE idContato = ?;";
                        $stmt = $conexao->prepare($SQL);
                        $stmt->bindParam(1, $IdContato);
                        $stmt->execute();
                        $SQL = "DELETE FROM cadastro WHERE idCadastro = ?;";
                        $stmt = $conexao->prepare($SQL);
                        $stmt->bindParam(1, $IdCadastro);
                        $stmt->execute();
                        echo "<script>alert('Falha ao tentar executar o Cadastro!');window.location.href='".BASE."cadastros/Unidade.php';</script>";
                    endif;
                else:
                    $SQL = "DELETE FROM cadastro WHERE idCadastro = ?;";
                    $stmt = $conexao->prepare($SQL);
                    $stmt->bindParam(1, $IdCadastro);
                    $stmt->execute();
                    echo "<script>alert('Falha ao tentar executar o Cadastro!');window.location.href='".BASE."cadastros/Unidade.php';</script>";
                endif;
            else:
                $SQL = "DELETE FROM endereco WHERE idEndereco = ?;";
                $stmt = $conexao->prepare($SQL);
                $stmt->bindParam(1, $IdEndereco);
                $stmt->execute();
                $SQL = "DELETE FROM cadastro WHERE idCadastro = ?;";
                $stmt = $conexao->prepare($SQL);
                $stmt->bindParam(1, $IdCadastro);
                $stmt->execute();
                echo "<script>alert('Falha ao tentar executar o Cadastro!');window.location.href='".BASE."cadastros/Unidade.php';</script>";
            endif;
        else:
            echo "<script>alert('Falha ao tentar executar o Cadastro!');window.location.href='".BASE."cadastros/Unidade.php';</script>";
        endif;
   else:
        header("Location: ".BASE);
    endif;
?>