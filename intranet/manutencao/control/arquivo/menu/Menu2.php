<?php
if (!isset($_SESSION)) session_start();
unset($_FILES);
unset($_SESSION['equipamento']);
unset($_SESSION['cont']);
?>
<!-- Navbar -->
<div class="col-xs-12 col-md-12 col-lg-12">
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
                            Equipamento <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dropdown-header">Equipamento</li>
                            <li><a href="<?php echo BASE?>equipamento/Cadastrar.php"><i class="fa fa-plus-circle"></i>
                                Cadastrar</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo BASE; ?>equipamento/Transferir.php"><i class="fa fa-exchange-alt"></i>
                                Tranferir</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo BASE; ?>equipamento/Listar.php"><i class="fa fa-search"></i>
                                Listar</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header">Categoria</li>
                            <li><a href="<?php echo BASE?>equipamento/Categoria.php"><i class="fa fa-plus-circle"></i>
                                Cadastrar</a></li>
                        </ul>
                    </li>
                    <?php if($_SESSION['idusuarios'] == 6 || $_SESSION['idusuarios'] == 1){?>
                    <li class="dropdown">              
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            Unidade <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo BASE?>unidade/Cadastrar.php"><i class="fa fa-plus-circle"></i>
                                Cadastrar</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo BASE; ?>unidade/Listar.php"><i class="fa fa-search"></i>
                                Pesquisar Unidade</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo BASE; ?>unidade/AcessoUnidade.php"><i class="fas fa-edit"></i>
                                Acesso Unidade</a></li>
                        </ul>
                    </li>
                    <?php } ?>
                    <li class="dropdown">              
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            OS <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo BASE?>ordemservico/NovaOS.php"><i class="fa fa-plus-circle"></i>
                                Abrir</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo BASE; ?>ordemservico/ListaOS.php"><i class="fa fa-search"></i>
                                Verificar OS</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo BASE; ?>ordemservico/ArquivoOS.php"><i class="fa fa-file-archive"></i>
                                Arquivo</a></li>
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
</div>