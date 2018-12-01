<?php
/**
 * @var \App\View\AppView $this
 * @var array $result
 *
 */
?>
<!-- msgstr -->
<div class="col-md-12">
    <div class="portlet light portlet-fit bordered">
        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject bold font-green uppercase">
                    <?=__d('translate','msgstr')?>
                </span>
            </div>
            <div class="actions">
                <button class="btn btn-sm green add-new">
                    <i class="fa fa-plus"></i> <?=__d('translate','Add new')?>
                </button>
            </div>
        </div>
        <div class="portlet-body util-btn-margin-bottom-5">
            <div class="add-new-key"></div>
            <?php foreach ($result as $key => $item): ?>
            <div class="form-group">
                <label for="<?=$key?>" class="font-green bold"><?= __d('translate', $key) ?></label>
                <input class="form-control translate-msgstr" data-language="<?=$key?>" id="<?=$key?>" type="text" name="<?=$file?>[<?=$key?>]" value="<?=$item?>">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- msgstr -->