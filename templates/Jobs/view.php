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
/**
 * @var $entity \App\Model\Entity\Job
 */
?>

<!-- Breadcrumbs -->
<?php
$this->Breadcrumbs->add(__('Jobs'), 'jobs/index');
$this->Breadcrumbs->add($entity->id);
?>

<!-- Content area -->
<?= $this->EntityHtml->entityForm($entity, 'view') ?>

<?php if (!empty($entity->result['downloads'])): ?>
    <h2><?= __('Downloads') ?></h2>
    <table>
        <thead>
        <tr>
            <th><?= __('File') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($entity->result['downloads'] ?? [] as $download): ?>
            <tr>
                <td>
                    <?= $this->Html->link(
                        $download['caption'],
                        ['controller'=>'Jobs', 'action' => 'execute',$entity->id, '?' => ['download' => $download['name'],'force'=>'1']],
                        ['target' => '_blank']
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (!empty($entity->config['pipeline_tasks'])): ?>
    <h2><?= __('Tasks') ?></h2>
    <table>
        <thead>
            <tr>
                <th><?= __('Done') ?></th>
                <th><?= __('Number') ?></th>
                <th><?= __('Type') ?></th>
                <th><?= __('Options') ?></th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($entity->config['pipeline_tasks'] as $step => $element): ?>

            <tr>
                <td>
                    <?php if ($step < (int)($entity->config['pipeline_progress'] ?? 0)): ?>
                    <span class="badge success">✓</span>
                    <?php endif; ?>
                </td>

                <td><?= h($element['number'] ?? '') ?></td>
                <td><?= h($element['type'] ?? '') ?></td>
                <td>
                    <?php
                        $options = array_diff_key($element,['number'=>false,'type'=>false]);
                        $options = array_filter($options, function($x) { return !is_array($x); });
                        $options = array_map(function($x, $y) {return ($x . ': ' . $y);}, array_keys($options),$options);
                        $options = implode('; ',$options);
                        echo $options;
                    ?>
                </td>

            </tr>

        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (!empty($entity->config)): ?>
    <h2><?= __('Config') ?></h2>

    <?php $options = array_diff_key($entity->config,['pipeline_tasks'=>false,'pipeline_progress'=>false]); ?>
    <?=	$this->Table->nestedTable($options);?>

<?php endif; ?>

    <!-- Actions -->
<?php
  $this->setShowBlock(['footer']);
  $this->Link->beginActionGroup ('bottom');

  if (!$entity->isFinished) {
    $this->Link->addAction(__('Cancel'), ['controller' => 'Jobs', 'action' => 'cancel', $entity->id], ['linktype' => 'post','method' => 'delete']);
  } else {
      $this->Link->addAction(__('Run'), ['controller' => 'Jobs', 'action' => 'execute', $entity->id, '?' => ['reset' => 1]]);
  }

  $this->Link->addAction(__('Edit'), 'jobs/edit/' . $entity->id);
?>
