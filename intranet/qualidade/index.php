<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("control/Pacote.php");
    $conexao = conexao::getInstance();
    $troca   = array(" ", ":");
    
    
    function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
            $arBytes = array(
                0 => array(
                    "UNIT" => "TB",
                    "VALUE" => pow(1024, 4)
                ),
                1 => array(
                    "UNIT" => "GB",
                    "VALUE" => pow(1024, 3)
                ),
                2 => array(
                    "UNIT" => "MB",
                    "VALUE" => pow(1024, 2)
                ),
                3 => array(
                    "UNIT" => "KB",
                    "VALUE" => 1024
                ),
                4 => array(
                    "UNIT" => "B",
                    "VALUE" => 1
                ),
            );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

?>
<!-- Content -->
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip(); 
});
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo text-center">
            <?php
            $sql = "SELECT * FROM categoria;";
            $stmt = $conexao->prepare($sql);
            $stmt->execute();
            $idCat = array();
            $y = 1;
            $co = $stmt->rowCount();
            while($r = $stmt->fetch(PDO::FETCH_OBJ)){
                array_push($idCat, $r->idCategoria);
                $x = rand(0,4);
                switch($x){
                    case(0):
                        $class  = "btn-info";
                        break;
                    case(1):
                        $class = "btn-warning";
                        break;
                    case(2):
                        $class = "btn-success";
                        break;
                    case(3):
                        $class = "btn-primary";
                        break;
                    case(4):
                        $class = "btn-danger";
                        break;
                }
                if($y == 1){
            ?>
            
            <ul class="nav nav-pills center-pills">
                <?php } ?>
                <li class="<?=$r->idCategoria == 1 ? "active" : ''; ?>"><a data-toggle="pill" href="#cat<?=$r->idCategoria;?>"><strong><?=utf8_decode($r->Nome);?></strong></a></li>
            <?php
                if($y % 5 == 0){
            ?>
            <div class="col-md-12 col-xs-12 col-lg-12"> </div>
            <?php
                }
                $y+=1;
            }
            ?>
            </ul>
            <h5 class="text-center">Selecione uma categoria acima</h5>
		</div>
        <div class="col-md-12 col-xs-12 col-lg-12 text-center conteudo">
            <hr />
            <div class="tab-content">
            <?php
            foreach($idCat as $id){

                $sql = "SELECT a.*, u.Nome as Responsavel, c.idCategoria, c.Nome as Categoria FROM arquivoqualidade a INNER JOIN categoria c ON c.idCategoria = a.Categoria_idCategoria INNER JOIN usuarios u ON u.idusuarios = a.Usuario_idUsuario WHERE c.idCategoria = ? ORDER BY a.Nome";
                $stm1 = $conexao->prepare($sql);
                $stm1->bindParam(1, $id);
                $stm1->execute();
                $contador = $stm1->rowCount();
                //$rs = $stm1->fetchAll(PDO::FETCH_OBJ);
                ?>
                <div class="text-left tab-pane fade <?=$id == 1 ? "in active" : ''; ?>" id="cat<?=$id;?>"  >
                <?php
                if( $contador > 0 ){
                    $res = $stm1->fetch(PDO::FETCH_OBJ);
            ?>
                    <h4 class="text-left"><strong><u><?=utf8_decode($res->Categoria);?></u></strong></h4>
            <?php
                    
                    $sql = "SELECT a.*, u.Nome as Responsavel, c.idCategoria, c.Nome as Categoria FROM arquivoqualidade a INNER JOIN categoria c ON c.idCategoria = a.Categoria_idCategoria INNER JOIN usuarios u ON u.idusuarios = a.Usuario_idUsuario WHERE c.idCategoria = ? ORDER BY a.Nome;";  
                    $stm = $conexao->prepare($sql);   
                    $stm->bindParam(1, $id);
                    $stm->execute();   
                    $dados = $stm->fetchAll(PDO::FETCH_OBJ);   
            ?>
                    <div class="col-md-12 col-xs-12 col-lg-12 col-sm-12 conteudo">
                        
            <?php
                    $num = 1;
                    foreach( $dados as $rss ){
                        $nome = strrev(utf8_decode($rss->Nome));
                        $nome   = strrchr($nome, '.');
                        $nome = strrev(str_replace(".","",$nome));
                        $arq = "documentos/".str_replace($troca,"_", $rss->DataHora)."/".$rss->Nome;
            ?>
                        <p>
                            <a download data-toggle="tooltip" data-placement="bottom" title="Disponibilizado em: <?=date("d/m/Y", strtotime($rss->DataHora));?>  Enviado por:<?=' '.utf8_decode($rss->Responsavel);?>" href="documentos/<?=str_replace($troca,"_", $rss->DataHora)."/".$rss->Nome;?>">
                                <span class="lista-arquivos"><?=str_pad($num,3,0, STR_PAD_LEFT) ." - ".$rss->Nome;?></span>
                                 <i class="fa fa-download"></i>
                            </a>
            				<?php
            				if($_SESSION['idusuarios'] == 1 || $_SESSION['idusuarios'] == 51 ){
                            ?>
                            <button type="button" class="btn btn-danger"><i class="fa fa-trash"></i></button>
            				<?php
                            }
                            ?>
                        </p>
            <?php
                        $num +=1;
                    }
            ?>
                        
                    </div>
            <?php
                }else{
            ?>
                    <div class="col-xs-12 col-md-12 col-lg-12 col-sm-12 conteudo">
                        <p>Sem dados para exibir!</p>
                    </div>
            <?php
                }
            ?>
                </div>  
            <?php
            }
            ?>
            </div>
        </div>
	</div>
</div>
<?php
}
?>