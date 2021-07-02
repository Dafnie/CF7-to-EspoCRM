# espocrm_integrator

## Wordpress CF7 to EspoCRM - send submission
Contributors: Carsten Gjedde
Donate link: https://contactform7.com/donate/
Tags: contact, form, contact form, crm, espocrm
Requires at least: 7.2
Tested up to: 5.7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


### Description

Contact form 7 to EspoCRM is a plugin for CMS [Wordpress](https://wordpress.org)
The plugin adds a tab to the [CF7 plugin](https://contactform7.com/)(Contact Form 7) settings, giving an option to send the CF7 formdata to your EspoCRM page.
- Integrate one or many CF7 forms.
- Uses [EspoCRM Api key](https://docs.espocrm.com/development/api/#authentication) to secure your POST data
- Pair the fields from Wordpress CF7 to EspoCRM


### Documentation

1. Install and activate [Contact form 7](https://da.wordpress.org/plugins/contact-form-7/) plugin
2. Install and activate [Wordpress to EspoCRM](https://da.wordpress.org/plugins/EspoCRM_integration/)
3. Create a Concact form 7 form or edit a existing one and save to fetch form fields
4. Click the EspoCRM Integration tab.
5. Enable 'Send to EspoCRM'
5. Enter the full URL to your EspoCRM instance
6. Enter the API Key from your EspoCRM instance
(Go to http://YOUR_ESPOCRM.COM//#ApiUser. Create a API user. The Authentication Method has to be API Key
7. Select the main entity from your EspoCRM enstance
(Save to fetch the fieldnames from your EspoCRM)
8. Map the field from CF7 to your EspoCRM
(If there are missing some formfields or EspoCRM fields, hit save)
9. *Optional* - Add some static field type the value and map it to your EspoCRM field.
10. Select Form field for duplicate search.
(The entity in 7. will not be created is the value in this form filed allready exits in your EspoCRM)
11. Select a child entity type
(the parent type to this will be the entity selected in 7.)
12. Map the fields as previously described in 8. to 9. 

#### Advanced use
**Assign the entity to a user**
1. Go to http://YOUR_ESPOCRM.COM/#User and navigate to the user. The slug of the URL is the userId
(Somethink like "5fdf45ce42eee0cbb")
2. In the EspoCRM Integration tab add a static field. Enter the UserId from above.
3. Map the value to assignedUserId

<strong>*TODO:*</strong>
- Add to errorlog when REST fail
- Optional send email when REST fail
- Send wordpress data to EspoCRM field
- Optional assign entity to a team 

### Requirements

* php v7.2 or later
* Wordpress v5.0 or later
* plugin [Contact form 7](https://da.wordpress.org/plugins/contact-form-7/)


### How to report a bug

Create an issue [here](https://github.com/Dafnie/espocrm_integrator/issues)

### How to contribute

You are free to join this development of this plugin. Create a new branch or make a feature request.

Branches:
* *develop* - Testing new features. new features should be pushed to this branch;
* *master* – develop branch

### License

Wordpress CF7 to EspoCRM is published under the GNU GPLv3
