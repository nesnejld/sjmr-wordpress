// alert('sjmr.js loaded');
var sjmrcontext = {};
sjmrcontext.drawmap = (target) => {
    let $ = jQuery;
    e = $(target).closest(".linkcontainer").find(".mapouter");
    e.css("display") == "none" ? e.css("display", "block") : e.css("display", "none");
    // alert(e)
};
sjmrcontext.sjmrinit = () => {
    // alert('sjmrinit');
    let $ = jQuery;
    // $("#exampleModalLong").css({ 'display': 'block', opacity: 1 });
    // console.log($("#exampleModalLong").css('display'))
};
jQuery(
    sjmrcontext.sjmrinit()
);