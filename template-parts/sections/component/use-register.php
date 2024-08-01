<?php
$attr = '';
?>
<div class="relative z-50 h-auto p-8 py-10 overflow-hidden bg-primary rounded-lg px-7">
    <?php if (get_option('users_can_register')): ?>
    <input type="hidden" name="register_nonce" value="<?php echo wp_create_nonce('ajax-register-nonce') ?>">
    <h3 class="mb-6 text-2xl font-medium text-center"><?php _e('Register New Account', 'kiranime');?></h3>
    <input type="text" name="register_username"
        class="block w-full px-4 py-3 text-sm mb-4 border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Username">
    <input type="email" name="register_email"
        class="block w-full px-4 py-3 text-sm mb-4 border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Email">
    <input type="password" name="register_password" autocomplete="off"
        class="block w-full px-4 py-3 text-sm mb-4 border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Password">
    <input type="password" name="register_confirm_password" autocomplete="off"
        class="block w-full px-4 py-3 text-sm mb-4 border  border-transparent rounded-lg focus:ring focus:ring-accent-2 focus:outline-none text-primary"
        placeholder="Confirm Password">
    <div class="block">
        <button data-register-button
            class="w-full px-2 py-3 text-sm font-medium text-text-color bg-accent-3 rounded-lg">
            <?php _e('Sign up', 'kiranime');?>
        </button>
    </div>
    <p data-register-error-info class="w-full mt-4 text-sm text-center text-error"></p>
    <p class="w-full mt-4 text-sm text-center text-accent">
        <?php _e('Already have an account?', 'kiranime');?>
        <button data-kiranime-modal="data-login-template" class="text-accent-2 underline">
            <?php _e('Log in here', 'kiranime');?>
        </button>
    </p>
    <?php else: ?>
    <p class="w-full mt-4 text-[13px] font-semibold text-center text-error">
        <?php _e('Registration is currently disabled by Administrator. If you already have an account', 'kiranime')?>,
        <button data-kiranime-modal="data-login-template"
            class="text-accent-2 underline"><?php _e('Log in here', 'kiranime')?></button>.
    </p>
    <?php endif;?>
</div>