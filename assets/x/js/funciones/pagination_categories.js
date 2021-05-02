
$(document).ready(function() {
  var a  = ($("#q").val());
  var curp = $("#pag").val();
  var tot = $("#tot").val();
  var extra = $("#order").val();
  var show = $(".show-box").val();
  if (extra!="false"&&extra!="")
  {
    extra = "&order="+extra+"&show="+show;
  }
  var itemsPerPage = $("#itemsPerPage").val();
    $("#page1").pagination({
        items: tot,
        itemsOnPage: itemsPerPage,
        cssStyle: 'light-theme',
        hrefTextPrefix: base_url+"categoria/s/?c="+a+extra+"&page=",
        /*hrefTextPrefix: "javascript:void(0);//",
        onPageClick: function(pageNumber, event)
        {
          console.log(pageNumber);
        },*/
        currentPage: curp,
        prevText: "<",
        nextText: ">",
        cssStyle: "compact-theme",
        ellipsePageSet:false
    })
});
$(document).on('click', '#filerc', function(event) {
  var a  = ($("#q").val());
  var sort = $(".sort-box").val();
  var show = $(".show-box").val();
  location.href=base_url+"categoria/s/?c="+a+"&order="+sort+"&show="+show;
});
