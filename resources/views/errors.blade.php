<div class="container">
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">
            @lang('Failed to display the [:title] widget.', compact('title'))
        </h4>
        <hr>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
