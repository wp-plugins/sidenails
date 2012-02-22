<?php
$sidenails = new sidenails();
echo "<div class='sidenails'>";
echo $sidenails->LastPostsImages($instance);
if ($show_credits)
{
  echo "<div class='sidenails credit'><a href='http://www.tranchesdunet.com/sidenails'>Powered by SideNails</a></div>";
}
echo "</div>";
echo "<br clear=all />";