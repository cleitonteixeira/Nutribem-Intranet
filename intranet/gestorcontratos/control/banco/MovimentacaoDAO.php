<?php
	if (!isset($_SESSION)) session_start();
	require_once("../arquivo/funcao/Outras.php");
	require_once("../arquivo/header/Header.php");
	require_once("conexao.php");
	$Trocas = array(' ','-','.',')','(','/');
	$conexao = conexao::getInstance();
	if(isset($_POST['Cliente']) && $_POST['Cliente']  ==  "Movimentação"){
		try{
			if(empty($_FILES['doc']["name"])){
				$Texto 	= anti_injection(utf8_encode($_POST['descricao']));
				$Data  	= anti_injection(utf8_encode($_POST['data']));
				$Tipo  	= anti_injection(utf8_encode($_POST['tipo']));
				$Cod	= anti_injection(utf8_encode($_POST['CodContratante']));
				$DataCad= date("Y-m-d");
				$SQL = "INSERT INTO historial (Contratante_idContratante, Usuario_idUsuario, Tipo, DataVis, Descricao, DataCad) VALUES (?, ?, ?, ?, ?, ?)";
				$stmt = $conexao->prepare($SQL);
				$stmt->bindParam(1, $Cod);
				$stmt->bindParam(2, $_SESSION['idusuarios']);
				$stmt->bindParam(3, $Tipo);
				$stmt->bindParam(4, $Data);
				$stmt->bindParam(5, $Texto);
				$stmt->bindParam(6, $DataCad);
				$Cadastro = $stmt->execute();
				echo '
					<div class="alert alert-success">
						<p><strong>Sucesso!</strong> Sucesso ao fazer o lançamento!</p>
						<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/Movimentacao.php">aqui</a>.</p>
					</div>
					';
				header('Refresh: 5;URL='.BASE.'clientes/Movimentacao.php');exit;
			}else{
				$Texto 	= anti_injection(utf8_encode($_POST['descricao']));
				$Data  	= anti_injection(utf8_encode($_POST['data']));
				$Tipo  	= anti_injection(utf8_encode($_POST['tipo']));
				$Cod	= anti_injection(utf8_encode($_POST['CodContratante']));
                $DataCad= date("Y-m-d");
                $SQL = "INSERT INTO historial (Contratante_idContratante, Usuario_idUsuario, Tipo, DataVis, Descricao, DataCad) VALUES (?, ?, ?, ?, ?, ?);";
                $stmt = $conexao->prepare($SQL);
                $stmt->bindParam(1, $Cod);
                $stmt->bindParam(2, $_SESSION['idusuarios']);
                $stmt->bindParam(3, $Tipo);
                $stmt->bindParam(4, $Data);
                $stmt->bindParam(5, $Texto);
                $stmt->bindParam(6, $DataCad);
                $Cadastro = $stmt->execute();
                $idHistorial = $conexao->lastInsertId();
				$Arquivo   = $_FILES['doc'];
				// Pega extensão da imagem
				$Extensao = strrchr($Arquivo["name"], '.');
				// Gera um nome único para a imagem
				$Nome = md5(uniqid(time())) . $Extensao;
				//Nome da pasta das imagens
				$NomePasta = '../../clientes/docs/'.$Nome;
				if(move_uploaded_file($Arquivo["tmp_name"], $NomePasta)){
					$SQL = "INSERT INTO dochistorial (Historial_idHistorial, Documento) VALUES (?, ?);";
                    $stmt = $conexao->prepare($SQL);
                    $stmt->bindParam(1, $idHistorial);
                    $stmt->bindParam(2, $Nome);
                    $stmt->execute();
					echo '
					<div class="alert alert-success">
						<p><strong>Sucesso!</strong> Sucesso ao fazer o lançamento!</p>
						<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/Movimentacao.php">aqui</a>.</p>
					</div>
					';
					header('Refresh: 5;URL='.BASE.'clientes/Movimentacao.php');exit;
				}else{
					echo '
						<div class="alert alert-danger">
							<p><strong>Falha!</strong> Falha ao mover arquivo...</p>
							<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/Movimentacao.php">aqui</a>.</p>
						</div>
						';
					header('Refresh: 5;URL='.BASE.'clientes/Movimentacao.php');exit;
				}
			}
		}catch(PDOException $erro_cad){
			echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao tentar realizar Cadastro...</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$erro_cad.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/Movimentacao.php">aqui</a>.</p>
			</div>
			';
			header('Refresh: 5;URL='.BASE.'clientes/Movimentacao.php');exit;
		}
	}else{
		header("Location: ".BASE);
	}
?>