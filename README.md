# bpn_request_access

Allows to request for access

## Configuration

Set values in plugin configuration for TypoScript template for

* verificationCode.secureKey
* verificationCode.numberOfSecondsBeforeExpiration
* email.validatorName
* email.validatorEmail

## Variable texts

### Actors 
Notice the following actors are in play:

1. The Granter, the person granting or denying access.
2. The Requester, the person making the request for access for Receiver
3. The Reciever, the person for whom the access is requested.

### Required labels

Add variable texts records with the following labels

* `request_access_requested`<br />
  The text to tell Requester, the access was requested
* `request_access_requested_another`<br />
  The text to tell Requester to request another
* `request_access_email_subject`<br />
  The subject of the e-mail to request access (for Granter)
* `request_access_email_body` [1]<br />
  The body of the e-mail to request for access (for Granter)
* `request_access_form`
  The intro text of the request form for Requester
* `request_access_grant_access` [2]<br />
  The text to display when Granter has granted the access.
* `request_access_account_not_valid`<br />
  The text displayed Requester when the selected user is not valid
* `request_access_deny_access`
  The text displayed when Granter has rejected the access for the Receiver
* `request_access_denied_source_subject`
  The email subject for Requester when the access was denied for Reciever.
* `request_access_denied_source_body` [3]<br />
  The email body for Requester when the access was denied for Reciever.
* `request_access_granted_source_body`[2]<br/>
  The email body to send to Requester the access was granted.
* `request_access_granted_target_body`[2]<br/>
  The email body to send to Receiver the access was granted.
* `request_access_granted_body`[2]<br/>
  The email body for Reciever when the access was requested for Reciever.
* `request_access_granted_subject`<br/>
  The email subject for Reciever when the access was requested for Reciever.
* `request_access_invalid_request` <br />
  The request was invalid message

[1] Accepts the following parameters in the text:

* `###requesting_user_email###`
* `###target_user_full_name###`
* `###target_user_email###`
* `###target_user_full_name###`
* `###requesting_usergroup###`
* `###user_expiring_groups###`
* `###start_date###`
* `###end_date###`
* `###link_allow_access###` Required!!!
* `###link_deny_access###` Required!!!

[2] Accepts the following parameters in the text:

* `###requesting_user_email###`
* `###target_user_full_name###`
* `###target_user_email###`
* `###target_user_full_name###`
* `###requesting_usergroup###`
* `###user_expiring_groups###`
* `###start_date###`
* `###end_date###`

[3] Accepts the following parameters in the text:

* `###requesting_user_email###`
* `###target_user_full_name###`
* `###target_user_email###`
* `###target_user_full_name###`
* `###requesting_usergroup###`
* `###start_date###`
* `###end_date###`
* `###user_request_denied_reason###`

## Placeholders in above text:

* `###requesting_user_email###` the email adres of the requester
* `###target_user_full_name###` the name of the requester
* `###target_user_email###` the email adres of the reciever
* `###target_user_full_name###` the name of the reciever
* `###requesting_usergroup###` the name of the usergroup the request was done for
* `###user_expiring_groups###` receivers current expiring groups
* `###start_date###` start date
* `###end_date###` end date
* `###link_allow_access###` the link to approve
* `###link_deny_access###` the link to reject
* `###user_request_denied_reason###` The reason given for denying the access.

## Error codes:

* 785325 Request was already processed
* 790051 Request was not found

## Page handles

When storing new request, some handles need to be created in the back end.

* page_handle_request_access
  This handle should be added to the (folder) page that stores the requests.

## Example variable texts

See [examples in Dutch](nl.examples.md) or <br />
See [examples in English](en.examples.md) or <br />


## Thanks to 
Frans van der Veen.
<br/>May the force be with you!

Ported to TYPO3 10.4 by Sjoerd Zonneveld
