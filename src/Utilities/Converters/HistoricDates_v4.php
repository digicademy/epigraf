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
 * Klasse zum Parsen von Datumsangaben.
 *
 * Wesentlich sind zwei statische Funktionen:
 *
 * - normalize prüft eine Datumsangabe und vereinheitlicht sie,
 * sodass sie in der Datenbank gespeichert werden kann.
 * Sie nimmt eine Textangabe entgegen (z.B. 1.H. 15.Jh.),
 * parsed sie und erzeugt dann wieder eine normalisierte Textangabe.
 *
 * - encode nimmt eine Textangabe entgegen und erzeugt einen Sortierschlüssel,
 * der in der Datenbank gespeichert werden kann.
 *
 * Start- und Endjahre von Zeiträumen können über die folgenden Funktionen extrahiert werden:
 * - years()
 * - minyear()
 * - maxyear()
 *
 * Die Textangaben sind wie folgt strukturiert und durchlaufen beim Parsen
 * die entsprechenden Funktionen:
 *
 * - Mehrere Datierungen sind mit " und " (alternativ durch "/" oder ",") getrennt (parseHistoricDateList)
 * - Eine einzelne Datierung kann mit "?" als unsicher gekennzeichnet sein (parseHistoricDateRange).
 * - Eine einzelne Datierung besteht aus einer oder zwei Teildatierungen (parseHistoricDateRange).
 * - Teildatierungen innerhalb einer einzelnen Datierung sind mit "-" (alternativ durch " bis ") oder " oder " getrennt (parseHistoricDateRange).
 *
 * Die Komponenten einer Teildatierung umfassen (parseHistoricDate):
 * - 'number' => false,   // Eine Zahl für das Jahr oder Jahrhundert [oder false]
 * - 'day' => false,      // Ein Datum in ISO-Schreibweise [oder false]
 * - 'fraction' => ['numerator_from' => false, 'numerator_to', 'denominator' => false] // Zahlen für die Anteile von Jahrhunderten [oder false]
 * - 'unit' => '',        // Werte: jahrhundert, jahreszahl, tag [oder ein leerer String]
 * - 'negative' => false, // true = before christ, false = after christ
 * - 'modifier' => '',    // Werte: um, vor, nach, oder früher, oder später, ende, anfang, mitte, teil [oder ein leerer String]
 * - 'short' => false,    // true = Kurzform beim Ende eines Zeitraums, z.B. 1922-23 [oder false]
 * - 'invalid' => false   // true = Eine ungültige Zeitangabe [oder false]
 *
 * Beim Parsen werden verschiedene Schreibweisen vereinheitlicht.
 *
 * # Datierungsschlüssel
 *
 * Mit dem Datierungsschlüssel soll eine Reihenfolge hergestellt werden, die folgende Kriterien abbildet:
 * - Genauere Datierungen vor ungenaueren Datierungen
 * - Ältere Datierungen vor jüngeren Datierungen
 *
 * Wenn beide Kriterien in Konflikt geraten, gilt das erste Kriterium. Beispiel:
 * - 3 v. Chr.
 * - 1200 v. Chr. - 1. Jh. v.Chr.
 * - 3 n. Chr.
 * - 800 v. Chr. - 1. Jh.
 *
 * Obwohl die mit einem Jahr ab 1200 v. Chr. bzw. ab 800 v. Chr. datierten Objekte potenziell älter
 * als die auf 3 v. Chr. bzw. 3 n. Chr. datierten Objekte sein können,
 * werden sie aufgrund der ungenaueren Datierung nachgeordnet.
 *
 * Beispiele für Datierungsschlüssel:
 *
 * 01433BBA08650BABA4 (M.14.–1.D.15. Jh.)
 * 01525BAA08480ADBA4 (um 1520–1525)
 * 01600BAD08400BDBA4 (2.H.16. Jh.–um 1600)
 * 01671AAA00000000A4 (1671)
 * 01800ABE00000000A4 (18. Jh.)
 * -1700ABE00000000A4 (17. Jh. v. Chr.)
 *
 * Aufbau des Schlüssels:
 *
 * - Mehrere Datierungen ("1433 und 1440") führen zu Teilschlüsseln,
 * die mit "X" verbunden sind (in den Beispielen nicht der Fall)
 * - Ein Teilschlüssel ist 18 Zeichen lang und setzt sich aus zwei jeweils achtstellingen
 * Jahresschlüsseln, einer einstelligen Unsicherheitskennung und einer einstelligen Versionsnummer zusammen.
 * - Enthält die Datierung einen Bereich, dann erfasst der erste Jahresschlüssel den jüngeren Teil des Bereichs,
 * das heißt die bis-Angabe. Auf diese weise werden potenziell ältere vor potenziell jüngeren
 * Objekten einsortiert (z. B. "1420-1425" vor "1420-1428").
 * Der zweite Jahresschlüssel erfasst dann den älteren Teil des Bereichs, das heißt die von-Angabe.
 * - Enthält die Datierung dagegen keinen Bereich, dann werden die Komponenten des
 * zweiten Jahresschlüssels alle auf 0 gesetzt.
 * - Nach beiden Jahresschlüsseln folgt eine Unsicherheitskennung:
 * A=Nicht unsicher, B=unsicher ("?").
 * - Am Ende wird die Versionsnummer des Datierungsschlüssels angehängt (aktuell 4).
 *
 * Der 18-stellige Schlüssel entspricht folgendem Muster (regex):
 * [0-][0-9]{4}[AB][AB][A-F][0-][0-9]{4}[AB][A-F][0AB][AB]4
 *
 * Aufbau der Jahresschlüssel:
 *
 * - Der erste Jahresschlüssel ist acht Zeichen lang und setzt sich aus der Jahreszahl,
 * einer Strukturziffer und einem Modifikator zusammen:
 * - Jahreszahl: fünfstellige Zahl (Ende des Datumsbereichs).
 * Angaben vor Christus beginnen in der ersten Stelle mit "-", Angaben nach Christus mit "0"
 * - Strukturziffer: A=einfache Datierung, B=zusammengesetzte Datierung bzw. Bereich.
 * Ist die Strukturziffer auf "A" gesetzt, dann sind alle Stellen des zweiten Jahresschlüssels auf "0" gesetzt.
 * - Modifikator: Erste Stelle A oder B, zweite Stelle A-F.
 * Kennzeichnet, ob es eine Zahl, ein Jahrhundert etc. ist.
 *
 * - Der zweite Jahresschlüssel ist acht Zeichen lang, setzt sich aus der umgepolten Jahresangabe,
 * einem Modifikator und einer Strukturziffer zusammen:
 * - Jahreszahl: Fünfstelliges Ende des Datumsbereichs. Die Zahl wird dabei immer umgepolt.
 * So wird zum Beispiel in der Angabe "1415-1420" im ersten Jahresschlüssel
 * das Jahr 1420 über die Zahl 1420 abgebildet.
 * Dagegen wird im zweiten Jahresschlüssel das Jahr 1415 über 10000 - 1415 = 8585 abgebildet.
 * Auf diese Weise werden potenziell genauere vor potenziell ungenaueren Datierungen einsortiert
 * (z.B. "1418-1420" vor "1415-1420").
 * Angaben vor Christus beginnen in der ersten Stelle mit "-", Angaben nach Christus mit "0".
 * - Modifikator: Erste Stelle A oder B, zweite Stelle A-F.
 * Kennzeichnet, ob es eine Zahl, ein Jahrhundert etc. ist
 * - Strukturziffer:
 * A = die beiden Datierungen sind Alternativen ("oder"),
 * B = die beiden Datierungen kennzeichnen einen Bereich ("bis", "-")
 */
