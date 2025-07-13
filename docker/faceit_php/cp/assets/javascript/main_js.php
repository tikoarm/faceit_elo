<script>
    document.getElementById('findUserBtn')?.addEventListener('click', function () {
        Swal.fire({
            title: '<span style="color:#ffd866;">Enter nickname</span>',
            input: 'text',
            inputLabel: 'FACEIT nickname',
            showCancelButton: true,
            confirmButtonText: 'Lookup',
            background: 'linear-gradient(135deg, #1a1a1a, #2a2a2a)',
            color: '#f5f5f5',
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

                fetch(window.location.pathname, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: form
                })
                .then(res => res.text())
                .then(text => {
                    Swal.fire({
                        title: 'Faceit ID',
                        text: text,
                        background: 'linear-gradient(135deg, #1a1a1a, #2a2a2a)',
                        color: '#f5f5f5'
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