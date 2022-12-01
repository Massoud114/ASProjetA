import {Controller} from '@hotwired/stimulus';
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

export default class extends Controller
{
	static values = {
		min: Number,
		max: Number,
		step: Number
	}

	static targets = ['container', 'minPrice', 'maxPrice']

	connect() {
		const minValue = Math.floor(parseInt(this.minValue, 10) / 10) * 10
		const maxValue = Math.floor(parseInt(this.maxValue, 10) / 10) * 10

		const range = noUiSlider.create(this.containerTarget, {
			start: [this.minPriceTarget.value || minValue, this.maxPriceTarget.value || maxValue],
			connect: true,
			step: this.stepValue,
			range: {
				'min' : minValue,
				'max' : maxValue
			}
		})

		range.on('slide', function(values, handle) {
			if (handle === 0) {
				this.minPriceTarget.value = Math.round(values[0])
			}

			if (handle === 1) {
				this.maxPriceTarget.value = Math.round(values[1])
			}
		})

		range.on('end', function(values, handle) {
			this.minPriceTarget.dispatchEvent(new Event('change'))
		})
	}
}
