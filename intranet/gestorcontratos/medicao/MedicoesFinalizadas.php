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
    $("#dMedicao tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
<div class="container-fluid">
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 conteudo">
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h2 class="text-center">MEDIÇÕES FINALIZADAS</h2></div>
        <form  name="Form" role="form" action="GeraMedicao.php" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" target="_blank" id="FormCliente" name="FormCliente" >
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="8"><input class="form-control" id="pesquisa" type="text" placeholder="Search.."></th>
                    </tr>
                    <tr class="header_medicao">
                        <th class="inf_medicao" colspan="3">INFORMAÇÕES DA MEDIÇÃO</th>
                        <th class="inf_medicao" colspan="1">APROVAÇÕES</th>
                        <th class="inf_medicao" colspan="2">CONCLUIDA</th>
                        <th class="inf_medicao" colspan="1">EXTRAS</th>
                    </tr>
                    <tr>
                        <th class="inf_medicao">CONTRATO</th>
                        <th class="inf_medicao">Nº MEDIÇÃO</th>                        
                        <th class="inf_medicao">UNIDADE</th>
                        <th class="inf_medicao">FATURAMENTO</th>
                        <th class="inf_medicao">FINALIZADA POR</th>
                        <th class="inf_medicao">DATA</th>
                        <th class="inf_medicao">DETALHES</th>
                    </tr>
                </thead>
                <tbody id="dMedicao">
                <?php
                $sql = "SELECT m.idMedicao ,cd.Nome AS Cliente, cdtf.Nome AS Unidade, c.nContrato, c.cCusto, m.Medicao, m.dInicio AS Inicio, m.dFinal AS Final, m.Documento, m.dMedicao AS Envio, m.Situacao, m.FinalizadaPor, m.dFinalizada FROM medicao m INNER JOIN contrato c ON c.idContrato = m.Contrato_idContrato INNER JOIN unidadefaturamento unf ON unf.idUnidadeFaturamento = c.Unidade_idUnidade INNER JOIN cadastro cdtf ON cdtf.idCadastro = unf.Cadastro_idCadastro INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cd ON cd.idCadastro = ct.Cadastro_idCadastro WHERE  unf.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) AND m.Finalizada = 'S' ORDER BY m.idMedicao DESC;";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $_SESSION['idusuarios']);
                $stmt->execute();
                $resultSet = $stmt->fetchAll(PDO::FETCH_OBJ);
                if($stmt->rowCount() > 0){
                    $c = 0;
                    foreach($resultSet as $r){
                ?>
                    <tr>
                        <td class="inf_medicao text-left"><?php echo utf8_decode($r->Cliente)." - ".utf8_decode($r->cCusto);?></td>
                        <td class="inf_medicao1"><?php echo utf8_decode($r->Medicao);?></td>
                        <td class="inf_medicao1"><?php echo utf8_decode($r->Unidade);?></td>
                        <td class="inf_medicao1"><?php echo utf8_decode($r->Situacao);?></td>
                        <td class="inf_medicao1"><?php echo utf8_decode($r->FinalizadaPor);?></td>
                        <td class="inf_medicao1"><?php echo date("d/m/Y H:i:s", strtotime($r->dFinalizada));?></td>
                        <td class="inf_medicao1">
                            <div class="opcoes">
                                <a class="btn btn-primary btn-xs" href="<?php echo BASE; ?>medicao/vMedicao.php?id=<?php echo $r->idMedicao; ?>&t=v" title="VER DETALHES" class="text-primary"><i class="fas fa-folder-open"></i></a>
                                <?php
                                if(!empty($r->Documento)){
                                ?>
                                <a target="_blank" href="<?=BASE.'medicao/docs/'.$r->Documento;?>" title="Ver Anexo" class="text-success"><i class="fa fa-paperclip"></i></a>
                                <?php  
                                }
                                ?>
                                
                            </div>
                        </td>
                    </tr>
                <?php
                    }
                    if($c != 0){
                ?>
                    <tr>
                        <td colspan="8">NÃO HÁ DADOS</td>
                    </tr>
                <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="8">NÃO HÁ DADOS</td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </form>
        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>