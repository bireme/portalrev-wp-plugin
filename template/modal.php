<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$resource->title;?></title>
    <style>
        /* Estilos básicos para o modal */
        body {font-family: Arial, sans-serif;}

        /* O modal (fica escondido por padrão) */
        .modal {
            display: none; overflow:hidden;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Fundo escuro com transparência */
        }

        /* Conteúdo do modal */
        .modal-content {
            overflow-y: auto;
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            max-height: 400px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        }

        /* Botão de fechar */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Botão para abrir o modal -->
    

    <!-- Inicio do modal -->
    <div id="myModal" class="modal">

        <!-- Conteúdo do Modal -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <p><?=$resource->title;?></p>
            <?php
            if ($resource->collection){
               // echo '<table>';
                       // echo '<tr>';
                     //   echo '  <td valign="top">Coleções no Catálogo Coletivo SeCS: </td>';


  //echo '  Coleções no Catálogo Coletivo SeCS: ';
                      //  echo '    <td>';
                      echo '<div class="row" style="font-size:14px;">';
                      $n=0;
                        foreach ( $resource->collection as $value ){
                            ?>
                            <div class="col-md-3">
                            <B>
                                <?=_e('Código do centro','cc');?><br>
                                <?=_e('Nome do centro','cc');?><br>
                                <?=_e('Coleções','cc');?></b>

                            </div>
                            <div class="col-md-9"><?php
    $link = explode('<br />', nl2br($value));
    foreach ( $link  as $meriope ){

    if ($n % 4 == 0){
        echo "<a href='https://bvsalud.org/centros?lang=pt&q=".$link[$n]."'>";                        
                            echo nl2br($meriope) . '</a>';
    }else{
                            echo nl2br($meriope) . '';
                            //$type = str_replace('\n','dgdgygyGONGO<br>',n12br($type));
                      }        
                      $n++;
                      //echo $type . '<br/>';
                      //echo 'n' . $n;
                    }
                        }echo '</div>';
                     //   echo '   </td>';
                       // echo '</tr>';
                       // echo '</table>';
                    }?>
                     </div>
        </div>

    </div>

    <script>
        // Obtém o modal
        var modal = document.getElementById("myModal");

        // Obtém o botão que abre o modal
        var btn = document.getElementById("openModalBtn");

        // Obtém o elemento <span> que fecha o modal
        var span = document.getElementsByClassName("close")[0];

        // Quando o usuário clicar no botão, abre o modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Quando o usuário clicar em <span> (x), fecha o modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Quando o usuário clicar fora do modal, fecha o modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>