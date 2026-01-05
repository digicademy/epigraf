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

/**
 *  Class for parsing date information.
 *
 *  Two static functions are essential:
 *
 *  - normalize: check a date and standardize it
 *    so that it can be stored in the database.
 *    It accepts a text entry (e.g., 1.H. 15.Jh.),
 *    parses it, and then generates a normalized text entry.
 *
 *  - encode: accept a text entry and generate a sort key
 *    that can be stored in the database.
 *
 *  The start and end years of time periods can be extracted using the following functions:
 *  - years()
 *  - minyear()
 *  - maxyear()
 *
 *
 *  The text entries are structured as follows and run through
 *  the corresponding functions during parsing:
 *
 *  - Multiple dates are separated by "und" (alternatively by "/" or ",") (parseHistoricDateList)
 *    The slash "/" can only be used if ISO 8601 date ranges are not involved (see single date).
 *  - A single date can be marked as uncertain with “?” (parseHistoricDateRange).
 *  - A single date consists of one or two partial dates (parseHistoricDateRange).
 *  - Partial dates within a single date are separated by "-", alternatively by "–" (n-dash),
 *    "bis" or "oder" (parseHistoricDateRange).
 *    In ISO 8601 date ranges the slash "/" separates the beginning and end of a time span.
 *
 *  The components of a partial date include (parseHistoricDate):
 *
 *  - 'number' => false,   // A number for the year or century [or false]
 *  - 'day' => ['month' => false, 'day' => false],
 *                         // Month and day in ISO notation [or false for each element]
 *  - 'hour' => ['hour' => false, 'minute' => false, 'second' => false]
 *                         // Hour, minute and second in ISO notation [or false for each element]
 *  - 'fraction' => [‘numerator_from’ => false, ‘numerator_to’, ‘denominator’ => false]
 *                         // Numbers for the fractions of centuries [or false for each element]
 *  - 'unit' => '',        // Values: 'jahrhundert', 'jahreszahl', 'tag', 'stunde', 'minute', 'sekunde', or an empty string
 *  - 'negative' => false, // true = before Christ, false = after Christ
 *  - 'modifier' => '',    // Values: 'um', 'vor', 'nach', 'oder früher', 'oder später', 'ende', 'anfang', 'mitte', 'teil', [or an empty string]
 *  - 'short' => false,    // true = short form at the end of a period, e.g. 1922-23 [or false]
 *  - 'invalid' => false   // true = An invalid time specification [or false]
 *
 *  During parsing, different spellings are standardized.
 *
 *  # Date key
 *
 *  The date key is used to establish an order that reflects the following criteria:
 *  - Every date represents a period of time. A date consisting of a year covers the entire year.
 *  - Each date is sorted based on its reference point and how far it extends into the past and future.
 *    Dates extending farther back in time appear before dates extending less far back.
 *    Dates extending further into the future appear after dates that do not extend as far into the future.
 *    Therefore, a potentially older date is sorted before a potentially younger date,
 *    and a potentially younger date is sorted after a potentially older date.
 *
 *  The sort key is a concatenation of three reference points written in ISO 8601 notation.
 *  The second and third reference points indicate the start and end of the specified date range.
 *  The first reference point depends on the type of date. Modifiers such as "um", "nach", "Anfang" and "Ende"
 *  play a role here. In most cases, the first and third reference points correspond to each other.
 *  A lexicographical sorting of keys structured in this way implements the above sorting criteria.
 *
 *  The content of DELTA_DATE determines how date specifications are interpreted, i.e.,
 *  how far they extend into the past or future relative to a base year of the time specification.
 */
class HistoricDates
{

    /**
     * Patterns
     *
     * @const string ISO_PATTERN Pattern for ISO 8601 date strings (e.g. 2023-10-05T14:30:00)
     */
    public const ISO_PATTERN = "(?<sign>-|\+)?(?<year>[0-9]{4})-?(?<month>1[0-2]|0[1-9])-?(?<day>3[01]|0[1-9]|[12][0-9])"
        . "(T(?<hour>[0-1][0-9]|2[0-4]):(?<minute>[0-5][0-9])(:(?<second>[0-5][0-9](\.[0-9]+)?))?)?";
    /**
     * Date constants
     */
    const UNIT_CENTURY = 'jahrhundert';
    const UNIT_YEAR = 'jahreszahl';
    const UNIT_DAY = 'tag';
    const UNIT_HOUR = 'stunde';
    const UNIT_MINUTE = 'minute';
    const UNIT_SECOND = 'sekunde';