class HistoricDates_v4
{

    /**
     * The years that are added to fuzzy borders
     *
     * @const int FUZZY_LONG Begin and end of centuries, after or before a specific date
     * @const int FUZZY_SHORT Circa values
     */
    const FUZZY_LONG = 25;
    const FUZZY_SHORT = 10;

    /**
     * Date constants
     */
    const UNIT_CENTURY = 'jahrhundert';
    const UNIT_YEAR = 'jahreszahl';

    const MODIFIER_EARLIER = 'frueher';
    const MODIFIER_LATER = 'spaeter';
    const MODIFIER_BEGIN = 'anfang';
    const MODIFIER_END = 'ende';
    const MODIFIER_MID = 'mitte';
    const MODIFIER_PART = 'teil';
    const MODIFIER_AROUND = 'um';
    const MODIFIER_AFTER = 'nach';
    const MODIFIER_BEFORE = 'vor';

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

    /**
     * @const int DATE_VERSION Version number of the date key
     */
    const DATE_VERSION = 4;

    /**
     *  Normalizes a natural language date
     *
     * Parses a date string and generated a new date string
     * from the parsed date
     *
     * @param string $date Natural language date, e.g. "15. Jh."
     * @return string Normalized natural language date
     */
    static function normalize(string $date): string
    {
        $hd = HistoricDates_v4::parseHistoricDateList($date);
        return HistoricDates_v4::listToString($hd);
    }


