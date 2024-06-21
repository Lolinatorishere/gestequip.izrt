<?php
session_start();
?>

<div class="equipment-tabbar">
  <div class="tabbar-item">
    <div id="first-tab" class="tabbar-content">
      <div id="your_equipment">your equipment</div>
    </div>
  </div>

  <div class="tabbar-item">
    <div class="tabbar-content">
      <div id="groups_equipment">groups equipment</div>
    </div>
  </div>

  <div class="tabbar-item">
    <div class="tabbar-content">
      <div id="search">search</div>
    </div>
  </div>

  <?php
  if($_SESSION["user_type"] === "Manager" || $_SESSION["user_type"] === "Admin") 
  echo '<div class="tabbar-item">
    <div class="tabbar-content">
      <div id="add_equipment">add equipment</div>
    </div>
  </div>';

  if($_SESSION["user_type"] === "Admin")
  echo '<div class="tabbar-item">
    <div class="tabbar-content">
      <div id="add_equipment_type">equipment types</div>
    </div>
  </div>

  <div class="tabbar-item">
    <div class="tabbar-content">
      <div id="all_equipment">all equipment</div>
    </div>
  </div>

  <div class="tabbar-item">
    <div class="tabbar-content">
      <div id="remove_equipment">remove equipment</div>
    </div>
  </div>

  <div class="tabbar-item">
    <div class="tabbar-content">
      <div id="logs">logs</div>
    </div>
  </div>';
  ?>
</div>
