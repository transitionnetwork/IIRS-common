<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<h3>framework independent abstraction layer</h3>
<p>
  These are functions that any framework plugin / module wrapper needs to implement to give the IIRS_common code
  access to the functions of the framework. For example the host translation system.
</p>
<h4>//--------------------------------------------------- optional functions</h4>
<ul>
  <li>IIRS_0_translation($string_to_translate)</li>
  <li>IIRS_0_input($sKey)</li>
  <li>IIRS_0_setting($setting)</li>
  <li>IIRS_0_TI_search_result_already_registered($town_name, $location_latitude, $location_longitude, $location_description)</li>
  <li>IIRS_0_current_path()</li>
  <li>IIRS_0_http_request($url)</li>
  <li>IIRS_0_redirect($url)</li>
  <li>IIRS_0_set_message($message)</li>
  <li>IIRS_0_details_TI_page()</li>
  <li>IIRS_0_TIs_all($page_size = 0, $page_offset = 0)</li>
  <li>IIRS_0_available_languages()</li>
  <li>IIRS_0_URL_view_TI()</li>
  <li>IIRS_0_URL_edit_TI()</li>
  <li>IIRS_0_HTML_editor()</li>
</ul>
<h4>//--------------------------------------------------- querying</h4>
<ul>
  <li>IIRS_0_TIs_nearby()</li>
  <li>IIRS_0_details_user()</li>
  <li>IIRS_0_details_TI_user()</li>
</ul>
<h4>//--------------------------------------------------- registering</h4>
<ul>
  <li>IIRS_0_TI_add_user()</li>
  <li>IIRS_0_TI_verify_add_TI()</li>
  <li>IIRS_0_TI_add_TI()</li>
  <li>IIRS_0_TI_update_TI()</li>
  <li>IIRS_0_TI_update_user()</li>
  <li>IIRS_0_next_initnumber()</li>
</ul>
<h4>//--------------------------------------------------- authentication</h4>
<ul>
  <li>IIRS_0_logged_in()</li>
  <li>IIRS_0_login()</li>
</ul>
