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
?>
<script>
	$(document).ready(function(){
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
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#nome').html('').show();
			$('#cnpj').html('').show();
			$('#IE').html('').show();
			$('#endereco').html('').show();
			$('#responsavel').html('').show();
			$('#telefone').html('').show();
			$('#celular').html('').show();
			$('#CodContratante').val('').show();
		}
	});
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<h1 class="text-center">HISTORICO CLIENTE</h1>
			<form name="Form" role="form" action="" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente">
				<div class="col-xs-12 col-md-12 col-lg-12 text-center">
					<div class="form-group">
						<label class="control-label col-sm-2" for="cliente">Cliente: </label>
						<div class="col-sm-8">
							<select class="selectpicker form-control dropdown" name="cliente" id="cliente" title="Selecione um Cliente" data-size="5" data-header="Selecione um Cliente" data-live-search="true" autofocus>
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
			<form name="Form" role="form" action="<?php echo BASE; ?>control/banco/MovimentacaoDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormCliente" name="FormCliente">
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
				<div class="col-xs-7 col-md-7 col-lg-7">
					<div class="form-group">
						<label class="control-label col-sm-2" for="tipo">Tipo: </label>
						<div class="col-sm-10">
							<select class="selectpicker form-control dropdown" required name="tipo" id="tipo" title="Selecione um Tipo" data-size="5" data-live-search="true">
								<option data-tokens="Ligação" value="Ligação" >Ligação</option>
								<option data-tokens="E-mail" value="E-mail" >E-mail</option>
								<option data-tokens="Mensagem" value="Mensagem" >Mensagem</option>
								<option data-tokens="Reunião" value="Reunião" >Reunião</option>
							</select>
							<div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-3 col-md-3 col-lg-3">
					<div class="form-group">
						<label class="control-label col-sm-2" for="data">Data: </label>
						<div class="col-sm-10">
							<input type="date" name="data" id="data" class="form-control" required />
							<div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-7 col-md-7 col-lg-7">
					<div class="form-group">
						<label class="control-label col-sm-2" for="descricao">Descrição: </label>
						<div class="col-sm-10">
							<textarea name="descricao" id="descricao" class="form-control" rows="4" required maxlength="500" minlength="50" ></textarea>
							<div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-7 col-md-7 col-lg-7">
					<div class="form-group">
						<label class="control-label col-sm-2" for="arquivo">Arquivo: </label>
						<div class="col-sm-10">
							<input type="file" class="filestyle" name="doc" id="doc" data-placeholder="Arquivo formato PDF!" data-icon="false" data-buttonText="Selecionar Arquivo" data-buttonName="btn-primary" data-buttonBefore="true" />
							<div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-2 col-md-offset-2 col-lg-offset-2">
					<input type="hidden" value="Movimentação" name="Cliente" />
					<input type="hidden" value="" name="CodContratante" id="CodContratante" />
					<button class="btn btn-success" type="submit">Enviar</button>
					<button class="btn btn-warning" type="reset">Cancelar</button>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			</form>
		</div>
	</div>
</div>
<?php
require_once("../control/arquivo/footer/Footer.php");
endif;
?>