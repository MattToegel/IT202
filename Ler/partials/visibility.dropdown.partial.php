<label for="visibility">Visibility:</label>
<select class="form-control" id="visibility" name="visibility">
    <option <?php if($visibility==0)echo "selected";?> value="<?php echo Visibility::draft; ?>">Draft</option>
    <option <?php if($visibility==1)echo "selected";?>  value="<?php echo Visibility::private; ?>">Private</option>
    <option <?php if($visibility==2)echo "selected";?>  value="<?php echo Visibility::public; ?>">Public</option>
</select>