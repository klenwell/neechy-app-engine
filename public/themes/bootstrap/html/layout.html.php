<!DOCTYPE html>
<html lang="en">
  <head>

    {{ head }}

  </head>
  <body>

    {{ navbar }}

    <div class="container">
      {{ header }}

      <div id="dynamic-content">
        {{ content }}
      </div>
    </div>

    {{ footer }}

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script
      src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js">
    </script>
    <script
      src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js">
    </script>
    <script
      src="templates/bootstrap/js/onload.js">
    </script>

  </body>
</html>
