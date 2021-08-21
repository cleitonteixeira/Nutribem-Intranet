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
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h1 class="text-center">AUDITORIA MEDICOES</h1></div>
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
                            foreach($rs as $r):
                            ?>
                            <optgroup label="<?php echo CNPJ_Padrao($r->CNPJ).'-'.utf8_decode($r->Nome); ?>" >
                                <?php
                                $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? AND un.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $r->CNPJ);
                                $stm->bindParam(2, $_SESSION['idusuarios']);
                                $stm->execute();
                                while($row = $stm->fetch(PDO::FETCH_OBJ)):
                                ?>
                                <option <?php echo isset($_POST['Fechamento']) && $_POST['Fechamento'] == "Unidade" && $_POST['Unidade'] == $row->idUnidadeFaturamento ? "selected" : ''; ?> value="<?php echo $row->idUnidadeFaturamento; ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                <?php endwhile; ?>
                            </optgroup>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
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
                        <?php
                        $sql = "SELECT idContrato, Fechamento FROM contrato c WHERE c.Unidade_idUnidade = ? AND c.Finalizado = 'N'";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bindParam(1, $rrt->idUnidadeFaturamento);
                        $stmt->execute();
                        $rest = $stmt->fetchAll(PDO::FETCH_OBJ);
                        $contador = 0;
                        $dFinal = 0;
                        $dInicio = 0;
                        foreach($rest as $r){
                            $sqlM = "SELECT * FROM medicao WHERE idMedicao = (SELECT MAX(idMedicao) FROM medicao WHERE Contrato_idContrato = ?);";
                            $stmtM = $conexao->prepare($sqlM);
                            $stmtM->bindParam(1, $r->idContrato);
                            $stmtM->execute();
                            if($stmtM->rowCount() > 0){
                                $result = $stmtM->fetch(PDO::FETCH_OBJ);
                            }else{
                                break;
                            }
                            
                            //print_r($result);
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
                            }
                            $hoje = date("Y-m-d");
                            //echo $dFinal." -- ".$hoje;
                            if(strtotime($dFinal) > strtotime($hoje)){
                                break;   
                            }
                            $sql2 = "SELECT * FROM medicao m INNER JOIN contrato c ON c.idContrato = m.Contrato_idContrato INNER JOIN contratante cc ON cc.idContratante = c.Contratante_idContratante INNER JOIN fechamento f ON f.idFechamento = c.Fechamento INNER JOIN cadastro cd ON cd.idCadastro = cc.Cadastro_idCadastro WHERE m.Contrato_idContrato = ?;";
                            $stm1 =$conexao->prepare($sql2);
                            $stm1->bindParam( 1, $r->idContrato );
                            $stm1->execute();
                            if($stm1->rowCount()>0){
                                $res = $stm1->fetchAll(PDO::FETCH_OBJ);
                                $x = '';
                                foreach($res as $r){
                                    $dInicio = $r->dInicio;
                                    while($dInicio <= $r->dFinal){
                                        if($dInicio != $r->dFinal){
                                            $x .= "'".$dInicio."',";
                                        }else{
                                            $x .= "'".$dInicio."',";
                                        }
                                        $dInicio = date("Y-m-d", strtotime("+ 1 DAY", strtotime($dInicio)));
                                    }
                                }
                                $x = substr_replace($x, '', -1);

                                $sqlj   = "SELECT * FROM lancamento WHERE dLancamento NOT IN (".$x.") AND dLancamento <= ? AND Contrato_idContrato = ? AND Quantidade > 0;";
                                $stmt1  =$conexao->prepare($sqlj);
                                $stmt1->bindParam( 1, $dFinal );
                                $stmt1->bindParam( 2, $r->idContrato );
                                $stmt1->execute();
                                $rr = $stmt1->fetchAll(PDO::FETCH_OBJ);
                                $xDatas = array();
                                foreach($rr as $rtt){
                                    array_push($xDatas, $rtt->dLancamento);
                                }
                                $xDatas = array_unique($xDatas);
                                sort($xDatas);

                                $sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
                                $stmt1 = $conexao->prepare($sql);
                                $stmt1->bindParam(1, $r->idContrato);
                                $stmt1->execute();
                                $itens = $stmt1->fetchAll(PDO::FETCH_OBJ);  
                                    
                            ?>
                        <h4 class="nome-contrato"><?php echo "".$r->idContrato." - ". utf8_decode($r->Nome)." - ".$r->nContrato." - FECHAMENTO: ". mb_strtoupper($r->Descricao); ?></h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <?php foreach($itens as $i){ ?>
                                    <th><?php echo utf8_decode($i->Servico);?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $vTotal = 0;
                                $dataInicial = $dInicio;
                                foreach($xDatas as $datas){
                                    $sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento = ?;";
                                    $stmt = $conexao->prepare($sql);
                                    $stmt->bindParam(1, $r->idContrato);
                                    $stmt->bindParam(2, $datas );
                                    $stmt->execute();
                                    $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                                    $contador1 = $stmt->rowCount();
                                    $contador2 = count($itens);
                                    if($stmt->rowCount() > 0){
                                ?>
                                <tr>
                                    <td><?php echo date("d/m/Y", strtotime($datas));?></td>
                                    <?php
                                        foreach($rs as $x){
                                            $Total = $x->Quantidade * $x->ValorUni;
                                            $vTotal += $Total;
                                    ?>
                                    <td><?php echo $x->Quantidade; ?></td>
                                    <?php 
                                        }
                                    while($contador1 < $contador2){
                                    ?>
                                    <td>0</td>
                                    <?php
                                        $contador1++;
                                    }
                                    ?>
                                </tr>
                                <?php 
                                    }else{
                                ?>
                                <tr>
                                    <td><?php echo date("d/m/Y", strtotime($datas));?></td>
                                    <?php
                                        foreach($itens as $i){
                                    ?>
                                    <td>0</td>
                                    <?php
                                        }
                                    ?>
                                </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                            <?php
                            }else{
                                break;
                            }
                        }
                ?>
                    </div>
                  </div>
                </div>
                <?php
                    }
                }else{
                    $sql = "SELECT idContrato, Fechamento FROM contrato c WHERE c.Unidade_idUnidade IN (?) AND c.Finalizado = 'N'";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindParam(1, $_POST['Unidade']);
                    $stmt->execute();
                    $rest = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $contador = 0;
                        $dFinal = 0;
                        $dInicio = 0;
                        foreach($rest as $r){
                            $sqlM = "SELECT * FROM medicao WHERE idMedicao = (SELECT MAX(idMedicao) FROM medicao WHERE Contrato_idContrato = ?);";
                            $stmtM = $conexao->prepare($sqlM);
                            $stmtM->bindParam(1, $r->idContrato);
                            $stmtM->execute();
                            if($stmtM->rowCount() > 0){
                                $result = $stmtM->fetch(PDO::FETCH_OBJ);
                            }else{
                                break;
                            }
                            
                            //print_r($result);
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
                            }
                            $hoje = date("Y-m-d");
                            //echo $dFinal." -- ".$hoje;
                            if(strtotime($dFinal) > strtotime($hoje)){
                                break;   
                            }
                            
                            $sql2 = "SELECT * FROM medicao m INNER JOIN contrato c ON c.idContrato = m.Contrato_idContrato INNER JOIN contratante cc ON cc.idContratante = c.Contratante_idContratante INNER JOIN fechamento f ON f.idFechamento = c.Fechamento INNER JOIN cadastro cd ON cd.idCadastro = cc.Cadastro_idCadastro WHERE m.Contrato_idContrato = ?;";
                            $stm1 =$conexao->prepare($sql2);
                            $stm1->bindParam( 1, $r->idContrato );
                            $stm1->execute();
                            if($stm1->rowCount()>0){
                                $res = $stm1->fetchAll(PDO::FETCH_OBJ);
                                $x = '';
                                foreach($res as $r){
                                    $dInicio = $r->dInicio;
                                    while($dInicio <= $r->dFinal){
                                        if($dInicio != $r->dFinal){
                                            $x .= "'".$dInicio."',";
                                        }else{
                                            $x .= "'".$dInicio."',";
                                        }
                                        $dInicio = date("Y-m-d", strtotime("+ 1 DAY", strtotime($dInicio)));
                                    }
                                }
                                $x = substr_replace($x, '', -1);

                                $sqlj   = "SELECT * FROM lancamento WHERE dLancamento NOT IN (".$x.") AND dLancamento <= ? AND Contrato_idContrato = ? AND Quantidade > 0;";
                                $stmt1  =$conexao->prepare($sqlj);
                                $stmt1->bindParam( 1, $dFinal );
                                $stmt1->bindParam( 2, $r->idContrato );
                                $stmt1->execute();
                                $rr = $stmt1->fetchAll(PDO::FETCH_OBJ);
                                $xDatas = array();
                                foreach($rr as $rtt){
                                    array_push($xDatas, $rtt->dLancamento);
                                }
                                $xDatas = array_unique($xDatas);
                                sort($xDatas);

                                $sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
                                $stmt1 = $conexao->prepare($sql);
                                $stmt1->bindParam(1, $r->idContrato);
                                $stmt1->execute();
                                $itens = $stmt1->fetchAll(PDO::FETCH_OBJ);  
                            ?>
                    <div class="panel-group">
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h4 class="panel-title text-left">
                            <a data-toggle="collapse" href="#collapse-u<?=$r->idContrato;?>"><?php echo "".$r->idContrato." - ". utf8_decode($r->Nome)." - ".$r->nContrato." - FECHAMENTO: ". mb_strtoupper($r->Descricao); ?></a>
                          </h4>
                        </div>
                        <div id="collapse-u<?=$r->idContrato;?>" class="panel-collapse collapse">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <?php foreach($itens as $i){ ?>
                                    <th><?php echo utf8_decode($i->Servico);?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $vTotal = 0;
                                $dataInicial = $dInicio;
                                foreach($xDatas as $datas){
                                    $sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento = ?;";
                                    $stmt = $conexao->prepare($sql);
                                    $stmt->bindParam(1, $r->idContrato);
                                    $stmt->bindParam(2, $datas );
                                    $stmt->execute();
                                    $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                                    $contador1 = $stmt->rowCount();
                                    $contador2 = count($itens);
                                    if($stmt->rowCount() > 0){
                                ?>
                                <tr>
                                    <td><?php echo date("d/m/Y", strtotime($datas));?></td>
                                    <?php
                                        foreach($rs as $x){
                                            $Total = $x->Quantidade * $x->ValorUni;
                                            $vTotal += $Total;
                                    ?>
                                    <td><?php echo $x->Quantidade; ?></td>
                                    <?php 
                                        }
                                    while($contador1 < $contador2){
                                    ?>
                                    <td>0</td>
                                    <?php
                                        $contador1++;
                                    }
                                    ?>
                                </tr>
                                <?php 
                                    }else{
                                ?>
                                <tr>
                                    <td><?php echo date("d/m/Y", strtotime($datas));?></td>
                                    <?php
                                        foreach($itens as $i){
                                    ?>
                                    <td>0</td>
                                    <?php
                                        }
                                    ?>
                                </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        </div>
                      </div>
                    </div>
                            <?php
                            }else{
                                break;
                            }
                        }
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