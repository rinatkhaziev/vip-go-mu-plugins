<?php

namespace Automattic\VIP\Performance;

class VIP_Do_Pings {

	const CRON_HOOK = 'vip_do_all_pings_hook';

	const OPTION = 'vip_do_pings_version';

	const VERSION = '1.0';

	public static function init() {
		add_action( 'schedule_event', [ __CLASS__ . 'disable_pings' ] );
		add_action( 'init', [ __CLASS__, self::CRON_HOOK ] );
		add_action( self::CRON_HOOK, '\do_all_pings' );
	}

	public static function disable_pings( $event ) {
		// Already blocked, carry on.
		if ( ! is_object( $event ) ) {
			return $event;
		}

		if ( 'do_pings' === $event->hook ) {
			return false;
		}

		return $event;
	}

	public static function schedule_do_all_pings() {
		if ( true === apply_filters( 'wpcom_vip_disable_do_all_pings_cron', false ) ) {
			self::maybe_clear_do_all_pings();
			return;
		}

		if ( ! is_same_version() ) {
			self::clear_do_all_pings();
			update_option( self::OPTION, self::VERSION );
		}

		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), 'daily', self::CRON_HOOK );
		}
	}

	public static function maybe_clear_do_all_pings() {
		if ( wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_clear_scheduled_hook( self::CRON_HOOK );
		}
	}

	private static is_same_version() {
		return self::VERSION === get_option( self::OPTION );
	}

}

add_action( 'init', [ 'Automattic\VIP\Performance\VIP_Do_All_Pings', 'init' ] );
