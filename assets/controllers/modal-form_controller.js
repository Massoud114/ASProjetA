import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';
import {flash} from '../js/functions/window';
import {jsonFetch} from '../js/functions/api';

export default class extends Controller {
	static targets = ['modal', 'modalBody'];

	static values = {
		formUrl: String,
	};
	modal = null;

	connect() {
	}

	async openModal(event) {
		this.modalBodyTarget.innerHTML = 'Chargement...';
		this.modal = new Modal(this.modalTarget);
		this.modal.show();

		// fetch and render response inside modal no matter if there is an error
		try {
			this.modalBodyTarget.innerHTML = await jsonFetch(this.formUrlValue, {}, false);
		} catch (e) {
			this.modalBodyTarget.innerHTML = "An error occurred";
			console.error(e);
		}

		this.modalBodyTarget.querySelectorAll('button[type="submit"]').
			forEach((button) => {
				button.style.display = 'none';
			});
	}

	async submitForm(event) {
		const $form = this.modalBodyTarget.querySelector('form');
		try {
			const response = fetch(this.formUrlValue || $form.getAttribute('action'), {
				method: $form.method,
				body: new FormData($form),
				headers: {
					Accept: 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
			}).then((response => response));

			if (response.status === 204) {
				flash('Nouvel Élément', 'success', 4);
				this.modal.hide();
				this.dispatch('success');
			} else {
				this.modalBodyTarget.innerHTML = await response.text()
			}
		} catch (e) {
			flash("An error occurred", 'danger', 4);
			this.modal.hide();
		}
	}

	modalHidden() {

	}

}
