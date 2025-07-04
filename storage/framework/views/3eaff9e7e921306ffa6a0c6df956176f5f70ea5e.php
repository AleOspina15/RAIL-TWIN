<?php $__env->startSection('title', 'RAIL TWIN'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <h4 class="text-sidap text-bold m-0"><i class="fa-solid fa-train"></i> BIM vs. As Built</h4>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <iframe class="responsive-iframe" src="http://212.128.193.56/?HoveringMouse=true&MatchViewportRes=false&KeyboardInput=true"></iframe>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_4_extend.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/admin_custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/app/resources/views/ueview.blade.php ENDPATH**/ ?>