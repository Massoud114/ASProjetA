/**
 * Renvoie la hauteur de la fenêtre
 *
 * @return {number}
 */
export function windowHeight() {
	return window.innerHeight || document.documentElement.clientHeight ||
		document.body.clientHeight;
}

/**
 * Renvoie la largeur de la fenêtre
 *
 * @return {number}
 */
export function windowWidth() {
	return window.innerWidth || document.documentElement.clientWidth ||
		document.body.clientWidth;
}

const uuid = new Date().getTime().toString();
if (localStorage) {
	localStorage.setItem('windowId', uuid);
	window.addEventListener('focus', function() {
		localStorage.setItem('windowId', uuid);
	});
}

/**
 * Renvoie true si la fenêtre est active ou si elle a été la dernière fenêtre active
 */
export function isActiveWindow() {
	if (localStorage) {
		return uuid === localStorage.getItem('windowId');
	} else {
		return true;
	}
}

/**
 *
 * @param {String} message
 * @param {string} type
 * @param {number|null} duration
 */
export function flash(message, type = 'success', duration = 3) {
	const alert = document.createElement('div');
    alert.dataset.controller = 'alert-floating';
	if (duration) {
		alert.dataset.alertFloatingDurationValue = duration.toString();
	}
	alert.dataset.alertFloatingTypeValue = type;
	alert.innerText = message;
	document.getElementById('toast-container').appendChild(alert);
}
