<form id="mclain-rets-config">
	<h2>McLain Sandicore Configuration</h2>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="login_url">Login URL:</label></th>
				<td><input name="login_url" type="text" id="login_url" class="regular-text" value="<?php echo MCRETS_Config::getLoginURL(); ?>"></td>
			</tr>

			<tr>
				<th scope="row"><label for="username">Username:</label></th>
				<td><input name="username" type="text" id="username" class="regular-text" value="<?php echo MCRETS_Config::getUsername(); ?>"></td>
			</tr>
			
			<tr>
				<th scope="row"><label for="password">Password:</label></th>
				<td><input name="password" type="text" id="password" class="regular-text" value="<?php echo MCRETS_Config::getPassword(); ?>"></td>
			</tr>
		</tbody>
	</table>

	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	</p>
</form>