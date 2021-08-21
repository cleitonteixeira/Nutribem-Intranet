<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    
    
    $option = '';
    $sql = "SELECT * FROM categoria";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    while($r = $stmt->fetch(PDO::FETCH_OBJ)){
        $option .= "<option data-tokens='".$r->Nome." ".$r->idCategoria."' value='".$r->idCategoria."' >".str_pad($r->idCategoria, 3, 0, STR_PAD_LEFT)." - ".$r->Nome."</option>";
    }
?>
<!-- Content -->
<div class="container-fluid">
    <div class="row conteudo">
        <div class="col-md-12 col-xs-12 col-lg-12 text-center">
            <h1 class="text-center"><u>CADASTRAR CATEGORIA</u></h1>
            <div class="col-md-12 col-xs-12 col-lg-12"> </div>
            <form action="<?=BASE;?>control/banco/CategoriaDAO.php" method="post" enctype="multipart/form-data" class="form-inline" data-toggle="validator">            
                <div class="form-group">
                    <label for="categoria">NOME:</label>
                    <input class="form-control" type="text" value="" name="categoria" id="categoria" require />
                </div>
                <input type="hidden" name="Categoria" id="Categoria" value="Cadastrar" />
                <button class="btn btn-success" type="submit">Salvar</button>
            </form>
            <div class="col-xs-12 col-md-12 col-lg-12"><hr /></div>
            <div class="col-xs-12 col-md-12 col-lg-12">
                <h4 class="text-center"><u>CATEGORIAS CADASTRADAS</u></h4>
                <div class="col-xs-12 col-md-12 col-lg-12"> </div>
              <?php
                $sql = "SELECT * FROM categoria;";
                $stmt = $conexao->prepare($sql);
                $stmt->execute();
                $x = 1;
                while($r = $stmt->fetch(PDO::FETCH_OBJ)){
                ?>
                <div class="col-md-3 col-lg-3 col-xs-3">
                    <p><span><?=str_pad($r->idCategoria, 3, 0, STR_PAD_LEFT);?> - <strong><?=utf8_decode($r->Nome);?></strong></span></p>
                </div>
                <?php
                  $x += 1;
                }
                ?>
            </div>
        </div>
	</div>
</div>
<?php
}
?>