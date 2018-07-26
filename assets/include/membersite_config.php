<?PHP
/* require_once("assets/include/leasingpro_membersite.php"); */
require_once("leasingpro_membersite.php");

$fgmembersite = new FGMembersite();

//Provide your site name here
$fgmembersite->SetWebsiteName('www.leasingpro.com');

//Provide the email address where you want to get notifications
$fgmembersite->SetAdminEmail('support@thescreeningpros.com');

//Provide your database login details here:
//hostname, user name, password, database name and table name
//note that the script will create the table (for example, fgusers in this case)
//by itself on submitting register.php for the first time

$fgmembersite->InitDB(/*hostname*/'localhost',
                    /*username*/'root',
                    /*password*/'',
                    /*database name*/'mohan',
                    /*table name*/'');


#$fgmembersite->InitDB(/*hostname*/'10.1.4.109',
##                     /*username*/'dbdeveloper',
##                     /*password*/'$ecofr23',
##                      /*database name*/'leasepro',
##				   /*table name*/'');
##

//For better security. Get a random string from this link: http://tinyurl.com/randstr
// and put it herez

$fgmembersite->SetRandomKey('vFabEWpUwrRIQWk');

?>
