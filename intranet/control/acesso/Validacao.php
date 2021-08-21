<?php
    require_once("../arquivo/funcao/Outras.php");
    require_once("../banco/conexao.php");
	$conexao = conexao::getInstance();
    // Verifica se houve POST e se o usuário ou a senha é(são) vazio(s)
	if (!empty($_POST) AND (empty($_POST['login']) OR empty($_POST['senha']))) {
		header("Location: ".BASE); exit;
	}
    
	$login = anti_injection($_POST['login']);
	$senha = anti_injection(Cript($_POST['login'],$_POST['senha']));
	// Validação do usuário/senha digitados
	$sql = "SELECT * FROM usuarios WHERE Login = ? AND Senha= ? AND Ativo = 0 LIMIT 1;";
    //echo "SELECT * FROM user WHERE Login = '".$login."' AND Senha = '".$senha."' AND Ativo = 0 LIMIT 1;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $login);
    $stm->bindParam(2, $senha);
    $stm->execute();
    $row = $stm->fetch(PDO::FETCH_ASSOC);
	if ($stm->rowCount() != 1) {
		 //Mensagem de erro quando os dados são inválidos e/ou o usuário não foi encontrado
		echo "<script>alert('Login ou Senha Incorreto!');window.setTimeout('history.go(-1)', 0);</script>";
	} else {
        // Se a sessão não existir, inicia uma
        if (!isset($_SESSION)) session_start();
        $_SESSION = $row;
		unset($_SESSION['Senha']);
		// Redireciona o visitante
		
		if($_SESSION['FirstAccess'] === "N"):
			unset($_SESSION['FirstAccess']);
        	header("Location: ".BASE); exit;
		elseif($_SESSION['FirstAccess'] === "S"):
			header("Location: ".BASE."control/acesso/Senha.php"); exit;
		else:
			header("Location: ".BASE."control/acesso/Sair.php"); exit;
		endif;
    }
?>