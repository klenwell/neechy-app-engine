<?php

$t = $this;   # templater object

?>
      <table class="table table-condensed">
        <tr>
          <th>id</th>
          <th>editor</th>
          <th>size</th>
          <th>datetime</th>
        </tr>
        <?php foreach ( $t->data('edits') as $row ): ?>
        <tr>
          <td class=""><?php echo $row['id']; ?></td>
          <td class=""><?php echo $row['editor']; ?></td>
          <td class=""><?php echo strlen($row['body']); ?></td>
          <td class=""><?php echo $row['created_at']; ?></td>
        </tr>
        <?php endforeach ?>
      </table>
