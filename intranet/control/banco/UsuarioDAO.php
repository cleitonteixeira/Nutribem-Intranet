<?php
    require_once("../arquivo/funcao/Outras.php");
    require_once("conexao.php");
    // Atribui uma conexão PDO
    /*echo "<pre>";
    var_dump($_POST);
    echo "</pre>";*/
    $conexao = conexao::getInstance();
    if(isset($_POST['Usuario']) && $_POST['Usuario']  ==  "Cadastro"):
        $Nome = anti_injection(utf8_encode($_POST['nome']));
        $login = anti_injection($_POST['login']);
        $senha = anti_injection(Cript($_POST['login'],$_POST['senha']));
        $Acesso = anti_injection($_POST['tipo']);
        $Unidade = $_POST['unidade'];
        $Chefia = $_POST['chefia'];
        if($_POST['chefia'] == "N"):
            $Email = "######";
        else:
            $Email = $_POST['email'];
        endif;
        $y = count($Unidade);
        $SQL = "INSERT INTO usuarios (Nome, Login, Senha, Acesso, Email, Chefia) VALUES (?,?,?,?,?,?)";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Nome);
        $stmt->bindParam(2, $login);
        $stmt->bindParam(3, $senha);
        $stmt->bindParam(4, $Acesso);
        $stmt->bindParam(5, $Email);
        $stmt->bindParam(6, $Chefia);
        $c = $stmt->execute();
        if($c):
            $User = $conexao->lastInsertId();
            $x = 0;
            foreach($Unidade as $a):
                $SQL = "INSERT INTO unidadeuser (Usuario_idUsuario, Unidade_idUnidade) VALUES (?, ?);";
                $stmt = $conexao->prepare($SQL);
                $stmt->bindParam(1, $User);
                $stmt->bindParam(2, $a);
                $cad = $stmt->execute();
                if($cad):
                    $x += 1;
                endif;
            endforeach;
            if($x == $y):
                echo "<script>alert('Usuário Cadastrado com Sucesso!');window.location.href='".BASE."cadastros/Usuario.php';</script>";
            endif;
        else:
            echo "<script>alert('Falha ao tentar executar o Cadastro!');window.location.href='".BASE."cadastros/Usuario.php';</script>";
        endif;
    else:
        header("Location: ".BASE);
    endif;
?>