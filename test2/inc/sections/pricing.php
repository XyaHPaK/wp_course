<section id="pricing" class="price_table_bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="section_heading section_heading_2">
					<h2> <?php echo $section['title']; ?> </h2>
				
					<h4> <?php echo $section['subtitle']; ?> </h4>
				</div>

<?php   foreach ($section['pricing_table'] as $key => $option) { ?>
                <div class="col-md-4">	
                    <div class="table-1">
                            <div class="discount">
                                <p> <?php echo $option['discount']; ?> </p>
                            </div>
                        
                        <h3> <?php echo $option['title']; ?> </h3>
                            
                            <div class="price_month">
                                <span class="round">
                                    <h3> <?php echo $option['price']; ?> </h3>
                                    <span>
                                        <p> <?php echo $option['period']; ?> </p>
                                    </span>
                                </span>
                                
                            </div>	
                            
                        <ul>
    <?php   foreach ($option['opportunities'] as $key => $opportunity) { ?>
                            <li> <?php echo $opportunity['opportunity']; ?> </li>
    <?php   } ?>
                        </ul>
                        
                        <div class="section_sub_btn">
    <?php   foreach ($option['button'] as $key => $button) { ?>
                            <button class="btn btn-default" type="submit" style="background-color:<?php echo $button['color']; ?>"> 
                                <i class="fa <?php echo $button['icon']; ?>" aria-hidden="true"></i> <?php echo $button['title']; ?>
                            </button>
    <?php   } ?>	
                        </div>
                    </div>
                </div>
<?php   } ?> 	
			</div>
		</div>
	</div>
</section>