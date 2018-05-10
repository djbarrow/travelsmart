<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>
{TITLE}
</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<script language="JavaScript">
function popup(url) {
window.open(url, '', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1');
}
</script>
<body>
{LOGOS}
<h1 align=center>{TITLE}</h1>
<!-- END EDIT_USER -->
<form enctype="multipart/form-data" action="add_edit_user.php" method=post>
<table align=center>
<!-- BEGIN ERROR_USER_ALREADY_EXISTS -->
<tr class=error_big align=center>
<td colspan=2>
A user with this email address already exists
</td>
</tr>
<!-- END ERROR_USER_ALREADY_EXISTS -->
<tr>
<!-- BEGIN ERROR_EMPTY_NAME -->
<tr class=error>
<td></td>
<td>
empty name
</td>
</tr>
<!-- END ERROR_EMPTY_NAME -->
<td>name *:</td>
<td><input name=name value="{NAME}" type=text size=30 maxlength=100></td>
</tr>
<!-- BEGIN ERROR_SEX_UNSELECTED -->
<tr class=error>
<td></td>
<td>
sex unselected
</td>
</tr>
<!-- END ERROR_SEX_UNSELECTED -->
<tr>
<td>sex *:</td>
<td><select name=sex>
<option value=0 {SELECTED_0}>------</option>
<option value=m {SELECTED_m}>male</option>
<option value=f {SELECTED_f}>female</option>
</select>
</td>
</tr>
<!-- BEGIN ERROR_EMPTY_PASSWORD -->
<tr class=error>
<td></td>
<td>
empty password
</td>
</tr>
<!-- END ERROR_EMPTY_PASSWORD -->
<tr>
<td>password *:</td>
<td><input name=password value="{PASSWORD}"type=password size=30 maxlength=12></td>
</tr>
<!-- BEGIN ERROR_EMPTY_CONFIRM_PASSWORD -->
<tr class=error>
<td></td>
<td>
empty confirm password
</td>
</tr>
<!-- END ERROR_EMPTY_CONFIRM_PASSWORD -->
<!-- BEGIN ERROR_UNMATCHED_PASSWORDS -->
<tr class=error>
<td></td>
<td>
password & confirm password do not match
</td>
</tr>
<!-- END ERROR_UNMATCHED_PASSWORDS -->
<tr>
<td>confirm password *:</td>
<td><input name=confirm_password value="{CONFIRM_PASSWORD}"type=password size=30 maxlength=12></td>
</tr>
<!-- BEGIN ERROR_USER_NOT_AUTHENTICATED -->
<tr class=error_big align=center>
<td colspan=2>
This email address has not been authenticated,<td>
</tr>
<tr class=error_medium align=center>
<td colspan=2>
please double check whether you got the authentication email,<br>
if you didn't click <a href="send_authenticate.php?user_id={USER_ID}">here</a>
 to resend it,sending the email may take a minute please be patient,<br>
check that the email isn't being spam filtered<br>
or in the bulk email folder.
</td>
</tr>
<!-- END ERROR_USER_NOT_AUTHENTICATED -->
<!-- BEGIN ERROR_INVALID_EMAIL_ADDRESS -->
<tr class=error>
<td></td>
<td>
invalid email address
</td>
</tr>
<!-- END ERROR_INVALID_EMAIL_ADDRESS -->
<!-- BEGIN ERROR_EMPTY_EMAIL_ADDRESS -->
<tr class=error>
<td></td>
<td>
empty email address
</td>
</tr>
<!-- END ERROR_EMPTY_EMAIL_ADDRESS -->
<tr>
<td>email address *:</td>
<td><input name=email_address value="{EMAIL_ADDRESS}" type=text size=30 maxlength=120></td>
</tr>
<!-- BEGIN SPAM_PRECAUTIONS -->
<tr>
<td colspan=3>
<b>Note:</b> If possible don't use a <b>hotmail</b> email address, we have encountered problems delivering to hotmail. 
</td>
</tr>
<!-- END SPAM_PRECAUTIONS -->
<!-- BEGIN ERROR_EMPTY_PHONE_NUMBER -->
<tr class=error>
<td></td>
<td>
empty phone number
</td>
</tr>
<!-- END ERROR_EMPTY_PHONE_NUMBER -->
<tr>
<td>primary phone number *:</td>
<td><input name=primary_phone_number value="{PRIMARY_PHONE_NUMBER}" type=text size=30 maxlength=30></td>
</tr>
<tr>
<td>secondary phone number:</td>
<td><input name=secondary_phone_number value="{SECONDARY_PHONE_NUMBER}" type=text size=30 maxlength=30></td>
</tr>
<tr>
<!-- BEGIN ERROR_ADDRESS_UNSELECTED -->
<tr class=error>
<td colspan=2>
Address not selected, click "Add Address" to add a new one.
</td>
</tr>
<!-- END ERROR_ADDRESS_UNSELECTED -->
<td>address *:</td>
<td><select name="address_location_id">
<!-- BEGIN ADDRESS_SELECT -->
<option value={ADDRESS_SELECT_LOCATION_ID} {ADDRESS_SELECT_LOCATION_ID_SELECTED}>{ADDRESS_SELECT_LOCATION_NAME}   
<!-- END ADDRESS_SELECT -->
</select>
</td>
<td>
<input type=submit name="add_address" value="Add Address"></td>
</tr>
<!-- BEGIN EMAIL_TYPE_SELECT -->
<tr>
<td>delivered email style:</td> 
<td colspan=2>
{EMAIL_STYLE_SELECT}
</td></tr>
<!-- END EMAIL_TYPE_SELECT -->
</table>
<table align=center>
<tr><td align=center>
Adding a picture of yourself is optional, it is used so that you can be recognised by people travelling with you & used for their security. 
</td></tr>
<tr><td>
<input type="hidden" name="MAX_FILE_SIZE" value="16777216" > Upload Image: <input type="file" name="imgfile">
Click browse to upload a local jpeg image of yourself.
</td></tr>
</table>

<table align=center>
<!-- BEGIN ERROR_TERMS_OF_USE_CHECKBOX_NOT_CLICKED -->
<tr class=error>
<td colspan=2>
You haven't clicked the checkbox indicating that you have read & accepted the terms of use.
</td>
</tr>
<!-- END ERROR_TERMS_OF_USE_CHECKBOX_NOT_CLICKED -->
<!-- BEGIN TERMS_OF_USE -->
<tr>
<td>* I have read & accept the terms of use<input type=checkbox {CHECKED_terms_of_use_1} value=1 name=terms_of_use></td>
<td>
<a href="javascript:;" onclick=popup('legal.php')>Terms of use, please click here & read.</a>
</td>
</tr>
<!-- END TERMS_OF_USE -->
</table>
<table align=center>
<tr>
<td align=center>Fields with "*" are required.</td>
</tr>
<tr>
<td align=centre>If an email needs to be sent submission might take a minute, please be patient.</td>
</tr>
<tr>
<td align=center><input name="add_edit_user" type=submit value="Submit"></td>
</tr>
</table>
<!-- BEGIN BACK_TO_CONTROL_CENTRE -->
<center>
<a href="control_centre.php">Back To Control Centre</a>
</center>
<!-- END BACK_TO_CONTROL_CENTRE -->
<!-- BEGIN BACK_TO_LOGIN_PAGE -->
<center>
<a href="index.php">Back To Login Page</a>
</center>
<!-- END BACK_TO_LOGIN_PAGE -->
</form>
{GOOGLE_ANALYTICS}
</body>
</html>
