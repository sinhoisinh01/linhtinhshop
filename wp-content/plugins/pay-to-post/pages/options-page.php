<div class="wrap">
	<h2><?php _e('Pay To Post Settings ', self::$textDomain); ?></h2>
	<?php add_thickbox(); ?>
	<div id="pay-to-post-tb" style="display:none">
		<form id="add-edit-pay-to-post-form" method="post">
			<ul>
				<li>
					<p class="description">User Role</p>
					<select id="user_roles_select">
						<?php echo $this->getUserRoles(); ?>
					</select>
				</li>
				<li>
					<p class="description">Post Type</p>
					<select id="post_types_select">
						<?php echo $this->getPostTypes(); ?>
					</select>
				</li>
				<li>
					<p class="description">Cost</p>
					<input type="number" step="any" name="cost" id="cost" pattern="^\d+\.\d{2}$">
				</li>
			</ul>
			<input type="submit" value="add" class="button-primary" id="add_rule_submit">
			<input type="submit" value="Update" class="button-primary" id="edit_rule_submit">
		</form>
	</div> <!--add-edit-tb-->
	<form method="post" action="options.php">
		<?php settings_fields('pay_to_post_options'); ?>
		<?php $payToPostRules = $this->getPluginOptions(); ?>
		<h3>Post charging rules <a title="add new payment rule" class="add-new-h2" id="add_new_payment">Add New Payment Rule</a></h3>
		<table class="wp-list-table widefat fixed striped" id="rules_list">
			<thead>
				<tr>
					<th>User Role</th>
					<th>Post Type</th>
					<th>Cost</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>User Role</th>
					<th>Post Type</th>
					<th>Cost</th>
					<th>Actions</th>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$count = 0;
			if(isset($payToPostRules['rule'])){
				foreach($payToPostRules['rule'] as $k => $v){
					echo '<tr>';
					echo '<td>'.$v['user_role'].'<input type="hidden" name="pay_to_post[rule]['.$count.'][user_role]" value="'.$v['user_role'].'"></td>';
					echo '<td>'.$v['post_type_display'].'<input type="hidden" name="pay_to_post[rule]['.$count.'][post_type]" value="'.$v['post_type'].'"><input type="hidden" name="pay_to_post[rule]['.$count.'][post_type_display]" value="'.$v['post_type_display'].'"></td>';
					echo '<td>'.$v['cost'].'<input type="hidden" name="pay_to_post[rule]['.$count.'][cost]" value="'.$v['cost'].'"></td>';
					echo '<td><span class="edit_rule button-primary">Edit</span> <span class="remove_rule button-primary">Remove</span></td>';
					echo '</tr>';
					$count++;
				}
			}
			
			?>
			</tbody>
		</table>
		<h3>Post charging settings</h3>
		<table class="form-table">
			<tbody>
				
				<tr>
					<th scope="row">Currency</th>
					<?php if(!isset($payToPostRules['paypal_currency'])){
						$payToPostRules['paypal_currency'] = 'USD';
					}
					?>
					<td>
						<select id="pay_to_post[paypal_currency]" name="pay_to_post[paypal_currency]">
							<option value="AUD" <?php if($payToPostRules['paypal_currency'] == "AUD"){echo 'selected';} ?> >Australian Dollar</option>
							<option value="CAD" <?php if($payToPostRules['paypal_currency'] == "CAD"){echo 'selected';} ?> >Canadian Dollar</option>
							<option value="EUR" <?php if($payToPostRules['paypal_currency'] == "EUR"){echo 'selected';} ?> >Euro</option>
							<option value="GBP" <?php if($payToPostRules['paypal_currency'] == "GBP"){echo 'selected';} ?> >British Pound</option>
							<option value="JPY" <?php if($payToPostRules['paypal_currency'] == "JPY"){echo 'selected';} ?> >Japanese Yen</option>
							<option value="USD" <?php if($payToPostRules['paypal_currency'] == "USD"){echo 'selected';} ?> >U.S. Dollar</option>
							<option value="NZD" <?php if($payToPostRules['paypal_currency'] == "NZD"){echo 'selected';} ?> >New Zealand Dollar</option>
							<option value="CHF" <?php if($payToPostRules['paypal_currency'] == "CHF"){echo 'selected';} ?> >Swiss Franc</option>
							<option value="HKD" <?php if($payToPostRules['paypal_currency'] == "HKD"){echo 'selected';} ?> >Hong Kong Dollar</option>
							<option value="SGD" <?php if($payToPostRules['paypal_currency'] == "SGD"){echo 'selected';} ?> >Singapore Dollar</option>
							<option value="SEK" <?php if($payToPostRules['paypal_currency'] == "SEK"){echo 'selected';} ?> >Swedish Krona</option>
							<option value="DKK" <?php if($payToPostRules['paypal_currency'] == "DKK"){echo 'selected';} ?> >Danish Krone</option>
							<option value="PLN" <?php if($payToPostRules['paypal_currency'] == "PLN"){echo 'selected';} ?> >Polish Zloty</option>
							<option value="NOK" <?php if($payToPostRules['paypal_currency'] == "NOK"){echo 'selected';} ?> >Norwegian Krone</option>
							<option value="HUF" <?php if($payToPostRules['paypal_currency'] == "HUF"){echo 'selected';} ?> >Hungarian Forint</option>
							<option value="CZK" <?php if($payToPostRules['paypal_currency'] == "CZK"){echo 'selected';} ?> >Czech Koruna</option>
							<option value="ILS" <?php if($payToPostRules['paypal_currency'] == "ILS"){echo 'selected';} ?> >Israeli New Shekel</option>
							<option value="MXN" <?php if($payToPostRules['paypal_currency'] == "MXN"){echo 'selected';} ?> >Mexican Peso</option>
							<option value="BRL" <?php if($payToPostRules['paypal_currency'] == "BRL"){echo 'selected';} ?> >Brazilian Real</option>
							<option value="MYR" <?php if($payToPostRules['paypal_currency'] == "MYR"){echo 'selected';} ?> >Malaysian Ringgit</option>
							<option value="PHP" <?php if($payToPostRules['paypal_currency'] == "PHP"){echo 'selected';} ?> >Philippine Peso</option>
							<option value="TWD" <?php if($payToPostRules['paypal_currency'] == "TWD"){echo 'selected';} ?> >New Taiwan Dollar</option>
							<option value="THB" <?php if($payToPostRules['paypal_currency'] == "THB"){echo 'selected';} ?> >Thai Baht</option>
							<option value="TRY" <?php if($payToPostRules['paypal_currency'] == "TRY"){echo 'selected';} ?> >Turkish Lira</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">Payment Page</th>
					<td>
						<select id="pay_to_post[payment_page]" name="pay_to_post[payment_page]">
							<?php 
								$paymentPageId = 0;
								if(array_key_exists('payment_page', $payToPostRules)){
									$paymentPageId = $payToPostRules['payment_page'];
								}
								echo $this->getAllPages($paymentPageId); 
							?>
						</select>
						<span class="description">Select the page where <code>[ptp_payment_form]</code> is located.</span>
					</td>
				</tr>
				<tr>
					<th scope="row">Payment Confirmation Page</th>
					<td>
						<select id="pay_to_post[payment_confirmation_page]" name="pay_to_post[payment_confirmation_page]">
							<?php
								$paymentConfirmationPageId = 0;
								if(array_key_exists('payment_confirmation_page', $payToPostRules)){
									$paymentConfirmationPageId = $payToPostRules['payment_confirmation_page'];
								} 
								echo $this->getAllPages($paymentConfirmationPageId); 
							?>
						</select>
						<span class="description">Select the page where <code>[ptp_confirmation_form]</code> is located.</span>
					</td>
				</tr>
				<tr>
					<th scope="row">Transactions Display Page</th>
					<td>
						<select id="pay_to_post[payment_transactions_page]" name="pay_to_post[payment_transactions_page]">
							<?php 
								$paymentTransactionsPageId = 0;
								if(array_key_exists('payment_transactions_page', $payToPostRules)){
									$paymentTransactionsPageId = $payToPostRules['payment_transactions_page'];
								}
								echo $this->getAllPages($paymentTransactionsPageId); 
							?>
						</select>
						<span class="description">Select the page where <code>[ptp_transaction_display]</code> is located.</span>
					</td>
				</tr>
				<tr>
					<th scope="row">Cancellation Display Page</th>
					<td>
						<select id="pay_to_post[payment_cancellation_page]" name="pay_to_post[payment_cancellation_page]">
							<?php
								$paymentCancellationPageId = 0;
								if(array_key_exists('payment_cancellation_page', $payToPostRules)){
									$paymentCancellationPageId = $payToPostRules['payment_cancellation_page'];
								} 
								echo $this->getAllPages($paymentCancellationPageId); 
							?>
						</select>
						<span class="description">Select the page where <code>[ptp_cancellation_display]</code> is located.</span>
					</td>
				</tr>
			</tbody>
		</table>
		<h3>PayPal account details</h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">Sandbox Mode</th>
					<td>
						<input type="checkbox" id="pay_to_post[paypal_sandbox]" name="pay_to_post[paypal_sandbox]" <?php $paypalSandbox = false; if(array_key_exists('paypal_sandbox', $payToPostRules)){$paypalSandbox = $payToPostRules['paypal_sandbox'];} if($paypalSandbox){echo 'checked';} ?> >
					</td>
				</tr>
				<tr>
					<th scope="row">PayPal API Username</th>
					<td>
						<input type="text" class="regular-text" id="pay_to_post[paypal_api_username]" name="pay_to_post[paypal_api_username]" value="<?php $paypalUserName=''; if(array_key_exists('paypal_api_username', $payToPostRules)){$paypalUserName = $payToPostRules['paypal_api_username'];} echo $paypalUserName; ?>">
					</td>
				</tr>
				<tr>
					<th scope="row">PayPal API Password</th>
					<td>
						<input type="text" class="regular-text" id="pay_to_post[paypal_api_password]" name="pay_to_post[paypal_api_password]" value="<?php $paypalPassword=''; if(array_key_exists('paypal_api_password', $payToPostRules)){$paypalPassword = $payToPostRules['paypal_api_password'];} echo $paypalPassword; ?>">
					</td>
				</tr>
				<tr>
					<th scope="row">PayPal API Signature</th>
					<td>
						<input type="text" class="regular-text" id="pay_to_post[paypal_api_signature]" name="pay_to_post[paypal_api_signature]" value="<?php $paypalSignature=''; if(array_key_exists('paypal_api_signature', $payToPostRules)){$paypalSignature = $payToPostRules['paypal_api_signature'];} echo $paypalSignature; ?>">
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>			
	</form>
</div> <!-- wrap -->
