<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>I considered giving it all away</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<script language="JavaScript">
function popup(url) {
window.open(url, '', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1');
}
</script>
<body>
{LOGOS}
<ul>
<p>Thanks google for making such a fantastic base for me to work off.
Are you interested in helping me take travelsmart to the next level?
</p>
Some other carpool apps/websites are ahead of this project & I hope we can co-operate by making it possible to issue remote sql searches on each others database hopefully using a database structured like my database_tables file given with the source, improvement suggestions welcome unless carpool websites start cooperating & talking to each other using a standard API no carpool website wins because we will all be dividing customer base & users of each website will realise it's crap because a ride on carpool site a cannot be matched with a ride on carpool site b, no traction.
</p>
<p>I'd like google to do is to link to their taxi service
& their route planning, then improve the teledata maps of Ireland
outside the cities.
</p>
<p>
To speed things up facebook have a compiler which compiles php into c++ called hiphop unfortunately I use a lot of Sajax which depends on php evaluation at runtime.
</p>
<p>All worldwide timetables for flights trains & buses & taxis
could be put into the system if some very hardworking person was willing to do it & API's for timetables were standardised & everybody adopted them.
</p>
<p>Technology merges with mitfahrgelegenheit.de carpoolworld.com
& tescos carpool website & user base merges too.
</p>
<p>
There is a government travelsmart carpool website in Australia,
there is room for improvment in the website name if trademark issues happen.
</p>
<p>
Auctioning of trips is a possibility, I like Avego but I didn't invent it.
</p>
<p>
Before you start coding & don't know php buy Web Database Applications with
PHP & MySQL by O'Reilly if you don't know php already.
Have a look at the mysqladmin command to create users
& my enter_databaseinfo to create scripts.
Just to look at the website code after that look at sencha.com
phonejack & get some decent html architect to do a decent model
view controller paradigm for the website unfortunately the O'Reilly
book was only good enough to make you an average php coder.
</p>
<p>
Thanks for implementing sajax my AJAX engine the clever stuff
I'm doing here is generating javascript from php & using
generic_cb to evaluate the javascript after the call.
</p>
<p>
Security notice I use mysqlclean to avoid sql injection.
I use xss_encode & xss_decode to avoid cross site scripting.
All form evaluation is done server side no javascript rubbish.
</p>
<p>
Most php bugs get reported in /var/log/httpd/error_log
Fix the bug usually save the page & reload it is the usual
debug cycle just like basic on the old sinclair spectrum.
Learn to use a decent PHP debugger I was coding on my own
& ignorant of this technology
</p>
<p>
Get familiar with the source base by becoming a power user.
database_tables is what you think it is.
defines.php & config.php are the most important files in the source.
</p>
<p>
Look at pear.php.net I use a lot of this stuff the code runs
on fedora core 5 & 6 it is untested in 7. Yes the code
unfortunately 3 years old doesn't use a model view controller
paradigm so it'll be easy to recognise people ripping the code
for commercial use because the html templates are brittle.
</p>
<p>
Until google get involved with their open source mysql extensions
& google.travelsmart.ie .
The best place to host travelsmart I found is rosehosting.com
they cost around $30 a month.
</p>
<p>
Pear list says the following modules are installed<br>
INSTALLED PACKAGES, CHANNEL PEAR.PHP.NET:<br>
=========================================<br>
PACKAGE          VERSION STATE<br>
Archive_Tar      1.3.1   stable<br>
Calendar         0.5.3   beta<br>
Console_Getopt   1.2     stable<br>
DB               1.7.6   stable<br>
Date             1.4.6   stable<br>
Event_Dispatcher 1.0.0   stable<br>
File             1.2.2   stable<br>
HTML_CSS         1.2.0   stable<br>
HTML_Common      1.2.4   stable<br>
HTML_Template_IT 1.1.4   stable<br>
HTTP             1.4.0   stable<br>
MDB2             2.0.3   stable<br>
Mail             1.1.10  stable<br>
Net_SMTP         1.2.8   stable<br>
Net_Socket       1.0.6   stable<br>
PEAR             1.4.9   stable<br>
Pager            2.4.2   stable<br>
XML_Parser       1.2.7   stable<br>
XML_RPC          1.4.8   stable<br>
</p>
<p>
I've tried to make localisation possible by using html templates,
there are a few strings left lying around like compass_direction
& one pig where I send the tripmatch in text format.
To localise create
templates/English
templates/French
subdirectories.
</p>
<p>
Google maps now have route planning on their google.uk
but not for Ireland from google.com I think implement this
stuff someone.
</p>
<p>
Google also have live traffic jam detection get this stuff working.
</p>
<p>
Dijkstras algorithm needs to be implemented for multi hop journeys
cheapest journeys, avoiding traffic jams & toll roads etc.
</p>
<p>
The spam filtering problems with gmail yahoo & hotmail are political
get onto the people responsible travelsmart emails on these sites.
</p>
<p>
Important lines in php.ini
; Initialize session on request startup.
session.auto_start = 1
allow_call_time_pass_reference = On
</p>
<p>
httpd.conf
<Directory "/home/djbarrow/development/atob/www"><br>
    Options FollowSymLinks<br>

#<br>
# AllowOverride controls what directives may be placed in .htaccess files.<br>
# It can be "All", "None", or any combination of the keywords:<br>
#   Options FileInfo AuthConfig Limit<br>
#<br>
    AllowOverride None<br>
#<br>
# Controls who can get stuff from this server.<br>
#<br>
    Order allow,deny<br>
    Allow from all<br>
</Directory><br>
</p>
<p>
uk.txt in the mergeinfo country database stuff is in bad shape
put pressure on the freegis.org supplier to fix the stuff up.
</p>
<p>
I want to remove the legal disclaimer under 18 thingy from the
website whats your opinion Torvalds the ambulance chasers
will be suing you if you throw me a bogey.
</lp>
<p>
atob_backup atob_restore do as you'd expect.
</p>
<p>
Enjoy.
</p>
</ul>
<center>
<table>
<tr><td>
<a href="marty/hi_marty.doc">Marty Letter 1.</a>
</td></tr>
<tr><td>
<a href="marty/hi_marty2.doc">Marty Letter 2.</a>
</td></tr>

<tr><td>
<a href="travelsmart-21032012.tgz">The travelsmart source is here.</a>
</td></tr>
</table>
<center>
If you want the source to travelsmart or have questions <a href="javascript:;" onclick=popup('send_email.php?email_to={DJ_BARROW_EMAIL_ADDRESS}&email_subject=travelsmart%20-open%20source')>contact me</a> I spent 18 months of unpaid work full time with a friend getting this going & failing miserably at marketing it & for now I hope to God to find a dedicated concentious competent brilliant maintainer.
</center>
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
</center>
{GOOGLE_ANALYTICS}
</body>
<html>
