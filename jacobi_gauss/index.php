<?php
function valorModulo($valor) {
  return $valor > 0 ? $valor : $valor * -1;
}

function eliminacao($SISTEMA, $IGUALDADE, $tam) {
  $m   = 0;
  $IGUALDADE_RESOLVIDA = [];
  $SISTEMA_RESOLVIDO = [];
  $RETORNO = [];

  for($k = 0; $k < $tam; $k++){
      for($i = $k+1; $i <= $tam; $i++){
          $m = $SISTEMA[$i][$k] / $SISTEMA[$k][$k];
          $SISTEMA_RESOLVIDO[$i][$k] = $SISTEMA[$i][$k] = 0;

          for($j = $k+1; $j < $tam; $j++) {
            $SISTEMA_RESOLVIDO[$i][$j] = $SISTEMA[$i][$j] - ($m * $SISTEMA[$k][$j]);
          }
          $IGUALDADE_RESOLVIDA[$i] = $IGUALDADE[$i] - ($m * $IGUALDADE[$k]);
      }
  }

  $RETORNO['sistema']   = $SISTEMA_RESOLVIDO;
  $RETORNO['igualdade'] = $IGUALDADE_RESOLVIDA;

  return $RETORNO;
}

function resolucao($SISTEMA, $IGUALDADE, $RESULTADO, $tam){
  $s;
  $RESULTADO[$tam-1] = $IGUALDADE[$tam-1] / $SISTEMA[$tam-1][$tam-1];

  for($k = $tam; $k >= 0; $k--){
      $s = 0;
      for($j = $k+1; $j < $tam; $j++){
        $s = $s + ($SISTEMA[$k][$j] * $RESULTADO[$j]);
      }

      $RESULTADO[$k] = ($IGUALDADE[$k] - $s) / $SISTEMA[$k][$k];
  }

  return $RESULTADO;
}

