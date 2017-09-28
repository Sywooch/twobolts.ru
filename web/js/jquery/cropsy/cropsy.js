/* cropsy.js by Adam Krebs
*
* Available under terms of the MIT license (see LICENSE in project root)
*/

(function($) {

	$.fn.cropsy = function(options) {
		var settings = {
			mask_padding: 40,
		}
	
		if ( typeof(options) == 'object' ) 
		{
			$.extend( settings, options );
		}
	
		var $image = $(this),
			$loading = $('.loading-indicator'),
			$viewport = $image.parent(),
			$container = $viewport.parent()
			$overlay = $('#cropper-overlay'),
			smallWidth = 0,
			smallHeight = 0;
		
		$viewport.css({
			height: "+=" + settings.mask_padding * 2,
			width: "+=" + settings.mask_padding * 2
		});

		$overlay.css({'left': settings.mask_padding, 'top': settings.mask_padding, 'position': 'absolute'});

		$image.hide();
	
		// attach on image load
		$image.load(function() {
	
			var originalWidth = $image.width(),
				originalHeight = $image.height();
							
			centerImage();
			
			$.extend($image, {
				originalWidth: $image.width(),
				originalHeight: $image.height()
			});
	
			var onStartDragPosition;
			
			$overlay.draggable({
				start: function(event, ui) {
					onStartDragPosition = $image.position();
				},
				drag: function(event, ui) {
					$image.offset({
						'top': onStartDragPosition.top + ui.offset.top,
						'left': onStartDragPosition.left + ui.offset.left
					});
				},
				stop: function(event, ui) {
					$overlay.css({'left': settings.mask_padding, 'top': settings.mask_padding});
					stickImage();
				}
			});
			
			var diff = smallWidth * 100 / originalWidth;
			
			var $zoom_widget = $('.zoom-slider')
				.width($viewport.width() - 20)
				.slider({
					value: diff,
					min: diff,
					max: 100,
					slide: function(event, ui) {
						var imgOffset = $image.offset(),
							diffWidth = originalWidth / smallWidth,
							diffHeight = originalHeight / smallHeight,
							
							centerX = Math.round(imgOffset.left + $image.width() / 2),
							centerY = Math.round(imgOffset.top + $image.height() / 2);
							
						var newHeight = Math.round($image.originalHeight * (ui.value / 100) * diffHeight),
                			newWidth  = Math.round($image.originalWidth * (ui.value / 100) * diffWidth);
							
						newHeight = (newHeight % 2) ? newHeight += 1 : newHeight;
						newWidth  = (newWidth % 2) ? newWidth += 1 : newWidth;
						
						$image.height(newHeight);
						$image.width(newWidth);
						
						$image.offset({
							top: Math.round(centerY - newHeight / 2),
							left: Math.round(centerX - newWidth / 2)
						});
					},
					stop: function(event, ui) {
						stickImage();
					}
				});
	
			// remove loader and show image
			$loading.hide();
			$loading.remove();
			$image.fadeIn();
		});
	
		var centerImage = function() {
	
			var image_width   = $image.width(),
				image_height  = $image.height(),
				actual_ratio  = image_width / image_height,
				mask_width    = $viewport.width(),
				mask_height   = $viewport.height(),
				crop_width    = $viewport.width() - 2 * settings.mask_padding,
				crop_height   = $viewport.height() - 2 * settings.mask_padding;
	
			if (image_width > image_height) 
			{
				image_height = crop_height;
				$image.height(image_height);
				image_width = $image.width();
			} 
			else 
			{
				image_width = crop_width;
				$image.width(image_width);
				image_height = $image.height();
			}
	
			if (image_width < crop_width)
			{
				image_width = crop_width;
				$image.width(image_width);
				image_height = image_width / actual_ratio;
				$image.height(image_height);
			}
			else if (image_height < crop_height)
			{
				image_height = crop_height;
				$image.height(image_height);
				image_width = image_height / actual_ratio;
				$image.width(image_width);
			}
			
			smallWidth = image_width;
			smallHeight = image_height;
			
			$image.offset({
				top: mask_height / 2 - image_height / 2,
				left: mask_width / 2 - image_width / 2
			});
		}
	
		var stickImage = function()
		{
			var imageTop = parseFloat($image.css('top')),
				imageLeft = parseFloat($image.css('left')),
				viewportWidth = $viewport.width() - settings.mask_padding,
				viewportHeight = $viewport.height() - settings.mask_padding;
	
			if (imageLeft > settings.mask_padding) imageLeft = settings.mask_padding;
	
			if (imageLeft + $image.width() < viewportWidth) imageLeft = viewportWidth - $image.width();
	
			if (imageTop > settings.mask_padding) imageTop = settings.mask_padding;
	
			if (imageTop + $image.height() < viewportHeight) imageTop = viewportHeight - $image.height();
	
			$image.css({
				'top': imageTop,
				'left': imageLeft
			});
		}
	}
})(jQuery)