    const MODIFIER_EARLIER = 'frueher';
    const MODIFIER_LATER = 'spaeter';
    const MODIFIER_BEGIN = 'anfang';
    const MODIFIER_END = 'ende';
    const MODIFIER_MID = 'mitte';
    const MODIFIER_PART = 'teil';
    const MODIFIER_AROUND = 'um';
    const MODIFIER_AFTER = 'nach';
    const MODIFIER_BEFORE = 'vor';
    const DATE_TIME_FORMAT = 'Y-m-d\TH:i:s';
    const PRIORITY_END = 0x001;
    const PRIORITY_AROUND = 0x002;
    const PRIORITY_EARLIER = 0x002;
    const PRIORITY_UNCERTAIN = 0x004;

    // TODO: replace all tokens by constants
    const TOKEN_MODIFIER = [
        'anfang' => self::MODIFIER_BEGIN,
        'ende' => self::MODIFIER_END,
        'um' => self::MODIFIER_AROUND,
        'mitte' => self::MODIFIER_MID,
        'nach' => self::MODIFIER_AFTER,
        'vor' => self::MODIFIER_BEFORE
    ];

    const TOKEN_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    const TOKEN_MONTHS = [
        'jan' => 1,
        'feb' => 2,
        'mär' => 3,
        'apr' => 4,
        'mai' => 5,
        'jun' => 6,
        'jul' => 7,
        'aug' => 8,
        'sep' => 9,
        'okt' => 10,
        'nov' => 11,
        'dez' => 12
    ];

    const EMPTY_DAT = [
        'number' => false,    // {Zahl}
        'unit' => '', // {nn, jahrhundert, jahreszahl, tag}
        'day' => ['month' => false, 'day' => false], // {Zahl}
        'hour' => ['hour' => false, 'minute' => false, 'second' => false],
        'fraction' => ['numerator_from' => false, 'numerator_to' => false, 'denominator' => false], // {Zahlen}
        'negative' => false, // Before or after christ
        'modifier' => '', //{nn, um, vor, nach, teil, ende, anfang, mitte, oder früher, oder später}
        'short' => false,
        'invalid' => false,
        'isiso' => false
    ];

    const EMPTY_DATE_ARRAY = [
        'invalid' => false,
        'uncertain' => false,
        'or' => false,
        'begin' => self::EMPTY_DAT,
        'end' => self::EMPTY_DAT
    ];

    // A date sort key consists of three reference points.
    // DELTA_DATE holds the offsets with respect to 1 Jan of the anchor year
    const DELTA_DATE = [
        'plain_day' => [
            'r1' => '+1 day -1 second',
            'r2' => '',
            'r3' => '+1 day -1 second'
        ],
        'plain_year' => [
            'r1' => '+1 year -1 second',
            'r2' => '',
            'r3' => '+1 year -1 second'
        ],
        self::MODIFIER_BEFORE => [
            'r1' => '',
            'r2' => '-9 years',
            'r3' => ''
        ],
        self::MODIFIER_AROUND  => [
            'r1' => '+1 year -1 second',
            'r2' => '-8 years',
            'r3' => '+10 years -1 second'
        ],
        self::MODIFIER_AFTER => [
            'r1' => '+1 year -1 second',
            'r2' => '+1 year -1 second',
            'r3' => '+10 years -1 second'
        ],
        self::MODIFIER_EARLIER => [
            'r1' => '+1 year -1 second',
            'r2' => '-8 years',
            'r3' => '+1 years -1 second'
        ],
        self::MODIFIER_LATER => [
            'r1' => '+1 year -1 second',
            'r2' => '',
            'r3' => '+10 years -1 second'
        ],
        'plain_century' => [
            'r1' => '+101 years -1 second',
            'r2' => '+1 year',
            'r3' => '+101 years -1 second'
        ],
        self::MODIFIER_BEGIN => [
            'r1' => '+1 year',
            'r2' => '+1 year',
            'r3' => '+10 years -1 second'
        ],
        self::MODIFIER_MID => [
            'r1' => '+51 years -1 second',
            'r2' => '+42 years',
            'r3' => '+60 years -1 second'
        ],
        self::MODIFIER_END => [
            'r1' => '+101 year -1 second',
            'r2' => '+92 years',
            'r3' => '+101 years -1 second'
        ]
    ];

    /**
     * @const int DATE_VERSION Version number of the date key
     */
    const DATE_VERSION = 5;

    /**
     *  Normalizes a natural language date
     *
     * Parses a date string and generated a new date string
     * from the parsed date
     *
     * @param string $date Natural language date, e.g. "15. Jh."
     * @return string|null Normalized natural language date. Null if the date is invalid.
     */
    static function normalize(string $date): string|null
    {
        $hd = HistoricDates::parseHistoricDateList($date);
        return HistoricDates::listToString($hd);
    }


