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
          <td><?php echo $t->data('db')->getAttribute(PDO::ATTR_CONNECTION_STATUS); ?></td>
        </tr>
      </table>
      <h4>Tables</h4>
      <table class="table">
        <tr>
          <th>Table</th>
          <th>Created?</th>
          <th>Record Count</th>
        </tr>
        <?php foreach ( NeechyDatabase::core_model_classes() as $model_class ): ?>
        <tr>
          <td><?php echo $model_class::table_name(); ?></td>
          <td><?php echo $model_class::table_exists() ? 'Yes' : 'No'; ?></td>
          <td><?php echo ( $model_class::table_exists() ) ? $model_class::count() : 0; ?></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <p>- Reset or Install Button</p>
    </div>
