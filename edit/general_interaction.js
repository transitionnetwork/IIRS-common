/* static javascript only
 * independent of the environment variables
 * separately called by loaders and changes
 * used by all implementations of the IIRS process
 */
function IIRS_0_attachEditInteractionEvents() {
  //----------------------------- place selector
  jQuery("#IIRS_0_research_town_name_new" ).keypress(function(event ){
    if (event.which == 13 ) {
      jQuery("#IIRS_0_research" ).click();
      event.preventDefault();
    }
  } );
  jQuery("#IIRS_0_research" ).click(function(event ){
    //note that this click will be overridden by the popup.js

    //get new town_name
    var sNewTownName = jQuery("#IIRS_0_research_town_name_new" ).val();
    var oParameters  = [];

    if (!sNewTownName ) {
      if (window.IIRS_0_message ) window.IIRS_0_message(g_tFormNotValid );
      else alert(g_tFormNotValid );
    } else {
      oParameters.push({"name":"town_name", "value":sNewTownName} );
      //set content
      IIRS_0_postAsForm(document.location, oParameters );
    }

    event.preventDefault();
    event.stopPropagation();
  } );
}

//attach events on content change
jQuery(document ).bind("IIRS_0_newContent", IIRS_0_attachEditInteractionEvents );

