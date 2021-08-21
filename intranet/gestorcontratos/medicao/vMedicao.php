                                    <!--SELECT * FROM medicao WHERE idMedicao = ?;-->
<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br");
}else{
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
function Telefone($dados){
    $tel_array = str_split($dados);
    $contador = sizeof($tel_array);
    $x = 0;
    $Telefone = "(";
    while($x<=$contador){
        $Telefone .= $tel_array[$x];
        if($x == 1){
            $Telefone .= ") ";
        }
        if($x == 5){
            $Telefone .= "-";
        }
        $x += 1;
        if($x == $contador){
            break;
        }
    }
    return $Telefone;
}
function Celular($dados){
    $tel_array = str_split($dados);
    $contador = sizeof($tel_array);
    $x = 0;
    $Telefone = "(";
    while($x<=$contador){
        $Telefone .= $tel_array[$x];
        if($x == 1){
            $Telefone .= ") ";
        }
        if($x == 6){
            $Telefone .= "-";
        }
        $x += 1;
        if($x == $contador){
            break;
        }
    }
    return $Telefone;
}
$sql = "SELECT * FROM medicao WHERE idMedicao = ?;";
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $_GET['id']);
$stm->execute();
$ct = $stm->fetch(PDO::FETCH_OBJ);
$idContrato = $ct->Contrato_idContrato;
$sql = 'SELECT p.*,cont.*, c.idContratante, c.IE, cd.Nome AS Cliente, fec.Descricao, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca INNER JOIN contrato cont ON cont.idContrato = ? INNER JOIN fechamento fec ON fec.idFechamento = cont.Fechamento INNER JOIN proposta p ON p.idProposta = cont.Proposta_idProposta WHERE c.idContratante = cont.Contratante_idContratante;';
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $idContrato);
$stm->execute();
$row = $stm->fetch(PDO::FETCH_OBJ);
$unidade = $row->Unidade_idUnidade;
$sql = "SELECT * FROM ccontratante WHERE Contratante_idContratante = ?;";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $row->idContratante);
$stm->execute();
$rx = $stm->fetchAll(PDO::FETCH_OBJ);
/* DADOS DO CLIENTE */
if(empty($row->IE)){
    $IE = "ISENTO";
}else{
    $IE = $row->IE;
}
$idContratante = $row->idContratante;
$Nome = utf8_decode($row->Cliente);
$Ccusto = utf8_decode($row->cCusto);
$CNPJ = utf8_decode(CNPJ_Padrao(str_pad($row->CNPJ, 14, 0, STR_PAD_LEFT)));
$Nome = utf8_decode($row->Cliente);
$Endereco = stripslashes(utf8_decode($row->Endereco.", N&ordm;: ".$row->Numero.", ".$row->Bairro." - ".$row->Cidade."-".$row->UF." - CEP: ".CEP_Padrao(str_pad($row->CEP, 8, 0, STR_PAD_LEFT))));
$eCobranca =stripslashes(utf8_decode($row->eCobranca.", N&ordm;: ".$row->nCobranca.", ".$row->bCobranca." - ".$row->cCobranca."-".$row->uCobranca." - CEP: ".CEP_Padrao(str_pad($row->ceCobranca, 8, 0, STR_PAD_LEFT))));
/* FIM DADOS DO CLIENTE */
foreach($rx as $c){
    if($c->Tipo == "Comercial"){
        $rComercial = utf8_decode($c->Responsavel);
        $eComercial = utf8_decode($c->Email);
        $tComercial = strlen($c->Telefone) == 11 ? Celular($c->Telefone) : Telefone($c->Telefone);
    }else{
        $rFinanceiro = utf8_decode($c->Responsavel);
        $eFinanceiro = utf8_decode($c->Email);
        $tFinanceiro = strlen($c->Telefone) == 11 ? Celular($c->Telefone) : Telefone($c->Telefone);
    }
}
$sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ?;";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idContrato);
$stmt->bindParam(2, $ct->dInicio);
$stmt->bindParam(3, $ct->dFinal);
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_OBJ);
$sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idContrato);
$stmt->execute();
$itens = $stmt->fetchAll(PDO::FETCH_OBJ);
$sql = "SELECT cd.Nome AS Empresa, cd.CNPJ AS CNPJ, cdu.Nome AS Unidade FROM contrato c INNER JOIN unidadefaturamento uf ON uf.idUnidadeFaturamento = c.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = uf.Cadastro_idCadastro INNER JOIN empresa e ON e.idEmpresa = c.Empresa_idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = e.Cadastro_idCadastro WHERE c.idContrato = ?;";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idContrato);
$stmt->execute();
$rjt = $stmt->fetch(PDO::FETCH_OBJ);
?>
<script>
    function rAnalitico (){
        window.open("<?php echo BASE; ?>medicao/RelatorioAnalitico.php?id=<?php echo $_GET['id'];?>");
    }
    function rSintetico (){
        window.open("<?php echo BASE; ?>medicao/RelatorioSintetico.php?id=<?php echo $_GET['id'];?>");
    }
    function rExcel (){
        window.open("<?php echo BASE; ?>medicao/Excel.php?id=<?php echo $_GET['id'];?>");
    }
    $(document).ready(function(){
        $('.input-daterange').datepicker({
            todayBtn: "linked",
            language: "pt-BR"
        })

        $("#success-alert").hide();
        $("#myWish").click(function showAlert() {
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                $("#success-alert").slideUp(500);
            });   
        });
    });
