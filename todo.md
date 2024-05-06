## todo
frontend: ui and the respective links

[x] login page 
  [x] add hover effect to login button
  [x] change the look to be more professional.
  [x] set up the buttons to work properly in terms of sending a form
  that searches for the component the person was looking for
[x] dashboard
  [x] "functional" navbar
    [x] profile component in correct orientation
    Username closer to the center and logout to the side
    [x] profile logout
    [x] search bar
  [x] "functional" sidebar
    [x] links "working"
    [x] specific icons per page
    [x] page highlighting 
  [x] Page content
    [x] appear the users name with a greeting like hello "username"
    [] a small tab with the ammount of notifications the user has and how many are "critical"
  [] merge sidebar and navbar functionality to reduce visual clutter
[x] profile page 
  [x] shows the username
  [x] shows the email registered
  [x] *shows the groups of which this user is 
  part of (*permission dependent wont show you they are an admin
  if you arent one yourself, or the position in the groups unless your inside them)
  [x] *shows what equipment is in their possesion 
  (*also permission dependant unless you are an admin, the user, or a manager of the user
  you cant see the machines and if you are a manager you can only see the machines atributed to 
  said user if they are under your management)
[] notifications page
  [] show all notifications
  [] notifications by catagory
  [] ignore certain notifications
  [] archive old notifications
[] Groups
  [] shows the groups and their currunt users
  (this only applies to certain levels of access, a user with little to no permissions
  can only see the groups they are part of meanwhile admins can see all the groups)
[] Users 
  shows all the users in the system or which are inside the groups the user is part of.
[x] Equipment
  [x] shows a tabbar with all the possibilities the user has
    [x] tabbar simple functionality 
      [] simple highlighting of the current page
      [] correct page information (simple like page 1 to n as a test)
    [] advanced page information
      [] will write after previous tasks complete
[] Reports
  [] Registration Report: Generates a report detailing the complete details of each piece of equipment,
    including model, serial number, acquisition date, and other relevant additional information.
  [] Assignment Report: facilitates the assignment of specific equipment to people in the company,
    associating them with corresponding employees or departments.
  []  History Report: Generates a detailed record of all equipment assignments,
    allowing tracking of when a piece of equipment was assigned,
    to whom, and for how long.
