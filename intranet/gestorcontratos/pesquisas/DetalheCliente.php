<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location: ".BASE);
else:
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
$Troca = array("/","\\","|");
?>
<script>
    function setaDados(v){
        docume
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <?php
            $sql = "SELECT c.idContratante, c.IE, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca WHERE c.idContratante = ?;";
            $stm = $conexao->prepare($sql);
            $stm-> bindParam(1, $_GET['cod']);
            $stm->execute();
            $rs = $stm->fetch(PDO::FETCH_OBJ);
            ?>
            <h1 class="text-center"><span class="label label-info">DADOS CLIENTE</span></h1>
            <div class="col-md-12 col-xs-12 col-lg-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#Dados">Dados Cliente</a></li>
                    <li><a data-toggle="tab" href="#contrato">Contrato</a></li>
                    <li><a data-toggle="tab" href="#historico">Histórico</a></li>
                </ul>
                <div class="tab-content">
                    <div id="Dados" class="tab-pane fade in active">
                        <form name="Form" role="form" action="<?php echo BASE; ?>adm-online/control/banco/ClienteDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <p><small>Dados Iniciais</small></p>
                                <div class="col-xs-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="razao">Razão Social:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="razao" id="razao" required class="form-control" value="<?php echo utf8_decode($rs->Cliente); ?> " readonly />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="cpnj">CNPJ:</label>
                                        <div class="col-sm-6">
                                            <input required type="text" name="cnpj" id="cnpj" class="form-control" value="<?php echo utf8_decode(CNPJ_Padrao(str_pad($rs->CNPJ, 14,0,STR_PAD_LEFT))); ?> " readonly />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="ie">IE:</label>
                                        <div class="col-sm-6">
                                            <?php
                                            if(empty($rs->IE)){
                                                $ie = "ISENTO";
                                            }else{
                                                $ie = $rs->IE;
                                            }
                                            ?>
                                            <input required type="text" name="ie" id="ie" class="form-control" value="<?php echo $ie; ?> " readonly />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <hr />
                                <p><small>Dados de Contato</small></p>
                                <?php
                                $sql = "SELECT * FROM ccontratante WHERE Contratante_idContratante = ?;";
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $rs->idContratante);
                                $stm->execute();
                                $rx = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rx as $c){
                                ?>
                                <h4>Responsável <?php echo $c->Tipo?></h4>
                                <div class="col-xs-4 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="Responsavel">Responsável:</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="Responsavel" id="Responsavel" required class="form-control" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode($c->Responsavel); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="Email">Email:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="Email" id="Email" required class="form-control" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode($c->Email); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="Telefone">Telefone:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="Telefone" id="Telefone" required class="form-control" onkeypress="return SomenteNumero(event)" value="<?php echo strlen($c->Telefone) == 11 ? Cel_Padrao($c->Telefone) : Tel_Padrao($c->Telefone); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <hr />
                                <p><small>Dados de Endereço</small></p>
                                <div class="col-xs-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="logradouro">Logradouro:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="logradouro" id="logradouro" required class="form-control" value="<?php echo utf8_decode($rs->Endereco); ?> " readonly />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="cep">CEP:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="cep" id="cep" required class="form-control" placeholder="Ex.: 99.999-999" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode(CEP_Padrao($rs->CEP)); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="uf">UF:</label>
                                        <div class="col-sm-3">
                                            <select class="selectpicker form-control" title="Selecione uma UF" name="uf" id="uf" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma UF." required disabled>
                                                <option <?php echo $rs->UF=='AC'?'selected':'';?> data-tokens="Acre AC" value="AC">Acre - AC</option>
                                                <option <?php echo $rs->UF=='AL'?'selected':'';?> data-tokens="Alagoas AL" value="AL">Alagoas - AL</option>
                                                <option <?php echo $rs->UF=='AP'?'selected':'';?> data-tokens="Amapá AP" value="AP">Amapá - AP</option>
                                                <option <?php echo $rs->UF=='AM'?'selected':'';?> data-tokens="Amazonas AM" value="AM">Amazonas - AM</option>
                                                <option <?php echo $rs->UF=='BA'?'selected':'';?> data-tokens="Bahia BA" value="BA">Bahia - BA</option>
                                                <option <?php echo $rs->UF=='CE'?'selected':'';?> data-tokens="Ceará CE" value="CE">Ceará - CE</option>
                                                <option <?php echo $rs->UF=='DF'?'selected':'';?> data-tokens="Distrito Federal DF" value="DF">Distrito Federal - DF</option>
                                                <option <?php echo $rs->UF=='ES'?'selected':'';?> data-tokens="Espirito Santo ES" value="ES">Espirito Santo - ES</option>
                                                <option <?php echo $rs->UF=='GO'?'selected':'';?> data-tokens="Goiás GO" value="GO">Goiás - GO</option>
                                                <option <?php echo $rs->UF=='MA'?'selected':'';?> data-tokens="Maranhão MA" value="MA">Maranhão - MA</option>
                                                <option <?php echo $rs->UF=='MT'?'selected':'';?> data-tokens="Mato Grosso MT" value="MT">Mato Grosso - MT</option>
                                                <option <?php echo $rs->UF=='MS'?'selected':'';?> data-tokens="Mato Grosso do Sul MS" value="MS">Mato Grosso do Sul - MS</option>
                                                <option <?php echo $rs->UF=='MG'?'selected':'';?> data-tokens="Minas Gerais MG" value="MG">Minas Gerais - MG</option>
                                                <option <?php echo $rs->UF=='PA'?'selected':'';?> data-tokens="Pará PA" value="PA">Pará - PA</option>
                                                <option <?php echo $rs->UF=='PB'?'selected':'';?> data-tokens="Paraíba PB" value="PB">Paraíba - PB</option>
                                                <option <?php echo $rs->UF=='PR'?'selected':'';?> data-tokens="Paraná PR" value="PR">Paraná - PR</option>
                                                <option <?php echo $rs->UF=='PE'?'selected':'';?> data-tokens="Pernabuco PE" value="PE">Pernabuco - PE</option>
                                                <option <?php echo $rs->UF=='PI'?'selected':'';?> data-tokens="Piauí PI" value="PI">Piauí - PI</option>
                                                <option <?php echo $rs->UF=='RJ'?'selected':'';?> data-tokens="Rio de Janeiro RJ" value="RJ">Rio de Janeiro - RJ</option>
                                                <option <?php echo $rs->UF=='RN'?'selected':'';?> data-tokens="Rio Grande do Norte RN" value="RN">Rio Grande do Norte - RN</option>
                                                <option <?php echo $rs->UF=='RS'?'selected':'';?> data-tokens="Rio Grande do Sul RS" value="RS">Rio Grande do Sul - RS</option>
                                                <option <?php echo $rs->UF=='RO'?'selected':'';?> data-tokens="Rondônia RO" value="RO">Rondônia - RO</option>
                                                <option <?php echo $rs->UF=='RR'?'selected':'';?> data-tokens="Roraima RR" value="RR">Roraima - RR</option>
                                                <option <?php echo $rs->UF=='SC'?'selected':'';?> data-tokens="Santa Catarina SC" value="SC">Santa Catarina - SC</option>
                                                <option <?php echo $rs->UF=='SP'?'selected':'';?> data-tokens="São Paulo SP" value="SP">São Paulo - SP</option>
                                                <option <?php echo $rs->UF=='SE'?'selected':'';?> data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
                                                <option <?php echo $rs->UF=='TO'?'selected':'';?> data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="numero">Número:</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="numero" id="numero" required class="form-control" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode($rs->Numero); ?> " readonly />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="cidade">Cidade:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="cidade" id="cidade" required class="form-control" value="<?php echo utf8_decode($rs->Cidade); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="bairro">Bairro:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="bairro" id="bairro" required class="form-control" value="<?php echo utf8_decode(stripslashes($rs->Bairro)); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <p><small>Endereço de Cobrança</small></p>
                                <div class="col-xs-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="logradouro">Logradouro:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="logradouro" id="logradouro" required class="form-control" value="<?php echo utf8_decode($rs->eCobranca); ?> " readonly />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="cep">CEP:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="cep" id="cep" required class="form-control" placeholder="Ex.: 99.999-999" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode(CEP_Padrao($rs->ceCobranca)); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="uf">UF:</label>
                                        <div class="col-sm-3">
                                            <select class="selectpicker form-control" title="Selecione uma UF" name="uf" id="uf" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma UF." required disabled>
                                                <option <?php echo $rs->uCobranca=='AC'?'selected':'';?> data-tokens="Acre AC" value="AC">Acre - AC</option>
                                                <option <?php echo $rs->uCobranca=='AL'?'selected':'';?> data-tokens="Alagoas AL" value="AL">Alagoas - AL</option>
                                                <option <?php echo $rs->uCobranca=='AP'?'selected':'';?> data-tokens="Amapá AP" value="AP">Amapá - AP</option>
                                                <option <?php echo $rs->uCobranca=='AM'?'selected':'';?> data-tokens="Amazonas AM" value="AM">Amazonas - AM</option>
                                                <option <?php echo $rs->uCobranca=='BA'?'selected':'';?> data-tokens="Bahia BA" value="BA">Bahia - BA</option>
                                                <option <?php echo $rs->uCobranca=='CE'?'selected':'';?> data-tokens="Ceará CE" value="CE">Ceará - CE</option>
                                                <option <?php echo $rs->uCobranca=='DF'?'selected':'';?> data-tokens="Distrito Federal DF" value="DF">Distrito Federal - DF</option>
                                                <option <?php echo $rs->uCobranca=='ES'?'selected':'';?> data-tokens="Espirito Santo ES" value="ES">Espirito Santo - ES</option>
                                                <option <?php echo $rs->uCobranca=='GO'?'selected':'';?> data-tokens="Goiás GO" value="GO">Goiás - GO</option>
                                                <option <?php echo $rs->uCobranca=='MA'?'selected':'';?> data-tokens="Maranhão MA" value="MA">Maranhão - MA</option>
                                                <option <?php echo $rs->uCobranca=='MT'?'selected':'';?> data-tokens="Mato Grosso MT" value="MT">Mato Grosso - MT</option>
                                                <option <?php echo $rs->UF=='MS'?'selected':'';?> data-tokens="Mato Grosso do Sul MS" value="MS">Mato Grosso do Sul - MS</option>
                                                <option <?php echo $rs->uCobranca=='MG'?'selected':'';?> data-tokens="Minas Gerais MG" value="MG">Minas Gerais - MG</option>
                                                <option <?php echo $rs->uCobranca=='PA'?'selected':'';?> data-tokens="Pará PA" value="PA">Pará - PA</option>
                                                <option <?php echo $rs->uCobranca=='PB'?'selected':'';?> data-tokens="Paraíba PB" value="PB">Paraíba - PB</option>
                                                <option <?php echo $rs->uCobranca=='PR'?'selected':'';?> data-tokens="Paraná PR" value="PR">Paraná - PR</option>
                                                <option <?php echo $rs->uCobranca=='PE'?'selected':'';?> data-tokens="Pernabuco PE" value="PE">Pernabuco - PE</option>
                                                <option <?php echo $rs->uCobranca=='PI'?'selected':'';?> data-tokens="Piauí PI" value="PI">Piauí - PI</option>
                                                <option <?php echo $rs->uCobranca=='RJ'?'selected':'';?> data-tokens="Rio de Janeiro RJ" value="RJ">Rio de Janeiro - RJ</option>
                                                <option <?php echo $rs->uCobranca=='RN'?'selected':'';?> data-tokens="Rio Grande do Norte RN" value="RN">Rio Grande do Norte - RN</option>
                                                <option <?php echo $rs->uCobranca=='RS'?'selected':'';?> data-tokens="Rio Grande do Sul RS" value="RS">Rio Grande do Sul - RS</option>
                                                <option <?php echo $rs->uCobranca=='RO'?'selected':'';?> data-tokens="Rondônia RO" value="RO">Rondônia - RO</option>
                                                <option <?php echo $rs->uCobranca=='RR'?'selected':'';?> data-tokens="Roraima RR" value="RR">Roraima - RR</option>
                                                <option <?php echo $rs->uCobranca=='SC'?'selected':'';?> data-tokens="Santa Catarina SC" value="SC">Santa Catarina - SC</option>
                                                <option <?php echo $rs->uCobranca=='SP'?'selected':'';?> data-tokens="São Paulo SP" value="SP">São Paulo - SP</option>
                                                <option <?php echo $rs->uCobranca=='SE'?'selected':'';?> data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
                                                <option <?php echo $rs->uCobranca=='TO'?'selected':'';?> data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="numero">Número:</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="numero" id="numero" required class="form-control" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode($rs->nCobranca); ?> " readonly />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="cidade">Cidade:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="cidade" id="cidade" required class="form-control" value="<?php echo utf8_decode($rs->cCobranca); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="bairro">Bairro:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="bairro" id="bairro" required class="form-control" value="<?php echo utf8_decode(stripslashes($rs->bCobranca)); ?> " readonly/>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="col-xs-offset-3 col-md-offset-3 col-lg-offset-3 col-xs-4 col-md-4 col-lg-4 ">
                            <input type="hidden" name="Cliente" value="Atualizar" />
                            <button class="btn btn-success" type="submit">Salvar</button>
                            <button class="btn btn-danger" type="reset">Cancelar</button>
                            </div>
                            -->
                        </form>
                    </div>
                    <div id="contrato" class="tab-pane fade">
                        <?php
                        $sql = "SELECT * FROM contrato INNER JOIN unidadefaturamento u ON idUnidadeFaturamento = Unidade_idUnidade INNER JOIN cadastro ON idCadastro = u.Cadastro_idCadastro WHERE Contratante_idContratante = ?;";
                        $st = $conexao->prepare($sql);
                        $st->bindParam(1, $_GET['cod']);
                        $st->execute();
                        $ct = $st->fetchAll(PDO::FETCH_OBJ);
                        if($st->rowCount() > 0){
                        ?>
                        <table class="table table-bordered">
                            <thead>
                                <th>Nº Contrato</th>    
                                <th>Vigência Inicio</th>
                                <th>Vigência Final</th>
                                <th>Data de Reajuste</th>
                                <th>UF</th>
                                <th>Unidade</th>
                                <th>Ativo</th>
                                <th>Detalhes</th>
                            </thead>
                            <tbody>
                                <?php 
                            foreach($ct as $a){
                                ?>
                                <tr class="text-center">
                                    <td><?php echo $a->nContrato; ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($a->VigenciaIni)); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($a->VigenciaFim)); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($a->DataReajuste)); ?></td>
                                    <td><?php echo $a->UF; ?></td>
                                    <td><?php echo utf8_decode($a->Nome); ?></td>
                                    <td><?php echo $a->Finalizado == 'S' ? 'N' : 'S'; ?></td>
                                    <td><a class="btn btn-primary btn-sm" target="_blank" href="DetalheContrato.php?id=<?php echo $a->idContrato; ?>"><i class="fas fa-external-link-alt"></i></a></td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        }else{
                        ?>
                        <h3 class="text-center">NENHUM CONTRATO CADASTRADO!</h3>
                        <?php 
                        }
                        ?>
                    </div>
                    <div id="historico" class="tab-pane fade">
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <?php
                            $sql = "SELECT h.*, u.Nome FROM historial h INNER JOIN usuarios u ON u.idusuarios = h.Usuario_idUsuario WHERE Contratante_idContratante = ? ORDER BY idHistorial DESC";
                            $stm = $conexao->prepare($sql);
                            $stm->bindParam(1, $_GET['cod']);
                            $stm->execute();
                            $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                            $x = 0;
                            if($stm->rowCount() < 1):
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h1 class="text-center">NÃO HÁ DADOS DE HISTÓRICO.</h1>
                                </div>
                            </div>
                            <?php
                            else:
                            foreach($rs as $r):
                            if($x % 2 == 0):
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <p class="panel-title"><strong>Tipo: </strong><?php echo utf8_decode($r->Tipo); ?> ----- <strong>Data Lançamento: </strong><?php echo Muda_Data($r->DataCad); ?> ----- <strong>Responsável: </strong><?php echo utf8_decode($r->Nome); ?></p> 
                                </div>
                                <div class="panel-body">
                                    <p><strong>Data do Evento: </strong><?php echo Muda_Data($r->DataVis); ?> </p>
                                    <p><strong>Descrição: </strong><?php echo utf8_decode($r->Descricao); ?> </p>
                                    <?php
                                    $sql = "SELECT * FROM dochistorial WHERE Historial_idHistorial = ?;";
                                    $stmt = $conexao->prepare($sql);
                                    $stmt->bindParam(1, $r->idHistorial);
                                    $stmt->execute();
                                    $contador = 1;
                                    $t = "";
                                    if($stmt->rowCount() > 0){
                                        while($j = $stmt->fetch(PDO::FETCH_OBJ)){
                                            if($contador % 2 == 0){
                                                $t = "default";
                                            }else{
                                                $t = "primary";
                                            }
                                    ?>
                                    <a class="btn btn-<?php echo $t; ?>" target="_blank" role="button" href="<?php echo BASE; ?>clientes/docs/<?php echo $j->Documento; ?>"><strong><i class="fa fa-file-o"></i> Ver Arquivo <?php echo str_pad($contador,2,0,STR_PAD_LEFT);?></strong></a>
                                    <?php
                                            $contador += 1;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            else:
                            ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <p class="panel-title"><strong>Tipo: </strong><?php echo utf8_decode($r->Tipo); ?> ----- <strong>Data Lançamento: </strong><?php echo Muda_Data($r->DataCad); ?> ----- <strong>Responsável: </strong><?php echo utf8_decode($r->Nome); ?></p>  
                                </div>
                                <div class="panel-body">
                                    <p><strong>Data do Evento: </strong><?php echo Muda_Data($r->DataVis); ?> </p>
                                    <p><strong>Descrição: </strong><?php echo utf8_decode($r->Descricao); ?> </p>
                                    <?php
                                    $sql = "SELECT * FROM dochistorial WHERE Historial_idHistorial = ?;";
                                    $stmt = $conexao->prepare($sql);
                                    $stmt->bindParam(1, $r->idHistorial);
                                    $stmt->execute();
                                    $contador = 1;
                                    $t = "";
                                    if($stmt->rowCount() > 0){
                                        while($j = $stmt->fetch(PDO::FETCH_OBJ)){
                                            if($contador % 2 == 0){
                                                $t = "primary";
                                            }else{
                                                $t = "default";
                                            }
                                    ?>
                                    <a class="btn btn-<?php echo $t; ?>" target="_blank" role="button" href="<?php echo BASE; ?>clientes/docs/<?php echo $j->Documento; ?>"><strong><i class="fa fa-file-o"></i> Ver Arquivo <?php echo str_pad($contador,2,0,STR_PAD_LEFT);?></strong></a>
                                    <?php
                                            $contador += 1;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            endif;
                            $x +=1;
                            endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>
<?php
require_once("../control/arquivo/footer/Footer.php");
endif;
?>