</script>
<div class="container-fluid">
    <div class="conteudo">

        <div class="col-xs-12 col-lg-12 col-md-12">
            <h1 class="text-center">MEDIÇÃO: <?php echo $ct->Medicao; ?></h1>
            <div class="panel panel-default text-justify">
                <div class="panel-heading">
                    <h1 class="panel-title">Dados Cliente</h1>
                </div>
                <div class="panel-body">
                    <div class="col-xs-8 col-md-8 col-lg-6"><p><strong>Nome: </strong><?php echo $Nome; ?></p></div>
                    <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>CNPJ: </strong><?php echo $CNPJ; ?></p></div>
                    <div class="col-xs-6 col-md-6 col-lg-6"><p><strong>IE: </strong><?php echo $IE; ?></p></div>
                    <div class="col-xs-6 col-md-6 col-lg-6"><p><strong>Centro de Custo: </strong><?php echo $Ccusto; ?></p></div>
                    <div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço: </strong><?php echo $Endereco; ?></p></div>	
                    <div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço de Cobranca: </strong><?php echo $eCobranca; ?></p></div>
                    <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>Responsável Comercial: </strong><?php echo $rComercial; ?></p></div>
                    <div class="col-xs-3 col-md-3 col-lg-3"><p><strong>Telefone: </strong><?php echo $tComercial; ?></p></div>
                    <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><?php echo $eComercial; ?></p></div>
                    <div class="col-xs-5 col-md-5 col-lg-5"><p><strong>Responsável Financeiro: </strong><?php echo $rFinanceiro; ?></p></div>
                    <div class="col-xs-3 col-md-3 col-lg-3"><p><strong>Telefone: </strong><?php echo $tFinanceiro; ?></p></div>
                    <div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><?php echo $eFinanceiro; ?></p></div>
                    <div class="col-xs-6 col-md-6 col-lg-6"><p><strong>Fechamento de Medição: </strong><?php echo utf8_decode($row->Descricao); ?></p></div>
                    <div class="col-xs-6 col-md-6 col-lg-6"><p><strong>Periodo Apurado: </strong><?php echo date("d/m/Y", strtotime($ct->dInicio)); ?> a <?php echo date("d/m/Y", strtotime($ct->dFinal)); ?></p></div>
                    <div class="col-xs-8 col-md-8 col-lg-8"><p><strong>Faturamento: </strong><?php echo utf8_decode($rjt->Empresa)." - ".CNPJ_Padrao($rjt->CNPJ); ?></p></div>
                    <div class="col-xs-4 col-md-4 col-lg-4"><strong>Unidade Faturamento: </strong><?php echo utf8_decode($rjt->Unidade); ?></div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-lg-12 col-md-12 text-center">
            <?php
            if($_GET['t'] === "v"){
            ?>
            <table class="table table-bordered text-center">
                <thead>
                    <tr class="inf_medicao">
                        <th>Data</th>
                        <?php foreach($itens as $i){ ?>
                        <th><?php echo utf8_decode($i->Servico);?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                $vTotal = 0;

                $dataInicial = $ct->dInicio;
                while(strtotime($dataInicial) <= strtotime($ct->dFinal)){
                    $diaSemana = date("D", strtotime($dataInicial));
                    $Dia = '';
                    switch($diaSemana){
                        case('Mon'):
                            $Dia = 'Seg';
                            break;
                        case('Tue'):
                            $Dia = 'Ter';
                            break;
                        case('Wed'):
                            $Dia = 'Qua';
                            break;
                        case('Thu'):
                            $Dia = 'Qui';
                            break;
                        case('Fri'):
                            $Dia = 'Sex';
                            break;
                        case('Sat'):
                            $Dia = 'Sab';
                            break;
                        case('Sun'):
                            $Dia = 'Dom';
                            break;
                    }
                    ?>
                    <tr>
                        <td class="dia_semana"><?php echo $Dia." ".date("d/m/Y", strtotime($dataInicial));?></td>
                    <?php
                    foreach($itens as $se){
                        $sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento = ? AND Servico = ?;";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $dataInicial);
                        $stmt->bindParam(3, $se->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetch(PDO::FETCH_OBJ);
                        ?>
                        <td><?php echo !empty($rs->Quantidade) ? $rs->Quantidade : '0'; ?></td>
                    <?php
                    }
                    ?>
                    </tr>
                    <?php
                    $dataInicial = date("Y-m-d",strtotime('+1 day', strtotime($dataInicial)));
                }
                
                foreach($itens as $i){

                        $sqli = "SELECT DISTINCT(ValorUni), (SUM(Quantidade)*ValorUni) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? GROUP BY ValorUni;";
                        $stmt = $conexao->prepare($sqli);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $ct->dInicio);
                        $stmt->bindParam(3, $ct->dFinal);
                        $stmt->bindParam(4, $i->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach($rs as $x){
                            $vTotal +=$x->Total;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <p>Valor Total da Medição: R$ <strong><?php echo number_format($vTotal,2,',','.'); ?></strong>.</p>
            <?php if($ct->Desconto > 0){ ?>

            <p>Valor Total do Desconto: R$ <strong><?php echo number_format($ct->Desconto,2,',','.'); ?></strong>.</p>
            <p>Valor Final da Medição: R$ <strong><?php echo number_format($vTotal-$ct->Desconto,2,',','.'); ?></strong>.</p>


            <?php } ?>
            <p>Período de apuração da medição de <strong><?php echo date('d/m/Y', strtotime($ct->dInicio)); ?></strong> até <strong><?php echo date('d/m/Y', strtotime($ct->dFinal)); ?></strong>.</p>
            <?php
            }elseif($_GET['t'] === "e"){
            ?>
            <h2 class="text-center">FORMULÁRIO PARA ALTERAÇÃO DE MEDIÇÃO</h2>
            <div class="form-group">
                <label class="col-sm-2 control-label">Período de Apuração:</label>
                <div class="col-sm-3">
                    <div class="input-daterange input-group" id="datepicker">
                        <input type="text" class="form-control" id="dataIN" name="dataIN" value="<?php echo date("d/m/Y", strtotime($ct->dInicio));?>" />
                        <span class="input-group-addon"> até </span>
                        <input type="text" class="form-control" id="dataFN" name="dataFN" data-date-end-date="0d" value="<?php echo date("d/m/Y", strtotime($ct->dFinal));?>"/>
                    </div>
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-warning" id="btnSData">Salvar Data <i class="far fa-save"></i></button>
                </div>
            </div>
            <div id="datas" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <div id="dResult"></div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(){
                    $("#btnSData").click(function(e){
                        var medicao = <?php echo $_GET['id']; ?>;//pegando o value do option selecionado
                        var dInicio = $('#dataIN').val();//pegando o value do option selecionado
                        var dFinal = $('#dataFN').val();//pegando o value do option selecionado
                        if(medicao != '' && dInicio != '' && dFinal != ''){
                            $.post('aMedicao.inc.php',{medicao: medicao, dInicio: dInicio, dFinal: dFinal} , function (dados){
                                //alert(dados);
                                dados = JSON.parse(dados);
                                //alert(dados);
                                if (dados.length > 0){
                                    $.each(dados, function(i, obj){
                                        if(obj.resultado == 'Sucesso'){
                                            $("#dResult").html('<div class="alert alert-success"><strong>Suceso!</strong> Sucesso ao alterar datas.</div>');
                                            $('#datas').modal('show');
                                            setTimeout(function() {
                                                $('#datas').modal('hide');
                                            }, 2750);
                                            setTimeout(function() {
                                                window.location.reload();
                                            }, 1500);
                                        }else{
                                            if(obj.resultado == 'Erro'){
                                                $("#dResult").html('<div class="alert alert-danger"><strong>Erro!</strong> Erro ao alterar datas.</div>');
                                                $('#datas').modal('show');
                                                setTimeout(function() {
                                                    $('#datas').modal('hide');
                                                }, 2750);
                                            }
                                        }
                                    })
                                }
                            })
                        }else{
                            $("#dResult").html('<div class="alert alert-warning"><strong>Erro!</strong> O valor não pode ser nulo.</div>');
                            $('#datas').modal('show');
                            setTimeout(function() {
                                $('#datas').modal('hide');
                            }, 2750);
                        }
                    })
                });
            </script>
            <div class="col-xs-12 col-md-12 col-lg-12"><hr /></div>
            <?php
                $vTotal = 0;
                foreach($rs as $x){
                    $Total = $x->Quantidade * $x->ValorUni;
                    $vTotal += $Total;
            ?>
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="col-xs-3 col-md-3 col-lg-3 text-justify">
                    <p><strong>Serviço: </strong><?php echo utf8_decode($x->Servico)?></p>
                </div>
                <div class="col-xs-3 col-md-3 col-lg-3">
                    <p><strong>Data: </strong><?php echo Muda_Data($x->dLancamento)?></p>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="col-sm-4"><label>Quantidade: </label></div><div class="col-sm-4"><input name="<?php echo $x->idLancamento; ?>" id="<?php echo $x->idLancamento; ?>" type="text" class="form-control" value="<?php echo $x->Quantidade; ?>" /></div>
                </div>
                <div class="col-xs-2 col-md-2 col-lg-2">
                    <button class="btn btn-primary" id="btn<?php echo $x->idLancamento; ?>">Editar Quantidade <i class="fas fa-edit"></i></button>
                </div>
                <div id="altera<?php echo $x->idLancamento; ?>" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-body">
                                <div id="result<?php echo $x->idLancamento; ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function(){
                        $("#btn<?php echo $x->idLancamento; ?>").click(function(e){
                            var lancamento = <?php echo $x->idLancamento; ?>;//pegando o value do option selecionado
                            var quant = $('#<?php echo $x->idLancamento; ?>').val();//pegando o value do option selecionado
                            if(quant != ''){
                                $.post('aMedicao.inc.php',{alterQuant: lancamento, quant: quant} , function (dados){
                                    //alert(dados);
                                    dados = JSON.parse(dados);
                                    //alert(dados);
                                    if (dados.length > 0){
                                        $.each(dados, function(i, obj){
                                            if(obj.resultado == 'Sucesso'){
                                                $("#result<?php echo $x->idLancamento; ?>").html('<div class="alert alert-success"><strong>Suceso!</strong> Sucesso ao alterar quantidade.</div>');
                                                $('#altera<?php echo $x->idLancamento; ?>').modal('show');
                                                setTimeout(function() {
                                                    $('#altera<?php echo $x->idLancamento; ?>').modal('hide');
                                                }, 2750);
                                            }else{
                                                if(obj.resultado == 'Erro'){
                                                    $("#result<?php echo $x->idLancamento; ?>").html('<div class="alert alert-danger"><strong>Erro!</strong> Erro ao alterar quantidade.</div>');
                                                    $('#altera<?php echo $x->idLancamento; ?>').modal('show');
                                                    setTimeout(function() {
                                                        $('#altera<?php echo $x->idLancamento; ?>').modal('hide');
                                                    }, 2750);
                                                }
                                            }
                                        })
                                    }
                                })
                            }else{
                                $("#result<?php echo $x->idLancamento; ?>").html('<div class="alert alert-warning"><strong>Erro!</strong> O valor não pode ser nulo.</div>');
                                $('#altera<?php echo $x->idLancamento; ?>').modal('show');
                                setTimeout(function() {
                                    $('#altera<?php echo $x->idLancamento; ?>').modal('hide');
                                }, 2750);
                            }
                        })
                    });
                </script>
                <hr />
            </div>
            <?php 
                }
            ?>
            <div class="col-xs-2 col-md-2 col-lg-2">
                <button class="btn btn-success" id="save<?php echo $_GET['id']; ?>">Salvar Alterações <i class="fas fa-save"></i></button>
            </div>    
            <div id="cReenviado" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <div id="vReenviado"></div>
                        </div>
                    </div>
                </div>
            </div>    
            <script>
                $(document).ready(function(){
                    $("#save<?php echo $_GET['id']; ?>").click(function(e){
                        var medicao = <?php echo $_GET['id']; ?>;//pegando o value do option selecionado
                        if(medicao != ''){
                            $.post('aMedicao.inc.php',{medicao1: medicao} , function (dados){
                                //alert(dados);
                                dados = JSON.parse(dados);
                                //alert(dados);
                                if (dados.length > 0){
                                    $.each(dados, function(i, obj){
                                        if(obj.resultado == 'Sucesso'){
                                            $("#vReenviado").html('<div class="alert alert-success"><strong>Suceso!</strong> Sucesso ao salvar alterações.</div>');
                                            $('#cReenviado').modal('show');
                                            setTimeout(function() {
                                                $('#cReenviado').modal('hide');
                                            }, 2750);
                                        }else{
                                            if(obj.resultado == 'Erro'){
                                                $("#vReenviado").html('<div class="alert alert-danger"><strong>Erro!</strong> Erro ao salvar alterações.</div>');
                                                $('#cReenviado').modal('show');
                                                setTimeout(function() {
                                                    $('#cReenviado').modal('hide');
                                                }, 2750);
                                            }
                                        }
                                    })
                                }
                            })
                        }else{
                            $("#vReenviado").html('<div class="alert alert-warning"><strong>Erro!</strong> O valor não pode ser nulo.</div>');
                            $('#cReenviado').modal('show');
                            setTimeout(function() {
                                $('#cReenviado').modal('hide');
                            }, 2750);
                        }
                    })
                });
            </script>
        </div>
        <?php
            }
            if($_GET['t'] == "v" && $ct->Situacao != "Aprovada"){
        ?>
        <div class="col-xs-12 col-md-12 col-lg-12 text-center">
            
            <?php
            if(true
            ){
                $id = array(1,36,37,44);
                if(in_array($_SESSION['idusuarios'],$id)){
            ?>
            <a class="btn btn-primary" target="_blank" href="RelatorioAnalitico.php?id=<?=$_GET['id'];?>">Relatório Analitico</a>
            <?php
                }
            ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmaMedicao">
                Validar
            </button>
            
            <?php
            }
            if(!empty($ct->idCoordenadora) && $ct->Situacao != "Aprovada" && !in_array($_SESSION['idusuarios'],$id2)){
                $sql = "SELECT m.Coordenadora, m.tCoordenadora, u.Nome FROM medicao m INNER JOIN usuarios u ON u.idUsuarios = m.idCoordenadora WHERE m.idMedicao = ?;";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $_GET['id']);
                $stmt->execute();
                $rx = $stmt->fetch(PDO::FETCH_OBJ);
            ?>
            <p>Medição foi <strong><?=utf8_decode($rx->Coordenadora);?></strong> por <strong><?=utf8_decode($rx->Nome);?></strong> no dia <?=date("d/m/Y \à\s H:i:s", strtotime($rx->tCoordenadora));?>, aguardando validação do <strong>FATURAMENTO</strong>.</p>
            <?php
            }
            ?>
        </div>
        <?php
            }
            if($_GET['t'] == "v" && $ct->Situacao === "Aprovada"){
                $id3 = array(1,36,37,39,42,44);
                if(in_array($_SESSION['idusuarios'],$id3)){
        ?>
        <div class="col-xs-12 col-md-12 col-lg-12">
            <p><strong>Observação:</strong> <?php echo utf8_decode($row->Obs); ?></p>
        </div>
        <div class="col-xs-12 col-md-12 col-lg-12">
                <p><strong>Quantidade Mínima:</strong> <?php echo utf8_decode($row->ConsumacaoMinima); ?></p>
            <?php
                
            $sqlq = 'SELECT SUM(Quantidade) AS Quantidade FROM lancamento WHERE dLancamento BETWEEN ? AND ? AND ValorUni > 9 AND Contrato_idContrato = ?';
            $stmt = $conexao->prepare($sqlq);
            $stmt->bindParam(1, $ct->dInicio);
            $stmt->bindParam(2, $ct->dFinal);
            $stmt->bindParam(3, $idContrato);
            $stmt->execute();
            $rsq = $stmt->fetch(PDO::FETCH_OBJ);
            $val = '%Desjejum%';
            $sqlq1 = "SELECT SUM(Quantidade) AS Quantidade FROM lancamento WHERE dLancamento BETWEEN ? AND ? AND Servico LIKE ? AND Contrato_idContrato = ?";
            $stmt = $conexao->prepare($sqlq1);
            $stmt->bindParam(1, $ct->dInicio);
            $stmt->bindParam(2, $ct->dFinal);
            $stmt->bindParam(3, $val);
            $stmt->bindParam(4, $idContrato);
            $stmt->execute();
            $rsq1 = $stmt->fetch(PDO::FETCH_OBJ);
            ?>
                <p><strong>Quantidade Consumida:</strong> <?php echo utf8_decode($rsq->Quantidade); ?></p>
                 <p><strong>Quantidade Consumida Desjejum:</strong> <?php echo !empty($rsq1->Quantidade) ? utf8_decode($rsq1->Quantidade) : '0'; ?></p>
            </div>

        <div class="col-xs-12 col-md-12 col-lg-12 text-center">
            <a class="btn btn-primary" onclick="rAnalitico()">
                Emitir Relatório Analítico
            </a>
            <a class="btn btn-primary" onclick="rSintetico()">
                Emitir Relatório Sintético
            </a>
            <a class="btn btn-primary" onclick="rExcel()">
                Emitir Relatório em Excel
            </a>
        </div>
        <?php
                }
            }
        ?>
        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
    </div>
