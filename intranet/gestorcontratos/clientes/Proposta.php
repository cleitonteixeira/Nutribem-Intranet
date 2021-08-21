<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
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
			$.post('Completa.inc.php',{ proposta: cli} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length != 0 ){
					var nProposta = '';
					$.each(dados, function(i, obj){
						nProposta = obj.nProposta;
					})
					$('#nProposta').val(nProposta);
				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#nProposta').val('').show();
		}
		$("#pConsolidada").hide();
        function StatusDis(Status){
            $("#tReajuste").prop("disabled", Status);
            $("#fechamento").prop("disabled", Status);
            $("#tVigencia").prop("disabled", Status);
            $("#condicao").prop("disabled", Status);
            $("#fPagamento").prop("disabled", Status);
            if(Status == false){
                $("#tReajuste").attr("required", "required");
                $("#fechamento").attr("required", "required");
                $("#tVigencia").attr("required", "required");
                $("#condicao").attr("required", "required");
                $("#fPagamento").attr("required", "required");
            }else{
                $("#tReajuste").removeAttr("required", "required");
                $("#fechamento").removeAttr("required", "required");
                $("#tVigencia").removeAttr("required", "required");
                $("#condicao").removeAttr("required", "required");
                $("#fPagamento").removeAttr("required", "required");
            }
        }
		$("input[name='cProposta']").change(function(e){
			if($("input[name='cProposta']").prop("checked")){
				$("#pConsolidada").toggle();
                StatusDis(false);
			}else{
				$("#pConsolidada").hide();
                StatusDis(true);
			}
		})
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
		$("#valor8").maskMoney();
		$("#valor9").maskMoney();
		$("#valor10").maskMoney();
		$("#valor11").maskMoney();
		$("#valor12").maskMoney();
		$("#valor13").maskMoney();
		$("#valor14").maskMoney();
		$("#valor15").maskMoney();
		$("#valor16").maskMoney();
		$("#valor17").maskMoney();
		$("#valor18").maskMoney();
		$("#valor19").maskMoney();
		$("#valor20").maskMoney();
		
	})
