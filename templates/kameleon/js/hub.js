/**
 * @package     hubzero-cms
 * @file        templates/kameleon/js/hub.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (typeof console === "undefined" || typeof console.log === "undefined") {
	console = {};
	console.log = function() {};
}

jQuery(document).ready(function($){
	var w = 760, 
		h = 520;

	// Set focus on username field for login form
	if ($('#username').length > 0) {
		$('#username').focus();
	}

	$(window)
		.on('scroll', function (e) {
			// what the y position of the scroll is
			var y = $(window).scrollTop();
			// whether that's below the form
			if (y >= 1) {
				// if so, add the fixed class
				$('#masthead').addClass('fixed');
			} else {
				// otherwise remove it
				$('#masthead').removeClass('fixed');
			}
		});

	// Turn links with specific classes into popups
	$('a').on('click', function (e) {
		var trigger = $(this);

		if (trigger.is('.demo, .popinfo, .popup, .breeze')) {
			e.preventDefault();

			if (trigger.attr('class')) {
				var sizeString = trigger.attr('class').split(' ').pop();
				if (sizeString && sizeString.match(/\d+x\d+/)) {
					var sizeTokens = sizeString.split('x');
					w = parseInt(sizeTokens[0]);
					h = parseInt(sizeTokens[1]);
				}
				else if (sizeString && sizeString == 'fullxfull')
				{
					w = screen.width;
					h = screen.height;
				}
			}

			window.open(trigger.attr('href'), 'popup', 'resizable=1,scrollbars=1,height='+ h + ',width=' + w);
		}

		if (trigger.attr('rel') 
		 && trigger.attr('rel').indexOf('external') !=- 1) {
			trigger.attr('target', '_blank');
		}
	});

	// Set the overlay trigger for launch tool links
	/*$('.launchtool').on('click', function(e) {
		$.fancybox({
			closeBtn: false, 
			href: HUB.Base.templatepath + 'images/anim/circling-ball-loading.gif'
		});
	});*/

	// Set overlays for lightboxed elements
	$('a[rel=lightbox]').fancybox();

	// Init tooltips
	$('.hasTip, .tooltips').tooltip({
		position: {
			my: 'center bottom',
			at: 'center top'
		},
		create: function(event, ui) {
			var tip = $(this),
				tipText = tip.attr('title');

			if (tipText.indexOf('::') != -1) {
				var parts = tipText.split('::');
				tip.attr('title', parts[1]);
			}
		},
		tooltipClass: 'tooltip'
	});

	//test for placeholder support
	var test = document.createElement('input'),
		placeholder_supported = ('placeholder' in test);

	//if we dont have placeholder support mimic it with focus and blur events
	if (!placeholder_supported) {
		$('input[type=text]:not(.no-legacy-placeholder-support)').each(function(i, el) {
			var placeholderText = $(el).attr('placeholder');

			//make sure we have placeholder text
			if (placeholderText != '' && placeholderText != null) {
				//add plceholder text and class
				if ($(el).val() == '') {
					$(el).addClass('placeholder-support').val(placeholderText);
				}

				//attach event listeners to input
				$(el)
					.on('focus', function() {
						if ($(el).val() == placeholderText) {
							$(el).removeClass('placeholder-support').val('');
						}
					})
					.on('blur', function(){
						if ($(el).val() == '') {
							$(el).addClass('placeholder-support').val(placeholderText);
						}
					});
			}
		});

		$('form').on('submit', function(event){
			$('.placeholder-support').each(function (i, el) {
				$(this).val('');
			});
		});
	}
});

