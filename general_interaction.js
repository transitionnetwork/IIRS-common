/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

// this static JavaScript file is valid for all scenarios
if (!window.jQuery) alert('jQuery required');

function IIRS_0_initialiseMaps() {
  if (jQuery(".IIRS_0_map:not(.initialised)").length) {
    // initialise only once
    // in case another plugin has included maps
    if (window.google) {
      IIRS_0_showMaps();
    } else {
      var callback = "IIRS_0_showMaps";
      var script   = document.createElement("script");
      script.type  = "text/javascript";
      script.src   = "https://maps.googleapis.com/maps/api/js?key=" + g_sGoogleAPIKey + "&callback=" + callback;
      document.body.appendChild(script);
    }
  }
}

var oCurrentInfoWindow;

function IIRS_0_showMaps() {
  jQuery(".IIRS_0_map:not(.initialised)").each(function(){
    var jThis         = jQuery(this);
    var fMapLatitude  = jThis.children(".location_latitude").text();
    var fMapLongitude = jThis.children(".location_longitude").text();
    var sZoom         = jThis.children(".zoom").text();
    var jMarkers      = jThis.children(".markers").children();
    var bFitBounds    = jThis.hasClass("IIRS_0_fit_bounds");

    // map
    var oMapOptions = {
      center: new google.maps.LatLng(fMapLatitude, fMapLongitude),
      zoom: ( sZoom ? parseInt(sZoom) : 8 )
    };
    var oMap = new google.maps.Map(this, oMapOptions);

    var oLatLngBounds = new google.maps.LatLngBounds();
    // var oWholePlanet  = new google.maps.LatLngBounds(new google.maps.LatLng(85, -180), new google.maps.LatLng(-85, 180));
    var oNE, oSW, iHeightDegrees, iWidthDegrees, iMaxDegrees;

    // markers
    jMarkers.each(function(){
      var jTI            = jQuery(this);
      var fLatitude      = jTI.children(".location_latitude").text();
      var fLongitude     = jTI.children(".location_longitude").text();
      var sName          = jTI.children(".name").text();
      var sStatus        = jTI.children(".status").text();
      var sCustomMarker  = jTI.children(".custom_marker").text();
      //TODO: categories of TI and associated markers (same name as category)
      var sDefaultMarker = (sStatus == "official" ? "official" : "muller");
      var sMarkerName    = (sCustomMarker ? sCustomMarker : sDefaultMarker);
      var sImage         = g_sIIRSURLImageStem + "/" + sMarkerName + ".png";
      var oLatLng        = new google.maps.LatLng(fLatitude, fLongitude);
      var oMarker        = new google.maps.Marker({
        position: oLatLng,
        map:      oMap,
        title:    sName,
        icon:     sImage
      });
      var oInfoWindow = new google.maps.InfoWindow({
        content: '<div id=\"content\"><b>' + sName + '</b><br/><a href="/IIRS/view?ID=' + (jTI.children(".native_ID").text()) + '">' + g_tViewFullProfile + '</a></p></div>'
      });
      google.maps.event.addListener(oMarker, "click", function() {
        if (oCurrentInfoWindow) oCurrentInfoWindow.close();
        oCurrentInfoWindow = oInfoWindow;
        oInfoWindow.open(oMap, oMarker);
      });
      oLatLngBounds.extend(oLatLng);
      if (window.console) console.log("adding TI [" + sName + "]")
    });

    google.maps.event.addListener(oMap, "click", function() {
      if (oCurrentInfoWindow) oCurrentInfoWindow.close();
      oCurrentInfoWindow = null;
    });

    if (bFitBounds) {
      if (window.console) console.log(oLatLngBounds);
      oNE = oLatLngBounds.getNorthEast();
      oSW = oLatLngBounds.getSouthWest();
      iHeightDegrees = oNE.lat() - oSW.lat();
      iWidthDegrees  = oNE.lng() - oSW.lng();
      iMaxDegrees    = (iHeightDegrees > iWidthDegrees ? iHeightDegrees : iWidthDegrees);
      if (iMaxDegrees > 8) {
        // the initiatives being shown span more than 8 degrees of the planet surface
        // so show the whole area
        oMap.fitBounds(oLatLngBounds);
      } else {
        // initiatives are in a small area, so show the whole country
        oMap.setCenter(oLatLngBounds.getCenter());
        oMap.setZoom(5);
      }
    }

    jThis.addClass("initialised");
  });
}

function IIRS_0_formCheckRequired(e) {
  // returns true or false
  // TODO: needs to return a validation failure string
  var bValidForm = true;
  var sValidationFailures = '';
  var jForm = jQuery(this);

  jForm.find(".IIRS_0_required").each(function(){
    var jRequiredFormElement = jQuery(this);
    var sName = jRequiredFormElement.attr("name");
    var sType = jRequiredFormElement.attr("type");
    var sValue;

    switch (sType) {
      case "radio": {
        sValue = jQuery("input:radio[name=" + sName + "]:checked").val();
        break;
      }
      default: {sValue = jRequiredFormElement.val();}
    }

    if (!sValue || jRequiredFormElement.hasClass("IIRS_0_hint")) {
      jRequiredFormElement.addClass("IIRS_0_validation_fail");
      bValidForm = false;
      sValidationFailures += sName + " is required"; // TODO: needs translating
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
  oParameters.push({'name':'lang_code', 'value': g_sLangCode});

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
  var oParam, oInput;
  var oForm = document.createElement("form");
  oForm.setAttribute("method", "POST");
  oForm.setAttribute("action", sHREF);

  for (var i in oParameters) {
    oParam = oParameters[i];
    sName  = oParam.name.replace(/"/, "&quot;");
    sValue = oParam.value.replace(/"/, "&quot;");
    oInput = document.createElement("input");
    oInput.setAttribute("name",  sName);
    oInput.setAttribute("value", sValue);
    oForm.appendChild(oInput);
  }

  oInput = document.createElement("input");
  oInput.setAttribute("type",  "submit");
  oInput.setAttribute("name",  "submit");
  oInput.setAttribute("value", "submit");
  oForm.appendChild(oInput);
  document.body.appendChild(oForm);

  oInput.click();
}

function IIRS_0_newContentSetup(e) {
  jQuery(".IIRS_0_no_javascript").hide();
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
  IIRS_0_initialiseMaps();
}

jQuery(document).bind("IIRS_0_newContent", IIRS_0_newContentSetup);

jQuery(document).ready(function(e){
  //for the plugin/module system and any initial load
  jQuery(this).trigger("IIRS_0_newContent");
});
