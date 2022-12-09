import {Controller} from '@hotwired/stimulus';
import {jsonFetch} from '../js/functions/api';

export default class extends Controller
{
	static targets = ['content']
	static values = {
		url: String,
	}


	async refreshContent(event) {
		event.preventDefault()
		event.stopPropagation()
		this.urlValue = event.params.url || this.urlValue
		const target = this.hasContentTarget ? this.contentTarget : this.element

		target.style.opacity = .5;
		const response = await jsonFetch(this.urlValue, {}, false)
		target.innerHTML = response
		target.style.opacity = 1;
		target.scrollIntoView({
			behavior: 'smooth',
		});
	}

}
