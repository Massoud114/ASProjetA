window.addEventListener('scroll', function() {
	if (window.scrollY > 0) {
		document.querySelector('.header-sticky').classList.add('sticky');
	} else {
		document.querySelector('.header-sticky').classList.remove('sticky');
	}
})
