<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
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
		$("select[name='colaborador']").change(function(e){
			var cod = $('#colaborador').val();//pegando o value do option selecionado
			//alert(cod);//apenas para debugar a variável

			$.getJSON('Completa.inc.php?sub='+cod, function (dados){
				//alert(dados);
				if (dados.length > 0){     
					var option = '<option value="">Selecione!</option>';
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.idColaborador+'">'+obj.CodColaborador+'-'+obj.Nome+'</option>';
						//alert(option);
					})
					$('#subs').html(option).show();
				}else{
					Reset1();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset1(){
			$('#subs').html("").show();
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
		$("select[name='colaborador']").change(function(e){
			var cod = $('#colaborador').val();//pegando o value do option selecionado
			//alert(cod);//apenas para debugar a variável
			$.getJSON('Completa.inc.php?cod='+cod, function (dados){
				//alert(dados);
				if (dados.length > 0){     
					var option = '<option value="">Selecione!</option>';
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.idCargo+'">'+obj.Funcao+'</option>';
						//alert(option);
					})
					$('#cargoP').html(option).show();
				}else{
					Reset1();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset1(){
			$('#cargoP').html("").show();
		}

		$("input[name='motivo']").click(function(e) {
			var m = $("input[name='motivo']:checked").val();
            switch(m) {
                case 'substituicao':
                    $("#substituicao").css({
                        display: 'block'
                    });
                    $("#aQuadro").css({
                        display: 'none'
                    });
                    $("#aSalario").css({
                        display: 'none'
                    });
                    $("#subs").attr("required", "required");
                    
                    $('#justificativa').removeAttr("required", "required");
                    $('#sAumento').removeAttr("required", "required");
                    $('#justificativa1').removeAttr("required", "required");
                    $("#cargoP").removeAttr("required", "required");
                    $('#cargoP').val("").change();
                    $('#justificativa').val("").show();
                    $('#sAumento').val("").show();
                    break;
                case 'aQuadro':
                    $("#substituicao").css({
                        display: 'none'
                    });
                    $("#aQuadro").css({
                        display: 'block'
                    });
                    $("#aSalario").css({
                        display: 'none'
                    });
                    $("#justificativa").attr("required", "required");
                    $("#cargoP").attr("required", "required");
                    
                    $('#justificativa1').removeAttr("required", "required");
                    $('#sAumento').removeAttr("required", "required");
                    $("#subs").removeAttr("required", "required");
                    $('#cargoSub').val("").show();
                    $('#salarioSub').val("").show();
                    $('#demissao').val("").show();
                    $('#admissao').val("").show();
                    $('#subs').val("").change();
                    $('#justificativa1').val("").show();
                    $('#sAumento').val("").show();
                    break;
                case 'aSalario':
                    $("#substituicao").css({
                        display: 'none'
                    });
                    $("#aQuadro").css({
                        display: 'none'
                    });
                    $("#aSalario").css({
                        display: 'block'
                    });
                    $('#justificativa1').attr("required", "required");
                    $('#sAumento').attr("required", "required");
                    $("#sAumento").maskMoney();
                    
                    $("#justificativa").removeAttr("required", "required");
                    $("#subs").removeAttr("required", "required");
                    $('#cargoSub').val("").show();
                    $('#salarioSub').val("").show();
                    $('#demissao').val("").show();
                    $('#admissao').val("").show();
                    $('#subs').val("").change();
                    $("#cargoP").removeAttr("required", "required");
                    $('#cargoP').val("").change();
                    $('#justificativa').val("").show();
                    break;
                default:
                    //code block
                    break;
		      }
        })

	});
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-lg-12 col-xs-12 conteudo">
			<div class="text-center"><h2>Requerimento de Promoção</h2></div>
			<form method="get" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator">
				<div class="form-group">
					<label for="colaborador" class="col-sm-2 control-label">Colaborador:</label>
					<div class="col-sm-6">
						<select autofocus class="selectpicker form-control" width="fit" data-size="5" data-error="Selecione uma Unidade." required title="Selecione um Colaborador!" name="colaborador" id="colaborador" data-live-search="true">
							<?php
							$conexao = conexao::getInstance();
							$sql = "SELECT cco.Nome AS Colaborador, cco.CPF, co.idColaborador, co.CodColaborador, cn.dAdmissao, cem.Nome AS Empresa, cem.CNPJ FROM chefia ch INNER JOIN colaborador co ON co.idColaborador = ch.Colaborador_idColaborador INNER JOIN cadastro cco ON cco.idCadastro = co.Cadastro_idCadastro INNER JOIN contratacao cn ON cn.idContratacao = co.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN empresa em ON em.idEmpresa = un.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro WHERE ch.Usuario_idUsuario IN (".$chefia.") AND cn.dDemissao IS NULL;";
							$stm = $conexao->prepare($sql);
							$stm->execute();
							$col = $stm->fetchAll(PDO::FETCH_OBJ);
							foreach($col as $c):
							?>
							<option value="<?php echo $c->idColaborador?>" data-tokens="<?php echo utf8_decode(str_pad($c->CPF,11,0,STR_PAD_LEFT));echo " "; echo $c->Colaborador;echo " "; echo $c->CodColaborador ?>" data-subtext="<?php echo "CPF: ".CPF_Padrao(str_pad($c->CPF,11,0,STR_PAD_LEFT)); ?>"><?php echo $c->CodColaborador." - ".utf8_decode($c->Colaborador); ?></option>
							<?php 
							endforeach;
							?>
						</select>
					</div>
				</div>
			</form>
            <div class="col-xs-12 col-md-12 col-lg-12"><h4 class="text-center"><u>FORMULÁRIO</u></h4></div>
			<form action="<?php echo BASE?>control/banco/PromocaoDAO.php" method="post" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator" target="_blank">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="text-justify">
						<p><strong>Empregador: </strong><span id="Empresa"></span>.</p>
						<p><strong>CNPJ nº: </strong><span id="CNPJ"></span>.</p>
						<p>
                            <strong>Nome do Funcionário: </strong><span id="CodColaborador"></span> - <span id="Colaborador"></span>
                            <strong class="direita">Cargo: </strong><span id="Cargo"></span></p>
						<p><strong>Data de Admissão: </strong><span id="dAdmissao"></span>.
						</p>
					</div>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
                    <hr/>
					<div class="">
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="tipoVaga">Tipo da Vaga:</label>
                                <div class="col-sm-8">
                                    <label class="radio-inline"><input required type="radio" id="tipoVaga" name="tipoVaga" value="Confiança">Confiança</label>
                                    <label class="radio-inline"><input required type="radio" id="tipoVaga" name="tipoVaga" value="Operacional">Operacional</label>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="dataPev">Data Prevista:</label>
                                <div class="col-sm-4">
                                    <input required type="date" name="dataPev" id="dataPev" class="form-control" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="perfilVaga">Perfil da Vaga:</label>
                                <div class="col-sm-8">
                                    <textarea maxlength="250" minlength="100" name="perfilVaga" id="perfilVaga" class="form-control" required placeholder="Descreva a vaga: (Atividades, sexo, faixa etária e experiências complementares)"></textarea>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="orcamento">Está solicitação está prevista em orçamento:</label>
                                <div class="col-sm-7">
                                    <label class="radio-inline"><input required type="radio" name="orcamento" id="orcamento" value="Sim">Sim</label>
                                    <label class="radio-inline"><input required type="radio" name="orcamento" id="orcamento" value="Não">Não</label>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
						<div class="col-xs-12 col-md-12 col-lg-12"><h4>Tipo: <small>Aumento de Quadro/Substituição/Aumento de Salário</small></h4></div>
						<div class="col-xs-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label class="col-sm-1 control-label text-left" for="motivo">Motivo:</label>
								<div class="col-sm-8">
									<label class="radio-inline"><input required type="radio" id="motivo" name="motivo" value="aQuadro">Aumento de Quadro</label>
									<label class="radio-inline"><input required type="radio" id="motivo" name="motivo" value="substituicao">Substituição</label>
                                    <label class="radio-inline"><input required type="radio" id="motivo" name="motivo" value="aSalario">Aumento de Salário</label>
									<div class="help-block with-errors"></div>
								</div>
							</div>
						</div>
						<div id="substituicao" class="col-xs-12 col-md-12 col-lg-12 substituicao" style="display: none;">
							<div class="panel panel-default">
								<div class="panel-heading"><p class="panel-title">Substituição</p></div>
								<div class="panel-body">
									<div class="form-group">
										<label class="col-sm-2 control-label" for="subs">Selecione o colaborador: </label>
										<div class="col-sm-6">
											<select class="form-control" title="Selecione um Colaborador!" name="subs" id="subs">

											</select>
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
						<div id="aQuadro" class="col-xs-12 col-md-12 col-lg-12 aQuadro" style="display: none;">
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
										<label class="col-sm-2 control-label" for="cargoP">Cargo:</label>  
										<div class="col-sm-6">
											<select class="form-control" title="Selecione um Cargo." name="cargoP" id="cargoP">

											</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
                        <div id="aSalario" class="col-xs-12 col-md-12 col-lg-12 aSalario" style="display: none;">
							<div class="panel panel-default">
								<div class="panel-heading"><p class="panel-title">Aumento de Salário</p></div>
								<div class="panel-body">
									<div class="form-group">
										<label class="col-sm-2 control-label" for="justificativa1">Justificativa:</label>
										<div class="col-sm-8">
											<textarea class="form-control" name="justificativa1" id="justificativa1" placeholder="Justifique-se!"></textarea>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label" for="sAumento">Salário:</label>  
										<div class="col-sm-2">
											<div class="input-group">
                                                <span class="input-group-addon" id="real">R$</span>
                                                <input type="text" class="form-control" id="sAumento" name="sAumento" placeholder="0,00" aria-describedby="real" value="" />
                                            </div>
											 <div class="help-block with-errors"></div>
										</div>
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
				<input type="hidden" name="aadmissao" id="aadmissao" value="">
				<div class="col-xs-offset-2 col-xs-4"><button type="submit" class="btn btn-success envio">Solicitar Avaliação</button></div>
			</form>
		</div>
	</div>
</div>
<br />
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>