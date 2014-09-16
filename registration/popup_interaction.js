/* static javascript only
 * independent of the environment variables
 * separately called by loaders and changes
 * used by all implementations of the IIRS process
 */
function attachRegistrationPopupEvents() {
  //----------------------------- nav buttons
  jQuery("#IIRS_0_form_popup_advanced").submit(function(e){
    jPopup.fadeOut(400);
    return IIRS_0_formPopupNavigate.call(this, e, "#IIRS_0_placeholder_initial");
  });

  jQuery("#IIRS_0_form_popup_location_general .IIRS_0_back").unbind("click").click(function(e){
    jPopup.fadeOut(400);
    e.stopPropagation();
  });

  //----------------------------- place selector
  jQuery("#IIRS_0_research").unbind("click").click(function(e){
    //needed to unbind the general handler for re-search

    //get new townname
    var sNewTownName = jQuery("#IIRS_0_research_townname_new").val();
    var aArguments   = Array.prototype.slice.call(g_sThisSetContentArguments, 0);
    aArguments[2]    = [{"name":"townname", "value":sNewTownName}];

    //set content
    IIRS_0_setContent.apply(this, aArguments);
    e.preventDefault();
    e.stopPropagation();
  });

  //----------------------------- custom form validation
  jQuery("#IIRS_0_form_popup_domain_selection").unbind("submit").submit(function(e){
    var ret = true;
    if (!(jQuery("input[name='domain_other']").val() || jQuery("input[name='domain']:checked").val())) {
      //failed checks
      if (window.IIRS_0_message) window.IIRS_0_message(g_tDomainRequired);
      else alert(g_tDomainRequired);
      e.preventDefault();
      e.stopPropagation();
      ret = false;
    } else {
      //everything ok
      IIRS_0_formPopupNavigate.apply(this, arguments);
    }
    return ret;
  });
}

//attach events on content change
jQuery(document).bind("IIRS_0_newContent", attachRegistrationPopupEvents);

