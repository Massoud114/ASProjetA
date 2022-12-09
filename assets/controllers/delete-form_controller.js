import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
	static values = {
		trigger: "product-delete"
	}
	connect() {
		this.element.style.display = "none"
		this.bindEvents()
	}

	bindEvents()
	{
		const triggers = document.querySelectorAll('.' + this.triggerValue)
		for (let i = 0; i < triggers.length; i++) {
			triggers[i].addEventListener('click', (event) => {
				event.preventDefault()
				this.element.action = event.currentTarget.dataset.url
				this.element.requestSubmit()
			})
		}
	}
}
