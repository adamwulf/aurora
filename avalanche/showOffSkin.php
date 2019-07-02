<?

if(!$theSkin)
	$theSkin = $_REQUEST['theSkin'];





$skn = $global->getSkin($theSkin);


echo $skn->javascript();

 echo "<table width='100%' cellpadding='8' cellspacing='0' border='1' bgcolor='" . $skn->bgcolor() . "' background='" . $skn->background() . "'>";
 echo "<tr>";
 echo "<td align='center'>";

$txt = $skn->title("This is the skin you've worked so hard on");

	$count = $skn->maxLayer() - $skn->minLayer() + 1;

	for($i = $count - 1; $i >= 0; $i--){
		$skn->setLayer($i);
		$olist  = $skn->oli("list item");
		$olist .= $skn->oli("another thing");
		$olist .= $skn->oli("lookie here");
		$olist .= $skn->oli("wheee! php!");
		$olist .= $skn->oli("coding is phat");
		$olist .= $skn->oli("howdy");
		$olist = $skn->ol($skn->font($olist));

		$ulist  = $skn->uli("this list isn't ordered");
		$ulist .= $skn->uli("whoa man");
		$ulist .= $skn->uli("neato");
		$ulist .= $skn->uli("look at this neat skin");
		$ulist .= $skn->uli("ckeck me out!");
		$ulist .= $skn->uli("no, seriously, check me out!");
		$ulist = $skn->ul($skn->font($ulist));

		$font = $skn->font("The quick brown fox jumped over the lazy dog.");
		$title = $skn->title($skn->name() . " Demo!");
		$hr = $skn->hr("90%", "align='left'");

		$cell1 = $skn->p_title("Check out this awesome table!");
		$cell1 .= $hr;
		$mintable = "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
		$mintable .= "<tr>";
		$mintable .= "<td width='50%'>";
		$mintable .= $ulist;
		$mintable .= "</td><td width='50%'>";
		$mintable .= $olist;
		$mintable .= "</td>";
		$mintable .= "</tr>";
		$mintable .= "</table>";
		$cell1 .= $mintable;
		$cell1 = $skn->td($cell1, "50%");
		$cell2 = $skn->td($txt, "50%", "align='center'");
		
		$row = $skn->tr($cell1 . $cell2);


		$username = $skn->input("type='text'");
		$password = $skn->input("type='password'");
		$comments = $skn->textarea("What are your comments?", "rows='6' cols='30'");
		$button = $skn->button("value='Submit!'");

		$cell3 = $skn->font("username " . $username . "<br>");
		$cell3 .= $skn->font("password " . $password . "<br>");
		$cell3 .= $skn->font("comments<br> " . $comments . "<br>");
		$cell3 .= $skn->font("<br>" . $button . "<br>");
		$cell3 = $skn->td($cell3, "width='100%' colspan='2'");

		$row .= $skn->tr($cell3);

		$header = $skn->th($skn->title("Check me out!"), "width='100%' colspan=2");


		$table = $skn->table($header . $row, "width='85%'");


		$table = "<br>" . $table . "<br>";
		$txt = $table;
	}


	echo $txt;



 echo "</td>";
 echo "</tr>";
 echo "</table>";
?>