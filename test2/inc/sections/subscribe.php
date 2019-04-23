<section class="primary-bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<div class="col-md-4">
					<div class="subscribe">
						<h3> <?php echo $section['title']; ?> </h3>
						
						<h6> <?php echo $section['subtitle']; ?> </h6>

						<div class="subscribe_form">
							<?php echo do_shortcode ('[contact-form-7 id="'.$section['contact_form'].'" title="subscribe form"]'); ?>
						</div>
			
					</div>	
				</div>
				
				<div class="col-md-4">
								<div class="workng_img">
									<img src="<?php echo $section['image']; ?>" alt="image">
								</div>
				</div>
				
				<div class="col-md-4">
					<div class="subscribe">
						<h3> <?php echo $section['title2']; ?> </h3>
						
						<h6> <?php echo $section['subtitle2']; ?> </h6>

						<div class="section_btn">
<?php   foreach ($section['buttons'] as $key => $button) { ?>
							<button class="btn btn-default" type="submit" style="background-color:<?php echo $button['color']; ?>"> 
								<i class="fa <?php echo $button['icon']; ?>" aria-hidden="true"></i> 
								<?php echo $button['title']; ?> 
							</button>      
<?php   } ?>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</section>