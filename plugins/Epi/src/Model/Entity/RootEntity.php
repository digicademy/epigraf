<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Entity;

use App\Utilities\Converters\Arrays;
use Cake\Utility\Hash;

/**
 * RootEntity is the parent class of articles and properties.
 * It provides functions to access the links and footnotes.
 *
 * # Virtual fields
 * @property array $linksByTagid
 * @property array $footnotesByTagid
 * @property array $footnotesByType
 * @property array $footnoteTypes
 * @property array $missingXmlTags
 * @property bool $hasDependencies
 */
class RootEntity extends BaseEntity
{
    /**
     * Index of links and footnotes, cached for performance reasons
     * // TODO: merge with $indexes of BaseEntity: done?
     *
     * @var array
     */
    public $_lookup = [];

    /**
     * Whether to filter out annotations for data exports that are in invisible fields.
     * Set to true for filtered data.
     *
     * @var bool
     */
    public $_filterAnnos = false;

    /**
     * Get links by tag ID
     *
     * @return array
     */
    protected function _getLinksByTagid()
    {
        if (empty($this->_lookup['links']['from_tagid'])) {
            $this->_lookup['links']['from_tagid'] = collection($this->links ?? [])
                ->groupBy('from_tagid')
                ->toArray();
        }
        return $this->_lookup['links']['from_tagid'];
    }

    /**
     * Get footnotes by tag ID
     *
     * @return array
     */
    protected function _getFootnotesByTagid()
    {
        if (empty($this->_lookup['footnotes']['from_tagid'])) {
            $this->_lookup['footnotes']['from_tagid'] = collection($this->footnotes ?? [])
                ->groupBy('from_tagid')
                ->toArray();
        }
        return $this->_lookup['footnotes']['from_tagid'];
    }

    /**
     * Get footnotes by type
     *
     * //TODO: cache
     *
     * @return array
     */
    protected function _getFootnotesByType()
    {
        return collection($this->footnotes)->groupBy('from_tagname')->toArray();
    }

    /**
     * Get the configuration of footnotes that are allowed in the entity (the article)
     *
     * @return array
     */
    protected function _getFootnoteTypes()
    {
        // From config
        $footnoteTypesConfig = $this->table->getDatabase()->types['footnotes'] ?? [];
        $footnoteTypesConfig = array_intersect_key($footnoteTypesConfig, array_flip($this->type['merged']['footnotes'] ?? []));

        // Additional from data
        $footnoteTypes = array_map(fn($x) => [], $this->footnotes_by_type);
        $footnoteTypes = array_diff_key($footnoteTypes, $footnoteTypesConfig);

        // Merge
        return array_merge($footnoteTypesConfig, $footnoteTypes);
    }

    /**
     * Get warnings of the root entity and its links and footnotes
     *
     * Important: _getTagErrors() must be evaluated beforehand to collect the tags for the links and footnotes.
     *
     * @return array
     */
    protected function _getWarnings()
    {
        $this->prepareRoot();

        if (is_null($this->_warnings)) {

            $warnings = parent::_getWarnings() ?? [];

            // Note: This will check for any footnotes or links with missing tags.
            //       Thus, it is important to request footnote and link warnings
            //       *after* all tags were extracted, their warnings were evaluated and their corresponding
            //       links or footnotes were assigned. And link warnings need to be requested after footnote warnings,
            //       since footnotes may contain annotations.
            //       Because in an article's _getWarning() function the sections and everything else
            //       is evaluated before calling the parent's function, this should work as expected.

            $annoWarnings = [];

            // Footnote warnings
            foreach ($this->footnotes ?? [] as $footnote) {
                $annoWarnings = Arrays::array_merge_grouped($annoWarnings, $footnote->warnings);
            }

            // Link warnings
            foreach ($this->links ?? [] as $link) {
                $annoWarnings = Arrays::array_merge_grouped($annoWarnings, $link->warnings);
            }

            $this->_warnings = Arrays::array_merge_grouped($warnings, $annoWarnings);
        }
        return $this->_warnings;
    }

