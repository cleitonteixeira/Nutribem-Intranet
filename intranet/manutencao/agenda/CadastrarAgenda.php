<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    $mControle = isset($_POST['mesano']) ? date("m", strtotime($_POST['mesano'])) : date("m");
    $yControle = isset($_POST['mesano']) ? date("Y", strtotime($_POST['mesano'])) : date("Y");
    switch ($mControle) {
        case "01":    $mes = "Janeiro";     break;
        case "02":    $mes = "Fevereiro";   break;
        case "03":    $mes = "Março";       break;
        case "04":    $mes = "Abril";       break;
        case "05":    $mes = "Maio";        break;
        case "06":    $mes = "Junho";       break;
        case "07":    $mes = "Julho";       break;
        case "08":    $mes = "Agosto";      break;
        case "09":    $mes = "Setembro";    break;
        case "10":    $mes = "Outubro";     break;
        case "11":    $mes = "Novembro";    break;
        case "12":    $mes = "Dezembro";    break; 
 	}
?>
<script>
$(document).ready(function(){
  $('[data-toggle="popover"]').popover({trigger: "hover", html: true});   
});
$(function(){
  $(document).on('click', '.btn-vls', function(e) {
      e.preventDefault;
      var data1= $(this).closest('td').find('.DataModal1').val();
      var data = $(this).closest('td').find('.DataModal').val();
      $("#DataModelo").html(data1).show();
      $("#DataEnvia").val(data).show();
      $('#AgendaVisita').modal('hide');
      $('#AgendaVisita').modal('show');
  });
  $(document).on('click', '.btn-svl', function(e) {
      e.preventDefault;
      var data2= $(this).closest('form').find('#manutencao[]').val();
      alert(data2);
      var data1= $(this).closest('form').find('#unidade').val();
      alert(data1);
      var data = $(this).closest('form').find('#DataEnvia').val();
      alert(data);
      
  });
});
</script>
<div class="container-fluid">
  <div class="row conteudo">
	   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 conteudo">
	   		<form class="form-inline" data-toggle="validate" method="POST" enctype="multipart/form-data">
	   			<div class="form-group">
	   				<label for="mesano">Mês/Ano:</label>
	   				<input type="month" class="form-control" name="mesano" id="mesano" value="<?=isset($_POST['mesano']) ? date("Y-m", strtotime($_POST['mesano'])) : date("Y-m")?>">
	   			</div>
	   			<button class="btn btn-primary" type="submit">SELECIONAR</button>
	   		</form>
	   </div>
	   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 conteudo">
	   	<table class="table table-bordered tAgenda">
	   		<thead>
	   			<tr>
	   				<th colspan="7" class="text-center aMes"><?=mb_strtoupper($mes).'/'.$yControle;?></th>
	   			</tr>
	   			<tr>
	   				<th class="text-center">DOM</th>
	   				<th class="text-center">SEG</th>
	   				<th class="text-center">TER</th>
	   				<th class="text-center">QUA</th>
	   				<th class="text-center">QUI</th>
	   				<th class="text-center">SEX</th>
	   				<th class="text-center">SAB</th>
	   			</tr>
	   		</thead>
	   		<tbody>
			<?php
			$ano = isset($_POST['mesano']) ? date("Y", strtotime($_POST['mesano'])) : date("Y");
			$mes = isset($_POST['mesano']) ? date("m", strtotime($_POST['mesano'])) : date("m");
			$data = date('Y-m-d', strtotime($ano.'-'.$mes.'-01'));
			$diaSemana = date('w', strtotime($data));
			$first = 0;
			$iSemana = 0;
			while(date('m', strtotime($data)) == $mes ){
				if($first != 0){
			?>
	   			<tr>
	   		<?php
					while($iSemana < 7){
						if(date("w", strtotime($data)) == 0 || date("w", strtotime($data)) == 6){
							if(date("Y-m-d", strtotime($data)) == date("Y-m-d") ){
								$cl = "aHoje";
							}elseif(date('m', strtotime($data)) == $mes){
								$cl = "fSemana";
							}
						}else{
							if(date("Y-m-d", strtotime($data)) == date("Y-m-d") ){
								$cl = "aHoje";
							}else{
								$cl = '';
							}
						}
						$controle = date('Y-m-d', strtotime($data)) <= date('Y-m-d') ? 'disabled':'';
			?>
	   				<td class="cData text-center <?=$cl;?>"><?=date('m', strtotime($data)) == $mes ? '<a class="btn btn-primary btn-lg btn-vls '.$controle.'"><strong>'.date('d', strtotime($data)).'</strong></a>' : '';?><input type="hidden" name="dModelo" class="DataModal" value="<?=date("Y-m-d", strtotime($data));?>" ><input type="hidden" name="dModelo1" class="DataModal1" value="<?=date("d/m/Y", strtotime($data));?>" ></td>
	   		<?php
		   				$iSemana++;
						$data = date('Y-m-d', strtotime('+1 day', strtotime($data)));
					}
	   		?>
	   			</tr>
			<?php
				}else{
			?>
	   			<tr>
	   		<?php
					while($iSemana < 7){
						if(date("w", strtotime($data)) == 0 || date("w", strtotime($data)) == 6){
							if(date("Y-m-d", strtotime($data)) == date("Y-m-d") ){
								$cl = "aHoje";
							}else{
								$cl = "fSemana";
							}
						}else{
							if(date("Y-m-d", strtotime($data)) == date("Y-m-d") ){
								$cl = "aHoje";
							}else{
								$cl = '';
							}
						}
						$controle = date('Y-m-d', strtotime($data)) <= date('Y-m-d') ? 'disabled':'';
	   		?>
	   				<td class="cData text-center <?=$cl;?>"><?=$iSemana >= $diaSemana ? '<a class="btn btn-primary btn-lg btn-vls '.$controle.'"><strong>'.date('d', strtotime($data)).'</strong></a>' : '';?><input type="hidden" name="dModelo" class="DataModal" value="<?=date("Y-m-d", strtotime($data));?>" ><input type="hidden" name="dModelo1" class="DataModal1" value="<?=date("d/m/Y", strtotime($data));?>" ></td>
	   		<?php
		   				if ($iSemana >= $diaSemana) {
		   					$first = 1;
		   					$data = date('Y-m-d', strtotime('+1 day', strtotime($data)));
		   				}
		   				$iSemana++;
	   				}
	   		?>
	   			</tr>
	   		<?php
				}
   				if ($iSemana == 7) {
   					$iSemana = 0;
   				}
			}
			?>
	   		</tbody>
	   	</table>
	   </div>
	</div>
