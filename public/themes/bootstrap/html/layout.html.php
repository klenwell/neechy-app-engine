<?php
  $t = $this;   # NeechyTemplater
?>
<!DOCTYPE html>
<html lang="en">
  <head>

    {{ head }}

    {{ head_appendix }}

  </head>
  <body>

    {{ navbar }}

    <?php echo $t->unflash(); ?>

    <div class="container">
      {{ content }}
    </div>

    {{ footer }}

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script
      src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js">
    </script>
    <script
      src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js">
    </script>
    <script
      src="/themes/bootstrap/js/neechy.js">
    </script>

    {{ body_appendix }}

  </body>
</html>
