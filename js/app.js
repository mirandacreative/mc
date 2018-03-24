$(document).foundation();

$(function () { // wait for document ready
	// init
	var controller = new ScrollMagic.Controller({
		globalSceneOptions: {
			triggerHook: 'onLeave'
		}
	});

	// get all slides
	$(".slide").each(function(index, el) {
		var self = $(this);
			self.prevSlide = $(this).prev('.scrollmagic-pin-spacer').find('.slide');
			self.prevPrevSlide = $(this).prevAll('.scrollmagic-pin-spacer').eq(1).find('.slide');
			self.logoColor = "#d4f56e";

		new ScrollMagic.Scene({
			triggerElement: el
		})

		.setPin(el)
		.addTo(controller)
		.on("enter", function (e) {
				self.logoColor = self.prevSlide.css('background-color');
				$('.top-bar svg g path').css('fill', self.logoColor);
		})
		.on("leave", function (e) {
				// force first slide to have "soft avocado" colored logo
				if ( index === 1 ){
					self.logoColor = "#d4f56e";
				} else {
					self.logoColor = $(self.prevPrevSlide).css('background-color');
				}
				$('.top-bar svg g path').css('fill', self.logoColor);
		})

	});

	// select the logo
	var logo = $('.top-bar svg');

	// // create scene for every slide
	// for (var i=0; i<slides.length; i++) {

	// 	var bgColor = '#d4f56e';

	// 	new ScrollMagic.Scene({
	// 			triggerElement: slides[i]
	// 		})
	// 		.setPin(slides[i])
	// 		//.addIndicators() // add indicators (requires plugin)
	// 		.addTo(controller)
	// 		.on("enter", function (e) {
	// 			var currentSlide = e.target.triggerElement();
	// 			bgColor = getComputedStyle(currentSlide).backgroundColor;
	// 			console.log(bgColor);
	// 		});
	// }


});