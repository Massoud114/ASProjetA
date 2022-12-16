import {Controller} from '@hotwired/stimulus';
import Swiper from 'swiper/bundle';

export default class extends Controller {
	static values = {
		options: Object
	}

	get defaultOptions () {
		return {}
	}

	connect () {
		console.log(this.optionsValue);
		this.swiper = new Swiper(this.element, this.optionsValue)
	}

	disconnect () {
		this.swiper.destroy()
		this.swiper = undefined
	}
}
