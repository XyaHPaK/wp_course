<section id="home" class="top_banner_bg secondary-bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="top_banner">
				
				</div>
				
				<div class="col-md-6">
					<div class="present">
						<h1> <?php echo $section['title']; ?></h1>
						
						<h5> <?php echo $section['subtitle']; ?> </h5>
						
						<div class="section_btn">
<?php   foreach ($section['buttons'] as $key => $button) { ?>
                            <a href="<?php echo $button['url']; ?>"> 
                                <button class="btn btn-default" type="submit" style="background-color:<?php echo $button['color']; ?>"> 
                                    <i class="fa <?php echo $button['icon']; ?>" aria-hidden="true"></i> <?php echo $button['title']; ?>
                                </button> 
                            </a>	
<?php   } ?> 
						</div>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="present_img">
						<img src="<?php echo $section['image']; ?>" alt="image">
					</div>
				</div>
				
			</div>
		</div>
	</div>
</section>