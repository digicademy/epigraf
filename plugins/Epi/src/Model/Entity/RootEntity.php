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
        $footnoteTypes = $this->table->getDatabase()->types['footnotes'] ?? [];
        $footnoteTypes = array_intersect_key($footnoteTypes, array_flip($this->type['merged']['footnotes'] ?? []));

        return array_merge(
            array_map(fn($x) => [], $this->footnotes_by_type),
            $footnoteTypes
        );
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
        $along = empty($along) ? Hash::combine($footnotes, "{*}.from_tagid", "{*}.sortno") : $along;
        $footnotes = Arrays::orderAlong($footnotes, 'from_tagid', $along);

        // Filter out unpublished footnotes for guests
        if (($this->currentUserRole === 'guest') || !empty(\App\Model\Table\BaseTable::$requestPublished)) {
            $footnotes = array_filter($footnotes, fn($footnote) => $footnote->number !== -INF);
        }

        return $footnotes;
    }

    /**
     * Return a list of links and footnotes
     *
     * @return array
     */
    public function extractAnnotations()
    {
        $annos = array_merge(
            $this->links_by_tagid ?? [],
            $this->footnotes_by_tagid ?? []
        );

        // TODO: unnest duplicate annotation
        $annos = array_map(fn($x) => [
            'from_tab' => $x[0]['from_tab'],
            'from_id' => $x[0]['from_id'],
            'from_field' => $x[0]['from_field'],
            'from_tagname' => $x[0]['from_tagname'],
            'from_tagid' => $x[0]['from_tagid']
        ], $annos);

        return $annos;
    }

    /**
     * Check whether elements in XML fields and link/footnote annotations match
     *
     * For fields containing XML, the links and footnotes are compared to the tags.
     * Missing tag IDs (in the links or footnote records as well as in the xml content)
     * are returned.
     *
     * @return array An array with error messages for missing tags
     */
    protected function _getMissingXmlTags()
    {
        // Extract all tags that should link somewhere
        // TODO: don't recurse, call in each _getProblems()
        $tags = $this->extractXmlTags(null, null, true);
        $tags = array_map(fn($x) => [
            'type' => 'tag',
            'from_tagname' => $x['tagname'],
            'from_tagid' => $x['tagid'],
            'from_tab' => $x['tab'],
            'from_id' => $x['id'],
            'from_field' => $x['field']
        ], $tags);

        // Extract all annotations
        $annos = $this->extractAnnotations();
        $annos = array_map(fn($x) => array_replace($x, ['type' => 'annotation']), $annos);

        // Get missing tags and annotations
        $missing = array_merge(
            array_diff_key($tags, $annos),
            array_diff_key($annos, $tags)
        );

        // Only keep tags that have a link configuration
        $types_links = $this->table->getDatabase()->types['links'] ?? [];
        $types_footnotes = $this->table->getDatabase()->types['footnotes'] ?? [];

        $missing = array_filter(
            $missing,
            fn($x) => (
                ($x['type'] !== 'tag') ||
                (
                    !empty($types_footnotes[$x['from_tagname']]) &&
                    !empty($types_links[$x['from_tagname']]['merged']['fields']['to'])
                )
            )
        );
        $missing = array_map(fn($tag) => array_replace($tag, [
            'problem' => __(
                'Missing {type} {from_tagname}#{from_tagid} in field {from_tab}-{from_id}.{from_field}.',
                array_replace($tag, ['type' => $tag['type'] === 'annotation' ? 'tag' : 'annotation'])
            )
        ]), $missing);

        // Unconfigured tags
        $unconfigured = array_filter(
            $tags,
            fn($x) => (
                empty($types_footnotes[$x['from_tagname']]) &&
                empty($types_links[$x['from_tagname']])
            )
        );
        $unconfigured = array_map(fn($tag) => array_replace($tag, [
            'problem' => __(
                'Missing configuration for {type} {from_tagname}#{from_tagid} in field {from_tab}-{from_id}.{from_field}.',
                $tag
            )
        ]), $unconfigured);

        // Merge
        $missing = array_merge($missing, $unconfigured);

        return $missing;
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
                ->groupBy('from_tab')
                ->map(fn($table) => collection($table)
                    ->groupBy(fn($link) => $link['from_id'] ?? '')
                    ->toArray()
                )
                ->toArray();
        }

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
     * @return array
     */
    public function getColumns($selected)
    {
        $columns = $this->table->getColumns($selected, [], $this->type['name'] ?? null);
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
}
