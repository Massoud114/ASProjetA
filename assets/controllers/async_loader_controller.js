import {Controller} from '@hotwired/stimulus';
import {jsonFetch} from '../js/functions/api';

export default class extends Controller {

	static values = {
		url: String,
		canAccess: Boolean,
	}

	static targets = ['container']

	get canAccess() {
		return this.canAccessValue
	}

	initialize () {
		this.load.bind(this)
	}

	disconnect () {	}

	connect () {
		if (!this.hasUrlValue) {
			console.error('[stimulus-content-loader] You need to pass an url to fetch the remote content.')
			return
		}
		this.container = this.hasContainerTarget ? this.containerTarget : this.element
		if (this.canAccess)	{
			this.load().then(r => r)
		} else {
			this.container.innerHTML = "An error occurred"
		}
	}

	async load () {
		const response = await jsonFetch(this.urlValue, {}, false)
		this.container.innerHTML = response ?? 'An error occurred'
	}
}
