<?php
if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['idusuarios'])):
        session_destroy();
        header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
    endif;
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
                    <?php if( $_SESSION['idusuarios'] == 51 || $_SESSION['idusuarios'] == 1 || $_SESSION['idusuarios'] == 27 ){ ?>
                    <li class="dropdown">              
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            Cadastros <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo BASE?>cadastros/Arquivo.php"><i class="fa fa-plus-circle"></i>
                                Arquivo</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo BASE; ?>cadastros/Categoria.php"><i class="fa fa-plus-circle"></i>
                                Categoria</a></li>
                        </ul>
                    </li>
                    <?php } ?>
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