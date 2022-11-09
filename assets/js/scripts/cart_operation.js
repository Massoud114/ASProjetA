/*$('.cart-plus-minus').append(
	'<div class="dec qtybutton"><i class="fa fa-minus"></i></div><div class="inc qtybutton"><i class="fa fa-plus"></i></div>'
);
$('.qtybutton').on('click', function () {
	var $button = $(this);
	var oldValue = $button.parent().find('input').val();
	if ($button.hasClass('inc')) {
		var newVal = parseFloat(oldValue) + 1;
	} else {
		// Don't allow decrementing below zero
		if (oldValue > 1) {
			var newVal = parseFloat(oldValue) - 1;
		} else {
			newVal = 1;
		}
	}
	$button.parent().find('input').val(newVal);
});*/

document.querySelectorAll('.cart-plus-minus').forEach(function (el) {
	el.innerHTML += '<div class="dec qtybutton"><i class="fa fa-minus"></i></div><div class="inc qtybutton"><i class="fa fa-plus"></i></div>';
})
document.querySelectorAll('.qtybutton').forEach(function (el) {
	el.addEventListener('click', function () {
		let button = el;
		let newValue = 0;
		let oldValue = button.parentNode.querySelector('input').value;
		if (button.classList.contains('inc')) {
			newValue = parseFloat(oldValue) + 1;
		} else {
			// Don't allow decrementing below zero
			if (oldValue > 1) {
				let newVal = parseFloat(oldValue) - 1;
			} else {
				newValue = 1;
			}
		}
		button.parentNode.querySelector('input').value = newValue.toString();
	});
})