    /**
     *  Derives a sort key from a natural language date
     *
     * @param string $date A natural language date, e.g. "15. Jh."
     * @return string|null A key that can be used to sort multiple dates. Null if the date is invalid.
     */
    static function encode(string $date): string|null
    {
        $hd = HistoricDates::parseHistoricDateList($date);
        return HistoricDates::listToKey($hd);
    }

    /**
     * Parse a natural language date
     *
     * Multiple dates can be combined with "und", "," or "/".
     * The dash "/" can only be used if no ISO date ranges are involved.
     *
     * @param string $date A natural language date
     * @return array An array of date arrays. A date array contains the components of the parsed dates.
     */
    static function parseHistoricDateList(string $date): array
    {
        // First, determine if the dates are in ISO notation.
        // Processing mixed lists requires somewhat complicated recursive parsing.
        $iso_found = preg_match("~" . self::ISO_PATTERN . "~", $date);
        $date = str_replace('und', ',', $date);
        if ($iso_found === 1) {
            $dates = explode(',', $date);
            return array_map([__CLASS__, 'parseHistoricISODateRange'], $dates);
        }
        else {
            $date = str_replace('/', ',', $date);
            $dates = explode(',', $date);
            return array_map([__CLASS__, 'parseHistoricDateRange'], $dates);
        }
    }

    static function parseHistoricISODate(string $date): array
    {
        $dat = HistoricDates::EMPTY_DAT;

        $matches = [];
        $iso_found = preg_match("~" . self::ISO_PATTERN . "~", $date, $matches);
        if ($iso_found === 1) {
            $dat['isiso'] = true;
            $dat['unit'] = self::UNIT_DAY;
            $dat['negative'] = $matches['sign'] == '-';
            $dat['number'] = $matches['year'];
            $dat['day'] = ['month' => $matches['month'], 'day' => $matches['day']];
            if (array_key_exists('hour', $matches)) {
                $dat['hour']['hour'] = $matches['hour'];
                $dat['unit'] = self::UNIT_HOUR;
                if (array_key_exists('minute', $matches)) {
                    $dat['hour']['minute'] = $matches['minute'];
                    $dat['unit'] = self::UNIT_MINUTE;
                }
                if (array_key_exists('second', $matches)) {
                    $dat['hour']['second'] = $matches['second'];
                    $dat['unit'] = self::UNIT_SECOND;
                }
            }
            $dat['invalid'] = false;
        }
        else {
            $dat['invalid'] = true;
        }
        return $dat;
    }

    static function parseHistoricISODateRange(string $date): array
    {
        $date_parsed = self::EMPTY_DATE_ARRAY;

        if (trim($date) === '') {
            return $date_parsed;
        }

        //Unsicher
        $date_parsed['uncertain'] = strpos($date, '?') !== false;
        $date = str_replace('?', '', $date);

        // Oder oder Von-bis-Angabe
        $date_parsed['or'] = strpos($date, 'oder') !== false;

        //Mehrere Angaben trennen
        $date = str_replace('oder', '/', $date);
        $date = str_replace('bis', '/', $date);
        $parts = explode('/', $date);
        $von = (count($parts) > 0) ? $parts[0] : '';
        $bis = (count($parts) > 1) ? $parts[1] : '';

        //Berechnen
        $date_parsed['begin'] = HistoricDates::parseHistoricISODate($von);
        $date_parsed['end'] = HistoricDates::parseHistoricISODate($bis);

        if ($date_parsed['begin']['invalid']) {
            $date_parsed['invalid'] = true;
        }

        return $date_parsed;

    }

    /**
     * Parse a date range
     *
     * Date ranges can be implicit as in single date terms
     * or explicit as in two date terms connected by a hyphen.
     *
     * 16.Jh.
     * 1510
     * 1510-1520
     *
     * @param string $date A natural language date
     * @return array
     */
    static function parseHistoricDateRange(string $date): array
    {
        $date = HistoricDates::normalizeDateString($date);

        $date_parsed = self::EMPTY_DATE_ARRAY;

        //Unsicher
        $date_parsed['uncertain'] = strpos($date, '?') !== false;
        $date = str_replace('?', '', $date);

        // Oder oder Von-bis-Angabe
        $date_parsed['or'] = strpos($date, 'oder') !== false;
        $date = str_replace('oder', '–', $date);

        //Mehrere Angaben trennen
        $parts = explode('–', $date);
        $parts = ($parts === false) ? [] : $parts;
        $von = (count($parts) > 0) ? $parts[0] : '';
        $bis = (count($parts) > 1) ? $parts[1] : '';

        //Berechnen
        $date_parsed['begin'] = HistoricDates::parseHistoricDate($von);
        $date_parsed['end'] = HistoricDates::parseHistoricDate($bis);

        if ($date_parsed['begin']['invalid']) {
            $date_parsed['invalid'] = true;
        }

        // Disambiguate
        $date_parsed = HistoricDates::disambiguateDate($date_parsed);

        return $date_parsed;
    }

