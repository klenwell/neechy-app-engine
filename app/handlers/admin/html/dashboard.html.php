<?php
#
# Admin Dashboard
#

$t = $this;   # templater object
#$t->append_to_head($t->css_link($t->css_href('form.css')));

?>
    <div class="admin dashboard">
      <h2>Admin Dashboard</h2>
      <h3>Database Status</h3>
      <table class="table">
        <tr>
          <th>Database</th>
          <th>Host</th>
          <th>User</th>
          <th>Connection Status</th>
        </tr>
        <tr>
          <td><?php echo NeechyConfig::get('mysql_database'); ?></td>
          <td><?php echo NeechyConfig::get('mysql_host'); ?></td>
          <td><?php echo NeechyConfig::get('mysql_user'); ?></td>
          <td><?php echo NeechyDatabase::connection_status(); ?></td>
        </tr>
      </table>
      <h4>Tables</h4>
      <table class="table">
        <tr>
          <th>Table</th>
          <th>Created?</th>
          <th>Record Count</th>
        </tr>
        <?php foreach ( $t->data('tables') as $table_name => $table ): ?>
        <tr>
          <td><?php echo $table_name; ?></td>
          <td><?php echo $table['exists'] ? 'Yes' : 'No'; ?></td>
          <td><?php echo $table['count']; ?></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <div class="form-group">
        <?php echo $t->open_form('', 'post', array('class' => 'form-inline')); ?>
          <?php if ( $t->data('database_installed') ): ?>
            <?php if ( $t->data('confirm-reset-db') ): ?>
              <h4>Are you <em>sure</em> you want to reset the database? If so, type RESET below:</h4>
              <input type="text" name="confirmed-reset-db" class="form-control"
                placeholder="Type RESET here" />
            <?php endif; ?>
            <button class="btn btn-danger" type="submit">Reset Database</button>
          <?php else: ?>
            <button class="btn btn-primary" type="submit">Install Database</button>
          <?php endif; ?>
        <?php echo $t->close_form(($t->data('database_installed')) ? 'reset-db' : 'install-db'); ?>
      </div>
    </div>
