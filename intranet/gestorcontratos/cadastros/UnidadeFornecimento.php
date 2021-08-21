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
<script type="text/javascript">$(document).ready(function(){	$("#cnpj").mask("99.999.999/9999-99");});</script>
<!-- Content -->
<div class="container-fluid">
    <script type="text/javascript">
        /* Máscaras ER */
        function mascara(o,f){
            v_obj=o
            v_fun=f
            setTimeout("execmascara()",1)
        }
        function execmascara(){
            v_obj.value=v_fun(v_obj.value)
        }
        function mtel(v){
            v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito
            v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
            v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos
            return v;
        }
        function id( el ){
            return document.getElementById( el );
        }
        window.onload = function(){
            id('telefone').onkeypress = function(){
                mascara( this, mtel );
            }
            id('celular').onkeypress = function(){
                mascara( this, mtel );
            }
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 conteudo"><h1 class="text-center">Unidade de Fornecimento</h1></div>
            <div class="col-md-6">
                <h3 class="text-center">Cadastro Unidade de Fornecimento</h3>
                <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/UnidadeFornecimentoDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator">
                    <div class="col-xs-12">   

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="nome">Nome:</label>
                            <div class="col-sm-10">
                                <input type="text" name="nome" id="nome" class="form-control" required="required" data-match-error="Digie o nome!">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="Grupo">Grupo:</label>  
                            <div class="col-sm-5">
                               <select class="selectpicker" title="Selecione um Grupo." name="Grupo" id="Grupo" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma Grupo." required>
                                <?php
                                    $conexao = conexao::getInstance();
                                    $sql = " SELECT * FROM grupo ;";
                                    $stm = $conexao->prepare($sql);
                                    $stm->execute();
                                    if($stm->rowCount() == 0):
                                   ?>
                                <script>
                                    alert("Nenhum Grupo Cadastrado!");
                                    location.href = 'Grupo.php';
                                </script>
                                   <?php
                                    else:
                                        while($row= $stm->fetch(PDO::FETCH_OBJ)):
                                   
                                   ?>
                                    <option data-tokens="<?php echo utf8_decode($row->Nome); ?>" value="<?php echo $row->idGrupo; ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                    <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-5"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-6 col-sm-3">
                            <input type="hidden" value="Cadastro" name="Unidade">
                            <button type="submit" class="btn btn-success">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <h3 class="text-center">Grupos e Unidades existentes</h3>
                <div class="panel-group" id="accordion">
                <?php
                    $conexao = conexao::getInstance();
                    $sql = " SELECT * FROM grupo;";
                    $stm = $conexao->prepare($sql);
                    $stm->execute();
                    while($row = $stm->fetch(PDO::FETCH_OBJ)){
                    ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo utf8_decode($row->idGrupo)?>"><?php echo utf8_decode($row->Nome)?></a>
                            </h4>
                        </div>
                        <div id="<?php echo utf8_decode($row->idGrupo)?>" class="panel-collapse collapse">
                            <ul class="list-group">

                <?php
                    $sql1 = " SELECT * FROM unidadefornecimento WHERE Grupo_idGrupo = ?;";
                    $stm1 = $conexao->prepare($sql1);
                    $stm1->bindParam(1 , $row->idGrupo);
                    $stm1->execute();
                    while($row1= $stm1->fetch(PDO::FETCH_OBJ)){
                    ?>
                            <li class="list-group-item"><?php echo utf8_decode($row1->Nome); ?></li>
                <?php
                    }
                ?>
                          </ul>
                        </div>
                    </div>

                    <?php
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
  endif;
require_once("../control/arquivo/footer/Footer.php");
?>