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
                        html: '<pre style="text-align:left; font-family:monospace; font-size:12px; white-space:pre-wrap; line-height: 1.4;">' + text + '</pre>',
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

    document.getElementById('findUserBtn')?.addEventListener('click', function () {
        Swal.fire({
            title: 'Enter nickname',
            input: 'text',
            inputLabel: 'FACEIT nickname',
            showCancelButton: true,
            confirmButtonText: 'Lookup',
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('Please enter a nickname');
                }
                return value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = new FormData();
                form.append('nickname_lookup', '1');
                form.append('nickname', result.value);

                fetch(window.location.pathname + '?subid=<?php echo $subid; ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: form
                })
                .then(res => res.text())
                .then(text => {
                    Swal.fire({
                        title: 'Faceit ID',
                        text: text
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка запроса',
                        text: err
                    });
                });
            }
        });
    });
</script>