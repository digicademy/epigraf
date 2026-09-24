<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
?>

<?php
use App\Utilities\Converters\Arrays;
?>

<?php
/**
 * @var App\View\AppView $this
 * @var Epi\Model\Entity\Article[] $entities
 * @var App\Model\Entity\Databank $database
 * @var array $columns
 */
?>

<?php
//    $columns = $this->getConfig('options')['columns'] ?? [];
//    $columns_visible = $this->Table->getSelectedColumns($columns);

    $params = $this->getConfig('options')['params'] ?? [];
    $selected = $params['selected'] ?? [];
    $detailContent = $params['details'] ?? [];
    $targets = $params['targets']  ?? [];

    // TODO: Do we need this? Refactor
    $searchResults = (($params['term'] ?? '') !== '') && (str_starts_with($params['field'] ?? '', 'text'));
    if ($searchResults) {
        $detailContent[] = 'search';
    }

    // Choose articles and their children if the targets and template parameters are set accordingly
    // TODO: expand the tree beginning with the second level

    $selectTemplate = $this->request->getQuery('template') === 'choose';
    $detailTargets = !empty(array_diff_key($targets, ['articles' => true]));
    $treeDetails =  ($selectTemplate && $detailTargets) ? 'cursor' : false;

    if ($treeDetails) {
        $isCursored = !empty($params['cursor']);
        $showTree = $treeDetails ? 'collapsed' : false;
        $treeFold = $treeDetails ? 'foldable' : 'fixed';

        $showTree = empty($params['cursor']) ? 'collapsed' : true;
        $treeFold = 'foldable';
    }

    // Show search results
    else {
        $showTree = false;
        $treeFold = 'fixed';
    }

    $tableModel = 'epi.articles';
    if ($selectTemplate) {
        $tableModel .= '.choose';
    }

?>

<div class="content-main widget-scrollbox" data-snippet="rows">
  <?php
    $params = $this->getConfig('options')['params'] ?? [];
    $actions = $this->Link->getActions($params['mode'] ?? MODE_DEFAULT, $params);

    // TODO: document
    $dataParams = [
        'data-filter-template' => $this->request->getQuery('template', 'table'),
        'data-filter-mode' => $this->getConfig('options')['params']['mode'] ?? ''
    ];

    // Add targets to the fixed query parameters
    foreach ($targets as $key => $value) {
        $dataParams['data-filter-param-targets_' . $key] = implode(',', $value);
    }
  ?>

    <?= $this->Table->filterTable(
        $tableModel,
        $entities,
        [
            'indent' => empty($showTree), // Add an empty extra column
            'select'=> true,              // Column selector
            'snippet' => false,           // Prevents the column selector to vanish
            'sort' => 'multi',            // Allows multi column sorting

            'tree' => $showTree,         // Tree rendering: true|false|collapsed
            'fold' => $treeFold,         // Foldable: fixed|foldable
            'details' => $treeDetails,   // Tree child nodes: true|false|cursor
            'content' => $detailContent, // Detail rows (content of tags, e.g. 'items.*.tags.*.content')
            'targets' => $targets,       // Target types indexed by table name (e.g. ['articles'=>'epi-article'])
            'label' => $selectTemplate, // Adds data-labels to the rows that can be used in selectors

            // TODO: document
            'class' => 'widget-filter-item widget-filter-item-template widget-filter-item-fixed',
            'data' => $dataParams,
            'actions' => $actions
        ]
    )
  ?>

</div>
