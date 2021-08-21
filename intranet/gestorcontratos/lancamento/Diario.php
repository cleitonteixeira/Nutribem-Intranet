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
$data1 = date("d/m/Y");
$data = date("Y-m-d");
$sql3   = "SELECT * FROM PrazoLancamento WHERE Usuario_idUsuario = ? LIMIT 1";
$stmt3   = $conexao->prepare($sql3);
$stmt3->bindParam(1, $_SESSION['idusuarios']);
$stmt3->execute();
$row3    = $stmt3->fetch(PDO::FETCH_OBJ);
$data = date('d/m/Y', strtotime("-".$row3->Prazo." days", strtotime($data)));
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='dLancamento']").focusout(function(e){
            var dLancamento  = $("input[name='dLancamento']").val();
            $.post('Completa.inc.php',{dLancamento: dLancamento} , function (dados){
                //alert(dados);
                dados = JSON.parse(dados);
                if (dados.length != 0 ){ 	
                    var result = "";
                    $.each(dados, function(i, obj){
                        result = obj.resultado;
                    })
                    if(result == "V"){
                        $("#formulario-complementar").hide();
                        $("#alerta-data").html('<div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;	</a><strong>Sucesso!</strong> Sua data está correta.</div>').show();
                        $("#formulario-complementar").toggle();
                    }else{
                        Reset1();						
                    }
                }else{
                    Reset1();
                }
            })
        })
        <!-- Resetar Selects -->
        function Reset1(){
            $("#formulario-complementar").hide();
            $("input[name='dLancamento']").val("");
            $("#alerta-data").html('<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Error!</strong> Data incorreta! A data deve estar entre <?php echo $data; ?> e <?php echo $data1; ?>.</div>').show();
        }
    });
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
</script>
<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">FORMULÁRIO DE LANÇAMENTO</h1>
            <div class="text-center">
                <div class="row">
                    <form  name="Form" role="form" action="" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente1" name="FormCliente1" >
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
                                <div class="col-xs-12 col-md-12 col-lg-12">
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
                                                    $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome FROM unidadefaturamento un  INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE un.Fornecimento_idFornecimento = ? AND un.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) AND Ativa = "S" ORDER BY cd.Nome';
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
                                        <h4 class="panel-title text-left">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$contador;?>"><strong>Cliente:</strong> <?php echo utf8_decode($r->Nome); ?> --- <strong>Centro de Custo:</strong> <?php echo utf8_decode($r->cCusto); ?> --- <strong>Contrato Nº:</strong> <?php echo $r->nContrato; ?></a>
                                        </h4>
                                    </div>
                                    <div id="collapse<?=$contador;?>" class="panel-collapse collapse <?php echo $contador == 0 ? "in" : ''; ?>">
                                        <div class="panel-body">
                            <?php
                                foreach($row as $rs){
                                    $sqlj = "SELECT * FROM lancamento WHERE contrato_idContrato = ?  AND Servico = ? AND dLancamento = ?;";
                                    $stmj = $conexao->prepare($sqlj);
                                    $stmj->bindParam(1, $r->idContrato);
                                    $stmj->bindParam(2, $rs->Servico);
                                    $stmj->bindParam(3, $_POST['dLancamento']);
                                    $stmj->execute();
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
                                <input type="hidden" value="Lancamento" name="Diario" />
                                <button class="btn btn-success" type="submit" >Salvar <i class="fa fa-save" aria-hidden="true"></i></button>
                            </div>
                            
                            <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                        </form>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
endif;
?>