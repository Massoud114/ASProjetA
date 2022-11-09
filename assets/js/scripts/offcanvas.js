let offCanvasWrapper = document.querySelector('.off-canvas-wrapper');
let offCanvasMenuWrapper = document.querySelector('.off-canvas-menus-wrapper');
let body = document.querySelector('body');

document.querySelector('.off-canvas-btn').addEventListener('click', function() {
	body.classList.add('fix')
	offCanvasWrapper.classList.add('open')
})

document.querySelector('.btn-close-off-canvas, .off-canvas-overlay').addEventListener('click', function() {
	body.classList.remove('fix')
	offCanvasWrapper.classList.remove('open')
})

document.querySelector('.off-canvas-menu-btn').addEventListener('click', function() {
	body.classList.add('fix')
	offCanvasMenuWrapper.classList.add('open')
})

document.querySelector('.btn-close-off-canvas, .off-canvas-overlay').addEventListener('click', function() {
	body.classList.remove('fix')
	offCanvasMenuWrapper.classList.remove('open')
})
/*
	$(".off-canvas-btn").on('click', function () {
		$("body").addClass('fix');
		$(".off-canvas-wrapper").addClass('open');
	});

	$(".btn-close-off-canvas,.off-canvas-overlay").on('click', function () {
		$("body").removeClass('fix');
		$(".off-canvas-wrapper").removeClass('open');
	});

	$(".off-canvas-menu-btn").on('click', function () {
		$("body").addClass('fix');
		$(".off-canvas-menu-wrapper").addClass('open');
	});

	$(".btn-close-off-canvas,.off-canvas-overlay").on('click', function () {
		$("body").removeClass('fix');
		$(".off-canvas-menu-wrapper").removeClass('open');
	});
*/
