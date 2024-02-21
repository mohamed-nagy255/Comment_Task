<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Comment Task</title>

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>

<body>
    <div class="card container mt-4 pt-2" style="max-width: 600px;">

        {{-- CREATE --}}
        <form class="row" id="commentForm">
            @csrf
            <div class="col-10">
                <input type="text" name="comment" class="form-control" id="inputComment"
                    placeholder="Enter a New Comment">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-primary mb-3" id="submitComment">Submit</button>
            </div>
        </form>
        <div id="commentsContainer" class="commentsContainer">
            @foreach ($comments as $comment)
                <div class="row d-flex justify-content-center mb-2">
                    <div class="card p-3" style="max-width: 500px">
                        <div class="row">
                            <div class="user flex-row align-items-center">
                                <img src="https://luminfire.com/wp-content/uploads/2023/07/Blue-Laravel-Icon.png"
                                    width="30" class="user-img rounded-circle mr-2">
                                <span>
                                    <small class="font-weight-bold text-primary">
                                        {{ $comment->name }}
                                    </small>
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2 mb-2">
                            <span>{{ $comment->comment }}</span>
                        </div>
                        <div class="row">
                            <small class="d-flex">

                                {{-- UPDATE --}}
                                @if (auth()->user()->id === $comment->user_id)
                                    <button type="button" class="btn update-comment-btn mr-2"
                                        data-comment-id="{{ $comment->id }}"
                                        data-comment="{{ $comment->comment }}">Update</button>
                                    <div class="update-comment-form" style="display: none;">
                                        <form action="{{ route('comment.update') }}" method="POST">
                                            @method('PATCH')
                                            @csrf
                                            <input type="hidden" name="id" class="update-comment-id">
                                            <input type="text" name="comment" class="form-control"
                                                placeholder="Enter Updated Comment">
                                            <button type="submit" class="btn btn-primary mt-2">Update Comment</button>
                                        </form>
                                    </div>
                                @endif

                                {{-- DELETE --}}
                                <form class="delete-comment-form" action="{{ route('comment.delete', $comment->id) }}"
                                    method="POST">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn delete-comment-btn">Remove</button>
                                </form>
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
            {{ $comments->links() }}
        </div>
    </div>

    {{-- BOOTSTRAP --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    {{-- JQUERY --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>

    {{-- LOADING IN SCROLL --}}
    <script type="text/javascript">
        $('nav[aria-label="Pagination Navigation"]').hide();
        $(function() {
            $('.commentsContainer').jscroll({
                autoTrigger: true,
                loadingHtml: '<img class="center-block" src="https://i.gifer.com/origin/34/34338d26023e5515f6cc8969aa027bca.gif" alt="Loading..." />',
                padding: 0,
                nextSelector: 'nav[aria-label="Pagination Navigation"] div a',
                contentSelector: 'div.commentsContainer',
                callback: function() {
                    $('nav[aria-label="Pagination Navigation"]').remove();
                }
            });
        });
    </script>

    <script>
        // Create comment
        $(document).ready(function() {
            $('#submitComment').click(function() {
                var comment = $('#inputComment').val();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('comment.store') }}",
                    data: {
                        comment: comment,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#commentsContainer').load(location.href + ' #commentsContainer');
                        $('#inputComment').val('');
                    }
                });
            });

            // Update comment
            $(document).on('click', '.update-comment-btn', function(e) {
                e.preventDefault();
                var commentId = $(this).data('comment-id');
                var comment = $(this).data('comment');
                var form = $('.update-comment-form');
                form.find('.update-comment-id').val(commentId);
                form.find('.form-control').val(comment);
                form.toggle();
            });
            $('.update-comment-form form').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#commentsContainer').load(location.href + ' #commentsContainer');
                        form.hide();
                    }
                });
            });

            // Delete comment
            $(document).on('click', '.delete-comment-btn', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        form.closest('.card').parent()
                            .remove();
                    }
                });
            });
        });
    </script>
</body>

</html>
