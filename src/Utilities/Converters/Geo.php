<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\Converters;

use InvalidArgumentException;

class Geo
{

    /**
     * Convert a tile to bounding box coordinates
     *
     * See https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#ECMAScript_.28JavaScript.2FActionScript.2C_etc..29
     *
     * @param string|array $tile A tile in the format zoom/y/x or an array with those three components
     * @return array An array with the keys west, east, north, and south
     */
    public static function tileToCoords($tile)
    {
        if (!is_array($tile)) {
            $tile = explode('/', $tile);
        }

        $zoom = (int)$tile[0] ?? 0;
        $ytile = (int)$tile[1] ?? 0;
        $xtile = (int)$tile[2] ?? 0;

        $n = pow(2, $zoom);

        return [
            'west' => $xtile / $n * 360.0 - 180.0,
            'east' => ($xtile + 1) / $n * 360.0 - 180.0,
            'north' => rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n)))),
            'south' => rad2deg(atan(sinh(pi() * (1 - 2 * ($ytile + 1) / $n))))
        ];
    }

    /**
     * Convert a bounding box to a polygon
     *
     * @param array $coords
     * @return array[] An array with the coordinates of the polygon, in lng/lat-order.
     */
    public static function boxToPolygon($coords)
    {
        return [
            [
                [$coords['west'], $coords['north']],
                [$coords['east'], $coords['north']],
                [$coords['east'], $coords['south']],
                [$coords['west'], $coords['south']],
                [$coords['west'], $coords['north']]
            ]
        ];
    }

    /**
     * Calculate the outer radius from a bounding box
     *
     * @param array $box An array with the keys west, east, north, and south.
     *                   Alternatively, numeric keys in the same order.
     * @return numeric The radius in meters.
     */
    public static function boxToRadius($box)
    {
        // Accept numeric or associative array
        if (isset($box['west'], $box['east'], $box['north'], $box['south'])) {
            $west = $box['west'];
            $east = $box['east'];
            $north = $box['north'];
            $south = $box['south'];
        } elseif (array_keys($box) === range(0, 3)) {
            list($west, $east, $north, $south) = $box;
        } else {
            throw new InvalidArgumentException('Bounding box must have [west, east, north, south].');
        }

        $centerLat = ($north + $south) / 2.0;
        $centerLon = ($west + $east) / 2.0;

        return self::haversine($centerLat, $centerLon, $north, $east);
    }


    /**
     * Haversine distance in meters between two lat/lon points.
     */
    public static function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371008.8; // meters (mean Earth radius)

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat/2) * sin($dLat/2) +
            cos($lat1) * cos($lat2) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

}

