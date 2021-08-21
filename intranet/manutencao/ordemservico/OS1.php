<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<script>
$(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});
</script>
<!-- Content -->
<div class="container-fluid">
  <div class="row conteudo">
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <?php
      $sql = "SELECT o.*, cd.Nome as Unidade, u.Nome as Responavel, su.Nome AS Super FROM os o INNER JOIN unidademt um ON um.idUnidadeMT = o.Unidade_idUnidade INNER JOIN usuarios u ON u.idusuarios = o.Responsavel_idResponsavel INNER JOIN cadastro cd ON cd.idCadastro = um.Cadastro_idCadastro INNER JOIN usuarios su ON su.idusuarios = u.Superior WHERE o.idOS = ? ORDER BY DataHoraAbertura LIMIT 1";
      $stmt = $conexao->prepare($sql);
      $stmt->bindParam(1, $_GET['id']);
      $stmt->execute();
      $r = $stmt->fetch(PDO::FETCH_OBJ);
      ?>
      <h1 class="text-center"><strong>OS Nº: </strong><?=$r->nOS;?></h1>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 conteudo"></div>
      <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
        <p><strong>Unidade: </strong><?=$r->Unidade;?></p>
      </div>
      <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
        <p><strong>Data/Hora: </strong><?=date("d/m/Y H:i:s", strtotime($r->DataHoraAbertura));?></p>
      </div>
      <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
        <p><strong>Responsável: </strong><?=$r->Responavel;?></p>
      </div>
      <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
        <p><strong>Superior: </strong><?=$r->Super;?></p>
      </div>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /><h3 class="text-center">Lista OS</h3></div>
      <?php
      $sql = "SELECT cd.Nome AS nEquipamento, io.*, ce.Nome AS Categoria, eq.* FROM itemos io INNER JOIN equipamento eq ON eq.idEquipamento = io.Equipamento_idEquipamento INNER JOIN cadastro cd ON cd.idCadastro = eq.Cadastro_idCadastro INNER JOIN categoriaequipamento ce ON ce.idCategoriaEquipamento = eq.Categoria_idCategoria WHERE io.OS_idOS = ?";
      $stmt = $conexao->prepare($sql);
      $stmt->bindParam(1, $_GET['id']);
      $stmt->execute();
      $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
      foreach ($rs as $eq) {
      ?>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /></div>
      <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4">
        <p><strong>Equipamento: </strong><?=utf8_decode($eq->nEquipamento);?></p>
      </div>
      <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4">
        <p><strong>Modelo: </strong><?=utf8_decode($eq->Modelo);?></p>
      </div>
      <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4">
        <p><strong>Fabricante: </strong><?=utf8_decode($eq->Fabricante);?></p>
      </div>
      <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
        <p><strong>Alimentação: </strong><?=utf8_decode($eq->Alimentacao);?></p>
      </div>
      <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3">
        <p><strong>Data Fabricação: </strong><?=date("d/m/Y", strtotime($eq->dFabrica));?></p>
      </div>
      <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6">
        <p><strong>Defeito: </strong><?=utf8_decode($eq->Comentario);?></p>
      </div>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"></div>
      <?php
        $sql = "SELECT * FROM anexoos WHERE Item_idItem = ?;";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $eq->idItemOS);
        $stmt->execute();
        $rst = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($rst as $ft) {
      ?>
        <div class="col-xs-1 col-md-1 col-sm-1 col-lg-1">
          <a href="../equipamento/doc/<?=$ft->nFantasia?>" data-toggle="lightbox">
              <img src="../equipamento/doc/<?=$ft->nFantasia?>" class="img-thumbnail" title="<?=$ft->Arquivo;?>" >
          </a>
        </div>
      <?php
        }
      }
      ?>
    </div>
    <?php
    $manutencao = array( 1, 4,5,6 );
    if(in_array($_SESSION['idusuarios'], $manutencao) && $r->DataHoraAc == null){
    ?>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /></div>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <form method="post" enctype="multipart/form-data" rel="form" class="form-inline" data-toggle="validator" action="<?=BASE;?>control/banco/OsDAO.php">
        <div class="form-group">
          <label for="dataag">Data Agenda:</label>
            <input class="form-control" type="date" name="dataag" id="dataag" required="required">
        </div>
        <input type="hidden" name="os" value="<?=$_GET['id'];?>">
        <input type="hidden" name="nOS" value="<?=$r->nOS;?>">
        <input type="hidden" name="Aceite" value="Aceitar">
        <button class="btn btn-success" type="submit">ACEITAR OS</button>
      </form>
    </div>
    <?php
    }elseif(!$r->DataHoraAc == null){
      $sql = "SELECT Nome FROM `os` INNER JOIN usuarios ON idusuarios = Manutencao_idManutencao WHERE idOS = ?;";
      $stmt = $conexao->prepare($sql);
      $stmt->bindParam(1, $_GET['id']);
      $stmt->execute();
      $rsu = $stmt->fetch(PDO::FETCH_OBJ);
    ?>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /></div>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <p>A ordem de serviço foi aceita por <strong><?=$rsu->Nome;?></strong> no dia <strong><?=date("d/m/Y H:i:s", strtotime($r->DataHoraAc))?></strong> e a visita foi agendada para o dia <strong><?=date("d/m/Y", strtotime($r->DataAgenda))?></strong>.</p>
    </div>
    <?php
      if(in_array($_SESSION['idusuarios'], $manutencao)){
    ?>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <a class="btn btn-success" target="blank" href="<?=BASE;?>ordemservico/OSPdf.php?id=<?=$_GET['id'];?>">DOWNLOAD OS EM PDF <i class="far fa-file-pdf"></i></a>
    </div>
    <?php
      }
    }else{
    ?>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /></div>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <p>A ordem de serviço não recebeu o aceite da manutenção.</p>
    </div>
    <?php
    }
    if(!$r->DataHoraAc == null){
    ?>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr /><h3 class="text-center"><u>Comentários OS</u></h3></div>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 conteudo">
      <?php
      $sql = "SELECT * FROM os WHERE idOS = ?;";
      $stmt = $conexao->prepare($sql);
      $stmt->bindParam(1, $_GET['id']);
      $stmt->execute();
      $cond = $stmt->fetch(PDO::FETCH_OBJ);
      if ($cond->DataHoraFinalizada == null) {
      ?>
      <form method="post" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator" action="<?=BASE;?>control/banco/OsDAO.php">
        <div class="form-group">
          <label for="comentario" class="control-label col-sm-1">Comentário:</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="comentario" required="required" maxlength="150" minlength="50"></textarea>
            <div class="help-block with-errors"></div>
          </div>
        </div>
        <input type="hidden" name="os" value="<?=$_GET['id'];?>">
        <input type="hidden" name="Comentario" value="Comentar">
        <button class="btn btn-success" name="BTComentario" type="submit">Enviar Atualização</button>
        <?php
          if($cond->Responsavel_idResponsavel == $_SESSION['idusuarios']){
        ?>
        <button class="btn btn-danger" name="BTFinaliza" type="submit">Finalizar OS</button>
         <?php
        }
        ?>
      </form>
      <?php
      }else{
      ?>
      <p>OS finalizada em <strong><?=date("d/m/Y à\s H:i:s");?></strong>.</p>
      <?php
      }
      $sql = "SELECT * FROM comentarioos INNER JOIN usuarios ON idusuarios = Usuario_idUsuario WHERE OS_idOS = ? ORDER BY DataHora DESC;";
      $stmt = $conexao->prepare($sql);
      $stmt->bindParam(1, $_GET['id']);
      $stmt->execute();
      $rsa = $stmt->fetchAll(PDO::FETCH_OBJ);
      foreach ($rsa as $cr) {
      ?>
      <div class="panel panel-default">
        <div class="panel-heading">No dia <strong><?=date("d/m/Y à\s H:i:s ", strtotime($cr->DataHora));?></strong> o usuário <strong><?=$cr->Nome;?></strong> disse:</div>
        <div class="panel-body">
          <p><?=utf8_decode($cr->Comentario);?></p>
        </div>
      </div>
      <?php
      }
      ?>
    </div>
    <?php
    }
    ?>
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"> </div>
  </div>
</div>
<?php
}3
?>