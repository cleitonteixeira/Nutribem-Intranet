<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    unset($_SESSION['codigos']);
    unset($_FILES);
    unset($_SESSION['equipamento']);
    unset($_SESSION['cont']);
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

  $("#unidade").on("change", function() {
    var unMT = $("#unidade option:selected").val();
    $.post('AddEquipamento.inc.php', {LoadItem: 'lItem', unMt: unMT }, function (dados1){
    dados1 = JSON.parse(dados1);
    //alert(dados);
    if (dados1.length > 0){
      $.each(dados1, function(i, obj1){
        //alert(obj1.Dados1);
        $('#TableEquipamento').html(obj1.Dados1).show();
      })
      }
    })
  });

  $(document).on("click", "#AddEquip", function(e) {
    e.preventDefault;
    var id = $("#id").val();
    var defeito = $("#defeito").val();
    var itemSessao = 'itemSessao';
    var unMT = $("#unidade").val();
     $.post('AddEquipamento.inc.php', {addItemSessao: itemSessao, id: id, defeito: defeito}, function (dados){
      dados = JSON.parse(dados);
      //alert(dados);
      if (dados.length > 0){
        $.each(dados, function(i, obj){
            switch(obj.Retorno){
              case 'Erro 1':
                $("#resultado").html('<div class="alert alert-danger"><strong>Erro!</strong> Selecione um Equipamento!</div>');
                $('#log').modal('hide');
                $('#log').modal('show');
                setTimeout(function() {
                    $('#log').modal('hide');
                }, 1700);
                break;
              case 'Erro 2':
                $("#resultado").html('<div class="alert alert-danger"><strong>Erro!</strong> Digite no minimo 25 caracteres para definir o defeito!</div>');
                $('#log').modal('hide');
                $('#log').modal('show');
                setTimeout(function() {
                    $('#log').modal('hide');
                }, 1700);
                break;
              case 'Erro 3':
                $("#resultado").html('<div class="alert alert-danger"><strong>Erro!</strong> Digite o defeito!</div>');
                $('#log').modal('hide');
                $('#log').modal('show');
                setTimeout(function() {
                    $('#log').modal('hide');
                }, 1700);
                break;
              case 'Sucesso':
                $("#resultado").html('<div class="alert alert-success"><strong>Sucesso!</strong> Sucesso ao adicionar equipamento!</div>');
                $('#log').modal('hide');
                $('#log').modal('show');

                $.post('AddEquipamento.inc.php', {LoadItem: 'lItem', unMt: unMT }, function (dados1){
                  dados1 = JSON.parse(dados1);
                  //alert(dados);
                  if (dados1.length > 0){
                    $.each(dados1, function(i, obj1){
                      //alert(obj1.Dados1);
                      $('#TableEquipamento').html(obj1.Dados1).show();
                    })
                  }
                })
                $("#id").val('');
                $("#equipamento").val('');
                $("#defeito").val('');
                setTimeout(function() {
                    $('#log').modal('hide');
                }, 1700);
                break;
            }
          })
        }
     })
  });
});
$(function(){
  $('#SelectEquipamento').on('hide.bs.modal', function() {
    var Showt = 'sItem';
    $.post('AddEquipamento.inc.php', {ShowItem: Showt}, function (dados){
      dados = JSON.parse(dados);
      //alert(dados);
      var valores = "";
      if (dados.length > 0){
        $.each(dados, function(i, obj){
          switch(obj.Dados){
            case 'Erro':
              $("#listar-item").html('<div class="alert alert-warning"><strong>Aviso!</strong> Nenhum equipamento adicionado a OS.</div>').show();
              setTimeout(function() {
                   $("#listar-item").html('').show();
              }, 5000);
              break;
            default:
              valores = obj.Dados;
              $("#listar-item").html(valores).show();
              break;
          }          
        })
      }
    });
  });
  $(document).on('click', '.btn-es', function(e) {
      e.preventDefault; 
      var equipamento = $(this).closest('tr').find('td[data-equipamento]').data('equipamento');
      $("#equipamento").val(equipamento);
      var id = $(this).closest('tr').find('td[data-id]').data('id');
      $("#id").val(id);
  });
});
</script>
<!-- Content -->
<div class="container-fluid">
  <div class="row conteudo">
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
      <h1 class="text-center">SOLICITAR OS</h1>
      <form name="Form" role="form" action="<?=BASE;?>control/banco/OsDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal conteudo text-center" data-toggle="validator">.
        <div class="col-xs-10 col-sm-10 col-lg-10 col-md-10">
          <div class="form-group">
            <label class="control-label col-sm-2" for="unidade">Unidade:</label>
            <div class="col-sm-4">
                <select name="unidade" id="unidade" required="required" class="selectpicker form-control" required="required" title="SELECIONE UMA UNIDADE" data-live-search="true">
                  <?php
                  $sql = "SELECT * FROM unidademt INNER JOIN cadastro ON idCadastro = Cadastro_idCadastro WHERE idUnidadeMT IN (SELECT Unidade_idUnidade FROM unidademtuser WHERE Usuario_idUsuario = ?);";
                  $stmt = $conexao->prepare($sql);
                  $stmt->bindParam(1, $_SESSION['idusuarios']);
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
        <div class="col-xs-10 col-sm-10 col-lg-10 col-md-10">
          <div class="form-group">
            <label class="control-label col-sm-2" for="nome">Equipamento:</label>
            <div class="col-sm-2">
              <a data-toggle="modal" data-target="#SelectEquipamento" class="btn btn-default">ADICIONAR EQUIPAMENTOS A OS <i class="far fa-plus-square"></i></a>
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="text-center col-xs-12 col-sm-12 col-lg-12 col-md-12">
          <div id="listar-item"></div>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12"> </div>
        <div class="col-xs-5 col-sm-5 col-lg-5 col-md-5">
          <div class="form-group">
            <input type="hidden" name="OS" value="Cadastrar">
            <button class="btn btn-success text-left" type="submit">Salvar <i class="fas fa-save"></i></button>
          </div>
        </div>
      </form>
    </div>
    </div>
