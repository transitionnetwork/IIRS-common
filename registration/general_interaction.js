/* static javascript only
 * independent of the environment variables
 * separately called by loaders and changes
 * used by all implementations of the IIRS process
 */
var oNameInput, oEmailInput, sOrigTownValue;

function IIRS_0_attachRegistrationInteractionEvents() {
  //----------------------------------------- hints
  //TODO: make these input hints generic
  sOrigTownValue = jQuery("#IIRS_0_town_name").val();
  jQuery("#IIRS_0_town_name").focus(function(e){
    if (jQuery(this).val() == sOrigTownValue) jQuery(this).val('');
    jQuery(this).removeClass("IIRS_0_hint");
  });
  jQuery("#IIRS_0_town_name").blur(function(e){
    if (jQuery(this).val() == '') {
      jQuery(this).val(sOrigTownValue);
      jQuery(this).addClass("IIRS_0_hint");
    }
  });

  //----------------------------- place selector
  jQuery("#IIRS_0_research_town_name_new").keypress(function(e){
    if (e.which == 13) {
      jQuery("#IIRS_0_research").click();
      e.preventDefault();
    }
  });
  jQuery("#IIRS_0_research").click(function(e){
    //note that this click will be overridden by the popup.js

    //get new town_name
    var sNewTownName = jQuery("#IIRS_0_research_town_name_new").val();
    var oParameters  = [];

    if (!sNewTownName) {
      if (window.IIRS_0_message) window.IIRS_0_message(g_tFormNotValid);
      else alert(g_tFormNotValid);
    } else {
      oParameters.push({"name":"town_name", "value":sNewTownName});
      //set content
      IIRS_0_postAsForm(document.location, oParameters);
    }

    e.preventDefault();
    e.stopPropagation();
  });

  //----------------------------- auto-fill name from email
  oNameInput  = jQuery("#IIRS_0_name");
  oEmailInput = jQuery("#IIRS_0_email");
  oEmailInput.keyup(IIRS_0_emailChange)
    .click(IIRS_0_emailChange)
    .change(IIRS_0_emailChange)
    .bind('rightclick', IIRS_0_emailChange)
    .bind('paste', function(e){setTimeout(IIRS_0_emailChange,0);});

  //----------------------------- custom form validation
  jQuery("#IIRS_0_form_popup_location_general").submit(function(e){
    var jForm = jQuery(this);
    var rxEmail = /^[a-z0-9._%+-]+@[a-z0-9._-]+\.[a-z]+$/i;
    var bValidForm = true;

    //only check it if it has been sent through
    //the required check will catch it if it is empty
    if (oEmailInput.val() && !rxEmail.test(oEmailInput.val())) {
      if (window.IIRS_0_message) window.IIRS_0_message(g_tEmailNotValidFormat);
      else alert(g_tEmailNotValidFormat);
      jForm.addClass("IIRS_0_form_validation_fail");
      e.preventDefault();
      e.stopPropagation();
      bValidForm = false;
    } else {
      jForm.removeClass("IIRS_0_form_validation_fail");
    }

    return bValidForm;
  });

  jQuery("#IIRS_0_form_popup_domain_selection").unbind("submit").submit(function(e){
    var bValidForm = true;

    if (!(jQuery("input[name='domain_other']").val() || jQuery("input[name='domain']:checked").val())) {
      //failed checks
      if (window.IIRS_0_message) window.IIRS_0_message(g_tDomainRequired);
      else alert(g_tDomainRequired);
      e.preventDefault();
      e.stopPropagation();
      bValidForm = false;
    }

    return bValidForm;
  });
}

function IIRS_0_emailChange(e) {
  var sEmail         = oEmailInput.val();
  var sEmailBeforeAt = sEmail.replace(/@.*/g, '');
  var sAZEmail       = sEmailBeforeAt.replace(/[^a-z]/gi, ' ');
  var sCapsEmail     = sAZEmail.replace(/ ([a-z])/g, function(x){return ' ' + x[1].toUpperCase();});
  var sCapsEmail     = sCapsEmail.replace(/^([a-z])/g, function(x){return x[0].toUpperCase();});
  oNameInput.val(sCapsEmail);
}

//attach events on content change
jQuery(document).bind("IIRS_0_newContent", IIRS_0_attachRegistrationInteractionEvents);

