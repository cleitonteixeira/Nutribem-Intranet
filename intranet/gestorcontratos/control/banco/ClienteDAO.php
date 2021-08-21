<?php
    require_once("../arquivo/funcao/Outras.php");
    require_once("../arquivo/funcao/Dados.php");
    require_once("../arquivo/header/Header.php");
    require_once("conexao.php");
 	$Trocas = array(' ','-','.',')','(','/');
    $conexao = conexao::getInstance();
    if(isset($_POST['Cliente']) && $_POST['Cliente']  ==  "Cadastro"){
        try{
            $conexao->beginTransaction();
			$Nome = anti_injection(utf8_encode($_POST['razao']));
			$CNPJ = str_replace($Trocas,"",$_POST['cnpj']);
			$SQL = "INSERT INTO cadastro (Nome, CNPJ) VALUES (?,?)";
			$stmt = $conexao->prepare($SQL);
			$stmt->bindParam(1, $Nome);
			$stmt->bindParam(2, $CNPJ);
			$Cadastro = $stmt->execute();
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
			$IdEndereco = $conexao->lastInsertId();
			$Endereco = anti_injection(utf8_encode($_POST['lCobranca']));
            $Bairro = anti_injection(utf8_encode($_POST['bCobranca']));
            $CEP = str_replace($Trocas,"",$_POST['ceCobranca']);
            $Cidade = anti_injection(utf8_encode($_POST['cCobranca']));
            $Numero = anti_injection(utf8_encode($_POST['nCobranca']));
            if(isset($_POST['uCobranca'])){
				$UF = anti_injection(utf8_encode($_POST['uCobranca']));
			}else{
				$UF = anti_injection(utf8_encode($_POST['uCobrancaH']));
			}
            $SQL = "INSERT INTO ecobranca (Endereco, Bairro, CEP, Cidade, Numero, UF) VALUES (?,?,?,?,?,?)";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Endereco);
            $stmt->bindParam(2, $Bairro);
            $stmt->bindParam(3, $CEP);
            $stmt->bindParam(4, $Cidade);
            $stmt->bindParam(5, $Numero);
            $stmt->bindParam(6, $UF);
            $Endereco = $stmt->execute();
			$IdEnderecoC = $conexao->lastInsertId();
			if(!$_POST['ie'] == ""){
				$IE = utf8_encode($_POST['ie']);
			}else{
				$IE = "0";
			}
			$SQL = "INSERT INTO contratante (Cadastro_idCadastro, Endereco_idEndereco, Cobranca_idCobranca, IE) VALUES (?,?,?,?);";
			$stmt = $conexao->prepare($SQL);
			$stmt->bindParam(1, $IdCadastro);
			$stmt->bindParam(2, $IdEndereco);
			$stmt->bindParam(3, $IdEnderecoC);
			$stmt->bindParam(4, $IE);
			$Empresa = $stmt->execute();
			$IdEmpresa = $conexao->lastInsertId();
			$Telefone = anti_injection($_POST['tFinanceiro']);
			$Telefone = str_replace($Trocas,"",$Telefone);
			$Email = anti_injection($_POST['eFinanceiro']);
			$Responsavel = utf8_encode(anti_injection($_POST['rFinanceiro']));
			$tipo = "Financeiro";
            $SQL = "INSERT INTO ccontratante (Contratante_idContratante, Responsavel, Email, Telefone, Tipo) VALUES (?,?,?,?,?);";
			$stmt = $conexao->prepare($SQL);
			$stmt->bindParam(1, $IdEmpresa);
			$stmt->bindParam(2, $Responsavel);
			$stmt->bindParam(3, $Email);
			$stmt->bindParam(4, $Telefone);
			$stmt->bindParam(5, $tipo);
			$stmt->execute();
			$Telefone = anti_injection($_POST['tComercial']);
			$Telefone = str_replace($Trocas,"",$Telefone);
			$Email = anti_injection($_POST['eComercial']);
			$Responsavel = utf8_encode(anti_injection($_POST['rComercial']));
			$tipo = "Comercial";
			$SQL = "INSERT INTO ccontratante (Contratante_idContratante, Responsavel, Email, Telefone, Tipo) VALUES (?,?,?,?,?);";
			$stmt = $conexao->prepare($SQL);
			$stmt->bindParam(1, $IdEmpresa);
			$stmt->bindParam(2, $Responsavel);
			$stmt->bindParam(3, $Email);
			$stmt->bindParam(4, $Telefone);
			$stmt->bindParam(5, $tipo);
			$stmt->execute();
            $conexao->commit();
			echo '
			<div class="alert alert-success">
			  	<p><strong>Sucesso!</strong> Cadastro Concluído com Sucesso...</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'cadastros/Clientes.php">aqui</a>.</p>
			</div>
			';
			header('Refresh: 5;URL='.BASE.'cadastros/Clientes.php');exit;
		}catch(PDOException $erro_cad){
            $conexao->rollBack();
			echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao tentar realizar Cadastro...</p>
			  	<p><strong>'.$erro_cad.' </strong></p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'cadastros/Clientes.php">aqui</a>.</p>
			</div>
			';
			header('Refresh: 5;URL='.BASE.'cadastros/Clientes.php');exit;
		}
    }else{
        header("Location: ".BASE);exit;
    }
?>