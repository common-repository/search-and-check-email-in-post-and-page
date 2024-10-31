<?php
/**
 * Search Clickable Email Function
 *
 * This file contains the search clickable email function, which is used to
 * generate a clickable email link for search results.
 *
 * @author Your Name
 * @since Version 1.0.1
 * @package SearchEngineImprovements
 */

if ( ! function_exists( 'sei_search_clickable_email' ) ) {
	 /**
	  * Searches for clickable email links in the content.
	  *
	  * @return void
	  */
	function sei_search_clickable_email() {

		global $wpdb;
		$sei_tbl_name = '';
		$sei_field = '';
		$sei_order = '';
		$sei_post_table_name = $wpdb->prefix . 'posts';
		$sei_comment_table_name = $wpdb->prefix . 'comments';
		?>
		<div class="wrap nosubsub">
		<h1><?php esc_html_e( 'Email Address' ); ?></h1>
			<div id="ajax-response"></div>
			<br class="clear">

			<div id="col-container">
				<?php
				if ( isset( $_POST['sei_submit'] ) && ! empty( $_POST['sei_submit'] ) &&
				isset( $_POST['sei_form_security'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sei_form_security'] ) ), 'sei-form-security' ) &&
				isset( $_POST['sei_select_field'] ) && ! empty( $_POST['sei_select_field'] ) &&
				isset( $_POST['sei_select_order'] ) && ! empty( $_POST['sei_select_order'] ) ) {

					$sei_field = sanitize_text_field( wp_unslash( $_POST['sei_select_field'] ) );
					$sei_order = sanitize_text_field( wp_unslash( $_POST['sei_select_order'] ) );

					switch ( $sei_field ) {

						case 'post_content':
							$sei_tbl_name = $sei_post_table_name;
							$sei_results = $wpdb->get_results( $wpdb->prepare( "SELECT * from %1s WHERE post_status = 'publish' AND post_type != 'wpcf7_contact_form' AND post_content LIKE '%_@_%_.__%' ORDER BY post_date %2s", $sei_tbl_name, $sei_order ) );
							break;
						case 'post_excerpt':
							$sei_tbl_name = $sei_post_table_name;
							$sei_results = $wpdb->get_results( $wpdb->prepare( "SELECT * from %1s WHERE post_status = 'publish' AND post_type != 'wpcf7_contact_form' AND post_excerpt LIKE '%_@_%_.__%' ORDER BY post_date %2s", $sei_tbl_name, $sei_order ) );
							break;
						case 'comment_content':
							$sei_tbl_name = $sei_comment_table_name;
							$sei_results = $wpdb->get_results( $wpdb->prepare( "SELECT * from %1s WHERE comment_approved = true AND comment_content LIKE '%_@_%_.__%' ORDER BY comment_date %2s", $sei_tbl_name, $sei_order ) );
							break;
					}
					?>
					<div id="col-right">
						<div class="col-wrap">
						<h2 class="screen-reader-text"><?php esc_html_e( 'Email Address list' ); ?></h2>
							<table class="wp-list-table widefat fixed striped tags">
								<thead>
									<tr>
										<th scope="col" id="sei_title" class="manage-column column-sei-title column-primary">
											<span><?php echo esc_html__( 'Type' ); ?></span>
										</th>
										<th scope="col" id="sei_type" class="manage-column column-sei-type">
											<span><?php echo esc_html__( 'Type' ); ?></span>
										</th>
										<th scope="col" id="sei_emailaddr" class="manage-column column-sei-emailaddr">
										<span><?php echo esc_html__( 'Email Addresses' ); ?></span>
										</th>
										<th scope="col" id="sei_status" class="manage-column column-sei-status">
											<span><?php echo esc_html__( 'Status - Clickable' ); ?></span>
										</th>
									</tr>
								</thead>
								<tbody id="the-list" data-wp-lists="list:tag">
									<?php
									if ( count( $sei_results ) > 0 ) {

										$sei_count = 0;

										foreach ( $sei_results as $check ) {

											$sei_found_yes = '';
											$sei_found_no = '';
											$sei_col = '';
											$sei_title = '';
											$sei_type = '';
											$sei_id = '';
											$sei_edit_link = '';

											if ( $sei_tbl_name == $wpdb->prefix . 'posts' && 'post_content' == $sei_field ) {

												$sei_col = $check->post_content;
												$sei_title = $check->post_title;
												$sei_type = $check->post_type;
												$sei_id = $check->ID;
												$sei_edit_link = admin_url( 'post.php?post=' . $check->ID . '&action=edit' );
											}

											if ( $sei_tbl_name == $wpdb->prefix . 'posts' && 'post_excerpt' == $sei_field ) {

												$sei_col = $check->post_excerpt;
												$sei_title = $check->post_title;
												$sei_type = $check->post_type;
												$sei_id = $check->ID;
												$sei_edit_link = admin_url( 'post.php?post=' . $check->ID . '&action=edit' );
											}

											if ( $sei_tbl_name == $wpdb->prefix . 'comments' && 'comment_content' == $sei_field ) {

												$sei_col = $check->comment_content;
												$sei_title = get_the_title( $check->comment_post_ID );
												$sei_type = get_comment_type( $check->comment_ID );
												$sei_id = $check->comment_ID;
												$sei_edit_link = admin_url( 'comment.php?action=editcomment&c=' . $check->comment_ID );
											}

											if ( preg_match_all( '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b/i', strip_tags( $sei_col ), $matches ) ) {

												foreach ( $matches[0] as $key => $value ) {
													if ( preg_match_all( '/<a\s[^>]*href=["\']mailto:' . preg_quote( $value, '/' ) . '["\'][^>]*>\s*' . preg_quote( $value, '/' ) . '\s*<\/a>/i', $sei_col, $match ) ) {
														$sei_found_yes .= $value . ',<br>';
													} else {
														$sei_found_no .= $value . ',<br>';
													}
												}
											}

											if ( $sei_found_yes ) {
												?>
												<tr id="tag-1">
													<td class="name column-name has-row-actions column-primary">
														<strong>
														<a class="row-title" href="<?php echo esc_url( $sei_edit_link ); ?>">
																	<?php echo esc_html( $sei_title ); ?>
																</a>
														</strong>
														<br>
														<div class="row-actions">
															<span class="edit"><a href="<?php echo esc_url( $sei_edit_link ); ?>">Edit</a> | </span>
															<span class="view"><a href="<?php the_permalink( $sei_id ); ?>"> View </a></span>
														</div>
													</td>
													<td class="column-description">
													<p><?php echo esc_html( $sei_type ); ?></p>
													</td>
													<td class="column-sei-type">
														<?php echo wp_kses_post( rtrim( $sei_found_yes, ',<br>' ) ); ?>
													</td>
													<td class="column-sei-status">
														<span style="color: #01a252">Yes</span>
													</td>
												</tr>
												<?php
												$sei_count++;
											}
											if ( $sei_found_no ) {
												?>
												<tr id="tag-1">
													<td class="name column-name has-row-actions column-primary" data-colname="Name">
														<strong>
														<a class="row-title" href="<?php echo esc_url( $sei_edit_link ); ?>">
																	<?php echo esc_html( $sei_title ); ?>
																</a>
														</strong>
														<br>
														<div class="row-actions">
															<span class="edit"><a href="<?php echo esc_url( $sei_edit_link ); ?>">Edit</a> | </span>
															<span class="view"><a href="<?php the_permalink( $sei_id ); ?>" >View</a></span>
														</div>
													</td>
													<td class="column-sei-type">
														<p><?php echo esc_html( $sei_type ); ?></p>
													</td>
													<td class="column-sei-emailaddr">
														<?php echo wp_kses_post( rtrim( $sei_found_no, ',<br>' ) ); ?>
													</td>
													<td class="column-sei-status">
														<span style="color: #F30">No</span>
													</td>
												</tr>
												<?php
												$sei_count++;
											}
										}
										if ( $sei_count < 0 ) {
											?>
											<tr class='no-items'>
												<td class='colspanchange' colspan='4'>No results found.</td>
											</tr>
											<?php
										}
									} else {
										?>
										<tr class='no-items'>
											<td class='colspanchange' colspan='4'>No results found.</td>
										</tr> 
										<?php
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th scope="col" id="sei_title" class="manage-column column-sei-title column-primary">
											<span><?php echo esc_html__( 'Title' ); ?></span>
										</th>
										<th scope="col" id="sei_type" class="manage-column column-sei-type">
											<span><?php echo esc_html__( 'Type' ); ?></span>
										</th>
										<th scope="col" id="sei_emailaddr" class="manage-column column-sei-emailaddr">
											<span><?php echo esc_html__( 'Email Adresses' ); ?></span>
										</th>
										<th scope="col" id="sei_status" class="manage-column column-sei-status">
											<span><?php echo esc_html__( 'Status - Clickable' ); ?></span>
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div><!-- /col-right -->
					<?php
				}
				?>
				<div id="col-left">
					<div class="col-wrap">
						<div class="form-wrap">
							<h2>Search Clickable Email Address</h2>
							<form name="sei_form" id="sei_from" method="POST" action="">
								<div class="form-field term-parent-wrap">
								<label for="parent"><?php esc_html_e( 'Field' ); ?></label>
									<select name="sei_select_field" id="sei_select_field" class="postform">
										<option value="post_content" <?php echo ( 'post_content' == $sei_field ) ? 'selected' : ''; ?> >Post Content</option>
										<option value="post_excerpt" <?php echo ( 'post_excerpt' == $sei_field ) ? 'selected' : ''; ?> >Post Excerpt</option>
										<option value="comment_content" <?php echo ( 'comment_content' == $sei_field ) ? 'selected' : ''; ?> >Comment Content</option>
									</select>
									<p>Select a field from you need to search email address.</p>
								</div>
								<div class="form-field term-parent-wrap">
									<label for="parent"><?php esc_html_e( 'Order' ); ?></label>
									<select name="sei_select_order" id="sei_select_order">
										<option value="ASC" <?php echo ( 'ASC' == $sei_order ) ? 'selected' : ''; ?> >Ascending</option>
										<option value="DESC" <?php echo ( 'DESC' == $sei_order ) ? 'selected' : ''; ?> >Descending</option>
									</select>
									<p>Select order in which you need to list search result.</p>
								</div>
								<p class="submit">
									<?php wp_nonce_field( 'sei-form-security', 'sei_form_security' ); ?>
									<input type="submit" name="sei_submit" id="sei_submit" class="button button-primary" value="Search">
								</p>
							</form>
						</div>
					</div><!-- /col-left -->
				</div><!-- /col-container -->
			</div>
		</div>
		<?php
	}
}
