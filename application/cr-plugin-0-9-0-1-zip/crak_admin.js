document.addEventListener("DOMContentLoaded", function() {
  function changePage () {
    var page = window.location.hash.substr(6);

    if (crakPages.indexOf(page) == -1) {
      page = crakPages[0];
    }

    crakSettingsForm.className = page;
  }

  function remove (elem) {
    return elem.parentNode.removeChild(elem);
  }

  function newLink () {
    k++;

    var linkData = JSON.parse(document.getElementById('crak_settings').dataset.links);
    var linkOptions = '';

    linkData.forEach(function (values, key) {
      linkOptions += '<option value="' + key + '" ' + (values.enabled ? '' : 'disabled') + '>' + values.name +
        (values.enabled ? '' : ' (coming soon)') + '</option>';
    });

    var sampleLinkCode = '<td>' + '<label for="crak_intext_vertical_' + k +
      '" class="th" title="Type of the content the word will link to">Vertical</label>' +
      '<select id="crak_intext_vertical_' + k + '" name="crak_intext_vertical[' + k +
      ']" title="Type of the content the word will link to">' + linkOptions + '</select>' + '</td>' + '<td>' +
      '<label for="crak_intext_words_' + k +
      '" class="th" title="Words to replace with a link, separated by commas">Words to replace</label>' +
      '<input id="crak_intext_words_' + k + '" type="text" name="crak_intext_words[' + k +
      ']" title="Words to replace with a link">' + '</td>' + '<td>' + '<label for="crak_intext_source_' + k +
      '" class="th" title="Tracking data">Source</label>' + '<input id="crak_intext_source_' + k +
      '" name="crak_intext_source[' + k + ']" type="text" title="Tracking data">' + '</td>' + '<td>' +
      '<label for="crak_intext_affsub_' + k + '" class="th" title="Tracking data">Aff Sub ID</label>' +
      '<input id="crak_intext_affsub_' + k + '" name="crak_intext_affsub[' + k +
      ']" type="text" title="Tracking data">' + '</td>' + '<td class="tiny-col">' + '<label for="crak_intext_number_' +
      k + '" class="th" title="Maximum number of occurrences">Number</label>' + '<input id="crak_intext_number_' + k +
      '" name="crak_intext_number[' + k + ']" type="text" title="Tracking data">' + '</td>' + '<td class="tiny-col">' +
      '<label for="crak_intext_target_' + k + '" class="th" title="Open links in a new tab">Pop</label>' +
      '<input id="crak_intext_target_' + k + '" name="crak_intext_target[' + k +
      ']" type="checkbox" class="checkbox" title="Tracking data">' + '<label for="crak_intext_target_' + k +
      '" title="Open links in a new tab"></label>' + '</td>';

    var row = document.createElement('tr');
    var removeBtn = document.createElement('span');
    var removeBtnCell = document.createElement('td');
    removeBtnCell.className = 'tiny-col';
    removeBtnCell.appendChild(removeBtn);
    removeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21"><path fill="#E21B1B" fill-rule="evenodd" d="M5.25 11.55h10.5v-2.1H5.25v2.1zM10.5 0C4.725 0 0 4.725 0 10.5S4.725 21 10.5 21 21 16.275 21 10.5 16.275 0 10.5 0z"/></svg>';
    row.innerHTML = sampleLinkCode;

    removeBtn.addEventListener('mouseup', function () {
      remove(row);
    });

    row.appendChild(removeBtnCell);
    intext.appendChild(row);
    var newInputs = row.querySelectorAll('input');

    Object.keys(newInputs).forEach(function (key) {
      newInputs[key].addEventListener('change', crakChanged);
    });

    intext.appendChild(newLine);

    if (this.id == 'crak_intext_target_new') {
      this.checked = false;
      document.getElementById(this.id.replace('new', k)).checked = true;
    }

    document.getElementById(this.id.replace('new', k)).focus();
  }

  function crakChanged () {
    changed = true;

    checkEnabled();
  }

  function checkEnabled () {
    Object.keys(crakEnablers).forEach(function (key) {
      if (!crakEnablers[key].checked) {
        crakEnablers[key].parentElement.parentElement.className = "disabled";
      } else {
        crakEnablers[key].parentElement.parentElement.className = "";
      }
    })
  }

  var crakSettingsForm = document.getElementById('crak_settings');
  var intext = document.getElementById('intext');
  var inputs = crakSettingsForm.querySelectorAll("input, selector");
  var crakEnablers = crakSettingsForm.querySelectorAll("legend input");
  var intextRemoves = document.getElementsByClassName('remove');
  var newLine = document.getElementById('new_line');
  var newLineElements = newLine.querySelectorAll('input, select, #addLink');
  var tabs = document.querySelectorAll('#crak_tabs a');
  var changed = false;
  var submits = false;
  var crakPages = ['dashboard', 'popup', 'intext', 'native'];
  var ii = intextRemoves.length;
  var k = document.querySelectorAll('#intext tr').length - 2;

  window.addEventListener('hashchange', changePage);
  changePage();

  newLineElements.forEach(function (el) {
    el.addEventListener('click', newLink);
  });

  tabs.forEach(function (el) {
    el.addEventListener('click', checkSubmitTabs);
  });

  for (var i = 0; i < ii; i++) {
    var el = intextRemoves[i];
    el.addEventListener('mouseup', function () {
      remove(this.parentNode.parentNode);
    });
  }

  function checkSubmit (e) {
    if (changed && !submits) {
      var confirmationMessage = "You have unsaved changes. Do you really want to close?";

      e.returnValue = confirmationMessage;
      return confirmationMessage;
    }

    return false;
  }

  function checkSubmitTabs (e) {
    if (changed && !submits) {
      if (!confirm("You have unsaved changes. Do you really want to change tab?\r\nNote: Changes won't be reverted in the form until you reload the page.")) {
        e.preventDefault();
        return false;
      } else {
        changed = false;
      }
    }

    return true;
  }

  window.addEventListener("beforeunload", checkSubmit);

  crakSettingsForm.addEventListener('submit', function () {
    submits = true;
  });

  Object.keys(inputs).forEach(function (key) {
    inputs[key].addEventListener('change', crakChanged);
  });

  checkEnabled();

  var enablers = document.getElementsByClassName('enabler');
  var dashEnablers = document.getElementsByClassName('dash_enabler');

  function enablersSync () {
    Object.keys(enablers).forEach(function (key) {
      document.getElementById('dash_' + enablers[key].id).checked = enablers[key].checked;
    });
  }

  function dashEnablersSync () {
    Object.keys(dashEnablers).forEach(function (key) {
      document.getElementById(dashEnablers[key].dataset.id).checked = dashEnablers[key].checked;
    });
  }

  Object.keys(enablers).forEach(function (key) {
    enablers[key].addEventListener('click', enablersSync);
  });

  Object.keys(dashEnablers).forEach(function (key) {
    dashEnablers[key].addEventListener('click', dashEnablersSync);
  });

  function checkAffID () {
    crak_aff_id.className = parseInt(crak_aff_id.value) > 1 && parseInt(crak_aff_id.value) == crak_aff_id.value ? '' : 'crak_error';
  }

  var crak_aff_id = document.getElementById('crak_aff_id');

  crak_aff_id.addEventListener('change', checkAffID);

  checkAffID();
});