import {Controller} from '@hotwired/stimulus';
import {jsonFetch} from '../js/functions/api';

export default class extends Controller {

	static values = {
		url: String,
		lazy: Boolean,
	}

	initialize () {
		this.load().bind(this)
		this.lazyLoad().bind(this)
	}

	connect () {
		if (!this.hasUrlValue) {
			console.error('[stimulus-content-loader] You need to pass an url to fetch the remote content.')
			return
		}

		this.lazyValue ? this.load() : this.lazyLoad()
	}

	disconnect () {	}

	async load () {
		const response = await jsonFetch(this.urlValue, {}, false)
		this.element.innerHTML = response ?? 'An error occurred'
	}

	lazyLoad () {
		console.log('lazyLoad')
	}

	/*lazyLoad () {
		const options = {
			threshold: 0.4,
			rootMargin: this.lazyLoadingRootMarginValue
		}

		const observer = new IntersectionObserver(
			(entries: IntersectionObserverEntry[], observer: IntersectionObserver) => {
				entries.forEach((entry: IntersectionObserverEntry) => {
					if (entry.isIntersecting) {
						this.load()

						observer.unobserve(entry.target)
					}
				})
			},
			options
		)

		observer.observe(this.element)
	}*/
}
