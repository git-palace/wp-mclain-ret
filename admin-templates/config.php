<form id="sandicor-config">
	<h1 class="wp-heading-inline">Sandicor Configuration</h1>
	<hr class="wp-header-end">

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="login_url">Login URL:</label></th>
				<td><input name="login_url" type="text" id="login_url" class="regular-text" value="<?php echo SandicorConfig::getLoginURL(); ?>"></td>
			</tr>

			<tr>
				<th scope="row"><label for="username">Username:</label></th>
				<td><input name="username" type="text" id="username" class="regular-text" value="<?php echo SandicorConfig::getUsername(); ?>"></td>
			</tr>
			
			<tr>
				<th scope="row"><label for="password">Password:</label></th>
				<td><input name="password" type="text" id="password" class="regular-text" value="<?php echo SandicorConfig::getPassword(); ?>"></td>
			</tr>
			
			<tr>
				<th scope="row"><label for="password">BRE License:</label></th>
				<td><input name="brelicense" type="text" id="brelicense" class="regular-text" value="<?php echo SandicorConfig::getBRELicense(); ?>"></td>
			</tr>

			<tr>
				<th scope="row"><label for="autosave">Run Cron Job:</label></th>
				<td>
					<select name="autosave" id="autosave">
						<option <?php echo SandicorConfig::getAutoSave() == "yes" ? "selected" : "" ?> value="yes">Yes</option>
						<option <?php echo SandicorConfig::getAutoSave() != "yes" ? "selected" : "" ?> value="no">No</option>
					</select>
					<p>Will run now and schedule it.</p>
				</td>
			</tr>
		</tbody>
	</table>

	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	</p>
</form>