<h3>custom pages</h3>
<?php
$permalink_structure = get_option('permalink_structure');
$disabled            = '';
if ($permalink_structure == '') {
  // there will always be an admin message at the top about permalinks if they are disabled
  $disabled = ' class="IIRS_0_disabled" disabled="1" onclick="alert(\'permalinks are off\');return false;" ';
}
?>
<p>
  The following URLs can be used for the user to carry our IIRS work.
  Note that the IIRS plugin will not setup any menus or links to it's functionality for you.
  User the Menu Editor to setup these menu links where you need them.
  Or embed the links directly in your posts / content using &lt;a href="/IIRS/[link]"&gt;[text of link]&lt;/a&gt;.
</p>
<ul>
  <li><a <?php print($disabled); ?> target="_blank" href="/IIRS/registration/">/IIRS/registration/</a> the registration system</li>
  <li><a <?php print($disabled); ?> target="_blank" href="/IIRS/edit/">/IIRS/edit/</a> edit the Transition Initiative you have already registered</li>
  <li><a <?php print($disabled); ?> target="_blank" href="/IIRS/view/">/IIRS/view/</a> view the Transition Initiative you have already registered</li>
  <li><a <?php print($disabled); ?> target="_blank" href="/IIRS/mapping/">/IIRS/mapping/</a> a map of all Transition Initiatives registered on this server</li>
  <li><a <?php print($disabled); ?> target="_blank" href="/IIRS/list/">/IIRS/list/</a> a list of all Transition Initiatives registered on this server</li>
  <!-- li><a <?php print($disabled); ?> target="_blank" disabled="1" href="/IIRS/search/">/IIRS/search/</a> search the registered initiatives</li -->
  <!-- li><a <?php print($disabled); ?> target="_blank" href="/IIRS/export/">/IIRS/export/</a> password protected XML export system</li -->
  <!-- li><a <?php print($disabled); ?> target="_blank" href="/IIRS/import/">/IIRS/import/</a> password protected XML import system</li -->
</ul>
