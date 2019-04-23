<section id="portfolio" class="fifth-bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			
				<div class="section_heading">
					<h2> <?php echo $section['title']; ?> </h2>
					
					<h4><?php echo $section['subtitle']; ?>  </h4>
				</div>		
					
					<div class="tags">
						<ul>
<?php   foreach ($section['buttons'] as $key => $button) { ?>
                            <li> <a href="<?php echo $button['url']; ?>"> <?php echo $button['title']; ?> </a> </li>
<?php   } ?> 
						</ul>
					</div>
				
					
					<div class="portfolio_img">
						<div class="port_img1">
							<img src="<?php echo $section['image']; ?>" alt="image">
						</div>
					</div>
				
				
				
				
				
				
				
				
				
								
			</div>
		</div>
	</div>
</section>