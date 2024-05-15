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
  can only see the groups they are part of meanwhile managers can see all the groups they 
  are part of and admins can see all the groups)

[] Users 
  shows all the users in the system or which are inside the groups the user is part of.
    [] show this tab if the user is a manager or an admin because normal users 
    should not have access to this tab without being those

[] loading ui (to avoid issues)
          [] should work while the server fetches items and or information
          [] should be used when loading anyting inside tabs

[x] Equipment
  [x] shows a tabbar with all the possibilities the user has
    [x] tabbar simple functionality 
      [x] simple highlighting of the current tab 
      [x] correct page information (simple like page 1 to n as a test)
    [] advanced tab information
      [] tab your_equipments
        [x] 70/30 split with equipments to the left and equipment info to the right
          [x] equipment loading places all the equipment on top with item controls 
            [x] create controler functions to support this request 
              [x] should return an array *controler
                [x] default return value should be an associative array 
                the value success should be by default false to allow for
                logic flow
                [x] should return the total number of items assigned
                  [] if there are no items return "no items"
                  [x] should accept a page number through $_GET
                    [x] if page requested > total set it to the last page
                    [x] if page is a neg number set page to n1 
            [] if no items says "no equipments assigned"
            [x] if less than one page of equipments exist shows only 1 page and no controls
            [x] else if less than 3 pages away from either begining or end shows all pages to the closses like 1... n-3 n-2 n-1 n n+1 n+max and vice versa
            [x] to the side of the page controls should show the total ammount of equipments assigned
            [] allow for the showing of a single type of equipment 
          [x] equipment item loaded
            [x] should show basic information like date assigned name of equipment etc..
            [] should load the items state and a notification icon
            [x] clicking on the item should load the side panel with the items info
            [] load notifications on the bottom part of a seleected item with the most recent one on top
        [] tab group_equipments
          [] 20% top reserved for group selection controls
            [] default to group 1 
            (which is the group with all equipments associated to the user)
            [] loads all the groups the user is part of
          [] shows all the equipment that is part of the groups they are associated 
             too 
             (associated to the users type, admins get access to all groups)
          [] same page capabilities as your equipments
        [] tab search
            [] colapsable internal sidebar with all search capabilities
              [] search capabilities
                [] search by user *
                [] search by group 
                [] search by equipment
                [] search by name
                * dependant on the user credentials
            [] page controls on the top of the page  
              [] allows for amogus will continue later
[] Reports
  [] Registration Report: Generates a report detailing the complete details of each piece of equipment,
    including model, serial number, acquisition date, and other relevant additional information.
  [] Assignment Report: facilitates the assignment of specific equipment to people in the company,
    associating them with corresponding employees or departments.
  []  History Report: Generates a detailed record of all equipment assignments,
    allowing tracking of when a piece of equipment was assigned,
    to whom, and for how long.
