import {Controller} from '@hotwired/stimulus';
import {isAuthenticated} from '../js/functions/auth';
import {jsonFetchOrFlash} from '../js/functions/api';
import {cookie} from '../js/functions/cookie';

export default class extends Controller {

	static values = {
		theme : String
	}

	connect() {
		let switcher = this.element
		switcher.classList.add('dark-switch')

		let self = this
		switcher.addEventListener('click', function(e) {
			e.preventDefault()
			self.changeTheme(e)
		})

		this.setTheme(switcher)
	}

	themeValueChanged(){
		if(this.themeValue === "dark") {
			this.element.classList.add('active')
			document.body.classList.remove('light-mode')
			document.body.classList.add("dark-mode")
		} else {
			this.element.classList.remove('active')
			document.body.classList.remove('dark-mode')
			document.body.classList.add('light-mode')
		}
	}

	disconnect() {
		let self = this
		this.element.removeEventListener('click', function(e) {
			e.preventDefault()
		})
	}

	changeTheme(e) {
		this.themeValue = e.currentTarget.classList.contains('active') ? 'light' : 'dark'

		if (isAuthenticated()){
			jsonFetchOrFlash('/api/profil/theme', {
				body: { theme: this.themeValue },
				method: 'POST'
			}).catch(console.error)
		} else {
			cookie('theme', this.themeValue, {expires : 30})
		}
	}

	setTheme(darkSwitcher) {
		if (!isAuthenticated()) {
			const savedTheme = cookie('theme')
			if (savedTheme === null) {
				const mq = window.matchMedia('(prefers-color-scheme: dark)')
				if (mq.matches){
					this.themeValue = "dark"
				}
			} else {
				this.themeValue = savedTheme
			}
		} else if (document.body.classList.contains('dark-mode')){
			this.themeValue = "dark"
		} else if (document.body.classList.contains('light-mode')){
			this.themeValue = "light"
		} else {
			if (window.matchMedia('(prefers-color-scheme: dark)').matches){
				this.themeValue = "dark"
			}
		}
	}
}
