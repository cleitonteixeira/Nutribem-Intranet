<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location: ".BASE);
else:
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
$sql = 'SELECT cont.nContrato, c.idContratante, c.IE, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca INNER JOIN contrato cont ON cont.idContrato = ? WHERE c.idContratante = cont.Contratante_idContratante;';
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $_POST['Contrato']);
$stm->execute();
$row = $stm->fetch(PDO::FETCH_OBJ);
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
$sqli = "SELECT * FROM contrato c WHERE idContrato = ?;";
$stmtt = $conexao->prepare($sqli);
$stmtt->bindValue(1, $_POST['Contrato']);
$stmtt->execute();
$rsx = $stmtt->fetch(PDO::FETCH_OBJ);
?>
<script>
	$(document).ready(function(){
		$('#botoes').hide();
	});
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<h1 class="text-center">FORMULÁRIO PARA REAJUSTE DE CONTRATO</h1>
			<div class="col-xs-12 col-md-12 col-lg-12">
				<hr />
			</div>
			<form name="Form" role="form" action="<?php echo BASE; ?>control/banco/ReajusteDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormCliente" name="FormCliente">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="panel panel-default text-justify">
						<div class="panel-heading">
							<h1 class="panel-title">Dados Cliente</h1>
						</div>
						<div class="panel-body">
							<div class="col-xs-8 col-md-8 col-lg-6"><p><strong>Nome: </strong><?php echo $Nome; ?></p></div>
							<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>CNPJ: </strong><?php echo $CNPJ; ?></p></div>
							<div class="col-xs-12 col-md-12 col-lg-12"><p><strong>IE: </strong><?php echo $IE; ?></p></div>	
							<div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço: </strong><?php echo $Endereco; ?></p></div>	
							<div class="col-xs-12 col-md-12 col-lg-12"><p><strong>Endereço de Cobranca: </strong><?php echo $eCobranca; ?></p></div>	
							
							<div class="col-xs-4 col-md-4 col-lg-5"><p><strong>Responsável Comercial: </strong><?php echo $rComercial; ?></p></div>
							<div class="col-xs-4 col-md-4 col-lg-3"><p><strong>Telefone: </strong><?php echo $tComercial; ?></p></div>
							<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><?php echo $eComercial; ?></p></div>
							
							<div class="col-xs-4 col-md-4 col-lg-5"><p><strong>Responsável Financeiro: </strong><?php echo $rFinanceiro; ?></p></div>
							<div class="col-xs-4 col-md-4 col-lg-3"><p><strong>Telefone: </strong><?php echo $tFinanceiro; ?></p></div>
							<div class="col-xs-4 col-md-4 col-lg-4"><p><strong>E-mail: </strong><?php echo $eFinanceiro; ?></p></div>
							
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<hr />
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="text-center"><h3><u>Valores e Eventos do Contrato</u></h3></div>
					<div class="col-xs-offset-2 col-xs-2">
						<p><strong>Valor Inicial</strong></p>
					</div>
					<div class="col-xs-2">
						<p><strong>% Reajuste</strong></p>
					</div>
					<div class="col-xs-2">
						<p><strong>Valor Final</strong></p>
					</div>
					<div class="col-xs-12 col-md-12 col-lg-12"></div>
					<?php
					$sql = "SELECT * FROM itensproposta WHERE Proposta_idProposta = ?";
					$stmt = $conexao->prepare($sql);
					$stmt->bindParam(1, $rsx->Proposta_idProposta);
					$stmt->execute();
					while($rp = $stmt->fetch(PDO::FETCH_OBJ)){
                        
					?>
                    <script>
                    $(document).ready(function(){
                        $("input[name='re<?php echo $rp->idItensProposta; ?>']").keyup(function () { 
                            this.value = this.value.replace(/[^0-9.]/g,'');
                        });

                        $("input[name='re<?php echo $rp->idItensProposta; ?>']").keyup(function(e){
                            var preco = $('#<?php echo $rp->idItensProposta; ?>').val();
                            var porc = $('#re<?php echo $rp->idItensProposta; ?>').val();
                            preco = preco.replace(',','.');
                            preco = parseFloat(preco);
                            porc = parseFloat(porc);
                            porc = porc/100;
                            porc = porc+1;
                            var precoF = preco*porc;
                            precoF = precoF.toFixed(2);
                            precoF = precoF.replace('.',',');
                            $('#al<?php echo $rp->idItensProposta; ?>').val(precoF).show();
                        })
                    });
                    </script>
					<div class="form-group">
						<label class="control-label col-sm-2" for="<?php echo $rp->idItensProposta; ?>"><?php echo utf8_decode($rp->Servico); ?>:</label>
						<div class="col-sm-6">
							<div class="col-sm-4">
								<div class="input-group">
									<span class="input-group-addon" id="real">R$</span>
									<input class="form-control text-right" readonly type="text" name="<?php echo $rp->idItensProposta; ?>" id="<?php echo $rp->idItensProposta; ?>" placeholder="0.00" aria-describedby="real" value="<?php echo number_format($rp->ValorUni,2,',','.'); ?>" />
								</div>
							</div>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" required class="form-control text-center" name="<?php echo "re".$rp->idItensProposta; ?>" id="<?php echo "re".$rp->idItensProposta; ?>"/>
									<span class="input-group-addon" id="real">%</span>
								</div>
								<div class="help-block with-errors"></div>
							</div>
							<div class="col-sm-3">
								<div class="input-group">
									<span class="input-group-addon" id="real">R$</span>
									<input class="form-control text-right" readonly type="text" name="<?php echo "al".$rp->idItensProposta; ?>" id="<?php echo "al".$rp->idItensProposta; ?>" placeholder="0.00" aria-describedby="real" value="<?php echo number_format($rp->ValorUni,2,',','.'); ?>" />	
								</div>
							</div>
						</div>
					</div>
					<?php
					}
					?>
				</div>
				<div class="col-xs-offset-1 col-xs-11 col-md-offset-1 col-md-11 col-lg-offset-1 col-lg-11">
					<input type="hidden" value="<?php echo $rsx->idContrato ?>" name="Contrato" />
					<input type="hidden" value="<?php echo $rsx->Proposta_idProposta ?>" name="Proposta" />
					<input type="hidden" value="Reajuste" name="Cliente" />
					<button class="btn btn-success" type="submit">Reajustar</button>
				</div>
				<div class="col-xs-12 col-md-12 col-lg-12"> 
				</div>
			</form>
		</div>
	</div>
</div>
<?php
require_once("../control/arquivo/footer/Footer.php");
endif;
?>