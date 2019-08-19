# Card Shuffler in Symfony 4.3
Just a basic command line card shuffler

## What does this do?
It's basically an example of a command line only application built in Symfony. It's light weight using on the bundles needed to complete the 
task at hand, in this case parse a json file containing a deck of playing card objects. Then it shuffles the deck of cards and displays a hand 
of five playing cards on the console. 

The basic idea behind this app is to demonstrate:

A) Symfony's ability to build flexible applicatons of any kind, from a basic command line application to a full blown web app.

B) Scalability. How a simple application such as this could be scaled up to consist of a controller, services, and any other utilities you might 
require down the road.

# Installation
Minimum specs: PHP 7.2 or higher

Download the files as a zip and place onto a working directory on a linux system of your choice. Once unzipped simply run the following 
command:
<pre>
cd php-console-app

php console.php card-shuffler:shuffle-and-deal
</pre>

From there you can follow the prompts on screen to go through the application. Pressing (n) on any prompt or ctrl c will exit the application 
at any time.
