$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
$(document).on('click', '#inputs1b', function(event) {
  location.href=base_url+"search/q/?n="+fixedEncodeURIComponent($("#inputs1").val());
});

$(document).on('keydown', '#inputs1', function(event) {
  if(event.keyCode == 13) {
    location.href=base_url+"search/q/?n="+fixedEncodeURIComponent($("#inputs1").val());
  }
});
$(document).on('click', '#inputs2b', function(event) {
  location.href=base_url+"search/q/?n="+fixedEncodeURIComponent($("#inputs2").val());
});

$(document).on('keydown', '#inputs2', function(event) {
  if(event.keyCode == 13) {
    location.href=base_url+"search/q/?n="+fixedEncodeURIComponent($("#inputs2").val());
  }
});
function fixedEncodeURIComponent (str) {
  return encodeURIComponent(str).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
}
