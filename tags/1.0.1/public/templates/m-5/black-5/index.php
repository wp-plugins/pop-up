<?php
/**
 *
 * id: black-5
 * base: m-5 
 * title: Black
 * 
 */

?>


<div class="cc-pu-bg m-5 black-5"></div>
<article class="pop-up-cc m-5 black-5">
	<div class="modal-inner">
		<a class="cc-pu-close" data-modalId="<?php echo $id; ?>"> <i class="fa fa-times"></i> </a> 
		
		<?php $content = $template_options['contents']; ?>
		<div class="cc-pu-header-section"> 
			<h2><?php echo $content['header'];?></h2>
		</div>
		
		<div class="cc-pu-subheader-section"> 
			<h3><?php echo $content['subheader'];?></h3>
		</div>
		
		<div class="cc-pu-content-section <?php echo get_the_ID();?>"> 
			<?php echo $content['content'];?>
		</div>
		
		<?php if(get_post_meta($id,'_cc_pop_up_newsletter',true) != 'no'):?>
			
			<?php if(!is_admin()):?>
			<form action="#" class="cc-pu-newsletter-form">
			<?php else:?>
			<div class="cc-pu-newsletter-form">
			<?php endif;?>
			
				<div class="cc-pu-form-group cc-pu-smart-form"> 
					<div class="cc-pu-thank-you"><p><?php  _e('<strong>Thank you!</strong>'); ?></p></div>
					<div class="cc-pu-error-message"><p><strong>WRONG EMAIL FORMAT!</strong></p></div>
					<i class="fa fa-envelope"></i>
					<?php $input = $template_options['input']; ?>
					<input type="email" class="cc-pu-form-control" placeholder="<?php echo $input['text'];?>">
					<input type="hidden" name="_ajax_nonce" id="_ajax_nonce" value="<?php echo wp_create_nonce("cc-pu-newsletter-subscribe"); ?>" data-popup="<?php echo $id; ?>">
					<?php $button = $template_options['button']; ?>
					<button type="submit" class="cc-pu-btn"><?php echo $button['text'];?></button>
				</div>
				
			<?php if(!is_admin()):?>
			</form>
			<?php else:?>
			</div>
			<?php endif;?>	
		
		<?php endif;?>
		<footer class="cc-pu-privacy-info"> <a href="<?php echo $content['privacy_link'];?>">Privacy policy</a>
			<div class="cc-pu-privacy-section"> 
				<p><?php echo $content['privacy_message'];?></p>
			</div>
		</footer>
	</div>
</article>
