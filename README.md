# Getting Started:
1. git clone the repository.
1. copy the rest_login.php file to rest_login.local.php and update the local file with your SuiteCRM credentials.
1. make a new directory called mail, in your repository directory, so the script has a place to put the emails it will be receiving.
1. Create an account with Mandrill: [Signup](https://mandrillapp.com/signup/)
1. After setting up, create a new [inbound domain](https://mandrillapp.com/inbound), use follow the instructions in the View Setup Instructions to configure your domain to send the emails to mandrill's servers
1. This is just your domain. For example, if your planned email address is archive@crm.example.com, your inbound domain would be crm.example.com. Mandrill recommends using inbound.example.com, or something else that would never receive a normal email.
1. Once your Test DNS Settings are saying MX: valid, select the Routes option from the dropdown arrow on the far right.
1. Add a new route and create your new archival email address (ex. archive@crm.example.com), then put in your Post To URL (ex. http://example.com/suitecrm-archive-email)
1. Now when you send an email to archive@crm.example.com, mandrill will send a post request, with a json object of the email, to your URL.

