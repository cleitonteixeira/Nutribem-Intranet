<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    $sql = "SELECT * FROM pendencias WHERE Data <= CURDATE() - INTERVAL 90 DAY ORDER BY idPendencias DESC;";
    $stm = $conexao->prepare($sql);
    $stm->execute();
?>
<script>
    $(document).ready(function(){
        $("select[name='unidade']").change(function(e){
            var unidade = $('#unidade').val();//pegando o value do option selecionado
            var usuario = $('#Solicitantes').val();//pegando o value do option selecionado
            var tipo    = $('#tipo').val();//pegando o value do option selecionado
            //alert(unidade);//apenas para debugar a variável
            $.post('Arquivo.inc.php',{ pUnidade: unidade, pUsuario: usuario, pTipo: tipo, FiltroCompleto: 1} , function (dados){
                //alert(dados);
                dados = JSON.parse(dados);
                if (dados.length != 0 ){
                    var option = '';
                    $.each(dados, function(i, obj){
                        option += "<tr>";
                        option += "<td>"+obj.Unidade+"</td>";
                        option += "<td>"+obj.Solicitante+"</td>";
                        option += "<td>"+obj.Tipo+"</td>";
                        option += "<td>"+obj.Colaborador+"</td>";
                        option += "<td>"+obj.Data+"</td>";
                        option += "<td>"+obj.Andamento+"</td>";
                        option += '<td><a href="<?php echo BASE;?>formularios/v'+tipo+'.php?p='+obj.idPendencia+'"><i class="fa fa-external-link-square" aria-hidden="true"></i></a></td>';
                        option += "</tr>";
                    })
                    $('#pendencias').html(option).show();
                }else{
                    Resetar();
                }
            })
        })
        <!-- Resetar Selects -->
        function Resetar(){
            var op = '';
			op += '<tr>';
			op += '<td colspan="7"><i class="fa fa-times-circle"></i> Sem dados para exibir!</td>';
			op += '</tr>';
			$('#pendencias').html(op).show();
        }
        $("select[name='Solicitantes']").change(function(e){
            var unidade = $('#unidade').val();//pegando o value do option selecionado
            var usuario = $('#Solicitantes').val();//pegando o value do option selecionado
            var tipo    = $('#tipo').val();//pegando o value do option selecionado
            //alert(unidade);//apenas para debugar a variável
            $.post('Arquivo.inc.php',{ pUnidade: unidade, pUsuario: usuario, pTipo: tipo, FiltroCompleto: 1} , function (dados){
                //alert(dados);
                dados = JSON.parse(dados);
                if (dados.length != 0 ){
                    var option = '';
                    $.each(dados, function(i, obj){
                        option += "<tr>";
                        option += "<td>"+obj.Unidade+"</td>";
                        option += "<td>"+obj.Solicitante+"</td>";
                        option += "<td>"+obj.Tipo+"</td>";
                        option += "<td>"+obj.Colaborador+"</td>";
                        option += "<td>"+obj.Data+"</td>";
                        option += "<td>"+obj.Andamento+"</td>";
                        option += '<td><a href="<?php echo BASE;?>formularios/v'+tipo+'.php?p='+obj.idPendencia+'"><i class="fa fa-external-link-square" aria-hidden="true"></i></a></td>';
                        option += "</tr>";
                    })
                    $('#pendencias').html(option).show();
                }else{
                    Resetar1();
                }
            })
        })
        <!-- Resetar Selects -->
        function Resetar1(){
            var op = '';
			op += '<tr>';
			op += '<td colspan="7"><i class="fa fa-times-circle"></i> Sem dados para exibir!</td>';
			op += '</tr>';
			$('#pendencias').html(op).show();
        }
        $("select[name='tipo']").change(function(e){
            var unidade = $('#unidade').val();//pegando o value do option selecionado
            var usuario = $('#Solicitantes').val();//pegando o value do option selecionado
            var tipo    = $('#tipo').val();//pegando o value do option selecionado
            //alert(unidade);//apenas para debugar a variável
            $.post('Arquivo.inc.php',{ pUnidade: unidade, pUsuario: usuario, pTipo: tipo, FiltroCompleto: 1} , function (dados){
                //alert(dados);
                dados = JSON.parse(dados);
                if (dados.length != 0 ){
                    var option = '';
                    $.each(dados, function(i, obj){
                        option += "<tr>";
                        option += "<td>"+obj.Unidade+"</td>";
                        option += "<td>"+obj.Solicitante+"</td>";
                        option += "<td>"+obj.Tipo+"</td>";
                        option += "<td>"+obj.Colaborador+"</td>";
                        option += "<td>"+obj.Data+"</td>";
                        option += "<td>"+obj.Andamento+"</td>";
                        option += '<td><a href="<?php echo BASE;?>formularios/v'+tipo+'.php?p='+obj.idPendencia+'"><i class="fa fa-external-link-square" aria-hidden="true"></i></a></td>';
                        option += "</tr>";
                    })
                    $('#pendencias').html(option).show();
                }else{
                    Resetar2();
                }
            })
        })
        <!-- Resetar Selects -->
        function Resetar2(){
            var op = '';
			op += '<tr>';
			op += '<td colspan="7"><i class="fa fa-times-circle"></i> Sem dados para exibir!</td>';
			op += '</tr>';
			$('#pendencias').html(op).show();
        }
        
    });
