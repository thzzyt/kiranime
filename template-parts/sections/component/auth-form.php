<?php
if (!is_user_logged_in()) {?>
<div id="login-form" class="fixed inset-0 hidden items-center justify-center z-50">
    <div data-login-overlay class="bg-black bg-opacity-80 fixed inset-0"></div>
    <div data-login-template class=" absolute top-1/2 transform -translate-y-1/2 md:-translate-y-1/2 md:max-w-sm"
        style="width: min(100%, 25rem);">
        <?php get_template_part('template-parts/sections/component/use', 'login');?>
    </div>
    <div data-register-template class="hidden" style="width: min(100%, 25rem);">
        <?php get_template_part('template-parts/sections/component/use', 'register')?>
    </div>
    <div data-recovery-template class="hidden" style="width: min(100%, 25rem);">
        <?php get_template_part('template-parts/sections/component/use', 'recovery')?>
    </div>
</div>
<?php }?>