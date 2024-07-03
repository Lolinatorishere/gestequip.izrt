<?php
session_start();
?>

<div class="equipment-tabbar">
  <div class="tabbar-item">
    <div id="first-tab" class="tabbar-content">
      <div id="groups_equipment">Inventory</div>
    </div>
  </div>

  <div class="tabbar-item">
    <div class="tabbar-content">
      <div id="search">Search</div>
    </div>
  </div>

  <?php

  if($_SESSION["user_type"] === "Admin")
  echo '
  <div class="tabbar-item">
    <div class="tabbar-content">
      <div id="all_equipment">Full Inventory</div>
    </div>
  </div>

  <div class="tabbar-item">
    <div class="tabbar-content">
      <div id="logs">logs</div>
    </div>
  </div>';
  ?>
</div>
