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
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
        </div>
    </div>
</div>
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>