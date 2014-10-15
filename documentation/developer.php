<h2>Developer documentation</h2>
<img id="IIRS_0_TN_logo" src="/IIRS/images/transition2_logo.png" />
<p>
  Developer documentation, yeah!
  Other files:
</p>
<ul>
  <li><a href="TODO">TODO file</a></li>
  <li><a href="README">README file</a></li>
</ul>

<h3>language, translation, PO files</h3>
<p>
  Translatable strings can be parsed from the code using the IIRS code parser tool, see the links below.
  <a target="_blank" href="https://www.gnu.org/savannah-checkouts/gnu/gettext/manual/html_node/PO-Files.html">.PO files</a> can then be generated from the discovered strings.
  This is similar to the WordPress translation for plugins and it's <a target="_blank" href="http://wpml.org/">WPML</a> plugin system.
  Here is <a href="/wp-content/plugins/IIRS/languages/IIRS-en_EN.po">the current English .PO file</a>.
</p>
<ul>
  <li>
    view source (PO format file): <a href="/wp-content/plugins/IIRS/IIRS_common/read_translations.php?format=PO&amp;start_directory=/var/www/wordpress/tnv3/wp-content/plugins/IIRS">PO format output</a><br/>
    Save the contents of this output in to the Wordpress languages/IIRS-en_EN.po<br/>
    Don't forget to generate the .mo afterwards at the command line with
    <pre>
      msgfmt IIRS-en_EN.po -o IIRS-en_EN.mo
    </pre>
  </li>
  <li>
    view source (PHP file!): <a href="/wp-content/plugins/IIRS/IIRS_common/read_translations.php?format=WPML&amp;start_directory=/var/www/wordpress/tnv3/wp-content/plugins/IIRS">WPML Wordpress translations</a><br />
    Save the contents of this output in to the Wordpress IIRS Wordpress plugin file generated_fake_translations.php.<br/>
    Then use <a target="_blank" href="http://wpml.org/">WPML</a> to
    <a href="/wp-admin/admin.php?page=sitepress-multilingual-cms/menu/theme-localization.php">scan for translations</a> and import all the strings.
  </li>
</ul>

<?php include('abstraction-layer.php'); ?>

<h3>coding standards and analysis</h3>
<p>
  Here is a handy <a href="/wp-content/plugins/IIRS/IIRS_common/apply_coding_standards.php">coding standards analyser</a>
  for the plugin.
</p>