</div>
<div class="modal fade" id="SelectEquipamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Adicionar Equipamentos a OS</h4>
            </div>
            <div class="modal-body" style="padding:40px 50px;">
                <form method="post" enctype="multipart/form-data" rel="form" class="form-horizontal" data-toggle="validator">
                  <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                          <th colspan="7"><input class="form-control" id="pesquisa" type="text" placeholder="Procurar..."></th>
                        </tr>
                        <tr>
                          <th>Código</th>
                          <th>Equipamento</th>
                          <th>Modelo</th>
                          <th>Fabricante</th>
                          <th>Categoria</th>
                          <th>Unidade</th>
                          <th></th>
                        </tr>
                    </thead>
                    <tbody id="TableEquipamento">
                    </tbody>
                  </table> 
                  <div class="form-group">
                    <label class="control-label col-sm-2" for="id">ID:</label>
                    <div class="col-sm-7">
                      <input type="text" name="id" id="id" readonly="readonly" required="required" class="form-control">
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2" for="equipamento">Equipamento:</label>
                    <div class="col-sm-7">
                      <input type="text" name="equipamento" readonly="readonly" id="equipamento" required="required" class="form-control" placeholder="Ex.: Forno Combinado">
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2" for="defeito">Defeito:</label>
                    <div class="col-sm-7">
                      <textarea name="defeito" id="defeito" minlength="25" maxlength="80" required="required" class="form-control" placeholder="Descrição sucinta do defeito. Minimo 25 caracteres."></textarea>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <span class="btn btn-primary" id="AddEquip">Adicionar <i class="fas fa-plus-circle"></i></span>
                  </div>
                </form>
            </div>
            <div class="modal-foot">  
              <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
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