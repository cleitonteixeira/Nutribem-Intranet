<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
endif;
?>
<!-- Navbar -->
<script>
$(document).ready(function(){
$('.dropdown-submenu a.test').on("click", function(e){
$(this).next('ul').toggle();
e.stopPropagation();
e.preventDefault();
});
});
</script>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><img src="<?php echo BASE; ?>img/Brand.png"></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo BASE; ?>">Home</a></li>
                <?php
                $chefia = array(1,4,5,36,37,39,42,44);
                if(in_array($_SESSION['idusuarios'],$chefia)):
                ?>
                <li class="dropdown">              
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Clientes <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo BASE?>cadastros/Clientes.php">
                            <i class="fa fa-plus-circle"></i>
                            Cadastrar</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE; ?>pesquisas/Clientes.php"><i class="fa fa-search"></i>
                            Pesquisar</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE; ?>clientes/Movimentacao.php"><i class="fa fa-plus-circle" aria-hidden="true"></i>
                            Historico</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-submenu">
                            <a href="#" class="test" tabindex="-1"  data-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="fas fa-suitcase"></i> Propostas <i class="fas fa-angle-right"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li><a href="<?php echo BASE; ?>clientes/Proposta.php"><i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    Cadastrar</a></li>
                                <li class="divider"></li>
                                <li><a href="<?php echo BASE; ?>clientes/Status.php"><i class="fa fa-suitcase" aria-hidden="true"></i>
                                    Situação</a></li>
                            </ul>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE; ?>clientes/Terceiros.php"><i class="fa fa-suitcase" aria-hidden="true"></i>
                            Contratos Terceiros</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE; ?>clientes/CadastroFechamento.php"><i class="fa fa-plus-circle" aria-hidden="true"></i>
                            Períodos de Fechamento</a></li>
                    </ul>
                </li>
                <?php endif;
                $chefia = array(1,4,5,36,37,39,42,44);
                if(in_array($_SESSION['idusuarios'],$chefia)):
                ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Contratos <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo BASE?>clientes/Reajuste.php">
                            <i class="fa fa-suitcase" aria-hidden="true"></i>
                            Reajustar</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>clientes/EditarContrato.php">
                            <i class="far fa-plus-square" aria-hidden="true"></i>
                            Adicionar Eventos</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>clientes/ModificarContrato.php">
                            <i class="fas fa-edit"></i>
                            Editar Contrato</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>clientes/AllContratos.php">
                            <i class="fas fa-list-ul"></i>
                            Todos Contratos</a></li>
                    </ul>
                </li>
                <?php endif;?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Lançamento <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo BASE?>lancamento/Diario.php">
                            <i class="far fa-plus-square"></i>
                            Diário</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>lancamento/Alteracao.php">
                            <i class="fas fa-edit"></i>
                            Alteração</a>
                        </li>
                        <?php
                        $chefia = array(1,44);
                        if(in_array($_SESSION['idusuarios'],$chefia)){
                        ?>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>lancamento/Historico.php">
                            <i class="fas fa-history"></i>
                            Histórico de Alterações</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>lancamento/PrazoLancamento.php">
                            <i class="fas fa-history"></i>
                            Prazo Lançamento</a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Medição <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo BASE?>medicao/Parcial.php">
                            <i class="far fa-file-pdf"></i>
                            Parcial</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>medicao/Medicao.php">
                            <i class="far fa-file-pdf"></i>
                            Gerar Medição</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>medicao/HMedicao.php">
                            <i class="far fa-file-pdf"></i>
                            Medições Ativas</a></li>
                        <?php
                        $chefia = array(1,4,5,36,37,39,42,44);
                        if(in_array($_SESSION['idusuarios'],$chefia)){
                        ?>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>medicao/MedicoesAprovadas.php">
                            <i class="far fa-file-pdf"></i>
                            Medições Aprovadas</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>medicao/MedicoesFinalizadas.php">
                            <i class="far fa-file-pdf"></i>
                            Medições Finalizadas</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
                $chefia = array(1,4,5,36,37,39,42,44);
                if(in_array($_SESSION['idusuarios'],$chefia)){
                ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Cadastros <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="dropdown-submenu">
                            <a href="#" class="test" tabindex="-1"  data-toggle="dropdown" role="button" aria-expanded="false">
                                <i class="fas fa-suitcase"></i> Empresa <i class="fas fa-angle-right"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li><a href="<?php echo BASE; ?>cadastros/Empresa.php"><i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    Cadastrar</a>
                                </li>
                            </ul>
                        </li>
                        <li class="divider"></li>
                        <li class="dropdown-submenu">
                            <a href="#" class="test" tabindex="-1"  data-toggle="dropdown" role="button" aria-expanded="false">
                                <i class="fas fa-suitcase"></i> Grupo <i class="fas fa-angle-right"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li><a href="<?php echo BASE; ?>cadastros/Grupo.php"><i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    Cadastrar</a>
                                </li>
                            </ul>
                        </li>
                        <li class="divider"></li>
                        <li class="dropdown-submenu">
                            <a href="#" class="test" tabindex="-1"  data-toggle="dropdown" role="button" aria-expanded="false">
                                <i class="fas fa-suitcase"></i> Unidade de Fornecimento <i class="fas fa-angle-right"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li><a href="<?php echo BASE; ?>cadastros/UnidadeFornecimento.php"><i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    Cadastrar</a>
                                </li>
                            </ul>
                        </li>
                        <li class="divider"></li>
                        <li class="dropdown-submenu">
                            <a href="#" class="test" tabindex="-1"  data-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="fas fa-suitcase"></i> Unidade Faturamento<i class="fas fa-angle-right"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li><a href="<?php echo BASE; ?>cadastros/Unidade.php"><i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    Cadastrar</a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="<?php echo BASE; ?>cadastros/PesquisarUnidade.php"><i class="fas fa-search"></i>
                                    Pesquisar</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php }?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Relatórios <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <?php 
                        if(in_array($_SESSION['idusuarios'],$chefia)){
                        ?>
                        <li><a href="<?php echo BASE?>relatorio/Lancamentos.php">
                            <i class="fas fa-info"></i>
                            Ultimos Lançamentos</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>relatorio/AuditoriaMedicoes.php">
                            <i class="fas fa-info"></i>
                            Auditoria Lançamentos</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>relatorio/Consumo.php">
                            <i class="fas fa-info"></i>
                            Consumo</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>relatorio/ConsumoCliente.php">
                            <i class="fas fa-info"></i>
                            Consumo Cliente/Unidade</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>relatorio/ConsumoClienteDia.php">
                            <i class="fas fa-info"></i>
                            Consumo Cliente/Unidade Dia</a></li>
                            <li class="divider"></li>
                        <li><a href="<?php echo BASE?>relatorio/ConsumoGeral.php">
                            <i class="fas fa-info"></i>
                            Consumo Cliente Geral</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>relatorio/ValorPraticado.php">
                            <i class="fas fa-info"></i>
                            Valor Praticado</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo BASE?>relatorio/ClientesAtivos.php">
                            <i class="fas fa-info"></i>
                            Clientes Ativos</a></li>
                        <li class="divider"></li>
                        <?php }?>
                        <li><a href="<?php echo BASE?>relatorio/Fornecimento.php">
                            <i class="fas fa-info"></i>
                            Fornecimento</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a>Bem vindo, <?php echo utf8_decode($_SESSION['Nome']); ?></a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-cog"></i>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="<?php echo BASE; ?>control/acesso/NewPassword.php">
                                <i class="fa fa-lock"></i>
                                Alterar Senha</a></li>
                        <li class="divider"></li>
                        <li>
                            <a href="http://www.nutribemrefeicoescoletivas.com.br/intranet/">
                                <i class="fa fa-question"></i>
                                Alterar Contexto</a></li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo BASE; ?>control/acesso/Sair.php">
                                <i class="fa fa-power-off"></i>
                                Sair
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>