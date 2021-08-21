<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
session_destroy();
header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
?>
<script>
	$(document).ready(function(){
		$('.input-daterange').datepicker({
			todayBtn: "linked",
			language: "pt-BR"
		})
	});
</script>
<div class="container-fluid">
	<div class="conteudo">
		<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h2 class="text-center">Medição Parcial</h2></div>
		<form  name="Form" role="form" action="RelatorioParcial.php" target="_blank" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente" >
			<div class="form-group">
				<label class="col-sm-2 control-label" for="unidade">Unidade:</label>
				<div class="col-sm-6">
					<select class="selectpicker form-control" title="Selecione uma Contrato!" name="contrato" id="contrato" data-live-search="true" data-width="100%" data-size="5" data-actions-box="true" required>
						<?php
						$sql = "SELECT c.idContrato, c.nContrato, c.cCusto, cad.Nome, cad.CNPJ FROM contrato c INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = ct.Cadastro_idCadastro WHERE c.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) AND c.Finalizado = 'N' ORDER BY c.nContrato;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $_SESSION['idusuarios']);
						$stm->execute();
						$rs = $stm->fetchAll(PDO::FETCH_OBJ);
						foreach($rs as $r):
						?>
						<option value="<?php echo $r->idContrato; ?>" data-tokens="<?php echo CNPJ_Padrao($r->CNPJ)." - ".utf8_decode($r->Nome)." - ".utf8_decode($r->cCusto); ?>">Contrato: <?php echo $r->nContrato." - Cliente: ".utf8_decode($r->Nome)." - Centro de Custo: ".utf8_decode($r->cCusto); ?></option>						
						<?php endforeach; ?>
					</select>
					<div class="help-block with-errors"></div>
                    <!--<p class="text-left"><small>CC = Centro de Custo</small></p>-->
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Data:</label>
				<div class="col-sm-3">
					<div class="input-daterange input-group" id="datepicker">
						<input type="text" class="form-control" id="dataIN" name="dataIN" />
						<span class="input-group-addon"> até </span>
						<input type="text" class="form-control" id="dataFN" name="dataFN" data-date-end-date="0d" />
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-md-4 col-lg-4">
				<div class="form-group">
                    <button class="btn btn-default" name="gerar" id="gerar">Gerar PDF</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
require_once("../control/arquivo/footer/Footer.php");
}
?>