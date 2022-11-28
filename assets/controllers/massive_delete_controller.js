import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

	connect() {
		this.element.addEventListener('submit', (e) => {
			e.preventDefault();
			this.appendSelectedProducts();

			this.element.submit();
		});
	}

	appendSelectedProducts() {
		let selected = [];
		let checkboxes = document.querySelectorAll('.product-checkbox:checked');
		for (let i = 0; i < checkboxes.length; i++) {
			selected.push(checkboxes[i].id.replace('pid', ''));
		}
		let input = document.createElement('input');
		input.type = 'hidden';
		input.name = 'productIds';
		input.value = selected.join(',');
		this.element.appendChild(input);
		console.log(selected);
	}

	disconnect() {
		this.element.removeEventListener('submit', (e) => {
			e.preventDefault();
		});
	}
}
