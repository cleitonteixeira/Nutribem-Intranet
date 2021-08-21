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
        $("#validaCod").click(function(e){
            var codigo = $('#cdl').val();//pegando o value do option selecionado
            //alert(codigo);
            $.post('LiberaData.inc.php',{ codigo: codigo} , function (dados){
                //alert(dados);
                dados = JSON.parse(dados);
                //alert(dados);
                if (dados.length > 0){
                    $.each(dados, function(i, obj){
                        if(obj.resultado == 'Sucesso'){
                            $('#unidade1').html(obj.unidade).show();
                            $('#unidade').val(obj.unidade).show();
                            $('#dLiberada').val(obj.dataLiberada).show();
                            $('#dLiberada1').html(obj.dataLiberada1).show();
                            $('#nUnidade1').html(obj.nUnidade).show();
                            $('#nUnidade').val(obj.nUnidade).show();
                            $('#dLimite').val(obj.dLimite).show();
                            $('#dLimite1').html(obj.dLimite1).show();
                            $('#cdl1').html(obj.cdl).show();
                            $('#cdl2').val(obj.cdl).show();
                            $('#confirmaCod').modal('show');
                        }else{
                            if(obj.resultado == 'Erro'){
                                $("#result").html('<div class="alert alert-danger"><p><strong>Erro!</strong> A seguir as possiveis causas:</p></div><ul class="list-group"><li class="list-group-item list-group-item-default">Código informado está incorreto!</li><li class="list-group-item list-group-item-warning">Data Limite ultrapassada!</li><li class="list-group-item list-group-item-default">Código incorreto para seu usuário!</li><li class="list-group-item list-group-item-warning">Código já ultilizado!</li></ul>').show();
                                $('#altera').modal('hide');
                                $('#altera').modal('show');
                            }
                        }
                    })
                }
            })
        })
    });
