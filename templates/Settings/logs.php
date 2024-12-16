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
    /** @var array $log */
?>

<?php $this->Breadcrumbs->add(__('Logs')); ?>

<?= $this->Table->simpleTable(
    array_reverse(iterator_to_array($log, false)),
    [
        'datetime' => __('Time'),
        'type' => __('Type'),
        'exception' => __('Exception'),
        'message' => __('Message'),
        'Request URL' => ['caption' => 'Request', 'link' => '{Request URL}'],
        'Referer URL' => ['caption' => 'Referer', 'link' => '{Referer URL}'],
        'User Agent' => ['caption' => 'User Agent']
    ],
    ['nowrap' => true, 'class'=> 'simple-table widget-table recordlist']
) ?>


<!-- Actions -->
<?php
//    $this->setShowBlock(['footer']);
//    $this->Link->beginActionGroup('bottom');
//    $this->Link->addAction(__('Download'), ['controller'=>'files','action' => 'download', '?' => ['root' => 'root', 'path' => 'logs', 'filename'=>'error.log']]);
?>
