import {Controller} from '@hotwired/stimulus';
import TomSelect from 'tom-select';
import {jsonFetch} from '../js/functions/api';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

/* stimulusFetch: 'lazy' */
export default class extends Controller {

	static values = {
		remote: String,
		valueField: {type : String, default : "id"},
		labelField: {type : String, default : "name"},
		searchField: String,
		options: Object
	}

	initialize() {
		this.bindSelect = this.bindSelect.bind(this);
	}

	/**
	 * @param {HTMLSelectElement} select
	 */
	bindSelect(select) {
		const options = {
			hideSelected: true,
			closeAfterSelect: true,
			valueField: this.valueFieldValue,
			labelField: this.labelFieldValue,
			searchField: this.searchFieldValue || this.labelFieldValue,
			plugins: {
				remove_button: { title: 'Remove' },
			},
			load: async (query, callback) => {
				const url = `${this.remoteValue}?q=${encodeURIComponent(query)}`
				callback(await jsonFetch(url));
			},
		}
		if (this.hasOptionsValue) {
			Object.assign(options, this.optionsValue);
		}
		new TomSelect(select, options);
	}

	connect() {
		if (this.hasRemoteValue) {
			this.bindSelect(this.element);
		}
	}

}
