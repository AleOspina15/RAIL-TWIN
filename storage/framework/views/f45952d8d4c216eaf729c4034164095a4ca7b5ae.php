<?php $__env->startSection('title', 'RAIL TWIN'); ?>

<?php $__env->startSection('content_header'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_4_extend.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/admin_custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/app/resources/views/home.blade.php ENDPATH**/ ?>