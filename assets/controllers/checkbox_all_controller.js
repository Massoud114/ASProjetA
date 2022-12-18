import {Controller} from '@hotwired/stimulus';
import {addFadeTransition} from '../js/functions/add-transition';

export default class extends Controller {

	static targets = ['selectAll', 'checkbox', 'action']

	get checked (){
		return this.checkboxTargets.filter(checkbox => checkbox.checked)
	}

	get unchecked () {
		return this.checkboxTargets.filter(checkbox => !checkbox.checked)
	}

	get actions() {
		return this.actionTargets
	}

	initialize () {
		this.toggle = this.toggle.bind(this)
		this.refresh = this.refresh.bind(this)
	}

	connect () {
		if (!this.hasSelectAllTarget) return

		this.selectAllTarget.addEventListener('change', this.toggle)
		this.checkboxTargets.forEach(checkbox => checkbox.addEventListener('change', this.refresh))
		this.actionTargets.forEach(element => addFadeTransition(this, element))
		this.refresh()
	}

	disconnect () {
		if (!this.hasSelectAllTarget) return

		this.selectAllTarget.removeEventListener('change', this.toggle)
		this.checkboxTargets.forEach(checkbox => checkbox.removeEventListener('change', this.refresh))
	}

	toggle (e) {
		e.preventDefault()

		this.checkboxTargets.forEach(checkbox => {
			checkbox.checked = e.target.checked
			this.triggerInputEvent(checkbox)
		})
		this.refresh()
	}

	refresh () {
		const checkboxesCount = this.checkboxTargets.length
		const checkboxesCheckedCount = this.checked.length

		this.selectAllTarget.checked = checkboxesCheckedCount > 0
		this.selectAllTarget.indeterminate = checkboxesCheckedCount > 0 && checkboxesCheckedCount < checkboxesCount
		// checkboxesCheckedCount > 0 ? this.enter() : this.leave()
		this.actionTargets.forEach(element => this.refreshActions(element))
	}

	refreshActions(element) {
		if (element.nodeName === "form" || element.nodeName === "FORM") {
			let selected = []
			for (let i = 0; i < this.checked.length; i++) {
				selected.push(this.checked[i].id.replace('id', ''));
			}
			element.querySelector('input[name=ids]').value = selected.join(',')
		}
	}

	triggerInputEvent (checkbox) {
		const event = new Event('input', { bubbles: false, cancelable: true })

		checkbox.dispatchEvent(event)
	}
}
