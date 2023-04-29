<style>
	.a { background-color: red; color: white; }	
</style>

<?php

echo "<table>";
for($a=0; $a<=10; $a++) {
	echo '<tr>';
	for($b=0; $b<=10; $b++) {
		echo '<td class="'.($a==0 || $b==0? 'a': '' ).'">' . ($a==0?$b:($b==0?$a:$a*$b));
	}
}
echo "</table>";
echo "<hr>";
echo "wykonał: Bartosz Bryniarski";