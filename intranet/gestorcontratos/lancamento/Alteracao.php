<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: ".BASE);
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
    $data1 = date("d/m/Y");
    $data = date("Y-m-d");
    $data = date('d/m/Y', strtotime("-3 days", strtotime($data)));
?>
<script language='JavaScript'>
    function SomenteNumero(e){
        var tecla=(window.event)?event.keyCode:e.which;   
        if((tecla>47 && tecla<58)) return true;
        else{
            if (tecla==8 || tecla==0) return true;
            else  return false;
        }
    }
</script>
<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">ALTERAÇÃO DE LANÇAMENTO</h1>
            <div class="text-center">
                <div class="row">
                    <form  name="Form" role="form" action="" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente" >
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="dLancamento">Data: </label>
                                    <div class="col-sm-5">
                                        <input type="date" class="form-control" required name="dLancamento" <?php echo !isset($_POST['Diario']) ? "autofocus" : ''; ?> id="dLancamento" <?php echo 	isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade" ? "readonly" : ''; ?> value="<?php echo 	isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade" ? $_POST['dLancamento'] : date("Y-m-d"); ?>" />
                                        <div class="help-block with-errors"></div>
                                        <div id="alerta-data"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="<?php echo !isset($_POST['Diario']) || $_POST['Diario'] == "Nova-Data" ? "formulario-complementar" : ''; ?>">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="Unidade">Unidade: </label>
                                        <div class="col-sm-5">
                                            <select class="selectpicker form-control dropdown" required name="Unidade" id="Unidade" title="Selecione um Unidade" data-size="5" data-live-search="true" <?php echo isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade" ? "disabled" : ''; ?> >
                                                <?php
                                                $sql = 'SELECT * FROM unidadefornecimento em';
                                                $stm = $conexao->prepare($sql);
                                                $stm->execute();
                                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                                foreach($rs as $r):
                                                ?>
                                                <optgroup label="<?php echo utf8_decode($r->Nome); ?>" >
                                                    <?php
                                                    $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome FROM unidadefaturamento un  INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE un.Fornecimento_idFornecimento = ? AND un.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
                                                    $stm = $conexao->prepare($sql);
                                                    $stm->bindParam(1, $r->idUnidadeFornecimento);
                                                    $stm->bindParam(2, $_SESSION['idusuarios']);
                                                    $stm->execute();
                                                    while($row = $stm->fetch(PDO::FETCH_OBJ)):
                                                    ?>
                                                    <option <?php echo isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade" && $_POST['Unidade'] == $row->idUnidadeFaturamento ? "selected" : ''; ?> value="<?php echo $row->idUnidadeFaturamento; ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                                    <?php endwhile; ?>
                                                </optgroup>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-1 col-md-offset-1 col-lg-offset-1">
                                        <?php
                                        if(isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade"){
                                        ?>
                                        <input type="hidden" value="Nova-Data" name="Diario" />
                                        <button class="btn btn-success" type="submit">Nova Data</button>
                                        <?php
                                        }else{
                                        ?>
                                        <input type="hidden" value="Data-Unidade" name="Diario" />
                                        <button class="btn btn-success" type="submit">Selecionar</button>
                                        <?php
                                        }	
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php if(isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade"){ ?>
                    <div class="col-xs-12 col-md-12 col-lg-12 conteudo"></div>
                        <?php
                        $sql = "SELECT cc.Nome, c.* FROM contrato c INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cc ON cc.idCadastro = ct.Cadastro_idCadastro WHERE c.Unidade_idUnidade = ? AND c.Finalizado = 'N';";
                        $stm = $conexao->prepare($sql);
                        $stm->bindParam(1, $_POST['Unidade']);
                        $stm->execute();
                        $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                        $dComp = date("Y-m-d");
                        foreach($rs as $r){
                            $sql = "SELECT * FROM itensproposta WHERE Proposta_idProposta = ?;";
                            $stm = $conexao->prepare($sql);
                            $stm->bindParam(1, $r->Proposta_idProposta);
                            $stm->execute();
                            $row = $stm->fetchAll(PDO::FETCH_OBJ);
                        ?>
                        <div class="col-xs-12 col-md-12 col-lg-12"><hr /></div>
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <p><strong>Cliente:</strong> <?php echo utf8_decode($r->Nome); ?> --- <strong>Centro de Custo:</strong> <?php echo utf8_decode($r->cCusto); ?> --- <strong>Contrato Nº:</strong> <?php echo $r->nContrato; ?></p>
                        </div>
                        <div class="col-xs-offset-2 col-xs-2 col-md-offset-2 col-md-2 col-lg-offset-2 col-lg-2">
                            <p><strong>Quantidade</strong></p>
                        </div>
                        <?php
                        $sqlj = "SELECT * FROM lancamento WHERE contrato_idContrato = ? AND dLancamento = ?;";
                        $stmj = $conexao->prepare($sqlj);
                        $stmj->bindParam(1, $r->idContrato);
                        $stmj->bindParam(2, $_POST['dLancamento']);
                        $stmj->execute();
                        $row = $stmj->fetchAll(PDO::FETCH_OBJ);
                        foreach($row as $rs){
                        ?>
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <form name="Form" role="form" action="" method="post" enctype="multipart/form-data" class="form-horizontal text-left" data-toggle="validator" id="FormCliente" name="FormCliente" >
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="<?php echo $rs->idLancamento; ?>"><?php echo utf8_decode($rs->Servico)?>:</label>
                                    <div class="col-sm-1">
                                        <input required type="text" class="form-control" id="v<?php echo $rs->idLancamento; ?>" value="<?php echo $rs->Quantidade; ?>" readonly/>
                                    </div>
                                    <button data-toggle="modal" data-target="#alter<?php echo $rs->idLancamento; ?>" type="button" class="btn btn-primary">Editar Quantidade <i class="fas fa-edit"></i></button>
                                </div>
                            </form>
                            <div id="alter<?php echo $rs->idLancamento; ?>" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                                            <h4 class="modal-title">CONFIRMAR ALTERAÇÃO DE LANÇAMENTO</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form role="form" method="post" class="form-horizontal text-left">
                                                <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                                                <div class="col-xs-12 col-md-12 col-lg-12">
                                                    <div class="form-group">
                                                        <label for="<?php echo $rs->idLancamento; ?>">Quantidade:</label>
                                                         <input required type="text" class="form-control" name="<?php echo $r->idContrato?>[<?php echo $rs->Servico?>][Quant]" id="<?php echo $rs->idLancamento; ?>" onkeypress="return SomenteNumero(event)" value="<?php echo $rs->Quantidade; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-md-12 col-lg-12">
                                                    <div class="form-group">
                                                        <label for="justificativa<?php echo $rs->idLancamento; ?>">Justificativa:</label>
                                                        <textarea id="justificativa<?php echo $rs->idLancamento; ?>" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                                
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                                                <button type="button" class="btn btn-primary" id="btn<?php echo $rs->idLancamento; ?>">Salvar Quantidade <i class="fas fa-edit"></i></button>
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div>
                            </div>
                            <div id="altera<?php echo $rs->idLancamento; ?>" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div id="result<?php echo $rs->idLancamento; ?>"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function(){
                                    $("#btn<?php echo $rs->idLancamento; ?>").click(function(e){
                                        var lancamento = <?php echo $rs->idLancamento; ?>;//pegando o value do option selecionado
                                        var quant = $('#<?php echo $rs->idLancamento; ?>').val();//pegando o value do option selecionado
                                        var justificativa = $('#justificativa<?php echo $rs->idLancamento; ?>').val();//pegando o value do option selecionado
                                        if(quant != ''){
                                            $.post('aLancamento.inc.php',{alterQuant: lancamento, quant: quant, justificativa: justificativa} , function (dados){
                                                //alert(dados);
                                                dados = JSON.parse(dados);
                                                //alert(dados);
                                                if (dados.length > 0){
                                                    $.each(dados, function(i, obj){
                                                        if(obj.resultado == 'Sucesso'){
                                                            $("#result<?php echo $rs->idLancamento; ?>").html('<div class="alert alert-success"><strong>Suceso!</strong> Sucesso ao alterar quantidade.</div>');
                                                            $('#alter<?php echo $rs->idLancamento; ?>').modal('hide');
                                                            $('#altera<?php echo $rs->idLancamento; ?>').modal('show');
                                                            $('#v<?php echo $rs->idLancamento; ?>').val(quant).show();
                                                            setTimeout(function() {
                                                                $('#altera<?php echo $rs->idLancamento; ?>').modal('hide');
                                                            }, 2750);
                                                            
                                                        }else{
                                                            if(obj.resultado == 'Erro'){
                                                                $("#result<?php echo $rs->idLancamento; ?>").html('<div class="alert alert-danger"><strong>Erro!</strong> Erro ao alterar quantidade.</div>');
                                                                $('#altera<?php echo $rs->idLancamento; ?>').modal('hide');
                                                                $('#altera<?php echo $rs->idLancamento; ?>').modal('show');
                                                                setTimeout(function() {
                                                                    $('#altera<?php echo $rs->idLancamento; ?>').modal('hide');
                                                                }, 2750);
                                                            }
                                                        }
                                                    })
                                                }
                                            })
                                        }else{
                                            $("#result<?php echo $rs->idLancamento; ?>").html('<div class="alert alert-warning"><strong>Erro!</strong> O valor não pode ser nulo.</div>');
                                            $('#altera<?php echo $rs->idLancamento; ?>').modal('show');
                                            setTimeout(function() {
                                                $('#altera<?php echo $rs->idLancamento; ?>').modal('hide');
                                            }, 2750);
                                        }
                                    })
                                });
                            </script>
                        </div>
                        <?php
                                }
                            }
                        }
                        ?>
                        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>