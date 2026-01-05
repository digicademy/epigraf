/*
 * Historic Dates Parser - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

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
 */
export default class HistoricDates {
    // Fuzzy years (uncertain borders)
    static FUZZY_LONG = 25;
    static FUZZY_SHORT = 10;

    // Units/modifiers/tokens
    static UNIT_CENTURY = 'jahrhundert';
    static UNIT_YEAR = 'jahreszahl';

    static MODIFIER_EARLIER = 'frueher';
    static MODIFIER_LATER = 'spaeter';
    static MODIFIER_BEGIN = 'anfang';
    static MODIFIER_END = 'ende';
    static MODIFIER_MID = 'mitte';
    static MODIFIER_PART = 'teil';
    static MODIFIER_AROUND = 'um';
    static MODIFIER_AFTER = 'nach';
    static MODIFIER_BEFORE = 'vor';

    static TOKEN_MODIFIER = {
        'anfang': HistoricDates.MODIFIER_BEGIN,
        'ende': HistoricDates.MODIFIER_END,
        'um': HistoricDates.MODIFIER_AROUND,
        'mitte': HistoricDates.MODIFIER_MID,
        'nach': HistoricDates.MODIFIER_AFTER,
        'vor': HistoricDates.MODIFIER_BEFORE
    };

    static TOKEN_DIGITS = ['0','1','2','3','4','5','6','7','8','9'];

    static TOKEN_MONTHS = {
        'jan': 1, 'feb': 2, 'mär': 3, 'apr': 4, 'mai': 5, 'jun': 6,
        'jul': 7, 'aug': 8, 'sep': 9, 'okt':10, 'nov':11, 'dez':12
    };

    static DATE_VERSION = 4;

    // === Normalize date string
    static normalize(date) {
        let hd = this.parseHistoricDateList(date);
        return this.listToString(hd);
    }

    // === Encode date to key
    static encode(date) {
        let hd = this.parseHistoricDateList(date);
        return this.listToKey(hd);
    }

