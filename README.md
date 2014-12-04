Syndicator
==========

Pulsd Syndicator Demo:
http://rorytaca.com/pulsd/syndicator-demo/


<br>
<b>Technologies Used:</b>

HTML5 / CSS3 / Bootstrap 3/ Javascript / jQuery / Angular.js / JSON / PHP / Firebase / Git

<br>
<b>Project Design:</b>
<ul>
  <li>Basic HTML5 mark-up layout</li>
  <li>Responsive UI Admin panel made with Bootstrap3 and custom CSS styling</li>
  <li>MVC structuring with Angular.js, supplies Routing, Data control, dependency injections, etc...</li>
  <li>Firebase for handling data/data hosting as light-weight JSON object</li>
</ul>

<br>
<b>Databases:</b>
<ul><b>
  <li>Products Table: https://amber-inferno-7588.firebaseio.com/ </li>
  <li>Syndicated Websites Table: https://amber-inferno-7600.firebaseio.com/</li>
</ul>

<br>
<b>Serve Side Scripts</b>
<p>
All written in PHP. There is a CRON JOB set up on the platforms current hosting plan, executing every hour on the hour to execute the
'php/scripts/automate_processes.php' which scans 'Product Table', checks for all rows where 'status' is false (not syndicated yet) and executes the script for that data node
</p>

<p>Looks something like:</p>
<p>0 * * * * /web/cgi-bin/php5 "$HOME/html/pulsd/syndicator-demo/php/scripts/automate_processes.php"</p>
<p>However the actual configuration for the CRON JOB in use was handled by a built-in cron job manager in the hosting platform</p>

<b>Websites used for Syndication</b>
<ul>
  <li></li>
  <li></li>
</ul>
