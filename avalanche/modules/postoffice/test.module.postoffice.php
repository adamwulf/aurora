<?

Class TestPostOffice extends Abstract_Avalanche_TestCase { 

   public function setUp(){
	global $avalanche;
	Abstract_Avalanche_TestCase::setUp();
	
	$sql = "DELETE FROM " . $avalanche->PREFIX() . "postoffice_accounts WHERE 1";
	$avalanche->mysql_query($sql);
	$sql = "DELETE FROM " . $avalanche->PREFIX() . "postoffice_mailbox WHERE 1";
	$avalanche->mysql_query($sql);
	$sql = "DELETE FROM " . $avalanche->PREFIX() . "postoffice_messages WHERE 1";
	$avalanche->mysql_query($sql);
	$sql = "DELETE FROM " . $avalanche->PREFIX() . "postoffice_message_rel WHERE 1";
	$avalanche->mysql_query($sql);
	$sql = "DELETE FROM " . $avalanche->PREFIX() . "postoffice_send_rel WHERE 1";
	$avalanche->mysql_query($sql);
   }

   public function test_get_bad_account_throws_exception(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$id = 4;
	$key = "alk14234";
	
	try{
		$account = $postoffice->getAccount($id, $key);
		$this->fail("should have failed getting account");
	}catch(MailAccountNotFoundException $e){
		// good, b/c it doesn't exist
	}
   }
   
   public function test_create_account(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$this->assert($account instanceof module_postoffice_account, "i was returned an account");
	$this->assertTrue($account->unlocked(), "the account is unlocked");
	$key = $account->lock();
	$this->assertFalse($account->unlocked(), "the account is locked");
   }

   public function test_get_account(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$id = $account->getId();
	$key = $account->getKey();
	
	$account = $postoffice->getAccount($id, $key);
	$this->assert($account instanceof module_postoffice_account, "i was returned an account");
	$this->assertTrue($account->unlocked(), "the account is unlocked");
   }

   public function test_get_inbox(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$id = $account->getId();
	$key = $account->getKey();
	
	$inbox = $account->getInbox();
	$this->assertTrue($inbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
	$account->lock();
	
	try{
		$inbox = $account->getInbox();
		$this->fail("i got a mailbox from a locked account");
	}catch(AccountIsLockedException $e){
		$this->pass("good, i can't get it b/c it's locked");
	}

	$account->unlock($key);

	$inbox = $account->getInbox();
	$this->assertTrue($inbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
   }

   public function test_get_sentbox(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$id = $account->getId();
	$key = $account->getKey();
	
	$sentbox = $account->getSentbox();
	$this->assertTrue($sentbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
	$account->lock();
	
	try{
		$sentbox = $account->getSentbox();
		$this->fail("i got a mailbox from a locked account");
	}catch(AccountIsLockedException $e){
		$this->pass("good, i can't get it b/c it's locked");
	}

	$account->unlock($key);

	$sentbox = $account->getSentbox();
	$this->assertTrue($sentbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
   }
   public function test_get_draftbox(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$id = $account->getId();
	$key = $account->getKey();
	
	$draftbox = $account->getDraftbox();
	$this->assertTrue($draftbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
	$account->lock();
	
	try{
		$draftbox = $account->getDraftbox();
		$this->fail("i got a mailbox from a locked account");
	}catch(AccountIsLockedException $e){
		$this->pass("good, i can't get it b/c it's locked");
	}

	$account->unlock($key);

	$draftbox = $account->getDraftbox();
	$this->assertTrue($draftbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
   }
   
   
   public function test_get_deletedbox(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$id = $account->getId();
	$key = $account->getKey();
	
	$deletedbox = $account->getDeletedbox();
	$this->assertTrue($deletedbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
	$account->lock();
	
	try{
		$deletedbox = $account->getDeletedbox();
		$this->fail("i got a mailbox from a locked account");
	}catch(AccountIsLockedException $e){
		$this->pass("good, i can't get it b/c it's locked");
	}

	$account->unlock($key);

	$deletedbox = $account->getDeletedbox();
	$this->assertTrue($deletedbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
   }

   public function test_set_box_name_and_quota(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$id = $account->getId();
	$key = $account->getKey();
	
	$inbox = $account->getInbox();
	$this->assertEquals($inbox->quota(), 150, "the quota is 150");
	$this->assertEquals($inbox->name(), "Inbox", "the quota is 150");
	
	$this->assertEquals($inbox->quota(5), 5, "the quota is 5");
	$this->assertEquals($inbox->name("billy's inbox"), "billy's inbox", "the name is \"billy's inbox\"");

	$this->assertEquals($inbox->quota(), 5, "the quota is 5");
	$this->assertEquals($inbox->name(), "billy's inbox", "the name is \"billy's inbox\"");
   }


   public function test_get_messages(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$id = $account->getId();
	$key = $account->getKey();
	
	$inbox = $account->getInbox();
	$this->assertTrue($inbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
	$this->assertEquals(count($inbox->getMessages()), 0, "there are zero messages");	
   }
   
   public function test_compose_messages(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$name2 = "sally's box";
	$account = $postoffice->newAccount($name);
	$to_acct = $postoffice->newAccount($name2);
	$to_acct->lock();
	$this->assertTrue(!$to_acct->unlocked(), " the account is locked");
	$id = $account->getId();
	$key = $account->getKey();
	
	$draftbox = $account->getDraftbox();
	$this->assertTrue($draftbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
	$this->assertEquals(count($draftbox->getMessages()), 0, "there are zero messages");
	$account->compose();
	$this->assertEquals(count($draftbox->getMessages()), 1, "there is one messages");
	$ms = $draftbox->getMessages();
	$m = $ms[0];
	
	$this->assertEquals($m->from(), $account->getId(), "the message is from me");
	$this->assertEquals($m->subject(), "", "the message is from me");
	$this->assertEquals($m->body(), "", "the message is from me");
	
	$this->assertEquals($m->subject("asf's"), "asf's", "the message is from me");
	$this->assertEquals($m->body("khg's"), "khg's", "the message is from me");
	
	$this->assertEquals($m->subject(), "asf's", "the message is from me");
	$this->assertEquals($m->body(), "khg's", "the message is from me");
	
	$this->assertEquals(count($m->getRecipients()), 0, "get recipients is length 0");
	$m->addRecipient($to_acct);
	$this->assertEquals(count($m->getRecipients()), 1, "get recipients is length 1");
	$this->assertTrue($m->hasRecipient($to_acct), "it has the correct recipient");

	$m->removeRecipient($to_acct);
	$this->assertEquals(count($m->getRecipients()), 0, "get recipients is length 0");
   }
   
   public function test_cant_compose_messages(){
	global $avalanche;
	
	$postoffice = $avalanche->getModule("postoffice");
	
	$name = "billy's box";
	$account = $postoffice->newAccount($name);
	$id = $account->getId();
	$key = $account->getKey();
	
	$draftbox = $account->getDraftbox();
	$this->assertTrue($draftbox instanceof module_postoffice_mailbox, "i was returned a mailbox");
	
	$account->lock();
	
	$this->assertEquals(count($draftbox->getMessages()), 0, "there are zero messages");
	try{
		$account->compose();
		$this->fail("the account should have been locked");
	}catch(AccountIsLockedException $e){
		$this->pass("the account is locked");
	}
   }
   
};
?>