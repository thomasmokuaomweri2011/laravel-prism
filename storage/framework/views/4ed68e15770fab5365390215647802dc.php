<?php $__env->startSection('content'); ?>
<h2>Streaming Demo</h2>
<button id="run">Run</button>
<pre id="out"></pre>
<script>
  document.getElementById('run').onclick = () => {
    const es = new EventSource('/stream/run');
    es.onmessage = e => {
      if (e.data === '[DONE]') es.close();
      else document.getElementById('out').textContent += e.data;
    };
  };
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/thomasomweri/Sites/laravel-prism/resources/views/stream/index.blade.php ENDPATH**/ ?>