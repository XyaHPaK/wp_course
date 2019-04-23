<section id="services" class="padding_bottom_none our_service_bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="section_heading section_heading_2">
					<h2> <?php echo $section['title']; ?> </h2>
				
					<h4> <?php echo $section['subtitle']; ?> </h4>
				</div>
				
				<div class="col-md-5 pull-right">
					<div class="services_detail">
						<ul>
<?php   foreach ($section['services'] as $key => $service) { ?>
                            <li>
                                <a href="<?php echo $service['url']; ?>">
                                    <span><i class="fa <?php echo $service['icon']; ?>" aria-hidden="true"></i></span> 
                                    <h5> <?php echo $service['title']; ?> </h5>
                                </a>
								<p> <?php echo $service['subtitle']; ?></p>
							</li>
<?php   } ?> 
							<!-- <li>
								<a href="#"><span><i class="fa fa-html5" aria-hidden="true"></i></span> 
								<h5> HTML5 & CSS3 </h5></a>
								<p> Contrary to popular belief, Lorem Ipsum is not simply random text.</p>
							</li>
							
							<li>
								<a href="#"><span><i class="fa fa-desktop" aria-hidden="true"></i></span> 
								<h5> Fully Reponsive </h5></a>
								<p> Contrary to popular belief, Lorem Ipsum is not simply random text.</p>
							</li>
							
							<li>
								<a href="#"><span><i class="fa fa-rocket" aria-hidden="true"></i></span> 
								<h5> Unlimited Support </h5></a>
								<p> Contrary to popular belief, Lorem Ipsum is not simply random text.</p>
							</li> -->
						</ul>
						
					</div>
				</div>
				
				<div class="col-md-7">
					<div class="services_img">
						<img src="<?php echo $section['image']; ?>" alt="image-1">
					</div>
						
					<div class="services_img_n">
						<img src="<?php echo $section['image2']; ?>" alt="image-1">
					</div>
				</div>
				
			</div>
		</div>
	</div>
</section>