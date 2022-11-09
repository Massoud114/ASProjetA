/*$('.shop_toolbar_btn > button').on('click', function (e) {

		e.preventDefault();

		$('.shop_toolbar_btn > button').removeClass('active');
		$(this).addClass('active');

		var parentsDiv = $('.shop_wrapper');
		var viewMode = $(this).data('role');


		parentsDiv.removeClass('grid_3 grid_4 grid_5 grid_list').addClass(viewMode);

		if(viewMode == 'grid_3'){
			parentsDiv.children().addClass('col-lg-4 col-md-6 col-sm-6').removeClass('col-lg-3 col-cust-5 col-12');

		}

		if(viewMode == 'grid_4'){
			parentsDiv.children().addClass('col-lg-3 col-md-6 col-sm-6').removeClass('col-lg-4 col-cust-5 col-12');
		}

		if(viewMode == 'grid_list'){
			parentsDiv.children().addClass('col-12').removeClass('col-lg-3 col-lg-4 col-md-6 col-sm-6 col-cust-5');
		}

	});*/

document.querySelector('.shop_toolbar_btn > button').addEventListener('click', function (e) {

	e.preventDefault();
	document.querySelector('.shop_toolbar_btn > button').classList.remove('active');
	this.classList.add('active');

	let parentsDiv = document.querySelector('.shop_wrapper');
	let viewMode = this.dataset.role;

	parentsDiv.classList.remove('grid_3 grid_4 grid_5 grid_list')
	parentsDiv.classList.add(viewMode);

	if(viewMode == 'grid_3'){
		let children = parentsDiv.children
		for (let i = 0; i < children.length; i++) {
			children[i].classList.add('col-lg-4 col-md-6 col-sm-6')
			children[i].classList.remove('col-lg-3 col-cust-5 col-12')
		}
	}

	if(viewMode == 'grid_4'){
		let children = parentsDiv.children
		for (let i = 0; i < children.length; i++) {
			children[i].classList.add('col-lg-3 col-md-6 col-sm-6')
			children[i].classList.remove('col-lg-4 col-cust-5 col-12')
		}
	}

	if(viewMode == 'grid_list'){
		let children = parentsDiv.children
		for (let i = 0; i < children.length; i++) {
			children[i].classList.add('col-12')
			children[i].classList.remove('col-lg-3 col-lg-4 col-md-6 col-sm-6 col-cust-5')
		}
	}

})