    /**
     * Parsed eine natürlichsprachliche Datierung
     *
     * @param string $date Datierung
     * @return array Geparste Datierung
     */
    static function parseHistoricDate(string $date): array
    {
        $dat = HistoricDates::EMPTY_DAT;

        //Zeittyp: früher, später
        if (strpos($date, 'früher') !== false) {
            $dat['modifier'] = self::MODIFIER_EARLIER;
        }
        elseif (strpos($date, 'später') !== false) {
            $dat['modifier'] = self::MODIFIER_LATER;
        }

        // Before or after christ
        $dat['negative'] = strpos($date, 'bc') !== false;

        //Zeitreferenz: Jahreszahl oder Jahrhundert
        if (strpos($date, 'jh') !== false) {
            $dat['unit'] = self::UNIT_CENTURY;
        }
        else {
            $dat['unit'] = self::UNIT_YEAR;
        }

        //Letzte Zahl ansteuern
        $number = HistoricDates::stripLastNumber($date);

        //Zeitreferenz: Jahrhundert ist durch einen Punkt gekennzeichnet
        if (str_starts_with($number['postfix'], '.')) {
            $dat['unit'] = self::UNIT_CENTURY;
        }

        $dat['number'] = $number['number'];
        $date = $number['prefix'];

        //Zeittyp: anfang, ende, um, mitte, nach, vor
        foreach (self::TOKEN_MODIFIER as $token => $modifier) {
            if (strpos($date, $token) !== false) {
                $dat['modifier'] = $modifier;
                break;
            }
        }

        //Zeittyp: teil
        if (strpos($date, 'hälfte') !== false) {
            $dat['modifier'] = self::MODIFIER_PART;
            $dat['fraction']['denominator'] = 2;
        }
        elseif (strpos($date, 'drittel') !== false) {
            $dat['modifier'] = self::MODIFIER_PART;
            $dat['fraction']['denominator'] = 3;
        }
        elseif (strpos($date, 'viertel') !== false) {
            $dat['modifier'] = self::MODIFIER_PART;
            $dat['fraction']['denominator'] = 4;
        }
        elseif (strpos($date, 'jahrzehnt') !== false) {
            $dat['modifier'] = self::MODIFIER_PART;
            $dat['fraction']['denominator'] = 10;
        }

        // Bei Jahrhundertteilen: Zähler extrahieren
        if ($dat['modifier'] == self::MODIFIER_PART) {
            $number = HistoricDates::stripLastNumber($date);
            $dat['fraction']['numerator_from'] = $number['number'];
        }

        // Bei Tagesangaben: Monat und Tag extrahieren
        else {
            if ($date !== '') {
                // Monat
                foreach (self::TOKEN_MONTHS as $token => $month) {
                    if (strpos($date, $token) !== false) {
                        $dat['day']['month'] = $month;
                        break;
                    }
                }

                if ($dat['day']['month'] === false) {
                    $number = HistoricDates::stripLastNumber($date);
                    $dat['day']['month'] = $number['number'];
                    $date = $number['prefix'];
                }

                // Tag
                $number = HistoricDates::stripLastNumber($date);
                $dat['day']['day'] = $number['number'];
            }
        }

        //Plausibilitätsprüfung
        if ($dat['number'] === false) {
            $dat['invalid'] = true;
        }

        if (($dat['unit'] === self::UNIT_YEAR) &&
            ($dat['modifier'] != '') &&
            ($dat['modifier'] != self::MODIFIER_AROUND) &&
            ($dat['modifier'] != self::MODIFIER_BEFORE) &&
            ($dat['modifier'] != self::MODIFIER_AFTER) &&
            ($dat['modifier'] != self::MODIFIER_EARLIER) &&
            ($dat['modifier'] != self::MODIFIER_LATER)
        ) {
            $dat['invalid'] = true;
        }

        if (($dat['unit'] == self::UNIT_CENTURY) &&
            ($dat['modifier'] != '') &&
            ($dat['modifier'] != self::MODIFIER_BEGIN) &&
            ($dat['modifier'] != self::MODIFIER_MID) &&
            ($dat['modifier'] != self::MODIFIER_END) &&
            ($dat['modifier'] != self::MODIFIER_PART)
        ) {
            $dat['invalid'] = true;
        }

        if (($dat['modifier'] == self::MODIFIER_PART) &&
            (($dat['fraction']['numerator_from'] > $dat['fraction']['denominator']) ||
                ($dat['fraction']['numerator_from'] == 0) ||
                ($dat['fraction']['denominator'] == 0) ||
                (!in_array($dat['fraction']['denominator'], [2, 3, 4, 10]))
            )) {
            $dat['invalid'] = true;
        }

        return $dat;
    }

