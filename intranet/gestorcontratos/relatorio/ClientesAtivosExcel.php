<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: HTTP://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<script>
    $(document).ready(function(){
        //Carrega Colaboradores
        $("input[name='Buscar']").change(function(e){
            if($("input[name='Buscar']").prop("checked")){
                var contrato = $('#contrato').val();//pegando o value do option selecionado
                var dataIN = $('#dataIN').val();//pegando o value do option selecionado
                var dataFN = $('#dataFN').val();//pegando o value do option selecionado
                if(contrato != '' && dataIN != '' && dataFN != ''){
                    $.post('Parcial.inc.php',{contrato: contrato, dataIN: dataIN, dataFN: dataFN} , function (dados){
                        //alert(dados);
                        dados = JSON.parse(dados);
                        //alert(dados);
                        if (dados.length > 0){
                            var option = '';
                            var total = parseFloat(0);
                            $.each(dados, function(i, obj){
                                var t = parseFloat(obj.Total);
                                option += '<tr>';
                                option += '<td>'+obj.Servico+'</td>';
                                option += '<td>'+obj.Data+'</td>';
                                option += '<td>'+obj.ValorUni+'</td>';
                                option += '<td>'+obj.Quant+'</td>';
                                option += '<td class="text-right"> R$ '+t.toLocaleString('pt-BR')+'</td>';
                                option += '</tr>';
                                total += parseFloat(obj.Total);
                            })
                            $('#itens-lancamento').html(option).show();
                            $('#Total').html(total.toLocaleString('pt-BR')).show();
                            setTimeout(function() {
                                $("input[name='Buscar']").prop("checked",false);
                            }, 1500);
                        }else{
                            $('#Total').html("aguardando...").show();
                            var op = '';
                            op += '<tr>';
                            op += '<td colspan="5"><i class="fas fa-times"></i> Sem dados para exibir.</i></td>';
                            op += '</tr>';
                            $('#itens-lancamento').html(op).show();
                            setTimeout(function() {
                                $("input[name='Buscar']").prop("checked",false);
                            }, 1500);
                            Reset();
                        }
                    })
                }else{
                    $('#Total').html("aguardando...").show();
                    var op = '';
                    op += '<tr>';
                    op += '<td colspan="5"><i class="fas fa-times"></i> Sem dados para exibir.</i></td>';
                    op += '</tr>';
                    $('#itens-lancamento').html(op).show();
                    setTimeout(function() {
                        $("input[name='Buscar']").prop("checked",false);
                    }, 1500);
                    return false;
                }
            }else{
                $('#Total').html("aguardando...").show();
                var op = '';
                op += '<tr>';
                op += '<td colspan="5"><i class="fas fa-times"></i> Sem dados para exibir.</i></td>';
                op += '</tr>';
                $('#itens-lancamento').html(op).show();
                setTimeout(function() {
                    $("input[name='Buscar']").prop("checked",false);
                }, 1500);
                return false;
            }
        })
        <!-- Resetar Selects -->
        function Reset(){
            $('#Total').html("aguardando...").show();
            var op = '';
            op += '<tr>';
            op += '<td colspan="5">Aguardando...<i class="fa fa-coffee" aria-hidden="true"></i></td>';
            op += '</tr>';
            $('#itens-lancamento').html(op).show();
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
					$.each(dados, function(i, obj){
						nMedicao = obj.nMedicao;
						Contrato = obj.Contrato;
						pFechamento = obj.pFechamento;
					})
					$('#nContrato').val(Contrato).show();
					$('#nMedicao').val(nMedicao).show();
					$('#pFechamento').val(pFechamento).show();
				}else{
					Reset1();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset1(){
			$('#contrato').val("").show();
            $('#nMedicao').val("").show();
            $('#pFechamento').val("").show();
		}
        $('.input-daterange').datepicker({
            todayBtn: "linked",
            language: "pt-BR"
        })
        $("#gMedicao").click(function(e){
            $('#vContrato').val($('#nContrato').val());
            $('#vMedicao').val($('#nMedicao').val());
            $('#iMedicao').val($('#dataIN').val());
            $('#fMedicao').val($('#dataFN').val());
           
        })
    });
</script>
<div class="container-fluid">
    <div class="conteudo">
        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h1 class="text-center">CLIENTES ATIVOS</h1></div>
        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
        <form  name="Form" role="form" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente" >
            <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="Unidade">Unidade: </label>
                    <div class="col-sm-5">
                        <select class="selectpicker form-control dropdown" required name="Unidade" id="Unidade" title="Selecione um Unidade" data-size="5" data-live-search="true" <?php echo isset($_POST['Fechamento']) && $_POST['Fechamento'] == "Unidade" ? "disabled" : ''; ?> >
                            <option <?php echo isset($_POST['Fechamento']) && $_POST['Fechamento'] == "Unidade" && $_POST['Unidade'] == 'Todas' ? "selected" : ''; ?> value="Todas">TODAS</option>
                            <?php
                            $sql = 'SELECT ca.CNPJ, ca.Nome FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro';
                            $stm = $conexao->prepare($sql);
                            $stm->execute();
                            $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                            foreach($rs as $r){
                            ?>
                            <optgroup label="<?php echo CNPJ_Padrao($r->CNPJ).'-'.utf8_decode($r->Nome); ?>" >
                                <?php
                                $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? AND un.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $r->CNPJ);
                                $stm->bindParam(2, $_SESSION['idusuarios']);
                                $stm->execute();
                                while($row = $stm->fetch(PDO::FETCH_OBJ)){
                                ?>
                                <option <?php echo isset($_POST['Fechamento']) && $_POST['Fechamento'] == "Unidade" && $_POST['Unidade'] == $row->idUnidadeFaturamento ? "selected" : ''; ?> value="<?php echo $row->idUnidadeFaturamento; ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                
                <?php
                if(!isset($_POST['Unidade'])){
                ?>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="Unidade">Período: </label>
                    <div class="col-sm-5">
                        <div class="input-daterange input-group" id="datepicker">
                            <input type="text" class="form-control" id="dataIN" name="dataIN" required />
                            <span class="input-group-addon"> até </span>
                            <input type="text" class="form-control" id="dataFN" name="dataFN" data-date-end-date="0d" required />
                        </div>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <?php
                }
                ?>
                
                <div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-1 col-md-offset-1 col-lg-offset-1">
                    <?php
                    if(isset($_POST['Fechamento']) && $_POST['Fechamento'] == "Unidade"){
                    ?>
                    <input type="hidden" value="Nova" name="Fechamento" />
                    <button class="btn btn-success" type="submit">Nova Unidade</button>
                    <?php
                    }else{
                    ?>
                    <input type="hidden" value="Unidade" name="Fechamento" />
                    <button class="btn btn-success" type="submit">Selecionar</button>
                    <?php
                    }	
                    ?>
                </div>
            </div>
            <div class="col-xs-12 col-md-12 col-lg-12"> </div>
            <div class="panel-group col-xs-12 col-md-12 col-lg-12" id="accordion">
            <?php
            $valor2x = explode('/',$_POST['dataIN']);
            $valor2 = $valor2x[2]."-".$valor2x[1]."-".$valor2x[0];
            $valor3x = explode('/',$_POST['dataFN']);
            $valor3 = $valor3x[2]."-".$valor3x[1]."-".$valor3x[0];
            $daInicio = $valor2;
            $daFinal = $valor3;
            if(isset($_POST['Fechamento']) && $_POST['Fechamento'] == "Unidade"){
                if($_POST['Unidade'] == 'Todas'){
                     $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
                    $stm = $conexao->prepare($sql);
                    $stm->bindParam(1, $_SESSION['idusuarios']);
                    $stm->execute();
                    $row = $stm->fetchAll(PDO::FETCH_OBJ);
                    foreach($row as $rrt){
                        ?>
                <div class="panel-group">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title text-left">
                        <a data-toggle="collapse" href="#collapse-u<?=$rrt->idUnidadeFaturamento;?>">UNIDADE: <?=$rrt->Nome;?></a>
                      </h4>
                    </div>
                    <div id="collapse-u<?=$rrt->idUnidadeFaturamento;?>" class="panel-collapse collapse">
                        <table class="table table-bordered table-striped table-responsive">
                            <thead>
                                <tr class="text-justify">
                                    <th>CLIENTE</th>
                                    <th>CENTRO DE CUSTO</th>
                                    <th>CONTRATO</th>
                                    <th>FECHAMENTO</th>
                                    <th>PERÍODO</th>
                                    <th>PREVISÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                        $sql = "SELECT idContrato, Fechamento FROM contrato c WHERE c.Unidade_idUnidade = ? AND c.Finalizado = 'N' ORDER BY Contratante_idContratante";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bindParam(1, $rrt->idUnidadeFaturamento);
                        $stmt->execute();
                        $rest = $stmt->fetchAll(PDO::FETCH_OBJ);
                        $total = 0;
                        $dFinal = 0;
                        $dInicio = 0;
                        
                        foreach($rest as $r){
                            $sqlM = "SELECT * FROM medicao WHERE idMedicao = (SELECT MAX(idMedicao) FROM medicao WHERE Contrato_idContrato = ?);";
                            $stmtM = $conexao->prepare($sqlM);
                            $stmtM->bindParam(1, $r->idContrato);
                            $stmtM->execute();
                            $result = $stmtM->fetch(PDO::FETCH_OBJ);
                            /*if($stmtM->rowCount() > 0){
                            }else{
                                break;
                            }
                            switch( $r->Fechamento ){
                                case 1:
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dFinal = $x[1]."-".$x[0]."-05";
                                    break;
                                case 2:
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dFinal = $x[1]."-".$x[0]."-10";
                                    break;
                                case 3:
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dFinal = $x[1]."-".$x[0]."-15";
                                    break;
                                case 4:
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dFinal = $x[1]."-".$x[0]."-19";
                                    break;
                                case 5:
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dFinal = $x[1]."-".$x[0]."-25";
                                    break;
                                case 6:
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dFinal = $x[1]."-".$x[0]."-26";
                                    break;
                                case 7:
                                    if(date("d", strtotime($result->dFinal)) == 10){
                                        $mes_ano = date("m/Y",strtotime($result->dFinal));
                                        $x = explode("/",$mes_ano);
                                        $dInicio = $x[1]."-".$x[0]."-11";
                                        $dFinal = $x[1]."-".$x[0]."-25";
                                    }elseif(date("d", strtotime($result->dFinal)) == 25){
                                        $mes_ano = date("m/Y",strtotime($result->dFinal));
                                        $x = explode("/",$mes_ano);
                                        $dInicio = $x[1]."-".$x[0]."-26";
                                        $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                        $x = explode("/",$mes_ano);
                                        $dFinal = $x[1]."-".$x[0]."-10";
                                    }
                                    break;
                                case 8:
                                    //Dia 15 e dia 25
                                    if(date("d", strtotime($result->dFinal)) == 15){
                                        $mes_ano = date("m/Y",strtotime($result->dFinal));
                                        $x = explode("/",$mes_ano);
                                        $dInicio = $x[1]."-".$x[0]."-16";
                                        $dFinal = $x[1]."-".$x[0]."-25";
                                    }elseif(date("d", strtotime($result->dFinal)) == 25){
                                        $mes_ano = date("m/Y",strtotime($result->dFinal));
                                        $x = explode("/",$mes_ano);
                                        $dInicio = $x[1]."-".$x[0]."-26";
                                        $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                        $x = explode("/",$mes_ano);
                                        $dFinal = $x[1]."-".$x[0]."-15";
                                    }
                                    break;
                                case 9:
                                    //Dia 10 e último dia do mês
                                    $ultimo_dia = date("t", mktime(0,0,0,date("m",strtotime($res->dFinal)),'01',date("Y",strtotime($result->dFinal))));
                                    if(date("d", strtotime($result->dFinal)) == 10){
                                        $mes_ano = date("m/Y",strtotime($result->dFinal));
                                        $x = explode("/",$mes_ano);
                                        $dInicio = $x[1]."-".$x[0]."-11";

                                        $dFinal = $x[1]."-".$x[0]."-".$ultimo_dia;
                                    }elseif(date("d", strtotime($result->dFinal)) == $ultimo_dia){
                                        $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                        $x = explode("/",$mes_ano);
                                        $dInicio = $x[1]."-".$x[0]."-01";
                                        $dFinal = $x[1]."-".$x[0]."-10";
                                    }
                                    break;
                                case 10:
                                    
                                    $ultimo_dia = date("t", mktime(0,0,0,date("m",strtotime($result->dFinal)),'01',date("Y",strtotime($result->dFinal))));
                                    if(date("d", strtotime($result->dFinal)) == 15){
                                        $mes_ano = date("m/Y",strtotime($result->dFinal));
                                        $x = explode("/",$mes_ano);
                                        $dInicio = $x[1]."-".$x[0]."-16";
                                        $dFinal = $x[1]."-".$x[0]."-".$ultimo_dia;
                                    }elseif(date("d", strtotime($result->dFinal)) == $ultimo_dia){
                                        $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                        $x = explode("/",$mes_ano);
                                        $dInicio = $x[1]."-".$x[0]."-01";
                                        $dFinal = $x[1]."-".$x[0]."-15";
                                    }
                                    break;
                                case 11:
                                    $data = $result->dInicio;
                                    
                                    $mes = date("m",strtotime('+ 1 MONTH', strtotime($data)));
                                    $ano = date("Y");
                                    $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
                                    $mes = date("d/m/Y",strtotime('+ 1 MONTH', strtotime($data)));
                                    $x = explode("/",$mes);
                                    $x[0] = $ultimo_dia;
                                    $fim = $x[2]."-".$x[1]."-".$x[0];
                                    $dInicio = date("Y-m-d",strtotime('+ 1 MONTH', strtotime($data)));
                                    $dFinal  = date("Y-m-d",strtotime($fim));
                                    $fim  = date("d/m/Y",strtotime($fim));
                                    break;
                            }*/
                            switch( $r->Fechamento ){
                                case 1:
                                    $fecha = "Todo dia 05";
                                    break;
                                case 2:
                                    $fecha = "Todo dia 10";
                                    break;
                                case 3:
                                    $fecha = "Todo dia 15";
                                    break;
                                case 4:
                                    $fecha = "Todo dia 19";
                                    break;
                                case 5:
                                    $fecha = "Todo dia 25";
                                    break;
                                case 6:
                                    $fecha = "Todo dia 26";
                                    break;
                                case 7:
                                    $fecha = "Dia 10 e dia 25";
                                    break;
                                case 8:
                                    $fecha = "Dia 15 e dia 25";
                                    break;
                                case 9:
                                    $fecha = "Dia 10 e último dia do mês";
                                    break;
                                case 10:
                                    $fecha = "Dia 15 e último dia do mês";
                                    break;
                                case 11:
                                    $fecha = "Último dia do mês";
                                    break;
                            }
                            $sql2 = "SELECT * FROM contrato c INNER JOIN contratante cc ON cc.idContratante = c.Contratante_idContratante INNER JOIN cadastro cd ON cd.idCadastro = cc.Cadastro_idCadastro WHERE c.idContrato = ? AND Finalizado = 'N';";
                            $stm1 = $conexao->prepare($sql2);
                            $stm1->bindParam( 1, $r->idContrato );
                            $stm1->execute();
                            $re = $stm1->fetch(PDO::FETCH_OBJ);
                            $sql3 = "SELECT (SUM(Quantidade) * ValorUni) AS 'VALOR_TOTAL' FROM lancamento WHERE dLancamento BETWEEN ? AND ? AND Contrato_idContrato = ? GROUP BY ValorUni, Servico;";
                            $stm3 = $conexao->prepare($sql3);
                            $stm3->bindParam( 1, $daInicio );
                            $stm3->bindParam( 2, $daFinal );
                            $stm3->bindParam( 3, $r->idContrato );
                            $stm3->execute();
                            $previ = $stm3->fetchAll(PDO::FETCH_OBJ);
                            $previsao = 0;
                            foreach($previ as $p){
                                $previsao += $p->VALOR_TOTAL;
                            }
                            ?>
                            <tr>
                                <td><?=utf8_decode($re->Nome)?></td>
                                <td><?=utf8_decode($re->cCusto)?></td>
                                <td><?=utf8_decode($re->nContrato)?></td>
                                <td><?=mb_strtoupper($fecha)?></td>
                                <td><?=date("d/m/Y", strtotime($daInicio))." até ".date("d/m/Y", strtotime($daFinal))?></td>
                                <td>R$ <?=number_format($previsao,2,',','.')?></td>
                            </tr>
                            <?php
                            $total += $previsao;
                        }
                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">PREVISÃO DE FATURAMENTO PARA A UNIDADE:</td>
                                    <td>R$ <?=number_format($total,2,',','.')?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                  </div>
                </div>
                <?php
                    }
                }else{
                    $sql4 = "SELECT Nome FROM unidadefaturamento INNER JOIN cadastro ON idCadastro = Cadastro_idCadastro WHERE idUnidadeFaturamento = ?;";
                    $stm4 = $conexao->prepare($sql4);
                    $stm4->bindParam(1, $_POST['Unidade']);
                    $stm4->execute();
                    $uf = $stm4->fetch(PDO::FETCH_OBJ);
                    ?>
                    <table class="table table-bordered table-striped table-responsive" >
                        <thead>
                            <tr class="nome-unidade"><td colspan="6"><strong><?=utf8_decode(mb_strtoupper($uf->Nome))?></strong></td></tr>
                            <tr class="text-center">
                                <th>CLIENTE</th>
                                <th>CENTRO DE CUSTO</th>
                                <th>CONTRATO</th>
                                <th>FECHAMENTO</th>
                                <th>PERÍODO</th>
                                <th>PREVISÃO</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php
                    $sql = "SELECT idContrato, Fechamento FROM contrato c WHERE c.Unidade_idUnidade = ? AND c.Finalizado = 'N' ORDER BY Contratante_idContratante";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindParam(1, $_POST['Unidade']);
                    $stmt->execute();
                    $rest = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $total = 0;
                    $dFinal = 0;
                    $dInicio = 0;

                    foreach($rest as $r){
                        $sqlM = "SELECT * FROM medicao WHERE idMedicao = (SELECT MAX(idMedicao) FROM medicao WHERE Contrato_idContrato = ?);";
                        $stmtM = $conexao->prepare($sqlM);
                        $stmtM->bindParam(1, $r->idContrato);
                        $stmtM->execute();
                        $result = $stmtM->fetch(PDO::FETCH_OBJ);
                        /*if($stmtM->rowCount() > 0){
                        }else{
                            break;
                        }
                        switch( $r->Fechamento ){
                            case 1:
                                $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                $x = explode("/",$mes_ano);
                                $dFinal = $x[1]."-".$x[0]."-05";
                                break;
                            case 2:
                                $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                $x = explode("/",$mes_ano);
                                $dFinal = $x[1]."-".$x[0]."-10";
                                break;
                            case 3:
                                $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                $x = explode("/",$mes_ano);
                                $dFinal = $x[1]."-".$x[0]."-15";
                                break;
                            case 4:
                                $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                $x = explode("/",$mes_ano);
                                $dFinal = $x[1]."-".$x[0]."-19";
                                break;
                            case 5:
                                $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                $x = explode("/",$mes_ano);
                                $dFinal = $x[1]."-".$x[0]."-25";
                                break;
                            case 6:
                                $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                $dInicio = date("Y-m-d",strtotime('+ 1 DAY', strtotime($result->dFinal)));
                                $x = explode("/",$mes_ano);
                                $dFinal = $x[1]."-".$x[0]."-26";
                                break;
                            case 7:
                                if(date("d", strtotime($result->dFinal)) == 10){
                                    $mes_ano = date("m/Y",strtotime($result->dFinal));
                                    $x = explode("/",$mes_ano);
                                    $dInicio = $x[1]."-".$x[0]."-11";
                                    $dFinal = $x[1]."-".$x[0]."-25";
                                }elseif(date("d", strtotime($result->dFinal)) == 25){
                                    $mes_ano = date("m/Y",strtotime($result->dFinal));
                                    $x = explode("/",$mes_ano);
                                    $dInicio = $x[1]."-".$x[0]."-26";
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dFinal = $x[1]."-".$x[0]."-10";
                                }
                                break;
                            case 8:
                                //Dia 15 e dia 25
                                if(date("d", strtotime($result->dFinal)) == 15){
                                    $mes_ano = date("m/Y",strtotime($result->dFinal));
                                    $x = explode("/",$mes_ano);
                                    $dInicio = $x[1]."-".$x[0]."-16";
                                    $dFinal = $x[1]."-".$x[0]."-25";
                                }elseif(date("d", strtotime($result->dFinal)) == 25){
                                    $mes_ano = date("m/Y",strtotime($result->dFinal));
                                    $x = explode("/",$mes_ano);
                                    $dInicio = $x[1]."-".$x[0]."-26";
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dFinal = $x[1]."-".$x[0]."-15";
                                }
                                break;
                            case 9:
                                //Dia 10 e último dia do mês
                                $ultimo_dia = date("t", mktime(0,0,0,date("m",strtotime($res->dFinal)),'01',date("Y",strtotime($result->dFinal))));
                                if(date("d", strtotime($result->dFinal)) == 10){
                                    $mes_ano = date("m/Y",strtotime($result->dFinal));
                                    $x = explode("/",$mes_ano);
                                    $dInicio = $x[1]."-".$x[0]."-11";

                                    $dFinal = $x[1]."-".$x[0]."-".$ultimo_dia;
                                }elseif(date("d", strtotime($result->dFinal)) == $ultimo_dia){
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dInicio = $x[1]."-".$x[0]."-01";
                                    $dFinal = $x[1]."-".$x[0]."-10";
                                }
                                break;
                            case 10:

                                $ultimo_dia = date("t", mktime(0,0,0,date("m",strtotime($result->dFinal)),'01',date("Y",strtotime($result->dFinal))));
                                if(date("d", strtotime($result->dFinal)) == 15){
                                    $mes_ano = date("m/Y",strtotime($result->dFinal));
                                    $x = explode("/",$mes_ano);
                                    $dInicio = $x[1]."-".$x[0]."-16";
                                    $dFinal = $x[1]."-".$x[0]."-".$ultimo_dia;
                                }elseif(date("d", strtotime($result->dFinal)) == $ultimo_dia){
                                    $mes_ano = date("m/Y",strtotime('+ 1 MONTH', strtotime($result->dFinal)));
                                    $x = explode("/",$mes_ano);
                                    $dInicio = $x[1]."-".$x[0]."-01";
                                    $dFinal = $x[1]."-".$x[0]."-15";
                                }
                                break;
                            case 11:
                                $data = $result->dInicio;

                                $mes = date("m",strtotime('+ 1 MONTH', strtotime($data)));
                                $ano = date("Y");
                                $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
                                $mes = date("d/m/Y",strtotime('+ 1 MONTH', strtotime($data)));
                                $x = explode("/",$mes);
                                $x[0] = $ultimo_dia;
                                $fim = $x[2]."-".$x[1]."-".$x[0];
                                $dInicio = date("Y-m-d",strtotime('+ 1 MONTH', strtotime($data)));
                                $dFinal  = date("Y-m-d",strtotime($fim));
                                $fim  = date("d/m/Y",strtotime($fim));
                                break;
                        }*/
                        switch( $r->Fechamento ){
                            case 1:
                                $fecha = "Todo dia 05";
                                break;
                            case 2:
                                $fecha = "Todo dia 10";
                                break;
                            case 3:
                                $fecha = "Todo dia 15";
                                break;
                            case 4:
                                $fecha = "Todo dia 19";
                                break;
                            case 5:
                                $fecha = "Todo dia 25";
                                break;
                            case 6:
                                $fecha = "Todo dia 26";
                                break;
                            case 7:
                                $fecha = "Dia 10 e dia 25";
                                break;
                            case 8:
                                $fecha = "Dia 15 e dia 25";
                                break;
                            case 9:
                                $fecha = "Dia 10 e último dia do mês";
                                break;
                            case 10:
                                $fecha = "Dia 15 e último dia do mês";
                                break;
                            case 11:
                                $fecha = "Último dia do mês";
                                break;
                        }
                        $sql2 = "SELECT * FROM contrato c INNER JOIN contratante cc ON cc.idContratante = c.Contratante_idContratante INNER JOIN cadastro cd ON cd.idCadastro = cc.Cadastro_idCadastro WHERE c.idContrato = ? AND Finalizado = 'N';";
                        $stm1 = $conexao->prepare($sql2);
                        $stm1->bindParam( 1, $r->idContrato );
                        $stm1->execute();
                        $re = $stm1->fetch(PDO::FETCH_OBJ);
                        $sql3 = "SELECT (SUM(Quantidade) * ValorUni) AS 'VALOR_TOTAL' FROM lancamento WHERE dLancamento BETWEEN ? AND ? AND Contrato_idContrato = ? GROUP BY ValorUni, Servico;";
                        $stm3 = $conexao->prepare($sql3);
                        $stm3->bindParam( 1, $daInicio );
                        $stm3->bindParam( 2, $daFinal );
                        $stm3->bindParam( 3, $r->idContrato );
                        $stm3->execute();
                        $previ = $stm3->fetchAll(PDO::FETCH_OBJ);
                        $previsao = 0;
                        foreach($previ as $p){
                            $previsao += $p->VALOR_TOTAL;
                        }
                        ?>
                        <tr>
                            <td><?=utf8_decode($re->Nome)?></td>
                            <td class=""><?=utf8_decode($re->cCusto)?></td>
                            <td><?=utf8_decode($re->nContrato)?></td>
                            <td><?=mb_strtoupper($fecha)?></td>
                            <td><?=date("d/m/Y", strtotime($daInicio))." até ".date("d/m/Y", strtotime($daFinal))?></td>
                            <td>R$ <?=number_format($previsao,2,',','.')?></td>
                        </tr>
                        <?php
                        $total += $previsao;
                    }
            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">PREVISÃO DE FATURAMENTO PARA A UNIDADE:</td>
                                <td>R$ <?=number_format($total,2,',','.')?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php
                    }
                } 
            ?>
            </div>
        </form>
        <div class="col-xs-12 col-md-12 col-lg-12"><hr /></div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>