<?php
$attr = '';
?>
<div
    class="relative z-50 h-auto p-8 py-10 overflow-hidden bg-primary rounded-md px-7 w-full container lg:max-w-md md:max-w-xs">
    <input type="hidden" name="login_nonce" value="<?php echo wp_create_nonce('ajax-login-nonce') ?>">
    <h3 class="mb-6 text-2xl font-medium text-center"><?php _e('Sign in to your Account', 'kiranime')?></h3>
    <input type="text" name="username"
        class="block w-full px-4 py-3 mb-4 text-sm border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Username">
    <input type="password" name="password"
        class="block w-full px-4 py-3 mb-4 text-sm border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Password">
    <div class="block">
        <button data-login-button class="w-full px-2 py-3 text-sm font-medium text-text-color bg-accent-3 rounded-lg">
            <?php _e('Log Me In', 'kiranime');?>
        </button>
        <div class="text-sm my-4 text-center">
            <input type="checkbox" name="remember_me" id="remember_me" class="bg-primary checked:text-accent-2">
            <label for="remember_me"><?php _e('Remember me', 'kiranime');?></label>
        </div>
    </div>
    <p data-login-error-info class="w-full mt-4 text-sm text-center text-error"></p>
    <p class="w-full mt-4 text-sm text-center text-accent"><?php _e("Don't have an account?", 'kiranime')?> <button
            data-kiranime-modal="data-register-template"
            class="text-accent-2 underline"><?php _e('Sign up here', 'kiranime')?></button></p>
    <p class="w-full mt-4 text-sm text-center text-accent">
        <button data-kiranime-modal="data-recovery-template"
            class="text-accent-2 underline"><?php _e('Reset Password', 'kiranime')?></button>
    </p>

</div>