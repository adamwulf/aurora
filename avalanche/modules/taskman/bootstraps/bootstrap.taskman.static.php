<?

class TaskmanGuiHelper {

	public static function taskTitleString($avalanche, $task){
		if(!($task instanceof module_taskman_task)){
			throw new IllegalArgumentException("argument 2 of " . __METHOD__ . " must be a taskman task");
		}

		$reminders = $avalanche->getModule("reminder");

		$title = $task->title();
		$title_panel = new Panel();
		$title_panel->setWidth("100%");

		if(count($reminders->getMyRemindersFor($task)) > 0){
			$reminder = new Icon($avalanche->HOSTURL() . $avalanche->APPPATH() . $avalanche->MODULES() . "os/images/alarm.gif");
			$title_panel->add($reminder);
		}

		$share_icon = new Icon($avalanche->HOSTURL() . $avalanche->APPPATH() . $avalanche->MODULES() . "taskman/gui/os/share.gif");
		$gift_icon  = new Icon($avalanche->HOSTURL() . $avalanche->APPPATH() . $avalanche->MODULES() . "taskman/gui/os/gift.gif");
		// get icons (as necessary)
		if($task->status() == module_taskman_task::$STATUS_DELEGATED &&
		   $task->delegatedTo() == $avalanche->loggedInHuh() &&
		   $task->assignedTo() != $avalanche->loggedInHuh()){
			// this task is delegated to me, but i haven't accepted it yet
			$title_panel->add($gift_icon);
		}else if($task->status() == module_taskman_task::$STATUS_DELEGATED &&
		   $task->delegatedTo() != $avalanche->loggedInHuh() &&
		   $task->assignedTo() == $avalanche->loggedInHuh() ||
		   $task->assignedTo() != $avalanche->loggedInHuh() &&
		   $task->author() == $avalanche->loggedInHuh()){
			// this task is assigned to me, but i have delegated it yet
			$title_panel->add($share_icon);
		}

		// get title
		if(strlen($title) == 0){
			$title = "<i>no title</i>";
		}
		if($task->priority() == module_taskman_task::$PRIORITY_HIGH){
			$title = "<b>$title</b>";
		}else if($task->priority() == module_taskman_task::$PRIORITY_LOW){
			$title = "<i>$title</i>";
		}
		$task_name = new Link($title, "index.php?view=task&task_id=" . $task->getId());

		$title_panel->add($task_name);

		return $title_panel;
	}



	public static function createTaskTip($avalanche, $task){
		if(!($avalanche instanceof avalanche_class)){
			throw new IllegalArgumentException("argument 2 of " . __METHOD__ . " must be an avalanche object");
		}
		if(!($task instanceof module_taskman_task)){
			throw new IllegalArgumentException("argument 2 of " . __METHOD__ . " must be a strongcal event");
		}
		$desc = "";
		if(strlen(trim($task->description()))){
			$d = str_replace("\n","<br>",$task->description());
			$desc .= "Description:<br>" . $d;
			$desc = wordwrap($desc, 35, "<br>");
		}
		if(strlen($desc)) $desc .= "<br>";
		$desc .= "(" . TaskmanGuiHelper::getStatusName($avalanche, $task) . ")";
		$desc = new Text($desc);
		return OsGuiHelper::createToolTip($desc);
	}


	public static function getStatusName($avalanche, $task){
		$status = $task->status();
		if(!is_int($status)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		$os = $avalanche->getModule("os");
		if($status == module_taskman_task::$STATUS_ACCEPTED){
			return "Accepted by " . $os->getUsername($task->assignedTo());
		}else if($status == module_taskman_task::$STATUS_NEEDS_ACTION){
			return "Assigned to " . $os->getUsername($task->assignedTo());;
		}else if($status == module_taskman_task::$STATUS_DECLINED){
			return "Declined by " . $os->getUsername($task->delegatedTo());;
		}else if($status == module_taskman_task::$STATUS_COMPLETED){
			$history = $task->history();
			$completer = false;
			foreach($history as $item){
				if($item["status"] == module_taskman_task::$STATUS_COMPLETED &&
				   $completer === false){
					$completer = $item["modified_by"];
				}
			}
			$str = "Completed";
			if($completer) $str .= " by " . $os->getUsername($completer);
			return "Completed " . $os->getUsername($task->assignedTo());;
		}else if($status == module_taskman_task::$STATUS_CANCELLED){
			$history = $task->history();
			$completer = false;
			foreach($history as $item){
				if($item["status"] == module_taskman_task::$STATUS_CANCELLED &&
				   $completer === false){
					$completer = $item["modified_by"];
				}
			}
			$str = "Completed";
			if($completer) $str .= " by " . $os->getUsername($completer);
			return "Cancelled";
		}else if($status == module_taskman_task::$STATUS_DELEGATED){
			return "Delegated to " . $os->getUsername($task->delegatedTo());
		}
		return "Unknown";
	}
}



?>