</script>
<script type="text/javascript">
	//Total máximo de campos que você permitirá criar em seu site:
	var totalCampos = 20;

	//Não altere os valores abaixo, pois são variáveis controle;
	var iLoop = 2;
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
			for (iLoop = 2; iLoop <= totalCampos; iLoop++) {
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
		var campoValor = document.getElementById("Evento"+id+"").value;
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
<script type="text/javascript">
	//Total máximo de campos que você permitirá criar em seu site:
	var totalCampos2 = 99;

	//Não altere os valores abaixo, pois são variáveis controle;
	var iLoop2 = 2;
	var iCount2 = 0;
	var linhaAtual2;

	function AddCampos2() {
		var hidden5 = document.getElementById("hidden5");
		var hidden6 = document.getElementById("hidden6");

		//Executar apenas se houver possibilidade de inserção de novos campos:
		if (iCount2 < totalCampos2) {

			//Limpar hidden1, para atualizar a lista dos campos que ainda estão vazios:
			hidden6.value = "";

			//Atualizando a lista dos campos que estão ocultos.
			//Essa lista ficará armazenada temporiariamente em hidden2;
			for (iLoop2 = 2; iLoop2 <= totalCampos2; iLoop2++) {
				if (document.getElementById("line"+iLoop2).style.display == "none") {
					if (hidden6.value == "") {
						hidden6.value = "line"+iLoop2;
					}else{
						hidden6.value += ",line"+iLoop2;
					}
				}
			}
			//Quebrando a lista que foi armazenada em hidden2 em array:

			linhasOcultas2 = hidden6.value.split(",");

			if (linhasOcultas2.length > 0) {
				//Tornar visível o primeiro elemento de linhasOcultas:
				document.getElementById(linhasOcultas2[0]).style.display = "block"; iCount2++;

				//Acrescentando o índice zero a hidden1:
				if (hidden5.value == "") {
					hidden5.value = linhasOcultas2[0];
				}else{
					hidden5.value += ","+linhasOcultas2[0];
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

	function RemoverCampos2(id2) {
		//Criando ponteiro para hidden1:        
		var hidden5 = document.getElementById("hidden5");
		//Pegar o valor do campo que será excluído:
		var campoValor2 = document.getElementById("Arquivo"+id2+"").value;
		//Se o campo não tiver nenhum valor, atribuir a string: vazio:
		if (campoValor2 == "") {
			campoValor2 = "vazio";
		}

		if(confirm("O campo que contém o valor:\n» "+campoValor2+"\nserá excluído!\n\nDeseja prosseguir?")){
			document.getElementById("line"+id2).style.display = "none"; iCount2--;

			//Removendo o valor de hidden1:
			if (hidden5.value.indexOf(",line"+id2) != -1) {
				hidden5.value = hidden5.value.replace(",line"+id2,"");
			}else if (hidden5.value.indexOf("line"+id2+",") == 0) {
				hidden5.value = hidden5.value.replace("line"+id2+",","");
			}else{
				hidden5.value = "";
			}
		}
	}
</script>
<script language='JavaScript'>
	function SomenteNumero(e){
		var tecla=(window.event)?event.keyCode:e.which;   
		if((tecla>47 && tecla<58)) return true;
		else{
			if (tecla==8 || tecla==0) return true;
			else  return false;
		}
	}
</script>pConsolidada
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<h1 class="text-center">FORMULÁRIO DE PROPOSTAS</h1>
			<form name="Form" role="form" action="<?php echo BASE; ?>control/banco/PropostaDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormCliente" name="FormCliente">
                <div class="col-xs-12 col-md-12 col-lg-12 text-center">
					<div class="form-group">
						<label class="control-label col-sm-2" for="cliente">Cliente: </label>
						<div class="col-sm-8">
							<select class="selectpicker form-control dropdown" name="cliente" id="cliente" title="Selecione um Cliente" data-size="5" data-header="Selecione um Cliente" required data-live-search="true">
								<?php
								foreach($rs as $r ):
								?>
								<option data-tokens="<?php echo $r->CNPJ.' '.utf8_decode($r->Cliente).' '.str_pad($r->idContratante,3,0,STR_PAD_LEFT); ?>" data-subtext="CNPJ: <?php echo CNPJ_Padrao(str_pad($r->CNPJ,14,0,STR_PAD_LEFT)); ?>" value="<?php echo $r->idContratante ?>" ><?php echo str_pad($r->idContratante,3,0,STR_PAD_LEFT)." - ".utf8_decode($r->Cliente); ?></option>
								<?php
								endforeach;
								?>
							</select>
                            <div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
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
				<div class="col-xs-7 col-md-7 col-lg-7">
					<div class="form-group">
						<label class="control-label col-sm-3" for="nProposta">Proposta Nº: </label>
						<div class="col-sm-3">
							<input type="text" name="nProposta" id="nProposta" value="" class="form-control" readonly />
							<div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-7 col-md-7 col-lg-7">
                    <h3 for="arquivos">Arquivos da Proposta: </h3>
                    <div class="col-md-12 col-xs-12 col-lg-12"> </div>
                    <div class="col-md-12 col-xs-12 col-lg-12">
                        <div class='col-xs-9 col-sm-9 col-md-9'>
                            <div class='form-group'>
                                <label class='control-label col-sm-4' for='Arquivo'>Arquivo Proposta:</label>
                                <div class='col-sm-8'>
                                    <input type='file' class='filestyle' name="Arquivo1" id="Arquivo1" data-icon='false' data-buttonText='Selecionar Arquivo' data-buttonName='btn-default' data-buttonBefore='true' required />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            //Escrevendo o código-fonte HTML e ocultando os campos criados:
                            for (iLoop2 = 2; iLoop2 <= totalCampos2; iLoop2++) {
                                document.write("<div id='line"+iLoop2+"' style='display:none'><div class='col-xs-9 col-sm-9 col-md-9'><div class='form-group'><label class='control-label col-sm-4' for='Arquivo"+iLoop2+"'>Arquivo Proposta:</label><div class='col-sm-8'><input type='file' class='filestyle' name='Arquivo"+iLoop2+"' id='Arquivo"+iLoop2+"' data-icon='false' data-buttonText='Selecionar Arquivo' data-buttonName='btn-default' data-buttonBefore='true' /></div></div></div><div class='col-xs-3 col-sm-3 col-md-3'><button type='button' class='btn btn-danger' title='Remover Campos' onclick='RemoverCampos2(\""+iLoop2+"\")'>-</button></div></div>");
                            }
                        </script>
                        <div class="col-xs-offset-2 col-xs-10">
                            <button type="button" title="Adicionar Campos" class="btn btn-default" onclick="AddCampos2()">+</button>
                            <input type="hidden" name="hidden5" id="hidden5">
                            <input type="hidden" name="hidden6" id="hidden6">
                        </div>
                    </div>
				</div>
				<div class="col-xs-12 col-lg-12 col-md-12"> </div>
				<div class="col-xs-12 col-lg-12 col-md-12">
					<div class="form-group">
						<label class="control-label col-sm-3" for="cProposta">Proposta Consolidada: </label>
						<div class="col-sm-7">
							<label class="switch">
							  <input name="cProposta" id="cProposta" type="checkbox" value="Sim">
							  <span class="slider round"></span>
							</label>
						</div>
					</div>
				</div>
				<div id="pConsolidada" class="pConsolidada">
					<div class="col-xs-7 col-md-7 col-lg-7">
						<div class="form-group">
							<label class="control-label col-sm-3" for="tReajuste">Frequência Reajuste: </label>
							<div class="col-sm-9">
								<select disabled class="form-control dropdown" name="tReajuste" id="tReajuste" title="Selecione um Tipo" data-size="5" data-live-search="true">
									<option data-tokens="" value="" >Selecione um Tipo</option>
									<option data-tokens="Trimestral" value="Trimestral" >Trimestral</option>
									<option data-tokens="Semestral" value="Semestral" >Semestral</option>
									<option data-tokens="Anual" value="Anual" >Anual</option>
									<option data-tokens="Bienal" value="Bienal" >Bienal</option>
								</select>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					<div class="col-xs-7 col-md-7 col-lg-7">
						<div class="form-group">
							<label class="control-label col-sm-3" for="tVigencia">Tempo de Vigência do Contrato: </label>
							<div class="col-sm-3">
								<div class='input-group'>
									<input disabled id="tVigencia" name="tVigencia" class="form-control" />
									<span class='input-group-addon' >Meses</span>
								</div>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					
					<div class="col-xs-12 col-md-12 col-lg-12">
						<div class="col-xs-offset-1">
							<div><h4><u>Serviços e Valores da Proposta</u></h4></div>
                            <div class='col-xs-12 col-md-12 col-lg-12'>
                                <div class='col-xs-4 col-sm-4 col-md-4'>
                                    <div class='form-group'>
                                        <label class='control-label col-sm-3' for='Evento1'>Serviço:</label>
                                        <div class='col-sm-8'>
                                            <input type='text' name='Evento1' id='Evento1' class='form-control' >
                                        </div>
                                    </div>
                                </div>
                                <div class='col-xs-4 col-sm-4 col-md-4'>
                                    <div class='form-group'>
                                        <label class='control-label col-sm-2' for='valor1'>Valores:</label>
                                        <div class='col-sm-8'>
                                            <div class='input-group'>
                                                <span class='input-group-addon' id='real'>R$</span>
                                                <input type='text' class='form-control' id='valor1' name='valor1' placeholder='0,00' aria-describedby='real' value='0,00'  />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<script type="text/javascript">
								//Escrevendo o código-fonte HTML e ocultando os campos criados:
								for (iLoop = 2; iLoop <= totalCampos; iLoop++) {
									document.write("<div class='col-xs-12 col-md-12 col-lg-12' id='linha"+iLoop+"' style='display:none'><div class='col-xs-4 col-sm-4 col-md-4'><div class='form-group'><label class='control-label col-sm-3' for='Evento"+iLoop+"'>Serviço:</label><div class='col-sm-8'><input type='text' name='Evento"+iLoop+"' id='Evento"+iLoop+"' class='form-control'></div></div></div><div class='col-xs-4 col-sm-4 col-md-4'><div class='form-group'><label class='control-label col-sm-2' for='valor"+iLoop+"'>Valores:</label><div class='col-sm-8'><div class='input-group'><span class='input-group-addon' id='real'>R$</span><input type='text' class='form-control' id='valor"+iLoop+"' name='valor"+iLoop+"' placeholder='0,00' aria-describedby='real' value='0,00' /></div></div></div></div><div class='col-xs-2 col-sm-2 col-md-2'><button type='button' class='btn btn-danger' title='Remover Campos' onclick='RemoverCampos(\""+iLoop+"\")'>-</button></div></div>");
								}
							</script>
							<div class="col-lg-12 col-xs-12 col-md-12">
								<div class="col-xs-offset-2 col-lg-2 col-lg-offset-2 col-xs-2 col-md-offset-2 col-md-2">
									<button type="button" title="Adicionar Campos" class="btn btn-default" onclick="AddCampos()">+</button>
									<input type="hidden" name="hidden1" id="hidden1">
									<input type="hidden" name="hidden2" id="hidden2">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-lg-12 col-md-12"> 
				</div>

				<div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-2 col-md-offset-2 col-lg-offset-2">
					<input type="hidden" value="Proposta" name="Cliente" />
					<input type="hidden" value="" name="CodContratante" id="CodContratante" />
					<button class="btn btn-success" type="submit">Enviar</button>
					<button class="btn btn-warning" type="reset">Cancelar</button>
				</div>
				<div class="col-xs-12 col-lg-12 col-md-12"> 
				</div>
			</form>
		</div>
	</div>
</div>
<div class="col-xs-12 col-lg-12 col-md-12">

</div>
<?php
require_once("../control/arquivo/footer/Footer.php");
endif;
?>