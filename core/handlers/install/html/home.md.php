<?php
#
# Default New Home Page
# This gets saved when install script is run
#

$t = $this;   # templater object

?>
## Congratulation on your new Neechy Wiki

You're new wiki, <?php echo NeechyConfig::get('title'); ?>, is ready!

### Login and Start Editing

Once you log in, you can start editing wiki pages. Neechy Wiki supports an extended markdown syntax. For more information, see the NeechyFormatting page.

### Administration

If you are an administrator, you'll have additional tools at your disposal to help you manage your wiki once you log in.

### Developers

Neechy is designed to allow you, as a developer, hack on your wiki. Extend existing features and add your own. For more information, visit the Neechy github site.
