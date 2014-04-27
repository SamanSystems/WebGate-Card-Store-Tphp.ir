<?php

/**
 * Rss Parser class
 *
 * @author 		Saeed johari <sjohari74@gmail.com>
 * @since		1.0
 * @package		extension
 * @copyright	(c)  2014 all rights reserved
 */
class Xml
{
	/**
	 * parse rss (xml)
	 *
	 * @param string $xml , xml code
	 * @access public
	 * @return object
	 */
	public function parse($xml)
	{
		preg_match_all( '/\<channel\>(.*)\<\/channel\>/is', $xml, $matches );

		if( !$matches ) {
			return [];
		}
		
		preg_match_all( '/\<(.*)\>(.*?)\<\/\\1\>/is', $matches[1][0], $matches2 );

		$result = [];  $i = 0;

		foreach( $matches2[1] as $id => $name ) {
			if( $name == 'item' ) {
				preg_match_all( '/\<(.*)\>(.*)\<\/\\1\>/is', $matches2[2][$id], $items );

				$itemTags = [];
				foreach( $items[1] as $id => $name ) {
					$itemTags[$name] = preg_replace( '/\<\!\[CDATA\[(.*)\]\]\>/', '$1', $items[2][$id] );
				}

				$result['items'][$i] = (object)$itemTags;
				$i++;
			} else {
				$result[$name] = $matches2[2][$id];
			}
		}

		unset( $matches, $i, $matches2, $itemTags );

		return (object)$result;
	}
}