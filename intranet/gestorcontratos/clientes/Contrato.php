<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location: ".BASE);
else:
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
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
$Troca = array("/","\\","|");
$sql = "SELECT e.idEmpresa, cad.Nome AS Empresa, cad.CNPJ AS CNPJ FROM empresa e INNER JOIN cadastro cad ON cad.idCadastro =  e.Cadastro_idCadastro";
$stm = $conexao->prepare($sql);
$stm->execute();
$rsEm = $stm->fetchAll(PDO::FETCH_OBJ);
$sql = "SELECT * FROM proposta p WHERE p.idProposta = ?";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $_GET['p']);
$stm->execute();
$rsx = $stm->fetch(PDO::FETCH_OBJ);

if(!isset($_GET['ct'])){
    $sql = 'SELECT c.idContratante, c.IE, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca WHERE c.idContratante = ?;';
    $stm = $conexao->prepare($sql);
    $stm->bindValue(1, $rsx->Contratante_idContratante);
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
}else{
    $sql = 'SELECT c.idContratante, c.IE, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca WHERE c.idContratante = ?;';
    $stm = $conexao->prepare($sql);
    $stm->bindValue(1, $_GET['ct']);
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
}
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
$Ano = date("Y");
$Cliente = str_pad($idContratante, 3, 0, STR_PAD_LEFT);
$Quant = 1;
$nContrato = "CT.".$Cliente.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
$sql = 'SELECT ct.nContrato FROM contratante c INNER JOIN contrato ct ON ct.Contratante_idContratante = c.idContratante WHERE c.idContratante = ?;';
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $idContratante);
$stm->execute();
$nContratoL = array();
while($row = $stm->fetch(PDO::FETCH_OBJ)){
    array_push($nContratoL, $row->nContrato);
}
while(in_array($nContrato, $nContratoL)){
    $Quant += 1;
    $nContrato = "CT.".$Cliente.".".$Ano.".".str_pad($Quant, 2, 0, STR_PAD_LEFT);
}

$dAtual  = date("Y-m-d");
switch ($rsx->tReajuste){
    case("Trimestral"):
        $tR = 3;
        break;
    case("Semestral"):
        $tR = 6;
        break;
    case("Anual"):
        $tR = 12;
        break;
    case("Bienal"):
        $tR = 24;
        break;
}
$dReajuste = date("Y-m-d", strtotime("+".$tR." month"));
$fVigencia = date("Y-m-d", strtotime("+".$rsx->pVigencia." month"));

