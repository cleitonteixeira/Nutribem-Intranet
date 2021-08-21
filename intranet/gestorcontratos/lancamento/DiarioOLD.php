<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location: ".BASE);
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
$data = date('d/m/Y', strtotime("-3 days", strtotime($data)));
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#formulario-complementar").hide();
        $("#Gasto-Diario").hide();
        $("#Resto").hide();
        $("input[name='almoco']").keyup(function(e){
            var preco = $('#almoco').val();
            var valor = $('#valAlmoco').val();
            preco = preco*valor;
            preco = preco.toFixed(2);
            preco = parseFloat(preco);
            $('#alAlmoco').val(preco.toLocaleString("pt-BR")).show();
        })
        $("input[name='jantar']").keyup(function(e){
            var preco = $('#jantar').val();
            var valor = $('#valJantar').val();
            preco = preco*valor;
            preco = preco.toFixed(2);
            preco = Number(preco);
            $('#alJantar').val(preco.toLocaleString("pt-BR")).show();
        })
        $("input[name='ceia']").keyup(function(e){
            var preco = $('#ceia').val();
            var valor = $('#valCeia').val();
            preco = preco*valor;
            preco = preco.toFixed(2);
            preco = Number(preco);
            $('#alCeia').val(preco.toLocaleString("pt-BR")).show();
        })
        $("input[name='desjejum']").keyup(function(e){
            var preco = $('#desjejum').val();
            var valor = $('#valDesjejum').val();
            preco = preco*valor;
            preco = preco.toFixed(2);
            preco = Number(preco);
            $('#alDesjejum').val(preco.toLocaleString("pt-BR")).show();
        })
        $("input[name='aniMes']").keyup(function(e){
            var preco = $('#aniMes').val();
            var valor = $('#valAniMesa').val();
            preco = preco*valor;
            preco = preco.toFixed(2);
            preco = Number(preco);
            $('#alAniMes').val(preco.toLocaleString("pt-BR")).show();
        })
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

<script type="text/javascript">
    //Total máximo de campos que você permitirá criar em seu site:
    var totalCampos = 99;

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
        var campoValor = document.getElementById("Evento"+id+"").value;
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
<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">FORMULÁRIO DE LANÇAMENTO</h1>
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
                                            <select class="selectpicker form-control dropdown" required name="Unidade" id="Unidade" title="Selecione um Unidade" data-size="5" data-live-search="true" <?php echo 	isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade" ? "disabled" : ''; ?> >
                                                <?php
                                                $sql = 'SELECT ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro';
                                                $stm = $conexao->prepare($sql);
                                                $stm->execute();
                                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                                foreach($rs as $r):
                                                ?>
                                                <optgroup label="<?php echo CNPJ_Padrao($r->CNPJ); ?>" >
                                                    <?php
                                                    $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? AND un.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
                                                    $stm = $conexao->prepare($sql);
                                                    $stm->bindParam(1, $r->CNPJ);
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
                    <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                    <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/DiarioDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente" >
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
                        <div class="col-xs-offset-3 col-xs-2 col-md-offset-3 col-md-2 col-lg-offset-3 col-lg-2">
                            <p><strong>Quantidade</strong></p>
                        </div>
                        <?php
                            $sqlj = "SELECT * FROM lancamento WHERE contrato_idContrato = ? AND dLancamento = ?;";
                            $stmj = $conexao->prepare($sqlj);
                            $stmj->bindParam(1, $r->idContrato);
                            $stmj->bindParam(2, $_POST['dLancamento']);
                            $stmj->execute();
                            foreach($row as $rs){
                        ?>
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="<?php echo utf8_decode($r->idContrato)?>[<?php echo utf8_decode($rs->Servico)?>][Quant]"><?php echo utf8_decode($rs->Servico)?>:</label>
                                <div class="col-sm-6">
                                    <div class="col-sm-4">
                                        <input required type="text" class="form-control" name="<?php echo $r->idContrato?>[<?php echo $rs->Servico?>][Quant]" id="<?php echo $r->idContrato; ?>[<?php echo $rs->Servico?>][Quant]" onkeypress="return SomenteNumero(event)" <?php echo $stmj->rowCount() > 0 ? "readonly" : ''; ?> />
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
                        <div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-1 col-md-offset-1 col-lg-offset-1">
                            <input type="hidden" value="<?php echo $_POST['dLancamento'] ?>" name="dLancamento" />
                            <input type="hidden" value="<?php echo $_POST['Unidade'] ?>" name="Unidade" />
                            <input type="hidden" value="Lancamento" name="Diario" />
                            <!--<button name="Voltar-Resto" class="btn btn-danger" >Voltar</button>-->
                            <button class="btn btn-success" type="submit" >Salvar <i class="fa fa-save" aria-hidden="true"></i></button>
                        </div>
                        <?php
                            }
                         ?>
                        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
endif;
?>