    /**
     * Normalize a date string by replacing alternatives
     *
     * @param string $date A natural language date
     * @return string
     */
    static function normalizeDateString(string $date): string
    {
        $nDash = "\u{2013}"; // Halbgeviertstrich
        // Normalise
        $date = mb_strtolower($date);
        $date = str_replace(' ', '', $date);
        $date = str_replace('v. chr', 'bc', $date);
        $date = str_replace('v.chr', 'bc', $date);
        $date = str_replace('bc.', 'bc', $date);
        $date = str_replace('oderfrüher', 'früher', $date);
        $date = str_replace('o.früher', 'früher', $date);
        $date = str_replace('o.f.', 'früher', $date);
        $date = str_replace('oderf.', 'früher', $date);
        $date = str_replace('o.s.', 'später', $date);
        $date = str_replace('oders.', 'später', $date);
        $date = str_replace('o.später', 'später', $date);
        $date = str_replace('oderspäter', 'später', $date);
        $date = str_replace('bis', $nDash, $date);
        $date = str_replace('-', $nDash, $date); #151
        $date = str_replace('-', $nDash, $date); #150
        $date = str_replace('—', $nDash, $date);
        $date = str_replace('‒', $nDash, $date);
        $date = str_replace('o.', 'oder', $date);
        $date = str_replace('a.', 'anfang', $date);
        $date = str_replace('e.', 'ende', $date);
        $date = str_replace('m.', 'mitte', $date);
        $date = str_replace('.h.', '.hälfte', $date);
        $date = str_replace('.d.', '.drittel', $date);
        $date = str_replace('.v.', '.viertel', $date);
        $date = str_replace('.jz.', '.jahrzehnt', $date);

        return $date;
    }

    /**
     * Disambiguate by replacing alternatives
     *
     * Example for elliptical bis-Angabe:
     *  1415-16
     *
     * Examples for fractions:
     *  1.-2. D. 15. Jh.
     *  2.-3. D. 15. Jh.
     *  1.-3. V. 15. Jh.
     *  2.-3. V. 15. Jh.
     *  2.-4. V. 15. Jh.
     *
     * @param array $date_parsed A date array parsed in parseHistoricDateRange()
     * @return array A disambuiguated date array
     */
    static function disambiguateDate(array $date_parsed): array
    {

        // Elliptical years
        if (
            ($date_parsed['begin']['unit'] === self::UNIT_YEAR) &&
            ($date_parsed['end']['unit'] === self::UNIT_YEAR) &&
            (!$date_parsed['begin']['invalid']) &&
            (!$date_parsed['end']['invalid']) &&
            ($date_parsed['begin']['number'] > $date_parsed['end']['number'])
        ) {
            $begin_digits = strlen((string)$date_parsed['begin']['number']);
            $end_digits = strlen((string)$date_parsed['end']['number']);
            if ($begin_digits > $end_digits) {
                $end_number = substr((string)$date_parsed['begin']['number'], 0, $begin_digits - $end_digits);
                $end_number .= $date_parsed['end']['number'];
                $date_parsed['end']['number'] = intval($end_number);
                $date_parsed['end']['short'] = true;
            }
        }

        // Elliptical fractions
        // (1.-2.D.15.Jh.)
        if (
            ($date_parsed['begin']['unit'] === self::UNIT_CENTURY) && (!$date_parsed['begin']['invalid']) &&
            ($date_parsed['end']['unit'] === self::UNIT_CENTURY) && (!$date_parsed['end']['invalid']) &&
            (!empty($date_parsed['end']['fraction']['numerator_from']) && !empty($date_parsed['end']['fraction']['denominator'])) &&
            ($date_parsed['begin']['number'] < $date_parsed['end']['fraction']['numerator_from'])
        ) {

            $numerator_from = $date_parsed['begin']['number'];
            $numerator_to = $date_parsed['end']['fraction']['numerator_from'];

            $date_parsed['begin'] = $date_parsed['end'];
            $date_parsed['end']['invalid'] = true;

            $date_parsed['begin']['fraction']['numerator_from'] = $numerator_from;
            $date_parsed['begin']['fraction']['numerator_to'] = $numerator_to;
        }

        return $date_parsed;
    }

