<?php
if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['idusuarios'])):
        session_destroy();
        header("Location: ".BASE);
    endif;
?>
<!-- Navbar -->
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
                <li class="dropdown">              
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Colaborador <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <?php if($_SESSION['Acesso'] != 2 and $_SESSION['Acesso'] != 3):?>
						<li><a href="<?php echo BASE?>cadastros/Colaborador.php">
							<i class="fa fa-plus-circle"></i>
							Cadastrar</a></li>
						<!--<li class="divider"></li>
                        <li><a href="<?php echo BASE; ?>colaborador/Transferencia.php"><i class="fa fa-exchange" aria-hidden="true"></i>
                            Transferir</a></li>-->
                        <li class="divider"></li>
                         <?php endif;?>
                        <li><a href="<?php echo BASE; ?>pesquisas/Colaborador.php"><i class="fa fa-search"></i>
                            Pesquisar</a></li>
						<?php if ($_SESSION['Acesso'] == 3 or $_SESSION['idusuarios'] == 1 ): ?>
						<li class="divider"></li>
                        <li><a href="<?php echo BASE; ?>seguranca/Asos.php"><i class="fa fa-search"></i>
                            Atualizar ASO</a></li>
						<li class="divider"></li>
                        <li><a href="<?php echo BASE; ?>seguranca/FiltroUnidade.php"><i class="fa fa-search"></i>
                            ASO/Unidade</a></li>
						<?php endif; ?>
                    </ul>
                </li>
                <?php if($_SESSION['Acesso'] != 2 and $_SESSION['Acesso'] != 3):?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            Empresa <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dropdown-header">Empresa</li>
                            <li><a href="<?php echo BASE?>cadastros/Empresa.php">
                                <i class="fa fa-plus-circle"></i>
                                Cadastrar</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header">Unidade</li>
                            <li><a href="<?php echo BASE?>cadastros/Unidade.php">
                                <i class="fa fa-plus-circle"></i>
                                Cadastrar</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            Cargo <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo BASE?>cadastros/Cargo.php">
                                <i class="fa fa-plus-circle"></i>
                                Cadastrar</a></li>
                            <?php if($_SESSION['Acesso'] != 2 and $_SESSION['Acesso'] != 3):?>
                            <li class="divider"></li>
                            <li><a href="<?php echo BASE?>cadastros/AtualizaCargo.php">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                                Atualizar</a></li>
                         <?php endif;?>
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Relatórios <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <?php if($_SESSION['Acesso'] != 2 and $_SESSION['Acesso'] != 3):?>
                        <li class="dropdown-header">Cargos</li>
                        <li><a href="<?php echo BASE?>relatorio/Cargos.php">
                            <i class="fa fa-file-pdf-o"></i>
                            Cargos</a></li>
                        <li class="divider"></li>
                        <?php endif;?>
                        <li class="dropdown-header">Funcionários</li>
                        <li><a href="<?php echo BASE?>relatorio/FichaFuncionario.php">
                            <i class="fa fa-file-pdf-o"></i>
                            Ficha Funcionário</a></li>
                        <li><a href="<?php echo BASE?>relatorio/ColaboradorUnidade.php">
                            <i class="fa fa-file-pdf-o"></i>
                            Funcionário/Unidade</a></li>
                    </ul>
                </li>
                <?php if($_SESSION['Acesso'] != 3){ ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Formulários <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo BASE?>formularios/Files.php">
                            <i class="fa fa-file-pdf-o"></i>
                            Arquivos</a></li>
                        <li class="divider"></li>
						<li class="dropdown-header">Férias</li>
                        <li><a href="<?php echo BASE?>formularios/Ferias.php">
                            <i class="fa fa-calendar"></i>
                           Solicitar</a></li>
                        <!--<li class="divider"></li>
						<li class="dropdown-header">Admissão</li>
                        <li><a href="<?php echo BASE?>formularios/Admissao.php">
                            <i class="fa fa-user"></i>
                           Solicitar</a></li>-->
						<li class="divider"></li>
						<li class="dropdown-header">Demissão</li>
                        <li><a href="<?php echo BASE?>formularios/Demissao.php">
                            <i class="fa fa-user"></i>
                           Solicitar</a></li>
						<li class="divider"></li>
						<li class="dropdown-header">Promoção</li>
                        <li><a href="<?php echo BASE?>formularios/Promocao.php">
                            <i class="fa fa-user"></i>
                           Solicitar</a></li>
                            <li class="divider"></li>
                        <li><a href="<?php echo BASE?>formularios/Pendencias.php">
                            <i class="fa fa-question"></i>
                           Pendências</a></li>
                            <li class="divider"></li>
                        <li><a href="<?php echo BASE?>formularios/HPendencias.php">
                            <i class="fa fa-question"></i>
                           Histórico de Pendências</a></li>
                          <li class="divider"></li>
                            <li><a href="<?php echo BASE?>formularios/Arquivo.php">
                            <i class="fa fa-history"></i>
                            Arquivo de Pendências</a></li>
                    </ul> 
                </li>
                
                <?php
                }
                if($_SESSION['Acesso'] == 0 ):?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            Usuários <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo BASE?>cadastros/Usuario.php">
                                <i class="fa fa-plus-circle"></i>
                                Cadastrar</a>
                            </li>
                        </ul>
                    </li>
                <?php endif;?>
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