</div>
<div class="modal fade" id="AgendaVisita" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" role="dialog">
  <div class="modal-dialog modal-lg">
      <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">AGENDAR VISITA PARA O DIA: <span id="DataModelo"></span></h4>
      </div>
      <div class="modal-body" style="padding:40px 50px;">
      	<form name="Form" role="form" action="<?=BASE;?>control/banco/AgendaDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator">
      		<?php
      		$sql = 'SELECT idUnidadeMT, Nome FROM unidademt INNER JOIN cadastro ON idCadastro = Cadastro_idCadastro';
      		$stm = $conexao->prepare($sql);
      		$stm->execute();
      		$Unidades = $stm->fetchAll(PDO::FETCH_OBJ);
      		$sql = 'SELECT idManutencao, Nome FROM manutencao';
      		$stm = $conexao->prepare($sql);
      		$stm->execute();
      		while($rs = $stm->fetch(PDO::FETCH_OBJ)){
      		?>
      		<div class="form-group">
	            <label class="control-label col-sm-4" for="unidade[]">Onde o <?=$rs->Nome?> vai estar: </label>
	            <div class="col-sm-7">
	                <select name="unidade[]" id="unidade[]" required="required" class="selectpicker form-control" required="required" title="SELECIONE UMA UNIDADE" data-live-search="true">
	                  <?php
	                  foreach ($Unidades as $r) {
	                  ?>
	                  <option <?=$r->idUnidadeMT == 1 ? 'selected="selected"':''?> data-tokens="<?=utf8_decode($r->Nome)." ".$r->idUnidadeMT;?>" value="<?=$r->idUnidadeMT;?>" ><?=str_pad($r->idUnidadeMT, 3, 0, STR_PAD_LEFT)." - ".utf8_decode($r->Nome);?></option>
	                  <?php
	                  }
	                  ?>
	                </select>
	              <div class="help-block with-errors"></div>
      			<input type="hidden" value="<?=$rs->idManutencao?>" id="manutencao[]" name="manutencao[]">
	            </div>
	        </div>
      		<?php
			}
      		?>
	        <div class="form-group">
	        	<button class="btn btn-success btn-svl">SALVAR</button>
	        </div>
      		<input type="hidden" value="" id="DataEnvia" name="DataEnvia">
      		<input type="hidden" value="Cadastrar" id="CadAgenda" name="CadAgenda">
      	</form>
      </div>
    </div>
  </div>
</div>
<?php
}
?>