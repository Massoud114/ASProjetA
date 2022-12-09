import {Controller} from '@hotwired/stimulus';
import {slideUp} from '../js/functions/animation';

export default class extends Controller
{
	static values = {
		type: String,
		duration: Number
	}

	connect() {
		this.alertContainer = document.getElementById('toast-container')
		if (!this.alertContainer) {
			return
		}
		if (this.typeValue === "error" || this.typeValue === null){
			this.typeValue = 'danger'
		}
		this.createAlert()
	}

	createAlert(){
		const text = this.element.innerHTML
		const duration = this.durationValue
		let progressBar = ''
		if (duration !== null) {
			progressBar = `<div class="toast-progress" style="animation-duration: ${duration}s"></div>`
			window.setTimeout(this.close.bind(this), duration * 1000)
		}
		this.element.classList.add('toastr')
		this.element.classList.add(`toast-${this.typeValue}`)
		this.element.setAttribute('aria-live', 'polite')
		this.element.innerHTML = `${progressBar}
			<span class="btn-trigger toast-close-button" role="button">Close</span>
			<div class="toast-message">
				<span class="toastr-icon">
					<em class="icon ni ni-${this.getIcon()}"></em>
				</span>
				<div class="toastr-text">
					${this.messageValue || text}
				</div>
			</div>`
		this.element.querySelector('.toast-close-button')
		.addEventListener('click', e => {
			e.preventDefault()
			this.close()
		})
	}

	close () {
		let element = this.element
		window.setTimeout(async () => {
			await slideUp(element)
			element.parentElement.removeChild(element)
			element.dispatchEvent(new CustomEvent('alert-close'))
		}, 200)
	}

	getIcon() {
		switch (this.typeValue) {
			case "danger":
				return "cross-circle-fill"
			case "warning":
				return "alert-fill"
			case "info":
				return "info-fill"
			case "success":
				return "check-circle-fill"
		}
	}

}
