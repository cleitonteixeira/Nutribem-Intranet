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
<!-- Content -->
<div class="container-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="conteudo col-xs-12 col-lg-12 col-md-12"><h1 class="text-center">Cadastro Grupo</h1></div>
            <div class="col-md-12"> </div>
            <div class="col-md-5 col-md-offset-1"> 
                <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/GrupoDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator">

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="nome">Nome:</label>
                        <div class="col-sm-8">
                            <input type="text" name="nome" id="nome" class="form-control" required="required" data-match-error="Digie o nome!">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="descricao">Descrição:</label>
                        <div class="col-sm-8">
                            <textarea name="descricao" id="descricao" class="form-control" required="required" data-match-error="Digie a descrição!" maxlength="100" minlength="50"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" value="Cadastro" name="Grupo">
                        <button type="submit" class="btn btn-success col-md-offset-1">Salvar</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="list-group">
                <?php
                    $conexao = conexao::getInstance();
                    $sql = " SELECT * FROM grupo ;";
                    $stm = $conexao->prepare($sql);
                    $stm->execute();
                    while($row= $stm->fetch(PDO::FETCH_OBJ)){
                    ?>
                    <a href="#" class="list-group-item lista-grupo">
                    <h4 class="list-group-item-heading"><?php echo utf8_decode($row->Nome); ?></h4>
                    <p class="list-group-item-text"><?php echo utf8_decode($row->Descricao); ?></p>
                    </a>
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