import Quill from 'quill';

document.addEventListener('DOMContentLoaded', function() {
	let editors = document.querySelectorAll('.rich-text-editor');
	if (editors.length > 0) {
		editors.forEach(function(editor) {
			let editorContainer = document.createElement('div');
			editorContainer.setAttribute('id', 'editor-container');
			editorContainer.innerHTML = editor.value;
			editorContainer.dataset.inputId = editor.name;

			editor.parentNode.insertBefore(editorContainer, editor);
			editor.style.display = 'none';

			let toolbarOptions = [
				['bold', 'italic', 'underline'],
				['blockquote', {'list': 'bullet'}],
				[{'header': 1}, {'header': 2}, {'header': [3, 4, 5, 6, false]}],
				[{'align': []}],
				['clean'],
			];

			let quill = new Quill(editorContainer, {
				modules: {
					toolbar: toolbarOptions,
				},
				theme: 'snow',
			});

			quill.on('text-change', function(delta, oldDelta, source) {
				let textarea = document.querySelector('textarea[name="' + editorContainer.dataset.inputId + '"]');
				textarea.innerText = editorContainer.children[0].innerHTML || '';
			});
		});
	}
})
