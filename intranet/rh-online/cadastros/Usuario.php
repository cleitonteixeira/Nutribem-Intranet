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
    $(function () {
        $("#senha").complexify({}, function (valid, complexity) {
            document.getElementById("mtSenha").value = complexity;
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='chefia']").click(function(e) {
            var m = $("input[name='chefia']:checked").val();
            if (m === "N") {
                $("#email-cf").css({
                    display: 'none'
                });
                $("#email").attr("disabled","true");
                $("#email").removeAttr("required");
            }else{
                $("#email-cf").css({
                    display: 'block'
                });
                $("#email").attr("required","required");
                $("#email").removeAttr("disabled");
            }
        })

    });
</script>
<div class="container-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 conteudo">
                <div class="col-xs-offset-3 col-xs-9"><h1>Cadastro Usuário</h1></div>
                <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/UsuarioDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator">
                    <div class="col-xs-12">   

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="nome">Nome:</label>
                            <div class="col-sm-4">
                                <input type="text" name="nome" id="nome" class="form-control" required="required" data-match-error="Digie o nome!">
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="login">Login:</label>
                            <div class="col-sm-4">
                                <input type="text" name="login" id="login" class="form-control" required="required" data-match-error="Digie o login!" maxlength="20" min="10">
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="senha">Senha:</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="password" name="senha" id="senha" class="form-control" required="required" data-match-error="Digie a senha!">
                                    <span class="input-group-addon">Força da Senha: <meter value="0" id="mtSenha" max="100"></meter></span>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="cSenha">Confime a Senha:</label>
                            <div class="col-sm-4">    
                                <input type="password" class="form-control" id="cSenha" name="cSenha" data-match="#senha" data-match-error="As senhas não estão iguais." placeholder="Confirmação de Senha." required>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo" class="control-label col-sm-2">Tipo do Usuário:</label>
                            <div class="col-sm-6">
                                <label class="radio-inline"><input type="radio" required id="tipo" name="tipo" value="1">Administrador</label>
                                <label class="radio-inline"><input type="radio" required id="tipo" name="tipo" value="2" checked>Usuário</label>
                                <label class="radio-inline"><input type="radio" required id="tipo" name="tipo" value="3">Segurança</label>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-6">
                            
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="chefia" class="control-label col-sm-2">Cargo de Chefia:</label>
                            <div class="col-sm-6">
                                <label class="radio-inline"><input type="radio" required id="chefia" name="chefia" value="N" checked>Não</label>
                                <label class="radio-inline"><input type="radio" required id="chefia" name="chefia" value="S">Sim</label>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                        </div>
                        <div class="form-group" id="email-cf" style="display: none" >
                            <label for="email" class="control-label col-sm-2">E-mail:</label>
                            <div class="col-sm-4">
                                <input class="form-control" type="text" name="email" id="email" />
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="unidade" class="control-label col-sm-2">Unidades:</label>
                            <div class="col-sm-6">
                                <select class="selectpicker form-control" multiple name="unidade[]" id="unidade[]" data-live-search="true" data-width="50%" data-size="5" data-actions-box="true" required>
                                    <?php
                                    $conexao = conexao::getInstance();
                                    $sql = 'SELECT ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro';
                                    $stm = $conexao->prepare($sql);
                                    $stm->execute();
                                    $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                    foreach($rs as $r):
                                    ?>
                                    <optgroup label="<?php echo CNPJ_Padrao($r->CNPJ); ?>" >
                                        <?php
                                        $sql = 'SELECT un.idUnidade, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidade un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? ORDER BY cd.Nome';
                                        $stm = $conexao->prepare($sql);
                                        $stm->bindParam(1, $r->CNPJ);
                                        $stm->execute();
                                        while($row = $stm->fetch(PDO::FETCH_OBJ)):
                                        ?>
                                        <option value="<?php echo $row->idUnidade; ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                        <?php endwhile; ?>

                                    </optgroup>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-6 col-sm-3">
                                <input type="hidden" value="Cadastro" name="Usuario">
                                <button type="submit" class="btn btn-success">Salvar</button>
                                <button type="reset" class="btn btn-danger">Cancelar</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>