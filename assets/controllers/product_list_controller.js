import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

	connect() {
		this.bindMassiveCheck()
	}

	toggleActive(event) {
		event.preventDefault();
		let target = event.currentTarget;
		let productId = target.dataset.productId;

		const action = !(target.dataset.active === 'true');

		if(action) {
			target.dataset.active = 'true';
			target.classList.add('active');
		} else {
			target.dataset.active = 'false';
			target.classList.remove('active');
		}

		//TODO : Send request to server
	}

	toggleFavorite(event) {
		event.preventDefault();
		let target = event.currentTarget;
		let productId = target.dataset.productId ;

		const action = !(target.dataset.favorite === 'true');

		if(action) {
			target.dataset.favorite = 'true';
			target.classList.add('active');
		} else {
			target.dataset.favorite = 'false';
			target.classList.remove('active');
		}

		//TODO : Send request to server
	}

	toggleMassiveDelete(event) {
		let massiveDeleteButton = document.getElementById('massive-delete-button');
		document.getElementById('massiveCheck').checked = false;
		if(event.currentTarget.checked) {
			massiveDeleteButton.classList.remove('hidden');
		} else {
			if(!document.querySelector('.product-checkbox:checked')) {
				massiveDeleteButton.classList.add('hidden');
			}
		}
	}

	bindMassiveCheck() {
		let checkboxes = document.querySelectorAll('.product-checkbox');
		let massiveCheckButton = document.getElementById('massiveCheck');
		let massiveDeleteButton = document.getElementById('massive-delete-button');

		massiveCheckButton.addEventListener('change', function(event) {
			checkboxes.forEach(checkbox => {
				checkbox.checked = event.currentTarget.checked;
			});
			if(event.currentTarget.checked) {
				massiveDeleteButton.classList.remove('hidden');
			} else {
				massiveDeleteButton.classList.add('hidden');
			}
		})
	}

	disconnect(){
		let massiveCheckButton = document.getElementById('massiveCheck');
		massiveCheckButton.removeEventListener('change', function(event) {
			event.preventDefault();
		})
	}
}
