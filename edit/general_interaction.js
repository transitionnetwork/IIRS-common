/* static javascript only
 * independent of the environment variables
 * separately called by loaders and changes
 * used by all implementations of the IIRS process
 */
function attachEditInteractionEvents() {
  //----------------------------- place selector
  jQuery("#IIRS_0_research_townname_new").keypress(function(e){
    if (e.which == 13) {
      jQuery("#IIRS_0_research").click();
      e.preventDefault();
    }
  });
  jQuery("#IIRS_0_research").click(function(e){
    //note that this click will be overridden by the popup.js

    //get new townname
    var sNewTownName = jQuery("#IIRS_0_research_townname_new").val();
    var oParameters  = [];

    if (!sNewTownName) {
      if (window.IIRS_0_message) window.IIRS_0_message(g_tFormNotValid);
      else alert(g_tFormNotValid);
    } else {
      oParameters.push({"name":"townname", "value":sNewTownName});
      //set content
      IIRS_0_postAsForm(document.location, oParameters);
    }

    e.preventDefault();
    e.stopPropagation();
  });
}

//attach events on content change
jQuery(document).bind("IIRS_0_newContent", attachEditInteractionEvents);

