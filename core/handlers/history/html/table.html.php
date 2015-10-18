<?php

$t = $this;   # templater object
$page_url = NeechyPath::url($t->request->page, 'page');

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
          <td class="id"><?php echo $row['id']; ?></td>
          <td class="editor"><?php echo $row['editor']; ?></td>
          <td class="body"><?php echo strlen($row['body']); ?></td>
          <td class="created_at"><?php echo $row['created_at']; ?></td>
        </tr>
        <?php endforeach ?>
      </table>

      <p><a href="<?php echo $page_url; ?>">return to read view</a></p>
