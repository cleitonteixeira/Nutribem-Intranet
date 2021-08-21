<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
			<div class="conteudo"></div>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<h1 class="text-center">FORMULÁRIO DE LIBERAÇÃO DE DATAS</h1>
				<div class="col-xs-12 col-md-12 col-lg-12"> </div>
				<?php
				$sql = "SELECT * FROM usuarios INNER JOIN PrazoLancamento ON Usuario_idUsuario = idusuarios WHERE Ativo = 0";
				$stmt = $conexao->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch(PDO::FETCH_OBJ)){
				?>
				<div class="col-xs-10 col-md-10 col-lg-10">
					<form class="form-inline" action="<?php echo BASE; ?>control/banco/PrazoDao.php" method="post" enctype="multipart/form-data" >
						<div class="form-group">
							<label for="dias">Prazo de Lançamento para o usuário <?=$row->Nome?>: </label>
							<input type="text" class="form-control" id="dias" name="dias" value="<?=$row->Prazo;?>">
							<input type="hidden" name="idusuarios" value="<?=$row->idusuarios;?>">
							<input type="hidden" name="Prazo" value="Alteracao">
						</div>
						<button type="submit" class="btn btn-default">Enviar</button>
					</form>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12"> </div>

				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>