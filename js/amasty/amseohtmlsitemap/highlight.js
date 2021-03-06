Element.addMethods({
	highlight: function(element, term, className) {
		function innerHighlight(element, term, className) {
			className = className || 'highlight';
			term = (term || '').toUpperCase();
			if (term.replace(/\s+/, '') == '') {
				return false
			}

			var skip = 0;
			if ($(element).nodeType == 3) {
				var pos = element.data.toUpperCase().indexOf(term);
				if (pos >= 0) {
					var middlebit = element.splitText(pos),
						endbit = middlebit.splitText(term.length),
						middleclone = middlebit.cloneNode(true),
						spannode = document.createElement('span');

					spannode.className = className;
					spannode.appendChild(middleclone);
					middlebit.parentNode.replaceChild(spannode, middlebit);
					skip = 1;
				}
			}
			else if (element.nodeType == 1 && element.childNodes && !/(script|style)/i.test(element.tagName)) {
				for (var i = 0; i < element.childNodes.length; ++i)
					i += innerHighlight(element.childNodes[i], term, className);
			}
			return skip;
		}

		innerHighlight(element, term, className);
		return element;
	},
	removeHighlight: function(element, term, className) {
		className = className || 'highlight';
		$(element).select("span."+className).each(function(e) {
			e.parentNode.replaceChild(e.firstChild, e);
		});
		return element;
	}
});