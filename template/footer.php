    
	<footer id="footer" class="py-5 px-5">
    	<!--<div class="container">-->
    		<div class="row">
    			<div class="col-lg-8">
					<div class="row">
				<div class="col-md-2">
				<a href="index.php"><img src="http://logos.bireme.org/img/<?php echo $lang; ?>/bvs_color.svg" alt="" class="img-fluid imgBlack" style="max-width:120px" ></a>
</div>
				<div class="col-md-10 text-justify" >

				<?=_e('A BVS é um produto colaborativo, coordenado pela BIREME/OPAS/OMS. Como biblioteca, oferece acesso abrangente à informação científica e técnica em saúde. A BVS coleta, indexa e armazena citações de documentos publicados por diversas organizações. A inclusão de qualquer artigo, documento ou citação na coleção da BVS não implica endosso ou concordância da BIREME/OPAS/OMS com o seu conteúdo.', 'cc');?>
				</div>
				</div>

    			</div>
    			<div class="col-lg-4 text-center" id="logoOPAS">
    				<img src="https://logos.bireme.org/img/<?php echo $lang ?>/v_bir_white.svg" alt="" class="imgBlack" style="max-width:100%">
    			</div>
    		</div>
			<hr class="text-white">
			<div class="row">
			<div class="col-md-12 text-center">


					<a href ="https://www.paho.org/<?php echo $lang ?>/bireme"  target="_blank">
					<img style="max-width:110px" src="https://bvsalud.org/wp-content/themes/portal-regional/img/powered.png">
					</a><br><br>
					<small>
    					<a href="https://politicas.bireme.org/terminos/<?php echo $lang ?>" target="_blank"><?php _e('Terms and conditions of use', 'cc'); ?></a> |
    					<a href="https://politicas.bireme.org/privacidad/<?php echo $lang ?>" target="_blank"><?php _e('Privacy Policy', 'cc'); ?></a>
    				</small>
</div>
</div>
    	<!--</div>-->
    </footer>
	<?php wp_footer() ?>
</body>
</html>
