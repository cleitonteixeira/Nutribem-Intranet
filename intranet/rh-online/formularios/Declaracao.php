<?php
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['idusuarios'])):
        session_destroy();
        require_once("../control/arquivo/funcao/Outras.php");
        require_once("../control/banco/conexao.php");
        require_once("../control/arquivo/header/Header.php");
        require_once("../control/arquivo/Login.php");
    else:
        require_once("../control/Pacote.php");
    
?>
<script type="text/javascript">
    $(document).ready(function(){
            $("select[name='colaborador']").change(function(e){
                var cod = $('#colaborador').val();//pegando o value do option selecionado
                //alert(cod);//apenas para debugar a variável

                $.getJSON('CompletaFerias.inc.php?cod='+cod, function (dados){
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
                            iPeriodo = obj.iPeriodo;
                            fPeriodo = obj.fPeriodo;
                        })
                        $('#Colaborador').html(Colaborador).show();
                        $('#dAdmissao').html(dAdmissao).show();
                        $('#Empresa').html(Empresa).show();
                        $('#CNPJ').html(CNPJ).show();
                        $('#CodColaborador').html(CodColaborador).show();
                        $('#fPeriodo').html(fPeriodo).show();
                        $('#iPeriodo').html(iPeriodo).show();
                        
                        $('#Nome').val(Colaborador).show;
                        $('#admissao').val(dAdmissao).show;
                        $('#Empregador').val(Empresa).show;
                        $('#cnpj').val(CNPJ).show;
                        $('#iAquisitivo').val(iPeriodo).show;
                        $('#fAquisitivo').val(fPeriodo).show;
                        $('#Registro').val(CodColaborador).show;
                            
                        $("#form-ferias").removeClass("Esconde");
                        
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
                $('#iPeriodo').html("").show();
                $('#fPeriodo').html("").show();
            }
    });
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="text-center"><h2>Requerimento de Férias</h2></div>
            <form method="get" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator">
                <div class="form-group">
                    <label for="colaborador" class="col-sm-2 control-label">Colaborador: </label>
                    <div class="col-sm-6">
                        <select autofocus class="selectpicker form-control" width="fit" data-size="5" data-error="Selecione uma Unidade." required title="Selecione um Colaborador!" name="colaborador" id="colaborador" data-live-search="true">
                            <?php
                            $conexao = conexao::getInstance();
                            $sql = "SELECT col.idColaborador, col.CodColaborador, con.dAdmissao, cad.Nome, cad.CPF,(SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER  JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ  FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN Contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN Unidade un ON un.idUnidade = con.Unidade_idUnidade;";
                            $stm = $conexao->prepare($sql);
                            $stm->execute();
                            $col = $stm->fetchAll(PDO::FETCH_OBJ);
                            foreach($col as $c):
                            ?>
                            <option value="<?php echo $c->idColaborador?>" data-tokens="<?php echo utf8_decode($c->CPF); echo $c->Nome; echo $c->CodColaborador ?>" data-subtext="<?php echo "CPF: ".$c->CPF; ?>"><?php echo $c->CodColaborador." - ".utf8_decode($c->Nome); ?></option>
                            <?php 
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 Esconde" id="form-ferias">
                <form action="<?php echo BASE."control/banco/FeriasDAO"?>" method="post" enctype="multipart/form-data" rel="form" class="form-inline" data-toggle="validator">
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
                            <p><strong>Período Aquisitivo de Férias: </strong><span id="iPeriodo"></span> à <span id="fPeriodo"></span>  </p>
                            <p><strong>Período Gozo das Férias: </strong><input type="date" class="form-control" name="iFerias" required/> à <input type="date" class="form-control" name="fFerias" required/></p>
                            <p><strong>Abono Pecuniário, sim ou não? </strong><select class="form-control selectpicker col-sm-6" title="Selecione!" name="Abono" required><option value="Sim">Sim</option><option value="Não">Não</option></select></p>
                        </div>
                        <p>&thinsp;</p>
                        <div class="col-xs-12 text-justify">
                            <p>
                                O empregador, através do presente documento, e em conformidade com o art. 135 da CLT, vem notificar o empregado, com antecedência de 30 (trinta) dias, a concessão de suas férias relativas ao período aquisitivo descrito acima e conforme período de gozo apontado pelo mesmo.
                            </p>
                            <p>
                                As férias serão remuneradas com o acréscimo de 1/3 constitucional, de acordo com o art. 7º, XVII da Constituição da República, e será pago até 2 (dois) dias antes do início do respectivo gozo de férias. 
                            </p>
                            <p> 
                                Assim sendo, o empregado fica ciente desde já para comparecer ao departamento pessoal da empresa, para que o empregador possa lhe fornecer o demonstrativo de valores creditados.
                            </p>
                        </div>
                        <p>&thinsp;</p>
                        <div class="text-right col-xs-12">
                            <p>________________________, <?php echo strftime("%d de %B de %Y") ?>.</p>
                        </div>
                        <p>&thinsp;</p>
                        <div>
                            <div class="col-xs-6 text-center"><p>_______________________________________</p><p>Assinatura do Gestor Imediato</p></div>
                            <div class="col-xs-6 text-center"><p>_______________________________________</p><p>Assinatura do Empregado</p></div>
                        </div>
                        <p>&thinsp;</p>
                        
                    </div>
                    
                    <input type="hidden" name="Nome" id="Nome" value="">
                    <input type="hidden" name="Empregador" id="Empregador" value="">
                    <input type="hidden" name="iAquisitivo" id="iAquisitivo" value="">
                    <input type="hidden" name="fAquisitivo" id="fAquisitivo" value="">
                    <input type="hidden" name="cnpj" id="cnpj" value="">
                    <input type="hidden" name="Registro" id="Registro" value="">
                    <input type="hidden" name="admissao" id="admissao" value="">
                    <div class="col-xs-offset-2 col-xs-4"><button type="submit" class="btn btn-success envio">Gerar PDF</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
      endif;
require_once("../control/arquivo/footer/Footer.php");
?>