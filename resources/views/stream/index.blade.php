@extends('layouts.app')
@section('content')
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
@endsection
