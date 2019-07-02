<?


/**
 * this visitor will only run once a day to see if it needs to communicate with
 * any accounts
 *
 * it is in charge of sending out 'thanks for signing up!' and
 * 'uh oh, you're about to expire' type emails...
 */
class AccountsCommunicationVisitor extends visitor_template{


	private $avalanche;

	function __construct($avalanche){
		$this->avalanche = $avalanche;
	}

	function visit($obj){
		if($obj instanceof avalanche_class){
			return $this->avalancheCase($obj);
		}else
		if($obj instanceof module_template){
			return $this->moduleCase($obj);
		}else
		if($obj instanceof skin_template){
			return $this->skinCase($obj);
		}else
		if($obj instanceof avalanche_usergroup_class){
			return $this->usergroupCase($obj);
		}else{
			throw new Exception("no case for object of type " . get_class($obj));
		}
		
	}



	function avalancheCase($avalanche){
		$ret = "Looking at modules\n";
		$count = $avalanche->getModuleCount();
		for($i=0; $i<$count; $i++){
			$module = $avalanche->getModuleAt($i);
			$ret .= $module->execute($this);
		}
		$ret .= "done\n";
		return $ret;
	}

	function moduleCase($module){
		$ret = "executing for: " . get_class($module) . "\n";
		$strongcal = $this->avalanche->getModule("strongcal");
		$now = new DateTime(date("Y-m-d H:i:s", $strongcal->gmttimestamp()));
		if($module instanceof module_accounts){
			$accts = $module->getAccounts();
			$ret .= "looking at accounts\n";
			foreach($accts as $a){
				$ret .= "account: " . $a->name() . "\n";
				
				$expire = new DateTime($a->expiresOn());
				
				$ao = new DateTime($a->addedOn());
				$eo = new DateTime($a->expiresOn());
				$ao = $now->getTimeStamp() - $ao->getTimeStamp();
				$eo = $eo->getTimeStamp() - $now->getTimeStamp();
				
				$to = $a->email();
				
				$us = $a->getAvalanche()->getAllUsers();
				foreach($us as $u){
					if($a->getAvalanche()->hasPermissionHuh($u->getId(), "view_cp") && $u->email() != $a->email()){
						$to .= ", " . $u->email();
					}
				}
				
				$from = "awulf@inversiondesigns.com";
				$mailheaders = "From: Inversion Designs <$from>\n";
				
				// output headers to $ret for debugging, just in case
				
				$ret .= "To: $to\n";
				$ret .= "From: $mailheaders\n";
				
				// now $ao is the difference between now and when it was added
				// so if $ao is less equal between 0 and 60*60*24, then it's the
				// very first day of the account, etc
				// so $ao % (60*60*24) is the day number the account is on
				$ao = floor($ao / (60*60*24));
				if($a->isDemo()){
					if($ao == 1){
						// send 'welcome email!' from awulf@...com
						$subject = "Welcome to Aurora Calendar!";
						$body = "Congratulations setting up your account at " . $a->name() . "." . $a->getAvalanche()->DOMAIN() . "!\n\n";
						$body .= "We offer a very flexible online calendar solution and are upgrading our services almost constantly. If you have any questions about our service or its features, or have specific needs for your online calendar, please let me know and I would be happy to answer your questions. You can reach me by replying to this email or using the contact form on our website at www.inversiondesigns.com.\n\n";
						$body .= "Happy Scheduling!\n\n";
						$body .= " - Adam Wulf\n";
						$body .= "   Account Manager\n";
						$body .= "   awulf@inversiondesigns.com";
						$this->avalanche->mail($to, $subject, $body, $mailheaders);
						$this->notifyAdam($a, $to, $subject, $body, $from);
					}else if($ao == 4){
						// send email about adding users
					}else if($ao == 7){
						// send email about sharing calendars
					}else if($ao == 14){
						// send email about constant updates
						$subject = "Your Aurora Calendar demo is half over!";
						$body = "I hope that your demo account at " . $a->name() . "." . $a->getAvalanche()->DOMAIN() . " is working out well for you!\n\n";
						$body .= "Remember, if you sign up now, all upgrades are free for the life of your account. We update our software monthly with new features, better features, and most importantly the features you request! ";
						$body .= "That's right! We take our customers very seriously and try to implement all of the feature requests that we can - as fast as we can. Contact me, Adam Wulf, by replying to this email to find out if we can add the feature you need to our online calendar suite.\n\n"; 
						$body .= "As always, if you have any questions at all or would like help signing up, feel free to email me at awulf@inversiondesigns.com or reply to this email, and I will be happy to help.\n\n";
						$body .= "Happy Scheduling!\n\n";
						$body .= " - Adam Wulf\n";
						$body .= "   Account Manager\n";
						$body .= "   awulf@inversiondesigns.com";
						$this->avalanche->mail($to, $subject, $body, $mailheaders);
						$this->notifyAdam($a, $to, $subject, $body, $from);
					}else if($ao == 21){
						// send email about something
					}else if($ao == 28){
						// send email about something
					}
				}
				
				if($eo <= 0){
					// the account has expired
					$eo = abs($eo);
					$eo = floor($eo / (60*60*24));
					if($eo == 1){
						// it's been expired for 1 day
					}else if($eo == 3){
						// it's been expired for 3 days
					}else if($eo == 5){
						// it's been expired for 5 days
					}
				}else{
					// the account has not expired
					$eo = floor($eo / (60*60*24));
					if($eo == 1){
						// it will expire in 1 day
						$subject = "Uh Oh! Your Aurora Calendar account is about to expire!";
						$body = "This is a friendly reminder that your account, " . $a->name() . "." . $a->getAvalanche()->DOMAIN() . " will expire on " . date("l, F jS", $expire->getTimeStamp()) . ".\n\n";
						$body .= "Signing up for more time is easy! Just visit www.inversiondesigns.com and click 'Log In'. Then enter your account name, " . $a->name() . ", and your regular username and password.\n\nRemember, you get a " . $a->discount() . "% discount when you sign up for 1 year or more!\n\n";
						$body .= "If you have any questions at all or would like help signing up, feel free to email me at awulf@inversiondesigns.com or reply to this email, and I will be happy to help.\n\n";
						$body .= "Happy Scheduling!\n\n";
						$body .= " - Adam Wulf\n";
						$body .= "   Account Manager\n";
						$body .= "   awulf@inversiondesigns.com";
						$this->avalanche->mail($to, $subject, $body, $mailheaders);
						$this->notifyAdam($a, $to, $subject, $body, $from);
					}else if($eo == 2){
						// it will expire in 2 days
					}else if($eo == 3){
						// it will expire in 3 days
						// it will expire in 1 day
						$subject = "Uh Oh! Your Aurora Calendar account is about to expire!";
						$body = "This is a friendly reminder that your account, " . $a->name() . "." . $a->getAvalanche()->DOMAIN() . " will expire on " . date("l, F jS", $expire->getTimeStamp()) . ".\n\n";
						$body .= "Signing up for more time is easy! Just visit www.inversiondesigns.com and click 'Log In'. Then enter your account name, " . $a->name() . ", and your regular username and password.\n\nRemember, you get a " . $a->discount() . "% discount when you sign up for 1 year or more!\n\n";
						$body .= "If you have any questions at all or would like help signing up, feel free to email me at awulf@inversiondesigns.com or reply to this email, and I will be happy to help.\n\n";
						$body .= "Happy Scheduling!\n\n";
						$body .= " - Adam Wulf\n";
						$body .= "   Account Manager\n";
						$body .= "   awulf@inversiondesigns.com";
						$this->avalanche->mail($to, $subject, $body, $mailheaders);
						$this->notifyAdam($a, $to, $subject, $body, $from);
					}
				}
			}
			$ret .= "done with accounts\n";
		}
		return $ret;
	}

	function skinCase($skin){
	}

	function usergroupCase($usergroup){

	}


	public function notifyAdam($a, $to, $subject, $body, $from){
		$body = "account: " . $a->name() . "\nto: $to\nfrom: $from\nsubject: $subject\n\n$body";
		@mail("bot@inversiondesigns.com", $a->name() . ": " . $subject, $body);
	}

}

?>