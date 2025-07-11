<?php $layoutHelper = app('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper'); ?>

<?php ( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') ); ?>

<?php if(config('adminlte.use_route_url', false)): ?>
    <?php ( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' ); ?>
<?php else: ?>
    <?php ( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' ); ?>
<?php endif; ?>

<a href="<?php echo e($dashboard_url); ?>"
   <?php if($layoutHelper->isLayoutTopnavEnabled()): ?>
       class="navbar-brand logo-switch <?php echo e(config('adminlte.classes_brand')); ?>"
   <?php else: ?>
       class="brand-link logo-switch <?php echo e(config('adminlte.classes_brand')); ?>"
        <?php endif; ?>>

    
    <img src="<?php echo e(asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png'))); ?>"
         alt="<?php echo e(config('adminlte.logo_img_alt', 'AdminLTE')); ?>"
         class="<?php echo e(config('adminlte.logo_img_class', 'brand-image-xl')); ?> logo-xs">

    
    <img src="<?php echo e(asset(config('adminlte.logo_img_xl'))); ?>"
         alt="<?php echo e(config('adminlte.logo_img_alt', 'AdminLTE')); ?>"
         class="<?php echo e(config('adminlte.logo_img_xl_class', 'brand-image-xs')); ?> logo-xl">

</a><?php /**PATH /var/www/app/resources/views/vendor/adminlte/partials/common/brand-logo-xl.blade.php ENDPATH**/ ?>