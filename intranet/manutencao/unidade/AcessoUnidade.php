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
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <h1 class="text-center"><u>CONTROLE DE ACESSO A UNIDADE</u></h1>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
        <form class="form-inline" data-toggle="validator" action="AcessoUnidade.php" method="post" enctype="multipart/form-data">
          <div class="form-group col-lg-offset-2 col-sm-offset-2 col-md-offset-2 col-xs-offset-2 col-lg-8 col-sm-8 col-md-8 col-xs-8">
            <select class="form-control selectpicker text-center" name="unidade" id="unidade" title="Selecione uma Unidade" data-live-search="true" required="required" >
                <?php
                $sql = "SELECT u.*, c.Nome AS Unidade, us.Nome as Responsavel FROM unidademt u INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro INNER JOIN usuarios us ON us.idusuarios = u.Responsavel_idResponsavel";
                $stmt= $conexao->prepare($sql);
                $stmt->execute();
                while($r = $stmt->fetch(PDO::FETCH_OBJ)){
                ?>
                <option data-subtext="Responsável: <?=$r->Responsavel;?>" <?=isset($_POST['unidade']) && $_POST['unidade'] == $r->idUnidadeMT ? 'selected="selected"':'';?> value="<?=$r->idUnidadeMT;?>" data-tokens="<?=$r->idUnidadeMT.' '.$r->Unidade;?>" ><?=$r->Unidade;?></option>
                <?php
                }
                ?>
            </select>
          </div>
          <button type="submit" name="SelecionaUnidade" class="btn btn-primary">Enviar</button>
        </form>
      </div>
      <?php
      if(isset($_POST['SelecionaUnidade'])){
        $sql1 = "SELECT * FROM unidademt INNER JOIN cadastro ON idCadastro = Cadastro_idCadastro WHERE idUnidadeMT = ?";
        $stmt= $conexao->prepare($sql1);
        $stmt->bindParam(1, $_POST['unidade']);
        $stmt->execute();
        $rumt = $stmt->fetch(PDO::FETCH_OBJ);
        $sql1 = "SELECT * FROM unidademtuser WHERE Unidade_idUnidade = ?";
        $stmt = $conexao->prepare($sql1);
        $stmt->bindParam(1, $_POST['unidade']);
        $stmt->execute();
        $user1 = array(1,4,5,6,27);
        array_push($user1, $rumt->Responsavel_idResponsavel);
        $user = array();
        while ($ru = $stmt->fetch(PDO::FETCH_OBJ)){
          array_push($user, $ru->Usuario_idUsuario);
        }
      ?>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /></div>
      <form class="form-horizontal" method="post" action="<?=BASE;?>control/banco/UnidadeDAO.php" method="post" enctype="multipart/form-data">
        <h3 class="text-center">QUEM PODE TER ACESSO A UNIDADE: <strong><?=$rumt->Nome;?></strong></strong></h3>
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"> </div>
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
        <?php
        $sql = "SELECT * FROM usuarios WHERE Nome NOT LIKE '%RH%' AND Nome NOT LIKE '%Estagiario%' AND Nome NOT LIKE '%Faturamento%' AND Nome NOT LIKE '%Trabalho%' AND Ativo = 0;";
        $stmt= $conexao->prepare($sql);
        $stmt->execute();
        while($r = $stmt->fetch(PDO::FETCH_OBJ)){
          ?>
          <div class="col-sm-3 col-xs-3 col-lg-3 col-md-3">
            <div class="form-group">
              <div class="checkbox">
                <label><input name="user[]" <?=in_array($r->idusuarios, $user) || in_array($r->idusuarios, $user1) ? 'checked="checked"':'';?> type="checkbox" value="<?=$r->idusuarios;?>"><?=$r->Nome;?></label>
              </div>
            </div>
          </div>
        <?php
        }
        ?>
        </div>
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /></div>
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
          <div class="form-group">
            <input type="hidden" name="unidade" value="<?=$_POST['unidade'];?>">
            <button type="submit" name="SalvaAcessoUnidade" class="btn btn-success">Salvar</button>
          </div>
        </div>
      </form>
      <?php
      }
      ?>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"> </div>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"> </div>
    </div>
  </div>
</div>
<?php
}
?>