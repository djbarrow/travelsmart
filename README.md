# Travelsmart
As my friend Pio who helped me develop this said in 2006.
I think we've out googled google.
This is my answer to Uber which google valued at $17 billion in 2013.
It's a simple google maps open source carpool website
the code is old and 2006-2009 era mysql & php and a bit obselete
I need a maintainers for this & itit's only 10k lines of code.
I recently modified this to work in a 2014 implementation of php,
This mostly involved kludgy getting rid of division by zero errors.
It needs to be made work on what was phonegap/newer javascript and google maps api's 
to show how far away the car thats to pick the passenger up is in realtime.
bootstrap 4 support to be added for mobiles and javascript popups
to explain the intimidating plan_trip page & make the plan trip page into a wizard
to make it less intimidating.

Read /htdocs/templates/give_it_away_now.tpl will explain how to insall additional
dependancies to get it to work like pear libraries & sajax.

Also join the http://ariasoft.ie/mailman/listinfo/carpool_protocol_ariasoft.ie
mailing list if you are a carpool website developerm it is early days yet but
I'm interested in getting all the carpool websites talking to each other, so, that
a passenger on carpool website A can match with a driver on carpool website B.
If I can get enough interest we will be writing an internet standard for carpool_protocol
as an RFC.
