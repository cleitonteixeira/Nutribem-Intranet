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
<?php
$sql = "SELECT cdu.Nome AS Unidade, o.nOS, o.idOS, o.Unidade_idUnidade AS idUnidade, u.Nome AS Responsavel, u1.Nome AS Superior, (SELECT SUM(Valor) FROM custoos WHERE OS_idOS = o.idOS) AS CustoOS FROM os o INNER JOIN unidademt umt ON umt.idUnidadeMT = o.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idcadastro = umt.Cadastro_idCadastro INNER JOIN usuarios u ON u.idusuarios = umt.Responsavel_idResponsavel INNER JOIN usuarios u1 ON u1.idusuarios = u.Superior WHERE o.idOS = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $_GET['id']);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_OBJ);
?>

<script type="text/javascript">
$(function(){
  $(document).on('click', '.btn-vls', function(e) {
      e.preventDefault; 
      var valor     = $(this).closest('form').find('.ValorOS').val();
      valor = valor.replace(".","");
      valor = valor.replace(",",".");
      //alert(valor);
      var id        = $(this).closest('form').find('input[data-id]').data('id');
      //alert(id);
      var idOS      = $(this).closest('form').find('.CapOS').val();
      //alert(idOS);
      var idUnidade = $(this).closest('form').find('.CapUnidade').val();
      //alert(idUnidade);
      $.post('CustoOS.inc.php', {idItemOS: id, idOS1: idOS, idUnidade1: idUnidade, ValorOS: valor, SalvaValor: 'Salvar'}, function (dados){
      dados = JSON.parse(dados);
      //alert(dados);
      if (dados.length > 0){
        $.each(dados, function(i, obj){
            switch(obj.Dados){
              case 'Sucesso':
                $("#resultado").html('<div class="alert alert-success"><strong>Sucesso!</strong> Valor cadastrado com sucesso!</div>');
                $('#log').modal('hide');
                $('#log').modal('show');
                setTimeout(function() {
                    $('#log').modal('hide');
                }, 1700);
                $("#vTotal").html(obj.Valor);
                $("#LogCusto").html(obj.LogCusto);
                break;
              case 'Falha':
                $("#resultado").html('<div class="alert alert-danger"><strong>Erro!</strong> Falha no cadasatro do valor!</div>');
                $('#log').modal('hide');
                $('#log').modal('show');
                setTimeout(function() {
                    $('#log').modal('hide');
                }, 1700);
                $("#vTotal").html(obj.Valor);
                $("#LogCusto").html(obj.LogCusto);
                break;
              }
            }
         )
      }});
  });
});
$(function(){
  $(".ValorOS").maskMoney();
})
</script>
<div class="container-fluid">
  <div class="row conteudo">
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <h1 class="text-center">CUSTO DA OS: <strong><?=$r->nOS?></strong></h1>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 conteudo"></div>
      <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 text-center">
        <p><strong>Unidade: </strong><?=$r->Unidade;?></p>
      </div>
      <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 text-center">
        <p><strong>Responsável: </strong><?=$r->Responsavel;?></p>
      </div>
      <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 text-center">
        <p><strong>Superior: </strong><?=$r->Superior;?></p>
      </div>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr></div>
      <h3 class="text-center">INFORME O VALOR DA MANUTENÇÃO DE CADA EQUIPAMENTO:</h3>
      <?php
      $sql = "SELECT e.idEquipamento, e.Codigo, cde.Nome AS Equipamento, i.idItemOS FROM os o INNER JOIN itemos i ON i.OS_idOS = o.idOS INNER JOIN unidademt umt ON umt.idUnidadeMT = o.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idcadastro = umt.Cadastro_idCadastro INNER JOIN equipamento e ON e.idEquipamento = i.Equipamento_idEquipamento INNER JOIN cadastro cde ON cde.idcadastro = e.Cadastro_idCadastro WHERE o.idOS = ?";
      $stmt = $conexao->prepare($sql);
      $stmt->bindParam(1, $_GET['id']);
      $stmt->execute();
      $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
      foreach ($rs as $k) {
      ?>
      <form class="form-inline conteudo text-right col-xs-6 col-md-6 col-sm-6 col-lg-6">
        <div class="form-group">
          <label class="normal FontP" for="valor<?=$k->idItemOS;?>"><?=$k->Codigo.' - '.$k->Equipamento;?></label>
          <div class='input-group'>
            <span class='input-group-addon' id='real'>R$</span>
            <?php
            $sql = "SELECT IFNULL(Valor,0) AS Valor FROM custoos os WHERE Item_idItem = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $k->idItemOS);
            $stmt->execute();
            $rsj = $stmt->fetch(PDO::FETCH_OBJ);
            if(isset($rsj->Valor)){
            ?>
            <input type="text" class="form-control ValorOS" id="valor<?=$k->idItemOS;?>" id="valor<?=$k->idItemOS;?>" placeholder='0,00' aria-describedby='real' value="<?=number_format($rsj->Valor,2,',','.');?>" />
            <?php
            }else{
            ?>
            <input type="text" class="form-control ValorOS" id="valor<?=$k->idItemOS;?>" id="valor<?=$k->idItemOS;?>" placeholder='0,00' aria-describedby='real' value="" />
            <?php
            }
            ?>
          </div>
        </div>
        <input data-id="<?=$k->idItemOS;?>" value="<?=$k->idItemOS;?>" type="hidden" name="id" id="id">
        <input value="<?=$r->idUnidade;?>" class="CapUnidade" type="hidden" name="idUnidade" id="idUnidade">
        <input value="<?=$_GET['id'];?>" class="CapOS" type="hidden" name="idOs" id="idOs">
        <a class="btn btn-success btn-vls btn-sm">Salvar <i class="far fa-save"></i></a>
      </form>
      <?php
      }
      ?>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><hr>
        <h3 class="text-center">Valor Total: R$ <span id="vTotal"><?=number_format($r->CustoOS,2,',','.')?></span></h3>
      </div>
      <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 text-center">
        <a data-toggle="modal" data-target="#SelectEquipamento" class="btn btn-primary">HISTÓRICO DE ALTERAÇÕES <i class="fas fa-book-open"></i></a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="SelectEquipamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" role="dialog">
  <div class="modal-dialog modal-lg">
      <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">LOG DE ALTERAÇÕES NOS VALORES DAS OS's</h4>
      </div>
      <div class="modal-body" style="padding:40px 50px;">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th colspan="7" class="text-center"><a class="btn btn-primary">DOWNLOAD XLS <i class="far fa-file-excel"></i></a> <a class="btn btn-primary">DOWNLOAD PDF <i class="far fa-file-pdf"></i></a></th>
            </tr>
            <tr>
              <th>Lançado por</th>
              <th>Alterado por</th>
              <th>Tipo</th>
              <th>Data</th>
              <th>Valor Anterior</th>
              <th>Valor Atual</th>
              <th>Equipamento</th>
            </tr>
          </thead>
          <tbody id="LogCusto">
            <?php
            $sql = "SELECT l.vAnterior, l.vAtual, l.DataMovimenta, l.TipoMovimenta, u.Nome AS Alterador, up.Nome AS Responsavel, eq.Codigo, ce.Nome AS Equipamento FROM logcustoos l INNER JOIN usuarios u ON u.idusuarios = l.Usuario_idUsuario INNER JOIN custoos co ON co.idCustoOS = l.Custo_idCusto INNER JOIN itemos io ON io.idItemOS = co.Item_idItem INNER JOIN equipamento eq ON eq.idEquipamento = io.Equipamento_idEquipamento INNER JOIN cadastro ce ON ce.idCadastro = eq.Cadastro_idCadastro INNER JOIN usuarios up ON up.idusuarios = co.Usuario_idUsuario WHERE Custo_idcusto IN (SELECT idCustoOS FROM custoos WHERE OS_idOS = ?) ORDER BY l.idlogCustoOS DESC;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $_GET['id']);
            $stmt->execute();
            while($x = $stmt->fetch(PDO::FETCH_OBJ)){
            ?>
            <tr>
              <td class="FontP"><?=utf8_decode($x->Responsavel);?></td>
              <td class="FontP"><?=utf8_decode($x->Alterador);?></td>
              <td class="FontP"><?=utf8_decode($x->TipoMovimenta);?></td>
              <td><?=date("d/m/Y", strtotime($x->DataMovimenta));?></td>
              <td>R$ <?=number_format($x->vAnterior,"2",",",".");?></td>
              <td>R$ <?=number_format($x->vAtual,"2",",",".");?></td>
              <td class="FontP"><?=utf8_decode($x->Codigo)." - ".utf8_decode($x->Equipamento);?></td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
      
<div id="log" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div id="resultado"></div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>