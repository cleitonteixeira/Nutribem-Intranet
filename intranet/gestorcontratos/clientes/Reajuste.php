<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location: ".BASE);
else:
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
$Troca = array("/","\\","|");
$sql = "SELECT  c.idContratante, cd.Nome AS Cliente, cd.CNPJ AS CNPJ FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro;";
$stm = $conexao->prepare($sql);
$stm->execute();
$rs = $stm->fetchAll(PDO::FETCH_OBJ);
$sql = "SELECT e.idEmpresa, cad.Nome AS Empresa, cad.CNPJ AS CNPJ FROM empresa e INNER JOIN cadastro cad ON cad.idCadastro =  e.Cadastro_idCadastro";
$stm = $conexao->prepare($sql);
$stm->execute();
$rsEm = $stm->fetchAll(PDO::FETCH_OBJ);
?>
<script>
	$(document).ready(function(){
		$('#botoes').hide();
		$("input[name='almoco']").keyup(function(e){
			var preco = $('#almoco').val();
			var valor = $('#valAlmoco').val();
			preco = preco*valor;
			$('#alAlmoco').val(preco).show();
		})
		$("input[name='jantar']").keyup(function(e){
			var preco = $('#jantar').val();
			var valor = $('#valJantar').val();
			preco = preco*valor;
			$('#alJantar').val(preco).show();
		})
		$("input[name='ceia']").keyup(function(e){
			var preco = $('#ceia').val();
			var valor = $('#valCeia').val();
			preco = preco*valor;
			$('#alCeia').val(preco).show();
		})
		$("input[name='desjejum']").keyup(function(e){
			var preco = $('#desjejum').val();
			var valor = $('#valDesjejum').val();
			preco = preco*valor;
			$('#alDesjejum').val(preco).show();
		})
		$("input[name='aniMes']").keyup(function(e){
			var preco = $('#aniMes').val();
			var valor = $('#valAniMes').val();
			preco = preco*valor;
			$('#alAniMes').val(preco).show();
		})
		$("select[name='cliente']").change(function(e){
			var cli = $('#cliente').val();//pegando o value do option selecionado
			//alert(cli);//apenas para debugar a variável
			$.post('Completa.inc.php',{cliente: cli} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length != 0 ){ 	
					var Nome = '';
					var CNPJ = '';
					var IE = '';
					var Endereco = '';
					var rComercial = '';
					var tComercial = '';
					var eComercial = '';
					var rFinanceiro = '';
					var eFinanceiro = '';
					var tFinanceiro = '';
					var Responsavel = '';
					var CodCont = '';
					$.each(dados, function(i, obj){
						Nome = obj.Nome;
						CNPJ = obj.CNPJ;
						IE = obj.IE;
						Endereco = obj.Endereco;
						eCobranca = obj.eCobranca;
						rComercial = obj.rComercial;
						tComercial = obj.tComercial;
						eComercial = obj.eComercial;
						rFinanceiro = obj.rFinanceiro;
						tFinanceiro = obj.tFinanceiro;
						eFinanceiro = obj.eFinanceiro;
						Telefone = obj.Telefone;
						CodCont = obj.idContratante;
					})
					$('#nome').html(Nome).show();
					$('#cnpj').html(CNPJ).show();
					$('#IE').html(IE).show();
					$('#endereco').html(Endereco).show();
					$('#eCobranca').html(eCobranca).show();
					$('#rComercial').html(rComercial).show();
					$('#eComercial').html(eComercial).show();
					$('#tComercial').html(tComercial).show();
					$('#rFinanceiro').html(rFinanceiro).show();
					$('#eFinanceiro').html(eFinanceiro).show();
					$('#tFinanceiro').html(tFinanceiro).show();
					$('#CodContratante').val(CodCont);
				}else{
					Reset1();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset1(){
			$('#nome').html('').show();
			$('#cnpj').html('').show();
			$('#IE').html('').show();
			$('#endereco').html('').show();
            $('#eCobranca').html('').show();
            $('#rComercial').html('').show();
            $('#eComercial').html('').show();
            $('#tComercial').html('').show();
            $('#rFinanceiro').html('').show();
            $('#eFinanceiro').html('').show();
            $('#tFinanceiro').html('').show();
			$('#CodContratante').val('').show();
		}
		$("select[name='cliente']").change(function(e){
			var cli = $('#cliente').val();//pegando o value do option selecionado
			//alert(cli);//apenas para debugar a variável
			$.post('Completa.inc.php',{gcontrato: cli} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length != 0 ){ 	
					var option = '<option value="">Selecione!</option>';
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.idContrato+'">Nº: '+obj.Contrato+' - Data Cadastro: '+obj.DataCadastro+' - Data Reajuste: '+obj.DataReajuste+'</option>';
					})
					$('#Contrato').html(option).show();
				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#Contrato').empty();
		}
		
		$("select[name='Contrato']").change(function(e){
			var prop = $('#Contrato').val();//pegando o value do option selecionado
			//alert(unidade);//apenas para debugar a variável
			$.post('Completa.inc.php',{cproposta: prop} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length > 0){ 	
					var option = '';
					$.each(dados, function(i, obj){
						option += '<tr>';
						option += '<td>'+obj.Servico+'</td>';
						option += '<td>'+obj.Valor+'</td>';
						option += '</tr>';
					})
					$('#botoes').hide();
					$('#itens-contrato').html(option).show();
					$('#botoes').toggle();
				}else{
					Reset3();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset3(){
			$('#botoes').hide();
			var op = '';
			op += '<tr>';
			op += '<td colspan="5">Aguardando...<i class="fa fa-coffee" aria-hidden="true"></i></td>';
			op += '</tr>';
			$('#itens-contrato').html(op).show();
		}
	});
</script>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<h1 class="text-center">REAJUSTE DE CONTRATO</h1>
			<form name="Form" role="form" action="" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente">
				<div class="col-xs-12 col-md-12 col-lg-12 text-center">
					<div class="form-group">
						<label class="control-label col-sm-2" for="cliente">Cliente: </label>
						<div class="col-sm-8">
							<select class="selectpicker form-control dropdown" name="cliente" id="cliente" title="Selecione um Cliente" data-size="5" data-header="Selecione um Cliente" data-live-search="true">
								<?php
								foreach($rs as $r ):
								?>
								<option data-tokens="<?php echo $r->CNPJ.' '.utf8_decode($r->Cliente).' '.str_pad($r->idContratante,3,0,STR_PAD_LEFT); ?>" data-subtext="CNPJ: <?php echo CNPJ_Padrao(str_pad($r->CNPJ,14,0,STR_PAD_LEFT)); ?>" value="<?php echo $r->idContratante ?>" ><?php echo str_pad($r->idContratante,3,0,STR_PAD_LEFT)." - ".utf8_decode($r->Cliente); ?></option>
								<?php
								endforeach;
								?>
							</select>
						</div>
					</div>
				</div>
			</form>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<hr />
			</div>
			<form name="Form" role="form" action="<?php echo BASE; ?>clientes/Readjust.php" method="post" enctype="multipart/form-data" target="_blank" class="form-horizontal" data-toggle="validator" id="FormCliente" name="FormCliente">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="panel panel-default text-justify">
						<div class="panel-heading">
							<h1 class="panel-title">Dados Cliente</h1>
						</div>
						<div class="panel-body">
							<div class="col-xs-8 col-md-8 col-lg-6"><p><strong>Nome: </strong><span id="nome"></span></p></div>
							<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>CNPJ: </strong><span id="cnpj"></span></p></div>
							<div class="col-xs-12 col-md-12 col-lg-12"><p><strong>IE: </strong><span id="IE"></span></p></div>
							<div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço: </strong><span id="endereco"></span></p></div>	
							<div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço de Cobranca: </strong><span id="eCobranca"></span></p></div>	
							
							<div class="col-xs-4 col-md-4 col-lg-5"><p><strong>Responsável Comercial: </strong><span id="rComercial"></span></p></div>
							<div class="col-xs-4 col-md-4 col-lg-3"><p><strong>Telefone: </strong><span id="tComercial"></span></p></div>
							<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><span id="eComercial"></span></p></div>
							
							<div class="col-xs-4 col-md-4 col-lg-5"><p><strong>Responsável Financeiro: </strong><span id="rFinanceiro"></span></p></div>
							<div class="col-xs-4 col-md-4 col-lg-3"><p><strong>Telefone: </strong><span id="tFinanceiro"></span></p></div>
							<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><span id="eFinanceiro"></span></p></div>
							
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<hr />
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="text-center"><h3><u>Valores e Eventos do Contrato</u></h3></div>
					<label class="col-xs-1 col-md-1 col-lg-1 control-label" for="Contrato">Contrato:</label>
					<div class="col-sm-6">
						<select class="form-control" title="Selecione um Contrato" name="Contrato" id="Contrato" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione um Contrato." required>
						</select>
					</div>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="col-xs-8 col-md-8 col-lg-8">
						<h2>Itens do Contrato</h2>
						<table class="table table-bordered text-center">
							<thead>
								<tr>
									<th>Evento</th>
									<th>Valor</th>
								</tr>
							</thead>
							<tbody id="itens-contrato">
								<tr>
									<td colspan="2">Aguardando...<i class="fa fa-coffee" aria-hidden="true"></i></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="2"><strong>Lista dos Serviços do Contrato.</strong></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div id="botoes" class="col-xs-offset-1 col-xs-11 col-md-offset-1 col-md-11 col-lg-offset-1 col-lg-11">
					<button class="btn btn-success">Reajustar Contrato</button>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12"> 
				</div>
			</form>
		</div>
	</div>
</div>
<?php
require_once("../control/arquivo/footer/Footer.php");
endif;
?>