    /**
     * Extract the last number from string
     *
     * Returns an array with the following keys
     * - prefix: Text before the number
     * - number: The number as integer or false
     * - postfix: Text after the number
     *
     * Example result for '12.':
     * ['number' => 12, 'postfix' => '.', 'prefix' => '']
     *
     *  Example result for '1. Drittel 12. Jh.':
     *  ['number' => 12, 'postfix' => '. Jh.', 'prefix' => '1. Drittel ']
     *
     * @param string $date A string
     * @return array An array with the parsed number, prefix and postfix
     */
    static function stripLastNumber($date): array
    {

        // An die letzte Zahl rantasten
        $postfix = '';
        $j = strlen($date) - 1;
        while (($j >= 0) and (!in_array($date[$j], self::TOKEN_DIGITS))) {
            $postfix = $date[$j] . $postfix;
            $j--;
        }

        //Letzte Zahl extrahieren
        $number = '';
        while (($j >= 0) and (in_array($date[$j], self::TOKEN_DIGITS))) {
            $number = $date[$j] . $number;
            $j--;
        }
        if ($number !== '') {
            $number = intval($number);
        }
        else {
            $number = false;
        }

        // Der ganze Rest vor der Zahl
        $prefix = substr($date, 0, $j + 1);

        return ['prefix' => $prefix, 'number' => $number, 'postfix' => $postfix];
    }

    /**
     * TODO: add description
     *
     * @param array $date
     * @param bool $short
     * @return string
     */
    static function dateToString(array $date, bool $short = false): string
    {
        $out = '';

        if ($date['invalid']) {
            return $out;
        }

        if ($date['isiso']) {
            if ($date['negative']) {
                $out .= "-";
            }
            $out .= sprintf('%04d-%02d-%02d', $date['number'], $date['day']['month'], $date['day']['day']);
            $out .= $date['hour']['hour'] ?  'T' . str_pad($date['hour']['hour'], '0', STR_PAD_LEFT)  : '';
            $out .= $date['hour']['minute'] ?  ':' . str_pad($date['hour']['minute'], '0', STR_PAD_LEFT)  : '';
            $out .= $date['hour']['second'] ?  ':' . str_pad($date['hour']['second'], '0', STR_PAD_LEFT)  : '';
            return $out;
        }

        $space = ' '; // add space after abbreviation point?

        //Präfix
        switch ($date['modifier']) {
            case self::MODIFIER_BEGIN:
                $out .= 'A.' . $space;
                break;
            case self::MODIFIER_MID:
                $out .= 'M.' . $space;
                break;
            case self::MODIFIER_END:
                $out .= 'E.' . $space;
                break;
            case self::MODIFIER_AROUND:
                $out .= 'um ';
                break;
            case self::MODIFIER_BEFORE:
                $out .= 'vor ';
                break;
            case self::MODIFIER_AFTER:
                $out .= 'nach ';
                break;
            default:
                if ($date['modifier'] == self::MODIFIER_PART) {
                    $out .= $date['fraction']['numerator_from'] . '.';

                    if ($date['fraction']['numerator_to'] !== false) {
                        $out .= '–' . $date['fraction']['numerator_to'] . '.';
                    }

                    if ($date['fraction']['denominator'] == 2) {
                        $out .= ' H.' . $space;
                    }
                    elseif ($date['fraction']['denominator'] == 3) {
                        $out .= ' D.' . $space;
                    }
                    elseif ($date['fraction']['denominator'] == 4) {
                        $out .= ' V.' . $space;
                    }
                    elseif ($date['fraction']['denominator'] == 10) {
                        $out .= ' Jz.' . $space;
                    }
                }
        }

        //Zahl
        if ($date['day']['month'] !== false) {
            $out .= str_pad($date['day']['month'], 2, '0', STR_PAD_LEFT) . '-';
            if ($date['day']['day'] !== false) {
                $out = str_pad($date['day']['day'], 2, '0', STR_PAD_LEFT) . '-' . $out;
            }
        }
        $out .= $date['number'];

        //Jahrhundert
        if (($date['unit'] === self::UNIT_CENTURY) and (!$short)) {
            $out .= '. Jh.';
        }
        elseif ($date['unit'] === self::UNIT_CENTURY) {
            $out .= '.';
        }

        // Before or after christ
        if ($date['negative']) {
            $out .= ' v. Chr.';
        }

        //Früher oder später
        if ($date['modifier'] === self::MODIFIER_EARLIER) {
            $out .= ' o. früher';
        }

        if ($date['modifier'] === self::MODIFIER_LATER) {
            $out .= ' o. später';
        }

        return $out;
    }

