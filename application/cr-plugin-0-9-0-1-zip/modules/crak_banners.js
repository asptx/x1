/**
 * Created by lpainchaud on 4/6/2018.
 */
function crak_resize(crak_frame) {
  var key = crak_frame.crak_key;
  var crak_width_detector = document.getElementById("crak_width_detector_" + key);

  var crak_ratio = (crak_width_detector.offsetWidth / crak_frame.crak_frame_width);
  var crak_margin = (parseInt(crak_frame.attributes.height.value) * crak_ratio) - parseInt(crak_frame.attributes.height.value);

  if (crak_ratio > 1 || !crak_ratio) {
    crak_ratio = 1;
  }

  if (crak_margin > 0) {
    crak_margin = 0;
  }
  crak_frame.style.transform = "scale(" + crak_ratio + ")";
  crak_frame.style.marginBottom = crak_margin + "px";
}

(function(){
  var crak_frames = document.querySelectorAll(".crak_frame");

  Object.keys(crak_frames).forEach(function(key){
    var crak_frame = crak_frames[key];
    var crak_width_detector = document.getElementById("crak_width_detector_" + key);

    crak_frame.crak_key = key;
    crak_frame.crak_frame_width = crak_frame.attributes.width.value;

    crak_frame.style.width = crak_frame.crak_frame_width + "px";
    crak_frame.style.maxWidth = crak_frame.crak_frame_width + "px";
    crak_frame.style.transformOrigin = "0 0";
    crak_width_detector.style.display = "block";
    crak_width_detector.style.width = "100%";

    crak_resize(crak_frame);

    setInterval(function(){crak_resize(crak_frame)}, 1000);

    window.addEventListener("resize", function(){
      crak_resize(crak_frame);
    });
  });
})();