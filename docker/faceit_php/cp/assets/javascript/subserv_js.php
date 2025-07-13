<script>
    document.querySelectorAll('.gameid-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var gameid = this.dataset.gameid;
            var userid = this.dataset.userid;
            var form = new FormData();
            form.append('ajax_matchinfo', '1');
            form.append('matchinfo_for', gameid);
            form.append('userinfo_for', userid || '0');
            fetch(window.location.pathname + '?subid=<?php echo $subid; ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: form
                })
                .then(function(res) {
                    return res.text();
                })
                .then(function(text) {
                    Swal.fire({
                        title: 'Match Info',
                        html: '<pre style="text-align:left; font-family:monospace; font-size:12px; white-space:pre-wrap; line-height: 1.6; background-color: rgba(30,30,30,0.8); color: #eee; padding: 15px; border-radius: 8px;"><style>pre table { background-color: #1b1b1b; border: 1px solid #444; } pre table td, pre table th { border: 1px solid #555; padding: 4px 6px; }</style>' + text + '</pre>',
                        background: 'linear-gradient(135deg, #1a1a1a, #2a2a2a)',
                        color: '#f5f5f5',
                        width: 700
                    });
                })
                .catch(function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err
                    });
                });
        });
    });
</script>