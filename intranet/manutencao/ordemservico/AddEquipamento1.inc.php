<?php
if (!isset($_SESSION)) session_start();
require_once("../control/banco/conexao.php");
$conexao = conexao::getInstance();
require_once("../control/arquivo/funcao/Outras.php");
function AddItemSessao($id, $defeito){
	//echo strlen($defeito);
	$controle = true;
	$Dados = array();
	if (!isset($_SESSION['equipamento'])){
		$_SESSION['equipamento'] = array();
	}
	if (!isset($_SESSION['codigos'])){
		$_SESSION['codigos'] = array();
	}
	while($controle){
		if(!empty($id)){
			$id = anti_injection($id);
		}else{
			$Itens = [
			        "Retorno" => "Erro 1"
			];
			array_push($Dados, $Itens);
			break;
		}
		if(!empty($defeito)){
			if(strlen($defeito) >= 25){
				$defeito = anti_injection($defeito);
			}else{
				$Itens = [
					"Retorno" => "Erro 2"
				];
				array_push($Dados, $Itens);
			break;
			}
		}else{
			$Itens = [
				"Retorno" => "Erro 3"
			];
			array_push($Dados, $Itens);
			break;
		}
		$controle = false;
		unset($Itens);
	}
	if(!empty($Dados)){
		echo json_encode($Dados);
		unset($Dados);
		unset($Itens);
	}else{
		$Itens = [
			"id" => $id,
			"defeito" => $defeito
		];
		array_push($_SESSION['equipamento'], $Itens);
		$Itens = [
			"Retorno" => "Sucesso"
		];
		array_push( $_SESSION['codigos'], $id );
		$_SESSION['cont'] = 0;
		array_push($Dados, $Itens);
		echo json_encode($Dados);
		unset($Dados);
		unset($Itens);
	}
}
function LoadItem(){
	$conexao = conexao::getInstance();
	$codigos = $_SESSION['codigos'];
    $sql = "SELECT c.Nome AS Equipamento, e.*, ce.* FROM equipamento e INNER JOIN cadastro c ON c.idCadastro = e.Cadastro_idCadastro INNER JOIN categoriaequipamento ce ON ce.idCategoriaEquipamento = e.Categoria_idCategoria;";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    ob_start();
    while($r = $stmt->fetch(PDO::FETCH_OBJ)){
    	if(!in_array($r->Codigo, $codigos)){
    ?>
      <tr>
        <td data-id="<?=utf8_decode($r->Codigo);?>"><?=utf8_decode($r->Codigo);?></td>
        <td data-equipamento="<?=utf8_decode($r->Equipamento);?>"><?=utf8_decode($r->Equipamento);?></td>
        <td><?=utf8_decode($r->Modelo);?></td>
        <td><?=utf8_decode($r->Fabricante);?></td>
        <td><?=utf8_decode($r->Nome);?></td>
        <td class="text-center"><span class="btn-primary btn-es" title="Selecionar Equipamento"><i class="fas fa-forward"></i></span></td>
      </tr>
    <?php
		}
	}
    $html = ob_get_clean();
    $html = ["Dados1" => $html];
	$dados = array();
	array_push($dados, $html);
	//unset($_SESSION['equipamento']);
	echo json_encode($dados);
}
function ShowItem(){
	$conexao = conexao::getInstance();
	if(isset($_SESSION['equipamento']) && count($_SESSION['equipamento'])>0){
		ob_start();
		if (!isset($_SESSION['cont'])){
			$_SESSION['cont'] = 0;
		}
		foreach ($_SESSION['equipamento'] as $x) {
			$sql = "SELECT * FROM equipamento INNER JOIN cadastro ON idCadastro = Cadastro_idCadastro WHERE Codigo = ? LIMIT 1;";
			$stmt = $conexao->prepare($sql);
			$stmt->bindParam(1, $x['id']);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_OBJ);
		?>
		<div class="col-xs-6 col-lg-6 col-md-6 box-equipamento text-left">
			 <p><strong>ID: </strong> <?=$x['id']?></p>
			 <p><strong>Equipamento: </strong> <?=$result->Nome;?></p>
			 <p><strong>Defeito: </strong> <?=$x['defeito']?></p>
			 <p><input data-text="Adicionar Fotos" type="file" multiple="multiple" name="img<?=$_SESSION['cont'];?>[]" class="form-control filestyle" data-input="false"></p>
			 <p class="text-right"><span class="btn btn-danger text-right"><i class="fas fa-trash-alt"></i></span></p>
		</div>
		<?php
			$_SESSION['cont'] += 1;
			if($_SESSION['cont']%2 == 0){
		?>
		<div class="col-md-12 col-lg-12 col-xs-12"></div>
		<?php
			}
		}
	    $html = ob_get_clean();
	    $html = ["Dados" => $html];
	    $dados = array();
	    array_push($dados, $html);
    	echo json_encode($dados);
	}else{
		$html = ["Dados" => "Erro"];
	    $dados = array();
	    array_push($dados, $html);
		echo json_encode($dados);
	}
}
if(isset($_POST['addItemSessao']) && $_POST['addItemSessao'] == 'itemSessao'){
	$x = anti_injection($_POST['id']);
	$y = anti_injection($_POST['defeito']);
	AddItemSessao($x, $y);
}else if(isset($_POST['ShowItem']) && $_POST['ShowItem'] == 'sItem'){
	ShowItem();
}else if(isset($_POST['LoadItem']) && $_POST['LoadItem'] == 'lItem'){
	LoadItem();
}

?>
