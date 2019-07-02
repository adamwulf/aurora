<?
/**
 * This module is in charge of loading the entire page.
 *
 * it will end up running 2 bootstraps. the first will load the os header.
 * the second will load the content. the os header bootstrap will be passed in the URL,
 * as will the content loader.
 *
 * this loader will return an html page. it will be a table with two rows, one cell each.
 * the top cell will be the os header. the bottom cell will be the content.
 */
class AccountMain extends module_bootstrap_module{

	private $account;
	function __construct($avalanche, $account){
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}

		$this->setName("Primary Account Management");
		$this->setInfo("");
		$this->avalanche = $avalanche;
		$this->account = $account;
	}

	function run($data = false){
		if(!is_object($data)){
			throw new IllegalArgumentException("form vars must be sent to " . __METHOD__);
		}
		$data_list = $data->data();

		$os = $this->account->getAvalanche()->getModule("os");
		$strongcal = $this->account->getAvalanche()->getModule("strongcal");

		$padding_style = new Style();
		$padding_style->setPadding(10);

		$header_style = new Style("header_style");
		// customer login
		$login_panel = new GridPanel(1);
		$login_panel->setWidth("100%");

		$header = new Link($this->account->name() . "." . $this->account->getAvalanche()->DOMAIN(), "http://" . $this->account->name() . "." . $this->account->getAvalanche()->DOMAIN());
		$header->setStyle($header_style);
		$header->setTarget("_new");
		$login_panel->add($header, $padding_style);
		$name = new Text("Welcome, " . $os->getUsername($this->account->getAvalanche()->getActiveUser()) . "&nbsp;&nbsp;&nbsp; [ <a href='member.php?account=" . $this->account->name() . "&submit=1&logout=1" . (isset($data_list["testserver"]) && $data_list["testserver"] ? "&testserver=1" : "") . "'>logout</a> ]");
		$name->setStyle(new Style("name_style"));
		$login_panel->add($name, $padding_style);

		// get the tabs and respective panels
		$tabs = $this->getPanels($data_list);
		$login_panel->add($tabs);
		$login_panel->add(new Text("<script>" . $tabs->getCloseFunction()->toJS() . "</script>"));

		// switch the the appropriate panel
		if(isset($data_list["subview"]) &&$data_list["subview"] == "purchase"){
			$tabs->selectTab(2);
		}else{
			$tabs->selectTab(1);
		}

		$simple = new SimplePanel();
		$simple->setStyle(new Style("loggedInStyle"));
		$simple->add($login_panel);

		return new module_bootstrap_data($simple, "asdf");
	}

	private function getPanels($data_list){
		$pto = new Style("openButton");

		$ptc = new Style("closeButton");

		$ptp = new Style("buttonRow");


		$buttons = new TabbedPanel();
		$buttons->setWidth("100%");
		$buttons->setHolderStyle(clone $ptp);

		// overview
		$open_overview_button = new Button("overview");
		$open_overview_button->setStyle(clone $pto);
		$open_overview_button->getStyle()->setMarginLeft(10);
		$panel = new ErrorPanel($open_overview_button);
		$panel->setStyle(clone $ptp);
		$open_overview_button = $panel;

		$closed_overview_button = new Button("overview");
		$closed_overview_button->setStyle(clone $ptc);
		$closed_overview_button->getStyle()->setMarginLeft(10);
		$panel = new ErrorPanel($closed_overview_button);
		$panel->setStyle(clone $ptp);
		$closed_overview_button = $panel;

		$overview_body = $this->getOverview($data_list);
		$buttons->add($overview_body, $open_overview_button, $closed_overview_button);

		// purchase
		$open_purchase_button = new Button("purchase");
		$open_purchase_button->setStyle(clone $pto);
		$open_purchase_button->getStyle()->setMarginLeft(10);
		$panel = new ErrorPanel($open_purchase_button);
		$panel->setStyle(clone $ptp);
		$open_purchase_button = $panel;

		$closed_purchase_button = new Button("purchase");
		$closed_purchase_button->setStyle(clone $ptc);
		$closed_purchase_button->getStyle()->setMarginLeft(10);
		$panel = new ErrorPanel($closed_purchase_button);
		$panel->setStyle(clone $ptp);
		$closed_purchase_button = $panel;

		$purchase_body = $this->getPurchase($data_list);
		$buttons->add($purchase_body, $open_purchase_button, $closed_purchase_button);

		return $buttons;
	}


	private function getOverview($data_list){
		$os = $this->account->getAvalanche()->getModule("os");
		$strongcal = $this->account->getAvalanche()->getModule("strongcal");
		$trans = $this->account->getTransactions();
		$calculator = new TransactionCalculator($this->avalanche, $this->account->getPendingTransaction());
		$is_demo = $this->account->isDemo();

		$button_style = new Style();
		$button_style->setBorderWidth(1);
		$button_style->setBorderColor("black");
		$button_style->setBorderStyle("solid");
		$button_style->setBackground("#CCCCCC");
		$button_style->setPadding(4);
		$button_style->setFontFamily("verdana, sans-serif");
		$button_style->setFontSize(9);

		$details_style = new Style();
		$details_style->setBorderWidth(1);
		$details_style->setBorderColor("black");
		$details_style->setBorderStyle("solid");
		$details_style->setPadding(3);
		$details_style->setFontFamily("verdana, sans-serif");
		$details_style->setFontSize(8);

		$title_details_style = new Style();
		$title_details_style->setBorderWidth(1);
		$title_details_style->setBorderColor("black");
		$title_details_style->setBorderStyle("solid");
		$title_details_style->setPadding(3);
		$title_details_style->setFontFamily("verdana, sans-serif");
		$title_details_style->setFontSize(8);
		$title_details_style->setFontWeight("bold");

		$overview = new BorderPanel();
		$overview->getStyle()->setPadding(10);
		$overview->setWidth("100%");
		$overview->setAlign("center");
		$overview->setHeight(300);

		$button = new Button("Launch Calendar");
		$button->setStyle($button_style);
		$button->addAction(new LoadPageAction("http://" . $this->account->name() . "." . $this->account->getAvalanche()->DOMAIN()));

		if(!$this->account->disabled()){
			$button = new ErrorPanel($button);
		}else{
			if($is_demo){
				$button = new ErrorPanel(new Text("Trial Expired"));
			}else{
				$button = new ErrorPanel(new Text("Account Disabled"));
			}
		}
		$overview->setCenter($button);


		$details = new GridPanel(1);
		$details->getStyle()->setWidth("270px");
		$details->setCellStyle($details_style);
		$details->add(new Text("Account Overview"), $title_details_style);

		if($this->account->disabled()){
			$details->add(new Text("This account has been disabled."));
		}

		$now = new DateTime(date("Y-m-d H:i:s", $strongcal->gmttimestamp()));
		$expires_on = new DateTime($this->account->expiresOn());
		$timeleft = mktime($now->hour() - $expires_on->hour(),
				   $now->minute() - $expires_on->minute(),
				   $now->second() - $expires_on->second(),
				   $now->month() - $expires_on->month(),
				   $now->day() - $expires_on->day(),
				   $now->year() - $expires_on->year());
		if($this->account->getMonthsLeft() < 0){
			// account ran out of time
			$details->add(new Text("Expired " . abs(round($this->account->getMonthsLeft(),1)) . " months ago"));
		}else{
			// get how much time is left
			$expires_on = new DateTime($this->account->expiresOn());
			$datetime = date("l, M jS, Y", $expires_on->getTimeStamp());
			$details->add(new Text("Valid until: " . $datetime));
		}
		// get account info about users
		$users = count($this->account->getAvalanche()->getAllUsers()) - 1;
		$max_users = $this->account->maxUsers();
		$details->add(new Text("You are using " . $users . " of " . $max_users . " allowed users"));

		if($is_demo){
			$details->add(new Text("You are currently a demo account"));
		}

		$overview->setEast($details);
		return $overview;
	}

	private function getPurchase($data_list){
		$os = $this->account->getAvalanche()->getModule("os");
		$strongcal = $this->account->getAvalanche()->getModule("strongcal");
		$accounts = $this->avalanche->getModule("accounts");
		$subview = isset($data_list["subsubview"]) ? $data_list["subsubview"] : "overview";

		$details_style = new Style();
		$details_style->setPaddingTop(3);
		$details_style->setPaddingLeft(3);
		$details_style->setPaddingBottom(3);
		$details_style->setPaddingRight(14);
		$details_style->setFontFamily("verdana, sans-serif");
		$details_style->setFontSize(8);

		$title_details_style = new Style();
		$title_details_style->setPaddingTop(3);
		$title_details_style->setPaddingLeft(3);
		$title_details_style->setPaddingBottom(3);
		$title_details_style->setPaddingRight(14);
		$title_details_style->setFontFamily("verdana, sans-serif");
		$title_details_style->setFontSize(8);
		$title_details_style->setFontWeight("bold");

		$align_right = clone $details_style;
		$align_right->setTextAlign("right");

		$sub_calc = clone $align_right;
		$sub_calc->setPaddingRight(34);
		$sub_calc->setPadding(0);
		$sub_calc->setFontColor("#999999");


		if($subview == "overview"){
			$title = new Panel();
			$title->setStyle($title_details_style);
			$title->add(new Text("Current Plan [ "));
			$title->add(new Link("Upgrade or Add Time", "member.php?account=" . $this->account->name() . "&subview=purchase&subsubview=upgrade&testserver=" . (isset($data_list["testserver"]) ? $data_list["testserver"] : "")));
			$title->add(new Text(" ]"));

			if(!$this->account->isDemo()){
				$p = $this->account->findCurrentProduct();
				$overview = new GridPanel(2);
				$overview->setWidth("100%");
				$overview->getCellStyle()->setPaddingRight(14);
				$overview->setStyle($details_style);
				$overview->add(new Text($p->users() . " User" . ($p->users() > 1 ? "s" : "")));
				$overview->add(new Text("\$" . number_format(round($p->pricePerMonth(), 2), 2, '.', ',') . "/mo"), $align_right);
				$diff = 0;
				if($p->users() < $this->account->maxUsers()){
					$diff = ($this->account->maxUsers() - $p->users());
					$overview->add(new Text("+" . $diff . " User" . ($diff > 1 ? "s" : "")));
					$overview->add(new Text("\$" . number_format(round($p->pricePerUser(), 2), 2, '.', ',') . " ea"), $sub_calc);
					$overview->add(new Text(""));
					$overview->add(new Text("\$" . number_format(round($diff * $p->pricePerUser(),2), 2, '.', ',') . "/mo"), $align_right);
				}

				$total = new GridPanel(2);
				$total->setWidth("100%");
				$total->getCellStyle()->setPaddingRight(14);
				$total->setStyle(clone $details_style);
				$total->getStyle()->setClassname("border_top");
				$total->add(new Text("Total for " . $this->account->maxUsers() . " user" . ($this->account->maxUsers() > 1 ? "s" : "") . "/mo"));
				$total->add(new Text("\$" . number_format(round($p->pricePerMonth() + $diff * $p->pricePerUser(),2), 2, '.', ',') . "/mo"), $align_right);



				$grid = new GridPanel(1);
				$grid->setWidth("370");
				$grid->setCellStyle($details_style);
				$grid->add($title);
				$grid->add($overview);
				$grid->add($total);
			}else{
				$expires_on = new DateTime($this->account->expiresOn());
				$datetime = date("l, M jS, Y", $expires_on->getTimeStamp());

				$overview = new GridPanel(1);
				$overview->setWidth("100%");
				$overview->getCellStyle()->setPaddingRight(14);
				$overview->setStyle($details_style);
				$overview->add(new Text("You are currently signed up for a demo account."));
				$overview->add(new Text("&nbsp;"));
				$overview->add(new Text("This account expires on <b>$datetime!</b>"));


				$grid = new GridPanel(1);
				$grid->setWidth("370");
				$grid->setCellStyle($details_style);
				$grid->add($title);
				$grid->add($overview);
			}

			$purchase = $grid;

		}else if($subview == "upgrade"){

			$total_before_discount = 0;

			$trans = $this->account->getPendingTransaction();
			$calculator = new TransactionCalculator($this->avalanche, $trans);
			if(isset($data_list["update_subtotal"]) && $data_list["update_subtotal"]){
				$trans->users((int)$data_list["max_users"]);
				$trans->quantity((int)$data_list["additional_months"]);
				$calculator->setToOptimum();
			}
			$title = new Panel();
			$title->setStyle($title_details_style);
			$title->add(new Text("Step 1: Select Your Plan [ "));
			$title->add(new Link("cancel", "member.php?account=" . $this->account->name() . "&subview=purchase&testserver=" . (isset($data_list["testserver"]) ? $data_list["testserver"] : "")));
			$title->add(new Text(" ]"));

			//
			// create form for button on right column
			//
			$products = $accounts->getProducts();
			$max_users_offered = 0;
			foreach($products as $p){
				if($p->users() > $max_users_offered){
					$max_users_offered = $p->users();
				}
			}


			$users_select = new DropDownInput();
			$users_select->setName("max_users");
			for($i=($this->account->isDemo() ? count($this->account->getAvalanche()->getAllUsers())-1 : $this->account->maxUsers()); $i<=$max_users_offered; $i++){
				$opt = new DropDownOption((string)$i, (string)$i);
				$opt->setSelected($i == $trans->users());
				$users_select->addOption($opt);
			}
			$month_select = new DropDownInput();
			$month_select->setName("additional_months");
			for($i=($this->account->isDemo() ? 1 : 0); $i<=36; $i++){
				$opt = new DropDownOption((string)$i, (string)$i);
				$opt->setSelected($i == $trans->quantity());
				$month_select->addOption($opt);
			}
			$trans->users();
			$trans->quantity();
			// purchase more time
			$left_form = new FormPanel("member.php");
			$left_form->setStyle($details_style);
			$simple_grid = new GridPanel(2);
			$simple_grid->setStyle($details_style);
			$simple_grid->getCellStyle()->setPadding(4);
			$left_form->addHiddenField("page", "members");
			$left_form->addHiddenField("account", (string)$this->account->name());
			$left_form->addHiddenField("subview", "purchase");
			$left_form->addHiddenField("subsubview", "upgrade");
			$left_form->addHiddenField("update_subtotal", "1");
			$left_form->setAsGet();
			if(isset($data_list["testserver"]) && $data_list["testserver"]){
				$left_form->addHiddenField("testserver", "1");
			}
			$left_form->add(new Text("I need for my account... "));
			$simple_grid->add(new Text("Users: "));
			$simple_grid->add($users_select);
			$simple_grid->add(new Text("Additional Months:"));
			$simple_grid->add($month_select);
			$simple_grid->add(new Text("<input type='submit' value='Update Subtotal' style='border: 1px solid black;'>"));
			$left_form->add($simple_grid);
			// end creating form
			//









			$subtotal_title = new Panel();
			$subtotal_title->setStyle($title_details_style);
			$subtotal_title->add(new Text("Step 2: Review and Purchase Plan"));

			//
			// create form for button on right column
			//
			$trans = $this->account->getPendingTransaction();
			$total = $calculator->calculateTotal();
			// purchase more time
			$right_form = new FormPanel("https://www.2checkout.com/2co/buyer/purchase");
			$right_form->setAsPost();
			$right_form->addHiddenField("sid", (string)SID);
			$right_form->addHiddenField("cart_order_id", (string)$this->account->name());
			if(isset($data_list["testserver"]) && $data_list["testserver"]){
				$right_form->addHiddenField("demo", "Y");
			}
			//$right_form->addHiddenField("return_url", "http://www.inversiondesigns.com/2co/success.php");
			$right_form->addHiddenField("total", (string)$total);
			$right_form->add(new Text("<input type='submit' value='Purchase' style='border: 1px solid black;'>"));
			// end creating form
			//




			$overview = new GridPanel(2);
			$overview->setWidth("100%");
			$overview->getCellStyle()->setPaddingRight(14);
			$overview->setStyle($details_style);
			$overview->add(new Text($trans->product()->users() . " User" . ($trans->product()->users() > 1 ? "s" : "")));
			$overview->add(new Text("\$" . number_format(round($trans->product()->pricePerMonth(), 2), 2, '.', ',') . "/mo"), $align_right);
			$diff = 0;
			if($trans->product()->users() < $trans->users()){
				$diff = ($trans->users() - $trans->product()->users());
				$overview->add(new Text("+" . $diff . " User" . ($diff > 1 ? "s" : "")));
				$overview->add(new Text("\$" . number_format(round($trans->product()->pricePerUser(), 2), 2, '.', ',') . " ea"), $sub_calc);
				$overview->add(new Text(""));
				$overview->add(new Text("\$" . number_format(round($diff * $trans->product()->pricePerUser(),2), 2, '.', ',') . "/mo"), $align_right);
			}

			$total = new GridPanel(2);
			$total->setWidth("100%");
			$total->getCellStyle()->setPaddingRight(14);
			$total->setStyle(clone $details_style);
			$total->getStyle()->setClassname("border_top_bottom_nocolor");
			$total->add(new Text("Total for " . ($trans->product()->users() + $diff) . " user" . ($trans->users() > 1 ? "s" : "") . "/mo"));
			$total->add(new Text("\$" . number_format(round($trans->product()->pricePerMonth() + $diff * $trans->product()->pricePerUser(),2), 2, '.', ',') . "/mo"), $align_right);

			$subtotal = new GridPanel(1);
			$subtotal->setCellStyle($details_style);
			$subtotal->add($subtotal_title);
			$subtotal->add(new Text("<i>New price per month</i>"));
			$subtotal->add($overview);
			$subtotal->add($total);

			$show_middle_huh = !$this->account->isDemo() && $trans->users() > $this->account->maxUsers();

			// show number of months purchased
			$overview = new GridPanel(2);
			$overview->setWidth("100%");
			$overview->getCellStyle()->setPaddingRight(14);
			$overview->setStyle($details_style);
			$overview->add(new Text("Price per month"));
			$overview->add(new Text("\$" . number_format(round($trans->product()->pricePerMonth() + $diff * $trans->product()->pricePerUser(),2), 2, '.', ',') . "/mo"), $align_right);
			$overview->add(new Text(""));
			$overview->add(new Text("x " . $trans->quantity()), $align_right);
			$total = new GridPanel(2);
			$total->setWidth("100%");
			$total->getCellStyle()->setPaddingRight(14);
			$total->setStyle(clone $details_style);
			if($show_middle_huh || $calculator->applyDiscountHuh()){
				$total->getStyle()->setClassname("border_top_bottom_nocolor");
			}else{
				$total->getStyle()->setClassname("border_top_bottom");
			}
			$total_before_discount = round($trans->quantity() * ($trans->product()->pricePerMonth() + $diff * $trans->product()->pricePerUser()),2);
			$total->add(new Text("Total for " . $trans->quantity() . " new month" . ($trans->quantity() != 1 ? "s" : "")));
			$total->add(new Text("\$" . number_format($total_before_discount, 2, '.', ',') . "/mo"), $align_right);

			$subtotal->add(new Text("<br><i>Subtotal for new months</i>"));
			$subtotal->add($overview);
			$subtotal->add($total);

			// check to see if we need to upgrade our already purchased months
			if($show_middle_huh){
				$old_p = $this->account->findCurrentProduct();
				$new_price = round($trans->product()->pricePerMonth() + $diff * $trans->product()->pricePerUser(),2);
				$old_price = round($old_p->pricePerMonth() + ($this->account->maxUsers() - $old_p->users()) * $old_p->pricePerUser(),2);
				$overview = new GridPanel(2);
				$overview->setWidth("100%");
				$overview->getCellStyle()->setPaddingRight(14);
				$overview->setStyle($details_style);
				$overview->add(new Text("New \$/mo"));
				$overview->add(new Text("\$" . number_format($new_price, 2, '.', ',') . "/mo"), $align_right);
				$overview->add(new Text("Current \$/mo"));
				$overview->add(new Text("- \$" . number_format($old_price, 2, '.', ',') . "/mo"), $align_right);
				$total = new GridPanel(2);
				$total->setWidth("100%");
				$total->getCellStyle()->setPaddingRight(14);
				$total->setStyle(clone $details_style);
				$total->getStyle()->setClassname("border_top");
				$total->add(new Text("Total for " . ($trans->product()->users() + $diff) . " user" . ($trans->users() > 1 ? "s" : "") . "/mo"));
				$total->add(new Text("\$" . number_format($new_price - $old_price, 2, '.', ',') . "/mo"), $align_right);
				$total->add(new Text(""));
				$total->add(new Text("x " . round($this->account->getMonthsLeft(),2)), $align_right);

				$total2 = new GridPanel(2);
				$total2->setWidth("100%");
				$total2->getCellStyle()->setPaddingRight(14);
				$total2->setStyle(clone $details_style);
				$total2->getStyle()->setClassname("border_top_bottom_nocolor");
				$total2->add(new Text("Total for " . ($trans->product()->users() + $diff) . " user" . ($trans->users() > 1 ? "s" : "") . "/mo"));
				$total2->add(new Text("\$" . number_format(round(($new_price - $old_price) * $this->account->getMonthsLeft(),2), 2, '.', ',') . "/mo"), $align_right);

				$subtotal->add(new Text("<br><i>Subtotal for current months</i>"));
				$subtotal->add($overview);
				$subtotal->add($total);
				$subtotal->add($total2);


				$overview = new GridPanel(2);
				$overview->setWidth("100%");
				$overview->getCellStyle()->setPaddingRight(14);
				$overview->setStyle($details_style);
				$overview->add(new Text("New Months"));
				$overview->add(new Text("\$" . number_format(round($trans->quantity() * ($trans->product()->pricePerMonth() + $diff * $trans->product()->pricePerUser()),2), 2, '.', ',')), $align_right);
				$overview->add(new Text("Upgrade current plan"));
				$overview->add(new Text("\$" . number_format(round(($new_price - $old_price) * $this->account->getMonthsLeft(),2), 2, '.', ',')), $align_right);
				$total = new GridPanel(2);
				$total->setWidth("100%");
				$total->getCellStyle()->setPaddingRight(14);
				$total->setStyle(clone $details_style);
				if(!$calculator->applyDiscountHuh()){
					$total->getStyle()->setClassname("border_top_bottom");
				}else{
					$total->getStyle()->setClassname("border_top_bottom_nocolor");
				}
				$total_before_discount = round(($trans->quantity() * ($trans->product()->pricePerMonth() + $diff * $trans->product()->pricePerUser()) + ($new_price - $old_price) * $this->account->getMonthsLeft()),2);
				$total->add(new Text("Subtotal"));
				$total->add(new Text("\$" . number_format($total_before_discount, 2, '.', ',')), $align_right);
				$subtotal->add(new Text("<br><i>Subtotal</i>"));
				$subtotal->add($overview);
				$subtotal->add($total);
			}


			if($calculator->applyDiscountHuh()){
				$st = $total_before_discount;
				$d = $this->account->discount()/100*$st;
				$t = $st - $d;
				$overview = new GridPanel(2);
				$overview->setWidth("100%");
				$overview->getCellStyle()->setPaddingRight(14);
				$overview->setStyle($details_style);
				$overview->add(new Text("Subtotal"));
				$overview->add(new Text("\$" . number_format(round($st,2), 2, '.', ',')), $align_right);
				$overview->add(new Text($this->account->discount() . "% Discount"));
				$overview->add(new Text("\$" . number_format(round($d,2), 2, '.', ',')), $align_right);
				$total = new GridPanel(2);
				$total->setWidth("100%");
				$total->getCellStyle()->setPaddingRight(14);
				$total->setStyle(clone $details_style);
				$total->getStyle()->setClassname("border_top_bottom");
				$total->add(new Text("Total"));
				$total->add(new Text("\$" . number_format(round($t,2), 2, '.', ',')), $align_right);
				$subtotal->add(new Text("<br><i>Apply Discounts</i>"));
				$subtotal->add($overview);
				$subtotal->add($total);
			}

			// show subtotal


			$subtotal->add($right_form);

			$options = new GridPanel(1);
			$options->getStyle()->setWidth("270px");
			$options->setCellStyle($details_style);
			$options->add($title);
			$options->add($left_form);

			$table = new GridPanel(2);
			$table->setValign("top");
			$table->setWidth("100%");
			$table->setCellStyle($details_style);
			$table->add($options);
			$table->add($subtotal);

			$purchase = $table;

		}
		return $purchase;
	}
}
?>