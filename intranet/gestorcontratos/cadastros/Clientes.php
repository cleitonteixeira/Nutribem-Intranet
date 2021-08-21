<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location: ".BASE);
else:
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
?>
<!-- Content -->
<script type="text/javascript">
	/* Máscaras ER */
	function mascara(o,f){
		v_obj=o
		v_fun=f
		setTimeout("execmascara()",1)
	}
	function execmascara(){
		v_obj.value=v_fun(v_obj.value)
	}
	function mtel(v){
		v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito
		v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
		v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos
		return v;
	}
	function id( el ){
		return document.getElementById( el );
	}
	window.onload = function(){
		id('tFinanceiro').onkeypress = function(){
			mascara( this, mtel );
		}
		id('tComercial').onkeypress = function(){
			mascara( this, mtel );
		}
	}
	function SomenteNumero(e){
		var tecla=(window.event)?event.keyCode:e.which;   
		if((tecla>47 && tecla<58)) return true;
		else{
			if (tecla==8 || tecla==0) return true;
			else  return false;
		}
	}
	$(document).ready(function(){
		$("input[name='sNumero']").click(function(e) {
			if($("input[name='sNumero']").prop("checked")){
				$('#numero').prop('readonly',true);
				$('#numero').val("S/N");
			}else{
				$('#numero').prop('readonly',false);
				$('#numero').attr('required','required');
			}
		})
		$("input[name='cNumero']").click(function(e) {
			if($("input[name='cNumero']").prop("checked")){
				$('#nCobranca').prop('readonly',true);
				$('#nCobranca').val("S/N");
			}else{
				$('#nCobranca').prop('readonly',false);
				$('#nCobranca').attr('required','required');
			}
		})
		
		$("input[name='cobranca']").change(function(e){
			if($("input[name='cobranca']").prop("checked")){
				if($('#numero').val() != "" && $('#bairro').val() != "" && $('#logradouro').val() != "" && $('#cidade').val() != "" && $('#uf').val() != "" && $('#cep').val() != ""){
					var numero 		= $('#numero').val();
					var bairro 		= $('#bairro').val();
					var logradouro 	= $('#logradouro').val();
					var cidade		= $('#cidade').val();
					var uf			= $('#uf').val();
					var cep			= $('#cep').val();
					$('#nCobranca').val(numero);
					$('#nCobranca').prop("readonly",true);
					$('#bCobranca').val(bairro);
					$('#bCobranca').prop("readonly",true);
					$('#lCobranca').val(logradouro);
					$('#lCobranca').prop("readonly",true);
					$('#cCobranca').val(cidade);
					$('#cCobranca').prop("readonly",true);
					$('#uCobranca').val(uf);
					$('#uCobranca').prop("readonly",true);
					$('#ceCobranca').val(cep);
					$('#ceCobranca').prop("readonly",true);
					$('#uCobranca').find('[value="'+uf+'"]').attr('selected', true);
					$('#uCobranca').prop("disabled",true);
					$('#uCobrancaH').val(uf);
					$('#uCobrancaH').prop("disabled",false);
					$('#cNumero').prop("checked",true);
					$('#cNumero').prop("readonly",true);
				}else{
					$("#eEndereco").html('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;	</a><p><strong>Error!</strong> Preencha todos os campo de endereço.</p></div>');
					setTimeout(function() {
						$("input[name='cobranca']").prop("checked", false);
					}, 750);
					return false;
				}
			}else{
				$('#cNumero').prop("checked",false);
				$('#nCobranca').val("");
				$('#bCobranca').val("");
				$('#lCobranca').val("");
				$('#cCobranca').val("");
				$('#ceCobranca').val("");
				$('#uCobranca').prop('selectedIndex',0);
				$('#nCobranca').prop("readonly",false);
				$('#bCobranca').prop("readonly",false);
				$('#lCobranca').prop("readonly",false);
				$('#cCobranca').prop("readonly",false);
				$('#ceCobranca').prop("readonly",false);
				$('#uCobranca').prop("disabled",false);
				$('#uCobrancaH').prop("disabled",true);
				$('#uCobrancaH').val("");
			}
		})
		$("input[name='cFinanceiro']").change(function(e){
			if($("input[name='cFinanceiro']").prop("checked")){
				if($('#rComercial').val() != "" && $('#eComercial').val() != "" && $('#rComercial').val() != ""){
					var rComercial	= $('#rComercial').val();
					var eComercial	= $('#eComercial').val();
					var tComercial	= $('#tComercial').val();
					$('#rFinanceiro').val(rComercial);
					$('#rFinanceiro').prop("readonly",true);
					$('#eFinanceiro').val(eComercial);
					$('#eFinanceiro').prop("readonly",true);
					$('#tFinanceiro').val(tComercial);
					$('#tFinanceiro').prop("readonly",true);
				}else{
					$("#jFinanceiro").html('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;	</a><p><strong>Error!</strong> Preencha todos os campo de contato.</p></div>');
					setTimeout(function() {
						$("input[name='cFinanceiro']").prop("checked", false);
					}, 750);
					return false;
				}
			}else{
				$('#rFinanceiro').val("");
				$('#rFinanceiro').prop("readonly",false);
				$('#eFinanceiro').val("");
				$('#eFinanceiro').prop("readonly",false);
				$('#tFinanceiro').val("");
				$('#tFinanceiro').prop("readonly",false);
			}
		})
	});
