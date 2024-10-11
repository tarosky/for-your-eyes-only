/**
 * Build JS.
 *
 * @see https://github.com/tarosky/workflows/tree/main/boilerplate/bin
 */

/* eslint-disable no-console */

const fs = require( 'fs' );
const { glob } = require( 'glob' );
const { dumpSetting } = require( '@kunoichi/grab-deps' );

const command = process.argv[ 2 ];

/**
 * Extract license header from JS file.
 *
 * @param {string} path
 * @param {string} src
 * @param {string} dest
 */
const extractHeader = ( path, src, dest ) => {
	const target = path.replace( src, dest ) + '.LICENSE.txt';
	const content = fs.readFileSync( path, 'utf8' );
	if ( ! content ) {
		return false;
	}
	const match = content.match( /^(\/\*{1,2}!.*?\*\/)/ms );
	if ( ! match ) {
		return false;
	}
	fs.writeFileSync( target, match[ 1 ] );
};

switch ( command ) {
	case 'dump':
		dumpSetting( 'assets' );
		console.log( 'wp-dependencies.json updated.' );
		break;
	case 'license':
		console.log( 'Extracting js files...' );
		// Put license.txt.
		glob( [ 'src/js/**/*.js' ] ).then( ( res ) => {
			res.map( ( path ) => {
				return extractHeader( path, 'src/js', 'assets/js' );
			} );
		} );
		console.log( 'Done.' );
		break;
}
