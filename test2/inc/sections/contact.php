<section id="contact" class="contact_bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="section_heading section_heading_2">
					<h2> <?php echo $section['title']; ?> </h2>
				
					<h4> <?php echo $section['subtitle']; ?> </h4>
				</div>
							
				<div class="col-md-6">
					<div class="contact_form">
                        <?php echo do_shortcode ('[contact-form-7 id="'.$section['contact_form'].'" title="contact form"]'); ?>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="contact_text">
						<ul>
<?php   foreach ($section['contact_us_info'] as $key => $info) { ?>
                            <li>
								<span><i class="fa <?php echo $info['icon']; ?>" aria-hidden="true"></i></span> 
								<h5> <?php echo $info['title']; ?></h5>
							</li> 
<?php   } ?>
						</ul>
						
					</div>
				</div>
				
				
			</div>
		</div>
	</div>
</section>