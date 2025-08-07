<!doctype html>
<html><body>
<h1>{{ $brochure['english']['title'] ?? 'Brochure' }}</h1>
<p>{{ $brochure['english']['mission'] ?? '' }}</p>
<ul>
  @foreach(($brochure['english']['services'] ?? []) as $s)
    <li>{{ $s }}</li>
  @endforeach
</ul>
</body></html>
