/* static javascript only
* separately called by loaders and changes
* used by JavaScript widget popup based implementations of the IIRS only
* governs the page step process in a popup
* by overriding mostly the submit events
*/
var jPopup;

function IIRS_0_formPopupNavigate(e, sContentArea, sFileName) {
  var jForm  = jQuery(this);
  var sHREF;

  if (!jForm.hasClass("IIRS_0_form_validation_fail")) {
    if (!sContentArea) sContentArea = "#IIRS_0_popup .IIRS_0_content";
    if (!sFileName)    sFileName    = jForm.attr("action");
    //absolute or relative filename
    if (sFileName[0] == '/') sHREF = g_sDomainStem + sFileName;
    else                     sHREF = g_sIIRSURLProcessStem + '/' + sFileName;

    if (jPopup.is(":visible")) {
      IIRS_0_setContent(sContentArea, sHREF, jForm.serializeArray());
    } else {
      jPopup.fadeIn(400, function(e){
        //the IIRS_0_setContent() is synchronous and will prevent the immediate fadeIn()
        IIRS_0_setContent(sContentArea, sHREF, jForm.serializeArray());
      });
    }

    //cancel bubble up from this
    e.preventDefault();
    e.stopPropagation();
  }
}

function IIRS_0_attachPopupNavigationEvents(e) {
  jPopup = jQuery("#IIRS_0_popup");

  jQuery("#IIRS_0_popup_close").click(function(e){
    jQuery(this).parent().parent().fadeOut();
  });
  jQuery("#IIRS_0_popup_refresh").click(function(e){
    //the form serialisation should work
    //because the parameters to this page are encode in the new form
    IIRS_0_setContent.apply(this, g_sThisSetContentArguments);
    e.stopPropagation();
    return false;
  });
  jQuery(".IIRS_0_formPopupNavigate").submit(IIRS_0_formPopupNavigate);

  jQuery(".IIRS_0_back").unbind("click").click(function(e){
    var sPlace = "#IIRS_0_popup .IIRS_0_content";
    jQuery(sPlace).html(g_sLastSetContentHTML);
    //dont re-submit the form!
    //IIRS_0_setContent.apply(this, g_sLastSetContentArguments);
    jQuery(sPlace).trigger("IIRS_0_newContent");
    e.stopPropagation();
  });
}

jQuery(document).bind("IIRS_0_newContent", IIRS_0_attachPopupNavigationEvents); //all new content
