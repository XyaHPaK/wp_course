<section id="team" class="primary-bg" style="background-image: url('<?php echo $section['background_image']; ?>')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			
				<div class="section_heading">
					<h2> <?php echo $section['title']; ?> </h2>
					
					<h4> <?php echo $section['subtitle']; ?> </h4>
				</div>

<?php   foreach ($section['team_members'] as $key => $member) { ?>
                <div class="col-md-3">
					<div class="member_detail">
						<div class="member_img">
							<img src="<?php echo $member['photo']; ?>" alt="image">
						</div>
						<div class="member_name">
							<h5> <?php echo $member['member_name']; ?> </h5>
							<p> <?php echo $member['position']; ?> </p>
						</div>
					</div>
				</div>          
<?php   } ?>		
								
			</div>
		</div>
	</div>
</section>