    /**
     * Get ordered footnotes by type
     *
     * @param $typeName
     * @return mixed
     */
    public function getOrderedFootnotes($typeName)
    {

        $footnotes = $this->footnotes_by_type[$typeName] ?? [];
        $along = $this->getCounter($typeName);
        $along = empty($along) ? $this->getCounter('tags') : $along;

        // Filter out unpublished footnotes for guests and readers
        if (in_array($this->currentUserRole, ['guest', 'reader']) || !empty(\App\Model\Table\BaseTable::$requestPublished)) {
            $footnotes = Arrays::orderAlong($footnotes, 'from_tagid', $along);
            $footnotes = array_filter($footnotes, fn($footnote) => $footnote->number !== -INF);
        }
        // Force order by sortno
        else {
            $along = empty($along) ? Hash::combine($footnotes, "{*}.from_tagid", "{*}.sortno") : $along;
            $footnotes = Arrays::orderAlong($footnotes, 'from_tagid', $along);
        }

        if ($this->_filterAnnos) {
            $footnotes = array_filter($footnotes, fn($footnote) => $footnote->getEntityIsVisible());
        }

        return $footnotes;
    }

    /**
     * Get all links of the root entity that
     * belong to the given table and id.
     *
     * @param string $table
     * @param int|string $id
     * @return array|mixed
     */
    public function getLinksFrom(string $table, $id)
    {
        // Index links by from_tab and id for performance reasons
        if (!isset($this->_lookup['links']['from_tab'])) {
            $this->_lookup['links']['from_tab'] = collection($this->links ?? [])
                ->filter(fn($link) => !is_null($link['from_tab'] ?? null))
                ->groupBy('from_tab')
                ->map(fn($table) => collection($table)
                    ->groupBy(fn($link) => $link['from_id'] ?? '')
                    ->toArray()
                )
                ->toArray();
        }

        // array of Link objects
        return $this->_lookup['links']['from_tab'][$table][$id] ?? [];
    }

    /**
     * Get all footnotes contained in the root entity that
     * belong to the given table and id.
     *
     * @param string $table
     * @param int|string $id
     * @return array|mixed
     */
    public function getFootnotesFrom(string $table, $id)
    {
        // Index footnotes by from_tab and id for performance reasons
        if (!isset($this->_lookup['footnotes']['from_tab'])) {
            $this->_lookup['footnotes']['from_tab'] = collection($this->footnotes ?? [])
                ->groupBy(fn($x) => $x['from_tab'] ?? '')
                ->map(fn($table) => collection($table)
                    ->groupBy(fn($x) => $x['from_id'] ?? '')
                    ->toArray()
                )
                ->toArray();
        }

        return $this->_lookup['footnotes']['from_tab'][$table][$id] ?? [];
    }

    /**
     * Check whether other entities depend on this entity
     *
     * Overwrite in child classes
     *
     * @return bool
     */
    protected function _getHasDependencies(): bool
    {
        return false;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getCounter($name)
    {
        return $this->table->getCounter($name);
    }

    /**
     * Get columns from the config
     *
     * TODO: implement filter directly in BaseTable::getColumns()
     *
     * @param array $selected List of selected columns to filter
     * @param boolean $joined Whether to join the columns
     * @return array
     */
    public function getColumns($selected, $joined = false)
    {
        $columns = $this->table->getColumns($selected, [], ['type' => $this->type['name'] ?? null, 'joined' => $joined]);
        $selectedColumns = array_filter($columns, fn($x) => ($x['selected'] ?? false));
        if (empty($selectedColumns)) {
            $selectedColumns = array_filter($columns, fn($x) => ($x['default'] ?? false));
        }
        return $selectedColumns;
    }

    /**
     * Copy entity files to a target folder
     *
     * @param string $targetFolder The target folder, absolute path on the server
     * @return string[]
     */
    public function copyFiles($targetFolder)
    {
        // TODO: implement (below code comes from copyImage(), here we want the full contents of project folders)
        // $filename = $this->file_name;
        // $sourceFolder = Files::joinPath([$this->file_properties['root'], $this->file_properties['path']]);
        // $result = Files::copyFile($filename, $sourceFolder, $targetFolder);
        return [];
    }

    /**
     * After removing XML tags, remove all respective link entities
     *
     * @param array $tags An array of element names
     * @param array $steps An array of processing steps
     * @param bool $recurse Whether to recurse into child entities
     * @return void
     */
    public function cleanXmlTags($tags, $steps = [], $recurse = false)
    {
        parent::cleanXmlTags($tags, $steps, $recurse);
        
        if (in_array('remove', $steps)) {
            foreach ($this->links as $link) {
                if (in_array($link->from_tagname, $tags)) {
                    $link['deleted'] = 1;
                }
            }
        }
    }

}
