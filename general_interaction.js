//this static JavaScript file is valid for all scenarios
if (!window.jQuery) alert('jQuery required');

function IIRS_0_formCheckRequired(e) {
  var bValidForm = true;
  var jForm = jQuery(this);

  jForm.find(".IIRS_0_required").each(function(){
    var jRequiredFormElement = jQuery(this);
    if (!jRequiredFormElement.val() || jRequiredFormElement.hasClass("IIRS_0_hint")) {
      jRequiredFormElement.addClass("IIRS_0_validation_fail");
      bValidForm = false;
    } else {
      jRequiredFormElement.removeClass("IIRS_0_validation_fail");
    }
  });

  if (!bValidForm) {
    jForm.addClass("IIRS_0_form_validation_fail");
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    if (window.IIRS_0_message) window.IIRS_0_message(g_tFormNotValid);
    else alert(g_tFormNotValid);
  } else {
    jForm.removeClass("IIRS_0_form_validation_fail");
  }

  return bValidForm;
}

var g_sLastSetContentArguments, g_sThisSetContentArguments;
var g_sLastSetContentHTML, g_sThisSetContentHTML;

function IIRS_0_setContent(sPlace, sHREF, oParameters, fCallback) {
  //dynamically inject HTML/CSS/JS content in to a page or popup
  //only used in the JavaScript Widget scenario by this widgetloader.php
  //loading...
  jQuery(sPlace).html(g_sThrobber + ' page loading...');

  //indicate that the request result is to be displayed on a different website
  if (!oParameters) oParameters = [];
  oParameters.push({'name':'IIRS_widget_mode', 'value': 'true'});
  oParameters.push({'name':'langCode', 'value': g_sLangCode});

  //record for refresh and back purposes
  g_sLastSetContentArguments = g_sThisSetContentArguments;
  g_sThisSetContentArguments = arguments;

  var ajaxSetup = {
    url: sHREF,
    data: oParameters,
    type: "POST",
    async:false,
    success: function(data, textStatus, jqXHR){
      //jqXHR = jQuery >= 1.5
      jQuery(sPlace).html(data);
      g_sLastSetContentHTML = g_sThisSetContentHTML;
      g_sThisSetContentHTML = data;
      jQuery(sPlace).trigger("IIRS_0_newContent");
      if (fCallback) fCallback();
    },
    error: function(jqXHR, textStatus, errorThrown){
      //jqXHR = jQuery >= 1.5
      //TODO: what does error: return < jQuery 1.5?
      alert(g_tSystemError + "\n[" + textStatus + "]:\n[" + errorThrown + "]");
    }
  };
  setTimeout(function(){jQuery.ajax(ajaxSetup);}, 0);
}

function IIRS_0_postAsForm(sHREF, oParameters) {
  var sForm = "<form method='POST' action='" + sHREF + "'>\n";
  var oParam;
  for (var i in oParameters) {
    oParam = oParameters[i];
    sForm += "<input type='hidden' name='" + oParam.name + "' value='" + oParam.value + "'></input>";
  }
  sForm += "</form>";
  jQuery(sForm).submit();
}

function IIRS_0_newContentSetup(e) {
  jQuery("#IIRS_0_no_javascript").hide();
  jQuery("#IIRS_0_submit").removeAttr("disabled");
  jQuery("#IIRS_0 form").submit(IIRS_0_formCheckRequired);
  //this is overridden by the popup one if in widget mode
  jQuery(".IIRS_0_back").click(function(e){history.go(-1);});

  jQuery("#IIRS_0_list_selector li").click(function(e){
    jQuery("#IIRS_0_list_selector li").removeClass("selected");
    jQuery(this).addClass("selected");
  });
  jQuery("#IIRS_0_other").click(function(e){
    var jOtherLIs = jQuery(this).parent().find("li").not(this);
    jOtherLIs.removeClass("selected");
    jOtherLIs.find("input").removeAttr("checked");
  });
}

jQuery(document).bind("IIRS_0_newContent", IIRS_0_newContentSetup);

jQuery(document).ready(function(e){
  //for the plugin/module system and any initial load
  jQuery(this).trigger("IIRS_0_newContent");
});

