<?php

$t = $this;   # templater object

?>
    <?php if ($t->data('handler') == 'PageHandler'): ?>
      <div id="page-header">
        <ul id="page-tabs" class="nav nav-tabs">
          <li class="title active"><a href="#read" data-toggle="tab">
            <?php echo $t->page_title(); ?></a>
          </li>
          <li><a href="#edit" data-toggle="tab">Edit</a></li>
          <li><a href="#discuss" data-toggle="tab">Discuss</a></li>
          <li><a href="#history" data-toggle="tab">History</a></li>
          <li><a href="#access" data-toggle="tab">Access</a></li>
        </ul>
      </div>
    <?php endif ?>
