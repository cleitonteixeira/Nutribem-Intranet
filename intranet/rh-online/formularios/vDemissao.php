<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
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
    if($stmt->rowCount()!= 1){
        header("Location: ". BASE);exit;
    }
    $rs = $stmt->fetch(PDO::FETCH_OBJ);
    $solicitante = $rs->Usuario_idUsuario;
    $gAdm = array(4,5);
    try{
        $sql = "SELECT ct.*, cg.*, ctu.Nome AS Unidade, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER  JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ,(SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa,u.idusuarios, u.Nome AS Solicitante, c.Nome AS Imediato, co.CodColaborador, ca.Nome AS Colaborador, f.* FROM usuarios u INNER JOIN demissao f ON f.iddemissao = ? INNER JOIN usuarios c ON c.idUsuarios = ? INNER JOIN colaborador co ON co.idColaborador = f.Colaborador_idColaborador INNER JOIN contratacao ct ON ct.idContratacao = co.Contratacao_idContratacao INNER JOIN cargo cg ON cg.idCargo = ct.Cargo_idCargo INNER JOIN unidade un ON un.idUnidade = ct.Unidade_idUnidade INNER JOIN cadastro ctu ON ctu.idCadastro = un.Cadastro_idCadastro INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro WHERE u.idUsuarios = ? LIMIT 1";
        $stm = $conexao->prepare($sql);	
        $stm->bindParam(1, $rs->CodTipo);
        $stm->bindParam(2, $rs->Responsavel_Colaborador);
        $stm->bindParam(3, $rs->Usuario_idUsuario);
        $stm->execute();
        $res = $stm->fetch(PDO::FETCH_OBJ);
        $cod = $res->CodColaborador;
    }catch(PDOexception $e){
        echo "Falha ao executar consulta: ".$e;
        header("Location: ". BASE);exit;
    }
    $unidade = $res->Unidade_idUnidade;
