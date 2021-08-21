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
$sql = "SELECT COUNT(*) AS Total, (SELECT COUNT(*) FROM os o1 WHERE o1.DataHoraFinalizada IS NOT NULL AND o1.Unidade_idUnidade = o.Unidade_idUnidade) AS Finalizada, (SELECT COUNT(*) FROM os o3 WHERE o3.DataHoraFinalizada IS NULL AND o3.DataAgenda IS NULL AND o3.Unidade_idUnidade = o.Unidade_idUnidade) AS Quant, (SELECT COUNT(*) FROM os o2 WHERE o2.DataHoraFinalizada IS NULL AND o2.DataAgenda IS NOT NULL AND o2.Unidade_idUnidade = o.Unidade_idUnidade) AS Agendada, cd.Nome FROM os o INNER JOIN unidademt umt ON umt.idUnidadeMT = o.Unidade_idUnidade INNER JOIN cadastro cd ON cd.idCadastro = umt.Cadastro_idCadastro WHERE umt.idUnidadeMT IN (SELECT Unidade_idUnidade FROM unidademtuser WHERE Usuario_idUsuario = ?) GROUP BY o.Unidade_idUnidade";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $_SESSION['idusuarios']);
$stmt->execute();
$rest = $stmt->fetchAll(PDO::FETCH_OBJ);

$sql = "SELECT CEIL(IFNULL(AVG(DATEDIFF(DataAgenda, DataHoraAbertura)),0)) AS 'MEDIA_AGENDA', CEIL(IFNULL(AVG(DATEDIFF(DataHoraFinalizada, DataHoraAbertura)),0)) AS 'MEDIA_CONCLUSAO', DATE_FORMAT(DataHoraAbertura, '%m/%Y') AS MesAno FROM os WHERE Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidademtuser WHERE Usuario_idUsuario = ?) GROUP BY YEAR(DataHoraAbertura), MONTH(DataHoraAbertura);";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $_SESSION['idusuarios']);
$stmt->execute();
$rest1 = $stmt->fetchAll(PDO::FETCH_OBJ);

$sql = "SELECT DATE_FORMAT(lco.DataMovimenta, '%Y-%m') AS MesAno, IFNULL(SUM(co.Valor),0) AS vTotal FROM logcustoos lco INNER JOIN custoos co ON co.idCustoOS = lco.Custo_idCusto INNER JOIN unidademt umt ON umt.idUnidadeMT = co.Unidade_idUnidade WHERE umt.idUnidadeMT IN (SELECT Unidade_idUnidade FROM unidademtuser WHERE Usuario_idUsuario = ?) GROUP BY MONTH(lco.DataMovimenta), YEAR(lco.DataMovimenta);";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $_SESSION['idusuarios']);
$stmt->execute();
$rest2 = $stmt->fetchAll(PDO::FETCH_OBJ);

?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['bar','line'], 'language': 'pt'});
    google.charts.setOnLoadCallback(drawChart);
//montando o array com os dados
function drawChart() {

  //GRAFICO DE BARRAS N OS
  var OsUnidade = google.visualization.arrayToDataTable([

    ['UNIDADE', 'EM ABERTO', 'AGENDADAS']
    <?php foreach ($rest as $k) {?>
    ,['<?=$k->Nome;?>', <?=$k->Quant;?>, <?=$k->Agendada;?>]
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
  //FIM GRAFICO DE BARRAS N OS
  //GRAFICO DE BARRAS PRAZOS
  var TpUnidade = google.visualization.arrayToDataTable([

    ['Mês/Ano', 'PRAZO SOLICITADO', 'CONCLUSÃO']
    <?php foreach ($rest1 as $k) {?>
    ,['<?=$k->MesAno;?>',  <?=$k->MEDIA_AGENDA;?>, <?=$k->MEDIA_CONCLUSAO;?>]
    <?php }?>
  ]);
  var OpTpUnidade = {
      chart: {
        title: 'Média de Prazos',
      }
    };

  var aLinha = new google.charts.Bar(document.getElementById('GrLinhas'));

  aLinha.draw(TpUnidade, google.charts.Bar.convertOptions(OpTpUnidade));
  //FIM GRAFICO DE BARRAS N OS
  //GRAFICO DE BARRAS PRAZOS
  var TpUnidade = google.visualization.arrayToDataTable([

    ['Mês/Ano','Valor']
    <?php foreach ($rest2 as $k) { $vTotalV = number_format($k->vTotal,2,'.','');?>
    ,['<?=date("m/Y", strtotime($k->MesAno));?>', <?=$vTotalV?>]
    <?php }?>
  ]);
  var OpTpUnidade = {
      chart: {
        title: 'Valor Gasto com Manutenção',
      },
      vAxis: {
        format: 'currency'
      }
    };

  var aLinha = new google.charts.Bar(document.getElementById('ValorOs'));

  aLinha.draw(TpUnidade, google.charts.Bar.convertOptions(OpTpUnidade));
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
      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="relatorio" id="GrLinhas" style="width: 500px; height: 250px;"></div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 conteudo">
      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="relatorio" id="ValorOs" style="width: 500px; height: 250px;"></div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> </div>
  </div>
</div>
<?php
}
?>