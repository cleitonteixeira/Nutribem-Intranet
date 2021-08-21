<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
else:
    require_once("control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<!-- Content -->
<div class="container-fluid">
    <div class="row"> 
        <?php
        $chefia = array(1,4,5,36,37,39,42,44);
        if(in_array($_SESSION['idusuarios'],$chefia)){
        ?>
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
			<div class="conteudo"></div>
				<div class="col-xs-6 col-md-6 col-lg-6">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center">Próximos Reajustes de Contratos</h1>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-responsive text-center" id="aso">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Nº Contrato</th>
                                    <th>Dias</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT ca.Nome AS Cliente, ct.nContrato, ct.DataReajuste AS Reajuste, ct.idContrato FROM contrato ct INNER JOIN contratante co ON co.idContratante = ct.Contratante_idContratante INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.idUnidadeFaturamento = ct.Unidade_idUnidade INNER JOIN cadastro cun ON cun.idCadastro = un.Cadastro_idCadastro INNER JOIN empresa em ON em.idEmpresa = ct.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro WHERE ct.Finalizado = 'N' AND ct.Unidade_idUnidade IN (SELECT UnidadeFaturamento_idUnidadeFaturamento FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY ct.DataReajuste, ct.idContrato ASC LIMIT 5";
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $_SESSION['idusuarios']);
                                $stm->execute();
                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rs AS $r):
                                    $dReajuste 	= date('Y-m-d', strtotime($r->Reajuste));
                                    $data 		= strtotime(date('Y-m-d', strtotime($r->Reajuste)));
                                    $data1 		= strtotime(date('Y-m-d'));
                                    $diferenca = $data - $data1;
                                    $dias = (int)floor( $diferenca / (60 * 60 * 24));
                                	if($dias>= 1 and $dias <= 60){
								?>
								<tr class="col_inf_index nCliente" style="background: #E3CE2A">
								<?php }elseif($dias<= 0){?>
								<tr class="col_inf_index nCliente" style="background: #FF161D">
								<?php }else{ ?>
								<tr class="col_inf_index nCliente" style="background: #3FD100">
								<?php } ?>
									<td><?php echo utf8_decode($r->Cliente); ?></td>
                                    <td><?php echo utf8_decode($r->nContrato); ?> </td>
                                    <td><?php echo "*".$dias; ?></td>
                                </tr>
                                <?php
                                endforeach;   
                                ?>
                            </tbody>
                        </table>
                        <small>
							<p>Próximos 5 reajustes. * Dias restantes.</p>
							<p><span style="color: #FF161D"><i class="fa fa-square" aria-hidden="true"></i></span> Já se encontra atrasado. Com 0 dias ou menos.</p>
							<p><span style="color: #E3CE2A"><i class="fa fa-square" aria-hidden="true"></i></span> Próximo ao Prazo de Vencimento. Entre 60 dias e 1 dia.</p>
							<p><span style="color: #3FD100"><i class="fa fa-square" aria-hidden="true"></i></span> Ainda vigente. 61 dias acima.</p>
						</small>
                    </div>
                </div>
            </div>
			<div class="col-xs-6 col-md-6 col-lg-6">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center">Proximos Vencimentos de Contratos</h1>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-responsive text-center" id="aso">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Nº Contrato</th>
                                    <th>Dias</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT ca.Nome AS Cliente, ct.nContrato, ct.VigenciaFim AS fContrato, ct.idContrato FROM contrato ct INNER JOIN contratante co ON co.idContratante = ct.Contratante_idContratante INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.idUnidadeFaturamento = ct.Unidade_idUnidade INNER JOIN cadastro cun ON cun.idCadastro = un.Cadastro_idCadastro INNER JOIN empresa em ON em.idEmpresa = ct.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro WHERE ct.Finalizado = 'N' AND ct.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY ct.VigenciaFim, ct.idContrato ASC LIMIT 5";
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $_SESSION['idusuarios']);
                                $stm->execute();
                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rs AS $r):
                                    $dReajuste 	= date('Y-m-d', strtotime($r->fContrato));
                                    $data 		= strtotime(date('Y-m-d', strtotime($r->fContrato)));
                                    $data1 		= strtotime(date('Y-m-d'));
                                    $diferenca = $data - $data1;
                                    $dias = (int)floor( $diferenca / (60 * 60 * 24));
                                	if($dias>= 1 and $dias <= 60){
								?>
								<tr class="col_inf_index nCliente" style="background: #E3CE2A">
								<?php }elseif($dias<= 0){?>
								<tr class="col_inf_index nCliente" style="background: #FF161D">
								<?php }else{ ?>
								<tr class="col_inf_index nCliente" style="background: #3FD100">
								<?php } ?>
									<td><?php echo utf8_decode($r->Cliente); ?></td>
                                    <td><?php echo utf8_decode($r->nContrato); ?> </td>
                                    <td><?php echo "*".$dias; ?></td>
                                </tr>
                                <?php
                                endforeach;   
                                ?>
                            </tbody>
                        </table>
                        <small>
							<p>Próximos 5 Vencimentos de Contrato. * Dias restantes.</p>
							<p><span style="color: #FF161D"><i class="fa fa-square" aria-hidden="true"></i></span> Já se encontra atrasado. Com 0 dias ou menos.</p>
							<p><span style="color: #E3CE2A"><i class="fa fa-square" aria-hidden="true"></i></span> Próximo ao Prazo de Vencimento. Entre 60 dias e 1 dia.</p>
							<p><span style="color: #3FD100"><i class="fa fa-square" aria-hidden="true"></i></span> Ainda vigente. 61 dias acima.</p>
						</small>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }else{
        ?>
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
			<div class="conteudo"></div>
            <?php
            $sql = "SELECT DISTINCT(l.Contrato_idContrato), ct.idContratante, cd.Nome AS Cliente, c.nContrato, c.cCusto, MAX(l.dLancamento) AS dLancamento FROM lancamento l INNER JOIN contrato c ON c.idContrato = l.Contrato_idContrato INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cd ON cd.idCadastro = ct.Cadastro_idCadastro WHERE l.Usuario_idUsuario = ? GROUP BY l.Contrato_idContrato ORDER BY l.Contrato_idContrato ASC LIMIT 5;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $_SESSION['idusuarios']);
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_OBJ);
            ?>
            <div class="col-xs-9 col-lg-9 col-md-9">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center">ÚLTIMOS LANÇAMENTOS DIARIOS</h1>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-responsive text-justify" id="aso">
                            <thead>
                                <tr class="nCliente">
                                    <th>Cliente</th>
                                    <th>Nº Contrato</th>
                                    <th>Último Lançamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($row as $r){
								?>
								<tr class="nCliente">
									<td><?php echo utf8_decode($r->Cliente).' -- <strong>CC:</strong> '.utf8_decode($r->cCusto); ?></td>
                                    <td><?php echo utf8_decode($r->nContrato); ?> </td>
                                    <td><?php echo date("d/m/Y", strtotime($r->dLancamento)); ?></td>
                                </tr>
                                <?php
                                } 
                                ?>
                            </tbody>
                        </table>
                        <small>
							<p><strong>Legenda:</strong></p>
                            <p><strong>CC</strong> = Centro de Custo</p>
						</small>
                    </div>
                </div>
            </div>
            <?php
            $sql = "SELECT l.dLancamento FROM lancamento l WHERE l.Usuario_idUsuario = ? ORDER BY l.dLancamento DESC LIMIT 1;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $_SESSION['idusuarios']);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if(!empty($row->dLancamento)){
                $data 		= strtotime(date('Y-m-d', strtotime($row->dLancamento)));
            }else{
                $data 		= strtotime(date('Y-m-d'));
            }
            $data1 		= strtotime(date('Y-m-d'));
            $diferenca = $data1 - $data;
            $dias = (int)floor( $diferenca / (60 * 60 * 24));
            ?>
            <div class="col-xs-3 col-lg-3 col-md-3">
                <?php if($dias <= 1){ ?>
                <div class="panel panel-success">
                <?php }elseif($dias == 2){ ?>
                <div class="panel panel-warning">
                <?php }elseif($dias > 2){ ?>
                <div class="panel panel-danger">
                <?php } ?>
                    <div class="panel-heading">
                        <h1 class="panel-title text-center">DIAS SEM LANÇAMENTO</h1>
                    </div>
                    <div class="panel-body">
                        <?php if($dias <= 1){ ?>
                        <p class="text-center">
                            <span class="atualizado">
                                VOCÊ ESTÁ EM DIA!
                            </span>
                        </p>
                        <?php }elseif($dias == 2){ ?>
                        <h1 class="text-center">
                            <span class="atencao"><?php echo $dias ?></span>
                        </h1>
                        <?php }elseif($dias > 2){ ?>
                        <h1 class="text-center">
                            <span class="atrasado"><?php echo $dias ?></span>
                        </h1>
                        <p class="text-justify"><strong><small>Hoje é seu último dia para fazer lançamento da data <?php echo date("d/m/Y", strtotime("-2 days", strtotime(date("Y-m-d"))));?></small></strong>.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>
<?php
require_once("control/arquivo/footer/Footer.php");
endif;
?>