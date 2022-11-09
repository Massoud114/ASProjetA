import './scripts/sticky';
import './scripts/offcanvas';
import './scripts/cart_operation';
import './scripts/responsive_menu';
import './scripts/shop_grid';
import './scripts/countdown';
import './scripts/scroll_top';

/*$(window).on('load', function () {
	$('#preloader').delay(350).fadeOut('slow')
	$('body').delay(350).css({'overflow':'visible'});
});*/

document.addEventListener('DOMContentLoaded', function () {
	document.querySelector('#preloader').style.display = 'none'
	document.querySelector('body').style.overflow = 'visible'
})
