<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location:". BASE);
else:
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
?>
<script>
	$(document).ready(function(){
		//Carrega Colaboradores
		$("input[name='Buscar']").change(function(e){
			if($("input[name='Buscar']").prop("checked")){
				var contrato = $('#contrato').val();//pegando o value do option selecionado
				var dataIN = $('#dataIN').val();//pegando o value do option selecionado
				var dataFN = $('#dataFN').val();//pegando o value do option selecionado
				if(contrato != '' && dataIN != '' && dataFN != ''){
					$.post('Parcial.inc.php',{contrato: contrato, dataIN: dataIN, dataFN: dataFN} , function (dados){
						//alert(dados);
						dados = JSON.parse(dados);
						//alert(dados);
						if (dados.length > 0){
							var option = '';
							var total = parseFloat(0);
							$.each(dados, function(i, obj){
								var t = parseFloat(obj.Total);
								option += '<tr>';
								option += '<td>'+obj.Servico+'</td>';
								option += '<td>'+obj.Data+'</td>';
								option += '<td>'+obj.ValorUni+'</td>';
								option += '<td>'+obj.Quant+'</td>';
								option += '<td class="text-right"> R$ '+t.toLocaleString('pt-BR')+'</td>';
								option += '</tr>';
								total += parseFloat(obj.Total);
							})
							$('#itens-lancamento').html(option).show();
							$('#Total').html(total.toLocaleString('pt-BR')).show();
							setTimeout(function() {
								$("input[name='Buscar']").prop("checked",false);
							}, 1500);
						}else{
							$("input[name='Buscar']").prop("checked",false);
							Reset();
						}
					})
				}else{
					$("input[name='Buscar']").prop("checked",false);
					return false;
				}
			}else{
				$("input[name='Buscar']").prop("checked",false);
				return false;
			}
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#Total').html("aguardando...").show();
			var op = '';
			op += '<tr>';
			op += '<td colspan="5">Aguardando...<i class="fa fa-coffee" aria-hidden="true"></i></td>';
			op += '</tr>';
			$('#itens-contrato').html(op).show();
		}
		$('.input-daterange').datepicker({
			todayBtn: "linked",
			language: "pt-BR"
		})
	});
</script>
<div class="container-fluid">
	<div class="conteudo">
		<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h2 class="text-center">Fechar Medição</h2></div>
		<form  name="Form" role="form" action="RelatorioAnalitico.php" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" target="_blank" id="FormCliente" name="FormCliente" >
			<div class="form-group">
				<label class="col-sm-2 control-label" for="unidade">Unidade:</label>
				<div class="col-sm-6">
					<select class="selectpicker form-control" title="Selecione uma Contrato!" name="contrato" id="contrato" data-live-search="true" data-width="100%" data-size="5" data-actions-box="true" required>
						<?php
						$sql = 'SELECT c.idContrato, c.nContrato, cad.Nome, cad.CNPJ FROM contrato c INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = ct.Cadastro_idCadastro WHERE c.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?);';
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $_SESSION['idusuarios']);
						$stm->execute();
						$rs = $stm->fetchAll(PDO::FETCH_OBJ);
						foreach($rs as $r):
						?>
						<option value="<?php echo $r->idContrato; ?>" data-tokens="<?php echo CNPJ_Padrao($r->CNPJ)." - ".utf8_decode($r->Nome); ?>"><?php echo $r->nContrato." - ".utf8_decode($r->Nome); ?></option>						
						<?php endforeach; ?>
					</select>
					<div class="help-block with-errors"></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Período de Apuração:</label>
				<div class="col-sm-3">
					<div class="input-daterange input-group" id="datepicker">
						<input type="text" class="form-control" id="dataIN" name="dataIN" />
						<span class="input-group-addon"> até </span>
						<input type="text" class="form-control" id="dataFN" name="dataFN" data-date-end-date="0d" />
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-2 col-md-offset-2 col-lg-offset-2">
				<div class="form-group">
					<label class="control-label col-sm-3" for="Buscar">Buscar: </label>
					<div class="col-sm-3">
						<label class="switch">
							<input name="Buscar" id="Buscar" type="checkbox" value="Sim">
							<span class="slider round"></span>
						</label>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-lg-12 col-md-12">
				<hr />
			</div>
			<div class="col-xs-12 col-lg-12 col-md-12 text-center">
				<table class="table table-bordered text-center">
					<thead>
						<tr>
							<th>Evento</th>
							<th>Data</th>
							<th>Valor Unitário</th>
							<th>Quantidade</th>
							<th>Valor Total</th>
						</tr>
					</thead>
					<tbody id="itens-lancamento">
						<tr>
							<td colspan="5">Aguardando...<i class="fa fa-coffee" aria-hidden="true"></i></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<small>
									Valor Total do periodo selecionado: 
									<strong>R$ <span id="Total">aguardando...</span></strong>
								</small>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="col-xs-offset-1 col-xs-11 col-md-offset-1 col-md-11 col-lg-offset-1 col-lg-11">
				<input type="hidden" value="Medicao" name="Cliente" />
				<button class="btn btn-success" type="submit">Gerar Medição</button>
			</div>
		</form>
		<div class="col-xs-12 col-md-12 col-lg-12"> </div>
	</div>
</div>
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>