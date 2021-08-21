<?php
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1","pt_BR.utf-8", "portuguese");
date_default_timezone_set('America/Sao_Paulo');
function CPF_Padrao($dado){
  $cpf_array = str_split($dado);
  $cont = sizeof($cpf_array);
  $x = 0;
  $cpf = "";
  while($x < $cont){
    $cpf .= $cpf_array[$x];
    if($x == 2){
      $cpf .= '.'; 
    }elseif($x == 5){
      $cpf .= '.';
    }elseif($x == 8){
      $cpf .= '-';
    }
    $x+=1;
  }
  return($cpf);
}
function PIS_Padrao($dado){
  $pis_array = str_split($dado);
  $cont = sizeof($pis_array);
  $x = 0;
  $pis = "";
  while($x < $cont){
    $pis .= $pis_array[$x];
    if($x == 2){
      $pis .= '.'; 
    }elseif($x == 7){
      $pis .= '.';
    }elseif($x == 9){
      $pis .= '-';
    }
    $x+=1;
  }
  return($pis);
}
function CEP_Padrao($dCEP){
  $cep_array = str_split(strrev($dCEP));
  $cont = sizeof($cep_array);
  $x = 0;
  $cep = "";
  while($x < $cont){
    $cep .= $cep_array[$x];
    if($x == 2){
      $cep .= '-'; 
    }elseif($x == 5){
      $cep .= '.';
    }
    $x+=1;
  }
  return(strrev($cep));
}

function Tel_Padrao($dados){
    $tel_array = str_split($dados);
    $contador = sizeof($tel_array);
    $x = 0;
    $Telefone = "(";
    while($x<=$contador){
        $Telefone .= $tel_array[$x];
        if($x == 1){
            $Telefone .= ") ";
        }
        if($x == 5){
            $Telefone .= "-";
        }
        $x += 1;
        if($x == $contador){
            break;
        }
    }
    return $Telefone;
}
function Cel_Padrao($dados){
    $tel_array = str_split($dados);
    $contador = sizeof($tel_array);
    $x = 0;
    $Telefone = "(";
    while($x<=$contador){
        $Telefone .= $tel_array[$x];
        if($x == 1){
            $Telefone .= ") ";
        }
        if($x == 6){
            $Telefone .= "-";
        }
        $x += 1;
        if($x == $contador){
            break;
        }
    }
    return $Telefone;
}
   
function CNPJ_Padrao($dados){
    $cnpj_array = str_split($dados);
    $contador = sizeof($cnpj_array);
    $x = 0;
    $CNPJ = "";
    while($x<=$contador){
        $CNPJ .= $cnpj_array[$x];
        if($x == 1){
            $CNPJ .= ".";
        }elseif($x == 4){
            $CNPJ .= ".";
        }elseif($x == 7){
            $CNPJ .= "/";
        }elseif($x == 11){
            $CNPJ .= "-";
        }
        $x += 1;
        if($x == $contador){
            break;
        }
    }
    return $CNPJ;
}

function Muda_Data($data){
    echo date('d/m/Y',strtotime($data));
}
?>