<?php
/**
*
* @package phpBB Extension - Football Football
* @copyright (c) 2016 football (http://football.bplaced.net)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB') OR !defined('IN_FOOTBALL'))
{
	exit;
}

$data_delivery = false;
$user_id = $user->data['user_id'];
$lang_dates = $user->lang['datetime'];
$index = 0;
$local_board_time = time() + (($config['board_timezone'] - $config['football_host_timezone']) * 3600); 
$sql = "(SELECT 
			m.season, 
			m.league, 
			m.matchday,
			l.league_name_short, 
			CASE m.matchday_name 
				WHEN '' 
					THEN CONCAT(m.matchday, '." . sprintf($user->lang['MATCHDAY']) . "') 
					ELSE m.matchday_name 
			END AS matchday_name, 
			CONCAT(
				CASE DATE_FORMAT(m.delivery_date,'%w')
					WHEN 0 THEN '" . $lang_dates['Sun'] . "'
					WHEN 1 THEN '" . $lang_dates['Mon'] . "'
					WHEN 2 THEN '" . $lang_dates['Tue'] . "'
					WHEN 3 THEN '" . $lang_dates['Wed'] . "'
					WHEN 4 THEN '" . $lang_dates['Thu'] . "'
					WHEN 5 THEN '" . $lang_dates['Fri'] . "'
					WHEN 6 THEN '" . $lang_dates['Sat'] . "'
					ELSE 'Error' END,
				DATE_FORMAT(m.delivery_date,' %d.%m.%y %H:%i')
			) as delivery_time,
			m.delivery_date AS delivery,
			SUM(IF(((b.goals_home = '') OR (b.goals_guest = '')), 0, 1)) AS bets_count,
			COUNT(*) AS matches_count,
			SUM(IF(eb.extra_no > 0, IF(eb.bet = '', 0, 1), 0)) AS extra_bets_count,
			SUM(IF(e.extra_no > 0, 1, 0)) AS extra_count
		FROM " . FOOTB_MATCHDAYS . " AS m
		JOIN " . FOOTB_LEAGUES . " AS l ON(l.season = m.season AND l.league = m.league)
		JOIN " . FOOTB_MATCHES . " AS ma ON (ma.season = m.season AND ma.league = m.league AND ma.matchday = m.matchday AND ma.status = 0)
		JOIN " . FOOTB_BETS . " AS b ON (b.season = m.season AND b.league = m.league AND b.match_no = ma.match_no AND b.user_id = $user_id)
		LEFT JOIN " . FOOTB_EXTRA . " AS e ON (e.season = m.season AND e.league = m.league AND e.matchday = m.matchday  AND e.extra_status = 0)
		LEFT JOIN " . FOOTB_EXTRA_BETS . " AS eb ON (eb.season = m.season AND eb.league = m.league AND eb.extra_no = e.extra_no AND eb.user_id = $user_id)
		WHERE m.delivery_date > FROM_UNIXTIME('$local_board_time') 
			AND m.status <= 0 
		GROUP BY m.delivery_date, m.league, b.user_id
	) 
	UNION
	(SELECT 
			m.season, 
			m.league, 
			m.matchday,
			l.league_name_short, 
			CASE m.matchday_name 
				WHEN '' 
					THEN CONCAT(m.matchday, '." . sprintf($user->lang['MATCHDAY']) . "') 
					ELSE m.matchday_name 
			END AS matchday_name, 
			CONCAT(
				CASE DATE_FORMAT(m.delivery_date_2,'%w')
					WHEN 0 THEN '" . $lang_dates['Sun'] . "'
					WHEN 1 THEN '" . $lang_dates['Mon'] . "'
					WHEN 2 THEN '" . $lang_dates['Tue'] . "'
					WHEN 3 THEN '" . $lang_dates['Wed'] . "'
					WHEN 4 THEN '" . $lang_dates['Thu'] . "'
					WHEN 5 THEN '" . $lang_dates['Fri'] . "'
					WHEN 6 THEN '" . $lang_dates['Sat'] . "'
					ELSE 'Error' END,
				DATE_FORMAT(m.delivery_date_2,' %d.%m.%y %H:%i')
			) as delivery_time,
			m.delivery_date_2 AS delivery ,
			SUM(IF(((b.goals_home = '') OR (b.goals_guest = '')), 0, 1)) AS bets_count,
			COUNT(*) AS matches_count,
			0 AS extra_bets_count,
			0 AS extra_count
		FROM " . FOOTB_MATCHDAYS . " AS m
		JOIN " . FOOTB_LEAGUES . " AS l ON(l.season = m.season AND l.league = m.league)
		JOIN " . FOOTB_MATCHES . " AS ma ON (ma.season = m.season AND ma.league = m.league AND ma.matchday = m.matchday AND ma.status = -1)
		JOIN " . FOOTB_BETS . " AS b ON (b.season = ma.season AND b.league = ma.league AND b.match_no = ma.match_no AND b.user_id = $user_id)
		WHERE m.delivery_date_2 > FROM_UNIXTIME('$local_board_time') 
			AND m.status <= 0
		GROUP BY m.delivery_date, m.league, b.user_id
	) 
	UNION
	(SELECT 
			m.season, 
			m.league, 
			m.matchday,
			l.league_name_short, 
			CASE m.matchday_name 
				WHEN '' 
					THEN CONCAT(m.matchday, '." . sprintf($user->lang['MATCHDAY']) . "') 
					ELSE m.matchday_name 
			END AS matchday_name, 
			CONCAT(
				CASE DATE_FORMAT(m.delivery_date_3,'%w')
					WHEN 0 THEN '" . $lang_dates['Sun'] . "'
					WHEN 1 THEN '" . $lang_dates['Mon'] . "'
					WHEN 2 THEN '" . $lang_dates['Tue'] . "'
					WHEN 3 THEN '" . $lang_dates['Wed'] . "'
					WHEN 4 THEN '" . $lang_dates['Thu'] . "'
					WHEN 5 THEN '" . $lang_dates['Fri'] . "'
					WHEN 6 THEN '" . $lang_dates['Sat'] . "'
					ELSE 'Error' END,
				DATE_FORMAT(m.delivery_date_3,' %d.%m.%y %H:%i')
			) as delivery_time,
			m.delivery_date_3 AS delivery, 
			SUM(IF(((b.goals_home = '') OR (b.goals_guest = '')), 0, 1)) AS bets_count,
			COUNT(*) AS matches_count,
			0 AS extra_bets_count,
			0 AS extra_count
		FROM " . FOOTB_MATCHDAYS . " AS m
		JOIN " . FOOTB_LEAGUES . " AS l ON(l.season = m.season AND l.league = m.league)
		JOIN " . FOOTB_MATCHES . " AS ma ON (ma.season = m.season AND ma.league = m.league AND ma.matchday = m.matchday AND ma.status = -2)
		JOIN " . FOOTB_BETS . " AS b ON (b.season = ma.season AND b.league = ma.league AND b.match_no = ma.match_no AND b.user_id = $user_id)
		WHERE m.delivery_date_3 > FROM_UNIXTIME('$local_board_time')
			AND m.status <= 0
		GROUP BY m.delivery_date, m.league, b.user_id
	) 
	ORDER BY delivery, league";
	
$result = $db->sql_query($sql);
while($row = $db->sql_fetchrow($result) AND $index < 11)
{
	$index++;
	$data_delivery = true;
	$row_class = (!($index % 2)) ? 'bg1 row_light' : 'bg2 row_dark';
		
	$template->assign_block_vars('delivery', array(
		'ROW_CLASS' 	=> $row_class,
		'U_BET_LINK'	=> $this->helper->route('football_main_controller', array('side' => 'bet', 's' =>  $row['season'], 'l' => $row['league'], 'm' => $row['matchday'])),
		'LEAGUE_SHORT' 	=> $row['league_name_short'],
		'MATCHDAY_NAME' => $row['matchday_name'],
		'COLOR'			=> ($row['bets_count'] == $row['matches_count'] && $row['extra_bets_count'] == $row['extra_count']) ? 'green' : 'red',
		'TITLE'			=> ($row['bets_count'] == $row['matches_count']) ? sprintf($user->lang['DELIVERY_READY']) : sprintf($user->lang['DELIVERY_NOT_READY']),
		'DELIVERY' 		=> $row['delivery_time'],
		)
	);
}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_DISPLAY_DELIVERY' 	=> $data_delivery,
	'S_DATA_DELIVERY' 		=> $data_delivery,
	)
);

?>