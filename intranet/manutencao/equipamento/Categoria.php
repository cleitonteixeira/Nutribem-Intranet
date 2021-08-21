<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    
?>
<!-- Content -->
<div class="container-fluid">
  <div class="row conteudo">
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 text-center">
      <h1 class="text-center">CADASTRAR CATEGORIA</h1>
      <form name="Form" role="form" action="<?=BASE;?>control/banco/CategoriaDAO.php" method="post" enctype="multipart/form-data" class="form-inline conteudo" data-toggle="validator">

        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12">
          <div class="form-group">
            <label for="categoria">Nome:</label>
            <input type="text" name="categoria" id="categoria" class="form-control" required="required">
            <input type="hidden" name="Categoria" value="Cadastrar">
            <button class="btn btn-success text-left" type="submit">Salvar <i class="fas fa-save"></i></button>
          </div>
        </div>
      </form>
     <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12">
      <hr />
      <div class="col-xs-12 col-md-12 col-lg-12">
        <h4 class="text-center"><u>CATEGORIAS CADASTRADAS</u></h4>
        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
        <?php
          $sql = "SELECT * FROM categoriaequipamento;";
          $stmt = $conexao->prepare($sql);
          $stmt->execute();
          $x = 1;
          while($r = $stmt->fetch(PDO::FETCH_OBJ)){
          ?>
          <div class="col-md-3 col-lg-3 col-xs-3">
              <p><span><?=str_pad($r->idCategoriaEquipamento, 3, 0, STR_PAD_LEFT);?> - <strong><?=utf8_decode($r->Nome);?></strong></span></p>
          </div>
          <?php
            $x += 1;
          }
          ?>
      </div>
     </div>
    </div>
	</div>
</div>
<?php
}
?>