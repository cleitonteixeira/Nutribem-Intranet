<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    $Trocas = array(' ','-','.',')','(','/');
    require_once("conexao.php");
    require_once("../arquivo/funcao/Outras.php");
    require_once("../arquivo/header/Header.php");
    // Atribui uma conexão PDO
    $conexao = conexao::getInstance();

    if(isset($_POST['Unidade']) && $_POST['Unidade']  ==  "Cadastrar"){
        
        try{
            $conexao->beginTransaction();
            $Unidade        = anti_injection(utf8_encode($_POST['nome']));
            $Responsavel    = anti_injection(utf8_encode($_POST['responsavel']));
            $Endereco       = anti_injection(utf8_encode($_POST['endereco']));
            $Numero         = anti_injection(utf8_encode($_POST['numero']));
            $Bairro         = anti_injection(utf8_encode($_POST['bairro']));
            $Cidade         = anti_injection(utf8_encode($_POST['cidade']));
            $UF             = anti_injection(utf8_encode($_POST['uf']));
            $CEP            = anti_injection(utf8_encode(str_replace($Trocas,"",$_POST['cep'])));
             
            $sql = "INSERT INTO cadastro (Nome)VALUES(?);";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $Unidade);
            $stmt->execute();
            
            $idCadastro = $conexao->lastInsertId();
            
            $SQL = "INSERT INTO endereco (Endereco, Bairro, CEP, Cidade, Numero, UF) VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Endereco);
            $stmt->bindParam(2, $Bairro);
            $stmt->bindParam(3, $CEP);
            $stmt->bindParam(4, $Cidade);
            $stmt->bindParam(5, $Numero);
            $stmt->bindParam(6, $UF);
            $stmt->execute();

            $idEndereco = $conexao->lastInsertId();
        
            $SQL = "INSERT INTO unidademt (Responsavel_idResponsavel, Endereco_idEndereco, Cadastro_idCadastro) VALUES (?, ?, ?);";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Responsavel);
            $stmt->bindParam(2, $idEndereco);
            $stmt->bindParam(3, $idCadastro);
            $stmt->execute();

            $conexao->commit();
            echo '
                <div class="alert alert-success">
                    <p><strong>Sucesso!</strong> Cadastrado com sucesso!</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'unidade/Cadastrar.php">aqui</a>.</p>
                </div>
                ';
           // header('Refresh: 5;URL='.BASE.'unidade/Cadastrar.php');exit;
        }catch(PDOException $e){
            
            $conexao->rollback();
            echo '
                <div class="alert alert-danger">
                    <p><strong>Falha!</strong> Falha no Cadastro!</p>
                    <p>'.$e->getMessage().'</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'unidade/Cadastrar.php">aqui</a>.</p>
                </div>
                ';
           // header('Refresh: 5;URL='.BASE.'unidade/Cadastrar.php');exit;
        }
    }else{
        header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/manutencao/");
    }
}
?>