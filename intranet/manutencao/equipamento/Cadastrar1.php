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
      <h1 class="text-center">CADASTRAR EQUIPAMENTO</h1>
      <form name="Form" role="form" action="<?=BASE;?>control/banco/EquipamentoDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal conteudo" data-toggle="validator">
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-5" for="nome">Equipamento:</label>
            <div class="col-sm-7">
              <input type="text" name="nome" id="nome" required="required" class="form-control" placeholder="Ex.: Forno Combinado">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-2" for="fabricante">Fabricante:</label>
            <div class="col-sm-7">
              <input type="text" name="fabricante" id="fabricante" required="required" class="form-control" placeholder="Ex.: Venâncio">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12"></div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-5" for="modelo">Modelo:</label>
            <div class="col-sm-7">
              <input type="text" name="modelo" id="modelo" required="required" class="form-control" placeholder="Ex.: 710">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-2" for="alimentacao">Alimentação:</label>
            <div class="col-sm-7">
              <select class="selectpicker form-control" name="alimentacao" id="alimentacao" title="SELECIONE O TIPO DE ALIMENTAÇÃO" required="required">
                <option value="110v">110v</option>
                <option value="220v">220v</option>
                <option value="Bivolt">Bivolt</option>
                <option value="Gás">Gás</option>
                <option value="N/A">N/A</option>
              </select>
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12"></div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-5" for="dFabrica" >Data Fabricação:</label>
            <div class="col-sm-7">
              <input type="date" name="dFabrica" id="dFabrica" required="required" class="form-control" required="required">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-2" for="categoria">Categoria:</label>
            <div class="col-sm-7">
                <select name="categoria" id="categoria" required="required" class="selectpicker form-control" required="required" title="SELECIONE UMA CATEGORIA" data-live-search="true">
                  <?php
                  $sql = "SELECT * FROM categoriaequipamento;";
                  $stmt = $conexao->prepare($sql);
                  $stmt->execute();
                  $x = 1;
                  while($r = $stmt->fetch(PDO::FETCH_OBJ)){
                  ?>
                  <option data-tokens="<?=utf8_decode($r->Nome)." ".$r->idCategoriaEquipamento;?>" value="<?=$r->idCategoriaEquipamento;?>" ><?=str_pad($r->idCategoriaEquipamento, 3, 0, STR_PAD_LEFT)." - ".utf8_decode($r->Nome);?></option>.
                  <?php
                  }
                  ?>
                </select>
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12"></div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-5" for="unidade">Unidade:</label>
            <div class="col-sm-7">
                <select name="unidade" id="unidade" required="required" class="selectpicker form-control" required="required" title="SELECIONE UMA UNIDADE" data-live-search="true">
                  <?php
                  $sql = "SELECT * FROM unidademt INNER JOIN cadastro ON idCadastro = Cadastro_idCadastro;";
                  $stmt = $conexao->prepare($sql);
                  $stmt->execute();
                  while($r = $stmt->fetch(PDO::FETCH_OBJ)){
                  ?>
                  <option data-tokens="<?=utf8_decode($r->Nome)." ".$r->idUnidadeMT;?>" value="<?=$r->idUnidadeMT;?>" ><?=str_pad($r->idUnidadeMT, 3, 0, STR_PAD_LEFT)." - ".utf8_decode($r->Nome);?></option>.
                  <?php
                  }
                  ?>
                </select>
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12"></div>
        <div class="col-xs-5 col-sm-5 col-lg-5 col-md-5">
          <div class="form-group">
            <input type="hidden" name="Equipamento" value="Cadastrar">
            <button class="btn btn-success text-left" type="submit">Salvar <i class="fas fa-save"></i></button>
          </div>
        </div>
      </form>
    </div>
	</div>
</div>
<?php

}
?>