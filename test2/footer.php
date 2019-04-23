<footer class="third-bg" style="background-image: url('<?php echo get_theme_mod('footer_social_backgroundcolor'); ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<div class="footer_top">
					<h4> <?php echo get_theme_mod('header_social'); ?>  </h4>
					
					<ul>
              <?php if(get_theme_mod('facebook_social') != ''): ?>
              <li> <a href="<?php echo get_theme_mod('facebook_social'); ?>"> <i class="fa fa-facebook" aria-hidden="true"></i> </a> </li>
              <?php endif; ?>
              <?php if(get_theme_mod('twitter_social') != ''): ?>
              <li> <a href="<?php echo get_theme_mod('twitter_social'); ?>"> <i class="fa fa-twitter" aria-hidden="true"></i> </a> </li>
              <?php endif; ?>
              <?php if(get_theme_mod('linkedin_social') != ''): ?>
              <li> <a href="<?php echo get_theme_mod('linkedin_social'); ?>"> <i class="fa fa-linkedin" aria-hidden="true"></i> </a> </li>
              <?php endif; ?>
              <?php if(get_theme_mod('googleplus_social') != ''): ?>
              <li> <a href="<?php echo get_theme_mod('googleplus_social'); ?>"> <i class="fa fa-google-plus" aria-hidden="true"></i> </a> </li>
              <?php endif; ?>
              <?php if(get_theme_mod('youtube_social') != ''): ?>
              <li> <a href="<?php echo get_theme_mod('youtube_social'); ?>"> <i class="fa fa-youtube-square" aria-hidden="true"></i> </a> </li>
              <?php endif; ?>
              <?php if(get_theme_mod('instagram_social') != ''): ?>
              <li> <a href="<?php echo get_theme_mod('instagram_social'); ?>"> <i class="fa fa-instagram" aria-hidden="true"></i> </a> </li>
              <?php endif; ?>
					</ul>
				</div>
				
				
				
				
			</div>
		</div>
	</div>
	
  <div class="footer_bottom fourth-bg" style="background-image: url('<?php echo get_theme_mod('footer_copy_backgroundcolor'); ?>')"> 
    <?php echo get_theme_mod('footer_copy'); ?> 
    <a href="#" class="backtop"> ^ </a>
  </div>
  
				
</footer>


</body>
    <?php wp_footer(); ?>
</html>

