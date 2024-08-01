<?php
$attr = '';
?>
<div
    class="relative z-50 h-auto p-8 py-10 overflow-hidden bg-primary rounded-md px-7 w-full container lg:max-w-md md:max-w-xs">
    <input type="hidden" name="recovery_nonce" value="<?php echo wp_create_nonce('ajax-recovery-nonce') ?>">
    <h3 class="mb-6 text-2xl font-medium text-center"><?php _e('Reset Password', 'kiranime')?></h3>
    <input type="text" name="userlogin"
        class="block w-full px-4 py-3 mb-4 text-sm border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Email or username">
    <input type="password" data-input-recovery-verification name="verification_code"
        class="hidden w-full px-4 py-3 mb-4 text-sm border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Verification code">
    <input type="password" data-input-recovery-verification name="new_password_recovery"
        class="hidden w-full px-4 py-3 mb-4 text-sm border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="New Password">
    <input type="password" data-input-recovery-verification name="repeat_password_recovery"
        class="hidden w-full px-4 py-3 mb-4 text-sm border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Repeat Password">
    <div class="block">
        <button data-recovery-button
            class="w-full px-2 py-3 text-sm font-medium text-text-color bg-accent-3 rounded-lg">
            <?php _e('Get Verification Code', 'kiranime');?>
        </button>
        <button data-reset-button
            class="hidden w-full px-2 py-3 text-sm font-medium text-text-color bg-accent-3 rounded-lg">
            <?php _e('Save', 'kiranime');?>
        </button>
    </div>
    <p data-recovery-error-info class="w-full mt-4 text-sm text-center text-error"></p>
    <div class="flex align-center justify-center gap-4">
        <button data-kiranime-modal="data-register-template"
            class="text-accent-2 underline"><?php _e('Sign up here', 'kiranime')?></button>
        <div class="w-1 h-full bg-primary"></div>
        <button data-kiranime-modal="data-login-template"
            class="text-accent-2 underline"><?php _e('Log in here', 'kiranime')?></button>
    </div>

</div>