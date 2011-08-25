<?php
defined('_JEXEC') or die('Restricted access');

if ( $this->show_gallery )
{
	?>
<div class="reset"></div>
<div class="product_gallery">
	<div id="product_gallery_header" class="tienda_header">
		<span><?php echo JText::_( "Images" ); ?> </span>
	</div>
	<?php
	foreach ( $this->images as $image )
	{
		?>
	<div class="product_gallery_thumb" id="product_gallery_thumb">
	<?php 
		echo TiendaUrl::popup( $this->uri . $image, '<img src="' . $this->uri . "thumbs/" . $image . '" alt="' . $this->product_name . '" />', array( 'update' => false, 'img' => true ) ); ?>
	</div>
	<?php
	}
	?>
	<div class="reset"></div>
</div>
	<?php
}