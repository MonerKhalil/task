<!DOCTYPE html>
<html>
<head>
    <title>New Post Created</title>
</head>
<body>
<h1>New Post Created</h1>
<h2>User : {{$post->user->name ?? "-" }}</h2>
<p>A new post titled "<strong>{{ $post->title }}</strong>" has been created.</p>
<p>content : "{{ $post->content }}"</p>
<p>Check it out!</p>
</body>
</html>
