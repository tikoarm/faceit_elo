server {
    listen 80;
    server_name db.cs2-faceit.tikoarm.com;

    location / {
        proxy_pass http://localhost:8082;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
