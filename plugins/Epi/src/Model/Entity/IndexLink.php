<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

declare(strict_types=1);

namespace Epi\Model\Entity;

use Cake\ORM\Entity;

/**
 * Link Entity for index generation
 */
class IndexLink extends Link
{

    /**
     * Default tag name used in the XmlView
     *
     * @var string
     */
    public $_xml_tag = 'link';

    /**
     * Default table name
     *
     * @var string
     */
    public $_tablename = 'links';

    /**
     * Snippets for export
     * Each snippet contains a list of fields that is added to $_serialize_fields.
     *
     * @var array[]
     */
    protected $_serialize_snippets = [
        'index' => ['from_tab', 'to_tab', 'root_tab'],
    ];

    /**
     * Constructor
     *
     * @param Entity|array $link
     * @param array $options
     *
     */
    public function __construct($link = null, array $options = [])
    {
        $property = null;
        if (!empty($link['property'])) {
            $property = new IndexProperty($link['property']);
            $property->prepareRoot($this, $property, true, true);
        }

        // Convert to array
        if (!is_array($link)) {
            $link = $link->toArray();
        }
        unset($link['type']);
        $link['property'] = $property;

        // TODO: Allow links to sections, articles, footnotes in the index
        unset($link['section']);
        unset($link['article']);
        unset($link['footnote']);

        $data = $link;

        $options['source'] = 'Epi.Links';
        parent::__construct($data, $options);
    }
}
