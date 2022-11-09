window.addEventListener('scroll', function() {
	if (window.scrollY > 0) {
		document.querySelector('.header-sticky').classList.add('sticky');
	} else {
		document.querySelector('.header-sticky').classList.remove('sticky');
	}
})

/*
$(window).on('scroll', function () {
	if ($(this).scrollTop() > 300) {
		$('.header-sticky').addClass('sticky');
	} else {
		$('.header-sticky').removeClass('sticky');
	}
});
*/