    /**
     * Serialize a date range to a normalized string
     *
     * @param array $date
     * @return string|null
     */
    static function rangeToString(array $date): string|null
    {
        $out = '';

        //Invalid
        if ($date['invalid']) {
            return null;
        }

        //Von
        $short = (!$date['end']['invalid']) && ($date['end']['unit'] === self::UNIT_CENTURY);
        $out .= HistoricDates::dateToString($date['begin'], $short);

        //Verbindung (bis / oder)
        if (!$date['end']['invalid']) {
            if ($date['or']) {
                $out .= ' oder ';
            }
            else {
                $separator = $date['begin']['isiso'] ? '/' : '–'; //U+2013
                $out .= $separator;
            }
        }

        //Bis
        $out .= HistoricDates::dateToString($date['end']);

        //Unsicher
        $out = trim($out);
        if ($date['uncertain']) {
            $out .= '?';
        }

        return $out;
    }

    /**
     * Serialize a date list to normalized string
     *
     * @param array $datelist
     * @return string|null
     */
    static function listToString(array $datelist): string|null
    {
        $datelist = array_filter(array_map([__CLASS__, 'rangeToString'], $datelist), fn($x) => $x !== null);
        if (empty($datelist)) {
            return null;
        } else if (count($datelist) < 3) {
            return implode(' und ', $datelist);
        } else {
            return implode(', ', $datelist);
        }
    }

    /**
     * Compute a list of reference points for a single historic date
     *
     * @param array $date A parsed date term
     * @return array of DateTime objects
     */
    static function dateToRefPoints(array $date):array
    {
        $deltaDate = []; // deltas of the reference points with respect to the base year
        $sign = $date['negative'] ? -1 : +1;

        // compute reference points relative to $dateObj
        $keyElements = [];
        // === ISO date format
        if (in_array($date['unit'], [self::UNIT_DAY, self::UNIT_MINUTE, self::UNIT_SECOND])) {
            $deltaDate = self::DELTA_DATE['plain_day'];
            $baseYear = $sign * intval($date['number']);
        }
        elseif ($date['unit'] == self::UNIT_YEAR) {
            $type = $date['modifier'] == "" ? 'plain_year' : $date['modifier'];
            $deltaDate = self::DELTA_DATE[$type];
            $baseYear = $sign * intval($date['number']);
            // ISO has a year 0
            if ($baseYear < 0) {
                $baseYear += 1;
            }
        }
        elseif ($date['unit'] == self::UNIT_CENTURY) {
            // shift by -100 if date is positive
            $baseYear = $sign * intval($date['number']) * 100 - ($sign + 1) * 50;

            if ($date['modifier'] === self::MODIFIER_PART) {
                $frac = intdiv(100, intval($date['fraction']['denominator']));
                $numerator = intval($date['fraction']['numerator_from']);
                $adjustThird = 0;
                if ($date['fraction']['denominator'] == '3') {
                    $frac += 1;
                    // avoid accumulation because of extended interval
                    $adjustThird = 1 - $numerator;
                }
                $r2 = 1 + ($numerator - 1) * $frac + $adjustThird;
                $r3 = 1 + $numerator * $frac + $adjustThird;
                if ($date['fraction']['numerator_to']) {
                    $numerator = $date['fraction']['numerator_to'];
                    $adjustThird = $date['fraction']['denominator'] == '3' ? 1 - $numerator : 0;
                    $r3 = 1 + $numerator * $frac + $adjustThird;
                }
                $deltaDate = [
                    "r1" => '+' . strval($r3) . ' years -1 second',
                    "r2" => '+' . strval($r2) . ' years',
                    "r3" => '+' . strval($r3) . ' years -1 second'
                ];
            }
            elseif ($date['modifier'] != "") {
                $deltaDate = self::DELTA_DATE[$date['modifier']];
            }
            else {
                $deltaDate = self::DELTA_DATE['plain_century'];
            }
        }

        $refPoints = [];
        $dateObjMonth = $date['day']['month'] ? intval($date['day']['month']) : 1;
        $dateObjDay = $date['day']['day'] ? intval($date['day']['day']) : 1;
        foreach ($deltaDate as $delta) {
            $dateObj = new \DateTime();
            $dateObj->setDate($baseYear, $dateObjMonth, $dateObjDay);
            $dateObj->setTime(0, 0, 0);
            if ($delta != "") {
                $dateObj->modify($delta);
            }
            $refPoints[] = $dateObj;
        }

        return $refPoints;
    }

