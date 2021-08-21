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
            <th colspan="8"><input class="form-control" id="pesquisa" type="text" placeholder="Procurar..."></th>
          </tr>
          <tr>
            <th>OS</th>
            <th>Unidade</th>
            <th>Equipamento</th>
            <th>Data Hora Solicitação</th>
            <th>Aberta Por</th>
            <th>Finalizada em</th>
            <th>Valor OS</th>
            <th>Abrir</th>
          </tr>
        </thead>
        <tbody id="lista-os">
      <?php
      $sql = "SELECT o.*, cd.Nome as Unidade, u.Nome as Responavel, (SELECT SUM(Valor) FROM custoos WHERE OS_idOS = o.idOS) AS CustoOS FROM os o INNER JOIN unidademt um ON um.idUnidadeMT = o.Unidade_idUnidade INNER JOIN usuarios u ON u.idusuarios = o.Responsavel_idResponsavel INNER JOIN cadastro cd ON cd.idCadastro = um.Cadastro_idCadastro WHERE o.DataHoraFinalizada IS NOT NULL AND o.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidademtuser WHERE Usuario_idUsuario = ?)";
      $stmt = $conexao->prepare($sql);
      $stmt->bindParam(1, $_SESSION['idusuarios']);
      $stmt->execute();
      $re = $stmt->fetchAll(PDO::FETCH_OBJ);
      foreach ($re as $k) {
        $sql = "SELECT Codigo, Nome AS Equipamento FROM itemos INNER JOIN equipamento ON idEquipamento = Equipamento_idEquipamento INNER JOIN cadastro ON idCadastro = Cadastro_idCadastro WHERE OS_idOS = ? LIMIT 1";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $k->idOS);
        $stmt->execute();
        $rst = $stmt->fetch(PDO::FETCH_OBJ);
      ?>
          <tr>
            <td class="text-center"><?=$k->nOS?></td>
            <td class="text-center"><?=$k->Unidade?></td>
            <td class="text-center FontP"><?=$rst->Codigo.' - '.utf8_decode($rst->Equipamento)?></td>
            <td class="text-center"><?=date("d/m/Y H:i:s", strtotime($k->DataHoraAbertura));?></td>
            <td class="text-center"><?=$k->Responavel?></td>
            <td class="text-center"><?=date("d/m/Y H:i:s", strtotime($k->DataHoraFinalizada));?></td>

            <?php if($k->CustoOS == null){?>
              <?php if($_SESSION['idusuarios'] == 4 || $_SESSION['idusuarios'] == 1){?>
              <td class="text-center"><a href="CustoOS.php?id=<?=$k->idOS;?>" title="Clique aqui para informar o valor da OS." class="btn btn-primary"><i class="fa fa-plus-circle"></i></a></td>
              <?php }else{ ?>
              <td class="text-center"><a title="Valor Ainda não informado pela manutenção." class="btn btn-primary"><i class="far fa-question-circle"></i></a></td>
              <?php } ?>
            <?php }else{ ?>
            <td class="text-center">R$ <?=number_format($k->CustoOS,2,',','.')?></td>
            <?php } ?>
            <td class="text-center"><a class="btn btn-primary btn-sm" href="OS.php?id=<?=$k->idOS?>"><i class="far fa-folder-open"></i></a> <?php if($_SESSION['idusuarios'] == 4 || $_SESSION['idusuarios'] == 1){?><a class="btn btn-primary btn-sm" href="CustoOS.php?id=<?=$k->idOS?>"><i class="fas fa-hand-holding-usd"></i></a><?php } ?></td>
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