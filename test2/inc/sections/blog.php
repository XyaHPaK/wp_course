<section id="blog" class="primary-bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			
				<div class="section_heading">
					<h2> <?php echo $section['title']; ?> </h2>
					
					<h4> <?php echo $section['subtitle']; ?> </h4>
				</div>

<?php   foreach ($section['blog'] as $key => $blog) { ?>
                <div class="col-md-4">
					<article class="our_blog">
						<div class="blog_image">
							<img src="<?php echo $blog['image']; ?>" alt="image">
						</div>
						
						<div class="blog_detail">
							<div class="category_heading">
								<a href="<?php echo $blog['url']; ?>"> <h6> <?php echo $blog['title']; ?> </h6> </a>
								<a href="<?php echo $blog['url']; ?>"> <h5> <?php echo $blog['subtitle']; ?> </h5> </a>
								
								<ul>
									<li> <i class="fa fa-clock-o" aria-hidden="true"></i> 20 March 2016 </li>
									<li> <a href="<?php echo $blog['comments_url']; ?>"> <i class="fa fa-comments-o" aria-hidden="true"></i> Comments </a> </li>
								</ul>
								
								<a href="<?php echo $blog['url']; ?>" class="read_more"> <p> Read More <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </p> </a>
							</div>
						</div>
					</article>					
				</div>      
<?php   } ?> 									
			</div>
		</div>
	</div>
</section>