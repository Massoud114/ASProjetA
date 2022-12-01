let scrollUp = document.querySelector('.scroll-to-top')
let lastScrollTop = 0

if (scrollUp) {
	window.addEventListener('scroll', function() {
		let topPos = this.scrollY
		if (topPos > lastScrollTop) {
			scrollUp.classList.remove('show')
		} else {
			if (this.scrollY > 200) {
				scrollUp.classList.add('show')
			} else {
				scrollUp.classList.remove('show')
			}
		}
		lastScrollTop = topPos
	})
	scrollUp.addEventListener('click', function(evt) {
		document.querySelector('html, body').animate({
			scrollTop: 0
		})
	})
}