?>
                <div class="col-xs-12 col-md-12 col-lg-12 text-center">
                    <div class="panel panel-primary text-justify">
                        <div class="panel-heading">Análise de Desligamento</div>
                        <div class="panel-body">
                            <p><strong>Empresa: </strong><?php echo utf8_decode($res->Empresa)." - ".CNPJ_Padrao($res->CNPJ) ; ?> </p>
                            <p><strong>Unidade: </strong><?php echo utf8_decode($res->Unidade) ; ?> </p>
                            <p><strong>Solicitante: </strong><?php echo utf8_decode($res->Solicitante); ?> </p>
                            <p><strong>Superior Imediato: </strong><?php echo utf8_decode($res->Imediato); ?> </p>
                            <hr />
                            <p>
                                <strong>Colaborador: </strong><?php echo utf8_decode($res->CodColaborador)."-".utf8_decode($res->Colaborador); ?>
                                
                                <strong class="direita">Data de Admissão: </strong><?php echo utf8_decode(date("d/m/Y",strtotime($res->dAdmissao))); ?>
                            </p>
                            <p>
                                <strong>Cargo: </strong><?php echo utf8_decode($res->Funcao)." - CBO: ".utf8_decode($res->CBO); ?>
                                
                                <strong class="direita">Salário: </strong><?php echo "R$ ". number_format($res->Salario,2,",","."); ?>
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
                            <p><strong>Justificativa: </strong><?php echo utf8_decode($res->Justificativa); ?> </p>
                            <p><strong>Outros: </strong><?php echo utf8_decode($res->Outros); ?> </p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-12 col-lg-12">
                    <div class="col-xs-6 col-lg-6 col-md-6">
                        <div class="panel panel-primary text-justify">
                            <div class="panel-heading">Aprovador</div>
                            <?php
    function aprovadores( $control , $superior ){
        $conexao = conexao::getInstance();
        $_SESSION['sup'] = "";
        $sql = "SELECT idusuarios FROM usuarios WHERE idusuarios = (SELECT Superior FROM usuarios WHERE idusuarios = ?);";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $control);
        $stmt->execute();
        if ($stmt->rowCount() > 0){
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            array_push($superior, $res->idusuarios);
            aprovadores( $res->idusuarios , $superior );
        }else{
            $a = array(1,3,4,5);
            foreach($a as $b){
                if(!in_array($b, $superior)){
                    array_push($superior, $b);
                }
            }
            sort($superior);
            foreach($superior as $s){
                if(empty($_SESSION['sup'])){
                    $_SESSION['sup'] = $s;
                }else{
                    $_SESSION['sup'] .= ",". $s;
                }
            }
        }
    }
    $spq = array();
    aprovadores( $res->idusuarios , $spq );
    $sp = explode(",",$_SESSION['sup']);
    $user = $_SESSION['sup'];
    unset($_SESSION['sup']);

    if(in_array($_SESSION['idusuarios'], $sp)){
        $sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (". $user .") AND Pendencia_idPendencia = ? LIMIT 1;";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $_GET['p']);
        $stm->execute();
        if($stm->rowCount() == 0){
            if($_SESSION['Chefia'] != "N"){
                            ?>
                            <div class="panel-body text-center">
                                <div class="col-xs-6 col-lg-6 col-md-6">
                                    <form action="<?php echo BASE."control/banco/pDemissaoDAO.php"?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
                                        <input type="hidden" name="Voto" value="Aprovada" />
                                        <input type="hidden" name="Cod" value="<?php echo $cod; ?>" />
                                        <input type="hidden" name="tipo" value="demissao" />
                                        <button class="btn btn-success" type="submit">Aprovar</button>
                                    </form>
                                </div>
                                <div class="col-xs-6 col-lg-6 col-md-6">
                                    <form action="<?php echo BASE."control/banco/pDemissaoDAO.php"?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
                                        <input type="hidden" name="Voto" value="Recusada" />
                                        <input type="hidden" name="Cod" value="<?php echo $cod; ?>" />
                                        <input type="hidden" name="tipo" value="demissao" />
                                        <button class="btn btn-danger" type="submit">Recusar</button>
                                    </form>
                                </div>
                            </div>
                            <?php
            }else{
                            ?>
                            <div class="panel-body text-center">
                                Seu login não lhe dá acesso a aprovações.
                            </div>
                            <?php
            }
        }else{
            $r = $stm->fetch(PDO::FETCH_OBJ); 
                            ?>
                            <div class="panel-body text-center">
                                Esta pendência foi <strong><?php echo utf8_decode($r->Voto); ?></strong> por  <strong><?php echo utf8_decode($r->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($r->Data))); ?> às <?php echo utf8_decode($r->Hora); ?>.
                            </div>
                            <?php
        }
    }else{
        $sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (". $user .") AND Pendencia_idPendencia = ? LIMIT 1;";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $_GET['p']);
        $stm->execute();
        if($stm->rowCount() == 0){
                            ?>
                            <div class="panel-body text-center">
                                Pendência ainda não verificada!
                            </div>
                            <?php
        }else{
            $resultset = $stm->fetch(PDO::FETCH_OBJ);
                            ?>
                            <div class="panel-body text-center">
                                Esta pendência foi <strong><?php echo utf8_decode($resultset->Voto); ?></strong> por  <strong><?php echo utf8_decode($resultset->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($resultset->Data))); ?> às <?php echo utf8_decode($resultset->Hora); ?>.
                            </div>
                            <?php
        }
    }
                            ?>

                        </div>
                    </div>
                    <div class="col-xs-6 col-lg-6 col-md-6">
                        <div class="panel panel-primary text-justify">
                            <div class="panel-heading">RH</div>
                            <?php

    $sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (". $user .") AND Pendencia_idPendencia = ? LIMIT 1;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $_GET['p']);
    $stm->execute();
    if($stm->rowCount() == 0){
                            ?>
                            <div class="panel-body text-center">
                                Pendência ainda não Aprovada/Recusada!
                            </div>
                            <?php
    }else{
        $resultset = $stm->fetch(PDO::FETCH_OBJ);
        if($resultset->Voto === "Recusada"){
                            ?>
                            <div class="panel-body text-center">
                                Essa pendência não pode ser validada, pois a mesma foi Recusada!
                            </div>
                            <?php
        }else{
            $rh = array(1,3,16,14);
            $rh1 = "1,3,16,14";
            $sql = "SELECT vp.*,u.Nome FROM validapendencia vp INNER JOIN usuarios u ON u.idUsuarios = vp.Usuario_idUsuario WHERE vp.Usuario_idUsuario IN (1,3,16,14) AND vp.Pendencia_idPendencia = ?;";
            $stm = $conexao->prepare($sql);
            $stm->bindParam(1, $_GET['p']);
            $stm->execute();

            if(in_array($_SESSION['idusuarios'],$rh)){
                if($stm->rowCount() === 0){
                            ?>
                            <div class="panel-body text-center">
                                <div class="col-xs-12 col-lg-12 col-md-12">
                                    <form action="<?php echo BASE."control/banco/pDemissaoDAO.php"?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="Pendencia" value="<?php echo $_GET['p']; ?>" />
                                        <input type="hidden" name="Voto" value="Validada" />
                                        <input type="hidden" name="tipo" value="demissao" />
                                        <button class="btn btn-success">Validar</button>
                                    </form>
                                </div>
                            </div>
                            <?php
                }else{
                    $rst = $stm->fetch(PDO::FETCH_OBJ);
                            ?>
                            <div class="panel-body text-center">
                                Esta pendência foi <strong><?php echo utf8_decode($rst->Voto); ?></strong> por  <strong><?php echo utf8_decode($rst->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($rst->Data))); ?> às <?php echo utf8_decode($rst->Hora); ?>.
                            </div>
                            <?php
                }
            }else{
                if($stm->rowCount() === 0){

                            ?>
                            <div class="panel-body text-center">
                                Essa pendência ainda não foi validada pelo RH!
                            </div>
                            <?php
                }else{
                    $rst = $stm->fetch(PDO::FETCH_OBJ);
                            ?>
                            <div class="panel-body text-center">
                                Esta pendência foi <strong><?php echo utf8_decode($rst->Voto); ?></strong> por  <strong><?php echo utf8_decode($rst->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($rst->Data))); ?> às <?php echo utf8_decode($rst->Hora); ?>.
                            </div>
                            <?php
                }
            }
        }
    }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>