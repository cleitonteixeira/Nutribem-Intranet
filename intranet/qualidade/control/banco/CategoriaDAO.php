<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    $troca = array('linha',',');
    $Trocas = array(' ','-','.',')','(');
    require_once("conexao.php");
    require_once("../arquivo/funcao/Outras.php");
    require_once("../arquivo/header/Header.php");
    // Atribui uma conexão PDO
    $conexao = conexao::getInstance();
    if(isset($_POST['Categoria']) && $_POST['Categoria']  ==  "Cadastrar"){
        try{
            $conexao->beginTransaction();
            
            $Categoria  = anti_injection(utf8_encode($_POST['categoria']));
            $Usuario    = anti_injection(utf8_encode($_SESSION['idusuarios']));
             
            $sql = "INSERT INTO categoria ( Usuario_idUsuario, Nome )VALUES( ?,? );";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $Usuario);
            $stmt->bindParam(2, $Categoria);
            $stmt->execute();
            
            $conexao->commit();
            echo '
                <div class="alert alert-success">
                    <p><strong>Sucesso!</strong> Cadastrado com sucesso!</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'cadastros/Categoria.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'cadastros/Categoria.php');exit;
        }catch(PDOException $e){
            echo '
                <div class="alert alert-danger">
                    <p><strong>Falha!</strong> Falha no Cadastro!</p>
                    <p>'.$e->getMessage().'</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'cadastros/Categoria.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'cadastros/Categoria.php');exit;
        }
    }else{
        header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/qualidade/");
    }
}
?>