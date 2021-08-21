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
        $("select[name='cargoS']").change(function(e){
            var cargo = $('#cargoS').val();//pegando o value do option selecionado
            //alert(empresa);//apenas para debugar a variável
            $.getJSON('CarregaCargos.inc.php?cargo='+cargo, function (dados){
                //alert(dados);
                if (dados.length > 0){ 	
                    var CodCargo = ""
                    var Cargo = ""
                    var Funcao = ""
                    var CBO = ""
                    var Salario = ""
                    var idCargo = ""
                    $.each(dados, function(i, obj){
                        CodCargo = obj.CodCargo;
                        Cargo = obj.Cargo;
                        Funcao = obj.Funcao;
                        CBO = obj.CBO;
                        Salario = obj.Salario;
                        idCargo = obj.idCargo;
                    })
                    $('#cargo').val(Cargo).show();
                    $('#codigo').val(CodCargo).show();
                    $('#funcao').val(Funcao).show();
                    $('#cbo').val(CBO).show();
                    $('#salario').val(Salario).show();
                    $('#CargoID').val(idCargo).show();
                }else{
                    Reset();
                }
            })
        })
        <!-- Resetar Selects -->
        function Reset(){
            $('#cargo').val().show();
            $('#codigo').val().show();
            $('#funcao').val().show();
            $('#cbo').val().show();
            $('#salario').val().show();
            $('#CargoID').val().show();
        }
    });
</script>
<script type="text/javascript">
    $(function(){
        $("#salario").maskMoney();
    })
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-12"><h2 class="text-center">Atualizar Cargos</h2></div>
            <form method="get" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="cargoS">Selecione um cargo:</label>  
                    <div class="col-sm-5">
                        <select class="selectpicker" title="Selecione uma Cargo." name="cargoS" id="cargoS" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma Cargo." required>
							<?php
							$conexao = conexao::getInstance();
							$sql = 'SELECT u.idUnidade, ca.Nome FROM unidade u INNER JOIN cadastro ca ON ca.idCadastro = u.Cadastro_idCadastro;';
							$stm = $conexao->prepare($sql);
							$stm->execute();
							$rs = $stm->fetchAll(PDO::FETCH_OBJ);
							foreach($rs as $r):
							?>
							<optgroup label="<?php echo utf8_decode($r->Nome); ?>" >
								<?php
								$sql = 'SELECT ca.* FROM cargo ca INNER JOIN unidade u ON u.idUnidade = ca.Unidade_idUnidade INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro WHERE Unidade_idUnidade = ? ORDER BY ca.Unidade_idUnidade';
								$stm = $conexao->prepare($sql);
								$stm->bindParam(1, $r->idUnidade);
								$stm->execute();
								while($row = $stm->fetch(PDO::FETCH_OBJ)):
								?>
								<option data-subtext="CBO:<?php echo $row->CBO ?> " data-tokens="<?php echo utf8_decode($row->Cargo)." ".$row->CodCargo." ".$row->CBO ?>" value="<?php echo $row->idCargo ?>"><?php echo $row->CodCargo."-".utf8_decode($row->Cargo);?></option>
								<?php endwhile; ?>

							</optgroup>
							<?php endforeach; ?>
                        </select>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="col-sm-5"></div>
                </div>
            </form>
            <hr />
            <div class="col-xs-12"><h3 class="text-center">Dados do Cargo</h3></div>
            <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/CargoDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormCargo" name="FormCargo">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="codigo">Código:</label>
                    <div class="col-sm-2" title="Código do Cargo!">
                        <div class="input-group">
                            <span class="input-group-addon" id="cod" ><i class="fa fa-info" aria-hidden="true"></i></span>
                            <input type="text" readonly name="codigo" id="codigo" value="" class="form-control" required="required" >
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="cargo">Cargo:</label>
                    <div class="col-sm-4">
                        <input type="text" name="cargo" id="cargo" class="form-control" required="required" data-error="Digie o Cargo!" readonly>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="funcao">Função:</label>
                    <div class="col-sm-4">
                        <input type="text" name="funcao" id="funcao" class="form-control" required="required" data-error="Digie a Função!">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="cbo">CBO:</label>
                    <div class="col-sm-4">
                        <input type="text" name="cbo" id="cbo" class="form-control" onkeypress="return SomenteNumero(event)" required="required" maxlength="6" data-minlength="6" readonly>
                        <div class="help-block">Mínimo 6 caracteres.</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="salario">Salário:</label>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <span class="input-group-addon" id="real">R$</span>
                            <input type="text" class="form-control" id="salario" required name="salario" placeholder="0.00" aria-describedby="real" value="" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-8 col-sm-3">
                        <input type="hidden" value="Atualizar" name="Cargo">
                        <input type="hidden" value="" name="CargoID" id="CargoID">
                        <button type="submit" class="btn btn-success">Atualizar <i class="fa fa-refresh" aria-hidden="true"></i>
                        </button>
                        <button type="reset" class="btn btn-danger">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>