</script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-lg-12 col-md-12 conteudo">
                <div class="text-center">
                    <h2>Histórico de Pendências</h2>
                </div>
				<div class="col-xs-12 col-lg-12 col-md-12">
                    <form class="form-inline">
                        <div class="form-group">
                            <label class="control-label" for="unidade">Unidade:</label>
                            <select name="unidade" id="unidade" class="form-control">
                                <option value="" title="Selecionar Todas">Todas Unidades</option>
                                <?php
								$sqlq = 'SELECT ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro';
								$stmt = $conexao->prepare($sqlq);
								$stmt->execute();
								$rs = $stmt->fetchAll(PDO::FETCH_OBJ);
								foreach($rs as $r):
								?>
								<optgroup label="<?php echo CNPJ_Padrao($r->CNPJ); ?>" >
									<?php
									$sqli = 'SELECT un.idUnidade, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidade un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? AND un.idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
									$stmt = $conexao->prepare($sqli);
									$stmt->bindParam(1, $r->CNPJ);
									$stmt->bindParam(2, $_SESSION['idusuarios']);
									$stmt->execute();
									while($row = $stmt->fetch(PDO::FETCH_OBJ)):
									?>
									<option value="<?php echo $row->idUnidade; ?>"><?php echo utf8_decode($row->Nome); ?></option>
									<?php endwhile; ?>
								</optgroup>
								<?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="Solicitantes">Solicitante:</label>
                            <select name="Solicitantes" id="Solicitantes" class="form-control">
                                <option value="" title="Selecione um Solicitante">Todos Solicitantes</option>
                                <?php
                                $msql = "SELECT DISTINCT(p.Usuario_idUsuario) AS idSolicitante, u.Nome AS Solicitante FROM pendencias p INNER JOIN usuarios u ON u.idusuarios = p.Usuario_idUsuario WHERE Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?) AND Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)') ORDER BY Solicitante ASC;";
                                $stt = $conexao->prepare($msql);
                                $stt->bindParam(1, $_SESSION['idusuarios']);
                                $stt->execute();
                                while($rs = $stt->fetch(PDO::FETCH_OBJ)){
                                ?>
                                <option value="<?php echo $rs->idSolicitante; ?>" ><?php echo utf8_decode($rs->Solicitante); ?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="tipo">Tipo:</label>
                            <select name="tipo" id="tipo" class="form-control">
                                <option value="" title="Selecione um Tipo">Todos os Tipos</option>
                                <?php
                                $msql = "SELECT DISTINCT(Tipo) FROM pendencias WHERE Resultado IN ('Recusada', 'Validada', 'Recusada (Preenchimento Incorreto)');";
                                $stt = $conexao->prepare($msql);
                                $stt->execute();
                                while($row = $stt->fetch(PDO::FETCH_OBJ)){
                                    $ti = "";
                                    switch($row->Tipo){
                                        case "Ferias":
                                            $ti = utf8_encode("Férias");
                                            break;
                                        case "Promocao":
                                            $ti = utf8_encode("Promoção");
                                            break;
                                        case "Demissao":
                                            $ti = utf8_encode("Demissão");
                                            break;
                                        case "Admissao":
                                            $ti = utf8_encode("Admissão");
                                            break;
                                    }
                                ?>
                                <option value="<?php echo $row->Tipo; ?>" title="<?php echo utf8_decode($ti) ?>"><?php echo utf8_decode($ti) ?></option>
                                <?php }?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="col-xs-12 col-lg-12 col-md-12"> </div>
				<div class="col-xs-12 col-lg-12 col-md-12">
					<?php
						if($stm->rowCount()>=1):
					?>
						<table class="table table-striped table-bordered table-responsive text-center" name="colaboradores" id="colaboradores">
							<thead>
								<tr>
									<th>Unidade</th>
									<th>Solicitante</th>
									<th>Tipo</th>
									<th>Colaborador</th>
									<th>Data</th>
									<th>Andamento</th>
									<th>Verificar</th>
								</tr>
							</thead>
							<tbody id="pendencias">
								<?php 
									while($rs = $stm->fetch(PDO::FETCH_OBJ)):
									$sql = "SELECT  u.Nome AS Solicitante, c.Nome AS Imediato, ca.Nome AS Unidade FROM usuarios u INNER JOIN usuarios c ON c.idUsuarios = ? INNER JOIN unidade un ON un.idUnidade = ? INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro WHERE u.idUsuarios = ? LIMIT 1";
									$stmt = $conexao->prepare($sql);
									$stmt->bindParam(1, $rs->Responsavel_Colaborador);
									$stmt->bindParam(2, $rs->Unidade_idUnidade);
									$stmt->bindParam(3, $rs->Usuario_idUsuario);
									$stmt->execute();
									$res = $stmt->fetch(PDO::FETCH_OBJ);
								?>
								<tr>
									<td><?php echo utf8_decode($res->Unidade); ?></td>
									<td><?php echo utf8_decode($res->Solicitante); ?></td>
									
									<?php
									switch($rs->Tipo):
										case "Ferias":
									?>
											<td>Férias</td>
									<?php	
											break;
										case "Promocao":
									?>
											<td>Promoção</td>
									<?php
											break;
										case "Demissao":
									?>	
											<td>Demissão</td>
									<?php
											break;
										case "Admissao":
									?>
											<td>Admissão</td>
									<?php
											break;
									endswitch;
									?>
									<td>
                                        <?php 
                                        $tabela = strtolower($rs->Tipo);
                                        $campo  = $rs->Tipo;
                                        $sql = "SELECT cad.Nome as Colaborador FROM ".$tabela." p INNER JOIN colaborador c ON c.idColaborador = p.Colaborador_idColaborador INNER JOIN cadastro cad ON cad.idCadastro = c.Cadastro_idCadastro  WHERE p.id".$campo." = ?";
                                        $stmt = $conexao->prepare($sql);
                                        $stmt->bindParam(1, $rs->CodTipo);
                                        $stmt->execute();
                                        $colaborador = $stmt->fetch(PDO::FETCH_OBJ);
                                        echo mb_strtoupper(utf8_decode($colaborador->Colaborador),"UTF-8");
                                        ?>
                                    </td>
									<td><?php echo utf8_decode(date("d/m/Y",strtotime($rs->Data))); ?></td>
									<td><?php echo utf8_decode($rs->Resultado); ?></td>
									<td><a href="<?php echo BASE;?>formularios/v<?php echo $rs->Tipo;?>.php?p=<?php echo $rs->idPendencias; ?>"><i class="fa fa-external-link-square" aria-hidden="true"></i></a></td>
								</tr>
								<?php
									endwhile;
								?>
							</tbody>
						</table>
					<?php 
						else:
					?>
					<br />
					<div class="col-xs-12 text-center">Nenhuma pendência encontrada!</div>
					<?php 
						endif;
					?>
				</div>
            </div>
        </div>
    </div>
<?php
	require_once("../control/arquivo/footer/Footer.php");
}
?>
