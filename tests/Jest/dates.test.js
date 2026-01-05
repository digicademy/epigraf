/*
 * Tests for the Historic Dates Parser class
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import HistoricDates  from '../../plugins/Widgets/webroot/js/historicdates.js';
import testData from '../Testdata/HistoricDates/dates_v4.json';

test('Normalize string', () => {
    expect(HistoricDates.normalize('1.Jh.') ).toBe('1. Jh.');
});

describe('Parse dates', () => {

    test('correctly parses historical date strings', () => {
        const inputStrings = Object.keys(testData);
        const results = inputStrings.map((dateStr) => ({
            value: HistoricDates.normalize(dateStr),
            sort: HistoricDates.encode(dateStr),
            from: HistoricDates.minyear(dateStr),
            to: HistoricDates.maxyear(dateStr),
        }));

        const expected = Object.values(testData);
        expect(results).toEqual(expected);
    });
});
