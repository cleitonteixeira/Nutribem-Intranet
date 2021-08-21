<?php
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['idusuarios'])){
        session_destroy();
        require_once("../control/arquivo/funcao/Outras.php");
        require_once("../control/banco/conexao.php");
        require_once("../control/arquivo/header/Header.php");
        require_once("../control/arquivo/Login.php");
    }else{
        require_once("../control/Pacote.php");
?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 conteudo"><h1 class="text-center">Unidade Faturamento</h1></div>
            <div class="col-md-6 ">
                <h3 class="text-center">Cadastro Unidade Faturamento</h3>

                <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/UnidadeDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator">
                    <div class="col-xs-12">

                        <div class="form-group">
                            <label class="col-sm-5 control-label" for="nome">Nome:</label>
                            <div class="col-sm-6">
                                <input type="text" name="nome" id="nome" class="form-control" required="required" data-match-error="Digie o nome!">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-5 control-label" for="UnidadeF">Unidade de Fornecimento:</label>  
                            <div class="col-sm-5">
                               <select class="selectpicker" title="Selecione uma Unidade de Fornecimento." name="UnidadeF" id="UnidadeF" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma Unidade de Fornecimento." required>
                                <?php
                                    $conexao = conexao::getInstance();
                                    $sql = " SELECT * FROM grupo;";
                                    $stm = $conexao->prepare($sql);
                                    $stm->execute();
                                    while($row= $stm->fetch(PDO::FETCH_OBJ)){
                                ?>
                                    <optgroup label="<?php echo utf8_decode($row->Nome); ?>" >

                                <?php
                                    $sql1 = " SELECT * FROM unidadefornecimento WHERE Grupo_idGrupo = ?;";
                                    $stm1 = $conexao->prepare($sql1);
                                    $stm1->bindParam(1, $row->idGrupo);
                                    $stm1->execute();
                                    if($stm1->rowCount() == 0){
                                   ?>
                                <script>
                                    alert("Nenhuma Unidade de Fornecimento Cadastrada!");
                                    location.href = 'UnidadeFornecimento.php';
                                </script>
                                   <?php
                                    }else{
                                        while($row1 = $stm1->fetch(PDO::FETCH_OBJ)){
                                   ?>
                                        <option data-tokens="<?php echo utf8_decode($row1->Nome);?>" value="<?php echo $row1->idUnidadeFornecimento ?>"><?php echo utf8_decode($row1->Nome);?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                    </optgroup>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-5"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-9 col-sm-2">
                                <input type="hidden" value="Cadastro" name="Unidade">
                                <button type="submit" class="btn btn-success">Salvar</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <h3 class="text-center">Unidades existentes</h3>
                <div class="panel-group" id="accordion">
                <?php
                    $conexao = conexao::getInstance();
                    $sql = " SELECT * FROM unidadefornecimento;";
                    $stm = $conexao->prepare($sql);
                    $stm->execute();
                    while($row = $stm->fetch(PDO::FETCH_OBJ)){
                    ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo utf8_decode($row->idUnidadeFornecimento)?>"><?php echo utf8_decode($row->Nome)?></a>
                            </h4>
                        </div>
                       <div id="<?php echo utf8_decode($row->idUnidadeFornecimento)?>" class="panel-collapse collapse">
                            <ul class="list-group">
                <?php
                    $sql1 = "SELECT * FROM unidadefaturamento INNER JOIN cadastro ON idCadastro = Cadastro_idCadastro WHERE Fornecimento_idFornecimento = ?;";
                    $stm1 = $conexao->prepare($sql1);
                    $stm1->bindParam(1 , $row->idUnidadeFornecimento);
                    $stm1->execute();
                        ?>
                       
                    <?php
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
    require_once("../control/arquivo/footer/Footer.php");
}
?>