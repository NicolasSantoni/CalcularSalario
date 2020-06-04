<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Calcular Salário</title>
    <link rel="stylesheet" href="./css/style.css">
  </head>
  <body>
    <header>
      <div class="titulo">
        <img src="./imagens/cimol.png" alt="cimol" id="img1">
        <center><h1 id="h1">CALCULAR SALÁRIO LÍQUIDO</h1></center>
        <img src="./imagens/infologo.png" alt="cimol" id="img2">
      </div>
    </header>
    <main>
      <?php
        $c=0;
        $e=0;
      ?>
      <form class="calculo" action="" method="post">
        <p>Indique seu nome:</p>
        <input type="text" name="nome" id="nome" pattern="^(?![ ])(?!.*[ ]{2})((?:e|da|do|das|dos|de|d'|D'|la|las|el|los)\s*?|(?:[A-Z][^\s]*\s*?)(?!.*[ ]$))+$"/>
        <p>Indique seu salário bruto:</p>
        <input type="text" name="bruto" id="bruto">
        <p>Indique o número de dependentes:</p>
        <input type="number" name="dep" id="dep">
        <button type="submit" name="button" onclick="calcular()">Calcular</button>
      </form>
      <?php
        error_reporting(0);
        ini_set(“display_errors”, 0 );
        if ($_POST['nome']!=NULL) {
          $nome = $_POST['nome'];
          $bruto = $_POST['bruto'];
          $dep = $_POST['dep'];
          $dep=189.59*$dep;
          //INSS
          if ($bruto<1045.01) {
            $retiraINSS=($bruto*7.5)/100;
          }
          else if ($bruto>1045.00){
            $etapa=$bruto-1045.00;
            $retiraINSS=(1045.00*7.5)/100;
            if ($etapa<1044.61) {//2089.61-1044.60
              $retiraINSS=$retiraINSS+(($etapa*9)/100);
            }
            else {
              $etapa=$etapa-1044.61;
              $retiraINSS=$retiraINSS+((1044.61*9)/100);
              if ($etapa<1044.81) {//3134.41-2089.60
                $retiraINSS=$retiraINSS+(($etapa*12)/100);
              }
              else {
                $etapa=$etapa-1044.81;
                $retiraINSS=$retiraINSS+((1044.81*12)/100);
                if ($etapa<=2966.67) {//6101.07-3134.40
                  $retiraINSS=$retiraINSS+(($etapa*14)/100);
                }
              }
            }
          }
          $brutoINSS=$bruto-$retiraINSS;
          $brutoF=$brutoINSS-$dep;
          //IRRF
          if ($brutoF<1903.99) {
            $retiraIRRF=0;
          }
          else if ($brutoF>1903.99){
            $etapaF=$brutoF-1903.99;
            $retiraIRRF=0;
            if ($etapaF<922.68) {//2826.66-1903.98
              $retiraIRRF=$retiraIRRF+(($etapaF*7.5)/100);
            }
            else {
              $etapaF=$etapaF-922.68;
              $retiraIRRF=$retiraIRRF+((922.68*7.5)/100);
              if ($etapaF<924.41) {//3751.06-2826.65
                $retiraIRRF=$retiraIRRF+(($etapaF*15)/100);
              }
              else {
                $etapaF=$etapaF-924.41;
                $retiraIRRF=$retiraIRRF+((924.41*12)/100);
                if ($etapaF<=913.64) {//4664.69-3751.05
                  $retiraIRRF=$retiraIRRF+(($etapaF*22.5)/100);
                }
                else {
                  $etapaF=$etapaF-913.64;
                  $retiraIRRF=$retiraIRRF+((913.64*22.5)/100);
                  if ($etapaF>0) {//4664.69-3751.05
                    $retiraIRRF=$retiraIRRF+(($etapaF*27.5)/100);
                  }
                }
              }
            }
          }
          $liquido=$brutoF-$retiraIRRF;
          $string="$nome|$bruto|$retiraINSS|$brutoF|$retiraIRRF|$dep|$liquido\n";
          //ARQUIVO
          $arquivo = fopen('arquivo.txt','a+');
          fwrite($arquivo, $string);
          fclose($arquivo);
          //PEGAR INFORMAÇÕES
          $arquivo = fopen('arquivo.txt','r');

          while(true) {
	           $valores[$e]=fgets($arquivo);
             $valoresT = explode('|',$valores[$e]);
             $nomeT[$c]=$valoresT[0];
             $brutoT[$c]=$valoresT[1];
             $INSS[$c]=$valoresT[2];
             $baseIRRF[$c]=$valoresT[3];
             $IRRF[$c]=$valoresT[4];
             $depT[$c]=$valoresT[5];
             $liqT[$c]=$valoresT[6];
             $brutoT[$c]=number_format($brutoT[$c], 2, '.', '');
             $INSS[$c]=number_format($INSS[$c], 2, '.', '');
             $baseIRRF[$c]=number_format($baseIRRF[$c], 2, '.', '');
             $IRRF[$c]=number_format($IRRF[$c], 2, '.', '');
             $depT[$c]=number_format($depT[$c], 2, '.', '');
             $liqT[$c]=number_format($liqT[$c], 2, '.', '');
	           if ($valores[$e]==null) break;
             $e++;
             $c++;
          }
          fclose($arquivo);
        }
      ?>
      <table class="tab">
      <tr>
        <th class="tit">NOME</th>
        <th class="tit">SALÁRIO BRUTO</th>
        <th class="tit">DESCONTO INSS</th>
        <th class="tit">BASE IRRF*</th>
        <th class="tit">DESCONTO IRRF</th>
        <th class="tit">ADICIONAL DEPENDENTES</th>
        <th class="tit">SALÁRIO LÍQUIDO</th>
      </tr>
      <?php
        if ($_POST['nome']!=NULL) {
          for ($i=0; $i < $c; $i++) {
            echo "<tr><th>$nomeT[$i]</td><td>R$$brutoT[$i]</td><td>$INSS[$i]</td><td>R$$baseIRRF[$i]</td><td>R$$IRRF[$i]</td><td>$depT[$i]</td><td>R$$liqT[$i]</td><tr>";
          }
          $c=$c+1;
        }
      ?>
    </table>
    </main>
    <footer>
      <div class="esq">
        <p>TABELA INSS</p>
        <table class="tabF">
          <tr>
            <th class="tit">SALÁRIO DE CONTRIBUIÇÃO</th>
            <th class="tit">ALÍQUOTA PARA O RECOLHIMENTO AO INSS</th>
          </tr>
          <tr>
            <td>Até R$ 1.045,00</td>
            <td>7,50%</td>
          </tr>
          <tr>
            <td>De R$ 1.045,01 até R$ 2.089,60</td>
            <td>9%</td>
          </tr>
          <tr>
            <td>De R$ 2.089,61 até R$ 3.134,40</td>
            <td>12%</td>
          </tr>
          <tr>
            <td>De R$ 3.134,41 até R$ 6.101,06</td>
            <td>14%</td>
          </tr>
        </table>
      </div>
      <div class="dir">
        <p>TABELA IRRF</p>
        <table class="tabG">
          <tr>
            <th class="tit">SALÁRIO BRUTO</th>
            <th class="tit">ALÍQUOTA PARA O RECOLHIMENTO AO IRRF</th>
          </tr>
          <tr>
            <td>Até 1.903,98</td>
            <td>-</td>
          </tr>
          <tr>
            <td>De 1.903,99 até 2.826,65</td>
            <td>7,5%</td>
          </tr>
          <tr>
            <td>De 2.826,66 até 3.751,05</td>
            <td>15%</td>
          </tr>
          <tr>
            <td>De 3.751,06 até 4.664,68</td>
            <td>22,5%</td>
          </tr>
          <tr>
            <td>Acima de 4.664,68</td>
            <td>27,5%</td>
          </tr>
        </table>
      </div>
      <p id="p">O valor da cota por dependente é de R$ 189,59.</p>
      <p id="p">*Valor com descontos do INSS e dependentes incluso.</p>
    </footer>
  </body>
</html>
