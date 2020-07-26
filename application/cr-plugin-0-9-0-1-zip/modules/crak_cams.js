var crak_cams_ctns = document.getElementsByClassName('crak_cams_ctn');

function setHeight () {
  Object.keys(crak_cams_ctns).forEach(function (key) {
    var cols = parseInt(crak_cams_ctns[key].dataset.cols);
    var rows = parseInt(crak_cams_ctns[key].dataset.rows);
    var width = parseInt(crak_cams_ctns[key].offsetWidth);
    var height = ((width - ((cols - 1) * 10)) / cols) * .75 * rows + ((rows - 1) * 10);
    crak_cams_ctns[key].style.paddingTop = height + 'px';
  });
}

setHeight();

window.addEventListener('resize', setHeight);

setInterval(setHeight, 500);