#!/usr/bin/php
<?php
/**
 * moOde audio player (C) 2014 Tim Curtis
 * http://moodeaudio.org
 *
 * This Program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3, or (at your option)
 * any later version.
 *
 * This Program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

set_include_path('/var/www/inc');
require_once 'playerlib.php';

$option = isset($argv[1]) ? $argv[1] : '';

switch ($option) {
	case '--bluetooth':
		restart_bluetooth();
		break;
	case '--airplay':
		restart_airplay();
		break;
	case '--spotify':
		restart_spotify();
		break;
	case '--squeezelite':
		restart_squeezelite();
		break;
	case '--roonbridge':
		restart_roonbridge();
		break;
	default:
		echo
"Usage: restart-renderer [OPTION]
Moode renderer restarter

With no OPTION print the help text and exit.

 --bluetooth\t\tRestart bluetooth
 --airplay\t\tRestart airplay
 --spotify\t\tRestart spotify
 --squeezelite\tRestart squeezelite
 --roonbridge\t\tRestart roonbridge\n";
		break;
}

function restart_bluetooth() {
	// stop bluetooth
	sysCmd('systemctl stop bluealsa');
	sysCmd('systemctl stop bluetooth');
	sysCmd('killall bluealsa-aplay');
	sysCmd('/var/www/vol.sh -restore');

	// reset to inactive
	session_id(playerSession('getsessionid'));
	session_start();
	playerSession('write', 'btactive', '0');
	// dismiss active screen
	sendEngCmd('btactive0');
	$GLOBALS['btactive'] = '0'; // TODO: Delete line because $btactive is not defined in this script or playerlib.php
	session_write_close();

	// restore MPD volume and start bluetooth
	sysCmd('/var/www/vol.sh -restore');
	startBt();
}

function restart_airplay() {
	session_id(playerSession('getsessionid'));
	session_start();
	stopAirplay();
	startAirplay();
	session_write_close();
}

function restart_spotify() {
	session_id(playerSession('getsessionid'));
	session_start();
	stopSpotify();
	startSpotify();
	session_write_close();
}

function restart_squeezelite() {
	session_id(playerSession('getsessionid'));
	session_start();
	stopSqueezelite();
	playerSession('write', 'rsmaftersl', 'No');
	startSqueezelite();
	session_write_close();
}

function restart_roonbridge() {
	session_id(playerSession('getsessionid'));
	session_start();
	stopRoonBridge();
	startRoonBridge();
	session_write_close();
}
