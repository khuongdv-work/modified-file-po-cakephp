<?php
/**
 * @var \App\View\AppView $this
 * @var array             $metrics
 *
 */
?>
<?php if(isset($enable) && $enable == true): ?>
    <!-- LIST CACHING -->
    <div class="row margin-top-2em">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-transgender font-green-sharp"></i>
                        <span class="caption-subject font-green-sharp bold uppercase"><?= __d('translate', 'Plugin translate') ?></span>
                    </div>
                </div>
                <div class="portlet-body">
                    <?= $this->Form->create(null, ['id' => 'translate-form']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="file" class="font-green bold"><?= __d('translate', 'File po') ?></label>
                                <select name="file" id="file" class="form-control js-msgid">
                                    <?php foreach ($files as $file): ?>
                                        <option value="<?=$file?>"><?= $file?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="msgid" class="font-green bold"><?= __d('translate', 'Msgid key') ?></label>
                                <select name="msgid" id="msgid" class="form-control js-get-msgstr"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row js-msgstr"><!-- ajax append here --></div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-default go-back">
                                <i class="fa fa-angle-double-left"></i>
                                <?=__d('translate','Go back')?>
                            </button>
                            <button class="btn btn-sm green js-update-language">
                                <i class="fa fa-plus"></i> <?= __d('translate', 'Update') ?>
                            </button>
                            <!-- button add new key -->
                            <button class="btn btn-sm green js-add-new">
                                <i class="fa fa-plus"></i> <?= __d('translate', 'Submit') ?>
                            </button>
                            <!-- button add new key -->
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
    <?= $this->Html->css('Translate./plugins/bootstrap-toastr/toastr.min.css', ['block' => true]) ?>
    <?= $this->Html->css('Translate./plugins/nprogress/nprogress.css', ['block' => true]) ?>
    <?= $this->Html->css('Translate.translate.css', ['block' => true]) ?>

    <?= $this->Html->script('Translate./plugins/bootstrap-toastr/toastr.min.js', ['block' => true]) ?>
    <?= $this->Html->script('Translate./plugins/jquery-validation/js/jquery.validate.js', ['block' => true]) ?>
    <?= $this->Html->script('Translate./plugins/nprogress/nprogress.js', ['block' => true]) ?>
    <?= $this->Html->script('Translate.validations.js', ['block' => true]) ?>
    <?= $this->Html->script('Translate.translate.js', ['block' => true]) ?>
<?php else: ?>
    <p>Not found folder Locale</p>
<?php endif ?>

