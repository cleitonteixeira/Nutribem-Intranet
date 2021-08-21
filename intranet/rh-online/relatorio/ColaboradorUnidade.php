<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
    session_destroy();
    require_once("../control/arquivo/funcao/Outras.php");
    require_once("../control/banco/conexao.php");
    require_once("../control/arquivo/header/Header.php");
    require_once("../control/arquivo/Login.php");
else:
    require_once("../control/Pacote.php");
	$conexao = conexao::getInstance();
?>
<script>
	$(document).ready(function(){
		//Carrega Colaboradores
		$("select[name='unidade']").change(function(e){
			var unidade = $('#unidade').val();//pegando o value do option selecionado
			//alert(empresa);//apenas para debugar a variável

			$.getJSON('CompletaSelect.inc.php?u='+unidade, function (dados){
				//alert(dados);
				if (dados.length > 0){ 	
					var option = '<option value="">Selecione!</option>';
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.Codigo+'">'+obj.Codigo+' - '+obj.Colaborador+'</option>';
					})
					$('#colaborador').html(option).show();
				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#colaborador').empty();
		}
		//Carrega Unidades
		$("select[name='unidade']").change(function(e){
			var unidade = $('#unidade').val();//pegando o value do option selecionado
			//alert(empresa);//apenas para debugar a variável

			$.getJSON('CompletaSelect.inc.php?t='+unidade, function (dados){
				//alert(dados);
				if (dados.length > 0){ 	
					var option = '<option value="">Selecione!</option>';
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.Codigo+'">'+obj.Unidade+'</option>';
					})
					$('#transferida').html(option).show();
				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#transferida').empty();
		}
		
		//Carrega cargos
		$("select[name='unidade']").change(function(e){
			var unidade = $('#unidade').val();//pegando o value do option selecionado
			//alert(unidade);//apenas para debugar a variável

			$.getJSON('CompletaSelect.inc.php?unidade='+unidade, function (dados){
				//alert(dados);
				if (dados.length > 0){
					var option = '<option value="" disable>Selecione!</option>';
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.idCargo+'">'+obj.CodCargo+' - '+obj.Funcao+'</option>';
					})
					$('#cargo').html(option).show();
				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#cargo').empty();
		}
		//Mostra dados dos cargos
		$("select[name='cargo']").change(function(e){
			var unidade = $('#cargo').val();//pegando o value do option selecionado
			//alert(empresa);//apenas para debugar a variável

			$.getJSON('CompletaSelect.inc.php?c='+unidade, function (dados){
				//alert(dados);
				if (dados.length == 1){
					var cargo = '';
					var cbo = '';
					var funcao = '';
					var salario = '';
					$.each(dados, function(i, obj){
						cargo  = obj.Cargo;
						cbo  = obj.CBO;
						funcao  = obj.Funcao;
						salario  = obj.Salario;
					})
					$('#cargoShow').val(cargo).show();
					$('#cboShow').val(cbo).show();
					$('#funcaoShow').val(funcao).show();
					$('#salarioShow').val(salario).show();
				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#cargoShow').empty();
			$('#cboShow').empty();
			$('#funcaoShow').empty();
			$('#salarioShow').empty();
		}
		$('.input-daterange').datepicker({
			todayBtn: "linked",
			language: "pt-BR"
		});
	});
</script>
<div class="container-fluid">
	<div class="conteudo">
		<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h2 class="text-center">Relatório Colaborador por Unidade</h2></div>
		<form name="Form" role="form" target="_blank" action="RelColaboradorUnidade.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormColaborador" name="FormColaborador">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="unidade">Unidade:</label>
				<div class="col-sm-6">
					<select class="selectpicker form-control" title="Selecione uma Unidade!" name="unidade" id="unidade" data-live-search="true" data-width="100%" data-size="5" data-actions-box="true" required>
						<?php
						$sql = 'SELECT DISTINCT(c.CNPJ) FROM unidade u INNER JOIN empresa e ON e.idEmpresa = u.Empresa_idEmpresa INNER JOIN cadastro c ON c.idCadastro = e.Cadastro_idCadastro WHERE u.idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)';
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $_SESSION['idusuarios']);
						$stm->execute();
						$rs = $stm->fetchAll(PDO::FETCH_OBJ);
						foreach($rs as $r):
						?>
						<optgroup label="<?php echo CNPJ_Padrao($r->CNPJ); ?>" >
							<?php
							$sql = 'SELECT un.idUnidade, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidade un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? AND un.idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $r->CNPJ);
							$stm->bindParam(2, $_SESSION['idusuarios']);
							$stm->execute();
							while($row = $stm->fetch(PDO::FETCH_OBJ)):
							?>
							<option value="<?php echo $row->idUnidade; ?>" data-tokens="<?php echo str_pad($row->idUnidade,2,0, STR_PAD_LEFT)." - ".utf8_decode($row->Nome); ?>"><?php echo str_pad($row->idUnidade,2,0, STR_PAD_LEFT)." - ".utf8_decode($row->Nome); ?></option>
							<?php endwhile; ?>
						</optgroup>
						<?php endforeach; ?>
					</select>
					<div class="help-block with-errors"></div>
				</div>
			</div>
			<div class="col-xs-offset-1 col-xs-8 text-center">
				<div class=" panel panel-default">
					<div class="panel-heading"><h1 class="panel-title">Filtros Opcionais</h1></div>
					<div class="panel-body">
						<div class="col-xs-6">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="cargo">Cargo:</label>
								<div class="col-sm-10">
									<select multiple class="form-control" title="Selecione um Cargo!" name="cargo[]" id="cargo">
									</select>
								</div>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<label class="col-sm-8 control-label" for="colaborador">Todos / Ativos / Inativos:</label>
								<div class="col-sm-12">
									<label class="radio-inline"><input type="radio" name="filtro" value="Todos" checked>Todos</label>
									<label class="radio-inline"><input type="radio" name="filtro" value="Ativos">Ativos</label>
									<label class="radio-inline"><input type="radio" name="filtro" value="Inativos">Inativos</label>
								</div>	
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="col-xs-6">
							<div class="form-group">
								<div class="col-xs-12">
									<div class="form-group">
										<label class="col-sm-8 control-label" for="colaborador">Entre / Antes / Depois :</label>
										<div class="col-sm-10">
											<label class="radio-inline"><input type="radio" name="fData" value="Entre">Entre</label>
											<label class="radio-inline"><input type="radio" name="fData" value="Antes">Antes</label>
											<label class="radio-inline"><input type="radio" name="fData" value="Depois">Depois</label>
										</div>	
									</div>
								</div>
								
								<label class="col-sm-2 control-label">Data:</label>
								<div class="col-sm-8">
									<div class="input-daterange input-group" id="datepicker">
										<input type="text" class="form-control" name="dataIN" />
										<span class="input-group-addon"> até </span>
										<input type="text" class="form-control" name="dataFN" data-date-end-date="0d" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-8 col-sm-3">
					<input type="hidden" value="ColUni" name="Relatorio">
					<button type="submit" id="submit" class="btn btn-success">Emitir</button>
					<button type="reset" class="btn btn-warning">Limpar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>