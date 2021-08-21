<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
require_once("control/arquivo/funcao/Outras.php");
require_once("control/arquivo/header/Header.php");
require_once("control/banco/conexao.php");
require_once("control/arquivo/Login.php");
else:
require_once("control/banco/conexao.php");
require_once("control/arquivo/funcao/Outras.php");
require_once("control/arquivo/header/Header.php");
?>
<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<div class="text-center">
				<div class="row">
					<h1>Selecione o Sistema</h1>
				</div>
				<div class="row text-center">
                    <?php
                    $conexao = conexao::getInstance();
                    $sql = "SELECT * FROM servicosdisponiveis WHERE Usuario_idUsuarios = ?";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindParam(1, $_SESSION['idusuarios']);
                    $stmt->execute();
                    $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $x = 0;
                    foreach($rs as $res){
                    ?>
                    <a href="<?php echo BASE.$res->Link; ?>" class="btn <?php echo $x%2==0 ? 'btn-success':'btn-primary';?> btn-lg conteudo"><?php echo utf8_decode($res->Servico); ?></a>
                    &thinsp;&thinsp;&thinsp;&thinsp;&thinsp;
                    <?php
                        $x +=1;
                    }
                    ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
endif;
?>