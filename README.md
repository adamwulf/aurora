## Aurora Calendar

This is the original source code for the Aurora Calendar and the Avalanche PHP code that powered it. Aurora was the precursor to Jotlet.net calendar, which was eventually acquired by Jive Software in 2007.

The code in this repository was written around 2004. I was incredibly proud of it at the time, and I think a good majority of it still stands up. Another good majority of it certainly does not! :)

### Images

A install wizard for the entire Avalanche and Aurora applications, which would configure the DB with any options.

![Install Page](images/install.png?raw=true)

The login page, I remember this like I was building it yesterday.

![Login Page](images/login.png?raw=true)

I was so proud of figuring out an algorithm for a nice looking day layout

![Day Page](images/day-view.png?raw=true)

We had fairly specific permissions that could be set for per-user or per-group for each calendar.

![Share Permissions](images/share-permissions.png?raw=true)

I was very proud of custom fields per-calendar. The user could add as many fields as they'd liked, of many different types. Then these fields would show up when the user was adding events to the calendar.

![Custom Fields](images/custom-fields.png?raw=true)

![Custom Fields](images/custom-fields2.png?raw=true)

The add event form showed the user's added custom fields at the bottom. Most calendar software will save a single instance with meta-data for the recurring options - but not Aurora! I would write a row in the DB or every instance of a series. This dramatically simplified fetching events with a time box, but wasn't the most efficient.

![Add Event](images/add-event.png?raw=true)

I remember having a lot of fun discussing these sharing and task-delegation features with Buck.

![Delegate Task](images/delegate-task.png?raw=true)


### References

The javascript was written in a pre-jQuery world. Instead, it was based on the [X-Library from cross-browser.com](http://cross-browser.com).

It also uses the pngfix.js file to load alpha-transparency into png image files. Yes, this was built when browsers had trouble rendering transparent png files! yikes!

This was also before `composer` for PHP, so finding well tested and well maintained libraries to build off of was nearly impossible. I remember the Zend framework was possibly the only option, but required a license fee outside our budget if i recall.

This was also before what we'd call CSS today. There was some CSS at the time, but browser support was very sketchy. There was no common grid layout, no twitter bootstrap, etc. The overwhelming majority of the layout code was in HTML `tables`.

### Things I'm proud of

I'm really proud of the JavaScript work - it was important for us to have it working consistently across all browsers of the time, and IE 6 was a beast to program for and support, but we did it.

I'm also generally proud of how organized the `library` folder and the `modules` folders are. It's a system that has well encapsulated code, a borderline plugin system with the `modules`. The `bootstrap` process for building the UI was far from perfect, but helped keep clean code.

### Hindsight mistakes

The biggest mistake is plainly obvious now: I was saving passwords in plaintext. My rational at the time was "what difference does encryption make, if i just have to decrypt it to compare for login anyways? if I can decrypt it, then an attacker can too, why bother?" I had either never heard of one-way hashes, or had completely misunderstood them.

The `bootstrap` architecture was clever at the time I thought, but become more cumbersome the later we got into development. The UI renders pretty slow, and that's one major reason.

The other major reason that the UI is so slow, is that the database is fairly integrated into the logic and the UI generation. I didn't have a clean separation of database and front end, which i'm sure caused too many queries and re-fetches to the db.

Another reason the UI is slow, and another database mistake: every time a user added a calendar, I would add 5 new tables to the database, with the calendar `id` as part of the table name (?!). I did this because adding fields to a calendar would also literally add columns to its calendar table. I'd obviously design the database very differently now, but it's fun to browse around and see the mistakes.


### Authors

Buck Wilson did all of the UI and UX design, and Adam Wulf did all of the programming.