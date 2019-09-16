#Section 1 - Hello World

1. Login to AFS
	1. ssh yourucid@afsconnect1.njit.edu
2. Run __pwd__
	1. You should be the last folder as your ucid
3. Move into your public_html
	1. __cd public_html__
	2. __pwd__ should how show your ucid/public_html
4. Create a new file with nano (alternative vi)
	1. __nano index.php__
	2. This should open up an empty editor if you didn't have index.php exist already
5. Add a sample to your index.php
```<?php
	echo "Hello World";
?>```
