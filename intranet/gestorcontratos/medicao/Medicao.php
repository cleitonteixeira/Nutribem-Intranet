,<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<script>
    $(document).ready(function(){
        $('#gMedicao').attr('disabled',true);
        //Carrega Colaboradores
        $("#BuscaInfo").click(function(e){
            $('#gMedicao').attr('disabled',true);

                $('#Total').html("aguardando...").show();
                $('#vtMedicao').val("").show();
            
                var contrato = $('#contrato').val();//pegando o value do option selecionado
                var dataIN = $('#dataIN').val();//pegando o value do option selecionado
                var dataFN = $('#dataFN').val();//pegando o value do option selecionado
                var us = <?php echo $_SESSION['idusuarios']; ?>;
                if(contrato != '' && dataIN != '' && dataFN != ''){
                    $.post('Medicao.inc.php',{buscar: us,dcontrato: contrato, dataIN: dataIN, dataFN: dataFN} , function (dados){
                        //alert(dados);
                        dados = JSON.parse(dados);
                        //alert(dados);
                        if (dados.length > 0){
                            var option = '';
                            var total = '';
                            $.each(dados, function(i, obj){
                                option = obj.Dados;
                                total = obj.Total;
                            })
                            $('#Total').html(option).show();
                            $('#vtMedicao').val(total).show();
                            $('#gMedicao').attr('disabled',false);
                            
                        }else{
                            $('#Total').html("aguardando...").show();
                            
                            Reset();
                        }
                    })
                }else{
                    $('#Total').html("aguardando...").show();
                    var op = '';
                    setTimeout(function() {
                        $("input[name='Buscar']").prop("checked",false);
                    }, 1500);
                    return false;
                }
        })
        <!-- Resetar Selects -->
        function Reset(){
            $('#Total').html("aguardando...").show();
                setTimeout(function() {
                    $("input[name='Buscar']").prop("checked",false);
                }, 1500);
                return false;
        }
        $('.input-daterange').datepicker({
            todayBtn: "linked",
            language: "pt-BR"
        })
        
        $("select[name='contrato']").change(function(e){
			var cli = $('#contrato').val();//pegando o value do option selecionado
			//alert(cli);//apenas para debugar a variável
			$.post('Medicao.inc.php',{contrato: cli} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length != 0 ){ 	
					var nMedicao = '';
					var Contrato = '';
					var Obs = '';
					$.each(dados, function(i, obj){
						nMedicao = obj.nMedicao;
						Contrato = obj.Contrato;
						pFechamento = obj.pFechamento;
						Obs = obj.Obs;
					})
					$('#nContrato').html(Contrato).show();
					$('#nMedicao').html(nMedicao).show();
                    $('#inContrato').val(Contrato).show();
					$('#inMedicao').val(nMedicao).show();
					$('#pFechamento').html(pFechamento).show();
					$('#Obs').html(Obs).show();
				}else{
					Reset1();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset1(){
			$('#inContrato').val("").show();
			$('#nContrato').html("").show();
			$('#inMedicao').val("").show();
            $('#nMedicao').html("").show();
            $('#pFechamento').html("").show();
		}
        $("#gMedicao").click(function(e){
            $('#vContrato').val($('#inContrato').val()).show();
            $('#vMedicao').val($('#inMedicao').val()).show();
            $('#iMedicao').val($('#dataIN').val()).show();
            $('#fMedicao').val($('#dataFN').val()).show();
        })
    });
</script>
<div class="container-fluid">
    <div class="conteudo">
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h2 class="text-center">Fechar Medição</h2></div>
        <form  name="Form" role="form" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente" >
            <div class="form-group">
                <label class="col-sm-2 control-label" for="contrato">Contrato:</label>
                <div class="col-sm-6">
                    <select class="selectpicker form-control" title="Selecione uma Contrato!" name="contrato" id="contrato" data-live-search="true" data-width="100%" data-size="5" data-actions-box="true" required>
                        <?php
                        $sql = 'SELECT cd.Nome AS Unidade, idUnidadeFaturamento AS idUnidade FROM unidadefaturamento uf INNER JOIN cadastro cd ON cd.idCadastro = uf.Cadastro_idCadastro WHERE uf.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?)';
                        $stm = $conexao->prepare($sql);
                        $stm->bindParam(1, $_SESSION['idusuarios']);
                        $stm->execute();
                        $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                        foreach($rs as $r){
                        ?>
                        <optgroup label="<?php echo utf8_decode($r->Unidade); ?>" >
                            <?php
                            $sql = "SELECT c.idContrato, c.cCusto, c.nContrato, cad.Nome, cad.CNPJ FROM contrato c INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = ct.Cadastro_idCadastro WHERE c.Unidade_idUnidade = ?  AND c.Finalizado = 'N';";
                            $stm = $conexao->prepare($sql);
                            $stm->bindParam(1, $r->idUnidade);
                            $stm->execute();
                            $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                            foreach($rs as $r){
                            ?>
                            <option value="<?php echo $r->idContrato; ?>" data-tokens="<?php echo CNPJ_Padrao($r->CNPJ)." - ".utf8_decode($r->Nome); ?>"><?php echo $r->nContrato." - ".utf8_decode($r->Nome).' - '.utf8_decode($r->cCusto); ?></option>						
                            <?php } ?>
                        </optgroup>
                        <?php } ?>
                    </select>
                    <div class="help-block with-errors"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Período de Apuração:</label>
                <div class="col-sm-3">
                    <div class="input-daterange input-group" id="datepicker">
                        <input type="text" class="form-control" id="dataIN" name="dataIN" />
                        <span class="input-group-addon"> até </span>
                        <input type="text" class="form-control" id="dataFN" name="dataFN" data-date-end-date="0d" />
                    </div>
                </div>
            </div>
            <div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-2 col-md-offset-2 col-lg-offset-2">
                <div class="form-group">
                    <button id="BuscaInfo" type="button" class="btn btn-primary">Buscar</button>
                </div>
            </div>
            <div class="col-xs-12 col-lg-12 col-md-12">
                <hr />
            </div>
            <div class="col-xs-12 col-lg-12 col-md-12 text-left">
                <div class="form-group">
                    <p>
                        <strong>Contrato: </strong>
                        <span name="nContrato" id="nContrato"></span>
                        <input type="hidden" name="inContrato" id="inContrato">
                    </p>
                </div>
                <div class="form-group">
                    <p>
                        <strong>Medição Nº: </strong>
                        <span name="nMedicao" id="nMedicao"></span>
                        <input type="hidden" name="inMedicao" id="inMedicao">
                    </p>
                </div>
                <div class="form-group">
                    <p>
                        <strong>Fechamento: </strong>
                        <span name="pFechamento" id="pFechamento"></span>
                    </p>
                </div>
                <div class="form-group">
                    <p>
                        <strong>Observações: </strong>
                        <span name="Obs" id="Obs"></span>
                    </p>
                </div>
            </div>
            <div class="col-xs-12 col-lg-12 col-md-12">
                <hr />
            </div>
            <div class="col-xs-12 col-lg-12 col-md-12 text-center">
                <div class="Total" id="Total"></div>
            </div>
            <div class="col-xs-12 col-md-12 col-lg-12 text-center" >
                <input type="hidden" value="Medicao" name="Cliente" />
                <!--<button class="btn btn-success" type="submit">Gerar Medição</button>-->
                <button type="button" id="gMedicao" class="btn btn-primary" data-toggle="modal" data-target="#confirmaMedicao">
                    Gerar Medição
                </button>
                 
            </div>
        </form>
        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
    </div>
</div>
<div class="modal fade " id="confirmaMedicao" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                <h4 class="modal-title">CONFIRMAR MEDIÇÃO</h4>
            </div>
            <div class="modal-body">
                <form role="form" method="post" action="<?php echo BASE;?>control/banco/Medicao.php" id="confirmMedicao" enctype="multipart/form-data">
                    <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label for="vContrato">Contrato:</label>
                            <input type="text" class="form-control" id="vContrato" name="vContrato" readonly>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label for="vMedicao">Medição Nº:</label>
                            <input type="text" class="form-control" id="vMedicao" name="vMedicao" readonly>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label for="iMedicao">Inicio da Medição</label>
                            <input type="text" class="form-control" value="" id="iMedicao" name="iMedicao" readonly>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label for="fMedicao">Fim da Medição</label>
                            <input type="text" class="form-control" value="" id="fMedicao" name="fMedicao" readonly>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-12">
                    
                        <div class="form-group">
                            <label for="fMedicao">Valor da Medição</label>
                            <input type="text" class="form-control" value="" id="vtMedicao" name="vtMedicao" readonly>
                        </div>
                    </div>
                    
                    <div class="col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label for="doc">Arquivo: </label>
                            <input type="file" class="filestyle" name="doc" id="doc" data-icon="false" data-buttonText="Selecionar Arquivo" data-buttonName="btn-primary" data-buttonBefore="true" />
                        </div>
                    </div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" >Confirmar Medição</button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>