<section id="testimonial" class="testimonial_bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="section_heading section_heading_2">
					<h2> <?php echo $section['title']; ?> </h2>
				
					<h4> <?php echo $section['subtitle']; ?> </h4>
				</div>

<?php   foreach ($section['testimonial'] as $key => $testi) { ?>
                <div class="testimonial_slide">
                    <div class="testi_detail">
                        <div class="testi_img">
                            <img src="<?php echo $testi['photo']; ?>" alt="image">
                            
                                <h5> <?php echo $testi['name']; ?> </h5>
                                <p> <?php echo $testi['position']; ?> </p>
                        </div>
                        
                        <div class="testi-text">
                            <p> <?php echo $testi['text']; ?> </p>
                        </div>
                    </div> 
                </div>        
<?php   } ?>			
			</div>
		</div>
	</div>
</section>