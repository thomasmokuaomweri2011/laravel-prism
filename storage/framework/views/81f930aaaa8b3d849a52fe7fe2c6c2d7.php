<!doctype html>
<html><body>
<h1><?php echo e($brochure['english']['title'] ?? 'Brochure'); ?></h1>
<p><?php echo e($brochure['english']['mission'] ?? ''); ?></p>
<ul>
  <?php $__currentLoopData = ($brochure['english']['services'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li><?php echo e($s); ?></li>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
</body></html>
<?php /**PATH /Users/thomasomweri/Sites/laravel-prism/resources/views/brochure/pdf.blade.php ENDPATH**/ ?>