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


function buscaSuperior( $control ){
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE Superior = ?;";
    if ($control->rowCount() > 0){
        $superior = $control->fetchAll(PDO::FETCH_OBJ);
        foreach($superior as $x){
            array_unique($_SESSION['idChefia']);
            array_push($_SESSION['idChefia'], $x->idusuarios);
        }
        foreach($superior as $s){
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $s->idusuarios);
            $stmt->execute();
            $control = $stmt;
            buscaSuperior( $control );
        }
    }
}
$a = array(1,3,14,16,4,5);
if(in_array($_SESSION['idusuarios'], $a)){
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE idusuarios != ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $_SESSION['idusuarios']);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
    $chefia = $_SESSION['idusuarios'];
    foreach( $resultado as $rest ){
        $chefia .= ", ". $rest->idusuarios;
    }
    $chefia;
}else{
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE Superior = ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $_SESSION['idusuarios']);
    $stmt->execute();
    $_SESSION['idChefia'] = array();
    $controlador = $stmt;
    buscaSuperior( $controlador );
    sort($_SESSION['idChefia']);
    $resultado = $_SESSION['idChefia'];
    unset($_SESSION['idChefia']);
    $chefia = $_SESSION['idusuarios'];
    foreach( $resultado as $rest ){
        $chefia .= ", ". $rest;
    }
    $chefia;
}
?>

