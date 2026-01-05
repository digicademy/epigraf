<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

use App\Utilities\Converters\Arrays;

?>

<?php
/**
 * @var \App\View\AppView $this
 * @var Cake\ORM\ResultSet $entities
 * @var \App\Model\Entity\Databank $database
 */
?>

<?php
    $scope = $this->getConfig('options')['scope'] ?? '';
    $type = $database->types['properties'][$scope] ?? null;
    $seek = $this->getConfig('options')['params']['seek'] ?? null;
    $append = $this->getConfig('options')['params']['append'] ?? null;
    $empty = $this->getConfig('options')['params']['empty'] ?? null;

    $find  = $this->getConfig('options')['params']['find'] ?? '';
    $find  = $this->getConfig('options')['params']['find'] ?? '';

    $selectedNode = null;
    foreach ($entities as $property) {
        if ($property->id === (int)$seek) {
            $selectedNode = $property;
            break;
        }
    }

    $find = empty($append) ? '' : $find;
    $find = $selectedNode ? $selectedNode->shortname : $find;

    $queryParams =  [
        'template' => 'choose',
        'show'=>'content',
        'references' => false,
        'append' => $append,
        'empty' => $empty ?? 0,
        'find' => $find
    ];

    $articleParams = $this->getConfig('options')['params']['articles'] ?? null;
    if (!empty($articleParams)) {
        $queryParams = array_merge($queryParams, Arrays::array_add_prefix($articleParams, 'articles.', true));
    }
    $projectParams = $this->getConfig('options')['params']['projects'] ?? null;
    if (!empty($projectParams)) {
        $queryParams = array_merge($queryParams, Arrays::array_add_prefix($projectParams, 'projects.', true));
    }
?>

<?php $this->Breadcrumbs->add(__('Categories'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($type['caption'] ??  $scope) ?>


<div class="content-main widget-scrollbox">
    <?= $this->Form->control('value',
        [
            'label' => false,
            'type' => 'reference',
            'frame' => true,
            'paneSnippet' => 'rows',
            'listValue' => 'id',
            'url' => [
                'controller' => 'Properties',
                'action' => 'index',
                $scope,
                '?' => $queryParams
            ],
            //'text' => $selected ? $selected->path : '',
            'value' => $selectedNode ? $selectedNode->id : null,
            'param' => 'find',
            // TODO: query the ancestors to get the full path, or assemble the path from $entities above
            'text' => $find,
            'textLabel' => $type['caption'] ?? '',
            'autofocus' => 'autofocus'
        ]
    ) ?>
</div>
