<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>App Layers</title>
    
	<link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,300,700' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
     
    <?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>


<header class="navbar-fixed-top">
	<div class="container">
    	<div class="row">
        	<div class="header_top">
        		<div class="col-md-2">
            		<div class="logo_img">
						<a href="#"><?php the_custom_logo(); ?></a>
					</div>
				</div>
					
				<div class="col-md-10">
					<div class="menu_bar">	
						<nav role="navigation" class="navbar navbar-default">
							<div class="navbar-header">
                                <button id="menu_slide"  aria-controls="navbar" aria-expanded="false" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                  </button>
								 </div>
<?php 
								 wp_nav_menu( [
                                    'theme_location'  => 'primary', 
                                    'container'       => 'div', 
                                    'container_class' => 'collapse navbar-collapse', 
                                    'container_id'    => 'navbar',
                                    'menu_class'      => 'nav navbar-nav', 
                                    'menu_id'         => '',
                                    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                    'depth'           => 2,
                                ] );
?>
							   
							  <!-- <div class="collapse navbar-collapse" id="navbar">
                            
									<ul class="nav navbar-nav">
									  <li><a href="#home" class="js-target-scroll">Home</a></li>
									  <li><a href="#services" class="js-target-scroll">Services</a></li>
									  <li><a href="#portfolio" class="js-target-scroll">Portfolio</a></li>
									  <li><a href="#pricing" class="js-target-scroll">Pricing</a></li>
									  <li><a href="#team" class="js-target-scroll">Team</a></li>
									  <li><a href="#testimonial" class="js-target-scroll">Testimonial</a></li>
									  <li><a href="#blog" class="js-target-scroll">Blog</a></li>
									  <li><a href="#contact" class="js-target-scroll">Contact</a></li>
									  <li  class="dropdown"><a href="#"  class="dropdown-toggle" data-toggle="dropdown"> page  </a>
									 	 <ul class="dropdown-menu"> 
											<li><a href="#">List  Width</a></li>
											<li><a href="#">List  Sidebar</a></li>
											<li><a href="#">List  Sidebar</a></li>
											<li><a href="#">List Sidebar</a></li>
										 </ul>
									  </li>
									</ul>      
                                </div> -->
							  
							 
       			
						</nav>
					</div>
    	        </div>
			  
			  </div>
			</div>
		</div>
</header>