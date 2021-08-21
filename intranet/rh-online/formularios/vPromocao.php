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
        <div class="col-md-12 col-xs-12 col-sm-12 conteudo validacao">
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
        $sql = "SELECT ct.*, cg.Funcao, cg.CBO, cg.Salario AS Pagamento, ctu.Nome AS Unidade, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ, (SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, u.Nome AS Solicitante, u.idusuarios, c.Nome AS Imediato, ca.Nome AS Colaborador, co.CodColaborador AS Cod,ct.dAdmissao, p.* FROM usuarios u INNER JOIN promocao p ON p.idPromocao = ? INNER JOIN usuarios c ON c.idUsuarios = ? INNER JOIN colaborador co ON co.idColaborador = p.Colaborador_idColaborador INNER JOIN contratacao ct ON ct.idContratacao = co.Contratacao_idContratacao INNER JOIN cargo cg ON cg.idCargo = ct.Cargo_idCargo INNER JOIN unidade un ON un.idUnidade = ct.Unidade_idUnidade INNER JOIN cadastro ctu ON ctu.idCadastro = un.Cadastro_idCadastro INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro WHERE u.idUsuarios = ? LIMIT 1";
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
            <div class="col-xs-12 col-md-12 col-lg-12 text-center">
                <div class="">
                    <div class="panel panel-primary text-left">
                        <div class="panel-heading">Aumento de Quadro /  Substituição /   Aumento de Salário</div>
                        <div class="panel-body">
                            <p><strong>Empresa: </strong><?php echo utf8_decode($res->Empresa)." - ".CNPJ_Padrao($res->CNPJ) ; ?> </p>
                            <p><strong>Unidade: </strong><?php echo utf8_decode($res->Unidade) ; ?> </p>
                            <p><strong>Solicitante: </strong><?php echo utf8_decode($res->Solicitante); ?> </p>
                            <p><strong>Superior Imediato: </strong><?php echo utf8_decode($res->Imediato); ?> </p>
                            <hr />
                            <p>
                                <strong>Colaborador: </strong><?php echo $res->Cod." - ".utf8_decode($res->Colaborador); ?>
                                <strong class="direita">Data de Admissão: </strong><?php echo utf8_decode(date("d/m/Y",strtotime($res->dAdmissao))); ?>
                            </p>
                            <p>
                                <strong>Cargo: </strong><?php echo utf8_decode($res->Funcao)." - CBO: ".utf8_decode($res->CBO); ?>
                                
                                <strong class="direita">Salário: </strong><?php echo "R$ ". number_format($res->Pagamento,2,",","."); ?>
                            </p>
                            <hr />
                            <p><strong>Data Pretendida: </strong><?php echo utf8_decode(date("d/m/Y",strtotime($res->DataPrev))); ?></p>
                            <p>
                                <strong>Motivo: </strong> 
                                <?php 
    
    switch($res->Motivo){
        case("aQuadro"):
            $motivo = "Aumento de Quadro";
            break;
        case("aSalario"):
            $motivo = "Aumento de Salário";
            break;
        case("substituicao"):
            $motivo = "Substituição";
            break;
    }
    echo $motivo;
                                ?>
                                <strong class="direita">Tipo da Vaga: </strong><?php echo utf8_decode($res->TipoVaga); ?>
                            </p>
                            <p><strong>Está Prevista no Orçamento: </strong><?php echo utf8_decode($res->Orcamento); ?>.</p>
                            <p><strong>Perfil da Vaga: </strong><?php echo utf8_decode($res->PerfilVaga); ?></p>


                            <?php
    if($res->Motivo === "aQuadro"){
        $sql = "SELECT * FROM cargo WHERE idCargo = ? LIMIT 1";
        $stm = $conexao->prepare($sql);	
        $stm->bindParam(1, $res->Cargo);
        $stm->execute();
        $rsCargo = $stm->fetch(PDO::FETCH_OBJ);
                            ?>
                            <p><strong>Justificativa para Aumento de Quadro: </strong><?php echo utf8_decode($res->JustificativaAum); ?></p>

                            <p>
                                <strong>Cargo: </strong><?php echo $rsCargo->CodCargo." - ".utf8_decode($rsCargo->Cargo); ?>
                                <strong class="direita">Função: </strong><?php echo utf8_decode($rsCargo->Funcao); ?>
                            </p>
                            <p>
                                <strong>Salário: </strong><?php echo "R$ ". number_format($rsCargo->Salario,2,',','.'); ?>
                            </p>
                            <?php
    }elseif($res->Motivo === "substituicao"){
        $sql = "SELECT ca.CodCargo, ca.Funcao, ca.Salario, col.CodColaborador, cad.Nome, co.dDemissao, co.dAdmissao, his.Historico, his.Justificativa FROM cargo ca INNER JOIN colaborador col ON col.idColaborador = ? INNER JOIN contratacao co ON co.idContratacao = col.Contratacao_idContratacao INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN historico his ON his.Colaborador_idColaborador = col.idColaborador WHERE ca.idCargo = co.Cargo_idCargo AND his.Historico = 'DemissÃ£o'";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $res->ColSub);
        $stm->execute();
        $rsCol = $stm->fetch(PDO::FETCH_OBJ);
                            ?>
                            <p><strong>Colaborador a ser Substituido: </strong><?php echo $rsCol->CodColaborador." - ". utf8_decode($rsCol->Nome); ?></p>
                            <p><strong>Função: </strong><?php echo $rsCol->CodCargo." - ". utf8_decode($rsCol->Funcao); ?></p>
                            <p><strong>Último Salário: </strong><?php echo "R$ ". number_format($rsCol->Salario,2,',','.'); ?></p>

                            <?php
    }elseif($res->Motivo === "aSalario"){
                            ?>
                            <p><strong>Justificativa: </strong><?php echo $res->JustSalario ?></p>
                            <p><strong>Salário: </strong><?php echo "R$ ". number_format($res->Salario,2,',','.'); ?></p>
                            <?php
    }else{
        header("Location: ".BASE);
    }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                
                <div class="col-xs-6">
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
        $sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (".$user.") AND Pendencia_idPendencia = ? LIMIT 1;";
        $stm = $conexao->prepare($sql);
        $stm->bindParam(1, $_GET['p']);
        $stm->execute();
        if($stm->rowCount() == 0){
                        ?>
                        <div class="panel-body text-center">
                            <div class="col-xs-2"></div>
                            <div class="col-xs-4">
                                <form action="<?php echo BASE."control/banco/pPromocaoDAO.php"?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
                                    <input type="hidden" name="Voto" value="Aprovada" />
                                    <input type="hidden" name="Cod" value="<?php echo $res->Cod ?>" />
                                    <input type="hidden" name="tipo" value="promocao" />
                                    <button class="btn btn-success" type="submit">Aprovar</button>
                                </form>
                            </div>
                            <div class="col-xs-4">
                                <form action="<?php echo BASE."control/banco/pPromocaoDAO.php"?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="Pendencia" value="<?php echo $_GET['p'] ?>" />
                                    <input type="hidden" name="Voto" value="Recusada" />
                                    <input type="hidden" name="Cod" value="<?php echo $res->Cod ?>" />
                                    <input type="hidden" name="tipo" value="promocao" />
                                    <button class="btn btn-danger" type="submit">Recusar</button>
                                </form>
                            </div>
                            <div class="col-xs-2"></div>
                        </div>
                        <?php
        }else{
            $r = $stm->fetch(PDO::FETCH_OBJ); 
                        ?>
                        <div class="panel-body text-center">
                            Esta pendência foi <strong><?php echo utf8_decode($r->Voto); ?></strong> por  <strong><?php echo utf8_decode($r->Nome); ?></strong>, no dia <?php echo utf8_decode(date("d/m/Y", strtotime($r->Data))); ?> às <?php echo utf8_decode($r->Hora); ?>.
                        </div>
                        <?php
        }
    }else{
        $sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (".$user.") AND Pendencia_idPendencia = ? LIMIT 1;";
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
        }}
                        ?>

                    </div>
                </div>	
                <div class="col-xs-6">
                    <div class="panel panel-primary text-justify">
                        <div class="panel-heading">RH</div>
                        <?php
    $sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ? AND Usuario_idUsuario NOT IN (3,14,16)) AND Pendencia_idPendencia = ?;";
    $stm = $conexao->prepare($sql);
    $stm->bindParam(1, $unidade);
    $stm->bindParam(2, $_GET['p']);
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
            $sql = "SELECT v.*, u.Nome FROM validapendencia v INNER JOIN usuarios u ON u.idusuarios = v.Usuario_idUsuario WHERE Usuario_idUsuario IN (SELECT Usuario_idUsuario FROM unidadeuser WHERE Unidade_idUnidade = ? AND Usuario_idUsuario NOT IN (3,14,16)) AND Pendencia_idPendencia = ? ;";
            $stm = $conexao->prepare($sql);
            $stm->bindParam(1, $unidade);
            $stm->bindParam(2, $_GET['p']);
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
                    $rh = array(1,3,14,16);
                    $rh1 = "3,16,14";
                    $sql = "SELECT vp.*,u.Nome FROM validapendencia vp INNER JOIN usuarios u ON u.idUsuarios = vp.Usuario_idUsuario WHERE vp.Usuario_idUsuario IN (3,16,14) AND vp.Pendencia_idPendencia = ?;";
                    $stm = $conexao->prepare($sql);
                    $stm->bindParam(1, $_GET['p']);
                    $stm->execute();

                    if(in_array($_SESSION['idusuarios'],$rh)){
                        if($stm->rowCount() === 0){
                        ?>
                        <div class="panel-body text-center">
                            <form action="<?php echo BASE."control/banco/pPromocaoDAO.php"?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="Pendencia" value="<?php echo $_GET['p']; ?>" />
                                <input type="hidden" name="Voto" value="Validada" />
                                <input type="hidden" name="tipo" value="promocao" />
                                <button class="btn btn-success">Validar</button>
                            </form>
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
                        }}}}
                        ?>	
                    </div>
                    <?php
        }
    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

}
?>