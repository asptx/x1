function replace_in_strings(word, url, el, blank) {
  var re = new RegExp('\\b(' + word + ')\\b', 'gi');

  if (el != null && typeof el.childNodes != 'undefined' && el.childNodes.length && crak_intext_limits[word] > 0) {
    Object.keys(el.childNodes).forEach(function (k) {
      if (el.childNodes[k].nodeName == '#text') {
        if (el.childNodes[k].nodeValue.split('	').join('').split(' ').join('').split('\\n').join('').split('\\r').join('').length > word.length) {

          var span = document.createElement('span');
          var newString = el.childNodes[k].nodeValue;
          var xArray;
          var extraLength = 0;

          while (xArray = re.exec(el.childNodes[k].nodeValue)) {
            if (crak_intext_limits[word]-- > 0) {
              var thisUrl = '/out/?url=' + encodeURIComponent(url);
              newString = newString.substr(0, xArray.index + extraLength) + '<a href="' + thisUrl + '" rel="nofollow" ' + (blank ? 'target="_blank"' : '') + '>' + xArray[0] + '</a>' + newString.substr(xArray.index + extraLength + xArray[0].length);
              extraLength += ('<a href="' + thisUrl + '" rel="nofollow" ' + (blank ? 'target="_blank"' : '') + '>' + '</a>').length;
            }
          }

          span.innerHTML = newString;
          el.childNodes[k].parentNode.replaceChild(span, el.childNodes[k]);

        }
      } else if ( ['script', 'style', 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'label', 'textarea', 'head', 'label', 'blockquote', 'input', 'select', 'nav'].indexOf(el.childNodes[k].nodeName.toLowerCase()) == -1 && crak_intext_limits[word] > 0) {
        replace_in_strings(word, url, el.childNodes[k], blank);
      }
    });
  }
}

var crak_intext_limits = {};
