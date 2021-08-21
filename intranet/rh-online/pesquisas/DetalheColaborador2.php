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
  
if(empty($_GET´['cod'])):
$Codigo = isset( $_GET['empresa'] ) ? (int)$_GET['empresa'] : 0;
else:
header("Location: Colaborador.php");
endif;
$conexao = conexao::getInstance();
$Troca = array("/","\\","|");
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <?php
            $sql = "SELECT ca.*, col.*, co.*, con.*, cad.Nome, cad.CPF,doc.*, en.*, (SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = con.Unidade_idUnidade) AS Unidade, (SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER  JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ  FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN endereco en ON en.idEndereco = col.Endereco_idEndereco INNER JOIN contato co ON co.idContato  = col.Contato_idContato INNER JOIN documento doc ON doc.idDocumento = col.Documento_idDocumento INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = con.Unidade_idUnidade INNER JOIN cargo ca ON ca.idCargo = con.Cargo_idCargo WHERE col.idColaborador = ?;";
            $stm = $conexao->prepare($sql);
            $stm-> bindParam(1, $_GET['cod']);
            $stm->execute();
            if($stm->rowCount() == 1):
            $rs = $stm->fetch(PDO::FETCH_OBJ);
            $sql = "SELECT en.* FROM empresa em INNER JOIN endereco en ON en.idEndereco = em.Endereco_idEndereco INNER JOIN unidade un ON un.idUnidade = ? WHERE em.idEmpresa = un.Empresa_idEmpresa";
            $stm = $conexao->prepare($sql);
            $stm-> bindParam(1, $rs->Unidade_idUnidade);
            $stm->execute();
            $rsEm = $stm->fetch(PDO::FETCH_OBJ);
            $sql = "SELECT en.* FROM unidade un INNER JOIN endereco en ON en.idEndereco = un.Endereco_idEndereco WHERE un.idUnidade = ?";
            $stm = $conexao->prepare($sql);
            $stm-> bindParam(1, $rs->Unidade_idUnidade);
            $stm->execute();
            $rsUn = $stm->fetch(PDO::FETCH_OBJ);
            ?>
            <div class="col-xs-3"></div>
            <div class="col-xs-6">
                <h1><span class="label label-info">REGISTRO DE EMPREGADO</span></h1>
            </div>
            <div class="col-xs-3"></div>
            <div class="col-xs-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#Pessoais">Dados Pessoais</a></li>
                    <li><a data-toggle="tab" href="#Contratacao">Dados da Contratação</a></li>
                    <li><a data-toggle="tab" href="#Filhos">Dados dos Dependentes</a></li>
                    <li><a data-toggle="tab" href="#Bancarios">Dados Bancários</a></li>
                    <li><a data-toggle="tab" href="#Endereco">Dados de Endereço</a></li>
                    <li><a data-toggle="tab" href="#Contato">Dados de Contato</a></li>
                    <li><a data-toggle="tab" href="#Extras">Histórico</a></li>
                </ul>
                <div class="tab-content">
                    <div id="Pessoais" class="tab-pane fade in active">
                        <div class="col-xs-10">

                            <div class="col-sm-3"></div><div class="col-sm-6"><h3 class="Colaborador"><strong>Nome:</strong> <?php echo utf8_decode($rs->Nome);?></h3></div><div class="col-sm-3"></div>
                        </div>
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>FILIAÇÃO</u></h4></div>
                        <div class="col-xs-10">
                            <div class="col-sm-12"><p><strong>Pai:</strong> <?php echo utf8_decode($rs->nPai);?></p></div>
                            <div class="col-sm-12"><p><strong>Mãe:</strong> <?php echo utf8_decode($rs->nMae);?></p></div>
                        </div>
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>DOCUMENTAÇÃO</u></h4></div>
                        <div class="col-xs-10">    
                            <div class="col-sm-2"><p><strong>RG:</strong> <?php echo utf8_decode($rs->RG);?></p></div>
                            <div class="col-sm-2"><p><strong>Emissor:</strong> <?php echo utf8_decode($rs->Emissor);?></p></div>
                            <div class="col-sm-3"><p><strong>Data de Emissão:</strong> <?php echo utf8_decode(Muda_Data($rs->dEmissao));?></p></div>
                            <div class="col-sm-3"><p><strong>CPF:</strong> <?php echo utf8_decode(CPF_Padrao(str_pad($rs->CPF,11,0, STR_PAD_LEFT)));?></p></div>
                            <div class="col-sm-2"></div>
                            <div class="col-sm-4"><p><strong>Título de Eleitoral:</strong> <?php echo utf8_decode($rs->Titulo);?></p></div>
                            <div class="col-sm-4"><p><strong>Zona:</strong> <?php echo utf8_decode($rs->Zona);?></p></div>
                            <div class="col-sm-4"><p><strong>Seção:</strong> <?php echo utf8_decode(str_pad($rs->Secao,4,0,STR_PAD_LEFT));?></p></div>
                            <?php
                            if(!empty($rs->CNH)):
                            ?>
                            <div class="col-sm-4"><p><strong>CNH:</strong> <?php echo utf8_decode(str_pad($rs->CNH,11,0, STR_PAD_LEFT));?></p></div>
                            <div class="col-sm-4"><p><strong>Categoria:</strong> <?php echo utf8_decode($rs->CategoriaCNH);?></p></div>
                            <?php
                            endif;
                            ?>
                        </div>
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>DADOS COMPLEMENTARES</u></h4></div>
                        <div class="col-xs-10">
                            <div class="col-sm-4"><p><strong>Data de Nascimento:</strong> <?php echo utf8_decode(Muda_Data($rs->dNascimento));?></p></div>
                            <div class="col-sm-3"><p><strong>Cor:</strong> <?php echo utf8_decode($rs->Cor);?></p></div>
                            <div class="col-sm-5"><p><strong>Naturalidade:</strong> <?php echo utf8_decode($rs->Naturalidade);?></p></div>
                            <div class="col-sm-4"><p><strong>Escolaridade:</strong> <?php echo utf8_decode($rs->Escolaridade);?></p></div>
                            <div class="col-sm-5"><p><strong>Estado Civil:</strong> <?php echo utf8_decode($rs->eCivil);?></p></div>
                            <?php
                            if(!empty($rs->NomeConjugue)):
                            ?>
                            <div class="col-sm-4"><p><strong>Cônjugue:</strong> <?php echo utf8_decode($rs->NomeConjugue);?></p></div>
                            <div class="col-sm-4"><p><strong>Mãe <small>(Cônjugue)</small>:</strong> <?php echo utf8_decode($rs->NomeMaeConjugue);?></p></div>
                            <div class="col-sm-4"><p><strong>Data de Nascimento <small>(Cônjugue)</small>:</strong> <?php echo utf8_decode(Muda_Data($rs->dNascimentoConjugue));?></p></div>
                            <div class="col-sm-4"><p><strong>CPF <small>(Conjugue)</small>:</strong> <?php echo utf8_decode(CPF_Padrao(str_pad($rs->ConjugueCPF,11,0, STR_PAD_LEFT)));?></p></div>
                            <div class="col-sm-4"><p><strong>Certidão de Casamento:</strong> <?php echo utf8_decode($rs->cCasamento);?></p></div>
                            <?php
                            endif;
                            ?>
                        </div>

                        <div class="col-xs-2"></div>
                    </div>
                    <div id="Contratacao" class="tab-pane fade">
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>EMPRESA</u></h4></div>
                        <div class="col-xs-10">
                            <div class="col-xs-8"><p><strong>Empregador:</strong> <?php echo utf8_decode(strtoupper($rs->Empresa));?></p></div>
                            <div class="col-xs-3"><p><strong>CNPJ:</strong> <?php  echo  CNPJ_Padrao($rs->CNPJ); ?></p></div>
                            <div class="col-xs-3"></div>
                            <div class="col-xs-12"><p><strong>Endereço:</strong>
                                <?php
                                $end = utf8_decode($rsEm->Endereco);
                                $bairro = utf8_decode($rsEm->Bairro);
                                $cidade = utf8_decode($rsEm->Cidade);
                                echo $end .", nº: ". $rsEm->Numero .", ". $bairro ." - ". $cidade ."-". $rsEm->UF;
                                ?>
                                </p></div>
                            <div class="col-xs-2"><p><strong>CEP: </strong><?php echo CEP_Padrao($rsEm->CEP); ?> </div>
                        </div>
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>UNIDADE</u></h4></div>
                        <div class="col-xs-10">
                            <div class="col-xs-6"><p><strong>Unidade:</strong> <?php echo utf8_decode($rs->Unidade);?></p></div>
                            <div class="col-xs-6"></div>
                            <div class="col-xs-12"><p><strong>Endereço:</strong>
                                <?php
                                $end = utf8_decode($rsUn->Endereco);
                                $bairro = utf8_decode($rsUn->Bairro);
                                $cidade = utf8_decode($rsUn->Cidade);
                                echo $end .", nº: ". $rsUn->Numero .", ". $bairro ." - ". $cidade ."-". $rsUn->UF;
                                ?>
                                </p></div>
                            <div class="col-xs-2"><p><strong>CEP: </strong><?php echo CEP_Padrao($rsUn->CEP); ?> </div>
                        </div>
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>DADOS CONTRATAÇÃO</u></h4></div>
                        <div class="col-xs-10">
                            <div class="col-xs-3"><p><strong>CTPS:</strong> <?php echo utf8_decode($rs->CTPS);?></p></div>
                            <div class="col-xs-3"><p><strong>Série CTPS:</strong> <?php echo str_pad($rs->sCTPS,4,0, STR_PAD_LEFT);?></p></div>
                            <div class="col-xs-3"><p><strong>UF CTPS:</strong> <?php echo utf8_decode($rs->UFCTPS);?></p></div>
                            <div class="col-xs-3"><p><strong>Data CTPS:</strong> <?php echo Muda_Data($rs->dCTPS);?></p></div>
                            <div class="col-xs-3"><p><strong>Cargo:</strong> <?php echo utf8_decode($rs->Cargo);?></p></div>
                            <div class="col-xs-3"><p><strong>Função:</strong> <?php echo utf8_decode($rs->Funcao);?></p></div>
                            <div class="col-xs-3"><p><strong>Salário:</strong>  <?php echo "R$ ".number_format($rs->Salario,2,',','.'); ?></p></div>
                            <div class="col-xs-3"><p><strong>ASO:</strong>  <?php echo Muda_Data($rs->dASO); ?></p></div>
                            <div class="col-xs-3"><p><strong>Admissão:</strong>  <?php echo Muda_Data($rs->dAdmissao); ?></p></div>
                            <?php if($rs->dDemissao != ""):?>    
                            <div class="col-xs-3"><p><strong>Demissão:</strong>  <?php echo Muda_Data($rs->dDemissao); ?></p></div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div id="Filhos" class="tab-pane fade">
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>DEPENDENTES</u></h4></div>
                        <?php
                            $sql = "SELECT * FROM dependente WHERE Colaborador_idColaborador = ?";
                            $stm = $conexao->prepare($sql);
                            $stm->bindParam(1, $rs->idColaborador);
                            $stm->execute();
                            if($stm->rowCount()>0):
                                while($fRow = $stm->fetch(PDO::FETCH_OBJ)):
                        ?>
                        <div class="col-xs-10 Separa">
                            <div class="col-xs-7"><p><strong>Nome:</strong> <?php echo utf8_decode($fRow->dNome);?></p></div>
                            <div class="col-xs-5"><p><strong>Parentesco:</strong> <?php echo utf8_decode($fRow->Parentesco);?></p></div>
                            <div class="col-xs-6"><p><strong>Nome da Mãe:</strong> <?php echo utf8_decode($fRow->nMae);?></p></div>
                            <div class="col-xs-6"><p><strong>Data de Nascimento:</strong> <?php echo utf8_decode(Muda_Data($fRow->dNascimento));?></p></div>
                            <div class="col-xs-3"><p><strong>Registro:</strong> <?php echo utf8_decode(strtoupper($fRow->Registro));?></p></div>
                            <div class="col-xs-2"><p><strong>Livro:</strong> <?php echo utf8_decode(strtoupper($fRow->Livro));?></p></div>
                            <div class="col-xs-2"><p><strong>Folha:</strong> <?php echo utf8_decode(strtoupper($fRow->Folha));?></p></div>
                            <div class="col-xs-5"><p><strong>DNV:</strong> <?php echo utf8_decode(strtoupper($fRow->DNV));?></p></div>
                            
                            <?php if($fRow->dCPF != "" && $fRow->dCPF != 0):?>
                                <div class="col-xs-4"><p><strong>CPF:</strong> <?php echo utf8_decode(CPF_Padrao(str_pad($fRow->dCPF,11,0, STR_PAD_LEFT)));?></p></div>
                            <?php else: ?>
                                <div class="col-xs-4"><p><strong>CPF:</strong>###.###.###-##</p></div>
                            <?php endif;?>
                        </div>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <div class="col-xs-10 Separa">
                            <p class="text-center">Nenhum Registro</p>
                        </div>
                        <?php
                            endif;
                        ?>
                    </div>
                    <div id="Bancarios" class="tab-pane fade">
                        <?php
                            $sql = "SELECT * FROM conta WHERE Colaborador_idColaborador  =  ?";
                            $stm = $conexao->prepare($sql);
                            $stm->bindParam(1, $rs->idColaborador);
                            $stm->execute();
                            if($stm->rowCount()>0):
                                $conta = $stm->fetchAll(PDO::FETCH_OBJ);
                        ?>
                        <div class="col-xs-10 Separa">
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>DADOS BANCÁRIOS</u></h4></div>

                        <?php
                            foreach($conta as $Valor):
                        ?>
                            <div class="col-xs-3">
                                <p><strong>Banco: </strong><?php echo $Valor->Banco ?></p>
                            </div>
                            <div class="col-xs-3">
                                <p><strong>Conta: </strong><?php echo $Valor->Conta ?></p>
                            </div>
                            <div class="col-xs-3">
                                <p><strong>Agência: </strong><?php echo $Valor->Agencia ?></p>
                            </div>
                            <div class="col-xs-3">
                                <p><strong>Tipo: </strong><?php echo utf8_decode($Valor->Tipo) ?></p>
                            </div>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                                
                        </div>
                    </div>
                    <div id="Endereco" class="tab-pane fade">
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>DADOS DE ENDEREÇO</u></h4></div>
                        <div class="col-xs-10 separa">
                            <div class="col-xs-6"><p><strong>Logradouro: </strong> <?php  echo utf8_decode($rs->Endereco); ?></p></div>
                            <div class="col-xs-3"><p><strong>Nº: </strong> <?php  echo utf8_decode($rs->Numero); ?></p></div>
                            <div class="col-xs-3"><p><strong>Bairro: </strong> <?php  echo utf8_decode($rs->Bairro); ?></p></div>
                            <div class="col-xs-3"><p><strong>Cidade: </strong> <?php  echo utf8_decode($rs->Cidade); ?></p></div>
                            <div class="col-xs-3"><p><strong>UF: </strong> <?php  echo utf8_decode($rs->UF); ?></p></div>
                            <div class="col-xs-3"><p><strong>CEP: </strong> <?php  echo CEP_Padrao($rs->CEP); ?></p></div>
                        </div>
                    </div>
                    <div id="Contato" class="tab-pane fade">
                        <div class="col-xs-offset-3 col-xs-9"><h4><u>DADOS DE CONTATO</u></h4></div>
                        <div class="col-xs-10 separa">
                        <?php if($rs->Celular != "" && $rs->Celular != 0):?>
                            <div class="col-xs-12"><p><strong>Celular:</strong> <?php echo Cel_Padrao($rs->Celular); ?></p></div>
                        <?php else: ?>
                            <div class="col-xs-12"><p><strong>Celular:</strong> (##) #####-####</p></div>
                        <?php endif; ?>
                        <?php if($rs->Telefone != "" && $rs->Telefone != 0):?>
                            <div class="col-xs-12"><p><strong>Telefone:</strong> <?php echo Tel_Padrao($rs->Telefone); ?></p></div>
                        <?php else: ?>
                            <div class="col-xs-12"><p><strong>Telefone:</strong> (##) ####-####</p></div>
                        <?php endif; ?>
                    
                        <?php if($rs->email != ""):?>
                            <div class="col-xs-12"><p><strong>E-mail:</strong> <?php echo $rs->email; ?></p></div>
                        <?php else: ?>
                            <div class="col-xs-12"><p><strong>E-mail:</strong> * Nenhum Registro *</p></div>
                        <?php endif; ?>
                        </div>
                    </div>
                    <div id="Extras" class="tab-pane fade">
                        <?php $rh = array(1, 3, 14, 15, 16, 35); if($rs->dDemissao == null && in_array($_SESSION['idusuarios'], $rh)){?>
                        <div class="col-xs-6 form01">
                            <div class="col-xs-offset-3"><h4>Promoção</h4></div>
                            <form role="form" class="form-horizontal" action="<?php echo BASE."control/banco/ExtrasDAO.php"; ?>" method="post" enctype="multipart/form-data" data-toggle="validator">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="cargo">Cargo:</label>  
                                    <div class="col-sm-10">
                                        <select class="form-control selectpicker" title="Selecione um Cargo." name="cargo" id="cargo" required data-live-search="true" required>
                                            <?php
                                            $sql = 'SELECT * FROM cargo WHERE Unidade_idUnidade = ? AND idCargo <> ?';
                                            $stm = $conexao->prepare($sql);
                                            $stm->bindValue(1, $rs->Unidade_idUnidade);
                                            $stm->bindValue(2, $rs->idCargo);
                                            $stm->execute();
                                            $Cargo = $stm->fetchAll(PDO::FETCH_OBJ);
                                            foreach($Cargo as $ca):
                                            ?>
                                            <option value="<?php echo $ca->idCargo?>" data-subtext="CBO: <?php echo $ca->CBO;?>" data-tokens="<?php echo $ca->Funcao?>"><?php echo $ca->CodCargo."-".strtoupper($ca->Funcao)." - ". "R$ ".number_format($ca->Salario,2,',','.'); ?></option>
                                            <?php
                                            endforeach;
                                            ?>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="data">Data:</label>
                                    <div class="col-sm-4">
                                        <input class="form-control" type="date" required name="data" id="data" />
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="justificativa">Justificativa:</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="justificativa" name="justificativa" maxlength="200" minlength="50" required></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-offset-2">
                                    <input type="hidden" value="Promoção" name="Promover" />
                                    <input type="hidden" value="<?php echo $rs->idColaborador; ?>" name="Colaborador" />
                                    <input type="hidden" value="<?php echo $rs->Contratacao_idContratacao; ?>" name="Contrato" />
                                    <button type="submit" class="btn btn-success">Promover</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-xs-6">
                            <div class="col-xs-offset-3"><h4>Demissão</h4></div>
                            <form role="form" class="form-horizontal" action="<?php echo BASE."control/banco/ExtrasDAO.php"; ?>" method="post" enctype="multipart/form-data" data-toggle="validator">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="data">Data:</label>
                                    <div class="col-sm-4">
                                        <input class="form-control" type="date" required name="data" id="data" />
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="explicacao">Justificativa:</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="explicacao" name="explicacao" maxlength="200" minlength="50" required></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-offset-2">
                                    <input type="hidden" value="Demissão" name="Demitir" />
                                    <input type="hidden" value="<?php echo $rs->idColaborador; ?>" name="Colaborador" />
                                    <input type="hidden" value="<?php echo $rs->Contratacao_idContratacao; ?>" name="Contrato" />
                                    <button type="submit" class="btn btn-warning">Demitir</button>
                                </div>
                            </form>
                        </div>
                        <?php }?>
                        
                        <div class="col-xs-12">
                            <div class="col-xs-offset-3"><h2>Histórico</h2></div>
                            <?php
                            $sql = "SELECT * FROM historico WHERE Colaborador_idColaborador = ?";
                            $stm = $conexao->prepare($sql);
                            $stm->bindValue(1, $rs->idColaborador);
                            $stm->execute();
                            $Hist = $stm->fetchAll(PDO::FETCH_OBJ);
                            //print_r($Hist);
                            foreach($Hist as $hi):
                            ?>
                            <div class="col-xs-12"><p><strong><?php  echo utf8_decode($hi->Historico); ?></strong> <?php  echo utf8_decode(Muda_Data($hi->Data)); ?></p></div>
                            <?php 
                            if($hi->Cargo != null):
                                $sql = "SELECT * FROM cargo WHERE idCargo = ?";
                                $stm = $conexao->prepare($sql);
                                $stm->bindValue(1, $hi->Cargo);
                                $stm->execute();
                                $car = $stm->fetch(PDO::FETCH_OBJ);
                            ?>
                            <div class="col-xs-12"><p><strong>Cargo: </strong> <?php echo utf8_decode($car->CodCargo)."-".utf8_decode($car->Funcao)." <strong>Salário: </strong> R$".number_format($car->Salario,2,',','.');?></p></div>
                            
                            <?php endif;?>
                            <?php if($hi->Justificativa != null): ?>
                            <div class="col-xs-12"><p><strong>Justificativa: </strong> <?php echo utf8_decode($hi->Justificativa);?></p></div>
                            <?php endif; ?>
                            <p>&thinsp;</p>
                            <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            else:
            echo "<script>window.location.href='".BASE."pesquisas/Colaborador';</script>";
            endif;
            ?>    
        </div>
    </div>
</div>
<?php
  endif;
require_once("../control/arquivo/footer/Footer.php");
?>