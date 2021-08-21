<?php
    require_once("conexao.php");
    $conexao = conexao::getInstance();
    require_once("../arquivo/funcao/Outras.php");
	if(isset($_POST['campo']) && $_POST['campo']  != 0):
        $data = Anti_Injection(utf8_encode($_POST['data']));
        $cod = Anti_Injection(utf8_encode($_POST['campo']));
        $sql = "UPDATE contratacao SET dAso = ? WHERE idContratacao = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $data);
        $stmt->bindParam(2, $cod);
        $att = $stmt->execute();
        if($att):
			if($_POST['pag'] == "seguranca"):
				echo "<script>alert('Atualizado com Sucesso!');window.location.href='".BASE."seguranca/Asos.php';</script>";
			elseif($_POST['pag'] == "home"):
				echo "<script>alert('Atualizado com Sucesso!');window.location.href='".BASE."seguranca/Asos.php';</script>";
			endif;
        else:
            echo "<script>alert('Falha ao atualizar!');window.location.href='".BASE."';</script>";
        endif;
    else:
        header("Location: ".BASE);
    endif;
?>