<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("select[name='unidade']").change(function(e){
			var cod = $('#unidade').val();//pegando o value do option selecionado
			//alert(cod);//apenas para debugar a variável

			$.getJSON('LiberaData.inc.php?unidade='+cod, function (dados){
				//alert(dados);
				if (dados.length > 0){
                    var option = '<option disabled selected>SELECIONE UM USUÁRIO.</option>';
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.idusuarios+'">'+obj.Nome+'</option>';
                       // alert(option);
					})
                    $('#user1').empty();
					$('#user1').html('<option disabled selected>AGUARDANDO A UNIDADE SER SELECIONADA....<i class="fas fa-coffee"></i></option>').show();
                    $('#user1').empty();
					$('#user1').html(option).show();
				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#user1').empty();
		}
	});
</script>
<script>
    $(document).ready(function(){
        $("#liberaData").click(function(e){
            var unidade = $('#unidade').val();//pegando o value do option selecionado
            var dataLiberada = $('#dLiberada').val();//pegando o value do option selecionado
            var dataLimite = $('#dLimite').val();//pegando o value do option selecionado
            var user1 = $('#user1').val();//pegando o value do option selecionado
            
            if(user1 != '' & dataLiberada != '' & dataLimite != '' & unidade != ''){
                $.post('LiberaData.inc.php',{un: unidade, dataLiberada: dataLiberada, dataLimite: dataLimite, user: user1} , function (dados){
                    //alert(dados);
                    dados = JSON.parse(dados);
                    //alert(dados);
                    if (dados.length > 0){
                        $.each(dados, function(i, obj){
                            if(obj.resultado === 'Sucesso'){
                                $("#result").html('<div class="alert alert-success"><strong>Suceso!</strong> E-mail enviado com as instruções.</div>').show();
                                $('#alter').modal('hide');
                                $('#altera').modal('show');
                                setTimeout(function() {
                                    $('#altera').modal('hide');
                                }, 2750);

                            }else{
                                if(obj.resultado == 'Erro'){
                                    $("#result").html('<div class="alert alert-danger"><strong>Erro!</strong> Verifique se todos os campos foram preenchidos corretamente. Caso o erro continue entre em contato com o administrados do sistema.</div>').show();
                                    $('#altera').modal('hide');
                                    $('#altera').modal('show');
                                    setTimeout(function() {
                                        $('#altera').modal('hide');
                                    }, 5000);
                                }
                            }
                        })
                    }
                })
            }else{
                $("#result").html('<div class="alert alert-warning"><strong>Erro!</strong> PREENCHA TODOS OS CAMPOS.</div>');
                $('#altera').modal('show');
                setTimeout(function() {
                    $('#altera').modal('hide');
                }, 2750);
            }
        })
    });
</script>
<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">DESBLOQUEIO PARA LANÇAMENTO DE DATA RETROATIVA</h1>
            <p class="text-center"><small>Dados Para Liberação</small></p>
            <form name="Form" role="form" action="#" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente">
                <div class="col-md-6 col-xs-6 col-lg-6 conteudo">
                    <div class="form-group">
                        <label class="col-sm-5 control-label" for="unidade">Unidade de Faturamento:</label>
                        <div class="col-sm-7">
                            <select autofocus name="unidade" id="unidade" required class="form-control selectpicker" title="Selecione uma Unidade" data-live-search="true">
                                <?php
                                $sql = 'SELECT ca.Nome,ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro';
                                $stm = $conexao->prepare($sql);
                                $stm->execute();
                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rs as $r):
                                ?>
                                <optgroup label="<?php echo CNPJ_Padrao($r->CNPJ)." - ".$r->Nome; ?>" >
                                    <?php
                                    $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? ORDER BY cd.Nome';
                                    $stm = $conexao->prepare($sql);
                                    $stm->bindParam(1, $r->CNPJ);
                                    $stm->execute();
                                    while($row = $stm->fetch(PDO::FETCH_OBJ)):
                                    ?>
                                    <option value="<?php echo $row->idUnidadeFaturamento; ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                    <?php endwhile; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label" for="user1">Usuário(s):</label>
                        <div class="col-sm-7">
                            <select name="user1" id="user1" required class="form-control" title="Selecione um Usuário" >
                               <option disabled selected>AGUARDANDO A UNIDADE SER SELECIONADA.</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6 conteudo">
                    <div class="form-group">
                        <label class="col-sm-5 control-label" for="dLiberada">Data Liberada:</label>
                        <div class="col-sm-7">
                            <input name="dLiberada" id="dLiberada" type="date" class="form-control" required/>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label" for="dLimite">Data Limite para Lançamento:</label>
                        <div class="col-sm-7">
                            <input name="dLimite" id="dLimite" type="date" class="form-control" required/>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-10 col-xs-10 col-lg-10 col-md-offset-2 col-xs-offset-2 col-lg-offset-2 text-left">
					<button class="btn btn-success" id="liberaData" type="button">Liberar</button>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12"> </div>
            </form>
            <div id="altera" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <div id="result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>