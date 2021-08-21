<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("control/Pacote.php");
    $conexao = conexao::getInstance();

?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<!-- Content -->

<?php
$sql = "SELECT COUNT(*) AS Quant, (SELECT COUNT(*) FROM os o1 WHERE o1.DataHoraFinalizada IS NOT NULL AND o1.Unidade_idUnidade = o.Unidade_idUnidade) AS Finalizada, (SELECT COUNT(*) FROM os o2 WHERE o2.DataHoraFinalizada IS NULL AND o2.DataAgenda IS NOT NULL AND o2.Unidade_idUnidade = o.Unidade_idUnidade) AS Agendada, cd.Nome FROM os o INNER JOIN unidademt umt ON umt.idUnidadeMT = o.Unidade_idUnidade INNER JOIN cadastro cd ON cd.idCadastro = umt.Cadastro_idCadastro WHERE umt.idUnidadeMT IN (SELECT Unidade_idUnidade FROM unidademtuser WHERE Usuario_idUsuario = ?) GROUP BY o.Unidade_idUnidade LIMIT 5";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $_SESSION['idusuarios']);
$stmt->execute();
$rest = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);
//montando o array com os dados
function drawChart() {
  var OsUnidade = google.visualization.arrayToDataTable([

    ['UNIDADE', 'OS SOLICITADAS', 'FINALIZADAS', 'AGENDADAS']
    <?php foreach ($rest as $k) {?>
    ,['<?=$k->Nome;?>', <?=$k->Quant;?>, <?=$k->Finalizada;?>, <?=$k->Agendada;?>]
    <?php }?>
  ]);
  var OpOsUnidade = {
      chart: {
        title: 'OS Solicitadas via Manutenção Online',
        subtitle: 'OS Solicitadas/Finalizadas',
      }
    };

  var chart = new google.charts.Bar(document.getElementById('UnidadeOS'));

  chart.draw(OsUnidade, google.charts.Bar.convertOptions(OpOsUnidade));
}
</script>
<div class="container-fluid">
  <div class="row conteudo">
    <!--<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <div class="relatorio" id="linhas"></div>
    </div>-->
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 conteudo">
      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="relatorio" id="UnidadeOS" style="width: 500px; height: 250px;"></div>
      </div>
    </div>
  </div>
</div>
<?php
}
?>