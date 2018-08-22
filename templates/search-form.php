<?php
function simple_search_form() {
?>
<form class="simple-search-form search-form">
	<div class="input-group">
	  <input type="text" class="form-control ssf-input" placeholder="Enter Address, Zip, Neighborhood, Building Name, MLS ID" aria-describedby="search-btn" name="search_key_word">
	  <a class="input-group-addon ssf-btn" id="search-btn"><i class="fa fa-search" aria-hidden="true"></i></a>
	</div>
</form>
<?php
}