    /**
     *  Derives a sort key from a natural language date
     *
     * @param string $date A natural language date, e.g. "15. Jh."
     * @return string A key that can be used to sort multiple dates
     */
    static function encode(string $date): string
    {
        $hd = HistoricDates_v4::parseHistoricDateList($date);
        return HistoricDates_v4::listToKey($hd);
    }

    /**
     * Parse a natural language date
     *
     * Multiple dates can be combined with "und", "," or "/".
     *
     * @param string $date A natural language date
     * @return array An array of date arrays. A date array contains the components of the parsed dates.
     */
    static function parseHistoricDateList(string $date): array
    {
        $date = str_replace('und', ',', $date);
        $date = str_replace('/', ',', $date);
        $dates = explode(',', $date);
        $dates = array_map([__CLASS__, 'parseHistoricDateRange'], $dates);
        return $dates;
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
        $date = HistoricDates_v4::normalizeDateString($date);

        $date_parsed = [
            'invalid' => false,
            'uncertain' => false,
            'or' => false,
            'begin' => false,
            'end' => false
        ];

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
        $date_parsed['begin'] = HistoricDates_v4::parseHistoricDate($von);
        $date_parsed['end'] = HistoricDates_v4::parseHistoricDate($bis);

        if ($date_parsed['begin']['invalid']) {
            $date_parsed['invalid'] = true;
        }

        // Disambiguate
        $date_parsed = HistoricDates_v4::disambiguateDate($date_parsed);

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
        $dat = [
            'number' => false,    // {Zahl}
            'unit' => '', // {nn, jahrhundert, jahreszahl}
            'day' => ['month' => false, 'day' => false], // {Zahl}
            'fraction' => ['numerator_from' => false, 'numerator_to' => false, 'denominator' => false], //{Zahlen}
            'negative' => false, // Before or after christ
            'modifier' => '', //{nn, um, vor, nach, teil, ende, anfang, mitte, oder früher, oder später}
            'short' => false,
            'invalid' => false
        ];

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
        $number = HistoricDates_v4::stripLastNumber($date);

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

        // Bei Jahrhundertteilen: Zähler extrahieren
        if ($dat['modifier'] == self::MODIFIER_PART) {
            $number = HistoricDates_v4::stripLastNumber($date);
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
                    $number = HistoricDates_v4::stripLastNumber($date);
                    $dat['day']['month'] = $number['number'];
                    $date = $number['prefix'];
                }

                // Tag
                $number = HistoricDates_v4::stripLastNumber($date);
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
                ($dat['fraction']['denominator'] > 4)
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
        $date = str_replace('bis', '–', $date);
        $date = str_replace('-', '–', $date);#151
        $date = str_replace('-', '–', $date);#150
        $date = str_replace('—', '–', $date);
        $date = str_replace('o.', 'oder', $date);
        $date = str_replace('a.', 'anfang', $date);
        $date = str_replace('e.', 'ende', $date);
        $date = str_replace('m.', 'mitte', $date);
        $date = str_replace('.h.', '.hälfte', $date);
        $date = str_replace('.d.', '.drittel', $date);
        $date = str_replace('.v.', '.viertel', $date);

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

        //Präfix
        switch ($date['modifier']) {
            case self::MODIFIER_BEGIN:
                $out .= 'A.';
                break;
            case self::MODIFIER_MID:
                $out .= 'M.';
                break;
            case self::MODIFIER_END:
                $out .= 'E.';
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
                        $out .= '-' . $date['fraction']['numerator_to'] . '.';
                    }

                    if ($date['fraction']['denominator'] == 2) {
                        $out .= 'H.';
                    }
                    elseif ($date['fraction']['denominator'] == 3) {
                        $out .= 'D.';
                    }
                    elseif ($date['fraction']['denominator'] == 4) {
                        $out .= 'V.';
                    }
                }
        }

        //Zahl
        if ($date['day']['month'] !== false) {
            $out .= $date['day']['month'] . '.';
            if ($date['day']['day'] !== false) {
                $out = $date['day']['day'] . '.' . $out;
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
     * TODO: Add description
     *
     * @param array $date
     * @return string
     */
    static function rangeToString(array $date): string
    {
        $out = '';

        //Invalid
        if ($date['invalid']) {
            return '';
        }

        //Von
        $short = (!$date['end']['invalid']) && ($date['end']['unit'] === self::UNIT_CENTURY);
        $out .= HistoricDates_v4::dateToString($date['begin'], $short);

        //Verbindung (bis / oder)
        if (!$date['end']['invalid']) {
            if ($date['or']) {
                $out .= ' oder ';
            }
            else {
                $out .= '–';
            }
        }

        //Bis
        $out .= HistoricDates_v4::dateToString($date['end']);

        //Unsicher
        $out = trim($out);
        if ($date['uncertain']) {
            $out .= '?';
        }

        return $out;
    }

    /**
     * TODO: Add description
     *
     * @param array $datelist
     * @return string
     */
    static function listToString(array $datelist): string
    {
        $datelist = array_map([__CLASS__, 'rangeToString'], $datelist);
        return implode(' und ', $datelist);
    }

    /**
     * Create a sort key for a single historic date
     *
     * @param array $date A parsed date term
     * @param bool $secondary Is this the end of a date range?
     * @return array
     */
    static function dateToKey(array $date, bool $secondary = false): array
    {
        //Jahreszahlen
        if ($date['unit'] == self::UNIT_YEAR) {
            $zahl = $date['number'];
            if ($date['modifier'] == self::MODIFIER_BEFORE) {
                $zeittyp = 'AB';
            }
            elseif ($date['modifier'] === self::MODIFIER_EARLIER) {
                $zeittyp = 'AC';
            }
            elseif ($date['modifier'] == self::MODIFIER_AROUND) {
                $zeittyp = 'AD';
            }
            elseif ($date['modifier'] === self::MODIFIER_LATER) {
                $zeittyp = 'AE';
            }
            elseif ($date['modifier'] == self::MODIFIER_AFTER) {
                $zeittyp = 'AF';
            }
            else {
                $zeittyp = 'AA';
            }
        }

        //Jahrhundert(teil)e
        elseif ($date['unit'] === self::UNIT_CENTURY) {

            $zahl = (($date['number'] - 1) * 100) + 100;
            $zeittyp = 'BE';

            if ($date['modifier'] == self::MODIFIER_BEGIN) {
                $zahl = (($date['number'] - 1) * 100);
                $zahl = $secondary ? $zahl : $zahl + 1;
                $zeittyp = $secondary ? 'BA' : 'BF';

            }
            elseif ($date['modifier'] == self::MODIFIER_MID) {
                $zahl = (($date['number'] - 1) * 100) + 50;
                $zeittyp = $secondary ? 'BA' : 'BC';

            }
            elseif ($date['modifier'] == self::MODIFIER_END) {
                $zahl = (($date['number'] - 1) * 100) + 100;
                $zeittyp = $secondary ? 'BA' : 'BA';

            }
            elseif ($date['modifier'] == self::MODIFIER_PART) {

                // Viertel
                if ($date['fraction']['denominator'] === 4) {
                    if ($date['fraction']['numerator_from'] === 1) {
                        $zahl = (($date['number'] - 1) * 100) + 25;
                        $zeittyp = 'BA';
                    }
                    elseif ($date['fraction']['numerator_from'] === 2) {
                        $zahl = (($date['number'] - 1) * 100) + 50;
                        $zeittyp = $secondary ? 'BB' : 'BA';
                    }
                    elseif ($date['fraction']['numerator_from'] === 3) {
                        $zahl = (($date['number'] - 1) * 100) + 75;
                        $zeittyp = 'BA';
                    }
                    elseif ($date['fraction']['numerator_from'] === 4) {
                        $zahl = (($date['number'] - 1) * 100) + 100;
                        $zeittyp = 'BB';
                    }

                    // TODO: proof read, does this work?
                    if (!$secondary) {
                        if ($date['fraction']['numerator_to'] === 2) {
                            $zahl = (($date['number'] - 1) * 100) + 50;
                            $zeittyp = 'BA';
                        }
                        elseif ($date['fraction']['numerator_to'] === 3) {
                            $zahl = (($date['number'] - 1) * 100) + 75;
                            $zeittyp = 'BA';
                        }
                        elseif ($date['fraction']['numerator_from'] === 4) {
                            $zahl = (($date['number'] - 1) * 100) + 100;
                            $zeittyp = 'BB';
                        }
                    }
                }

                // Drittel
                elseif ($date['fraction']['denominator'] === 3) {
                    if ($date['fraction']['numerator_from'] === 1) {
                        $zahl = (($date['number'] - 1) * 100) + 33;
                        $zeittyp = 'BA';
                    }

                    elseif ($date['fraction']['numerator_from'] === 2) {
                        $zahl = (($date['number'] - 1) * 100) + 66;
                        $zeittyp = 'BA';
                    }

                    elseif ($date['fraction']['numerator_from'] === 3) {
                        $zahl = (($date['number'] - 1) * 100) + 100;
                        $zeittyp = 'BC';
                    }

                    if (!$secondary) {
                        if ($date['fraction']['numerator_to'] === 2) {
                            $zahl = (($date['number'] - 1) * 100) + 66;
                            $zeittyp = 'BA';
                        }
                        elseif ($date['fraction']['numerator_to'] === 3) {
                            $zahl = (($date['number'] - 1) * 100) + 100;
                            $zeittyp = 'BD';
                        }
                    }

                }

                // Hälften
                elseif ($date['fraction']['denominator'] === 2) {
                    if ($date['fraction']['numerator_from'] === 1) {
                        $zahl = (($date['number'] - 1) * 100) + 50;
                        $zeittyp = $secondary ? 'BC' : 'BB';
                    }
                    elseif ($date['fraction']['numerator_from'] === 2) {
                        $zahl = (($date['number'] - 1) * 100) + 100;
                        $zeittyp = 'BD';
                    }

                    if (!$secondary) {
                        if ($date['fraction']['numerator_to'] === 2) {
                            $zahl = (($date['number'] - 1) * 100) + 100;
                            $zeittyp = 'BD';
                        }
                    }
                }
            }

        }

        if ($secondary) {
            $zahl = 10000 - $zahl;
        }

        if (!$date['negative']) {
            $zahl = "0" . sprintf('%04d', $zahl);
        }
        else {
            $zahl = "-" . sprintf('%04d', $zahl);
        }

        return ['number' => $zahl, 'modifier' => $zeittyp];
    }

    /**
     * Create a sort key for a single parsed date
     *
     * @param array $date
     * @return string
     */
    static function rangeToKey(array $date): string
    {
        //Erstkriterium: Ende des Zeitraums bzw. einfache Datierung
        //Zweitkriterium: Anfang des Zeitraums oder leer
        if ($date['end']['invalid']) {
            $key1 = HistoricDates_v4::dateToKey($date['begin']);
            $key2 = ['number' => '00000', 'modifier' => '00'];

            // Century fraction ranges (e.g. 2.-3. V. 15. Jh.)
            if (($date['begin']['unit'] === self::UNIT_CENTURY) && ($date['begin']['fraction']['numerator_to'] !== false)) {
                $key2 = HistoricDates_v4::dateToKey($date['begin'], true);
            }

        }
        else {
            $key1 = HistoricDates_v4::dateToKey($date['end']);
            $key2 = HistoricDates_v4::dateToKey($date['begin']);

            // Swap if necessary
            if ((intval($key1['number'])) < (intval($key2['number']))) {
                $key1 = HistoricDates_v4::dateToKey($date['begin']);
                $key2 = HistoricDates_v4::dateToKey($date['end'], true);
            }
            else {
                $key2 = HistoricDates_v4::dateToKey($date['begin'], true);
            }
        }

        //Modifikator: unsicher
        if ($date['uncertain']) {
            $unsicher = 'B';
        }
        else {
            $unsicher = 'A';
        }

        //Modifikator: einfach
        if ($date['end']['invalid'] || $date['or']) {
            $einfach = 'A';
        }
        else {
            $einfach = 'B';
        }

        //Modifikator: von-bis, oder
        if ($date['end']['invalid']) {
            $oder = '0';
        }
        elseif ($date['or']) {
            $oder = 'A';
        }
        else {
            $oder = 'B';
        }

        //Zusammensetzen
        $key = $key1['number'] . $einfach . $key1['modifier']
            . $key2['number'] . $key2['modifier']
            . $oder . $unsicher . self::DATE_VERSION;

        return $key;
    }

    /**
     * Get the first and the last year of the parsed date term
     *
     * @param array $date A parsed date term
     * @return array A list of two years, the beginning and the end
     */
    static function dateToSpan(array $date): array
    {
        $start = null;
        $end = null;

        //Jahreszahlen
        if ($date['unit'] === self::UNIT_YEAR) {
            $start = $end = $date['number'];
            if ($date['modifier'] == self::MODIFIER_BEFORE) {
                $start -= HistoricDates_v4::FUZZY_LONG;
            }
            elseif ($date['modifier'] == self::MODIFIER_EARLIER) {
                $start -= self::FUZZY_LONG;
            }
            elseif ($date['modifier'] == self::MODIFIER_AROUND) {
                $start -= self::FUZZY_SHORT;
                $end += self::FUZZY_SHORT;
            }
            elseif ($date['modifier'] == self::MODIFIER_EARLIER) {
                $end += self::FUZZY_LONG;
            }
            elseif ($date['modifier'] == self::MODIFIER_AFTER) {
                $end += self::FUZZY_LONG;
            }
        }

        //Jahrhundert(teil)e
        elseif ($date['unit'] === self::UNIT_CENTURY) {

            $start = (($date['number'] - 1) * 100) + 1;
            $end = (($date['number'] - 1) * 100) + 100;

            if ($date['modifier'] == self::MODIFIER_BEGIN) {
                $start = (($date['number'] - 1) * 100) + 1;
                $end = (($date['number'] - 1) * 100) + self::FUZZY_LONG;
            }
            elseif ($date['modifier'] == self::MODIFIER_MID) {
                $start = (($date['number'] - 1) * 100) + 50 - self::FUZZY_LONG;
                $end = (($date['number'] - 1) * 100) + 50 + self::FUZZY_LONG;
            }
            elseif ($date['modifier'] == self::MODIFIER_END) {
                $start = (($date['number'] - 1) * 100) + 100 - self::FUZZY_LONG;
                $end = (($date['number'] - 1) * 100) + 100;
            }
            elseif ($date['modifier'] == self::MODIFIER_PART) {

                // Handle numerator_from
                // - Viertel
                if ($date['fraction']['denominator'] == 4) {

                    if ($date['fraction']['numerator_from'] === 1) {
                        $start = (($date['number'] - 1) * 100) + 1;
                        $end = (($date['number'] - 1) * 100) + 25;
                    }
                    elseif ($date['fraction']['numerator_from'] === 2) {
                        $start = (($date['number'] - 1) * 100) + 25;
                        $end = (($date['number'] - 1) * 100) + 50;
                    }
                    elseif ($date['fraction']['numerator_from'] === 3) {
                        $start = (($date['number'] - 1) * 100) + 50;
                        $end = (($date['number'] - 1) * 100) + 75;
                    }
                    elseif ($date['fraction']['numerator_from'] === 4) {
                        $start = (($date['number'] - 1) * 100) + 75;
                        $end = (($date['number'] - 1) * 100) + 100;
                    }

                    if ($date['fraction']['numerator_to'] === 2) {
                        $end = (($date['number'] - 1) * 100) + 50;
                    }
                    elseif ($date['fraction']['numerator_to'] === 3) {
                        $end = (($date['number'] - 1) * 100) + 75;
                    }
                    elseif ($date['fraction']['numerator_to'] === 4) {
                        $end = (($date['number'] - 1) * 100) + 100;
                    }

                }
                // -Drittel
                elseif ($date['fraction']['denominator'] == 3) {
                    if ($date['fraction']['numerator_from'] === 1) {
                        $start = (($date['number'] - 1) * 100) + 1;
                        $end = (($date['number'] - 1) * 100) + 33;
                    }
                    elseif
                    ($date['fraction']['numerator_from'] === 2) {
                        $start = (($date['number'] - 1) * 100) + 33;
                        $end = (($date['number'] - 1) * 100) + 66;
                    }
                    elseif
                    ($date['fraction']['numerator_from'] === 3) {
                        $start = (($date['number'] - 1) * 100) + 66;
                        $end = (($date['number'] - 1) * 100) + 100;
                    }

                    if ($date['fraction']['numerator_to'] === 2) {
                        $end = (($date['number'] - 1) * 100) + 66;
                    }
                    elseif ($date['fraction']['numerator_to'] === 3) {
                        $end = (($date['number'] - 1) * 100) + 100;
                    }

                }

                // - Hälften
                elseif ($date['fraction']['denominator'] === 2) {
                    if ($date['fraction']['numerator_from'] === 1) {
                        $start = (($date['number'] - 1) * 100) + 1;
                        $end = (($date['number'] - 1) * 100) + 50;
                    }

                    elseif ($date['fraction']['numerator_from'] === 2) {
                        $start = (($date['number'] - 1) * 100) + 50;
                        $end = (($date['number'] - 1) * 100) + 100;
                    }

                    if ($date['fraction']['numerator_to'] === 2) {
                        $end = (($date['number'] - 1) * 100) + 100;
                    }

                }
            }
        }

        if ($date['negative']) {
            $start = is_null($start) ? null : -$start;
            $end = is_null($end) ? null : -$end;
        }

        return array_filter([$start, $end], fn($year) => $year !== null);
    }

    /**
     * Create a sort key from a list of parsed dates
     *
     * @param array $datelist The list of parsed dates
     * @return string
     */
    static function listToKey(array $datelist): string
    {
        $datelist = array_map([__CLASS__, 'rangeToKey'], $datelist);
        $out = implode('X', $datelist);
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

        $years = array_merge($years, HistoricDates_v4::dateToSpan($date['begin']));
        if (!$date['end']['invalid']) {
            $years = array_merge($years, HistoricDates_v4::dateToSpan($date['end']));
        }

        return $years;
    }

    /**
     * TODO: add description
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
        $hd = HistoricDates_v4::parseHistoricDateList($date);
        $years = HistoricDates_v4::listToYears($hd);

        return $years;
    }

    /**
     * Start of the date range
     *
     * @param string $date The date string
     * @return int
     */
    static function minyear(string $date): int
    {
        $years = HistoricDates_v4::years($date);
        return min($years);
    }

    /**
     * End of the date range
     *
     * @param string $date The date string
     * @return int
     */
    static function maxyear(string $date): int
    {
        $years = HistoricDates_v4::years($date);
        return max($years);
    }
}

