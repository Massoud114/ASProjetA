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
				['bold', 'italic', 'underline', 'strike'],
				['blockquote', 'code-block'],
				[{ 'list': 'ordered'}, { 'list': 'bullet' }],
				[{ 'script': 'sub'}, { 'script': 'super' }],
				[{ 'indent': '-1'}, { 'indent': '+1' }],
				[{ 'header': [1, 2, 3, 4, 5, 6] }],
				[{ 'color': [] }, { 'background': [] }],
				[{ 'font': [] }],
				[{ 'align': [] }],
				['clean']
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
