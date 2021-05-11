# Voorbeeld teksten

##request_access_requested

###Plaintext
Wij hebben uw verzoek in goede staat ontvangen.
Wij zullen het zo spoedig mogelijk in behandeling nemen. 

### html
```
<p>Uw aanvraag is ontvangen.</p>
<p>Wij zullen het zo spoedig mogelijk in behandeling nemen.&nbsp;</p>
```

##request_access_requested_another

###Plaintext
Wilt u nog een aanvraag indienen?

### html
```
<p>Als u nog een aanvraag wilt indienen, dan kunt u op de onderstaande knop klikken.&nbsp;</p>
```

##request_access_email_subject

###Plaintext
Extra toegang aanvraag

##request_access_email_body

### html
```
<p>Hoi Helpdesk,</p>
<p><strong>Gebruiker vraagt extra toegang aan</strong></p>

<p>Gebruiker ###REQUESTING_USER_FULL_NAME### (###REQUESTING_USER_EMAIL###) wil toegang aanvragen voor ###TARGET_USER_FULL_NAME### (###TARGET_USER_EMAIL###)</p>

<p>De toegang is voor "###REQUESTING_USERGROUP###" en geldt van ###START_DATE### tot ###END_DATE###</p>

<p>Gebruiker heeft nu deze verlopende groepen:</p>

<p>###USER_EXPIRING_GROUPS###</p>

<p>Klik hieronder om het verzoek te:</p>

<ul>
    <li>###LINK_ALLOW_ACCESS###</li>
    <li>###LINK_DENY_ACCESS###</li>
</ul>

<p>Met vriendelijke groet,</p>
<p>De website</p>
```

##request_access_form

### html
```
<p>Hier kunt u voor uzelf een aanvraag indienen.&nbsp;</p>
```

##request_access_grant_access

### html
```
<p>De aanvraag is goedgekeurd. Gebruiker (###TARGET_USER_EMAIL###) krijgt hierover een e-mail.</p>
```

##request_access_account_not_valid

### html
```
<p>Uw account kan niet worden gevalideerd. Neem contact op met de helpdesk.</p>
```

##request_access_deny_access

###Plaintext
Toegang is geweigerd en de aanvrager is geïnformeerd.

##request_access_denied_source_body

### html
```
<p>Beste ###requesting_user_full_name###,</p>

<p>Extra toegang voor "###requesting_usergroup###" aangevraagd voor ###TARGET_USER_FULL_NAME### (###TARGET_USER_EMAIL###) is geweigerd.</p>
<p>Reden weigering:</p>
<p>###USER_REQUEST_DENIED_REASON###</p>
<p>Met vriendelijke groet,</p>

<p>De website</p>
```

##request_access_granted_source_body

### html
```
<p>Beste ###SOURCE_USER_FULL_NAME###,</p>

<p>De toegang tot "###requesting_usergroup###" voor ###TARGET_USER_FULL_NAME### (###TARGET_USER_EMAIL###) is goedgekeurd en direct actief.</p>
<p>De toegang is geldig van ###START_DATE### tot ###END_DATE###, waarna deze automatisch verloopt.</p>

<p>Met vriendelijke groet,</p>

<p>De website</p>
```

##request_access_granted_target_body

### html
```
<p>Beste ###TARGET_USER_FULL_NAME###,</p>

<p>U heeft vanaf nu toegang tot "###requesting_usergroup###". 
De toegang is geldig van ###START_DATE### tot ###END_DATE###, waarna deze automatisch verloopt. 
U kunt uw toegang verlengen door te zijner tijd opnieuw een aanvraag in dienen.</p>

<p>Met vriendelijke groet,</p>

<p>De website</p>
```

##request_access_invalid_request
###Plaintext
Er is een fout opgetreden.&nbsp;
Probeer het opnieuw of als het probleem zich blijft voordoen, neem dan contact op.

##request_access_denied_source_subject
###Plaintext
Toegang is geweigerd

##request_access_granted_target_subject
###Plaintext
Toegang is goedgekeurd

##request_access_granted_subject
###Plaintext
Toegang is goedgekeurd

##request_access_granted_target_body

### html
```
<p>Beste ###TARGET_USER_FULL_NAME###,</p>

<p>U heeft vanaf nu toegang tot "###USERGROUP###".</p>
<p>De toegang is geldig van ###START_DATE### tot ###END_DATE###, waarna deze automatisch verloopt. 
U kunt uw toegang verlengen door te zijner tijd opnieuw een aanvraag in dienen.</p>

<p>Met vriendelijke groet,</p>

<p>De website</p>
```
