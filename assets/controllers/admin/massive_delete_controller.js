import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

	static values = {
		row : String
	}

	connect() {
		this.element.addEventListener('submit', (e) => {
			e.preventDefault();
			this.appendSelectedProducts();

			this.element.submit();
		});
	}

	appendSelectedProducts() {
		let selected = [];
		let checkboxes = document.querySelectorAll('.' + this.crudValue + '-checkbox:checked');
		for (let i = 0; i < checkboxes.length; i++) {
			selected.push(checkboxes[i].id.replace('id', ''));
		}
		let input = document.createElement('input');
		input.type = 'hidden';
		input.name = this.crudValue + 'Ids';
		input.value = selected.join(',');
		this.element.appendChild(input);
	}

	disconnect() {
		this.element.removeEventListener('submit', (e) => {
			e.preventDefault();
		});
	}
}
