<pre>
--------------------------------------- IIRS first phase production TODO:
send password in email* (wordpress?) : initial one time login? wordpress settings?

-------------- user functionality / registration: 1 day
send password in email* (wordpress?)

-------------- functionality in registration: 2-3 days
is location data county sensitive?* -> no it is defaulting to USA (of course)
  need to send through the country: we have it now....?
  users language is not necessarily their desired registration location
  their REMOTE_ADDR may well be though...
  Oakley returns ONLY the American one, Oakley, UK does return the UK one
    add a note about user entry specificity?

-------------- finalising
Akismet spam protection
  what to do if it is marked as spam?
  use Annesley robot spotting
error reporting (only user based error reports for now)
  User updating: duplicate emails and user names are not being checked
  TI updating: duplicate places and names are not being checked
re-check coding standards: move everything to Wordpress standard for now (see apply_coding_standards.php)
  sandbox all Javascript function names
fix local sendmail email system to test emailing!
  what format do we want the email?
  configurable? -> use the translation system
testing, testing, testing...
ensure that a "place" has been selected.
final redirect -> list / mapping?
HTML editor necessary OR just replace with <p>s
  summary entry -> paragraphs? pre? word limit suggestion?
Luis: to write own template
  put this in the documentation
  with the example template
Auto-load the appropriate text-domain from Wordpress/IIRS/languages (it_IT)

-------------- questions:
http://annesley.parrot.transitionnetwork.org/

-------------- other widgets: 3-6 days
other widgets (see spec at https://www.transitionnetwork.org/blogs/ed-mitchell/2013-08/international-initiative-registration-service-workflows):
  mapping
    infowindows on map on their own marker!
    make maps init generic
    show map on view, but not on list
    integrate / require
      wp-geo https://wordpress.org/plugins/wp-geo/
      geo-mashup https://wordpress.org/plugins/geo-mashup/

-------------- deployment / testing: 1 day
deployment on to live
commenting / code documentation / re-visit
cross browser testing

--------------------------------------- IIRS SECOND phase production TODO:
the_title for IIRS full TI view (not used before)
integrate with a mapping plugin
recommend during installation twig and post_formats
email address validation in JS widget still submits the form
provide standard WordPress display template
wordpress settings link up (lot of work surprisingly -> feedback first, some of framework is there)
auto / or in settings.page setup a generic menu
version control and updating information (upload the plugin to the Wordpress repository)
WISYWYG summary editor (paragraphs, pictures, etc.)
  this can be done manually using the backend Wordpress editor for the initial registrations

Plugins and widgets:
  register themselves with TN.org
    domain, type (widget/plugin), host system (WP / Drupal / x), last accessed, last amalgamated, altered, new fields
    ask before TN.org registration happens
    pull sync + push sync (just in case)
    use framework content-type
    implement Drupal functionality first

cache:
  server location lookups and things
list
  auto-ajax-pager on scroll to bottom (not enough registrations to warrant this)
    requires paging functions on server...
  restrict to current country?
    -> no, all registrations on this server + all relevant country registrations on TN.org
    that requires the XML sync to be working
  order by date added desc
Wordpress
  siderbar widget?
  make sure permalinks are set on with an valid .htaccess in the plugin activation
  check Wordpress geotag functionality on posts
    maybe optionally detect presence of popular geotag plugins
    maybe make it a requirement?
  shortcodes for WordPress rather than pretending its a "node"?
  move everything in to the class.iirs.php
  feed wordpress plugin layout back in to the Drupal plugin: abstract wp_enqueue_javascript() = drupal_add_javascript()
  settings admin page
  internationalise the plugin
search screen DROPPED
what happens if the uid owns several TIs?
json_encode PHP4
Wordpress 3.9.1
  show_page() using filters
  menu_registration
  <%=IIRS%> in-HTML key replaceement widgets
  display settings and other settings
multi form buttons submit to prevent robots
MX domain validation check
maximum results from google... and advice to narrow search
map zoom based typeahead for town area entry
refresh stopped working: makes loads of requests
back buttons not working
  double back buttons are failing
  back buttons re-submit form in widget: creating already in use warnings: use last HTML instead
throbber not spinning: move to asynchronous calls
TIs_nearby() calcs - important to do this for future proofing of concepts*
  use the view::load('nearby me') and set the Location: distance / proximity filter
better error reporting when TI save fails
validation messages combine and show in a DIV popup
take some style CSS out of Drupal plugin in to TN.org
calcs for if already registered
  using: TIs_nearBy()*
actual translations: where and how?
status will change => registered / active
add page titles
return a proper 404 if the page is not found
installation checks for jQuery
test that all functions are PHP 5.0 compatible (install on PHP 5.0.0 and see if works)
jQuery these event handlers need to be added once, not several times!
general.css is included in popup.php
  widgetloader.php loads popup.php, index.php and should load general.css also
  we need a multi-loader in one call
makes IIRS_0_hint generic
  and fix the initial value on back...
google not loaded in the popup system for the image search
get hook_menu() to deal with index.php and IIRS/registration better
Wordpress 4.0 plugin
content-type => features => IIRS.install
  or add dependency on initiative-profile CCK type?
IIRS flow:
  browse grass-roots strap lines
    sense of movement
  calculate the stage from the activity (number google results)
    from URL
    from websearch
Plugins, Modules & widgets should all:
  expose XML data for anyone amalgamation

--------------------------------------- IIRS thinking:
option 1: javascript widget
  uses the same form creation and sync logic as the shared plugin code
  uses javascript hooks on client site to alter functionality
  confiusion: search, login, linkage, topology
option 2: PHP plugin for WordPress, Drupal, etc.
  nat-hub must accept responsibility for owning data and users
  email addresses and passwords are sensitive

--------------------------------------- IIRS first phase production:
XML basic universal form elements definition
  content-type (views) => XML (Javascript / PHP) => HTML
  the plugin and the TN.org use this XML => HTML form code to produce the same HTML
  client can write Javascript or PHP hook functions to alter the form

</pre>