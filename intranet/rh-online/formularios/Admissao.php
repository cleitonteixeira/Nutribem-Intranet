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
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("select[name='colaborador']").change(function(e){
			var cod = $('#colaborador').val();//pegando o value do option selecionado
			//alert(cod);//apenas para debugar a variável

			$.getJSON('Completa.inc.php?cargo='+cod, function (dados){
				//alert(dados);
				if (dados.length == 1){ 	

					var Empresa = '';
					var CNPJ = '';
					var CodColaborador = '';

					$.each(dados, function(i, obj){
						Empresa = obj.Empresa;
						CNPJ = obj.CNPJ;
						CodColaborador = obj.CodColaborador;
					})

					$('#Colaborador').html(Colaborador).show();
					$('#dAdmissao').html(dAdmissao).show();
					$('#Empresa').html(Empresa).show();
					$('#CNPJ').html(CNPJ).show();
					$('#CodColaborador').html(CodColaborador).show();
					$('#Cargo').html(Cargo).show();

					$('#dCargo').val(Cargo).show();
					$('#Nome').val(Colaborador).show();
					$('#aadmissao').val(dAdmissao).show();
					$('#Empregador').val(Empresa).show();
					$('#cnpj').val(CNPJ).show();
					$('#Registro').val(CodColaborador).show();

				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#dCargo').html("").show();
			$('#Colaborador').html("").show();
			$('#dAdmissao').html("").show();
			$('#Empresa').html("").show();
			$('#CNPJ').html("").show();
			$('#CodColaborador').html("").show();
		}
		$("select[name='subs']").change(function(e){
			var cod = $('#subs').val();//pegando o value do option selecionado
			//alert(cod);//apenas para debugar a variável

			$.getJSON('Completa.inc.php?ca='+cod, function (dados){
				//alert(dados);
				if (dados.length > 0){     
					var cargo = '';
					var salario = '';
					var demissao = '';
					var admissao = '';
					$.each(dados, function(i, obj){
						cargo = obj.Cargo;
						salario = obj.Salario;
						demissao = obj.Demissao;
						admissao = obj.Admissao;
						//alert(option);
					})
					$('#cargoSub').val(cargo).show();
					$('#salarioSub').val(salario).show();
					$('#demissao').val(demissao).show();
					$('#admissao').val(admissao).show();
				}else{
					Reset2();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset2(){
			$('#cargoSub').val("").show();
			$('#salarioSub').val("").show();
			$('#demissao').val("").show();
			$('#admissao').val("").show();
		}

		$("input[name='motivo']").click(function(e) {
			var m = $("input[name='motivo']:checked").val();
			if (m === "Substituição") {
				$("#substituicao").css({
					display: 'block'
				});
				$("#aumento").css({
					display: 'none'
				});
				$("#subs").attr("required", "required");

				$('#justificativa').removeAttr("required", "required");
				$('#justificativa').val("").show();
				$("#cargoS").removeAttr("required", "required");
				$('#cargoS').val("").change();
			}else{
				$("#substituicao").css({
					display: 'none'
				});
				$("#aumento").css({
					display: 'block'
				});
				$("#justificativa").attr("required", "required");
				$("#cargoS").attr("required", "required");
				
				$("#subs").removeAttr("required", "required");
				$('#cargoSub').val("").show();
				$('#salarioSub').val("").show();
				$('#demissao').val("").show();
				$('#admissao').val("").show();
				$('#subs').val("").change();

			}
		})
	});
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 conteudo">
			<div class="col-xs-12"><h3 class="text-center">Formulário de Requisição de Pessoal</h3></div>

			<form action="<?php echo BASE?>control/banco/AdmissaoDAO.php" method="post" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator" target="_blank">
				<div class="col-xs-12">
					<div class="row">
						<h3 class="text-center"><u>Formulário</u></h3>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="col-xs-6">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="tipoVaga">Tipo da Vaga:</label>
									<div class="col-sm-8">
										<label class="radio-inline"><input required type="radio" id="tipoVaga" name="tipoVaga" value="Confiança">Confiança</label>
										<label class="radio-inline"><input required type="radio" id="tipoVaga" name="tipoVaga" value="Operacional">Operacional</label>
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="dataPev">Data Prevista:</label>
									<div class="col-sm-4">
										<input required type="date" name="dataPev" id="dataPev" class="form-control" />
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="perfilVaga">Perfil da Vaga:</label>
								<div class="col-sm-8">
									<textarea maxlength="250" minlength="100" name="perfilVaga" id="perfilVaga" class="form-control" required placeholder="Descreva a vaga: (Atividades, sexo, faixa etária e experiências complementares)"></textarea>
									<div class="help-block with-errors"></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="orcamento">Está solicitação está prevista em orçamento:</label>
							<div class="col-sm-7">
								<label class="radio-inline"><input required type="radio" name="orcamento" id="orcamento" value="Sim">Sim</label>
								<label class="radio-inline"><input required type="radio" name="orcamento" id="orcamento" value="Não">Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<h4 class="col-sm-10 col-sm-offset-2"><u>Tipo</u>: <small>Aumento de Quadro/Substituição</small></h4>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="motivo">Motivo:</label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" id="motivo" name="motivo" value="Aumento de Quadro">Aumento de Quadro</label>
								<label class="radio-inline"><input required type="radio" id="motivo" name="motivo" value="Substituição">Substituição</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div id="substituicao" class="col-xs-12" style="display: none;">
							<div class="panel panel-default">
								<div class="panel-heading"><p class="panel-title">Substituição</p></div>
								<div class="panel-body">
									<div class="form-group">
										<label class="col-sm-2 control-label" for="subs">Selecione o colaborador: </label>
										<div class="col-sm-6">
											<select class="selectpicker" title="Selecione um Colaborador!" name="subs" id="subs" data-live-search="true" data-size="5" data-width="fit">

												<?php
												$conexao = conexao::getInstance();
												$sql = 'SELECT col.idColaborador, col.CodColaborador, cad.Nome FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao WHERE con.dDemissao IS NOT NULL AND con.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?);';
												$stm = $conexao->prepare($sql);
												$stm->bindParam(1, $_SESSION['idusuarios']);
												$stm->execute();
												while($row = $stm->fetch(PDO::FETCH_OBJ)):
												?>
												<option data-tokens="<?php echo utf8_decode($row->CodColaborador)." ".$row->idColaborador." ".$row->Nome ?>" value="<?php echo $row->idColaborador ?>"><?php echo $row->CodColaborador."-".utf8_decode($row->Nome);?></option>
												<?php endwhile; ?>


											</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<label class="col-sm-2 control-label" for="cargoSub">Cargo: </label>
											<div class="col-sm-4">
												<input class="form-control" type="text" readonly name="cargoSub" id="cargoSub" value="..." />
											</div>
											<label class="col-sm-2 control-label" for="salarioSub">Salário: </label>
											<div class="col-sm-4">
												<input class="form-control" type="text" readonly name="salarioSub" id="salarioSub" value="..." />
											</div>
										</div>
										<br>
										<div class="row">
											<label class="col-sm-2 control-label" for="admissao">Admissão: </label>
											<div class="col-sm-4">
												<input class="form-control" type="date" readonly name="admissao" id="admissao" />
											</div>
											<label class="col-sm-2 control-label" for="demissao">Demissão: </label>
											<div class="col-sm-4">
												<input class="form-control" type="date" readonly name="demissao" id="demissao" value="" />
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="aumento" class="col-xs-12" style="display: none;">
							<div class="panel panel-default">
								<div class="panel-heading"><p class="panel-title">Aumento de Quadro</p></div>
								<div class="panel-body">
									<div class="form-group">
										<label class="col-sm-2 control-label" for="justificativa">Justificativa:</label>
										<div class="col-sm-8">
											<textarea class="form-control" name="justificativa" id="justificativa" placeholder="Justifique-se caso aumento de quadro!"></textarea>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label" for="cargoS">Selecione um cargo:</label>  
										<div class="col-sm-5">
											<select class="selectpicker" title="Selecione uma Cargo." name="cargoS" id="cargoS" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma Cargo.">
												<?php
												$conexao = conexao::getInstance();
												$sql = 'SELECT u.idUnidade, ca.Nome FROM unidade u INNER JOIN cadastro ca ON ca.idCadastro = u.Cadastro_idCadastro WHERE u.idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)';
												$stm = $conexao->prepare($sql);
												$stm->bindParam(1, $_SESSION['idusuarios']);
												$stm->execute();
												$rs = $stm->fetchAll(PDO::FETCH_OBJ);
												foreach($rs as $r):
												?>
												<optgroup label="<?php echo utf8_decode($r->Nome); ?>" >
													<?php
													$sql = 'SELECT ca.* FROM cargo ca INNER JOIN unidade u ON u.idUnidade = ca.Unidade_idUnidade INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro WHERE Unidade_idUnidade = ? ORDER BY ca.Unidade_idUnidade';
													$stm = $conexao->prepare($sql);
													$stm->bindParam(1, $r->idUnidade);
													$stm->execute();
													while($row = $stm->fetch(PDO::FETCH_OBJ)):
													?>
													<option data-subtext="CBO: <?php echo $row->CBO ."  R$ ". number_format($row->Salario,2,',','.') ?>" data-tokens="<?php echo utf8_decode($row->Cargo)." ".$row->CodCargo." ".$row->CBO ?>" value="<?php echo $row->idCargo ?>"><?php echo $row->CodCargo."-".utf8_decode($row->Cargo);?></option>
													<?php endwhile; ?>

												</optgroup>
												<?php endforeach; ?>
											</select>
											<div class="help-block with-errors"></div>
										</div>
										<div class="col-sm-5"></div>
									</div>
								</div>
							</div>
						</div>
					</div>   
				</div>

				<input type="hidden" name="dCargo" id="dCargo" value="">
				<input type="hidden" name="Nome" id="Nome" value="">
				<input type="hidden" name="Empregador" id="Empregador" value="">
				<input type="hidden" name="cnpj" id="cnpj" value="">
				<input type="hidden" name="Registro" id="Registro" value="">
				<div class="col-xs-offset-2 col-xs-4"><button type="submit" class="btn btn-success envio">Solicitar Avaliação</button></div>
			</form>
		</div>
	</div>
</div>
<br />
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>