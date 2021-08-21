<?php
if (!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$Trocas = array(' ','-','.',')','(','/');
$troca = array('linha', 'line',',');
$conexao = conexao::getInstance();
try{
    $conexao->beginTransaction();
	$sql = "SELECT c.idContrato, c.nContrato, c.Proposta_idProposta FROM contrato c INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cc ON cc.idCadastro = ct.Cadastro_idCadastro WHERE c.Unidade_idUnidade = ? AND c.Finalizado = 'N';";
	$stm = $conexao->prepare($sql);
	$stm->bindParam(1, $_POST['Unidade']);
	$stm->execute();
	$rs = $stm->fetchAll(PDO::FETCH_OBJ);
	$Unidade = $_POST["Unidade"];
	$Usuario = $_SESSION['idusuarios'];
	$data = date("Y-m-d");
	foreach($rs as $r){
        $sql = "SELECT * FROM itensproposta WHERE Proposta_idProposta = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $r->Proposta_idProposta);
        $stm->execute();
        $row = $stm->fetchAll(PDO::FETCH_OBJ);
        foreach($row as $x){
            if(isset($_POST[$r->idContrato][$x->Servico]['Quant']) && $_POST[$r->idContrato][$x->Servico]['Quant'] >= 0){
                $sqj = 'SELECT * FROM lancamento WHERE Servico = ? AND dLancamento = ? AND Contrato_idContrato = ? AND Unidade_idUnidade = ?';
                $stmj = $conexao->prepare($sqj);
                $stmj->bindParam(1, $x->Servico);
                $stmj->bindParam(2, $_POST['dLancamento']);
                $stmj->bindParam(3, $r->idContrato);
                $stmj->bindParam(4, $Unidade);
                $stmj->execute();
                if($stmj->rowCount() < 1){
                    $sql = "INSERT INTO lancamento (Unidade_idUnidade, Usuario_idUsuario, Contrato_idContrato, Servico, Quantidade, ValorUni, dLancamento, dCadastro) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindParam(1, $Unidade);
                    $stmt->bindParam(2, $Usuario);
                    $stmt->bindParam(3, $r->idContrato);
                    $stmt->bindParam(4, $x->Servico);
                    $stmt->bindParam(5, $_POST[$r->idContrato][$x->Servico]['Quant']);
                    $stmt->bindParam(6, $x->ValorUni);
                    $stmt->bindParam(7, $_POST['dLancamento']);
                    $stmt->bindParam(8, $data);
                    $stmt->execute();
                }
            }
        }
	}
    if(isset($_POST['cdl']) && $_POST['cdl'] != ''){
        $sql2 = "UPDATE controledata SET Ativo = 'N', DataLancamento = ? WHERE CodControle = ?;";
        $stmt = $conexao->prepare($sql2);
        $stmt->bindParam(1, $data);
        $stmt->bindParam(2, $_POST['cdl']);
        $stmt->execute();
    }
    $conexao->commit();
	echo '
		<div class="alert alert-success">
			<p><strong>Sucesso!</strong> Sucesso ao fazer o Lançamento!</p>
			<p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'lancamento/Diario.php">aqui</a>.</p>
		</div>
		';
	header('Refresh: 5;URL='.BASE.'lancamento/Diario.php');exit;
}catch(PDOException $e){
    $conexao->rollBack();
	echo '
		<div class="alert alert-danger">
			<p><strong>Falha!</strong> Falha ao tentar realizar Lançamento...</p>
			<p><strong>O sistema apresentou o seguinte erro:</strong>'.$e.'</p>
			<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'lancamento/Diario.php">aqui</a>.</p>
		</div>
		';
	header('Refresh: 10;URL='.BASE.'lancamento/Diario.php');exit;
}
?>