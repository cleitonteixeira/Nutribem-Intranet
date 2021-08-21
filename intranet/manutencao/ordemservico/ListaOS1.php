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
$(document).ready(function(){
  $("#pesquisa").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#lista-os tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
<!-- Content -->
<div class="container-fluid">
  <div class="row conteudo">
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <h1 class="text-center">LISTA OS</h1>
      <?php
      $sql = "SELECT (SELECT COUNT(*) FROM `os` WHERE DataHoraAc IS NOT NULL AND DataHoraFinalizada IS NULL) AS Aceita, (SELECT COUNT(*) FROM `os` WHERE DataHoraFinalizada IS NULL) AS Aberta, (SELECT COUNT(*) FROM `os` WHERE DataHoraFinalizada IS NOT NULL) AS Concluida, (SELECT COUNT(*) FROM `os`) AS Total";
      $stmt = $conexao->prepare($sql);
      $stmt->execute();
      $rt = $stmt->fetch(PDO::FETCH_OBJ);
      ?>
      <p><small>Temos o total de <strong><?=$rt->Aberta;?> OS</strong> em aberto, <strong><?=$rt->Aceita;?></strong> com manutenção agendada e <strong><?=$rt->Concluida;?></strong> concluída num total de <strong><?=$rt->Total;?></strong> solicitadas pelo o sistema.</small></p>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th colspan="6"><input class="form-control" id="pesquisa" type="text" placeholder="Procurar..."></th>
          </tr>
          <tr>
            <th>OS</th>
            <th>Unidade</th>
            <th>Data Hora Solicitação</th>
            <th>Aberta Por</th>
            <th>Aceita</th>
            <th>Abrir</th>
          </tr>
        </thead>
        <tbody id="lista-os">
      <?php
      $sql = "SELECT o.*, cd.Nome as Unidade, u.Nome as Responavel FROM os o INNER JOIN unidademt um ON um.idUnidadeMT = o.Unidade_idUnidade INNER JOIN usuarios u ON u.idusuarios = o.Responsavel_idResponsavel INNER JOIN cadastro cd ON cd.idCadastro = um.Cadastro_idCadastro WHERE o.DataHoraFinalizada IS NULL";
      $stmt = $conexao->prepare($sql);
      $stmt->execute();
      $re = $stmt->fetchAll(PDO::FETCH_OBJ);
      foreach ($re as $k) {
      ?>
          <tr>
            <td class="text-center"><?=$k->nOS?></td>
            <td class="text-center"><?=$k->Unidade?></td>
            <td class="text-center"><?=date("d/m/Y H:i:s", strtotime($k->DataHoraAbertura));?></td>
            <td class="text-center"><?=$k->Responavel?></td>
            <td class="text-center"><?=$k->DataHoraAc == null ? 'Não' : 'Sim';?></td>
            <td class="text-center"><a class="btn btn-primary btn-sm" href="OS.php?id=<?=$k->idOS?>"><i class="far fa-folder-open"></i></a></td>
          </tr>
      <?php
      }
      ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
}
?>