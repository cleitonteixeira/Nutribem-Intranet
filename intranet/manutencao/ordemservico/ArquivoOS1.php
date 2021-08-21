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
            <th>Finalizada em</th>
            <th>Abrir</th>
          </tr>
        </thead>
        <tbody id="lista-os">
      <?php
      $sql = "SELECT o.*, cd.Nome as Unidade, u.Nome as Responavel FROM os o INNER JOIN unidademt um ON um.idUnidadeMT = o.Unidade_idUnidade INNER JOIN usuarios u ON u.idusuarios = o.Responsavel_idResponsavel INNER JOIN cadastro cd ON cd.idCadastro = um.Cadastro_idCadastro WHERE o.DataHoraFinalizada IS NOT NULL";
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
            <td class="text-center"><?=date("d/m/Y H:i:s", strtotime($k->DataHoraFinalizada));?></td>
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