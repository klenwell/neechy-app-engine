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
          <td class="id"><a href="<?php echo $row['history_url']; ?>"><?php echo $row['id']; ?></a></td>
          <td class="editor"><?php echo $row['editor']; ?></td>
          <td class="body"><?php echo strlen($row['body']); ?></td>
          <td class="created_at"><?php echo $row['created_at']; ?></td>
        </tr>
        <?php endforeach ?>
      </table>
