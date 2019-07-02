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
class OSNewsGui extends module_bootstrap_module{

	private $avalanche;
	private $doc;
	
	function __construct($avalanche, Document $d){
		$this->setName("Login Panel");
		$this->setInfo("returns the gui component for the OS Login Panel");
		if(!is_object($avalanche)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche object");
		}
		$this->doc = $d;
		$this->avalanche = $avalanche;
	}

	function run($data = false){
		global $avalanche;
		$timer = new Timer();
		if(is_array($data->data())){
			$data_list = $data->data();

			$main_panel = new GridPanel(1);
			$main_panel->getCellStyle()->setPadding(2);
			$main_panel->setWidth("100%");
			
			$text_style = new Style();
			$text_style->setFontFamily("verdana, sans-serif");
			$text_style->setFontSize(10);
			$text_style->setFontColor("black");

			
			$data = array(
					array("title" => "2004-05-03",
					      "description" => "
					      <li>Some pages should now load faster. We have optimized alot of our database calls so most pages are now more efficient and faster.</li>
					      "),
					array("title" => "2004-05-02",
					      "description" => "
					      <li>Fixed user and group management headers to span the width of the page.</li>
					      <li>Date and time inputs now do not allow invalid times and dates.</li>
					      <li>The search results page looks nicer if there are no results found for a search.</li>
					      "),
					array("title" => "2004-04-28",
					      "description" => "
					      <li>Major upgrade to calendar management page. Sharing calendars is now quite a bit easier.</li>
					      <li>Fixed bug that would cause page to not load in Internet Explorer in some cases.</li>
					      "),
					array("title" => "2004-03-23",
					      "description" => "
					      <li>Fixed bug in owerview that would crash overview.</li>
					      <li>Fixed bug that would show a reminder as being for multiple events.</li>
					      "),
					array("title" => "2004-03-21",
					      "description" => "
					      <li>Task titles are now in bold or italics on overview page if they are high or low priority, respectively.</li>
					      "),
					array("title" => "2004-03-19",
					      "description" => "
					      <li>Fixed bug where new Overview page would crash in special circumstances.</li>
					      "),
					array("title" => "2004-03-18",
					      "description" => "
					      <li>Overview page has been redesigned and is not significantly more organized.</li>
					      "),
					array("title" => "2004-03-14",
					      "description" => "
					      <li>Security bug has been patched.</li>
					      <li>Small bug with show/hiding calendars not always working has been fixed.</li>
					      "),
					array("title" => "2004-03-12",
					      "description" => "
					      <li>Users can now have their lost password reset and emailed to them.</li>
					      "),
					array("title" => "2004-03-10",
					      "description" => "
					      <li>Login page has been added to replace the small login box.</li>
					      <li>The 'About' page now includes the Terms of Service and Privacy Policy.</li>
					      <li>Adding and editing users now requires an email to be entered.</li>
					      <li>The sprocket menu as well as the other smaller menus have a new color scheme. The sprocket menu has new icons as well.</li>
					      <li>The search field now saves your last search text.</li>
					      "),
					array("title" => "2004-03-09",
					      "description" => "
					      <li>Users can now manage their account online by logging in at inversiondesigns.com.</li>
					      <li>Double clicking in month, week, or day view brings the user to add event screen.</li>
					      <li>Accounts now respect user limits.</li>
					      "),
					array("title" => "2004-02-25",
					      "description" => "
					      <li>Various small bug fixes.</li>
					      <li>Accounts can now be disabled. If an account is disabled, users will be taken to a simple administrative screen on the InversionDesigns.com site.</li>
					      <li>Demo accounts are now limited to 100 users. Active accounts are limited to the amount of users that they have purchased.</li>
					      "),
					array("title" => "2004-02-17",
					      "description" => "
					      <li>Calendar Management now requires fewer clicks to navigate thanks to the new tabbed interface.</li>
					      <li>An Add Calendar link is now in the calendar list in the sidebar if the active user is allowed to add calendars. This reduces the number of steps to add calendar since it is no longer necessaary to go to the Calendar Management page.</li>
					      "),
					array("title" => "2004-02-16",
					      "description" => "
					      <li>Updated completed task icons to checkmarks instead of X's.</li>
					      <li>Added \"Cancelled\" status for tasks.</li>
					      <li>Cancelled tasks are marked with an X icon.</li>
					      <li>Fixed calendar permission problems. Showing events as \"busy\" now works correctly. Attendees of events can now see that event in main views.</li>
					      <li>Users can now be notified when tasks are delegated to them.</li>
					      <li>Avalanche now properly cleans up after deleted users and groups.</li>
					      "),
					array("title" => "2004-01-31",
					      "description" => "
					      <li>Icon added to main views to show which events and tasks have reminders set.</li>
					      <li>Change calendar list in sidebar to show a dropdown menu instead of hiding calendar when clicking a calendar name.</li>
					      <li>Improved caching of users and groups.</li>
					      <li>Fixed a reminders and notifications system error.</li>
					      "),
					array("title" => "2004-01-26",
					      "description" => "
					      <li>Overview screen now correctly highlights high and low priority tasks.</li>
					      <li>Users can now customize their avatar. To change your avatar, go to your profile page by clicking \"My Profile\" in the sprocket menu.</li>
					      <li>The comment for the most recent task status changes is now displayed on the task view.</li>
					      "),
					array("title" => "2004-01-25",
					      "description" => "
					      <li>Added day of the week headers to the navigation calendar.</li>
					      <li>Clicking on a time in day view now takes you straight to the add event page if you only have 1 calendar visible in the sidebar calendar list.</li>
					      <li>Account Administrators can modify the site title in their preferences menu.</li>
					      <li>Add account page now resides within main InversionDesigns.com site.</li>
					      "),
					array("title" => "2004-01-22",
					      "description" => "
					      <li>Added icons to sprocket menu.</li>
					      "),
					array("title" => "2004-01-20",
					      "description" => "
					      <li>Reminders are reset when tasks or events are edited.</li>
					      <li>Reminders for tasks are now updated when a task is edited.</li>
					      <li>Autogenerated email for new accounts now contains the correct domain name for the account.</li>
					      <li>Day view now shows  up to and including the end of day preference time, instead of being exclusive.</li>
					      <li>Various UI upgrades.</li>
					      "),
					array("title" => "2004-01-19",
					      "description" => "
					      <li>Fixed the add and edit event forms to better auto-estimate start and end times for events.</li>
					      <li>Added examples for SMS entry in the first-time login screen and edit profile page.</li>
					      "),
					array("title" => "2004-01-17",
					      "description" => "
					      <li>You can now set Reminders for Tasks</li>
					      <li>The list of reminders for events and tasks now show when the reminder was sent.</li>
					      <li>Add and Edit event views will now auto update start and end date fields when the other field is changed.</li>
					      <li>You can now invite groups of users as well as single users to an event.</li>
					      <li>Reminders list in event view shows the time that the reminder was sent.</li>
					      <li>Reminders now work for all accounts.</li>
					      "),
					array("title" => "2004-01-13",
					      "description" => "
					      <li>Users that log in for the first time are now presented with a welcome wizard. This wizard guides the user through setting up personal information and preferences and also helps set up the user's first calendar.</li>
					      "),
					array("title" => "2004-01-12",
					      "description" => "
					      <li>Calendar Permissions are alot easier to set now. Added a 'Simple' and 'Advanced' views for setting calendar permissions. Simple view is very easy to use.</li>
					      "),
					array("title" => "2004-01-11",
					      "description" => "
					      <li>When creating an account, users can pick between inversiondesigns.com and calendarcampus.com for the primary domain of their calendar</li>
					      "),
					array("title" => "2004-01-08",
					      "description" => "
					      <li>Fixed major caching problems. Pages now load significantly faster. Enter backend is more efficient.</li>
					      "),
					array("title" => "2004-01-07",
					      "description" => "
					      <li>Fixed bug in Invite Attendee page where user list would not filter correctly.</li>
					      <li>Added the Guest group who's members include only the non-logged in visitor.</li>
					      <li>Added the All Users group who's members include all registered users.</li>
					      <li>Team Management has become Group Management in preparation for our Project Manager module that will be appearing soon. All Teams have been converted to Public groups and otherwise act the same (now they're called 'Groups' instead of 'Teams'). All users have the ability to create personal Private Groups to help manage their calendars.</li>
					      <li>Title graphic for the lists in the sidebar now contains some color.</li>
					      <li>Unit tests have been added for parts of the GUI. This means that we are working hard to keep you productive with a bug free calendar system.</li>
					      "),
					array("title" => "2004-12-28",
					      "description" => "
					      <li>The status of a task can be changed (with or without comment) on the view task page. It is no longer necessary to edit a task to change it's status. To change the status on the view task page, click the \"change\" link in the information box where the current status is displayed.</li>
					      <li>All usernames now display as a link that brings a popup menu with various options including: view user's profile, email user, and sms user (according to availability).</li>
					      <li>User Profile page has been created. Users can view and update their profile by clicking the \"My Profile\" link in the sprocket menu.</li>
					      <li>High Priority icon for events has been updated.</li>
					      <li>Background graphic in task add/edit/view has been updated.</li>
					      "),
					array("title" => "2004-12-24",
					      "description" => "
					      <li>Fixed tabs in preferences, event view, and about page to work correctly in the Safari browser.</li>
					      <li>Fixed bug when adding and editing multi-day duration recurring events.</li>
					      "),
					array("title" => "2004-12-23",
					      "description" => "
					      <li>Users that have been invited to an event and are an attendee are now granted temporary read access to that calendar for that event only. This allows all attendees to at least view event information regardless of other team permissions. This is also allows users to share specific events in a calendar with other specific users of the system without needing to set up a seperate team.</li>
					      "),
					array("title" => "2004-12-22",
					      "description" => "
					      <li>Fixed bug that let users see reminders set by a different user. User's will now only see all reminders set for themselves or for all attendees.</li>
					      "),
					array("title" => "2004-12-21",
					      "description" => "
					      <li>Added ability for users to set reminders for events. Users can now be contacted via their preferred contact method before an event. To set a reminder, view the event and click the \"Reminders\" tab.</li>
					      <li>Attendee list is now sorted by username.</li>
					      <li>Users can now be notified when an event is edited or when a task changes status.</li>
					      "),
					array("title" => "2004-12-16",
					      "description" => "
					      <li>Added attendees to events. Users can now invite other users as attendees to events or to series of events. To invite a user, view the event and click the \"Attendees\" button, then click the [Invite New] tab.</li>
					      <li>Also, users can send messages to all attendees of an event. View event information, and click the \"Attendees\" button, then click the [Send Message] tab.</li>
					      <li>Users can set a description for their calendars. Simply add or edit a calendar to set its description.</li>
					      <li>SMS and email notifications about new events now include the event title</li>
					      "),
					array("title" => "2004-12-08",
					      "description" => "
					      <li>Users can now add a comment when changing the status of a task.</li>
					      <li>Added \"About\" link in sprocket menu.</li>
					      <li>Added ability to set Notifications in a user's preferences menu. User's can now be email'd or sms'd when new tasks or events are added or deleted from a calendar. To set up a notification, click the sprocket in the top right and click [Preferences]. Then click the Notifications tab on the Preferences page.</li>
					      "),
					array("title" => "2004-12-04",
					      "description" => "
					      <li>Added buttons to expand and collapse calendar list in sidebar.</li>
					      "),
					array("title" => "2004-11-24",
					      "description" => "
					      <li>Comments in events are now searchable. Also, new comments show up in the overview page in the New Stuff section.</li>

					      <li>Posted Date for comments is now correctly saved in GMT time.</li>

					      <li>Add comment form now insists that either the title or body of a comment must have text (instead of both).</li>

					      <li>Calendar list and Task list in sidebar now save collapse state on page load.</li>

					      <li>Date and time in add event/task forms now defaults to current date and time.</li>

					      <li>Added some stats to calendar overview page and user overview page.</li>
					      "),
					array("title" => "2004-11-16",
					      "description" => "
					      <li>Added \"View History\" option to task view page. Users can now see the status history of a task, as well as who had changed the status of the task.</li>

					      <li>Fixed various display bugs in Overview page.</li>
					      "),
					array("title" => "2004-11-13",
					      "description" => "
					      <li>Added Overview page. This page gives you an overview of the coming week, as well as helps you track tasks that you have delegated. This page can also help keep you up to date on new events and tasks in Aurora Calendar.</li>

					      <li>The author of a new team is now added to the team by default.</li>
					      "),
					array("title" => "2004-11-13",
					      "description" => "
					      <li>Fixed bug that allowed unauthorized users to see add and edit event/task forms even though no event information was shown.</li>

					      <li>Fixed bug that showed edit and delete buttons for tasks and events when users were not authorized.</li>

					      <li>Various UI upgrades.</li>
					      "),
					array("title" => "2004-11-11",
					      "description" => "<li>Added support for custom fields in calendars. You can now add small and large text fields, date, time, dropdown, checkbox, and url fields to calendars.</li>
					      
					      <li>Day view now starts at 6:00a instead of midnight.</li>
					      
					      <li>Avatars now draw from an account specific repository instead of every account using the same avatars.</li>
					      
					      <li>Cookies are now saved on a per domain basis.</li>
					      "),
					array("title" => "2004-10-25",
					      "description" => "<li>Fixed timezone bugs in task manager. Now all tasks' due date, completed-on date, and created-on date will appear in the proper timezone.</li>
					      
					      <li>Added Accept and Decline buttons to task view if task is delegated to the logged in user.</li>
					      
					      <li>Began reformatting task view to contain more relevant information than before, such as who the task is currently assigned to.</li>
					      
					      <li>Added search feature. To search, type in search terms in field in top right of screen and press Enter. By default, the search will find calendars, events, tasks, users, or teams that match the search terms. Click the magnifying glass to refine search categories.</li>
					      "),
					array("title" => "2004-10-20",
					      "description" => "<li>Task Management has been added. To add a task, click on the add task icon in the top right (next to the add event icon).</li>
					      
					      <li>Tasks can now be delegated to other users.</li>
					      
					      <li>Permissions for tasks are derived from the permissions set for viewing and editing events in a calendar.</li>"),
					array("title" => "2004-10-14",
					      "description" => "<li>Added week view.</li>
					      
					      <li>Manage Users page now shows full names of users if available. Also, the list of users will scroll instead of grow in length.</li>"),
					array("title" => "2004-10-13",
					      "description" => "<li>Added High Priority icon to event view.</li>
					      
					      <li>Moved Edit and Delete buttons to bottom of Event View page.</li>
					      
					      <li>Added background image to event view.</li>"),
					array("title" => "2004-09-28",
					      "description" => "<li>High and Low priority events are bolded and italicized, respectively, in main views.</li>"),
					array("title" => "2004-09-26",
					      "description" => "<li>Fixed Incorrect caching of team permissions for calendars.</li>
					      
					      <li>The sidebar calendar list expands vertically to accommodate long calendar names.</li>
					      
					      <li>Errors (such as incorrect form input) are now reported in a more user friendly error screen.</li>"),
					array("title" => "2004-09-25",
					      "description" => "<li>Added \"My Profile\" button to sprocket menu in top right.</li>
					      
					      <li>Fixed problem which gave teams with \"add/edit/delete comments\" permission full admin permission to comments."),
					array("title" => "2004-09-17",
					      "description" => "<li>Users with appropriate permission can now add, edit, and delete comments from calendar events.</li>
					      
					      <li>A descriptive error message is now shown when users try to log in with incorrect username or password.</li>
					      
					      <li>Users can now manage their first, middle, and last names in User Management. Users can be added to teams based on either their usernames or legal names.</li>
					      
					      <li>Added permission to System Usergroups which can regulate who can view other users names and email.</li>
					      
					      <li>Users' legal names now appear in team membership list (to users with permission to view names).</li>
					      
					      <li>Single event view, edit event view, and add event view now contain the appropriate calendar's name.</li>
					      
					      <li>This news section has been added to keep track of new features and important news.</li>
					      
					      <li>In day view, the times in the left margin are now links to add an event beginning at that time.</li>
					      
					      <li>The navigation calendar in the sidebar now remembers your last main view (month/week/day).</li>"),
					array("title" => "2004-09-15",
					      "description" => "<li>Caching inefficiency has been fixed, so pages now load significantly faster.</li>"),
					array("title" => "2004-09-14",
					      "description" => "<li>We're open for business! Enjoy the calendar system!</li>")
				     );
			
			foreach($data as $datum){
				$title = $datum["title"];
				$year = substr($title, 0, 4);
				$month = substr($title, 5, 2);
				$day = substr($title, 8, 2);
				$time = mktime(0,0,0,$month,$day,$year);
				$title = date("F jS, Y", $time);
				$description = $datum["description"];
				$description = "<ul class='news_buttons'>" . $description . "</ul>";
				// $description = str_replace("\n", "<br>", $description);
				$title = new Text("<b>$title</b>");
				$title->setStyle($text_style);
				$description = new Text($description);
				$description->setStyle($text_style);
				$desc_panel = new Panel();
				// $desc_panel->getStyle()->setMarginLeft(10);
				$desc_panel->setWidth("100%");
				$desc_panel->add($description);
				$main_panel->add($title);
				$main_panel->add($desc_panel);
			}
			
			return new module_bootstrap_data($main_panel, "the news panel gui component");
		}else{
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be an array of form input.<br>");
		}
	}
}
?>