function pivoteamento($SISTEMA, $IGUALDADE, $tam){
  $AUX = [];
  $ans = 0;

  for($i = 0; $i <= $tam; $i++) {
    $linha = 0;
    $maior = 0;

    for($j = 0; $j <= $tam; $j++) {
      if($j == 0) {
        $maior = $SISTEMA[$i][$j];
      }

      if(floatval($SISTEMA[$i][$j]) > $maior) {
        $maior = $SISTEMA[$i][$j];
        $linha = $j; 
      }
    }

    $ans = $IGUALDADE[$linha];
    $IGUALDADE[$linha] = $IGUALDADE[$i];
    $IGUALDADE[$i] = $ans;

    for($j = 0; $j <= $tam; $j++) {
      $AUX[$j] = $SISTEMA[$linha][$j];
      $SISTEMA[$linha][$j] = $SISTEMA[$i][$j];
      $SISTEMA[$i][$j] = $AUX[$j];   
    }
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Seidel-Jacobi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="../assets/css/bootstrap.css" />
  </head>
  <body>
    
    <?php if(empty($_POST['ordem']) && empty($_POST['valor'])) { ?>
      <form name="frm_ordem" action="" method="post">
        <div class="container mt-5">
          <div class="card border-secondary">
            <h5 class="card-header">Método de Gauss Jacobi ou Gauss Seidel</h5>
            <div class="card-body">
              
              <div class="row">
                <div class="col-md-6">
                  <h5 class="card-title">Ordem da matriz</h5>
                
                  <div class="form-group">
                    <input type="number" name="ordem" class="form-control" aria-describedby="ordemMatriz" step="1" placeholder="Ex.: 3" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <h5 class="card-title">Precisão</h5>
                
                  <div class="form-group">
                    <input type="number" name="precisao" class="form-control" aria-describedby="precisao" step="0.0001" placeholder="Ex.: 0,0001" required>
                  </div>
                </div>
              </div>
              

            </div>
            <div class="card-footer bg-transparent">
              <button type="submit" class="btn btn-primary" name="continuar">Continuar</button>
            </div>
          </div>
        </div>
      </form>
    <?php
      } elseif(empty($_POST['valor'])) { 
        $ordem = $_POST['ordem'];

        $INPUTS = [];

        $tagInputValor = 
          '<div class="col-sm">
            <div class="input-group mb-3">
              <div class="input-group-append">
                <span class="input-group-text" id="basic-addon1">X<span style="font-size: 10px;">%3$d</span></span>
              </div>
              <input type="number" name="valor[%1$d][%2$d]" class="form-control" placeholder="" aria-label="Valor de X" step="0.01" aria-describedby="basic-addon1" required>
            </div>
          </div>';

        $tagInputResult = 
          '<strong style="font-size:22px">=</strong>
            <div class="col-sm">
              <div class="input-group mb-3">
                <input type="number" name="resultado[%d]" class="form-control" placeholder="" aria-label="Valor de B" step="0.01" aria-describedby="basic-addon1" required>
              </div>
            </div>
        </div><br>';
        
        $tagFormMatriz = 
          '<form name="frm_ordem" action="" method="post">
            <input type="hidden" name="precisao" value="%s">
            <input type="hidden" name="ordem" value="%s">
            <div class="container mt-5">
              <div class="card border-secondary">
                <h5 class="card-header">Método de Gauss Jacobi ou Gauss Seidel</h5>
                <div class="card-body">
                  <h5 class="card-title">Preencha os campos com a matriz</h5>
                  
                  %s

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="jacobi" name="metodo" value="jacobi">
                    <label class="form-check-label" for="jacobi">Gauss Jacobi</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="seidel" name="metodo" value="seidel" checked>
                    <label class="form-check-label" for="seidel">Gauss Seidel</label>
                  </div>

                </div>
                <div class="card-footer bg-transparent">
                  <button type="submit" class="btn btn-primary" name="calcular">Calcular</button>
                </div>
              </div>
            </div>
          </form>';

        for($coluna = 0; $coluna < $ordem; $coluna++) {
          $INPUTS[] = '<div class="row">';

          for($linha = 0; $linha < $ordem; $linha++) {
            $INPUTS[] = sprintf($tagInputValor, $coluna, $linha, $linha+1);
          }
          $INPUTS[] = sprintf($tagInputResult, $coluna);
        }

        printf($tagFormMatriz, $_POST['precisao'], $_POST['ordem'], implode( $INPUTS));
      
      } else {
        // RESOLUÇÃO DO SISTEMA ----------------------
        $SISTEMA   = $_POST['valor'];
        $RESULTADO = $_POST['resultado'];

        $ERRO     = [];
        $precisao = $_POST['precisao'];
        $iteracao = 0;
        $ordem    = $_POST['ordem'];

        // Inicia os valores de X com 0
        for($index = 0; $index < $ordem; $index++) {
          $X[$iteracao][$index] = 0;
        }

        $erroMinimo = $precisao * 2;

        while(empty($ERRO) || $erroMinimo >= $precisao && $iteracao < 1000) {
          $X_MODULO[$iteracao] = [];
          
          for($index = 0; $index < $ordem; $index++) {
            // 1/x
            $div = (1 / $SISTEMA[$index][$index]);
            $equacao = $RESULTADO[$index];

            // (a12x2 - a13x3)
            for($i = 0; $i < $ordem; $i++) {

              if($_POST['metodo'] == 'jacobi') {
                // Gauss-Jacobi ----

                if($iteracao == 0) {
                  $xAtual = 0;
                } else {
                  $xAtual = $X[$iteracao-1][$i];
                }

              } elseif($_POST['metodo'] == 'seidel') {
                // Gauss-Seidel -----

                if(empty($X[$iteracao][$i]) && $iteracao != 0) {
                  $xAtual = $X[$iteracao-1][$i];
                } else {
                  $xAtual = $X[$iteracao][$i];
                }
              }

              if($index != $i) {
                $equacao -= ($SISTEMA[$index][$i] * $xAtual);
              }
            }

            // 1/X11 (Bn - a12x2 - a13x3)
            $X[$iteracao][$index] = $div * $equacao;
            $X_MODULO[$iteracao][$index] = valorModulo($X[$iteracao][$index]);

            // Cálculo do erro
            if($iteracao > 0) {
              $ERRO[$iteracao][$index] = valorModulo($X[$iteracao][$index] - $X[$iteracao-1][$index]);
            }
          }

          $erroMinimo = !empty($ERRO[$iteracao]) ? (max($ERRO[$iteracao]) / max($X_MODULO[$iteracao])) : $precisao * 2;

          if($iteracao > 0) {
            $ERRO[$iteracao]['EM'] = $erroMinimo;
          }
          $iteracao++;
        }

        foreach($X[count($X)-1] as $index => $valX) {
          $X[count($X)-1][$index] = round($valX, 2);
        }

        // Gerando a tabela ---------------------------------------------------------------------------------
        $tagTableResultados = 
          '<table class="table">
            <thead class="thead-light">
              <tr>
                <th scope="col">i</th>
                %s
                %s
                <th scope="col">Err. Médio</th>
              </tr>
            </thead>
            <tbody>
              %s
            </tbody>
          </table>
          <hr>
          <form action="" method="post">
            <div style="width:100%%;margin:30px 0 30px 0" class="text-center">
              <input type="submit" class="btn btn-primary btn-lg" value="Calcular novo sistema">
            </div>
          </form>';

          $tr = 
          '<tr>
            <th scope="row">%d</th>
            %s
          </tr>';

        $THEAD = [];
        $TBODY = [];

        for($i = 1; $i <= $ordem; $i++) {
          $THEAD['x'][] = '<th scope="col"><span class="text-success">X</span><span class="text-success" style="font-size: 10px;">'.$i.'</span></th>';
          $THEAD['e'][] = '<th scope="col"><span class="text-danger">Ʃ</span><span class="text-danger" style="font-size: 10px;">'.$i.'</span></th>';
        }

        foreach($X as $iteracao => $VALORES_ITERACAO) {
          
          
          $TBODY['x'] = [];
          foreach($VALORES_ITERACAO as $valor) {
            $TBODY['x'][] = "<td>$valor</td>";
          }

          for($cont = 0; $cont < $ordem; $cont++) {
            if($iteracao == 0) {
              $TBODY['x'][] = '<td>-</td>';
            } else {
              $TBODY['x'][] = '<td>'.$ERRO[$iteracao][$cont].'</td>';
            }
          }

          if($iteracao != 0) {
            

            $TBODY['x'][] = '<td>'.$ERRO[$iteracao]['EM'].'</td>';
          } else {
            $TBODY['x'][] = '<td>-</td>';
          }

          $TBODY[] = sprintf($tr, $iteracao, implode($TBODY['x']));
          unset($TBODY['x']);
        }

        printf($tagTableResultados, implode($THEAD['x']), implode($THEAD['e']), implode($TBODY));
      }
    ?>

    <script href="../assets/js/bootstrap.min.js"></script>
  </body>
</html>

<?php


// $SISTEMA = [
//   [-9,4,-2],
//   [1,-10,2],
//   [-1,-4,6],
// ];

// $RESULTADO = [-16,6.5,22];

// $SISTEMA = [
//   [-5,2,-1],
//   [2,-6,2],
//   [-2,8,-3],
// ];

// $RESULTADO = [8,-9,12];



