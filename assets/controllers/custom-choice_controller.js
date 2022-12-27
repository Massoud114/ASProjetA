import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

	initialize() {

	}

	connect() {
		this.choiceContainers = this.element.querySelectorAll('.form-check');
		this.choiceContainers.forEach((choiceContainer) => {
			if (choiceContainer.querySelector('input').value === this.element.getAttribute('selected')) {
				choiceContainer.classList.add('checked');
				choiceContainer.querySelector('input').checked = true;
			}
			choiceContainer.addEventListener('click', (event) => {
				this.choiceContainers.forEach((choiceContainer) => {
					choiceContainer.classList.remove('checked');
				});
				choiceContainer.classList.add('checked');
				choiceContainer.querySelector('input').checked = true;
				this.element.setAttribute('selected', choiceContainer.querySelector('input').value);
			})
			choiceContainer.classList.add("custom-control")
			choiceContainer.classList.add("custom-radio")
			choiceContainer.classList.add("custom-control-pro")
			choiceContainer.classList.add("no-control")
			choiceContainer.classList.add("mx-2")
			choiceContainer.classList.remove("form-check")
		})
	}
}
