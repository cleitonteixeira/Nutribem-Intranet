<?php
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['idusuarios'])):
        session_destroy();
        require_once("../control/arquivo/funcao/Outras.php");
        header("Location: ".BASE);
    else:
        require_once("../control/Pacote.php");

function buscaSuperior( $control ){
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE Superior = ?;";
    if ($control->rowCount() > 0){
        $superior = $control->fetchAll(PDO::FETCH_OBJ);
        foreach($superior as $x){
            array_unique($_SESSION['idChefia']);
            array_push($_SESSION['idChefia'], $x->idusuarios);
        }
        foreach($superior as $s){
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $s->idusuarios);
            $stmt->execute();
            $control = $stmt;
            buscaSuperior( $control );
        }
    }
}
$a = array(1,3,14,16,4,5);
if(in_array($_SESSION['idusuarios'], $a)){
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE idusuarios != ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $_SESSION['idusuarios']);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
    $chefia = $_SESSION['idusuarios'];
    foreach( $resultado as $rest ){
        $chefia .= ", ". $rest->idusuarios;
    }
    $chefia;
}else{
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE Superior = ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $_SESSION['idusuarios']);
    $stmt->execute();
    $_SESSION['idChefia'] = array();
    $controlador = $stmt;
    buscaSuperior( $controlador );
    sort($_SESSION['idChefia']);
    $resultado = $_SESSION['idChefia'];
    unset($_SESSION['idChefia']);
    $chefia = $_SESSION['idusuarios'];
    foreach( $resultado as $rest ){
        $chefia .= ", ". $rest;
    }
    $chefia;
}
?>

<script type="text/javascript">
    $(document).ready(function(){
            $("select[name='colaborador']").change(function(e){
                var cod = $('#colaborador').val();//pegando o value do option selecionado
                //alert(cod);//apenas para debugar a variável

                $.getJSON('Completa.inc.php?cargo='+cod, function (dados){
                    //alert(dados);
                    if (dados.length == 1){ 	
                        var Colaborador = '';
                        var dAdmissao = '';
                        var Empresa = '';
                        var CNPJ = '';
                        var CodColaborador = '';
                        $.each(dados, function(i, obj){
                            Colaborador = obj.Colaborador;
                            dAdmissao = obj.dAdmissao;
                            Empresa = obj.Empresa;
                            CNPJ = obj.CNPJ;
                            CodColaborador = obj.CodColaborador;
                        })
                        $('#Colaborador').html(Colaborador).show();
                        $('#dAdmissao').html(dAdmissao).show();
                        $('#Empresa').html(Empresa).show();
                        $('#CNPJ').html(CNPJ).show();
                        $('#CodColaborador').html(CodColaborador).show();
                        
                        $('#Nome').val(Colaborador).show;
                        $('#admissao').val(dAdmissao).show;
                        $('#Empregador').val(Empresa).show;
                        $('#cnpj').val(CNPJ).show;
                        $('#Registro').val(CodColaborador).show;
                        
                    }else{
                        Reset();
                    }
                })
            })
        <!-- Resetar Selects -->
            function Reset(){
                $('#Colaborador').html("").show();
                $('#dAdmissao').html("").show();
                $('#Empresa').html("").show();
                $('#CNPJ').html("").show();
                $('#CodColaborador').html("").show();
            }
    });
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <div class="text-center"><h2>Requerimento de Férias</h2></div>
            <form method="get" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator">
                <div class="form-group">
                    <label for="colaborador" class="col-sm-2 control-label">Colaborador: </label>
                    <div class="col-sm-6">
                        <select autofocus class="selectpicker form-control" width="fit" data-size="5" data-error="Selecione uma Unidade." required title="Selecione um Colaborador!" name="colaborador" id="colaborador" data-live-search="true">
                            <?php
                            $conexao = conexao::getInstance();
                            echo $sql = "SELECT col.idColaborador, col.CodColaborador, con.dAdmissao, cad.Nome, cad.CPF,(SELECT cad.Nome AS empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER  JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ  FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = con.Unidade_idUnidade WHERE col.idColaborador IN (SELECT Colaborador_idColaborador FROM chefia WHERE Usuario_idUsuario IN (".$chefia.")) AND con.dDemissao IS NULL;";
                            $stm = $conexao->prepare($sql);
                            //$stm->bindParam(1, $chefia);
                            $stm->execute();
                            $col = $stm->fetchAll(PDO::FETCH_OBJ);
                            foreach($col as $c):
                            ?>
                            <option value="<?php echo $c->idColaborador?>" data-tokens="<?=utf8_decode(CPF_Padrao(str_pad($c->CPF,11,0, STR_PAD_LEFT))).' '.$c->Nome.' '.$c->CodColaborador ?>" data-subtext="<?php echo "CPF: ". CPF_Padrao(str_pad($c->CPF,11,0, STR_PAD_LEFT)); ?>"><?=$c->CodColaborador." - ".utf8_decode($c->Nome); ?></option>
                            <?php 
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-lg-12 col-md-12 " id="form-ferias">
                <form action="<?php echo BASE?>control/banco/FeriasDAO.php" method="post" enctype="multipart/form-data" rel="form" class="form-inline" data-toggle="validator" target="_blank">
                    <div class="col-xs-2"></div>
                    <div class="col-xs-8 ferias-t">
                        <h3 class="text-center">Aviso de Férias</h3>
                        <div class="text-center col-xs-12">
                            <p><strong>Empregador: </strong><span id="Empresa"></span>.</p>
                            <p><strong>CNPJ nº: </strong><span id="CNPJ"></span>.</p>
                        </div>
                        <div class="col-xs-12">
                            <p><strong>Nome do Funcionário: </strong><span id="Colaborador"></span>.</p>
                            <p><strong>Registro: </strong><span id="CodColaborador"></span>&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                                <strong>Data de Admissão: </strong><span id="dAdmissao"></span>.
                            </p>
                            <p><strong>Período Aquisitivo de Férias: </strong><input type="date" class="form-control" name="iAquisitivo" required/> à <input type="date" class="form-control" name="fAquisitivo" required/></p>
                            <p><strong>Período Gozo das Férias: </strong><input type="date" class="form-control" name="iFerias" required/> à <input type="date" class="form-control" name="fFerias" required/></p>
                            <p><strong>Abono Pecuniário, sim ou não? </strong><select class="form-control selectpicker col-sm-6" title="Selecione!" name="Abono" required><option value="Sim">Sim</option><option value="Não">Não</option></select></p>
                        </div>
                        <p>&thinsp;</p>
                    </div>
                    
                    <input type="hidden" name="Nome" id="Nome" value="">
                    <input type="hidden" name="Empregador" id="Empregador" value="">
                    <input type="hidden" name="cnpj" id="cnpj" value="">
                    <input type="hidden" name="Registro" id="Registro" value="">
                    <input type="hidden" name="admissao" id="admissao" value="">
                    <div class="col-xs-offset-2 col-xs-4"><button type="submit" class="btn btn-success envio">Solicitar</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
      endif;
require_once("../control/arquivo/footer/Footer.php");
?>