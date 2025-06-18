<!DOCTYPE html>
<html>
<head>
    <title>Chat dengan Penjual</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f8e9;
            font-family: 'Segoe UI', sans-serif;
        }
        .chat-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .chat-box {
            height: 450px;
            overflow-y: auto;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 12px;
            box-shadow: inset 0 0 8px #c8e6c9;
        }
        .chat-message {
            margin-bottom: 15px;
            display: flex;
        }
        .chat-bubble {
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
            word-wrap: break-word;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .from-me {
            background-color: #a5d6a7;
            color: #000;
            margin-left: auto;
            text-align: right;
        }
        .from-them {
            background-color: #f1f1f1;
            color: #000;
            margin-right: auto;
            text-align: left;
        }
        .chat-meta {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        #form-chat input {
            border-radius: 30px;
        }
        #form-chat button {
            border-radius: 30px;
        }
        .btn-back {
            background-color: #f5f5f5;
            border: 1px solid #a5d6a7;
            color: #388e3c;
        }
        .btn-back:hover {
            background-color: #c8e6c9;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const id_ternak = <?= json_encode($id_ternak) ?>;
        const id_penjual = <?= json_encode($id_penjual) ?>;

        function loadChat() {
            $.post('', {mode: 'ambil'}, function(res) {
                let html = '';
                res.forEach(msg => {
                    let bubble = msg.pengirim_id == <?= $user_id ?> ? 'from-me' : 'from-them';
                    html += `<div class="chat-message ${bubble === 'from-me' ? 'justify-content-end' : 'justify-content-start'}">
                                <div class="chat-bubble ${bubble}">
                                    <div><strong>${msg.nama}</strong></div>
                                    <div>${msg.pesan}</div>
                                    <div class="chat-meta">${msg.created_at}</div>
                                </div>
                            </div>`;
                });
                $('.chat-box').html(html);
                $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
            }, 'json');
        }

        $(document).ready(function(){
            loadChat();
            setInterval(loadChat, 3000);

            $('#form-chat').submit(function(e){
                e.preventDefault();
                let pesan = $('[name=pesan]').val().trim();
                if (pesan === '') return;
                $.post('', {mode: 'kirim', pesan}, function(){
                    $('[name=pesan]').val('');
                    loadChat();
                });
            });
        });
    </script>
</head>
<body>

<div class="chat-container">
    <h5>💬 Chat dengan <b><?= htmlspecialchars($ternak['nama_penjual']) ?></b></h5>
    <p class="text-muted">Ternak: <strong><?= htmlspecialchars($ternak['jenis']) ?></strong></p>
    
    <div class="chat-box mb-3"></div>

    <form id="form-chat" class="d-flex gap-2">
        <input type="text" name="pesan" class="form-control" placeholder="Ketik pesan..." autocomplete="off" required>
        <button type="submit" class="btn btn-success px-4">Kirim</button>
    </form>

    <div class="mt-4">
        <a href="dashboard.php" class="btn btn-back">← Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>
