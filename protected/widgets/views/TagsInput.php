<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2017
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

    $div_id = @$htmlOptions['div_id'];
    $div_class = isset($htmlOptions['div_class']) ? $htmlOptions['div_class'] : 'eventDetail';
?>
<div id="<?php echo $div_id ?>" class="<?php echo $div_class ?> row field-row widget"<?php if ($hidden) {?> style="display: none;"<?php }?>>
    <div class="large-<?php echo $layoutColumns['label'];?> column">
        <label for="<?php echo $field?>">
            <?php echo $label; ?>:
        </label>
    </div>
    <div class="large-<?php echo $layoutColumns['field'];?> column end">
        <div class="oe-tagsinput-wrapper">
            <input
                name="<?php echo $field?>" id="tags"
                value="<?php echo implode(',', $this->default_tags); ?>"
                type="text" class="tagsinput"
                <?php if($this->autocomplete_url){ echo 'data-autocomplete-url = "'.$this->autocomplete_url.'"';} ?>
            />
        </div>
    </div>
</div>

<?php
    $assetManager = Yii::app()->getAssetManager();
    $widgetPath = $assetManager->publish('protected/widgets/js');
    $assetManager->registerScriptFile('components/jquery.tagsinput/src/jquery.tagsinput.js');
    $assetManager->registerCssFile('components/jquery.tagsinput/dist/jquery.tagsinput.min.css');
    Yii::app()->clientScript->registerScriptFile($widgetPath . '/TagsInput.js');
?>
