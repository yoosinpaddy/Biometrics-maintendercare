<x-app-layout>
<x-slot name="header">
    <h3>Posts page</h3>

</x-slot>

    <div class="container">
        <h3>Posts page</h3>
        {{-- @foreach ($allPosts as $post)
            <p>{{$post->content}}</p>
        @endforeach --}}

        post per user
        @foreach ($user->posts as $post)
            <p>{{$post->content}}</p>
        @endforeach

    </div>
</x-app-layout>



