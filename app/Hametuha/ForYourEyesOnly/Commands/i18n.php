<?php

namespace Hametuha\ForYourEyesOnly\Commands;


use Hametuha\ForYourEyesOnly;

class i18n extends \WP_CLI_Command {

	const COMMAND_NAME = 'fyeo-i18n';

	/**
	 * Make i18n ready JSON from pot JSON.
	 *
	 * @synopsys [<locale>]
	 */
	public function __invoke( $args ) {
		$prefix = 'fyeo-';
		global $wp_scripts;
		$locale = $args[0] ?? get_locale();
		$json = $this->get_whole_json( $locale );
		if ( is_wp_error( $json ) ) {
			\WP_CLI::error( $json->get_error_message() );
		}
		foreach ( $wp_scripts->registered as $handle => $option ) {
			if ( 0 !== strpos( $handle, $prefix ) ) {
				continue;
			}
			$src = explode( 'wp-content', $option->src );
			$src[0] = ABSPATH;
			$src = implode( 'wp-content', $src );
			$js_string = file_get_contents( $src );
			// Copy json and empty message body.
			$handle_json = array_merge( [], $json );
			$handle_json[ 'locale_data' ][ 'messages' ] = [];
			foreach ( $json[ 'locale_data' ][ 'messages' ] as $original => $translated ) {
				// If this is setting, copy left.
				if ( '' === $original || false !== strpos( $js_string, $original ) ) {
					$handle_json[ 'locale_data' ][ 'messages' ][ $original ] = $translated;
				}
			}
			// If empty, nothing to save.
			if ( 2 > count( $handle_json[ 'locale_data' ][ 'messages' ] ) ) {
				\WP_CLI::line( sprintf( '%s has no string to be translated.', $handle ) );
				continue;
			}
			// Save json as handle name.
			$handle_path = ForYourEyesOnly::get_instance()->dir . "/languages/fyeo-{$locale}-{$handle}.json";
			$result = file_put_contents( $handle_path, json_encode( $handle_json ) );
			if ( $result ) {
				\WP_CLI::line( sprintf( 'Saved a translation JSON of %s in %s.', $handle, $handle_path ) );
			} else {
				\WP_CLI::warning( sprintf( 'Failed to save translation JSON of %s.', $handle ) );
			}
		}
		// Finish.
		\WP_CLI::success( 'Translation JSON generation finished.' );
	}

	/**
	 * Get JSON file.
	 *
	 * @param string $locale
	 * @return array|\WP_Error
	 */
	private function get_whole_json( $locale ) {
		$file = ForYourEyesOnly::get_instance()->dir . "/languages/fyeo-{$locale}.json";
		if ( ! file_exists( $file ) ) {
			return new \WP_Error( 'not_found', sprintf( 'JSON file `%s` should exist.', $file ) );
		}
		$content = file_get_contents( $file );
		$json = json_decode( $content, true );
		if ( ! $json ) {
			return new \WP_Error( 'failed', 'Failed to parse JSON.' );
		}
		return $json;
	}
}
