# Example texts

##request_access_requested

###Plaintext
Your request has been received in good order.
We will process it as soon as possible.

### html

<p>Your request has been received in good order.</p>
<p>We will process it as soon as possible.</p>

##request_access_requested_another

###Plaintext
Would you like to submit another request?

### html
<p>If you would like to submit another application, please click the button below</p>

##request_access_email_subject

###Plaintext
Additional access request

##request_access_email_body

### html
```
<p> Hi Helpdesk, </p>
<p> <strong> User wants to request access to digital exam instruments </strong> </p>

<p> User ###REQUESTING_USER_FULL_NAME### (###REQUESTING_USER_EMAIL###) wants to request access for ###TARGET_USER_FULL_NAME### (###TARGET_USER_EMAIL###) </p>

<p> The access is for "###REQUESTING_USERGROUP###" and is from ###START_DATE### to ###END_DATE### </p>

<p> User now has these expiring groups: </p>

<p> ### USER_EXPIRING_GROUPS ### </p>

<p> Click below to request: </p>

<ul>
     <li> ###LINK_ALLOW_ACCESS### </li>
     <li> ###LINK_DENY_ACCESS### </li>
</ul>

<p> Sincerely, </p>
<p> The website </p>
```

##request_access_form

### html
```
<p>Here you can request additional access.</p>
```

##request_access_grant_access

### html
```
<p> The request has been approved. 
User (###TARGET_USER_EMAIL###) will receive an email about this. </p>
```

##request_access_account_not_valid

### html
```
<p> Your account cannot be validated. Please contact the helpdesk. </p>
```

##request_access_deny_access

###Plaintext
Toegang is geweigerd en de aanvrager is geïnformeerd.

##request_access_denied_source_body

### html
```
<p> Dear ###requesting_user_full_name###, </p>

<p> Additional access for "###requesting_usergroup###" requested for ###TARGET_USER_FULL_NAME### (###TARGET_USER_EMAIL###) has been denied. </p>
<p> Refusal Reason: </p>
<p> ###USER_REQUEST_DENIED_REASON### </p>
<p> Sincerely, </p>

<p> The website </p>
```

##request_access_granted_source_body

### html
```
<p> Dear ###SOURCE_USER_FULL_NAME###, </p>

<p> Access to "###requesting_usergroup###" for ###TARGET_USER_FULL_NAME### (###TARGET_USER_EMAIL###) has been approved and immediately active. </p>
<p> The access is valid from ###START_DATE### to ###END_DATE###, after which it will expire automatically. </p>

<p> Sincerely, </p>

<p> The website </p>
```

##request_access_granted_target_body

### html
```
<p> Dear ###TARGET_USER_FULL_NAME###, </p>

<p> You can now access "###requesting_usergroup###".
The access is valid from ###START_DATE### to ###END_DATE###, after which it expires automatically.
You can extend your access by re-applying in due course. </p>

<p> Sincerely, </p>

<p> The website </p>
```

##request_access_invalid_request
###Plaintext
An error has occurred.
Please try again or if the problem persists please contact us.

##request_access_denied_source_subject
###Plaintext
Access is denied

##request_access_granted_target_subject
###Plaintext
Access is approved

##request_access_granted_subject
###Plaintext
Access is approved

##request_access_granted_target_body

### html
```
<p> Dear ###TARGET_USER_FULL_NAME###, </p>

<p> You will now have access to "###USERGROUP###". </p>
<p> The access is valid from ###START_DATE### to ###END_DATE###, after which it expires automatically.
You can extend your access by re-applying in due course. </p>

<p> Sincerely, </p>

<p> The website </p>
```
