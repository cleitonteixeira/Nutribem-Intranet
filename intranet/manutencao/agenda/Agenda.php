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
						$sql2 = "SELECT a.Data, m.Nome, m.Telefone, cdu.Nome AS Unidade FROM agenda a INNER JOIN manutencao m ON m.idManutencao = a.Manutencao_idManutencao INNER JOIN unidademt umt ON umt.idUnidadeMT = a.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = umt.Cadastro_idCadastro WHERE a.Data = ?";
						$stmt2 = $conexao->prepare($sql2);
						$stmt2->bindParam(1, $data);
						$stmt2->execute();
						$Agendado = "";
						while ($r = $stmt2->fetch(PDO::FETCH_OBJ)) {
							if($Agendado == ""){
								$Agendado = $r->Nome." | ".Cel_Padrao($r->Telefone)." vai estar na unidade: ".$r->Unidade;
							}else{
								$Agendado .= "</br></br>".$r->Nome." | ".Cel_Padrao($r->Telefone)." vai estar na unidade: ".$r->Unidade;
							}
						}
						if($Agendado == ""){
							$Agendado = "Sem agenda para esse dia.";
						}
			?>
	   				<td class="cData text-center <?=$cl;?>"><?=date('m', strtotime($data)) == $mes ? '<a data-toggle="popover" title="Agenda do Dia: '.date("d/m/Y", strtotime($data)).'" data-content="'.$Agendado.'" class="btn btn-primary btn-lg"><strong>'.date('d', strtotime($data)).'</strong></a>' : '';?></td>
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
						$sql2 = "SELECT a.Data, m.Nome, m.Telefone, cdu.Nome AS Unidade FROM agenda a INNER JOIN manutencao m ON m.idManutencao = a.Manutencao_idManutencao INNER JOIN unidademt umt ON umt.idUnidadeMT = a.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = umt.Cadastro_idCadastro WHERE a.Data = ?";
						$stmt2 = $conexao->prepare($sql2);
						$stmt2->bindParam(1, $data);
						$stmt2->execute();
						$Agendado = "";
						while ($r = $stmt2->fetch(PDO::FETCH_OBJ)) {
							if($Agendado == ""){
								$Agendado = $r->Nome." | ".Cel_Padrao($r->Telefone)." vai estar na unidade: ".$r->Unidade;
							}else{
								$Agendado .= "</br></br>".$r->Nome." | ".Cel_Padrao($r->Telefone)." vai estar na unidade: ".$r->Unidade;
							}
						}
						if($Agendado == ""){
							$Agendado = "Sem agenda para esse dia.";
						}
	   		?>
	   				<td class="cData text-center <?=$cl;?>"><?=$iSemana >= $diaSemana ? '<a data-toggle="popover" title="Agenda do Dia: '.date("d/m/Y", strtotime($data)).'" data-content="'.$Agendado.'" class="btn btn-primary btn-lg"><strong>'.date('d', strtotime($data)).'</strong></a>' : '';?></td>
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
<?php
}
?>