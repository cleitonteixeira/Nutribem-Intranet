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
    $("#TableEquipamento tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});  
</script>
<!-- Content -->
<div class="container-fluid">
  <div class="row conteudo">
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 text-center">
      <h1 class="text-center">LISTA DE EQUIPAMENTOS</h1>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th colspan="7"><input class="form-control" id="pesquisa" type="text" placeholder="Procurar..."></th>
          </tr>
          <tr>
            <th>Equipamento</th>
            <th>Codigo</th>
            <th>Fabricante</th>
            <th>Alimentação</th>
            <th>Categoria</th>
            <th>Local</th>
            <th>Responsável</th>
          </tr>
        </thead>
        <tbody id="TableEquipamento">
          <?php
          $sql = "SELECT eq.*, cd.Nome AS Equipamento, cdu.Nome AS Unidade, us.Nome AS Responsavel, cat.Nome AS Categoria FROM equipamento eq INNER JOIN cadastro cd ON cd.idCadastro = eq.Cadastro_idCadastro INNER JOIN unidademt um ON um.idUnidadeMT = eq.Unidade_idUnidade INNER JOIN categoriaequipamento cat ON cat.idCategoriaEquipamento = eq.Categoria_idCategoria INNER JOIN cadastro cdu ON cdu.idCadastro = um.Cadastro_idCadastro INNER JOIN usuarios us ON us.idusuarios = um.Responsavel_idResponsavel";
          $stmt = $conexao->prepare($sql);
          $stmt->execute();
          $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
          foreach ($rs as $r) {
          ?>
          <tr>
            <td><?=utf8_decode($r->Codigo);?></td>
            <td><?=utf8_decode($r->Equipamento);?></td>
            <td><?=utf8_decode($r->Fabricante);?></td>
            <td><?=utf8_decode($r->Alimentacao);?></td>
            <td><?=utf8_decode($r->Categoria);?></td>
            <td><?=utf8_decode($r->Unidade);?></td>
            <td><?=utf8_decode($r->Responsavel);?></td>
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