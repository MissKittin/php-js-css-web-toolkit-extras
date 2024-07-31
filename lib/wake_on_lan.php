<?php
	/*
	 * Send WOL package from PHP
	 *
	 * Note:
	 *  throws an wake_on_lan_exception on error
	 *
	 * Warning:
	 *  this function was not tested
	 *  sockets extension is required
	 *
	 * Author: Mez
	 * Source: https://stackoverflow.com/questions/6055293/wake-on-lan-script-that-works
	 */

	class wake_on_lan_exception extends Exception {}

	if(extension_loaded('sockets'))
	{
		function wake_on_lan(
			string $mac,
			string $broadcast='255.255.255.255',
			int $port=7
		){
			$socket=socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

			if($socket === false)
				return false;

			if(socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, true) === false)
				return false;

			$packet=sprintf(
				'%s%s',
				str_repeat(chr(255), 6),
				str_repeat(pack(
					'H*',
					preg_replace('/[^0-9a-fA-F]/', '', $mac)
				), 16)
			);

			if(socket_sendto(
				$socket,
				$packet,
				strlen($packet),
				0,
				$broadcast,
				$port
			) === false){
				socket_close($socket);
				return false;
			}

			socket_close($socket);

			return true;
		}
	}
	else
	{
		function wake_on_lan()
		{
			throw new wake_on_lan_exception('sockets extension is not loaded');
		}
	}
?>