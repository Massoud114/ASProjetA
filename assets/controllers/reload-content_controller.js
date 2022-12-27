import {Controller} from '@hotwired/stimulus';
import {jsonFetch} from '../js/functions/api';
import {scrollTo} from '../js/functions/animation';
import {useDebounce} from 'stimulus-use';

export default class extends Controller
{
	static targets = ['content']
	static values = {
		url: String,
	}
	static debounces = ['search']

	connect(){
		useDebounce(this)
	}

	refreshContent(event) {
		event.preventDefault()
		event.stopPropagation()
		this.urlValue = event.params.url || this.urlValue
		this.requestContent(this.urlValue).then(r => r)
	}

	async requestContent(url) {
		const target = this.hasContentTarget ? this.contentTarget : this.element

		target.style.opacity = .5;
		const response = await jsonFetch(url, {}, false)
		target.innerHTML = response
		target.style.opacity = 1;
		scrollTo(target)
	}

	onSearchInput(event) {
		this.search(event.currentTarget.value)
	}

	search(query) {
		const params = new URLSearchParams({
			q: query,
		})
		console.log(this.urlValue);
		const url = `${this.urlValue}${this.urlValue.includes('?') ? '&' : '?'}${params.toString()}`
		this.requestContent(url).then(r => r)
	}

}
