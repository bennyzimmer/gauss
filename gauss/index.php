<?php
  // FUNCOES ------------------------
  function eliminacao() {
    global $SISTEMA, $IGUALDADE, $tam;
    $m   = 0;

    for($k = 0; $k < $tam; $k++){
        for($i = $k+1; $i <= $tam; $i++){
            $m = $SISTEMA[$i][$k] / $SISTEMA[$k][$k];
            $SISTEMA[$i][$k] = 0;

            for($j = $k+1; $j < $tam; $j++) {
              $SISTEMA[$i][$j] = $SISTEMA[$i][$j] - ($m * $SISTEMA[$k][$j]);
            }
            $IGUALDADE[$i] = $IGUALDADE[$i] - ($m * $IGUALDADE[$k]);
        }
    }
  }

  function resolucao(){
    global $SISTEMA, $IGUALDADE, $RESULTADO, $tam;
    $s;
    $RESULTADO[$tam-1] = $IGUALDADE[$tam-1] / $SISTEMA[$tam-1][$tam-1];
    for($k = $tam; $k >= 0; $k--){
        $s = 0;
        for($j = $k+1; $j < $tam; $j++){
          $s = $s + ($SISTEMA[$k][$j] * $RESULTADO[$j]);
        }
  
        $RESULTADO[$k] = ($IGUALDADE[$k] - $s) / $SISTEMA[$k][$k];
    }
  }
  
  function pivoteamento(){
    global $SISTEMA, $IGUALDADE, $tam;
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

      if($i != $linha) {
        $tagLinhaTrocada = 
          '<div class="alert alert-danger" role="alert">
          Troca linhas: <b>L%s <-> L%s</b>
          </div>';

        $linhaTrocaInicio = $linha+1;
        $linhaTrocaFim = $i+1;
        
        printf($tagLinhaTrocada, $linhaTrocaInicio, $linhaTrocaFim);
        impirme_matriz('troca');
      }
    }
  }

  function impirme_matriz($operacao = 'info') {
    $tipo = [
      'inicio' => 'info',
      'final' => 'success',
      'troca' => 'warning'
    ];

    $tagMatriz = 
      '<div class="alert alert-%s" style="width:300px" role="alert">
          %s
       </div>';

    $valores = '';

    global $IGUALDADE, $SISTEMA;
    for($coluna = 0; $coluna < count($IGUALDADE); $coluna++) {
      for($linha = 0; $linha < count($IGUALDADE); $linha++) {
        $valor = $SISTEMA[$coluna][$linha];
        $valores .= '<b class="text-center" style="display: inline-block; width:40px;">'.$valor.'</b> | ';
      }
      $valores .= '<br>';
    }

    printf($tagMatriz, $tipo[$operacao], $valores);
  }

  function imp($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre><hr>';
  }

  function imprime_solucao() {
    global $RESULTADO;

    printf(
      '<div class="alert alert-success" role="alert">
        <b>S = {%s}</b>
      </div>'
      , implode(', ', $RESULTADO)
    );
  }
?>

<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Método de Jacobi</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" type="text/css" media="screen" href="../assets/css/bootstrap.css" />
  </head>
  <body class="p-5">
    <div class="container-fluid">
      <div class="card w-100">
        <div class="card-body">
          <h5 class="card-title">Implementação de Gauss com pivoteamento</h5>
          <?php 
            $ordem = !empty($_POST['ordem']) ? $_POST['ordem'] : null;

            if (empty($ordem)) {
                echo '
                <form action="" method="post">
                  <div class="form-group">
                    <label for="inputOrdem">Ordem do sistema</label>
                    <input type="number" name="ordem" class="form-control" id="inputOrdem" aria-describedby="ordemsistema" placeholder="Ex.: 3">
                  </div>
                  <input type="submit" name="proximo" class="btn btn-primary" value="Próximo">
                </form>';

            } else {

              $tagCampo = 
                '<div class="col-sm">
                  <input type="number" class="form-control " name="sistema[%d][%d]" placeholder="x%d">
                </div>';

              echo '<form action="" method="post">';

              for($i = 0; $i < $ordem; $i++) {

                echo 
                  '<div class="row">';

                for($j = 0; $j < $ordem; $j++) {
                  printf($tagCampo, $i, $j, $j+1);
                }
                
                echo
                    '<label><b style="font-size:21px">=</b></label>
                      <div class="col-sm">
                      <input type="number" class="form-control" name="igual['.$i.']" placeholder="Resultado">
                    </div>
                  </div>';
              }

              echo 
                '<div class="row" style="padding-top: 10px">
                    <div class="col-sm">
                      <input type="submit" name="calcular" class="btn btn-primary" value="Calcular">
                    </div>
                  </div>
                </form>';

            }
          ?>
      </div>
    </div>
    <br>

    <?php
      $RESULTADO = [];

      if(!empty($_POST['sistema'])) {

        $SISTEMA   = $_POST['sistema'];
        $IGUALDADE = $_POST['igual'];

        $tam = count($IGUALDADE)-1;

        impirme_matriz('inicio');    
        pivoteamento();
        eliminacao();
        resolucao();

        ksort($RESULTADO);
        imprime_solucao();
      }
    ?>

    </div>
    <script href="../assets/js/bootstrap.min.js"></script>
  </body>
</html>