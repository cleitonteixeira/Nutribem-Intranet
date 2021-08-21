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
      <h1 class="text-center">LISTA DE UNIDADES</h1>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Unidade</th>
            <th>Local</th>
            <th>Responsável</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT c.Nome AS Unidade, us.Nome AS Responsavel, e.* FROM unidademt u INNER JOIN cadastro c ON idCadastro = Cadastro_idCadastro INNER JOIN endereco e ON idEndereco = Endereco_idEndereco INNER JOIN usuarios us ON idusuarios = Responsavel_idResponsavel";
          $stmt = $conexao->prepare($sql);
          $stmt->execute();
          $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
          foreach ($rs as $r) {
          ?>
          <tr>
            <td><?=utf8_decode($r->Unidade);?></td>
            <td><?=utf8_decode($r->Cidade).'-'.$r->UF;?></td>
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