?>
<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='VigenciaIni']").focusout(function(e){
            var iVigencia = $("#VigenciaIni").val();

            //alert(iVigencia);
            $.post('Completa.inc.php',{gData: iVigencia, dReajuste: <?php echo $tR ?>, pVigencia: <?php echo $rsx->pVigencia; ?>} , function (dados){
                //alert(dados);
                dados = JSON.parse(dados);
                var Data = '';
                var Data1 = '';
                if (dados.length != 0 ){
                    $.each(dados, function(i, obj){
                        Data = obj.fVigencia;
                        Data1 = obj.dReajuste;
                    })
                    $("#VigenciaFim").val(Data).show();
                    $("#dReajuste").val(Data1).show();
                }
            })
        })
    });

    //Total máximo de campos que você permitirá criar em seu site:
    var totalCampos = 7;

    //Não altere os valores abaixo, pois são variáveis controle;
    var iLoop = 1;
    var iCount = 0;
    var linhaAtual;

    function AddCampos() {
        var hidden1 = document.getElementById("hidden1");
        var hidden2 = document.getElementById("hidden2");

        //Executar apenas se houver possibilidade de inserção de novos campos:
        if (iCount < totalCampos) {

            //Limpar hidden1, para atualizar a lista dos campos que ainda estão vazios:
            hidden2.value = "";

            //Atualizando a lista dos campos que estão ocultos.
            //Essa lista ficará armazenada temporiariamente em hidden2;
            for (iLoop = 1; iLoop <= totalCampos; iLoop++) {
                if (document.getElementById("linha"+iLoop).style.display == "none") {
                    if (hidden2.value == "") {
                        hidden2.value = "linha"+iLoop;
                    }else{
                        hidden2.value += ",linha"+iLoop;
                    }
                }
            }
            //Quebrando a lista que foi armazenada em hidden2 em array:

            linhasOcultas = hidden2.value.split(",");

            if (linhasOcultas.length > 0) {
                //Tornar visível o primeiro elemento de linhasOcultas:
                document.getElementById(linhasOcultas[0]).style.display = "block"; iCount++;

                //Acrescentando o índice zero a hidden1:
                if (hidden1.value == "") {
                    hidden1.value = linhasOcultas[0];
                }else{
                    hidden1.value += ","+linhasOcultas[0];
                }

                /*Retirar a opção acima da lista de itens ocultos: <-------- OPCIONAL!!!
			if (hidden2.value.indexOf(","+linhasOcultas[0]) != -1) {
					hidden2.value = hidden2.value.replace(linhasOcultas[0]+",","");
			}else if (hidden2.indexOf(linhasOcultas[0]+",") == 0) {
					hidden2.value = hidden2.value.replace(linhasOcultas[0]+",","");
			}else{
					hidden2.value = "";
			}
			*/
            }
        }
    }

    function RemoverCampos(id) {
        //Criando ponteiro para hidden1:        
        var hidden1 = document.getElementById("hidden1");
        //Pegar o valor do campo que será excluído:
        var campoValor = document.getElementById("dNome["+id+"]").value;
        //Se o campo não tiver nenhum valor, atribuir a string: vazio:
        if (campoValor == "") {
            campoValor = "vazio";
        }

        if(confirm("O campo que contém o valor:\n» "+campoValor+"\nserá excluído!\n\nDeseja prosseguir?")){
            document.getElementById("linha"+id).style.display = "none"; iCount--;

            //Removendo o valor de hidden1:
            if (hidden1.value.indexOf(",linha"+id) != -1) {
                hidden1.value = hidden1.value.replace(",linha"+id,"");
            }else if (hidden1.value.indexOf("linha"+id+",") == 0) {
                hidden1.value = hidden1.value.replace("linha"+id+",","");
            }else{
                hidden1.value = "";
            }
        }
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">FORMULÁRIO DE CONTRATO</h1>
            <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/ContratoDAO.php" method="post" enctype="multipart/form-data" target="_blank" class="form-horizontal" data-toggle="validator" id="FormCliente" name="FormCliente">
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
                <div class="col-xs-7 col-md-7 col-lg-7">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="data">Contrato Nº: </label>
                        <div class="col-sm-4">
                            <input type="text" name="nContrato" id="nContrato" value="<?php echo $nContrato ?>" class="form-control" readonly />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-7 col-md-7 col-lg-7 text-center">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="empresa">Empresa: </label>
                        <div class="col-sm-9">
                            <select class="selectpicker form-control dropdown" name="empresa" id="empresa" title="Selecione uma Empresa" data-size="5" data-live-search="true" required>
                                <?php
                                foreach($rsEm as $r ):
                                ?>
                                <option data-tokens="<?php echo $r->CNPJ.' '.utf8_decode($r->Empresa); ?>" data-subtext="CNPJ: <?php echo CNPJ_Padrao(str_pad($r->CNPJ,14,0,STR_PAD_LEFT)); ?>" value="<?php echo $r->idEmpresa ?>" ><?php echo str_pad($r->idEmpresa,3,0,STR_PAD_LEFT)." - ".utf8_decode($r->Empresa); ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-xs-7 col-md-7 col-lg-7">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="tReajuste">Tipo Reajuste: </label>
                        <div class="col-sm-9">
                            <input type="text" value="<?php echo utf8_decode($rsx->tReajuste); ?>" class="form-control" readonly />
                        </div>
                    </div>
                </div>
                <div class="col-xs-7 col-md-7 col-lg-7">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="dataCt">Vigência do Contrato:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="VigenciaIni" id="VigenciaIni" value="<?php echo $dAtual ?>" required />
                            <div class="help-block with-errors"></div>
                        </div> 

                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="VigenciaFim" id="VigenciaFim" value="<?php echo $fVigencia ?>" required readonly />
                            <div class="help-block with-errors"></div>
                        </div> 
                    </div>
                </div>
                <div class="col-xs-5 col-md-5 col-lg-5">
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="dReajuste">Data do Reajuste:</label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" value="<?php echo $dReajuste ?>" name="dReajuste" id="dReajuste" required />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-12"></div>
                
                <div class="col-xs-7 col-md-7 col-lg-7">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="condicao">Condição: </label>
                        <div class="col-sm-7">
                            <select id="condicao" name="condicao" class="dropdown form-control" title="Selecione uma Condição" data-live-search="true">
                                <option value="">Selecione uma Condição</option>
                                <option value="7 dias">7 dias</option>
                                <option value="10 dias">10 dias</option>
                                <option value="15 dias">15 dias</option>
                                <option value="30 dias">30 dias</option>
                                <option value="45 dias">45 dias</option>
                                <option value="45 dias">45 dias</option>
                                <option value="60 dias">60 dias</option>
                                <option value="90 dias">90 dias</option>
                                <option value="A Vista">A Vista</option>
                                <option value="Pag. Antecipado">Pag. Antecipado</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-5 col-md-5 col-lg-5">
                    <div class="form-group">
                        <label class="control-label col-sm-5" for="fPagamento">Forma de pagamento: </label>
                        <div class="col-sm-7">
                            <select id="fPagamento" name="fPagamento" class="form-control" title="Selecione uma Forma" data-live-search="true">
                                <option value="">Selecione uma Forma</option>
                                <option value="Deposito">Deposito</option>
                                <option value="Boleto">Boleto</option>
                                <option value="A Vista">A Vista</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                
                
                <div class="col-xs-7 col-md-7 col-lg-7">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="cCusto">Centro de Custo:</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="cCusto" id="cCusto" required />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2 col-md-2 col-lg-2">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="uf">UF:</label>
                        <div class="col-sm-3">
                            <select class="selectpicker form-control" title="Selecione uma UF" name="uf" id="uf" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma UF." required>
                                <option data-tokens="Acre AC" value="AC">Acre - AC</option>
                                <option data-tokens="Alagoas AL" value="AL">Alagoas - AL</option>
                                <option data-tokens="Amapá AP" value="AP">Amapá - AP</option>
                                <option data-tokens="Amazonas AM" value="AM">Amazonas - AM</option>
                                <option data-tokens="Bahia BA" value="BA">Bahia - BA</option>
                                <option data-tokens="Ceará CE" value="CE">Ceará - CE</option>
                                <option data-tokens="Distrito Federal DF" value="DF">Distrito Federal - DF</option>
                                <option data-tokens="Espirito Santo ES" value="ES">Espirito Santo - ES</option>
                                <option data-tokens="Goiás GO" value="GO">Goiás - GO</option>
                                <option data-tokens="Maranhão MA" value="MA">Maranhão - MA</option>
                                <option data-tokens="Mato Grosso MT" value="MT">Mato Grosso - MT</option>
                                <option data-tokens="Mato Grosso do Sul MS" value="MS">Mato Grosso do Sul - MS</option>
                                <option data-tokens="Minas Gerais MG" value="MG">Minas Gerais - MG</option>
                                <option data-tokens="Pará PA" value="PA">Pará - PA</option>
                                <option data-tokens="Paraíba PB" value="PB">Paraíba - PB</option>
                                <option data-tokens="Paraná PR" value="PR">Paraná - PR</option>
                                <option data-tokens="Pernabuco PE" value="PE">Pernabuco - PE</option>
                                <option data-tokens="Piauí PI" value="PI">Piauí - PI</option>
                                <option data-tokens="Rio de Janeiro RJ" value="RJ">Rio de Janeiro - RJ</option>
                                <option data-tokens="Rio Grande do Norte RN" value="RN">Rio Grande do Norte - RN</option>
                                <option data-tokens="Rio Grande do Sul RS" value="RS">Rio Grande do Sul - RS</option>
                                <option data-tokens="Rondônia RO" value="RO">Rondônia - RO</option>
                                <option data-tokens="Roraima RR" value="RR">Roraima - RR</option>
                                <option data-tokens="Santa Catarina SC" value="SC">Santa Catarina - SC</option>
                                <option data-tokens="São Paulo SP" value="SP">São Paulo - SP</option>
                                <option data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
                                <option data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-7 col-md-7 col-lg-7">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="pCompra">Pedido de Compra:</label>
                        <div class="col-sm-7">
                            <input type="text" maxlength="25" class="form-control" name="pCompra" id="pCompra" placeholder="Opcional" />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-5 col-md-5 col-lg-5">
                    <div class="form-group">
                        <label class="col-sm-5 control-label" for="unidade">Unidade de Faturamento:</label>
                        <div class="col-sm-7">
                            <select class="selectpicker form-control" title="Selecione uma unidade" name="unidade" id="unidade" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma unidade." required>
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
                        </div>
                    </div>
                </div>
                <div class="col-xs-7 col-md-7 col-lg-7">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="fechamento">Período de Fechamento:</label>
                        <div class="col-sm-9">
                            <select class="selectpicker form-control" title="Selecione o Período" name="fechamento" id="fechamento" data-live-search="true" data-size="5" data-error="Selecione uma unidade." required>
                                <?php
                                
                                $sql = 'SELECT * FROM fechamento;';
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $r->CNPJ);
                                $stm->execute();
                                while($row = $stm->fetch(PDO::FETCH_OBJ)){
                                ?>
                                <option value="<?php echo $row->idFechamento; ?>"><?=$row->idFechamento." - ".utf8_decode($row->Descricao);?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="col-xs-5 col-md-5 col-lg-5">
                    <div class="form-group">
                        <label class="col-sm-5 control-label" for="cMinimo">Consumo Mínimo:</label>
                        <div class="col-sm-7">
                            <input class="form-control" name="cMinimo" id="cMinimo" type="text" required>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-7 col-md-7 col-lg-7">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="obs">Observação:</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="obs" id="obs" maxlength="150" minlength="5" required></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-12">
                    <p><strong>Itens e Valores da Proposta:</strong></p>
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Serviço</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sql = "SELECT * FROM itensproposta WHERE Proposta_idProposta = ?";
                            $stm = $conexao->prepare($sql);
                            $stm->bindValue(1, $_GET['p']);
                            $stm->execute();
                            while ($row = $stm->fetch(PDO::FETCH_OBJ)){
                            ?>
                            <tr>
                                <td><?php echo utf8_decode($row->Servico); ?></td>
                                <td>R$ <?php echo number_format($row->ValorUni,2,',','.'); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-12"> </div>

                <div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-2 col-md-offset-2 col-lg-offset-2">
                    <input type="hidden" value="<?php echo $_GET['p']; ?>" name="Proposta" />
                    <input type="hidden" value="Contrato" name="Cliente" />
                    <input type="hidden" value="<?php echo $idContratante; ?>" name="CodContratante" id="CodContratante" />
                    <button class="btn btn-success" type="submit">Enviar</button>
                    <button class="btn btn-warning" type="reset">Cancelar</button>
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