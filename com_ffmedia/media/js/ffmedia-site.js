document.addEventListener('DOMContentLoaded', (event) => {

	let previews = document.getElementsByClassName("img-preview");

	var imgPreview = function() {
		ffmediaAction(this);
	}

	for (var i = 0; i < previews.length; i++) {
		previews[i].addEventListener('click', imgPreview, false);
	}

	function ffmediaAction(element) {
		var paths = Joomla.getOptions(["system.paths"], 'No good');
		var root = paths.root;
		var modal = document.getElementById('collapseModal');
		var title = document.getElementsByClassName('modal-title')[0];
		var body = document.getElementsByClassName('modal-body')[0];
		var url = element.dataset.url;
		var alt = element.dataset.alt;
		var selected = 'zoom';
		if (! element.classList.contains('img-preview')) {
			selected = element.value;
			var id = element.id.split('_')[1];
		}

		switch (selected) {
			case 'zoom':
				var tag = '<img src="'+root+url+'" class="cover">';
				title.innerText = Joomla.Text._('COM_FFMEDIA_JS_IMAGE_ZOOM') + ' ' + alt;
				body.classList.add("text-center");
				body.innerHTML = tag;
				modal.open();
			break;
		}
	}
})