</script>
<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">LANÇAMENTO DE DATA RETROATIVA</h1>
            
            <form name="Form" role="form" action="LancamentoRetroativo.php" method="post" enctype="multipart/form-data" class="conteudo form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="cdl">Codigo:</label>
                    <div class="col-sm-2">
                        <input type="text" name="cdl" id="cdl" required class="form-control" >
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-success" id="validaCod" type="button">VALIDAR CODIGO</button>
                    </div>
                </div>
            </form>
            
            <?php if(isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade"){?>
            
            <div class="col-md-12 col-xs-12 col-md-12"><hr /></div>
            <div class="col-md-12 col-xs-12 col-md-12">
                <h3 class="text-center">LANÇAMENTO PARA A DATA <strong><?=date("d/m/Y", strtotime($_POST['dLancamento']));?></strong> PARA A UNIDADE <strong><?=$_POST['nUnidade'];?></strong></h3>
                <div class="lancamentos col-md-12 col-xs-12 col-md-12">
                        <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/DiarioDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente" >
                            <div class="panel-group" id="accordion">
                            <?php
                            $sql = "SELECT cc.Nome, c.* FROM contrato c INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cc ON cc.idCadastro = ct.Cadastro_idCadastro WHERE c.Unidade_idUnidade = ? AND c.Finalizado = 'N';";
                            $stm = $conexao->prepare($sql);
                            $stm->bindParam(1, $_POST['Unidade']);
                            $stm->execute();
                            $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                            $dComp = date("Y-m-d");
                            $contador = 0;
                            foreach($rs as $r){
                                $sql = "SELECT * FROM itensproposta WHERE Proposta_idProposta = ?;";
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $r->Proposta_idProposta);
                                $stm->execute();
                                $row = $stm->fetchAll(PDO::FETCH_OBJ);
                            ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$contador;?>"><strong>Cliente:</strong> <?php echo utf8_decode($r->Nome); ?> --- <strong>Centro de Custo:</strong> <?php echo utf8_decode($r->cCusto); ?> --- <strong>Contrato Nº:</strong> <?php echo $r->nContrato; ?></a>
                                        </h4>
                                    </div>
                                    <div id="collapse<?=$contador;?>" class="panel-collapse collapse <?php echo $contador == 0 ? "in" : ''; ?>">
                                        <div class="panel-body">
                            <?php
                                $sqlj = "SELECT * FROM lancamento WHERE contrato_idContrato = ? AND dLancamento = ?;";
                                $stmj = $conexao->prepare($sqlj);
                                $stmj->bindParam(1, $r->idContrato);
                                $stmj->bindParam(2, $_POST['dLancamento']);
                                $stmj->execute();
                                foreach($row as $rs){
                                    if(!$stmj->rowCount() > 0){
                            ?>
                                        <div class="col-xs-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label class="control-label col-sm-3" for="<?php echo utf8_decode($r->idContrato)?>[<?php echo utf8_decode($rs->Servico)?>][Quant]"><?php echo utf8_decode($rs->Servico)?>:</label>
                                                <div class="col-sm-6">
                                                    <div class="col-sm-4">
                                                        <input required type="text" class="form-control" name="<?php echo $r->idContrato?>[<?php echo $rs->Servico?>][Quant]" id="<?php echo $r->idContrato; ?>[<?php echo $rs->Servico?>][Quant]" onkeypress="return SomenteNumero(event)" <?php echo $stmj->rowCount() > 0 ? "disabled" : ''; ?> placeholder="Quantidade" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            <?php
                                        }
                                    }
                            ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                                    $contador += 1;
                                }
                            ?>
                            </div>
                            <div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-1 col-md-offset-1 col-lg-offset-1">
                                <input type="hidden" value="<?php echo $_POST['dLancamento'] ?>" name="dLancamento" />
                                <input type="hidden" value="<?php echo $_POST['Unidade'] ?>" name="Unidade" />
                                <input type="hidden" value="<?php echo $_POST['cdl'] ?>" name="cdl" />
                                <input type="hidden" value="Lancamento" name="Diario" />
                                <button class="btn btn-success" type="submit" >Salvar <i class="fa fa-save" aria-hidden="true"></i></button>
                            </div>
                            
                            <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                        </form>
                    </div>
                 <?php
                        }
                    ?>
                
            <div id="confirmaCod" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">CONFIRMAÇÃO DE DATA RETROATIVA</h4>
                        </div>
                        <div class="modal-body body-medicao">
                            <div class="col-xs-8 col-md-8 col-lg-8"> </div>
                            <div class="col-xs-8 col-md-8 col-lg-8">
                                <p><strong>Unidade:</strong> <span id="unidade1"></span> - <span id="nUnidade1"></span></p>
                                <p><strong>Data Liberada:</strong> <span id="dLiberada1"></span></p>
                                <p><strong>Data Limite para Lançamento:</strong> <span id="dLimite1"></span></p>
                                <p><strong>Codigo Liberação:</strong> <span id="cdl1"></span></p>
                            </div>

                            <div class="col-xs-8 col-md-8 col-lg-8"> </div>
                            <div class="col-xs-8 col-md-8 col-lg-8"> </div>
                            <div class="col-xs-8 col-md-8 col-lg-8"> </div>
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <div class="col-xs-3 col-md-3 col-lg-3">
                                    <form action="LancamentoRetroativo.php" method="post" enctype="multipart/form-data" class="form-inline" data-toggle="validator">
                                        <input type="hidden" class="form-control" value="" id="dLimite" name="dLimite" readonly>
                                        <input type="hidden" class="form-control" value="" id="dLiberada" name="dLancamento" readonly>
                                        <input type="hidden" class="form-control" value="" id="unidade" name="Unidade" readonly>
                                        <input type="hidden" class="form-control" value="" id="nUnidade" name="nUnidade" readonly>
                                        <input type="hidden" class="form-control" value="" id="cdl2" name="cdl" readonly>
                                        <input type="hidden" class="form-control" value="Data-Unidade" id="Diario" name="Diario" readonly>
                                        <button type="submit" class="btn btn-success" >CONTINUAR</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="altera" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">RETORNO SOLICITAÇÃO</h4>
            </div>
            <div class="modal-body">
                <div id="result"></div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
