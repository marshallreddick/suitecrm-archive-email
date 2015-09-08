# Getting Started:

1. Create an account with Mandrill: [Signup](https://mandrillapp.com/signup/)
1. After setting up, create a new [inbound domain](https://mandrillapp.com/inbound), use follow the instructions in the View Setup Instructions to configure your domain to send the emails to mandrill's servers
1. Once your Test DNS Settings are saying MX: valid, select the Routes option from the dropdown arrow on the far right.
1. Add a new route and create your new archival email address (ex. archive@crm.example.com), then put in your Post To URL (ex. http://example.com/suitecrm-archive-email/)
1. Now when you send an email to archive@crm.example.com, mandrill will send a post request, with a json object of the email, to your URL.

