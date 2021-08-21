<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoleivas.com.br/intranet");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <h1 class="text-center">CADASTRO PERÍODOS DE FECHAMENTO</h1>
            <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/ContratoDAO.php" method="post" enctype="multipart/form-data" target="_blank" class="form-horizontal" data-toggle="validator" id="FormCliente" name="FormCliente">
                
                <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-3" for="desc">Descrição:</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" placeholder="PREENCHER COM LETRAS MAIUSCULAS" required name="desc" id="desc" minlength="10"/>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>

                <div class="col-xs-4 col-md-4 col-lg-4 col-xs-offset-2 col-md-offset-2 col-lg-offset-2">
                    <button class="btn btn-success" type="submit">Enviar</button>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-12"><hr /></div>
                <div class="col-xs-12 col-md-12 col-lg-12">
                    <h4 class="text-center"><u>PERÍODOS PREDEFINIDOS</u></h4>
                    <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                  <?php
                    $sql = "SELECT * FROM fechamento;";
                    $stmt = $conexao->prepare($sql);
                    $stmt->execute();
                    $x = 1;
                    while($r = $stmt->fetch(PDO::FETCH_OBJ)){
                    ?>
                    <div class="col-md-3 col-lg-3 col-xs-3">
                        <p><span><?=$r->idFechamento;?> - <strong><?=$r->Descricao;?></strong></span></p>
                    </div>
                    <?php
                      $x += 1;
                    }
                    ?>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
      require_once("../control/arquivo/footer/Footer.php");
}
?>