/*var $offCanvasNav = $('.mobile-menu'),
	$offCanvasNavSubMenu = $offCanvasNav.find('.dropdown');
//Add Toggle Button With Off Canvas Sub Menu
$offCanvasNavSubMenu.parent().prepend('<span class="menu-expand"><i></i></span>');
//Close Off Canvas Sub Menu
$offCanvasNavSubMenu.slideUp();
//Category Sub Menu Toggle
$offCanvasNav.on('click', 'li a, li .menu-expand', function(e) {
	var $this = $(this);
	if ( ($this.parent().attr('class').match(/\b(menu-item-has-children|has-children|has-sub-menu)\b/)) && ($this.attr('href') === '#' || $this.hasClass('menu-expand')) ) {
		e.preventDefault();
		if ($this.siblings('ul:visible').length){
			$this.parent('li').removeClass('active');
			$this.siblings('ul').slideUp();
		} else {
			$this.parent('li').addClass('active');
			$this.closest('li').siblings('li').removeClass('active').find('li').removeClass('active');
			$this.closest('li').siblings('li').find('ul:visible').slideUp();
			$this.siblings('ul').slideDown();
		}
	}
});*/

let offCanvasNav = document.querySelector('.mobile-menu');
let offCanvasNavSubMenu = offCanvasNav.querySelectorAll('.dropdown');
//Add Toggle Button With Off Canvas Sub Menu
offCanvasNavSubMenu.forEach(function (element) {
	element.parentElement.prepend('<span class="menu-expand"><i></i></span>');
});
//Close Off Canvas Sub Menu
offCanvasNavSubMenu.forEach(function (element) {
	element.style.display = 'none'; //Animate using JS
})
//Category Sub Menu Toggle
offCanvasNav.addEventListener('click', function (e) {
	let $this = e.target;
	if ( ($this.parentElement.classList.contains('menu-item-has-children') || $this.parentElement.classList.contains('has-children') || $this.parentElement.classList.contains('has-sub-menu')) && ($this.getAttribute('href') === '#' || $this.classList.contains('menu-expand')) ) {
		e.preventDefault();
		if ($this.siblings('ul:visible').length){
			$this.parentElement.classList.remove('active');
			$this.siblings('ul').slideUp();
		} else {
			$this.parentElement.classList.add('active');
			$this.closest('li').siblings('li').classList.remove('active')
			$this.closest('li').siblings().find('li').classList.remove('active');
			$this.closest('li').siblings('li').find('ul:visible').slideUp();
			$this.siblings('ul').slideDown();
		}
	}
})