</script>
<script type="text/javascript">$(document).ready(function(){	$("#cnpj").mask("99.999.999/9999-99");});</script>
<script type="text/javascript">$(document).ready(function(){	$("#cep").mask("99.999-999");});</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<h2 class="text-center">Cadastro Cliente</h2>
			<form name="Form" role="form" action="<?php echo BASE; ?>control/banco/ClienteDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<p><small>Dados Iniciais</small></p>
					<div class="col-xs-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label class="col-sm-3 control-label" for="razao">Razão Social:</label>
							<div class="col-sm-9">
								<input autofocus type="text" maxlength="50" name="razao" id="razao" required class="form-control" placeholder="Ex.: NUTRIBEM REFEIÇÕES EIRELI-EPP" />
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="ie">IE:</label>
							<div class="col-sm-9">
								<input type="text" name="ie" id="ie" class="form-control" />
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="cpnj">CNPJ:</label>
							<div class="col-sm-6">
								<input required type="text" name="cnpj" id="cnpj" class="form-control" placeholder="Ex.: 99.999.999/0001-99" />
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<hr />
					<p><small>Dados de Contato</small></p>
					<div>
						<h4 class="col-xs-offset-1 col-md-offset-1 col-lg-offset-1 text-left">Comercial</h4>
						<div class="col-xs-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="rComercial">Responsável:</label>
								<div class="col-sm-9">
									<input type="text" name="rComercial" id="rComercial" required class="form-control" placeholder="Ex.: Virgílio Faria"/>
									<div class="help-block with-errors"></div>
								</div>
							</div>
						</div>
						<div class="col-xs-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="eComercial">E-mail:</label>
								<div class="col-sm-9">
									<input type="text" name="eComercial" id="eComercial" required class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" placeholder="Ex.: financeiro@email.com"/>
									<div class="help-block with-errors"></div>
								</div>
							</div>
						</div>
						<div class="col-xs-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="tComercial">Telefone:</label>
								<div class="col-sm-9">
									<input type="text" name="tComercial" id="tComercial" maxlength="15" required class="form-control" placeholder="Ex.: (99) 9999-9999"/>
									<div class="help-block with-errors"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-12 col-lg-12">
						<div class="form-group">
							<label class="control-label col-sm-4" for="cFinanceiro">Contato Financeiro igual ao comercial: </label>
							<div class="col-sm-1">
								<label class="switch">
								  <input name="cFinanceiro" id="cFinanceiro" type="checkbox" value="Sim">
								  <span class="slider round"></span>
								</label>
							</div>
							<div class="col-sm-5" id="jFinanceiro"></div>
						</div>
					</div>
					<div>
						<h4 class="col-xs-offset-1 col-md-offset-1 col-lg-offset-1 text-left">Financeiro</h4>
						<div class="col-xs-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="rFinanceiro">Responsável:</label>
								<div class="col-sm-9">
									<input type="text" name="rFinanceiro" id="rFinanceiro" required class="form-control" placeholder="Ex.: Virgílio Faria"/>
									<div class="help-block with-errors"></div>
								</div>
							</div>
						</div>
						<div class="col-xs-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="eFinanceiro">E-mail:</label>
								<div class="col-sm-9">
									<input type="text" name="eFinanceiro" id="eFinanceiro" required class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" placeholder="Ex.: financeiro@email.com"/>
									<div class="help-block with-errors"></div>
								</div>
							</div>
						</div>
						<div class="col-xs-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="tFinanceiro">Telefone:</label>
								<div class="col-sm-9">
									<input type="text" name="tFinanceiro" id="tFinanceiro" maxlength="15" required class="form-control" placeholder="Ex.: (99) 9999-9999"/>
									<div class="help-block with-errors"></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xs-12 col-md-12 col-lg-12">
					<hr />
					<p><small>Dados de Endereço</small></p>
					<div class="col-xs-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label class="col-sm-3 control-label" for="logradouro">Logradouro:</label>
							<div class="col-sm-9">
								<input type="text" name="logradouro" id="logradouro" required class="form-control" placeholder="Ex.: Rua do Brasil" />
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="cep">CEP:</label>
							<div class="col-sm-9">
								<input type="text" name="cep" id="cep" required class="form-control" placeholder="Ex.: 99.999-999" onkeypress="return SomenteNumero(event)"/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="uf">UF:</label>
							<div class="col-sm-3">
								<select class="selectpicker form-control" title="Selecione uma UF" name="uf" id="uf" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma UF." required>
									<option data-tokens="Acre AC" value="AC">Acre - AC</option>
									<option data-tokens="Alagoas AL" value="AL">Alagoas - AL</option>
									<option data-tokens="Amapá AP" value="AP">Amapá - AP</option>
									<option data-tokens="Amazonas AM" value="AM">Amazonas - AM</option>
									<option data-tokens="Bahia BA" value="BA">Bahia - BA</option>
									<option data-tokens="Ceará CE" value="CE">Ceará - CE</option>
									<option data-tokens="Distrito Federal DF" value="DF">Distrito Federal - DF</option>
									<option data-tokens="Espirito Santo ES" value="ES">Espirito Santo - ES</option>
									<option data-tokens="Goiás GO" value="GO">Goiás - GO</option>
									<option data-tokens="Maranhão MA" value="MA">Maranhão - MA</option>
									<option data-tokens="Mato Grosso MT" value="MT">Mato Grosso - MT</option>
									<option data-tokens="Mato Grosso do Sul MS" value="MS">Mato Grosso do Sul - MS</option>
									<option data-tokens="Minas Gerais MG" value="MG">Minas Gerais - MG</option>
									<option data-tokens="Pará PA" value="PA">Pará - PA</option>
									<option data-tokens="Paraíba PB" value="PB">Paraíba - PB</option>
									<option data-tokens="Paraná PR" value="PR">Paraná - PR</option>
									<option data-tokens="Pernabuco PE" value="PE">Pernabuco - PE</option>
									<option data-tokens="Piauí PI" value="PI">Piauí - PI</option>
									<option data-tokens="Rio de Janeiro RJ" value="RJ">Rio de Janeiro - RJ</option>
									<option data-tokens="Rio Grande do Norte RN" value="RN">Rio Grande do Norte - RN</option>
									<option data-tokens="Rio Grande do Sul RS" value="RS">Rio Grande do Sul - RS</option>
									<option data-tokens="Rondônia RS" value="RO">Rondônia - RS</option>
									<option data-tokens="Roraima RR" value="RR">Roraima - RR</option>
									<option data-tokens="Santa Catarina SC" value="SC">Santa Catarina - SC</option>
									<option data-tokens="São Paulo SP" value="SP">São Paulo - SP</option>
									<option data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
									<option data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
								</select>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="numero">Número:</label>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" name="numero" id="numero" required class="form-control" placeholder="Ex.: 999" onkeypress="return SomenteNumero(event)" />
									<span class="input-group-addon">
										<input type="checkbox" name="sNumero" id="sNumero" value="S/N" /> S/N
									</span>
								</div>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="cidade">Cidade:</label>
							<div class="col-sm-9">
								<input type="text" name="cidade" id="cidade" required class="form-control" placeholder="Ex.: Paracatu"  />
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="bairro">Bairro:</label>
							<div class="col-sm-9">
								<input type="text" name="bairro" id="bairro" required class="form-control" placeholder="Ex.: Centro"  />
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-lg-12 col-md-12">
					<div class="form-group">
						<label class="control-label col-sm-3" for="cobranca">Endereço de Cobrança igual: </label>
						<div class="col-sm-3">
							<label class="switch">
							  <input name="cobranca" id="cobranca" type="checkbox" value="Sim">
							  <span class="slider round"></span>
							</label>
						</div>
						<div class="col-sm-5" id="eEndereco"></div>
					</div>
					
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<p><small>Endereço de Cobrança</small></p>
					<div class="col-xs-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label class="col-sm-3 control-label" for="lCobranca">Logradouro:</label>
							<div class="col-sm-9">
								<input type="text" name="lCobranca" id="lCobranca" required class="form-control" placeholder="Ex.: Rua do Brasil" />
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="ceCobranca">CEP:</label>
							<div class="col-sm-9">
								<input type="text" name="ceCobranca" id="ceCobranca" required class="form-control" placeholder="Ex.: 99.999-999" onkeypress="return SomenteNumero(event)"/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="uCobranca">UF:</label>
							<div class="col-sm-5">
								<select class="form-control" title="Selecione uma UF" name="uCobranca" id="uCobranca" data-live-search="true" data-width="fit" data-size="5" required>
									<option data-tokens="" value="" selected>Selecione uma UF</option>
									<option data-tokens="Acre AC" value="AC">Acre - AC</option>
									<option data-tokens="Alagoas AL" value="AL">Alagoas - AL</option>
									<option data-tokens="Amapá AP" value="AP">Amapá - AP</option>
									<option data-tokens="Amazonas AM" value="AM">Amazonas - AM</option>
									<option data-tokens="Bahia BA" value="BA">Bahia - BA</option>
									<option data-tokens="Ceará CE" value="CE">Ceará - CE</option>
									<option data-tokens="Distrito Federal DF" value="DF">Distrito Federal - DF</option>
									<option data-tokens="Espirito Santo ES" value="ES">Espirito Santo - ES</option>
									<option data-tokens="Goiás GO" value="GO">Goiás - GO</option>
									<option data-tokens="Maranhão MA" value="MA">Maranhão - MA</option>
									<option data-tokens="Mato Grosso MT" value="MT">Mato Grosso - MT</option>
									<option data-tokens="Mato Grosso do Sul MS" value="MS">Mato Grosso do Sul - MS</option>
									<option data-tokens="Minas Gerais MG" value="MG">Minas Gerais - MG</option>
									<option data-tokens="Pará PA" value="PA">Pará - PA</option>
									<option data-tokens="Paraíba PB" value="PB">Paraíba - PB</option>
									<option data-tokens="Paraná PR" value="PR">Paraná - PR</option>
									<option data-tokens="Pernabuco PE" value="PE">Pernabuco - PE</option>
									<option data-tokens="Piauí PI" value="PI">Piauí - PI</option>
									<option data-tokens="Rio de Janeiro RJ" value="RJ">Rio de Janeiro - RJ</option>
									<option data-tokens="Rio Grande do Norte RN" value="RN">Rio Grande do Norte - RN</option>
									<option data-tokens="Rio Grande do Sul RS" value="RS">Rio Grande do Sul - RS</option>
									<option data-tokens="Rondônia RS" value="RO">Rondônia - RS</option>
									<option data-tokens="Roraima RR" value="RR">Roraima - RR</option>
									<option data-tokens="Santa Catarina SC" value="SC">Santa Catarina - SC</option>
									<option data-tokens="São Paulo SP" value="SP">São Paulo - SP</option>
									<option data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
									<option data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
								</select>
								<div class="help-block with-errors"></div>
								<input type="hidden" name="uCobrancaH" id="uCobrancaH" />
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="nCobranca">Número:</label>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" name="nCobranca" id="nCobranca" required class="form-control" placeholder="Ex.: 999" onkeypress="return SomenteNumero(event)" />
									<span class="input-group-addon">
										<input type="checkbox" name="cNumero" id="cNumero" value="S/N" /> S/N
									</span>
								</div>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="cCobranca">Cidade:</label>
							<div class="col-sm-9">
								<input type="text" name="cCobranca" id="cCobranca" required class="form-control" placeholder="Ex.: Paracatu"  />
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="bCobranca">Bairro:</label>
							<div class="col-sm-9">
								<input type="text" name="bCobranca" id="bCobranca" required class="form-control" placeholder="Ex.: Centro"  />
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-offset-2 col-md-offset-2 col-lg-offset-2 col-xs-4 col-md-4 col-lg-4 ">
					<input type="hidden" name="Cliente" value="Cadastro" />
					<button class="btn btn-success" type="submit">Salvar</button>
					<button class="btn btn-danger" type="reset">Cancelar</button>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12"> </div>
			</form>
		</div>
	</div>
</div>
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>