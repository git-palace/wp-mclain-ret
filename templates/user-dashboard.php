<div class="container sandicor-dashboard">
	<div class="col-sm-12">
		<!-- Nav tabs -->
		<ul class="col-sm-3 nav nav-pills nav-stacked">
			<li class="active"><a class="text-center" href="#saved-searches">Saved Searches</a></li>
			<li><a class="text-center" href="#change-password">Change Password</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="col-sm-9 tab-content">
			<div class="tab-pane active" id="saved-searches">
				<?php $sandicor_criterias = get_user_meta( get_current_user_id(), 'sandicor_criterias', true ); ?>
				<?php if ( isset( $sandicor_criterias ) && !empty( $sandicor_criterias ) ) : $idx = 0; ?>
					<table class="table keywords">
						<thead>
							<tr>
								<th class="text-center"><b>#</b></th>
								<th class="text-center">Search Keyword</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ( $sandicor_criterias as $criteria ): ?>
								<tr>
									<td><b><?php _e( $idx = $idx +1 ) ?></b></td>
									<td><?php _e( $criteria['keyword'] ) ?></td>
									<td>
										<a class="delete" href="javascript:void(0)" k-index="<?php echo esc_attr( $idx ) ?>" keyword="<?php echo esc_attr( $criteria['keyword'] ); ?>">Delete</a> |
										<a class="" href="/search-results/<?php echo esc_attr( $criteria['keyword'] ); ?>" k-index="<?php echo esc_attr( $idx ) ?>" keyword="<?php echo esc_attr( $criteria['keyword'] ); ?>">Visit</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<h1 class="text-center">No Result</h1>
				<?php endif; ?>
			</div>

			<div class="tab-pane" id="change-password">
				<form class="form-horizontal" role="form">
				  <div class="form-group">
				    <label for="oldPWD" class="col-sm-4 control-label">Old Password</label>
				    <div class="col-sm-8">
				      <input type="password" class="form-control" id="oldPWD" placeholder="Old Password">
				    </div>
				  </div>
				  <div class="form-group">
				    <label for="newPWD" class="col-sm-4 control-label">New Password</label>
				    <div class="col-sm-8">
				      <input type="password" class="form-control" id="newPWD" placeholder="New Password">
				    </div>
				  </div>

				  <div class="form-group">
				    <div class="col-sm-offset-2 col-sm-10">
				      <button type="submit" class="btn btn-default">Confirm</button>
				    </div>
				  </div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
(function($) {
	$(".nav-pills a").click(function() {
		$(this).tab('show');
	});
})(jQuery);
</script>