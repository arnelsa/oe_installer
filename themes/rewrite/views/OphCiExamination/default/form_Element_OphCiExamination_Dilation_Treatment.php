<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<tr class="dilationTreatment" data-key="<?php echo $key ?>">
	<td>
		<?php echo CHtml::textField('dilation_treatment['.$key.'][treatment_time]',isset($treatment) ? substr($treatment->treatment_time,0,5) : date('H:i'),array('class' => 'input-time'))?>
	</td>
	<td>
		<?php if (isset($treatment) && $treatment->id) {?>
			<input type="hidden" name="dilation_treatment[<?php echo $key ?>][id]" value="<?php echo $treatment->id?>" />
		<?php }?>
		<input type="hidden" name="dilation_treatment[<?php echo $key ?>][side]" value="<?php echo $side ?>" />
		<span class="drugName"><?php echo $drug_name?></span>
		<input type="hidden" class="drugId" name="dilation_treatment[<?php echo $key ?>][drug_id]" value="<?php echo @$drug_id ?>" />
	</td>
	<td>
		<select name="dilation_treatment[<?php echo $key ?>][drops]">
			<?php for ($i=1;$i<=10;$i++) {?>
				<option value="<?php echo $i?>"<?php if ($i == @$treatment->drops) {?> selected="selected"<?php }?>><?php echo $i?></option>
			<?php }?>
		</select>
	</td>
	<td class="treatmentActions">
		<a href="#" class="removeTreatment">Remove</a>
	</td>
</tr>