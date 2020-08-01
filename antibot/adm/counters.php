<?php
// Last update date: 2020.05.06
if(!defined('ANTIBOT')) die('access denied');

$title = abTranslate('Statistics');

$list = $antibot_db->query("SELECT * FROM counters ORDER BY date DESC LIMIT 30;"); 
if ($ab_config['memcached_counter'] == 1) {
$cron_update_time = $ab_memcached->get($ab_config['memcached_prefix'].'update') + 0;
} else {
$cron_update_time = 0;
}

$content = '<p>'.abTranslate('Until the next update statistics remains:').' '.(600 - (time() - $cron_update_time)).' '.abTranslate('sec.').'</p>
<table class="table table-bordered table-hover table-sm">
<thead class="thead-light">
<tr>
<th>'.abTranslate('Date').'</th>
<th style="color:red;">'.abTranslate('Failed').'</th>
<th style="color:green;">'.abTranslate('Automatic').'</th>
<th style="color:green;">'.abTranslate('Clicked').'</th>
<th>'.abTranslate('Unique').'</th>
<th>'.abTranslate('Hits').'</th>
<th>'.abTranslate('Good bots').'</th>
<th style="color:red;">'.abTranslate('Blocked').'</th>
<th style="color:red;">'.abTranslate('Fake bots').'</th>
</tr>
</thead>
<tbody>
';
while ($echo = $list->fetchArray(SQLITE3_ASSOC)) {
$no = $echo['test'] - $echo['auto'] - $echo['click'];
$content .= '<tr>
<td>'.date("Y.m.d", strtotime($echo['date'])).'</td>
<td>'.$no.'</td>
<td>'.$echo['auto'].'</td>
<td>'.$echo['click'].'</td>
<td>'.$echo['uusers'].'</td>
<td>'.$echo['husers'].'</td>
<td>'.$echo['whits'].'</td>
<td>'.$echo['bbots'].'</td>
<td>'.$echo['fakes'].'</td>
</tr>';
}
$content .= '</tbody>
</table>
<p><strong>'.abTranslate('Failed').'</strong> - '.abTranslate('visitors (mostly the bad bots) who didn\'t make to pass AntiBot.').'<br />
<strong>'.abTranslate('Automatic').'</strong> - '.abTranslate('visitors who successfully passed the test.').'<br />
<strong>'.abTranslate('Clicked').'</strong> - '.abTranslate('visitors who did not pass the automatic check, but clicked on the button.').'<br />
<strong>'.abTranslate('Unique').'</strong> - '.abTranslate('the number of unique visitors who passed the AntiBot check.').'<br />
<strong>'.abTranslate('Hits').'</strong> - '.abTranslate('the number of views by visitors who passed the AntiBot check.').'<br />
<strong>'.abTranslate('Good bots').'</strong> - '.abTranslate('the number of hits by good bots (allowed in the conf.php).').'<br />
<strong>'.abTranslate('Blocked').'</strong> - '.abTranslate('blocked hits according to your rules (by ip, country, language, referrer, ptr).').'<br />
<strong>'.abTranslate('Fake bots').'</strong> - '.abTranslate('hits by fake bots that disguise themselves as good bots.').'</p>
';
