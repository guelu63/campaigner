<?php
#$modx->regClientStartupScript($campaigner->config['jsUrl'].'widgets/extensions.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/campaigner.panel.js');
// if($modx->getOption('campgaigner.has_autonewsletter')) {
// 	$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/campaigner.auto.panel.js');
	
// } else {
	
// }
$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/autonewsletter.grid.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/newsletter.grid.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/group.grid.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/subscriber.grid.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/queue.grid.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/bounce.grid.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'/widgets/statistics.grid.js');
$modx->regClientStartupScript($campaigner->config['jsUrl'].'sections/home.js');

return '<div id="campaigner-panel-home-div"></div>';