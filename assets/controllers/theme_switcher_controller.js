import {Controller} from '@hotwired/stimulus';
import {isAuthenticated} from '../js/functions/auth';
import {cookie} from '../js/functions/cookie';

export default class extends Controller {

	connect() {
		let darkSwitcher = this.element
		darkSwitcher.classList.add('dark-switch')

		let self = this
		darkSwitcher.addEventListener('click', function(e) {
			self.changeTheme(e)
		})

		this.setTheme(darkSwitcher)
	}

	disconnect() {
		let self = this
		this.element.removeEventListener('click', function(e) {
			e.preventDefault()
		})
	}

	changeTheme(e) {
		const themeToRemove = e.currentTarget.classList.contains('active') ? 'light' : 'dark'
		const themeToAdd = e.currentTarget.classList.contains('active') ? 'dark' : 'light'
		document.body.classList.add(`${themeToAdd}-mode`)
		document.body.classList.remove(`${themeToRemove}-mode`)

		if (isAuthenticated()){
			/*jsonFetchOrFlash('/api/profil/theme', {
				body: { theme: themeToAdd },
				method: 'POST'
			}).catch(console.error)*/
		}
		else {
			cookie('theme', themeToAdd, {expires : 30})
		}
	}

	setTheme(darkSwitcher) {
		if (!isAuthenticated()) {
			const savedTheme = cookie('theme')
			if (savedTheme === null) {
				const mq = window.matchMedia('(prefers-color-scheme: dark)')
				if (mq.matches){
					darkSwitcher.classList.add('active')
					document.body.classList.add('dark-mode')
				}
			} else {
				document.body.classList.add(`${savedTheme}-mode`)
				savedTheme === 'dark' ?
					darkSwitcher.classList.add('active') :
					darkSwitcher.classList.remove('active')
			}
		} else if (document.body.classList.contains('dark-mode')){
			darkSwitcher.classList.add('active')
		} else if (document.body.classList.contains('light-mode')){
			darkSwitcher.classList.remove('active')
		} else {
			if (window.matchMedia('(prefers-color-scheme: dark)').matches){
				darkSwitcher.classList.add('active')
				document.body.classList.add('dark-mode')
			}
		}
	}
}
