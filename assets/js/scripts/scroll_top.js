/*
function scrollToTop() {
		var $scrollUp = $('.scroll-to-top'),
			$lastScrollTop = 0,
			$window = $(window);

		$window.on('scroll', function () {
			var topPos = $(this).scrollTop();
			if (topPos > $lastScrollTop) {
				$scrollUp.removeClass('show');
			} else {
				if ($window.scrollTop() > 200) {
					$scrollUp.addClass('show');
				} else {
					$scrollUp.removeClass('show');
				}
			}
			$lastScrollTop = topPos;
		});

		$scrollUp.on('click', function (evt) {
			$('html, body').animate({
				scrollTop: 0
			}, 600);
			evt.preventDefault();
		});
	}
	scrollToTop();
 */

let scrollUp = document.querySelector('.scroll-to-top')
let lastScrollTop = 0

window.addEventListener('scroll', function () {
	let topPos = this.scrollTop
	if (topPos > lastScrollTop) {
		scrollUp.classList.remove('show')
	} else {
		if (this.scrollTop > 200) {
			scrollUp.classList.add('show')
		} else {
			scrollUp.classList.remove('show')
		}
	}
	lastScrollTop = topPos
})
scrollUp.addEventListener('click', function (evt) {
	document.querySelector('html, body').animate({
		scrollTop: 0
	})
})
