<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
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
                    $('#botoes').toggle();
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
            $('#botoes').hide();
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
            $.post('Completa.inc.php',{gcontrato: cli} , function (dados){
                //alert(dados);
                dados = JSON.parse(dados);
                if (dados.length != 0 ){ 	
                    var option = '<option value="">Selecione!</option>';
                    $.each(dados, function(i, obj){
                        option += '<option value="'+obj.idContrato+'">Nº: '+obj.Contrato+' - Data Cadastro: '+obj.DataCadastro+' - Fim Vigência: '+obj.DataVigencia+' - UNIDADE: '+obj.Unidade+' - Desativado: '+obj.Finalizado+'</option>';
                    })
                    $('#Contrato').html(option).show();
                }else{
                    Reset();
                }
            })
        })
        <!-- Resetar Selects -->
        function Reset(){
            $('#Contrato').empty();
        }
        $("select[name='Contrato']").change(function(e){
            var prop = $('#Contrato').val();//pegando o value do option selecionado
            //alert(unidade);//apenas para debugar a variável
            $.post('Contrato.inc.php',{contrato: prop} , function (dados){
                //alert(dados);
                dados = JSON.parse(dados);
                if (dados.length == 1){ 	
                    var Contrato = '';
                    var nContrato = '';
                    var eResponsavel = '';
                    var uFornecimento = '';
                    var vigencia = '';
                    var dReajuste = '';
                    var cCusto = '';
                    var obs = '';
                    var cnpj = '';
                    var pCompra = '';
                    var idEmpresa = '';
                    var pFechamento = '';
                    var cMinima = '';
                    $.each(dados, function(i, obj){
                        Contrato = obj.idContrato;
                        nContrato = obj.nContrato;
                        eResponsavel = obj.Nome+" - "+obj.CNPJ.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
                        uFornecimento = obj.Unidade;
                        vigencia = obj.VigenciaFim;
                        dReajuste = obj.DataReajuste;
                        cCusto = obj.cCusto;
                        pCompra = obj.pCompra;
                        obs = obj.Obs;
                        cnpj = obj.CNPJ;
                        idEmpresa = obj.Responsavel;
                        pFechamento = obj.Fechamento;
                        cMinima = obj.ConsumacaoMinima;
                    })
                    
                    $('#ContratoF').val(Contrato).show();
                    $('#ContratoI').val(Contrato).show();
                    $('#idContrato').val(Contrato).show();
                    $('#idContrato2').val(Contrato).show();
                    $('#idContratoEd').val(Contrato).show();
                    $('#fVigencia').val(vigencia).show();
                    $('#nContrato').html(nContrato).show();
                    $('#eResponsavel').html(eResponsavel).show();
                    $('#uFornecimento').html(uFornecimento).show();
                    var split1 = vigencia.split('-');
                    $('#vigencia').html(split1[2]+"/"+split1[1]+"/"+split1[0]).show();
                    var split2 = dReajuste.split('-');
                    $('#dReajuste').html(split2[2]+"/"+split2[1]+"/"+split2[0]).show();
                    $('#cCusto').html(cCusto).show();
                    $('#cMinima').html(cMinima).show();
                    $('#ceCusto').val(cCusto).show();
                    $('#obsInstrucoes').val(obs).show();
                    $('#pCompra').val(pCompra).show();
                    $('#empresa').find('[value="' + idEmpresa + '"]').attr('selected', true);
                    $('#pFechamento').find('[value="' + pFechamento + '"]').attr('selected', true);
                }else{
                    Reset3();
                }
            })
        })
        <!-- Resetar Selects -->
        function Reset3(){
            $('#obsInstrucoes').val().show();
            $('#ContratoI').val().show();
            $('#idContrato').val().show();
            $('#idContrato2').val().show();
            $('#idContratoEd').val().show();
            $('#ContratoF').val().show();
            $('#fVigencia').val().show();
            $('#nContrato').html().show();
            $('#eResponsavel').html().show();
            $('#uFornecimento').html().show();
            $('#vigencia').html().show();
            $('#dReajuste').html().show();
            $('#cCusto').html().show();
            $('#ceCusto').val().show();
            $('#pCompra').val().show();
            $('#cMinima').html().show();
        }
    });
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">EDITAR CONTRATO</h1>
            <form name="Form" role="form" action="" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente">
                <div class="col-xs-12 col-md-12 col-lg-12 text-center">
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="cliente">Cliente: </label>
                        <div class="col-sm-8">
                            <select class="selectpicker form-control dropdown" autofocus name="cliente" id="cliente" title="Selecione um Cliente" data-size="5" data-header="Selecione um Cliente" data-live-search="true">
                                <?php
                                foreach($rs as $r ):
                                ?>
                                <option <?php echo isset($_GET['cod']) && $_GET['cod'] == $r->idContratante ? "selected" : "" ?> data-tokens="<?php echo $r->CNPJ.' '.utf8_decode($r->Cliente).' '.str_pad($r->idContratante,3,0,STR_PAD_LEFT); ?>" data-subtext="CNPJ: <?php echo CNPJ_Padrao(str_pad($r->CNPJ,14,0,STR_PAD_LEFT)); ?>" value="<?php echo $r->idContratante ?>" ><?php echo str_pad($r->idContratante,3,0,STR_PAD_LEFT)." - ".utf8_decode($r->Cliente); ?></option>
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
            <div class="col-xs-10 col-md-10 col-lg-10">
                <form name="Form" role="form" class="form-horizontal text-left">
                    <label class="col-sm-2 control-label" for="Contrato">Contrato:</label>
                    <div class="col-sm-10">
                        <select class="form-control" title="Selecione um Contrato" name="Contrato" id="Contrato" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione um Contrato." required>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-xs-12 col-md-12 col-lg-12"> </div>
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="panel panel-default text-justify">
                    <div class="panel-heading">
                        <h1 class="panel-title">DADOS CONTRATO</h1>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-12 col-md-12 col-lg-12"><p><strong>EMPRESA RESPONSÁVEL: </strong><span id="eResponsavel"></span></p></div>
                        <div class="col-xs-12 col-md-12 col-lg-12"><p><strong>UNIDADE DE FORNECIMENTO: </strong><span id="uFornecimento"></span></p></div>	
                        <div class="col-xs-3 col-md-3 col-lg-3"><p><strong>VIGÊNCIA: </strong><span id="vigencia"></span></p></div>
                        <div class="col-xs-3 col-md-3 col-lg-3"><p><strong>DATA REAJUSTE: </strong><span id="dReajuste"></span></p></div>
                        <div class="col-xs-3 col-md-3 col-lg-3"><p><strong>CONTRATO: </strong><span id="nContrato"></span></p></div>
                        <div class="col-xs-6 col-md-6 col-lg-6"><p><strong>CENTRO DE CUSTO: </strong><span id="cCusto"></span></p></div>
                        <div class="col-xs-6 col-md-6 col-lg-6"><p><strong>CONSUMAÇÃO MÍNIMA: </strong><span id="cMinima"></span></p></div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-12 col-lg-12">
                <hr />
            </div>
            <div class="col-xs-12 col-md-12 col-lg-12 text-left" id="botoes">
                <div class="col-xs-3 col-md-3 col-lg-3">
                    <form name="Form" role="form" class="form-horizontal text-center">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <button class="btn btn-success" type="button" id="pConfirmar" data-toggle="modal" data-target="#pConfirma">Prorrogar Contrato</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xs-3 col-md-3 col-lg-3">
                    <form name="Form" action="<?php echo BASE; ?>control/banco/Modify.php" method="post" role="form" class="form-horizontal text-center">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <button class="btn btn-danger" type="button" id="fConfirmar" data-toggle="modal" data-target="#fConfirma">Ativar/Desativar Contrato</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xs-3 col-md-3 col-lg-3">
                    <form name="Form" action="<?php echo BASE; ?>control/banco/Modify.php" method="post" role="form" class="form-horizontal text-center">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <button class="btn btn-primary" type="button" id="iConfirmar" data-toggle="modal" data-target="#mContratoIns">Editar Contrato</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xs-3 col-md-3 col-lg-3">
                    <form name="Form" role="form" class="form-horizontal text-center">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <button class="btn btn-warning" type="button" id="fConfirmar" data-toggle="modal" data-target="#mInstrucoes">Modificar Instruções</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="fConfirma" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">CONFIRMAR FINALIZAÇÃO DE CONTRATO?</h4>
                        </div>
                        <div class="modal-body">
                            <form name="Form" action="<?php echo BASE; ?>control/banco/Modify.php" method="post" role="form" class="form-horizontal text-left">
                                <input type="hidden" value="Modificar" name="dContrato" />
                                <input type="hidden" value="Sim" name="Finalizar" />
                                <input type="hidden" value="" name="ContratoF" id="ContratoF" />
                                <button class="btn btn-success" type="submit">Sim</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Não</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="pConfirma" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">PRORROGAR CONTRATO POR QUANTOS MESES?</h4>
                        </div>
                        <div class="modal-body">
                            <form name="Form" action="<?php echo BASE; ?>control/banco/Modify.php" method="post" role="form" class="form-horizontal text-center">
                                
                                <div class="form-group">
                                    <label class="control-label col-sm-4" for="data">Data: </label>
                                    <div class="col-sm-8">
                                        <input type="hidden" value="Modificar" name="iContrato" />
                                        <input type="hidden" value="Modificar" name="dContrato" />
                                        <input type="hidden" value="" name="idContrato" id="idContrato" />
                                        <input type="date" class="form-control" name="data" id="data" required />
                                        <input type="hidden" class="form-control" name="fVigencia" id="fVigencia" required />
                                    </div>
                                </div>
                                <button class="btn btn-success" type="submit">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="mInstrucoes" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">INSTRUÇÕES DO CONTRATO:</h4>
                        </div>
                        <div class="modal-body">
                            <form name="Form" action="<?php echo BASE; ?>control/banco/Modify.php" method="post" role="form" class="text-center">
                                <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                                <div class="col-xs-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <input type="hidden" value="Modificar" name="iContrato" />
                                        <input type="hidden" value="Modificar" name="dContrato" />
                                        <input type="hidden" value="" name="idContrato" id="idContrato2" />
                                        <label for="obsInstrucoes">Instruções:</label>
                                        <textarea class="form-control" id="obsInstrucoes" name="obsInstrucoes"></textarea>
                                    </div>
                                </div>
                                <button class="btn btn-success" type="submit">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="mContratoIns" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">EDITAR INFORMAÇÕES DO CONTRATO:</h4>
                        </div>
                        <div class="modal-body">
                            <form name="Form" action="<?php echo BASE; ?>control/banco/Modify.php" method="post" role="form" class="form-horizontal text-center">
                                <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                                <div class="col-xs-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="empresa">Empresa: </label>
                                        <div class="col-sm-8">
                                            <select class="form-control dropdown" name="empresa" id="empresa" title="Selecione uma Empresa" data-size="5"  required>
                                                <?php
                                                foreach($rsEm as $r ):
                                                ?>
                                                <option data-tokens="<?php echo $r->CNPJ.' '.utf8_decode($r->Empresa); ?>" value="<?php echo $r->idEmpresa ?>" ><?php echo str_pad($r->idEmpresa,3,0,STR_PAD_LEFT)." - ".utf8_decode($r->Empresa); ?>  - CNPJ: <?php echo CNPJ_Padrao(str_pad($r->CNPJ,14,0,STR_PAD_LEFT)); ?></option>
                                                <?php
                                                endforeach;
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="ceCusto">Centro de Custo: </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" id="ceCusto" name="ceCusto" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="pCompra">Pedido de Compra: </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="text" id="pCompra" name="pCompra" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="pFechamento">Periodo de Fechamento: </label>
                                        <div class="col-sm-8">
                                           <select class="form-control dropdown" name="pFechamento" id="pFechamento" title="Selecione um Periodo" data-size="5"  required>
                                                <?php
                                                $sql = 'SELECT * FROM fechamento;';
                                                $stm = $conexao->prepare($sql);
                                                $stm->bindParam(1, $r->CNPJ);
                                                $stm->execute();
                                                while($row = $stm->fetch(PDO::FETCH_OBJ)){
                                                ?>
                                                <option  value="<?php echo $row->idFechamento; ?>"><?=$row->idFechamento." - ".utf8_decode($row->Descricao);?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" value="Editar" name="iContrato" />
                                <input type="hidden" value="Modificar" name="dContrato" />
                                <input type="hidden" value="" name="idContratoEd" id="idContratoEd" />
                                <button class="btn btn-success" type="submit">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>