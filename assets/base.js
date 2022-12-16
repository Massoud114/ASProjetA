import './bootstrap';

import './scss/base/style.scss';
import './styles/animate.min.css';
import './styles/nice-select.min.css';
import './styles/swiper-bundle.min.css';

import 'bootstrap';
import './js/scripts/sticky';
import './js/scripts/offcanvas';
import './js/scripts/responsive_menu';
import './js/scripts/scroll_top';

document.addEventListener('DOMContentLoaded', function () {
	window.setTimeout(function () {
		document.querySelector('#preloader').style.display = 'none'
		document.body.style.overflow = 'visible'
	}, 350)
})

