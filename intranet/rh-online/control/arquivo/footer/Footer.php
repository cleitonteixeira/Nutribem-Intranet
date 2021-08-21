		<hr />
		<div class="legenda">
			<div class="col-xs-12 col-sm-12 col-md-12 text-center"><strong>Legenda: </strong></div>
			<div class="col-xs-12 col-sm-12 col-md-12 text-center"> </div>
<?php
	$sql = "SELECT u.idUnidade, c.Nome FROM unidade u INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro";
	$stm = $conexao->prepare($sql);
	$stm->execute();
	$un = "";
	$x = 0;
	$z = 6;
	$w = 0;
	$cont = $stm->rowCount();
	$cCont = 0;
	$y = (int)ceil($cont/$z);
	$v = $cont%$z ;
	while($rs = $stm->fetch(PDO::FETCH_OBJ)):
		if($x == 0):
			$w++;
?>
			<div class="col-xs-12 col-sm-12 col-md-12 text-center"><p>
<?php
		endif;
		echo "<strong>".str_pad($rs->idUnidade,2,0, STR_PAD_LEFT).".xx</strong> - ". utf8_decode($rs->Nome);
		$x++;
		if($x < $z):
		    if($cCont != $cont){
			    echo " | ";
		    }
		endif;
		if($x == $z):
			$x = 0;
	?>
				</p>
			</div>
<?php endif;
        $cCont += 1;
	endwhile; ?>
			</div>
</body>
</html>