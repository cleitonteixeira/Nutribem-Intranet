<?php
if (!isset($_SESSION)) session_start();
$troca = array('linha',',');
$Trocas = array(' ','-','.',')','(','/');
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
// Atribui uma conexão PDO

$conexao = conexao::getInstance();
$Celular = "99999999999";
$Telefone = "9999999999";
$Email = "Nenhum Registro";
if(isset($_POST['Equipamento']) && $_POST['Equipamento']  ==  "Cadastrar"){
    try{
        $conexao->beginTransaction();
        
        $Nome = anti_injection(utf8_encode($_POST['nome']));
        $SQL = "INSERT INTO cadastro (Nome) VALUES (?)";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $Nome);
        $stmt->execute();
        $IdCadastro = $conexao->lastInsertId();

        $fabricante = anti_injection(utf8_encode($_POST['fabricante']));
        $modelo = anti_injection(utf8_encode($_POST['modelo']));
        $alimentacao = anti_injection(utf8_encode($_POST['alimentacao']));
        $dFabrica = anti_injection(utf8_encode($_POST['dFabrica']));
        $categoria = anti_injection(utf8_encode($_POST['categoria']));
        $unidade = anti_injection(utf8_encode($_POST['unidade']));
        $dCadastro = date("Y-m-d");

        $Ano = date("Y");
        $categoriaC = str_pad($categoria, 3, 0, STR_PAD_LEFT);
        $Quant = 1;
        $Codigo = "EQ.".$categoriaC.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
        $sql = 'SELECT Codigo FROM equipamento WHERE Categoria_idCategoria = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $categoria);
        $stm->execute();
        $codigoL = array();
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
            array_push($codigoL, $row->Codigo);
        }
        while(in_array($Codigo, $codigoL)){
            $Quant += 1;
            $Codigo = "EQ.".$categoriaC.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
        }
        
        $SQL = "INSERT INTO equipamento(Unidade_idUnidade,Cadastro_idCadastro,Categoria_idCategoria,Usuario_idUsuario,Fabricante,Modelo,Alimentacao,dFabrica,dCadastro, Codigo) VALUES (?,?,?,?,?,?,?,?,?,?);";
        $stmt = $conexao->prepare($SQL);
        $stmt->bindParam(1, $unidade);
        $stmt->bindParam(2, $IdCadastro);
        $stmt->bindParam(3, $categoria);
        $stmt->bindParam(4, $_SESSION['idusuarios']);
        $stmt->bindParam(5, $fabricante);
        $stmt->bindParam(6, $modelo);
        $stmt->bindParam(7, $alimentacao);
        $stmt->bindParam(8, $dFabrica);
        $stmt->bindParam(9, $dCadastro);
        $stmt->bindParam(10, $Codigo);
        $stmt->execute();
        $conexao->commit();
		echo '
			<div class="alert alert-success">
				<p><strong>Sucesso!</strong> Sucesso ao Cadastrar Equipamento!</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'equipamento/Cadastrar.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'equipamento/Cadastrar.php');exit;
	}catch(PDOException $er){
        $conexao->rollBack();
		echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao Cadastrar Equipamento!</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$er->getMessage().'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'equipamento/Cadastrar.php">aqui</a>.</p>
			</div>
			';
		header('Refresh: 5;URL='.BASE.'equipamento/Cadastrar.php');exit;
	}

}else{
    header("Location: ".BASE);
}
?>