    // === Parse a date list (split, map to ranges)
    static parseHistoricDateList(date) {
        date = date.replace(/und/g, ',').replace(/\//g, ',');
        let dates = date.split(',').map(d => this.parseHistoricDateRange(d));
        return dates;
    }

    // === Parse a date range (handles dash, 'oder', '?')
    static parseHistoricDateRange(date) {
        date = this.normalizeDateString(date);

        let dateParsed = {
            invalid: false, uncertain: false, or: false, begin: false, end: false
        };

        dateParsed.uncertain = date.includes('?');
        date = date.replace(/\?/g, '');
        dateParsed.or = date.includes('oder');
        date = date.replace(/oder/g, '–'); // ndash (U+2013)

        let parts = date.split('–');
        let von = parts.length > 0 ? parts[0] : '';
        let bis = parts.length > 1 ? parts[1] : '';

        dateParsed.begin = this.parseHistoricDate(von);
        dateParsed.end = this.parseHistoricDate(bis);

        if (dateParsed.begin.invalid) dateParsed.invalid = true;

        dateParsed = this.disambiguateDate(dateParsed);
        return dateParsed;
    }

    // === Parse 1 date, output object as per comment
    static parseHistoricDate(date) {
        let dat = {
            number: false,
            unit: '',
            day: {month: false, day: false},
            fraction: {numerator_from: false, numerator_to: false, denominator: false},
            negative: false,
            modifier: '',
            short: false,
            invalid: false
        };

        // Zeittyp
        if (date.includes('früher')) dat.modifier = this.MODIFIER_EARLIER;
        else if (date.includes('später')) dat.modifier = this.MODIFIER_LATER;

        // Before/after christ
        dat.negative = date.includes('bc');

        // Jahrhundert oder Jahreszahl?
        if (date.includes('jh')) dat.unit = this.UNIT_CENTURY;
        else dat.unit = this.UNIT_YEAR;

        // Number extraction
        let number = this.stripLastNumber(date);
        if (number.postfix.startsWith('.')) dat.unit = this.UNIT_CENTURY;
        dat.number = number.number;
        date = number.prefix;

        // Modifiers
        for (let token in this.TOKEN_MODIFIER) {
            if (date.includes(token)) {
                dat.modifier = this.TOKEN_MODIFIER[token];
                break;
            }
        }

        // Teil/Fraction
        if (date.includes('hälfte')) {
            dat.modifier = this.MODIFIER_PART;
            dat.fraction.denominator = 2;
        } else if (date.includes('drittel')) {
            dat.modifier = this.MODIFIER_PART;
            dat.fraction.denominator = 3;
        } else if (date.includes('viertel')) {
            dat.modifier = this.MODIFIER_PART;
            dat.fraction.denominator = 4;
        }

        // Fractions: extract numerator
        if (dat.modifier === this.MODIFIER_PART) {
            number = this.stripLastNumber(date);
            dat.fraction.numerator_from = number.number;
        } else if (date !== '') {
            // Monat
            for (let token in this.TOKEN_MONTHS) {
                if (date.includes(token)) {
                    dat.day.month = this.TOKEN_MONTHS[token];
                    break;
                }
            }
            if (dat.day.month === false) {
                number = this.stripLastNumber(date); dat.day.month = number.number; date = number.prefix;
            }
            // Tag
            number = this.stripLastNumber(date); dat.day.day = number.number;
        }

        // Plausibility
        if (dat.number === false) dat.invalid = true;

        if ((dat.unit === this.UNIT_YEAR)
            && dat.modifier !== ''
            && dat.modifier !== this.MODIFIER_AROUND
            && dat.modifier !== this.MODIFIER_BEFORE
            && dat.modifier !== this.MODIFIER_AFTER
            && dat.modifier !== this.MODIFIER_EARLIER
            && dat.modifier !== this.MODIFIER_LATER)
            dat.invalid = true;

        if ((dat.unit === this.UNIT_CENTURY)
            && dat.modifier !== ''
            && dat.modifier !== this.MODIFIER_BEGIN
            && dat.modifier !== this.MODIFIER_MID
            && dat.modifier !== this.MODIFIER_END
            && dat.modifier !== this.MODIFIER_PART)
            dat.invalid = true;

        if ((dat.modifier === this.MODIFIER_PART)
            && (
                dat.fraction.numerator_from > dat.fraction.denominator ||
                dat.fraction.numerator_from === 0 ||
                dat.fraction.denominator === 0 ||
                dat.fraction.denominator > 4
            )
        ) dat.invalid = true;

        return dat;
    }

    // === Normalize date string (fix tokens, lower etc)
    static normalizeDateString(date) {
        date = date.toLowerCase().replace(/\s+/g, '');

        date = date.replace(/v\.chr(\.|\b)/g, 'bc')
            .replace(/oderfrüher/g, 'früher')
            .replace(/o\.früher/g, 'früher')
            .replace(/o\.f\./g, 'früher')
            .replace(/oderf\./g, 'früher')
            .replace(/o\.s\./g, 'später')
            .replace(/oders\./g, 'später')
            .replace(/o\.später/g, 'später')
            .replace(/oderspäter/g, 'später')
            .replace(/bis/g, '–')
            .replace(/-/g, '–')
            .replace(/—/g, '–')
            .replace(/‒/g, '–')
            .replace(/o\./g, 'oder')
            .replace(/a\./g, 'anfang')
            .replace(/e\./g, 'ende')
            .replace(/m\./g, 'mitte')
            .replace(/\.h\./g, '.hälfte')
            .replace(/\.d\./g, '.drittel')
            .replace(/\.v\./g, '.viertel');
        return date;
    }

    // === Elliptical numbers/fractions disambiguation
    static disambiguateDate(dateParsed) {
        // Elliptical years
        if (
            dateParsed.begin.unit === this.UNIT_YEAR &&
            dateParsed.end.unit === this.UNIT_YEAR &&
            !dateParsed.begin.invalid &&
            !dateParsed.end.invalid &&
            dateParsed.begin.number > dateParsed.end.number
        ) {
            let beginDigits = String(dateParsed.begin.number).length;
            let endDigits = String(dateParsed.end.number).length;
            if (beginDigits > endDigits) {
                let endNumber = String(dateParsed.begin.number).substr(0, beginDigits - endDigits) +
                    String(dateParsed.end.number);
                dateParsed.end.number = parseInt(endNumber, 10);
                dateParsed.end.short = true;
            }
        }

        // Elliptical fractions
        if (
            dateParsed.begin.unit === this.UNIT_CENTURY && !dateParsed.begin.invalid &&
            dateParsed.end.unit === this.UNIT_CENTURY && !dateParsed.end.invalid &&
            dateParsed.end.fraction.numerator_from && dateParsed.end.fraction.denominator &&
            dateParsed.begin.number < dateParsed.end.fraction.numerator_from
        ) {
            const numerator_from = dateParsed.begin.number;
            const numerator_to = dateParsed.end.fraction.numerator_from;
            dateParsed.begin = {...dateParsed.end}; dateParsed.end.invalid = true;
            dateParsed.begin.fraction.numerator_from = numerator_from;
            dateParsed.begin.fraction.numerator_to = numerator_to;
        }
        return dateParsed;
    }

    // === Extract the last number from a string
    static stripLastNumber(date) {
        let postfix = '', j = date.length -1;
        // PHP in_array equivalent: just .includes()
        while (j >= 0 && !this.TOKEN_DIGITS.includes(date[j])) {
            postfix = date[j] + postfix; j--;
        }
        let number = '';
        while (j >= 0 && this.TOKEN_DIGITS.includes(date[j])) {
            number = date[j] + number; j--;
        }
        number = (number !== '') ? parseInt(number, 10) : false;
        let prefix = date.substring(0, j + 1);
        return {prefix, number, postfix};
    }

    // === Stringify date obj
    static dateToString(date, short = false) {
        let out = '';
        if (date.invalid) return out;
        switch (date.modifier) {
            case this.MODIFIER_BEGIN: out += 'A.'; break;
            case this.MODIFIER_MID: out += 'M.'; break;
            case this.MODIFIER_END: out += 'E.'; break;
            case this.MODIFIER_AROUND: out += 'um '; break;
            case this.MODIFIER_BEFORE: out += 'vor '; break;
            case this.MODIFIER_AFTER: out += 'nach '; break;
            default:
                if (date.modifier === this.MODIFIER_PART) {
                    out += date.fraction.numerator_from + '.';
                    if (date.fraction.numerator_to !== false)
                        out += '-' + date.fraction.numerator_to + '.';
                    if (date.fraction.denominator === 2) out += 'H.';
                    else if (date.fraction.denominator === 3) out += 'D.';
                    else if (date.fraction.denominator === 4) out += 'V.';
                }
        }
        // Zahl (month, day)
        if (date.day.month !== false) {
            out += date.day.month + '.';
            if (date.day.day !== false) {
                out = date.day.day + '.' + out;
            }
        }
        out += date.number;

        // Jahrhundert
        if (date.unit === this.UNIT_CENTURY && !short) out += '. Jh.';
        else if (date.unit === this.UNIT_CENTURY) out += '.';
        if (date.negative) out += ' v. Chr.';
        if (date.modifier === this.MODIFIER_EARLIER) out += ' o. früher';
        if (date.modifier === this.MODIFIER_LATER) out += ' o. später';
        return out;
    }

    // === Stringify range object
    static rangeToString(date) {
        if (date.invalid) return '';
        let short = (!date.end.invalid) && (date.end.unit === this.UNIT_CENTURY);
        let out = this.dateToString(date.begin, short);

        if (!date.end.invalid) out += (date.or ? ' oder ' : '–');
        out += this.dateToString(date.end);
        out = out.trim();
        if (date.uncertain) out += '?';
        return out;
    }

    // === Stringify list
    static listToString(datelist) {
        return datelist.map(d => this.rangeToString(d)).join(' und ');
    }

    // === Sort key calculation (as array, for date range)
    static dateToKey(date, secondary = false) {
        let zahl, zeittyp;
        if (date.unit == this.UNIT_YEAR) {
            zahl = date.number;
            if (date.modifier == this.MODIFIER_BEFORE) zeittyp = 'AB';
            else if (date.modifier === this.MODIFIER_EARLIER) zeittyp = 'AC';
            else if (date.modifier == this.MODIFIER_AROUND) zeittyp = 'AD';
            else if (date.modifier === this.MODIFIER_LATER) zeittyp = 'AE';
            else if (date.modifier == this.MODIFIER_AFTER) zeittyp = 'AF';
            else zeittyp = 'AA';
        } else if (date.unit === this.UNIT_CENTURY) {
            zahl = (date.number - 1) * 100 + 100;
            zeittyp = 'BE';
            if (date.modifier == this.MODIFIER_BEGIN) {
                zahl = (date.number - 1) * 100;
                zahl = secondary ? zahl : zahl + 1;
                zeittyp = secondary ? 'BA' : 'BF';
            } else if (date.modifier == this.MODIFIER_MID) {
                zahl = (date.number - 1) * 100 + 50;
                zeittyp = secondary ? 'BA' : 'BC';
            } else if (date.modifier == this.MODIFIER_END) {
                zahl = (date.number - 1) * 100 + 100;
                zeittyp = secondary ? 'BA' : 'BA';
            } else if (date.modifier == this.MODIFIER_PART) {
                // Viertel
                if (date.fraction.denominator === 4) {
                    if (date.fraction.numerator_from === 1) {
                        zahl = (date.number - 1) * 100 + 25; zeittyp = 'BA';
                    } else if (date.fraction.numerator_from === 2) {
                        zahl = (date.number - 1) * 100 + 50; zeittyp = secondary ? 'BB':'BA';
                    } else if (date.fraction.numerator_from === 3) {
                        zahl = (date.number - 1) * 100 + 75; zeittyp = 'BA';
                    } else if (date.fraction.numerator_from === 4) {
                        zahl = (date.number - 1) * 100 + 100; zeittyp = 'BB';
                    }

                    if (!secondary) {
                        if (date.fraction.numerator_to === 2) {
                            zahl = (date.number - 1) * 100 + 50; zeittyp = 'BA';
                        } else if (date.fraction.numerator_to === 3) {
                            zahl = (date.number - 1) * 100 + 75; zeittyp = 'BA';
                        } else if (date.fraction.numerator_to === 4) {
                            zahl = (date.number - 1) * 100 + 100; zeittyp = 'BB';
                        }
                    }
                }
                // Drittel
                else if (date.fraction.denominator === 3) {
                    if (date.fraction.numerator_from === 1) {
                        zahl = (date.number - 1) * 100 + 33; zeittyp = 'BA';
                    } else if (date.fraction.numerator_from === 2) {
                        zahl = (date.number - 1) * 100 + 66; zeittyp = 'BA';
                    } else if (date.fraction.numerator_from === 3) {
                        zahl = (date.number - 1) * 100 + 100; zeittyp = 'BC';
                    }
                    if (!secondary) {
                        if (date.fraction.numerator_to === 2) {
                            zahl = (date.number - 1) * 100 + 66; zeittyp = 'BA';
                        } else if (date.fraction.numerator_to === 3) {
                            zahl = (date.number - 1) * 100 + 100; zeittyp = 'BD';
                        }
                    }
                }
                // Hälften
                else if (date.fraction.denominator === 2) {
                    if (date.fraction.numerator_from === 1) {
                        zahl = (date.number - 1) * 100 + 50; zeittyp = secondary ? 'BC':'BB';
                    } else if (date.fraction.numerator_from === 2) {
                        zahl = (date.number - 1) * 100 + 100; zeittyp = 'BD';
                    }
                    if (!secondary) {
                        if (date.fraction.numerator_to === 2) {
                            zahl = (date.number - 1) * 100 + 100; zeittyp = 'BD';
                        }
                    }
                }
            }
        }

        if (secondary) zahl = 10000 - zahl;
        zahl = (date.negative ? "-" : "0") + String(zahl).padStart(4, '0');
        return {number: zahl, modifier: zeittyp};
    }

    // === Sort key for range
    static rangeToKey(date) {
        let key1, key2;
        if (date.end.invalid) {
            key1 = this.dateToKey(date.begin);
            key2 = {number:'00000', modifier:'00'};
            if ((date.begin.unit === this.UNIT_CENTURY) && (date.begin.fraction.numerator_to !== false)) {
                key2 = this.dateToKey(date.begin, true);
            }
        } else {
            key1 = this.dateToKey(date.end);
            key2 = this.dateToKey(date.begin);
            if (Number(key1.number) < Number(key2.number)) {
                key1 = this.dateToKey(date.begin);
                key2 = this.dateToKey(date.end, true);
            } else {
                key2 = this.dateToKey(date.begin, true);
            }
        }
        let unsicher = date.uncertain ? 'B' : 'A';
        let einfach = (date.end.invalid || date.or) ? 'A':'B';
        let oder = (date.end.invalid) ? '0' : (date.or ? 'A' : 'B');
        let key = key1.number + einfach + key1.modifier +
            key2.number + key2.modifier + oder + unsicher +
            this.DATE_VERSION;
        return key;
    }

    // === years: Get start & end year from a date
    static dateToSpan(date) {
        let start = null, end = null;
        if (date.unit === this.UNIT_YEAR) {
            start = end = date.number;
            if (date.modifier === this.MODIFIER_BEFORE) start -= this.FUZZY_LONG;
            else if (date.modifier === this.MODIFIER_EARLIER) start -= this.FUZZY_LONG;
            else if (date.modifier === this.MODIFIER_AROUND) {start -= this.FUZZY_SHORT; end += this.FUZZY_SHORT;}
            else if (date.modifier === this.MODIFIER_EARLIER) end += this.FUZZY_LONG;
            else if (date.modifier === this.MODIFIER_AFTER) end += this.FUZZY_LONG;
        } else if (date.unit === this.UNIT_CENTURY) {
            start = (date.number - 1) * 100 + 1;
            end = (date.number - 1) * 100 + 100;
            if (date.modifier === this.MODIFIER_BEGIN) {
                start = (date.number - 1) * 100 + 1;
                end = (date.number - 1) * 100 + this.FUZZY_LONG;
            } else if (date.modifier === this.MODIFIER_MID) {
                start = (date.number - 1) * 100 + 50 - this.FUZZY_LONG;
                end = (date.number - 1) * 100 + 50 + this.FUZZY_LONG;
            } else if (date.modifier === this.MODIFIER_END) {
                start = (date.number - 1) * 100 + 100 - this.FUZZY_LONG;
                end = (date.number - 1) * 100 + 100;
            } else if (date.modifier === this.MODIFIER_PART) {
                if (date.fraction.denominator === 4) {
                    if (date.fraction.numerator_from === 1) {
                        start = (date.number - 1) * 100 + 1; end = (date.number - 1) * 100 + 25;
                    } else if (date.fraction.numerator_from === 2) {
                        start = (date.number - 1) * 100 + 25; end = (date.number - 1) * 100 + 50;
                    } else if (date.fraction.numerator_from === 3) {
                        start = (date.number - 1) * 100 + 50; end = (date.number - 1) * 100 + 75;
                    } else if (date.fraction.numerator_from === 4) {
                        start = (date.number - 1) * 100 + 75; end = (date.number - 1) * 100 + 100;
                    }
                    if (date.fraction.numerator_to === 2) {
                        end = (date.number - 1) * 100 + 50;
                    } else if (date.fraction.numerator_to === 3) {
                        end = (date.number - 1) * 100 + 75;
                    } else if (date.fraction.numerator_to === 4) {
                        end = (date.number - 1) * 100 + 100;
                    }
                }
                else if (date.fraction.denominator === 3) {
                    if (date.fraction.numerator_from === 1) {
                        start = (date.number - 1) * 100 + 1; end = (date.number - 1) * 100 + 33;
                    } else if (date.fraction.numerator_from === 2) {
                        start = (date.number - 1) * 100 + 33; end = (date.number - 1) * 100 + 66;
                    } else if (date.fraction.numerator_from === 3) {
                        start = (date.number - 1) * 100 + 66; end = (date.number - 1) * 100 + 100;
                    }
                    if (date.fraction.numerator_to === 2) {
                        end = (date.number - 1) * 100 + 66;
                    } else if (date.fraction.numerator_to === 3) {
                        end = (date.number - 1) * 100 + 100;
                    }
                }
                else if (date.fraction.denominator === 2) {
                    if (date.fraction.numerator_from === 1) {
                        start = (date.number - 1) * 100 + 1; end = (date.number - 1) * 100 + 50;
                    } else if (date.fraction.numerator_from === 2) {
                        start = (date.number - 1) * 100 + 50; end = (date.number - 1) * 100 + 100;
                    }
                    if (date.fraction.numerator_to === 2) {
                        end = (date.number - 1) * 100 + 100;
                    }
                }
            }
        }
        if (date.negative) {
            start = start == null ? null : -start;
            end = end == null ? null : -end;
        }
        // Only actual years
        return [start, end].filter(y => y !== null);
    }

    // === listToKey: sort-keys for full list
    static listToKey(datelist) {
        return datelist.map(d => this.rangeToKey(d)).join('X');
    }

    // === Range to years
    static rangeToYears(date) {
        let years = [];
        years = years.concat(this.dateToSpan(date.begin));
        if (!date.end.invalid) years = years.concat(this.dateToSpan(date.end));
        return years;
    }
    static listToYears(datelist) {
        let years = datelist.map(d => this.rangeToYears(d));
        return [].concat(...years);
    }

    // === API for years({date}), minyear, maxyear
    static years(date) {
        let hd = this.parseHistoricDateList(date);
        return this.listToYears(hd);
    }
    static minyear(date) { return Math.min(...this.years(date)); }
    static maxyear(date) { return Math.max(...this.years(date)); }
}

