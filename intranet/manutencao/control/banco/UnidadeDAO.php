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
            header('Refresh: 5;URL='.BASE.'unidade/Cadastrar.php');exit;
        }catch(PDOException $e){
            
            $conexao->rollback();
            echo '
                <div class="alert alert-danger">
                    <p><strong>Falha!</strong> Falha no Cadastro!</p>
                    <p>'.$e->getMessage().'</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'unidade/Cadastrar.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'unidade/Cadastrar.php');exit;
        }
    }elseif(isset($_POST['SalvaAcessoUnidade'])){
        try{
            $conexao->beginTransaction();
            $Unidade = $_POST['unidade'];
            $SQL = "SELECT Usuario_idUsuario FROM unidademtuser WHERE Unidade_idUnidade = ?;";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Unidade);
            $stmt->execute();
            $usUnidade = array();
            while($r = $stmt->fetch(PDO::FETCH_OBJ)) {
                array_push($usUnidade, $r->Usuario_idUsuario);
            }
            foreach ($_POST['user'] as $User) {
                if(!in_array($User, $usUnidade)){
                    $SQL = "INSERT INTO unidademtuser (Usuario_idUsuario, Unidade_idUnidade) VALUES ( ?, ? );";
                    $stmt = $conexao->prepare($SQL);
                    $stmt->bindParam(1, $User);
                    $stmt->bindParam(2, $Unidade);
                    $stmt->execute();
                }
            }
            foreach ($usUnidade as $User) {
                if(!in_array($User, $_POST['user'])){
                    $SQL = "DELETE FROM unidademtuser WHERE Unidade_idUnidade = ? AND Usuario_idUsuario = ?;";
                    $stmt = $conexao->prepare($SQL);
                    $stmt->bindParam(1, $Unidade);
                    $stmt->bindParam(2, $User);
                    $stmt->execute();
                }
            }

            $conexao->commit();
            echo '
                <div class="alert alert-success">
                    <p><strong>Sucesso!</strong> Acesso a unidade atualizado com sucesso!</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'unidade/AcessoUnidade.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'unidade/AcessoUnidade.php');exit;
        }catch(PDOException $e){
            $conexao->rollback();
            echo '
                <div class="alert alert-danger">
                    <p><strong>Falha!</strong> Falha na Atualização Acesso a Unidade!</p>
                    <p>'.$e->getMessage().'</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'unidade/AcessoUnidade.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'unidade/AcessoUnidade.php');exit;
        }
    }else{
        header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/manutencao/");
    }
}
?>