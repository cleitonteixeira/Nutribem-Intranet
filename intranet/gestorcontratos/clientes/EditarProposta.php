<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    $sql = "SELECT c.idContratante, cd.Nome AS Cliente, cd.CNPJ AS CNPJ FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro;";
    $stm = $conexao->prepare($sql);
    $stm->execute();
    $rs = $stm->fetchAll(PDO::FETCH_OBJ);
?>
<script>
	$(document).ready(function(){
		$("select[name='cliente']").change(function(e){
			var cli = $('#cliente').val();//pegando o value do option selecionado
			//alert(cli);//apenas para debugar a variável
			$.post('Terceiros.inc.php',{contratante: cli} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length != 0 ){ 	
					var option = '<option value="">SELECIONE UMA PROPOSTA</option>';
					
					$.each(dados, function(i, obj){
						option += '<option value="'+obj.idProposta+'">Nº: '+obj.nProposta+' - Data Cadastro: '+obj.dProposta+'</option>';
                    })
                    $('#proposta').html(option).show();
				}else{
					Reset1();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset1(){
			$('#proposta').empty();
		}
        $("select[name='proposta']").change(function(e){
			var cli = $('#proposta').val();//pegando o value do option selecionado
			//alert(cli);//apenas para debugar a variável
			$.post('Terceiros.inc.php',{proposta: cli} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length != 0 ){ 	
					var option = '';
					
					$.each(dados, function(i, obj){
						option += '<tr>';
						option += '<td>'+obj.Servico+'</td>';
						option += '<td>'+obj.Valor+'</td>';
						option += '</tr>';
                    })
                    $('#itens_proposta').html(option).show();
				}else{
					Reset2();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset2(){
			$('#itens_proposta').html('').show();
		}
		$("select[name='proposta']").change(function(e){
			var cli = $('#proposta').val();//pegando o value do option selecionado
			//alert(cli);//apenas para debugar a variável
			$.post('Terceiros.inc.php',{iProposta: cli} , function (dados){
				//alert(dados);
				dados = JSON.parse(dados);
				if (dados.length != 0 ){ 	
					var nProposta  = '';
                    var tReajuste   = '';
                    var fMedicao    = '';
                    var Condicao    = '';
                    var fPagamento  = '';
                    var tVigencia   = '';
					
					$.each(dados, function(i, obj){
						nProposta   = obj.nProposta;
                        tReajuste   = obj.tReajuste;
                        fMedicao    = obj.fMedicao;
                        Condicao    = obj.Condicao;
                        fPagamento  = obj.fPagamento;
                        tVigencia   = obj.tVigencia;
                    })
                    $('#nProposta').html(nProposta).show();
                    $('#tReajuste').html(tReajuste).show();
                    $('#fMedicao').html(fMedicao).show();
                    $('#Condicao').html(Condicao).show();
                    $('#fPagamento').html(fPagamento).show();
                    $('#tVigencia').html(tVigencia).show();
				}else{
					Reset3();
				}
			})
        })
		<!-- Resetar Selects -->
		function Reset3(){
			$('#nProposta').html("Aguardando...").show();
            $('#tReajuste').html("Aguardando...").show();
            $('#fMedicao').html("Aguardando...").show();
            $('#Condicao').html("Aguardando...").show();
            $('#fPagamento').html("Aguardando...").show();
            $('#tVigencia').html("Aguardando...").show();
		}
	});
</script>
<div class="container-fluid">
	<div class="conteudo">
		<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h2 class="text-center">Contrato Terceiros</h2></div>
<?php
    echo "<pre>";    
    var_dump($_POST);
    echo "</pre>";   
?>
        <form  name="Form" role="form" action="EditarProposta.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormCliente" name="FormCliente" >
            <div class="form-group">
                <label class="control-label col-sm-2" for="cliente">Cliente: </label>
                <div class="col-sm-6">
                    <select class="selectpicker form-control dropdown" name="cliente" id="cliente" title="Selecione um Cliente Principal" data-size="5" data-header="Selecione um Cliente" data-live-search="true" autofocus required <?php echo isset($_POST['EditaProposta']) && $_POST['EditaProposta'] == "Proposta" ? "disabled" : ''; ?>>
                        <?php
                        foreach($rs as $r ){
                        ?>
                        <option <?php echo isset($_POST['EditaProposta']) && $_POST['EditaProposta'] == "Proposta" && $_POST['cliente'] == $r->idContratante ? "selected" : ''; ?> data-tokens="<?php echo $r->CNPJ.' '.utf8_decode($r->Cliente).' '.str_pad($r->idContratante,3,0,STR_PAD_LEFT); ?>" data-subtext="CNPJ: <?php echo CNPJ_Padrao(str_pad($r->CNPJ,14,0,STR_PAD_LEFT)); ?>" value="<?php echo $r->idContratante ?>" ><?php echo str_pad($r->idContratante,3,0,STR_PAD_LEFT)." - ".utf8_decode($r->Cliente); ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php if(!isset($_POST['EditaProposta'])){ ?>
            <div class="form-group">
                <label class="control-label col-sm-2" for="proposta">Proposta: </label>
                <div class="col-sm-4">
                    <select class="form-control dropdown" name="proposta" id="proposta" title="Selecione um Pedido" required>
                        
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-primary"  data-toggle="modal" data-target="#dtProposta">Ver Detalhes</button>
                </div>
            </div>
            <?php }else{
                $sql = "SELECT * FROM proposta WHERE idProposta = ?";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $_POST['proposta']);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_OBJ);
            ?>
            <div class="col-xs-12 col-md-12 col-lg-12 text-left">
                <p class="col-sm-4"><strong>Proposta:</strong> <?php echo $result->nProposta; ?></p>
            </div>
            <?php } ?>
            
            
            
            
            
            
            
            
            
            
            
            
            <input type="hidden" name="EditaProposta" value="Proposta" />
            <button class="btn btn-success" role="button" id="gerar-contrato" type="submit"><i class="fa fa-suitcase" aria-hidden="true"></i>  Gerar Contrato</button>
        </form>
    </div>
</div>
<div id="dtProposta" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">DETALHES DO PEDIDO</h4>
            </div>
            <div class="modal-body">
                <p><strong>Nº Proposta: </strong><span id="nProposta">Aguardando...</span></p>
                <p><strong>Reajuste: </strong><span id="tReajuste">Aguardando...</span></p>
                <p><strong>Fechamento de Medição: </strong><span id="fMedicao">Aguardando...</span></p>
                <p><strong>Condição: </strong><span id="Condicao">Aguardando...</span></p>
                <p><strong>Forma de Pagamento: </strong><span id="fPagamento">Aguardando...</span></p>
                <p><strong>Tempo de Vigência: </strong><span id="tVigencia">Aguardando...</span></p>
                
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody id="itens_proposta">
                        <tr>
                            <td colspan="2">Aguardando...<i class="fas fa-coffee"></i></td>
                        </tr> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
require_once("../control/arquivo/footer/Footer.php");
}
?>