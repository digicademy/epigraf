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
<epi-array v-slot="scope">

<div class="array_data">
  <?php if (!empty($data['@displayvalue']) ): ?>
    <div class="array_value">
      <a href="#" @click.prevent="scope.toggle"><?= $data['@displayvalue'] ?></a>
    </div>
  <?php endif; ?>

  <?php if (!empty($data) ): ?>
    <table class="array_items vertical-table" v-show="scope.showdetails">
    <?php foreach ($data as $key => $value): ?>
      <tr>
          <th scope="row"><?= $key ?></th>
          <td>
            <?php if (is_array($value) && isset($value['@xmltext'])): ?>
              <?= h($value['@xmltext']) ?>
            <?php elseif (is_array($value)): ?>
              <?= $this->element('array',['data'=>$value]) ?>
            <?php else: ?>
              <?= h($value) ?>
            <?php endif; ?>
          </td>
      </tr>
    <?php endforeach; ?>
     </table>
  <?php endif; ?>
</div>

</epi-array>
