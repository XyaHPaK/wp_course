<section class="primary-bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			
			<div class="section_heading">
				<h2> <?php echo $section['title']; ?> </h2>
				
				<h4> <?php echo $section['subtitle']; ?></h4>
			</div>		
				
			<div class="col-md-6">
				<div class="features_detail">
					<ul>
<?php   foreach ($section['tiles'] as $key => $tile) { ?>
                        <li>
                            <i class="fa <?php echo $tile['icon']; ?>" aria-hidden="true"></i> 
                            <h5> <?php echo $tile['title']; ?> </h5>
                            <?php echo $tile['sub_title']; ?>
                        </li>   	
<?php   } ?> 
					</ul>
				</div>
			</div>
			
				<div class="col-md-6">
					<div class="features_img pull-left">
						<img src="<?php echo $section['image']; ?>" alt="image">
					</div>
				</div>
				
			</div>
		</div>
	</div>
</section>