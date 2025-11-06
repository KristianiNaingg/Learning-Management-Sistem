
<script src="https://cdn.tiny.cloud/1/0d7m15tgwl2ntjvt6oe0swi3jh6xmk21ij4hvy0ddzf26jnu/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
    tinymce.init({
        selector: '{{ $selector ?? "textarea" }}', // Default to all textareas, override with parameter
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        setup: function (editor) {
            editor.on('init', function () {
                console.log('TinyMCE initialized successfully');
            });
            editor.on('error', function (e) {
                console.error('TinyMCE error:', e);
            });
        }
    });
</script>
