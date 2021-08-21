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
			//alert(unidade);//apenas para debugar a variável
			$.post('Completa.inc.php',{gproposta: cli} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length > 0){ 	
					var option = '<option value="">Selecione!</option>';
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.idProposta+'">Nº: '+obj.nProposta+' - Data: '+obj.dProposta+'</option>';
					})
					$('#Proposta').html(option).show();
				}else{
					Reset2();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset2(){
			$('#Proposta').empty();
		}
		$("select[name='Proposta']").change(function(e){
			var prop = $('#Proposta').val();//pegando o value do option selecionado
			//alert(unidade);//apenas para debugar a variável
			$.post('Completa.inc.php',{dproposta: prop} , function (dados){
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
					$('#itens-proposta').html(option).show();
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
			$('#itens-proposta').html(op).show();
		}
		$("select[name='Proposta']").change(function(e){
			var prop = $('#Proposta').val();//pegando o value do option selecionado
			//alert(prop);//apenas para debugar a variável
			$.post('Completa.inc.php',{iproposta: prop} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length > 0){ 	
					var tReajuste = '';
					var fMedicao = '';
					var tVigencia = '';
					var Condicao = '';
					var fPagamento = '';
					var nProposta = '';
					$.each(dados, function(i, obj){
						tReajuste = obj.tReajuste;
						tVigencia = obj.tVigencia;
						nProposta = obj.nProposta;
					})
					$('#tReajuste').html(tReajuste).show();
					$('#tVigencia').html(tVigencia).show();
					$('#nProposta').html(nProposta).show();
					document.getElementById('gerar-contrato').href="<?php echo BASE; ?>clientes/Contrato.php?p="+prop;
					document.getElementById('gerar-proposta').href="<?php echo BASE; ?>clientes/GerarProposta.php?p="+prop;
				}else{
					Reset4();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset4(){
			$('#tReajuste').html().show();
			$('#tVigencia').html().show();
			document.getElementById('gerar-contrato').href="#";
			document.getElementById('gerar-proposta').href="#";
		}
	});
</script>
<script type="text/javascript">
	$(function(){
		$("#valor1").maskMoney();
		$("#valor2").maskMoney();
		$("#valor3").maskMoney();
		$("#valor4").maskMoney();
		$("#valor5").maskMoney();
		$("#valor6").maskMoney();
		$("#valor7").maskMoney();
	})
</script>
<script type="text/javascript">
	//Total máximo de campos que você permitirá criar em seu site:
	var totalCampos = 7;

	//Não altere os valores abaixo, pois são variáveis controle;
	var iLoop = 1;
	var iCount = 0;
	var linhaAtual;

	function AddCampos() {
		var hidden1 = document.getElementById("hidden1");
		var hidden2 = document.getElementById("hidden2");

		//Executar apenas se houver possibilidade de inserção de novos campos:
		if (iCount < totalCampos) {

			//Limpar hidden1, para atualizar a lista dos campos que ainda estão vazios:
			hidden2.value = "";

			//Atualizando a lista dos campos que estão ocultos.
			//Essa lista ficará armazenada temporiariamente em hidden2;
			for (iLoop = 1; iLoop <= totalCampos; iLoop++) {
				if (document.getElementById("linha"+iLoop).style.display == "none") {
					if (hidden2.value == "") {
						hidden2.value = "linha"+iLoop;
					}else{
						hidden2.value += ",linha"+iLoop;
					}
				}
			}
			//Quebrando a lista que foi armazenada em hidden2 em array:

			linhasOcultas = hidden2.value.split(",");

			if (linhasOcultas.length > 0) {
				//Tornar visível o primeiro elemento de linhasOcultas:
				document.getElementById(linhasOcultas[0]).style.display = "block"; iCount++;

				//Acrescentando o índice zero a hidden1:
				if (hidden1.value == "") {
					hidden1.value = linhasOcultas[0];
				}else{
					hidden1.value += ","+linhasOcultas[0];
				}

				/*Retirar a opção acima da lista de itens ocultos: <-------- OPCIONAL!!!
			if (hidden2.value.indexOf(","+linhasOcultas[0]) != -1) {
					hidden2.value = hidden2.value.replace(linhasOcultas[0]+",","");
			}else if (hidden2.indexOf(linhasOcultas[0]+",") == 0) {
					hidden2.value = hidden2.value.replace(linhasOcultas[0]+",","");
			}else{
					hidden2.value = "";
			}
			*/
			}
		}
	}

	function RemoverCampos(id) {
		//Criando ponteiro para hidden1:        
		var hidden1 = document.getElementById("hidden1");
		//Pegar o valor do campo que será excluído:
		var campoValor = document.getElementById("dNome["+id+"]").value;
		//Se o campo não tiver nenhum valor, atribuir a string: vazio:
		if (campoValor == "") {
			campoValor = "vazio";
		}

		if(confirm("O campo que contém o valor:\n» "+campoValor+"\nserá excluído!\n\nDeseja prosseguir?")){
			document.getElementById("linha"+id).style.display = "none"; iCount--;

			//Removendo o valor de hidden1:
			if (hidden1.value.indexOf(",linha"+id) != -1) {
				hidden1.value = hidden1.value.replace(",linha"+id,"");
			}else if (hidden1.value.indexOf("linha"+id+",") == 0) {
				hidden1.value = hidden1.value.replace("linha"+id+",","");
			}else{
				hidden1.value = "";
			}
		}
	}
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<h1 class="text-center">SITUAÇÃO DAS PROPOSTAS</h1>
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
			<form name="Form" role="form" action="<?php echo BASE; ?>control/banco/ContratoDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormCliente" name="FormCliente">
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
					<div class="text-center"><h3><u>Serviços e Valores da Propostas</u></h3></div>
					<label class="col-xs-1 col-md-1 col-lg-1 control-label" for="Proposta">Propostas:</label>
					<div class="col-sm-6">
						<select class="form-control" title="Selecione uma Proposta" name="Proposta" id="Proposta" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma Proposta." required>
						</select>
					</div>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="col-xs-6 col-md-6 col-lg-6">
						<h2>Itens do Pedido Nº:  <small><span id="nProposta"></span></small></h2>
						<table class="table table-bordered text-center">
							<thead>
								<tr>
									<th>Serviço</th>
									<th>Valor</th>
								</tr>
							</thead>
							<tbody id="itens-proposta">
								<tr>
									<td colspan="2">Aguardando...<i class="fa fa-coffee" aria-hidden="true"></i></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="2"><strong>Lista dos Serviços da proposta.</strong></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="col-xs-6 col-md-6 col-lg-6">
						<p><strong>Tipo Reajuste:</strong> <span id="tReajuste"></span></p>
						<p><strong>Tempo de Vigência do Contrato:</strong> <span id="tVigencia"></span></p>
					</div>
				</div>
				
				<div id="botoes" class="col-xs-offset-1 col-xs-11 col-md-offset-1 col-md-11 col-lg-offset-1 col-lg-11">
					<input type="hidden" value="" name="CodContratante" id="CodContratante" />
					
					<a class="btn btn-success" role="button" id="gerar-contrato" href="" ><i class="fa fa-suitcase" aria-hidden="true"></i>  Gerar Contrato</a>
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