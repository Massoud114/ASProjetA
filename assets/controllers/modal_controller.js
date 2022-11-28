import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';

export default class extends Controller {

	static values = {
		handler: String
	}

	connect() {
		this.modal = new Modal('#' + this.element.id, {
			keyboard: true
		})
		this.bindEvents()
		this.element.addEventListener('hide.bs.modal', (e) => {
			if(this.element.dataset.target){
				this.element.dataset.target = null
			}
		})
	}

	bindEvents(){
		if (this.handlerValue !== null) {
			let forms = document.querySelectorAll('.' + this.handlerValue)
			let modal = this.modal
			let modalContainer = this.element
			forms.forEach(function(form) {
				if (form.dataset.confirmation == 'true') {
					form.addEventListener('submit', (e) => {
						e.preventDefault()
						modal.show()
						modalContainer.dataset.target = e.currentTarget.id
					})
				}
			})
		}
	}

	cancel(event) {
		this.element.dataset.target = null
		this.modal.hide()
	}

	handle(event) {
		let form = document.getElementById(this.element.dataset.target)
		if (form) {
			form.submit()
		}
	}

	disconnect() {
		this.modal.dispose()
		if (this.handlerValue !== null) {
			let forms = document.querySelectorAll('.' + this.handlerValue)
			forms.forEach(function(form) {
				form.removeEventListener('submit', (e) => {
					e.preventDefault()
				})
			})
		}
		this.element.removeEventListener('hide.bs.modal', (e) => {
			if(this.element.dataset.target){
				this.element.dataset.target = null
			}
		})
	}
}
