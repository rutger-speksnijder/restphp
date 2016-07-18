<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        $.ajax({
            type: 'PATCH',
            url: '/example',
            beforeSend: function(xhr) {
                //xhr.setRequestHeader("Content-Type", 'application/xml');
                //xhr.setRequestHeader("Content-Type", 'application/json');
                xhr.setRequestHeader("Content-Type", 'test');
            },
            //data: '\<\?xml version="1.0"?><request><test>123</test></request>',
            data: '{"test1": "test2"}',
            success: function(result) {
            }
        });
    });
</script>
