<?php $__env->startSection('title', 'RAIL TWIN'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <h4 class="text-sidap text-bold m-0"><i class="fa-solid fa-fire"></i> Simulación Incendio</h4>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body" id="video-body1">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="videoinun" role="tabpanel">
                <video width="60%" controls>
                    <source src="<?php echo e(asset('videos/Incendio.mp4')); ?>" type="video/mp4">
                    Tu navegador no soporta la reproducción de video.
                </video>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_4_extend.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/admin_custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/app/resources/views/incendio.blade.php ENDPATH**/ ?>