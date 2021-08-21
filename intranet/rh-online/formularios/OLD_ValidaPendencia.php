<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
require_once("../control/arquivo/funcao/Outras.php");
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/header/Header.php");
require_once("../control/arquivo/Login.php");
else:
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-sm-12 conteudo">
			<div class="validacao">
				<?php
				$sql = "SELECT * FROM pendencias WHERE idPendencias = ?";
				$stmt = $conexao->prepare($sql);
				$stmt->bindParam(1, $_GET['p']);
				$stmt->execute();
				if($stmt->rowCount()!= 1):
				header("Location: ". BASE);exit;
				endif;
				$rs = $stmt->fetch(PDO::FETCH_OBJ);
				switch($rs->Tipo):
				case("Ferias"):
				try{
					$sql = "SELECT ct.Unidade_idUnidade, ctu.Nome AS Unidade, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER  JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ,(SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, u.Nome AS Solicitante, c.Nome AS Imediato, co.CodColaborador, ca.Nome AS Colaborador, f.* FROM usuarios u INNER JOIN ferias f ON idFerias = ? INNER JOIN usuarios c ON c.idUsuarios = ? INNER JOIN colaborador co ON co.idColaborador = f.Colaborador_idColaborador INNER JOIN contratacao ct ON ct.idContratacao = co.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = ct.Unidade_idUnidade INNER JOIN cadastro ctu ON ctu.idCadastro = un.Cadastro_idCadastro  INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro WHERE u.idUsuarios = ? LIMIT 1";
					$stm = $conexao->prepare($sql);	
					$stm->bindParam(1, $rs->CodTipo);
					$stm->bindParam(2, $rs->Responsavel_Colaborador);
					$stm->bindParam(3, $rs->Usuario_idUsuario);
					$stm->execute();
					$res = $stm->fetch(PDO::FETCH_OBJ);

				}catch(PDOexception $e){
					echo "Falha ao executar consulta: ".$e;
					header("Location: ". BASE);exit;
				}
				$unidade = $res->Unidade_idUnidade;
				?>
				<!-- INÍCIO FÉRIAS -->
				<div class="col-xs-12 text-center">
					<div class="col-xs-3"></div>
					<div class="col-xs-6">
						<div class="panel panel-primary text-justify">
							<div class="panel-heading">Validação de Férias</div>
							<div class="panel-body">
								<p><strong>Empresa: </strong><?php echo utf8_decode($res->Empresa)." - ".CNPJ_Padrao($res->CNPJ) ; ?> </p>
								<p><strong>Unidade: </strong><?php echo utf8_decode($res->Unidade) ; ?> </p>
								<p><strong>Colaborador: </strong><?php echo utf8_decode($res->Colaborador); ?> </p>
								<p><strong>Solicitante: </strong><?php echo utf8_decode($res->Solicitante); ?> </p>
								<p><strong>Superior Imediato: </strong><?php echo utf8_decode($res->Imediato); ?> </p>
								<hr />
								<p><strong>Período Aquisitivo de Férias: </strong><?php echo date("d/m/Y", strtotime($res->AquisitivoInicio)) ." à ". date("d/m/Y", strtotime($res->AquisitivoFinal));?> </p>
								<p><strong>Período de Gozo das Férias: </strong><?php echo date("d/m/Y", strtotime($res->pGozoInicio)) ." à ". date("d/m/Y", strtotime($res->pGozoFinal));?> </p>
								<p><strong>Abono Pecuniário: </strong><?php echo utf8_decode($res->Abono) ?> </p>
							</div>
						</div>
					</div>
					<div class="col-xs-3"></div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-2"></div>
					<div class="col-xs-4">
						<div class="panel panel-primary text-justify">
							<div class="panel-heading">Chefia</div>
							<?php
								$chf = "0";
								$j = array();

								$sql = "SELECT idusuarios FROM usuarios WHERE idUsuarios IN ( SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ?) AND Login <> 'rh.sede' AND Login <> 'rh.sede01'";
								$stm = $conexao->prepare($sql);
								$stm->bindParam(1, $res->Unidade_idUnidade);
								$stm->execute();
								while($rs = $stm->fetch(PDO::FETCH_OBJ)):
								$chf .= ",". $rs->idusuarios;
								array_push($j, $rs->idusuarios);
								endwhile;

								if(in_array($_SESSION['idusuarios'], $j)):
								$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
								$stm = $conexao->prepare($sql);
								$stm->bindParam(1, $_GET['p']);
								$stm->execute();
								if($stm->rowCount() == 0):
							?>
							<div class="panel-body text-center">
								<div class="col-xs-2"></div>
								<div class="col-xs-4">
									<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
										<input type="hidden" name="Voto" value="Aprovada" />
										<input type="hidden" name="Cod" value="<?php echo $res->CodColaborador; ?>" />
										<input type="hidden" name="tipo" value="ferias" />
										<button class="btn btn-success" type="submit">Aprovar</button>
									</form>
								</div>
								<div class="col-xs-4">
									<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
										<input type="hidden" name="Voto" value="Recusada" />
										<input type="hidden" name="tipo" value="ferias" />
										<button class="btn btn-danger" type="submit">Recusar</button>
									</form>
								</div>
								<div class="col-xs-2"></div>
							</div>
							<?php
								else:
									$r = $stm->fetch(PDO::FETCH_OBJ); 
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($r->Voto); ?></strong> por  <strong><?php echo utf8_decode($r->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($r->Data))); ?> às <?php echo utf8_decode($r->Hora); ?>.
							</div>
							<?php
							endif;
							?>
							<?php
							else:
							$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_GET['p']);
							$stm->execute();
							if($stm->rowCount() == 0):
							?>
							<div class="panel-body text-center">
								Pendência ainda não verificada!
							</div>
							<?php
							else:
							$resultset = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($resultset->Voto); ?></strong> por  <strong><?php echo utf8_decode($resultset->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($resultset->Data))); ?> às <?php echo utf8_decode($resultset->Hora); ?>.
							</div>
							<?php
							endif;
							endif;
							?>

						</div>
					</div>
					<div class="col-xs-4">
						<div class="panel panel-primary text-justify">
							<div class="panel-heading">RH</div>
							<?php

							$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_GET['p']);
							$stm->execute();
							if($stm->rowCount() == 0):
							?>
							<div class="panel-body text-center">
								Pendência ainda não Aprovada/Recusada!
							</div>
							<?php
							else:
							$resultset = $stm->fetch(PDO::FETCH_OBJ);
							if($resultset->Voto === "Recusada"):
							?>
							<div class="panel-body text-center">
								Essa pendência não pode ser validada, pois a mesma foi Recusada!
							</div>
							<?php
							else:
							$rh = array(3,16);
							$rh1 = "3,16";
							$sql = "SELECT vp.*,u.Nome FROM validapendencia vp INNER JOIN usuarios u ON u.idUsuarios = vp.Usuario_idUsuario WHERE vp.Usuario_idUsuario IN (3,16) AND vp.Pendencia_idPendencia = ?;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_GET['p']);
							$stm->execute();

							if(in_array($_SESSION['idusuarios'],$rh)):
							if($stm->rowCount() === 0):
							?>
							
							<div class="panel-body text-center">
								<div class="col-xs-2"></div>
								<div class="col-xs-4">
									<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="Pendencia" value="<?php echo $_GET['p']; ?>" />
										<input type="hidden" name="Voto" value="Validada" />
										<input type="hidden" name="tipo" value="ferias" />
										<button class="btn btn-success">Validar</button>
									</form>
								</div>
								<div class="col-xs-2">
									<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="Pendencia" value="<?php echo $_GET['p']; ?>" />
										<input type="hidden" name="Voto" value="Recusada (Preeenchimento Incorreto)" />
										<input type="hidden" name="tipo" value="ferias" />
										<button class="btn btn-warning">Recusar</button>
									</form>
								</div>
								<div class="col-xs-2"></div>
							</div>
							<?php
							else:
							$rst = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($rst->Voto); ?></strong> por  <strong><?php echo utf8_decode($rst->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($rst->Data))); ?> às <?php echo utf8_decode($rst->Hora); ?>.
							</div>
							<?php
							endif;
							else:
							if($stm->rowCount() === 0):

							?>
							<div class="panel-body text-center">
								Essa pendência ainda não foi validada pelo RH!
							</div>
							<?php
							else:
							$rst = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($rst->Voto); ?></strong> por  <strong><?php echo utf8_decode($rst->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($rst->Data))); ?> às <?php echo utf8_decode($rst->Hora); ?>.
							</div>
							<?php
							endif;
							endif;
							endif;
							endif;
							?>

						</div>
					</div>

					<div class="col-xs-2"></div>
				</div>
				<?php
				exit;
				?>
				<!-- FIM FÉRIAS -->
				<!-- INÍCIO DEMISSÃO -->
				<?php
				case("Demissao"):
				try{
					$sql = "SELECT ct.Unidade_idUnidade, ctu.Nome AS Unidade, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ, (SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, u.Nome AS Solicitante, c.Nome AS Imediato, co.CodColaborador AS Cod, ca.Nome AS Colaborador, ct.dAdmissao, d.* FROM usuarios u INNER JOIN demissao d ON d.idDemissao = ? INNER JOIN usuarios c ON c.idUsuarios = ? INNER JOIN colaborador co ON co.idColaborador = d.Colaborador_idColaborador INNER JOIN contratacao ct ON ct.idContratacao = co.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = ct.Unidade_idUnidade INNER JOIN cadastro ctu ON ctu.idCadastro = un.Cadastro_idCadastro  INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro WHERE u.idUsuarios = ? LIMIT 1";
					$stm = $conexao->prepare($sql);	
					$stm->bindParam(1, $rs->CodTipo);
					$stm->bindParam(2, $rs->Responsavel_Colaborador);
					$stm->bindParam(3, $rs->Usuario_idUsuario);
					$stm->execute();
					$res = $stm->fetch(PDO::FETCH_OBJ);
				}catch(PDOexception $e){
					echo "Falha ao executar consulta: ".$e;
					header("Location: ". BASE);exit;
				}
				$unidade = $res->Unidade_idUnidade;
				?>
				<div class="col-xs-12 text-center">
					<div class="col-xs-2"></div>
					<div class="col-xs-8">
						<div class="panel panel-primary text-justify">
							<div class="panel-heading">Análise de Desligamento</div>
							<div class="panel-body">
								<p><strong>Empresa: </strong><?php echo utf8_decode($res->Empresa)." - ".CNPJ_Padrao($res->CNPJ) ; ?> </p>
								<p><strong>Unidade: </strong><?php echo utf8_decode($res->Unidade) ; ?> </p>
								<p><strong>Solicitante: </strong><?php echo utf8_decode($res->Solicitante); ?> </p>
								<p><strong>Superior Imediato: </strong><?php echo utf8_decode($res->Imediato); ?> </p>
								<hr />
								<p>
									<strong>Colaborador: </strong><?php echo utf8_decode($res->Cod)."-".utf8_decode($res->Colaborador); ?>
									&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
									<strong>Data de Admissão: </strong><?php echo utf8_decode(date("d/m/Y",strtotime($res->dAdmissao))); ?>
								</p>
								<hr />
								<p><strong>Data Pretendida para Recisão: </strong><?php echo utf8_decode(date("d/m/Y",strtotime($res->DataRecisao))); ?></p>
								<p><strong>Tipo de Contrato: </strong><?php echo utf8_decode($res->TipoContrato); ?> </p>
								<p><strong>Advertência/Suspenção: </strong><?php echo utf8_decode($res->AdvSup); ?> </p>
								<p><strong>Promoção: </strong><?php echo utf8_decode($res->Promocao); ?> </p>
								<p><strong>Bens da Empresa: </strong></p>
								<ul>
									<?php echo utf8_decode($res->BensEmpresa); ?>
								</ul>
								<p><strong>Justifica: </strong><?php echo utf8_decode($res->Justificativa); ?> </p>
								<p><strong>Outros: </strong><?php echo utf8_decode($res->Outros); ?> </p>
							</div>
						</div>
					</div>
					<div class="col-xs-2"></div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-4">
						<div class="panel panel-primary text-justify">
							<div class="panel-heading">Sócio Administrador</div>
							<?php

							$chf = "4, 5";
							$j = array(4,5);

							if(in_array($_SESSION['idusuarios'], $j)):
							$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_GET['p']);
							$stm->execute();
							if($stm->rowCount() == 0):
							?>
							<div class="panel-body text-center">
								<div class="col-xs-2"></div>
								<div class="col-xs-4">
									<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
										<input type="hidden" name="Voto" value="Aprovada" />
										<input type="hidden" name="Cod" value="<?php echo $res->Cod; ?>" />
										<input type="hidden" name="tipo" value="demissao" />
										<button class="btn btn-success" type="submit">Aprovar</button>
									</form>
								</div>
								<div class="col-xs-4">
									<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
										<input type="hidden" name="Voto" value="Recusada" />
										<input type="hidden" name="tipo" value="ferias" />
										<button class="btn btn-danger" type="submit">Recusar</button>
									</form>
								</div>
								<div class="col-xs-2"></div>
							</div>
							<?php
								else:
										  $r = $stm->fetch(PDO::FETCH_OBJ); 
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($r->Voto); ?></strong> por  <strong><?php echo utf8_decode($r->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($r->Data))); ?> às <?php echo utf8_decode($r->Hora); ?>.
							</div>
							<?php
							endif;
							?>
							<?php
							else:
							$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_GET['p']);
							$stm->execute();
							if($stm->rowCount() == 0):
							?>
							<div class="panel-body text-center">
								Pendência ainda não verificada!
							</div>
							<?php
							else:
							$resultset = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($resultset->Voto); ?></strong> por  <strong><?php echo utf8_decode($resultset->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($resultset->Data))); ?> às <?php echo utf8_decode($resultset->Hora); ?>.
							</div>
							<?php
							endif;
							endif;
							?>

						</div>
					</div>

					<div class="col-xs-4">
						<div class="panel panel-primary text-justify">
							<div class="panel-heading">Superior Imediato</div>
							<?php

							$chf = "0";
							$j = array();

							$sql = "SELECT idusuarios FROM usuarios WHERE idUsuarios IN ( SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ?) AND Login <> 'rh.sede' AND Login <> 'anderson' AND Login <> 'virgilio' AND Login <> 'rh.sede01'";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $res->Unidade_idUnidade);
							$stm->execute();
							while($rs = $stm->fetch(PDO::FETCH_OBJ)):
							$chf .= ",". $rs->idusuarios;
							array_push($j, $rs->idusuarios);
							endwhile;

							if(in_array($_SESSION['idusuarios'], $j)):
							$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_GET['p']);
							$stm->execute();
							if($stm->rowCount() == 0):
							?>
							<div class="panel-body text-center">
								<div class="col-xs-2"></div>
								<div class="col-xs-4">
									<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
										<input type="hidden" name="Voto" value="Aprovada" />
										<input type="hidden" name="tipo" value="demissao" />
										<button class="btn btn-success" type="submit">Aprovar</button>
									</form>
								</div>
								<div class="col-xs-4">
									<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
										<input type="hidden" name="Voto" value="Recusada" />
										<input type="hidden" name="tipo" value="ferias" />
										<button class="btn btn-danger" type="submit">Recusar</button>
									</form>
								</div>
								<div class="col-xs-2"></div>
							</div>
							<?php
	else:
										  $r = $stm->fetch(PDO::FETCH_OBJ); 
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($r->Voto); ?></strong> por  <strong><?php echo utf8_decode($r->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($r->Data))); ?> às <?php echo utf8_decode($r->Hora); ?>.
							</div>
							<?php
							endif;
							?>
							<?php
							else:
							$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_GET['p']);
							$stm->execute();
							if($stm->rowCount() == 0):
							?>
							<div class="panel-body text-center">
								Pendência ainda não verificada!
							</div>
							<?php
							else:
							$resultset = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($resultset->Voto); ?></strong> por  <strong><?php echo utf8_decode($resultset->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($resultset->Data))); ?> às <?php echo utf8_decode($resultset->Hora); ?>.
							</div>
							<?php
							endif;
							endif;
							?>

						</div>
					</div>	
					<div class="col-xs-4">
						<div class="panel panel-primary text-justify">
							<div class="panel-heading">RH</div>
							<?php
							$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ? AND Usuario_idUsuario NOT IN (3,16)) AND Pendencia_idPendencia = ? LIMIT 2;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $unidade);
							$stm->bindParam(2, $_GET['p']);
							$stm->execute();
							if($stm->rowCount() <= 1):
							?>
							<div class="panel-body text-center">
								Pendência ainda não Aprovada/Recusada!	
							</div>
							<?php
							else:
							$resultset = $stm->fetch(PDO::FETCH_OBJ);
							if($resultset->Voto === "Recusada"):
							?>
							<div class="panel-body text-center">
								Essa pendência não pode ser validada, pois a mesma foi Recusada!
							</div>
							<?php
							else:
							$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ? AND Usuario_idUsuario NOT IN (3,16)) AND Pendencia_idPendencia = ? LIMIT 2;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $unidade);
							$stm->bindParam(2, $_GET['p']);
							$stm->execute();
							if($stm->rowCount() <= 1):
							?>
							<div class="panel-body text-center">
								Pendência ainda não Aprovada/Recusada!
							</div>
							<?php
							else:
							$resultset = $stm->fetch(PDO::FETCH_OBJ);
							if($resultset->Voto === "Recusada"):
							?>
							<div class="panel-body text-center">
								Essa pendência não pode ser validada, pois a mesma foi Recusada!
							</div>
							<?php
							else:
							$rh = array(3,16);
							$rh1 = "3,16";
							$sql = "SELECT vp.*,u.Nome FROM validapendencia vp INNER JOIN usuarios u ON u.idUsuarios = vp.Usuario_idUsuario WHERE vp.Usuario_idUsuario IN (3,16) AND vp.Pendencia_idPendencia = ?;";
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_GET['p']);
							$stm->execute();

							if(in_array($_SESSION['idusuarios'],$rh)):
							if($stm->rowCount() === 0):
							?>
							<div class="panel-body text-center">
								<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
									<input type="hidden" name="Pendencia" value="<?php echo $_GET['p']; ?>" />
									<input type="hidden" name="Voto" value="Validada" />
									<input type="hidden" name="tipo" value="ferias" />
									<button class="btn btn-success">Validar</button>
								</form>
							</div>
							<?php
							else:
							$rst = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($rst->Voto); ?></strong> por  <strong><?php echo utf8_decode($rst->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($rst->Data))); ?> às <?php echo utf8_decode($rst->Hora); ?>.
							</div>
							<?php
							endif;
							else:
							if($stm->rowCount() === 0):

							?>
							<div class="panel-body text-center">
								Essa pendência ainda não foi validada pelo RH!
							</div>
							<?php
							else:
							$rst = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<div class="panel-body text-center">
								Esta pendência foi <strong><?php echo utf8_decode($rst->Voto); ?></strong> por  <strong><?php echo utf8_decode($rst->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($rst->Data))); ?> às <?php echo utf8_decode($rst->Hora); ?>.
							</div>
							<?php
							endif;
							endif;
							endif;
							endif;
							?>	
						</div>
						<?php
						endif;
						endif;
						?>

					</div>
				</div>

				<div class="col-xs-2"></div>
			</div>


			<?php
			exit;
			?>
			<!-- FIM DEMISSÃO -->
			<!-- INÍCIO PROMOÇÃO -->
			<?php
			case("Promocao"):
			try{
				$sql = "SELECT ct.Unidade_idUnidade, ctu.Nome AS Unidade, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ, (SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, u.Nome AS Solicitante, c.Nome AS Imediato, ca.Nome AS Colaborador, co.CodColaborador AS Cod,ct.dAdmissao, p.* FROM usuarios u INNER JOIN promocao p ON p.idPromocao = ? INNER JOIN usuarios c ON c.idUsuarios = ? INNER JOIN colaborador co ON co.idColaborador = p.Colaborador_idColaborador INNER JOIN contratacao ct ON ct.idContratacao = co.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = ct.Unidade_idUnidade INNER JOIN cadastro ctu ON ctu.idCadastro = un.Cadastro_idCadastro INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro WHERE u.idUsuarios = ? LIMIT 1";
				$stm = $conexao->prepare($sql);	
				$stm->bindParam(1, $rs->CodTipo);
				$stm->bindParam(2, $rs->Responsavel_Colaborador);
				$stm->bindParam(3, $rs->Usuario_idUsuario);
				$stm->execute();
				$res = $stm->fetch(PDO::FETCH_OBJ);
			}catch(PDOexception $e){
				echo "Falha ao executar consulta: ".$e;
				header("Location: ". BASE);exit;
			}
			$unidade = $res->Unidade_idUnidade;
			?>
			<div class="col-xs-12 text-center">
				<div class="col-xs-2"></div>
				<div class="col-xs-8">
					<div class="panel panel-primary text-left">
						<div class="panel-heading">Aumento de Quadro/Substituição</div>
						<div class="panel-body">
							<p><strong>Empresa: </strong><?php echo utf8_decode($res->Empresa)." - ".CNPJ_Padrao($res->CNPJ) ; ?> </p>
							<p><strong>Unidade: </strong><?php echo utf8_decode($res->Unidade) ; ?> </p>
							<p><strong>Solicitante: </strong><?php echo utf8_decode($res->Solicitante); ?> </p>
							<p><strong>Superior Imediato: </strong><?php echo utf8_decode($res->Imediato); ?> </p>
							<hr />
							<p>
								<strong>Colaborador: </strong><?php echo $res->Cod." - ".utf8_decode($res->Colaborador); ?>
								&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
								<strong>Data de Admissão: </strong><?php echo utf8_decode(date("d/m/Y",strtotime($res->dAdmissao))); ?>
							</p>
							<hr />
							<p><strong>Data Pretendida: </strong><?php echo utf8_decode(date("d/m/Y",strtotime($res->DataPrev))); ?></p>
							<p>
								<strong>Motivo: </strong><?php echo utf8_decode($res->Motivo); ?>
								&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
								<strong>Tipo da Vaga: </strong><?php echo utf8_decode($res->TipoVaga); ?>
							</p>
							<p><strong>Está Prevista no Orçamento: </strong><?php echo utf8_decode($res->Orcamento); ?> </p>
							<div class="form-group">
								<label class="control-label">Perfil da Vaga: </label>
								<textarea readonly rows='4' class="form-control" ><?php echo utf8_decode($res->PerfilVaga); ?></textarea>
							</div>
							
							<?php
							if($res->Motivo === "Aumento de Quadro"):
								$sql = "SELECT * FROM cargo WHERE idCargo = ? LIMIT 1";
								$stm = $conexao->prepare($sql);	
								$stm->bindParam(1, $res->Cargo);
								$stm->execute();
								$rsCargo = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<div class="form-group">
								<label class="control-label">Justificativa para Aumento de Quadro: </label>
								<textarea readonly rows='4' class="form-control" ><?php echo utf8_decode($res->JustificativaAum); ?></textarea>
							</div>
							<p>
								<strong>Cargo: </strong><?php echo $rsCargo->CodCargo." - ".utf8_decode($rsCargo->Cargo); ?>
								&thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
								<strong>Função: </strong><?php echo utf8_decode($rsCargo->Funcao); ?>
							</p>
							<p>
								<strong>Salário: </strong><?php echo "R$ ". number_format($rsCargo->Salario,2,',','.'); ?>
							</p>
							<?php
							else:
								$sql = "SELECT ca.CodCargo, ca.Funcao, ca.Salario, col.CodColaborador, cad.Nome, co.dDemissao, co.dAdmissao, his.Historico, his.Justificativa FROM cargo ca INNER JOIN colaborador col ON col.idColaborador = ? INNER JOIN contratacao co ON co.idContratacao = col.Contratacao_idContratacao INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN historico his ON his.Colaborador_idColaborador = col.idColaborador WHERE ca.idCargo = co.Cargo_idCargo AND his.Historico = 'DemissÃ£o'";
								$stm = $conexao->prepare($sql);
								$stm->bindValue(1, $res->ColSub);
								$stm->execute();
								$rsCol = $stm->fetch(PDO::FETCH_OBJ);
							?>
							<p><strong>Colaborador a ser Substituido: </strong><?php echo $rsCol->CodColaborador." - ". utf8_decode($rsCol->Nome); ?></p>
							<p><strong>Função: </strong><?php echo $rsCol->CodCargo." -". utf8_decode($rsCol->Funcao); ?></p>
							<p><strong>Último Salário: </strong><?php echo "R$ ". number_format($rsCol->Salario,2,',','.'); ?></p>
							
							<?php
							endif;
							?>
						</div>
					</div>
				</div>
				<div class="col-xs-2"></div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-4">
					<div class="panel panel-primary text-justify">
						<div class="panel-heading">Sócio Administrador</div>
						<?php

						$chf = "4, 5";
						$j = array(4,5);

						if(in_array($_SESSION['idusuarios'], $j)):
						$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $_GET['p']);
						$stm->execute();
						if($stm->rowCount() == 0):
						?>
						<div class="panel-body text-center">
							<div class="col-xs-2"></div>
							<div class="col-xs-4">
								<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
									<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
									<input type="hidden" name="Voto" value="Aprovada" />
									<input type="hidden" name="Cod" value="<?php echo $res->Cod ?>" />
									<input type="hidden" name="tipo" value="promocao" />
									<button class="btn btn-success" type="submit">Aprovar</button>
								</form>
							</div>
							<div class="col-xs-4">
								<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
									<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
									<input type="hidden" name="Voto" value="Recusada" />
									<input type="hidden" name="tipo" value="ferias" />
									<button class="btn btn-danger" type="submit">Recusar</button>
								</form>
							</div>
							<div class="col-xs-2"></div>
						</div>
						<?php
							else:
								$r = $stm->fetch(PDO::FETCH_OBJ); 
						?>
						<div class="panel-body text-center">
							Esta pendência foi <strong><?php echo utf8_decode($r->Voto); ?></strong> por  <strong><?php echo utf8_decode($r->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($r->Data))); ?> às <?php echo utf8_decode($r->Hora); ?>.
						</div>
						<?php
						endif;
						?>
						<?php
						else:
						$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $_GET['p']);
						$stm->execute();
						if($stm->rowCount() == 0):
						?>
						<div class="panel-body text-center">
							Pendência ainda não verificada!
						</div>
						<?php
						else:
						$resultset = $stm->fetch(PDO::FETCH_OBJ);
						?>
						<div class="panel-body text-center">
							Esta pendência foi <strong><?php echo utf8_decode($resultset->Voto); ?></strong> por  <strong><?php echo utf8_decode($resultset->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($resultset->Data))); ?> às <?php echo utf8_decode($resultset->Hora); ?>.
						</div>
						<?php
						endif;
						endif;
						?>

					</div>
				</div>

				<div class="col-xs-4">
					<div class="panel panel-primary text-justify">
						<div class="panel-heading">Superior Imediato</div>
						<?php

						$chf = "0";
						$j = array();

						$sql = "SELECT idusuarios FROM usuarios WHERE idUsuarios IN ( SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ?) AND Login <> 'rh.sede' AND Login <> 'anderson' AND Login <> 'virgilio' AND Login <> 'rh.sede01'";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $res->Unidade_idUnidade);
						$stm->execute();
						while($rs = $stm->fetch(PDO::FETCH_OBJ)):
						$chf .= ",". $rs->idusuarios;
						array_push($j, $rs->idusuarios);
						endwhile;

						if(in_array($_SESSION['idusuarios'], $j)):
						$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $_GET['p']);
						$stm->execute();
						if($stm->rowCount() == 0):
						?>
						<div class="panel-body text-center">
							<div class="col-xs-2"></div>
							<div class="col-xs-4">
								<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
									<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
									<input type="hidden" name="Voto" value="Aprovada" />
									<input type="hidden" name="tipo" value="demissao" />
									<button class="btn btn-success" type="submit">Aprovar</button>
								</form>
							</div>
							<div class="col-xs-4">
								<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
									<input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
									<input type="hidden" name="Voto" value="Recusada" />
									<input type="hidden" name="tipo" value="ferias" />
									<button class="btn btn-danger" type="submit">Recusar</button>
								</form>
							</div>
							<div class="col-xs-2"></div>
						</div>
						<?php
							else:
								$r = $stm->fetch(PDO::FETCH_OBJ); 
						?>
						<div class="panel-body text-center">
							Esta pendência foi <strong><?php echo utf8_decode($r->Voto); ?></strong> por  <strong><?php echo utf8_decode($r->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($r->Data))); ?> às <?php echo utf8_decode($r->Hora); ?>.
						</div>
						<?php
						endif;
						?>
						<?php
						else:
						$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN ($chf) AND Pendencia_idPendencia = ? LIMIT 1;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $_GET['p']);
						$stm->execute();
						if($stm->rowCount() == 0):
						?>
						<div class="panel-body text-center">
							Pendência ainda não verificada!
						</div>
						<?php
						else:
						$resultset = $stm->fetch(PDO::FETCH_OBJ);
						?>
						<div class="panel-body text-center">
							Esta pendência foi <strong><?php echo utf8_decode($resultset->Voto); ?></strong> por  <strong><?php echo utf8_decode($resultset->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($resultset->Data))); ?> às <?php echo utf8_decode($resultset->Hora); ?>.
						</div>
						<?php
						endif;
						endif;
						?>

					</div>
				</div>	
				<div class="col-xs-4">
					<div class="panel panel-primary text-justify">
						<div class="panel-heading">RH</div>
						<?php
						$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ? AND Usuario_idUsuario NOT IN (3,16)) AND Pendencia_idPendencia = ? LIMIT 2;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $unidade);
						$stm->bindParam(2, $_GET['p']);
						$stm->execute();
						if($stm->rowCount() <= 1):
						?>
						<div class="panel-body text-center">
							Pendência ainda não Aprovada/Recusada!	
						</div>
						<?php
						else:
						$resultset = $stm->fetch(PDO::FETCH_OBJ);
						if($resultset->Voto === "Recusada"):
						?>
						<div class="panel-body text-center">
							Essa pendência não pode ser validada, pois a mesma foi Recusada!
						</div>
						<?php
						else:
						$sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ? AND Usuario_idUsuario NOT IN (3,16)) AND Pendencia_idPendencia = ? LIMIT 2;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $unidade);
						$stm->bindParam(2, $_GET['p']);
						$stm->execute();
						if($stm->rowCount() <= 1):
						?>
						<div class="panel-body text-center">
							Pendência ainda não Aprovada/Recusada!
						</div>
						<?php
						else:
						$resultset = $stm->fetch(PDO::FETCH_OBJ);
						if($resultset->Voto === "Recusada"):
						?>
						<div class="panel-body text-center">
							Essa pendência não pode ser validada, pois a mesma foi Recusada!
						</div>
						<?php
						else:
						$rh = array(3,16);
						$rh1 = "3,16";
						$sql = "SELECT vp.*,u.Nome FROM validapendencia vp INNER JOIN usuarios u ON u.idUsuarios = vp.Usuario_idUsuario WHERE vp.Usuario_idUsuario IN (3,16) AND vp.Pendencia_idPendencia = ?;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $_GET['p']);
						$stm->execute();

						if(in_array($_SESSION['idusuarios'],$rh)):
						if($stm->rowCount() === 0):
						?>
						<div class="panel-body text-center">
							<form action="<?php echo BASE."control/banco/PendenciaDAO.php"?>" method="post" enctype="multipart/form-data">
								<input type="hidden" name="Pendencia" value="<?php echo $_GET['p']; ?>" />
								<input type="hidden" name="Voto" value="Validada" />
								<input type="hidden" name="tipo" value="ferias" />
								<button class="btn btn-success">Validar</button>
							</form>
						</div>
						<?php
						else:
						$rst = $stm->fetch(PDO::FETCH_OBJ);
						?>
						<div class="panel-body text-center">
							Esta pendência foi <strong><?php echo utf8_decode($rst->Voto); ?></strong> por  <strong><?php echo utf8_decode($rst->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($rst->Data))); ?> às <?php echo utf8_decode($rst->Hora); ?>.
						</div>
						<?php
						endif;
						else:
						if($stm->rowCount() === 0):

						?>
						<div class="panel-body text-center">
							Essa pendência ainda não foi validada pelo RH!
						</div>
						<?php
						else:
						$rst = $stm->fetch(PDO::FETCH_OBJ);
						?>
						<div class="panel-body text-center">
							Esta pendência foi <strong><?php echo utf8_decode($rst->Voto); ?></strong> por  <strong><?php echo utf8_decode($rst->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($rst->Data))); ?> às <?php echo utf8_decode($rst->Hora); ?>.
						</div>
						<?php
						endif;
						endif;
						endif;
						endif;
						?>	
					</div>
					<?php
					endif;
					endif;
					?>

				</div>
			</div>

			<div class="col-xs-2"></div>
		</div>


		<?php
		exit;
		?>
		<!-- FIM PROMOÇÃO -->


		<?php
		endswitch;
		?>
	</div>
</div>

<?php
require_once("../control/arquivo/footer/Footer.php");
endif;
?>