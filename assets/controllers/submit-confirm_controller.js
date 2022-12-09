import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';
import {flash} from '../js/functions/window';

export default class extends Controller {

	static values = {
		title: String,
		text: String,
		icon: String,
		cancel: String,
		confirmButtonText: String,
		asyncSubmit: Boolean
	}

	connect() {
	}

	onSubmit(event) {
		event.preventDefault()
		this.modal = this.createModalContainer()
	}

	createModalContainer() {
		const modalContainer = document.createElement('div')
		modalContainer.classList.add('modal')
		modalContainer.classList.add('fade')
		modalContainer.classList.add('zoom')
		modalContainer.setAttribute('tabindex', '-1')
		modalContainer.setAttribute('role', 'dialog')
		modalContainer.setAttribute('aria-modal', 'true')
		modalContainer.innerHTML = `
		 	<div class="modal-dialog" role="dialog">
		 		<div class="modal-content">
		 			<div class="modal-header">
		 				<h5 class="modal-title">${this.titleValue || null}</h5>
		 				<a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
		 					<em class="icon ni ni-cross"></em>
		 				</a>
		 			</div>
		 			<div class="modal-body">
		 				<p>${this.textValue || null}</p>
		 			</div>
		 			<div class="modal-footer bg-light">
		 				<button type="button" class="btn btn-default cancelButton" id="btnNo">
		 					<em class="icon ni ni-ban"></em> <span>${this.cancelValue}</span>
		 				</button>
		 				<button type="button" class="btn btn-danger confirmButton" id="btnYes">
		 					<em class="icon ni ni-trash"></em> ${this.confirmButtonTextValue || null}
		 				</button>
		 			</div>
		 		</div>
		 	</div>
		`
		this.modalContainer = modalContainer
		document.body.appendChild(modalContainer)
		this.modal = new Modal(modalContainer, {
			keyboard: false,
		})
		this.modal.show()
		modalContainer.querySelector('#btnNo').addEventListener('click', (e) => {
			this.modal.hide()
			modalContainer.remove()
		})
		modalContainer.querySelector('#btnYes').addEventListener('click', (e) => {
			this.submitForm().then(r => r)
		})
		return this.modal
	}

	disconnect() {
	}

	async submitForm() {
		if (!this.asyncSubmitValue) {
			this.element.submit()
			return
		}
		this.startLoading()
		const response = await fetch(this.element.action, {
			method: this.element.method,
			body: new URLSearchParams(new FormData(this.element))
		}).then((r) => {
			flash("Le produit a bien été supprimé", "success")
		}).catch(e => {
			flash(e.response, "danger")
		}).finally(() => {
			this.modal.hide()
			this.modalContainer.remove()
		})

		this.dispatch('async:submitted', {
			response,
		})
	}

	startLoading(){
		const confirmButton = this.modalContainer.querySelector('#btnYes')
		const cancelButton = this.modalContainer.querySelector('#btnNo')
		confirmButton.innerHTML = `
			<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Chargement...</span>
		`
		confirmButton.setAttribute('disabled', 'disabled')
		cancelButton.setAttribute('disabled', 'disabled')
	}
}
