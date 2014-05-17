<?php

$t = $this;   # templater object

?>
      <!-- Tabs -->
      <div id="page-header">
        <ul id="page-tabs" class="nav nav-tabs">
          <li class="title active"><a href="#read" data-toggle="tab">
            <?php echo $t->data('page-title'); ?></a>
          </li>
          <li><a href="#edit" data-toggle="tab">Edit</a></li>
          <li><a href="#discuss" data-toggle="tab">Discuss</a></li>
          <li><a href="#history" data-toggle="tab">History</a></li>
          <li><a href="#access" data-toggle="tab">Access</a></li>
        </ul>
      </div>

      <!-- Tab Panes -->
      <div id="main-content">
        <div class="tab-content">
          <div class="tab-pane active" id="read"></div>
          <div class="tab-pane" id="edit"><?php echo $t->data('editor'); ?></div>
          <div class="tab-pane" id="discuss">Under development</div>
          <div class="tab-pane" id="history">Under development</div>
          <div class="tab-pane" id="access">Under development</div>
        </div>
      </div>

      <!-- Page Controls -->
      <div id="page-controls" class="navbar">
        <div class="container">
          <ul class="nav navbar-nav">
            <li><p class="navbar-text"><?php echo $t->data('last-edited'); ?></p></li>
          </ul>
        </div>
      </div>
