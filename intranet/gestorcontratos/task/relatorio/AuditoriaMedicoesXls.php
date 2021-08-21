<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/banco/conexao.php");
    require_once("../control/arquivo/funcao/Dados.php");
    $conexao = conexao::getInstance();
    header("Content-type: application/msexcel");
    header("Content-Disposition: attachment; filename=as.xls");
?>
<div class="container-fluid">
    <div class="conteudo">
        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12"><h1 class="text-center">AUDITORIA MEDICOES</h1></div>
        <div class="col-xs-12 col-md-12 col-lg-12"> </div>
        <form  name="Form" role="form" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente" >
            <div class="col-xs-12 col-md-12 col-lg-12"> </div>
            <div class="panel-group col-xs-12 col-md-12 col-lg-12" id="accordion">
            <?php
                     $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
                    $stm = $conexao->prepare($sql);
                    $stm->bindParam(1, $_SESSION['idusuarios']);
                    $stm->execute();
                    $row = $stm->fetchAll(PDO::FETCH_OBJ);
                    foreach($row as $rrt){
                        ?>
                    <h4>UNIDADE: <?=$rrt->Nome;?></h4>
                        <?php
                        $sql = "SELECT idContrato FROM contrato c WHERE c.Unidade_idUnidade = ? AND c.Finalizado = 'N'";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bindParam(1, $rrt->idUnidadeFaturamento);
                        $stmt->execute();
                        $rest = $stmt->fetchAll(PDO::FETCH_OBJ);
                        $contador = 0;
                        $dFinal = 0;
                        $dInicio = 0;
                        foreach($rest as $r){
                            $sql2 = "SELECT * FROM medicao m INNER JOIN contrato c ON c.idContrato = m.Contrato_idContrato INNER JOIN contratante cc ON cc.idContratante = c.Contratante_idContratante INNER JOIN cadastro cd ON cd.idCadastro = cc.Cadastro_idCadastro WHERE m.Contrato_idContrato = ?;";
                            $stm1 =$conexao->prepare($sql2);
                            $stm1->bindParam( 1, $r->idContrato );
                            $stm1->execute();
                            if($stm1->rowCount()>0){
                                $res = $stm1->fetchAll(PDO::FETCH_OBJ);
                                $x = '';
                                foreach($res as $r){
                                    $dInicio = $r->dInicio;
                                    while($dInicio <= $r->dFinal){
                                        if($dInicio != $r->dFinal){
                                            $x .= "'".$dInicio."',";
                                        }else{
                                            $x .= "'".$dInicio."',";
                                        }
                                        $dInicio = date("Y-m-d", strtotime("+ 1 DAY", strtotime($dInicio)));
                                    }
                                }
                                $x = substr_replace($x, '', -1);
                                
                                $sqlj   = "SELECT * FROM lancamento WHERE dLancamento NOT IN (".$x.") AND Contrato_idContrato = ? AND Quantidade >0;";
                                $stmt1  =$conexao->prepare($sqlj);
                                $stmt1->bindParam( 1, $r->idContrato );
                                $stmt1->execute();
                                $rr = $stmt1->fetchAll(PDO::FETCH_OBJ);
                                $xDatas = array();
                                foreach($rr as $rtt){
                                    array_push($xDatas, $rtt->dLancamento);
                                }
                                $xDatas = array_unique($xDatas);
                                
                                $sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
                                $stmt1 = $conexao->prepare($sql);
                                $stmt1->bindParam(1, $r->idContrato);
                                $stmt1->execute();
                                $itens = $stmt1->fetchAll(PDO::FETCH_OBJ);
                                    
                            ?>
                            <table class="table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo count($itens)+1; ?>"><?php echo "".$r->idContrato." - ". utf8_decode($r->Nome)." - ".$r->nContrato.""; ?></th>
                                    </tr><tr>
                                        <th>Data</th>
                                        <?php foreach($itens as $i){ ?>
                                        <th><?php echo utf8_decode($i->Servico);?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $vTotal = 0;
                                    $dataInicial = $dInicio;
                                    foreach($xDatas as $datas){
                                        $sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento = ?;";
                                        $stmt = $conexao->prepare($sql);
                                        $stmt->bindParam(1, $r->idContrato);
                                        $stmt->bindParam(2, $datas );
                                        $stmt->execute();
                                        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                                        $contador1 = $stmt->rowCount();
                                        $contador2 = count($itens);
                                        if($stmt->rowCount() > 0){
                                    ?>
                                    <tr>
                                        <td><?php echo date("d/m/Y", strtotime($datas));?></td>
                                        <?php
                                            foreach($rs as $x){
                                                $Total = $x->Quantidade * $x->ValorUni;
                                                $vTotal += $Total;
                                        ?>
                                        <td><?php echo $x->Quantidade; ?></td>
                                        <?php 
                                            }
                                        while($contador1 < $contador2){
                                        ?>
                                        <td>0</td>
                                        <?php
                                            $contador1++;
                                        }
                                        ?>
                                    </tr>
                                    <?php 
                                        }else{
                                    ?>
                                    <tr>
                                        <td><?php echo date("d/m/Y", strtotime($datas));?></td>
                                        <?php
                                            foreach($itens as $i){
                                        ?>
                                        <td>0</td>
                                        <?php
                                            }
                                        ?>
                                    </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                            }else{
                                break;
                            }
                        }
                    }
            ?>
            </div>
        </form>
        <div class="col-xs-12 col-md-12 col-lg-12"><hr /></div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>