    /**
     * Create sort key components for a single historic date
     *
     * @param array $dateRefPoints date key reference points
     * @param string $modifier
     * @param bool $secondary Is this the end of a date range?
     * @return array of strings
     */
    static function dateToKey(array $dateRefPoints, string $modifier, bool $uncertain, bool $secondary = false): array
    {

        $keyElements = array_map(function ($r) {
            // shift date values for negative dates to ensure correct sorting
            $r->modify("+1 10000 years");
            return str_pad($r->format(self::DATE_TIME_FORMAT), 20, '0', STR_PAD_LEFT);
        },
            $dateRefPoints
        );

        $priority = 0x0000; // resolve the ambiguity for otherwise identical keys
        if ($modifier === self::MODIFIER_END) {
            $priority = $priority | self::PRIORITY_END;
        }
        if ($modifier === self::MODIFIER_EARLIER) {
            $priority = $priority | self::PRIORITY_EARLIER;
        }
        if ($modifier === self::MODIFIER_AROUND) {
            $priority = $priority | self::PRIORITY_AROUND;
        }
        if ($uncertain) {
            $priority = $priority | self::PRIORITY_UNCERTAIN;
        }

        $keyElements[] = str_pad($priority, 2, "0", STR_PAD_LEFT);
        $keyElements[] = self::DATE_VERSION;
        return $keyElements;
    }

    /**
     * Create sort key elements for a date
     *
     * @param array $date
     * @return array|null
     */
    static function rangeToKey(array $date): array|null
    {
        if ($date['invalid']) {
            return null;
        }

        $beginRefPoints = self::dateToRefPoints($date['begin']);
        $beginKeyElements = self::dateToKey($beginRefPoints, $date['begin']['modifier'], $date['uncertain']);
        $keyElements = $beginKeyElements;
        if (!$date['end']['invalid']) {
            $endRefPoints = self::dateToRefPoints($date['end']);
            $endKeyElements = self::dateToKey($endRefPoints, $date['end']['modifier'], $date['uncertain']);
            $keyElements[2] = $endKeyElements[2];
            if (!$date['or']) {
                $keyElements[0] = $endKeyElements[0]; // first reference point
                $keyElements[3] = $endKeyElements[3]; // priority
            }
            // use the earlier date for the key
            elseif ($endRefPoints[0] < $beginRefPoints[0]) {
                $keyElements[0] = $endKeyElements[0];
                $keyElements[1] = $endKeyElements[1];
                $keyElements[2] = $beginKeyElements[2];
                $keyElements[3] = $endKeyElements[3];
            }
        }
        else {
            $keyElements = $beginKeyElements;
        }
        return $keyElements;
    }

    /**
     * Get the first and the last year of the parsed date term
     *
     * @param array $date A parsed date term
     * @return array A list of two years, the beginning and the end
     */
    static function dateToSpan(array $date): array
    {

        $refPoints = self::dateToRefPoints($date);
        return array_filter([
                $refPoints[1]->format('Y'),
                $refPoints[2]->format('Y')
            ],
            fn($year) => $year !== null
        );

    }

    /**
     * Create a sort key from a list of parsed dates
     *
     * @param array $datelist The list of parsed dates
     * @return string|null
     */
    static function listToKey(array $datelist): string|null
    {
        $keys = array_values(array_filter(array_map([__CLASS__, 'rangeToKey'], $datelist), fn($x) => $x !== null));
        if (empty($keys)) {
            return null;
        }
        // the first date provides the first reference point
        $outKeys = $keys[0];
        if (count($keys) > 1) {
            $outKeys[1] = min(array_column($keys, 1));
            $outKeys[2] = max(array_column($keys, 2));
        }
        $out = implode('/', $outKeys);
        return $out;
    }


    /**
     * TODO: add description
     *
     * @param array $date
     * @return array
     */
    static function rangeToYears(array $date): array
    {
        $years = [];

        if (!$date['begin']['invalid']) {
            $years = array_merge($years, HistoricDates::dateToSpan($date['begin']));
        }
        if (!$date['end']['invalid']) {
            $years = array_merge($years, HistoricDates::dateToSpan($date['end']));
        }

        return $years;
    }

    /**
     * Return a list of years from a list of historic dates
     *
     * @param array $datelist
     * @return array
     */
    static function listToYears(array $datelist): array
    {
        $years = array_map([__CLASS__, 'rangeToYears'], $datelist);
        return array_merge(...$years);
    }


    /**
     *  Get the start and end year of a date string
     *
     * @param string $date A natural language date
     * @return array with keys start and end
     */
    static function years(string $date): array
    {
        $hd = HistoricDates::parseHistoricDateList($date);
        $years = HistoricDates::listToYears($hd);

        return $years;
    }

    /**
     * Start of the date range
     *
     * @param string $date The date string
     * @return int|null
 */
    static function minyear(string $date): int|null
    {
        $years = HistoricDates::years($date);
        return empty($years) ? null : min($years);
    }

    /**
     * End of the date range
     *
     * @param string $date The date string
     * @return int|null
     */
    static function maxyear(string $date): int|null
    {
        $years = HistoricDates::years($date);
        return empty($years) ? null : max($years);
    }
}

