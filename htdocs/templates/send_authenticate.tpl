<!-- BEGIN PAGE_HEADER -->
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Emails Sent</title>
<link rel=stylesheet href={CSS_FILE}>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<script language="JavaScript">
function popup(url) {
window.open(url, '', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1');
}
</script>
<body>
{LOGOS}
<!-- END PAGE_HEADER -->
<!-- BEGIN PAGE_FOOTER -->
<table align=center width=80%>
<tr><td align=center>
<!-- BEGIN AUTHENTICATION_SENT -->
<h1>Authentication emails sent</h1>
<p>
Two emails have been sent to {EMAIL_ADDRESS} one as plain text the
other as html, if you don't receive the html email or have
problems following the authentication link in the html email
( the one with the word html in the email subject ) 
please use the plain text authentication email & all future
trip matches will be delivered as plain text.
</p>
<!-- END AUTHENTICATION_SENT -->
<!-- BEGIN TEST_TRIPMATCH_EMAILS_SENT -->
<h1>Test Tripmatch Emails Sent</h1>
<p>
Two test tripmatch emails have been sent to {EMAIL_ADDRESS} 
one as plain text the other as html.
If you fail to receive the html email please set
the email style to plain text which is more likely
to get through spam filters.
If you prefer the plain text email format do likewise.
</p>
<!-- END TEST_TRIPMATCH_EMAILS_SENT -->
<p>
We have noticed that plain text emails are less likely to
be delivered into the junk email folder. 
<!-- BEGIN SPAM_PRECAUTIONS -->
We have also noticed that hotmail frequently doesn't deliver html emails
at all.
<!-- END SPAM_PRECAUTIONS -->
<!-- BEGIN COMPLETE_REGISTRATION --> 
Please follow the link in the appropriate email to complete registration
which verifies that the email address you gave is genuine.
<!-- END COMPLETE_REGISTRATION -->
<!-- BEGIN FORGOT_PASSWORD --> 
Please follow the link in the email to reset your users password.
<!-- END FORGOT_PASSWORD -->
</p>
<p><b>If you fail to receive any emails</b></p>
</td></tr>
<tr><td>
<li>Check your bulk or junk email folder, if the email
ends up in the bulk or junk email folder mark it as not spam
so that your email service will be trained to recognise TravelSmart
emails as important.</li>
<li>
You are also advised to put the following email addresses
in your contact list in & mark them as 
safe senders to improve the likelyhood that tripmatch emails etc. will be 
delivered to your Inbox,
{TRIPMATCH_EMAIL_ADDRESS}, {ADMINISTRATOR_EMAIL_ADDRESS}, {DEVELOPER_EMAIL_ADDRESS}, {ENQUIRIES_EMAIL_ADDRESS}.
</li>
<li>
Even if you dont receive emails as long as you are authenticated 
you will be able to log onto the website
& view saved matches in the control centre & you will see if anybody
has attempted to initiate a carpool with you.
If you have initiated a carpool by email & you've got no response
within a week contact the person by phone.
</li>
<li>If all else fails email the TravelSmart Administrator <a href="javascript:;" onclick=popup('send_email.php?email_to={ADMINISTRATOR_EMAIL_ADDRESS}&email_subject=travelsmart%20-%20authentication%20problem')>by clicking here</a> or by emailing from the email address you wish to use to {ADMINISTRATOR_EMAIL_ADDRESS} & we will have authenticated your email address.
</li>
</p>
</td></tr>
<tr><td align=center>
<a href="index.php">Back To Login Page</a>
</td></tr>
</table>
{GOOGLE_ANALYTICS}
</body>
</html>
<!-- END PAGE_FOOTER -->