</div>
<div id="confirmaMedicao" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">VALIDAR MEDIÇÃO</h4>
            </div>
            <div class="modal-body body-medicao">
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="vContrato">Contrato:</label>
                        <input type="text" class="form-control" value="<?php echo $row->nContrato; ?>" id="vContrato" name="vContrato" readonly>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="vMedicao">Medição Nº:</label>
                        <input type="text" class="form-control" value="<?php echo $ct->Medicao; ?>" id="vMedicao" name="vMedicao" readonly>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="iMedicao">Valor da Medição:</label>
                        <input type="text" class="form-control" value="R$ <?php echo number_format($vTotal,2,',','.');?>" id="vTotal" name="vTotal" readonly>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="iMedicao">Início da Medição:</label>
                        <input type="text" class="form-control" value="<?php echo date("d/m/Y", strtotime($ct->dInicio)); ?>" id="iMedicao" name="iMedicao" readonly>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="fMedicao">Fim da Medição:</label>
                        <input type="text" class="form-control" value="<?php echo date("d/m/Y", strtotime($ct->dFinal)); ?>" id="fMedicao" name="fMedicao" readonly>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-12">
                    <div class="col-xs-3 col-md-3 col-lg-3">
                        <form action="<?php echo BASE;?>control/banco/vMedicaoDAO.php" method="post" enctype="multipart/form-data" class="form-inline" data-toggle="validator">
                            <input type="hidden" value="Aprovada" name="voto">
                            <input type="hidden" value="voto" name="Medicao">
                            <input type="hidden" value="<?php echo $_GET['id'];?>" name="idMedicao">
                            <button type="submit" class="btn btn-success" >Aprovar Medição</button>
                        </form>
                    </div>
                    <div class="col-xs-9 col-md-9 col-lg-9">
                        <form action="<?php echo BASE;?>control/banco/vMedicaoDAO.php" method="post" enctype="multipart/form-data"  class="form-inline" data-toggle="validator" >
                            <div class="form-group">
                                <label for="voto">Motivo:</label>
                                <input type="text" required maxlength="33" minlength="16" name="voto" id="voto" class="form-control"/>
                            </div>
                            <input type="hidden" value="voto" name="Medicao">
                            <input type="hidden" value="<?php echo $_GET['id'];?>" name="idMedicao">
                            <input type="hidden" value="Recusada" name="recusada">
                            <button type="submit" class="btn btn-danger">Recusar Medição</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="confirmaMedicaoCoordenacao" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">VALIDAR MEDIÇÃO</h4>
            </div>
            <div class="modal-body body-medicao">
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="vContrato">Contrato:</label>
                        <input type="text" class="form-control" value="<?php echo $row->nContrato; ?>" id="vContrato" name="vContrato" readonly>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="vMedicao">Medição Nº:</label>
                        <input type="text" class="form-control" value="<?php echo $ct->Medicao; ?>" id="vMedicao" name="vMedicao" readonly>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="iMedicao">Valor da Medição:</label>
                        <input type="text" class="form-control" value="R$ <?php echo number_format($vTotal,2,',','.');?>" id="vTotal" name="vTotal" readonly>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="iMedicao">Início da Medição:</label>
                        <input type="text" class="form-control" value="<?php echo date("d/m/Y", strtotime($ct->dInicio)); ?>" id="iMedicao" name="iMedicao" readonly>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="fMedicao">Fim da Medição:</label>
                        <input type="text" class="form-control" value="<?php echo date("d/m/Y", strtotime($ct->dFinal)); ?>" id="fMedicao" name="fMedicao" readonly>
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