<script type="text/javascript">
	$(document).ready(function(){
		$("select[name='colaborador']").change(function(e){
			var cod = $('#colaborador').val();//pegando o value do option selecionado
			//alert(cod);//apenas para debugar a variável

			$.getJSON('Completa.inc.php?cargo='+cod, function (dados){
				//alert(dados);
				if (dados.length == 1){ 	
					var Colaborador = '';
					var dAdmissao = '';
					var Empresa = '';
					var CNPJ = '';
					var CodColaborador = '';
					$.each(dados, function(i, obj){
						Colaborador = obj.Colaborador;
						dAdmissao = obj.dAdmissao;
						Empresa = obj.Empresa;
						CNPJ = obj.CNPJ;
						CodColaborador = obj.CodColaborador;
						iPeriodo = obj.iPeriodo;
						fPeriodo = obj.fPeriodo;
						Cargo = obj.Cargo;
					})
					$('#Colaborador').html(Colaborador).show();
					$('#dAdmissao').html(dAdmissao).show();
					$('#Empresa').html(Empresa).show();
					$('#CNPJ').html(CNPJ).show();
					$('#CodColaborador').html(CodColaborador).show();
					$('#Cargo').html(Cargo).show();

					$('#dCargo').val(Cargo).show();
					$('#Nome').val(Colaborador).show;
					$('#admissao').val(dAdmissao).show;
					$('#Empregador').val(Empresa).show;
					$('#cnpj').val(CNPJ).show;
					$('#Registro').val(CodColaborador).show;

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

		$("input[name='tContrato']").click(function(e){
			var t = $("input[name='tContrato']:checked").val();
			//alert(t);
			if (t == "Temporário"){
				$("#periodo").removeAttr("disabled");
				$("#periodo").attr("required","required");
			}else{
				$("#periodo").attr("disabled",true);
				$("#periodo").removeAttr("required");
			}
		})
		$("input[name='exame']").click(function(e){
			var t = $("input[name='exame']:checked").val();
			//alert(t);
			if (t == "Sim"){
				$("#dExame").removeAttr("disabled");
				$("#dExame").attr("required","required");
			}else{
				$("#dExame").attr("disabled",true);
				$("#dExame").removeAttr("required");
			}
		})
		$("input[name='doenca']").click(function(e){
			var t = $("input[name='doenca']:checked").val();
			//alert(t);
			if (t == "Sim"){
				$("#dDoenca").removeAttr("disabled");
				$("#dDoenca").attr("required","required");
			}else{
				$("#dDoenca").attr("disabled",true);
				$("#dDoenca").removeAttr("required");
			}
		})
	});
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<div class="text-center"><h2>Análise de Desligamento</h2></div>
			<form action="<?php echo BASE."control/banco/DemissaoDAO.php"?>" method="post" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator" >
			    <div class="form-group">
					<label for="colaborador" class="col-sm-2 control-label">Colaborador: </label>
					<div class="col-sm-6">
						<select autofocus class="selectpicker form-control" width="fit" data-size="5" data-error="Selecione uma Unidade." required title="Selecione um Colaborador!" name="colaborador" id="colaborador" data-live-search="true">
							<?php
							$conexao = conexao::getInstance();
							$sql = "SELECT col.idColaborador, col.CodColaborador, con.dAdmissao, cad.Nome, cad.CPF,(SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER  JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ  FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = con.Unidade_idUnidade WHERE un.idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario IN (".$chefia.")) AND con.dDemissao IS NULL;";
							$stm = $conexao->prepare($sql);
							//$stm->bindParam(1, $_SESSION['idusuarios']);
							$stm->execute();
							$col = $stm->fetchAll(PDO::FETCH_OBJ);
							foreach($col as $c):
							?>
							<option value="<?php echo $c->idColaborador?>" data-tokens="<?php echo utf8_decode($c->CPF); echo $c->Nome; echo $c->CodColaborador ?>" data-subtext="<?php echo "CPF: ".CPF_Padrao(str_pad($c->CPF,11,0,STR_PAD_LEFT)); ?>"><?php echo $c->CodColaborador." - ".utf8_decode($c->Nome); ?></option>
							<?php 
							endforeach;
							?>
						</select>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="text-justify col-xs-12">
						<p><strong>Empregador: </strong><span id="Empresa"></span>.</p>
						<p><strong>CNPJ nº: </strong><span id="CNPJ"></span>.</p>
					</div>
				</div>
				<hr>
				<div class="text-justify col-xs-12">
					<p><strong>Nome do Funcionário: </strong><span id="CodColaborador"></span> - <span id="Colaborador"></span>&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;<strong>Cargo: </strong><span id="Cargo"></span></p>
					<p><strong>Data de Admissão: </strong><span id="dAdmissao"></span>.
					</p>
				</div>
				<hr>
				<div class="col-xs-12">
					<h3 class="text-center"><u>Formulário</u></h3>
					<div class="col-xs-12"><h4><u>Dados da Recisão:</u></h4></div>
					<!-- Divisão de formulário -->
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label col-sm-4" for="dDemissao">Data de Demissão:</label>
							<div class="col-sm-6">
								<input type="date" id="dDemissao" name="dDemissao" class="form-control" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<!-- Divisão de formulário -->
					</div>

					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label col-sm-4" for="tContrato">Tipo de Contrato:</label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" name="tContrato" id="tContrato" value="Efetivo CLT" />Efetivo CLT</label>
								<label class="radio-inline"><input required type="radio" name="tContrato" id="tContrato" value="Estagiário" />Estagiário</label>
								<label class="radio-inline"><input required type="radio" name="tContrato" id="tContrato" value="Temporário" />Temporário</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="periodo">Período:</label>
							<div class="col-sm-4">
								<input type="text" name="periodo" id="periodo" class="form-control" disabled />
								<div class="help-block with-errors"></div>
							</div>
						</div>

					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label col-sm-6" for="tDesligamento">Motivo do Afastamento: </label>
							<div class="col-sm-10">
								<div class="radio">
									<label><input type="radio" name="tDesligamento" required value="Demitido sem justa causa">Demitido sem justa causa</label>
								</div>
								<div class="radio">
									<label><input type="radio" name="tDesligamento" required value="Pedido de demissão ">Pedido de demissão </label>
								</div>
								<div class="radio">
									<label><input type="radio" name="tDesligamento" required value="Rescisão contrato de experiência pelo EMPREGADOR">Rescisão contrato de experiência pelo EMPREGADOR</label>
								</div>
								<div class="radio">
									<label><input type="radio" name="tDesligamento" required value="Rescisão contrato de experiência pelo EMPREGADO">Rescisão contrato de experiência pelo EMPREGADO</label>
								</div>
								<div class="radio">
									<label><input type="radio" name="tDesligamento" required value="Término de contrato de experiência no prazo">Término de contrato de experiência no prazo</label>
								</div>

								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label col-sm-6" for="avisoPrevio">Aviso Prévio: </label>
							<div class="col-sm-10">
								<label class="radio-inline"><input type="radio" name="avisoPrevio" required value="Indenizado">Indenizado</label>
								<label class="radio-inline"><input type="radio" name="avisoPrevio" required value="Cumprido">Cumprido</label>
								<label class="radio-inline"><input type="radio" name="avisoPrevio" required value="Pedido de Dispensa">Pedido de Dispensa</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-goup">
							<label class="control-label col-sm-4">Data do Aviso: </label>
							<div class="col-sm-8">
								<input type="date" name="dataAviso" required class="form-control">
							</div>
						</div>
					</div>
					<!-- Divisão de formulário -->
					<div class="col-xs-12"><h4><u>Justificativa:</u></h4></div>
					<div class="col-xs-12">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="justificativa">Justificativa/Motivo da Recisão:</label>
							<div class="col-sm-8">
								<textarea maxlength="250" minlength="100" name="justificativa" id="justificativa" class="form-control" required></textarea>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					<div class="col-xs-12"><h4><u>Medicina:</u></h4></div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="exame">Há algum exame específico:</label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" name="exame" id="exame" value="Sim" required> Sim</label>
								<label class="radio-inline"><input required type="radio" name="exame" id="exame" value="Não" required> Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="dExame">Qual:</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="dExame" id="dExame" disabled />
							</div>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="col-sm-12 control-label" for="doenca">Há algum Impedimento para o Desligamento? <small>(Doença ou afastamentos):</small></label>
							<div class="col-sm-offset-4 col-sm-6">
								<label class="radio-inline"><input required type="radio" name="doenca" id="exame" value="Sim" required> Sim</label>
								<label class="radio-inline"><input required required type="radio" name="doenca" id="exame" value="Não" required> Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="dDoenca">Qual:</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="dDoenca" id="dDoenca" disabled />
							</div>
						</div>
					</div>
					<div class="col-xs-12"><h4><u>Recursos Humanos:</u></h4></div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label col-sm-4" for="advSusp">Houve advertência(s) ou suspenção(ões):</label>
							<div class="col-sm-4">
								<label class="radio-inline"><input required type="radio" required name="advSusp" id="advSusp" value="Sim"/>Sim</label>
								<label class="radio-inline"><input required type="radio" required name="advSusp" id="advSusp" value="Não"/>Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label col-sm-4" for="promo">Houve promoção ou aumento de salários nos últimos 3 meses?</label>
							<div class="col-sm-4">
								<label class="radio-inline"><input required type="radio" required name="promo" id="promo" value="Sim"/>Sim</label>
								<label class="radio-inline"><input required type="radio" required name="promo" id="promo" value="Não"/>Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					<div class="col-xs-12"><h5><u>Bens de posse da empresa:</u></h5></div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label col-sm-4" for="computador">Computador/Notebook: </label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" name="computador" id="computador" value="Sim"/>Sim</label>
								<label class="radio-inline"><input required type="radio" name="computador" id="computador" value="Não"/>Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="veiculo">Veículo: </label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" name="veiculo" id="veiculo" value="Sim"/>Sim</label>
								<label class="radio-inline"><input required type="radio" name="veiculo" id="veiculo" value="Não"/>Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="estacionamento">Estacionamento: </label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" name="estacionamento" id="estacionamento" value="Sim"/>Sim</label>
								<label class="radio-inline"><input required type="radio" name="estacionamento" id="estacionamento" value="Não"/>Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="outros">Outros: </label>
							<div class="col-sm-8">
								<textarea class="form-control" name="outros" id="outros"></textarea>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label col-sm-4" for="ramal">Ramal: </label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" name="ramal" id="ramal" value="Sim"/>Sim</label>
								<label class="radio-inline"><input required type="radio" name="ramal" id="ramal" value="Não"/>Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="celular">Celular: </label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" name="celular" id="celular" value="Sim"/>Sim</label>
								<label class="radio-inline"><input required type="radio" name="celular" id="celular" value="Não"/>Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="email">E-mail: </label>
							<div class="col-sm-8">
								<label class="radio-inline"><input required type="radio" name="email" id="email" value="Sim"/>Sim</label>
								<label class="radio-inline"><input required type="radio" name="email" id="email" value="Não"/>Não</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
					</div>
				</div>

				<input type="hidden" name="dCargo" id="dCargo" value="">
				<input type="hidden" name="Nome" id="Nome" value="">
				<input type="hidden" name="Empregador" id="Empregador" value="">
				<input type="hidden" name="cnpj" id="cnpj" value="">
				<input type="hidden" name="Registro" id="Registro" value="">
				<input type="hidden" name="admissao" id="admissao" value="">
				<div class="col-xs-offset-2 col-xs-4"><button type="submit" class="btn btn-success envio">Solicitar Avaliação</button></div>
			</form>
		</div>
	</div>
</div>
<?php
	endif;
				  require_once("../control/arquivo/footer/Footer.php");
?>