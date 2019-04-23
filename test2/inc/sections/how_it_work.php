<section class="primary-bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			
				<div class="section_heading">
					<h2> <?php echo $section['title']; ?> </h2>
					
					<h4> <?php echo $section['subtitle']; ?></h4>
				</div>		
				
				<div class="col-md-4">
<?php   foreach ($section['how_it_work_left'] as $key => $description) { ?>
                    <div class="how_it_work_m text-right">
						<a href="<?php echo $description['url']; ?>"> <i class="fa <?php echo $description['icon']; ?>" aria-hidden="true"></i> </a>
						<a href="<?php echo $description['url']; ?>"> <h5> <?php echo $description['title']; ?> </h5> </a>
						<p> <?php echo $description['subtitle']; ?></p>
					</div>	
<?php   } ?> 
				</div>
				
				<div class="col-md-4">
								<div class="workng_img">
									<img src="<?php echo $section['image']; ?>" alt="image">
								</div>
				</div>
				
				<div class="col-md-4">
<?php   foreach ($section['how_it_work_right'] as $key => $description) { ?>
                    <div class="how_it_work_m text-left">
                        <a href="<?php echo $description['url']; ?>"> <i class="fa <?php echo $description['icon']; ?>" aria-hidden="true"></i> </a>
                        <a href="<?php echo $description['url']; ?>"> <h5> <?php echo $description['title']; ?> </h5> </a>
                        <p> <?php echo $description['subtitle']; ?></p>
                    </div>	
<?php   } ?> 
				</div>

				
			</div>
		</div>
	</div>
</section>