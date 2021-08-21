<?php
/*echo "<pre>";
var_dump($_POST);
echo "</pre>";*/
require_once("conexao.php");
$conexao = conexao::getInstance();
require_once("../arquivo/funcao/Outras.php");
if(isset($_POST['Controle']) && $_POST['Controle']  ==  "Atualizar"):
    $Unidade = $_POST['unidade'];
    $Usuario = $_POST['usuario'];
    $y = count($Usuario);
    $x = 0;
    foreach($Usuario as $a):
        $SQL = "INSERT INTO unidadeuser (Usuario_idUsuario, Unidade_idUnidade) VALUES (?, ?);";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $a);
        $stmt->bindParam(2, $Unidade);
        $cad = $stmt->execute();
        if($cad):
            $x += 1;
        endif;
    endforeach;
    if($x == $y):
        echo "<script>alert('Atualizado com Sucesso!');window.location.href='".BASE."cadastros/Unidade.php';</script>";
        //echo "Deu certo!";
    endif;
elseif(isset($_POST['Controle']) && $_POST['Controle']  ==  "Remover"):
    
endif;
?>