# custom-invite-and-login
Administer the custom registration and login plugin:

General background:
The plugin was altered from an open source invite plugin and extended to use its functionality and include details for a custom login page and then further extended to include a report of invitees included in the database. In the coming days it will be extended further still, to allow for a CSV download to match the import CSV of the client’s email software.

Registration:

Navigate using WordPress’ left-hand column nav, to the ‘invite users’ main options page. Paste or type invitee’s email addresses, one per line in the first text-area. When complete, click “Send Invitation”. The page will refresh and report which users have been added to the invites list.
Further down the page is a table of all invited users currently in this database.
CSV download to come.

Login page theming:

Select which page acts as a login from the top dropdown list. The plugin assumes you will choose a log-in page with a custom template - using Template Name: ‘Authorised’.
The plugin uses WordPress ‘slug’ as an identifier. Data is not saved as an object, so
if page slugs or other data are changed, please re-edit these settings to match - they will not update automatically and will need all relevant pages to exist within the site already, in order to be used.

Beneath the login page dropdown, there’s a list of all the published pages on the site. Please select which of these should be viewable without logging in. 

Click save settings to save these choices.

Login scripts can be found within the TLS-includes folder in the Law Society’s custom theme. 
