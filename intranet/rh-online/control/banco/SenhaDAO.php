<?php
    require_once("../arquivo/funcao/Outras.php");
    require_once("conexao.php");
	if (!isset($_SESSION)) session_start();
    $conexao = conexao::getInstance();
	if(isset($_POST['Senha']) && $_POST['Senha'] === "AttSenha"):
		$senha = anti_injection(Cript($_SESSION['Login'],$_POST['nSenha']));
        $sql = "UPDATE usuarios SET Senha = ? , FirstAccess = 'N'  WHERE idusuarios = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $senha);
        $stmt->bindParam(2, $_SESSION['idusuarios']);
        $att = $stmt->execute();
        if($att):
			echo "<script>alert('Senha atualizada com sucesso!');window.location.href='".BASE."';</script>";
        else:
            echo "<script>alert('Falha ao atualizar!');window.location.href='".BASE."control/acesso/Sair.php';</script>";
        endif;
	elseif(isset($_POST['Senha']) && $_POST['Senha'] === "newSenha"):
		$senha = anti_injection(Cript($_SESSION['Login'],$_POST['nSenha']));
        $sql = "UPDATE usuarios SET Senha = ? WHERE idusuarios = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $senha);
        $stmt->bindParam(2, $_SESSION['idusuarios']);
        $att = $stmt->execute();
        if($att):
			echo "<script>alert('Senha modificada com sucesso!');window.location.href='".BASE."';</script>";
        else:
            echo "<script>alert('Falha ao atualizar!');window.location.href='".BASE."control/acesso/Sair.php';</script>";
        endif;
    else:
        header("Location: ".BASE."control/acesso/Sair.php");
    endif;
?>