import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
	static targets = ['input']

	connect(){
		if(this.inputTarget.value == 0){
			this.inputTarget.value = 1
		}
	}

	increment(event) {
		if(!this.hasInputTarget) return
		this.inputTarget.value = parseInt(this.inputTarget.value) + 1
	}

	decrement(event) {
		if(!this.hasInputTarget) return
		if(this.inputTarget.value > 1) {
			this.inputTarget.value = parseInt(this.inputTarget.value) - 1
		}
	}
}
